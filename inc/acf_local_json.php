<?php

/**
 * Local JSON is the version-controlled source for ACF schemas.
 * Database synchronization remains an explicit deployment operation.
 */

function payam_acf_json_path(): string {
	return get_theme_file_path( '/acf-json' );
}

add_filter( 'acf/settings/save_json', static function (): string {
	return payam_acf_json_path();
} );

add_filter( 'acf/settings/load_json', static function ( array $paths ): array {
	$theme_path = payam_acf_json_path();
	$paths      = array_values( array_filter(
		$paths,
		static fn ( $path ): bool => wp_normalize_path( (string) $path ) !== wp_normalize_path( $theme_path )
	) );

	array_unshift( $paths, $theme_path );

	return $paths;
} );

// Schema editing is intentionally unavailable in production. Content fields remain editable.
add_filter( 'acf/settings/show_admin', static function ( bool $show ): bool {
	return wp_get_environment_type() === 'production' ? false : $show;
} );

/**
 * Validates canonical ACF Local JSON files before a protected sync.
 *
 * @return true|WP_Error
 */
function payam_validate_acf_json_schema() {
	$files = glob( trailingslashit( payam_acf_json_path() ) . '*.json' ) ?: [];

	if ( [] === $files ) {
		return new WP_Error( 'payam_acf_json_missing', 'No canonical ACF JSON files were found.' );
	}

	$item_keys  = [];
	$field_keys = [];

	$collect_field_keys = static function ( array $fields, string $file ) use ( &$collect_field_keys, &$field_keys ) {
		foreach ( $fields as $field ) {
			if ( ! is_array( $field ) ) {
				continue;
			}

			$key = (string) ( $field['key'] ?? '' );
			if ( '' === $key ) {
				return new WP_Error( 'payam_acf_field_key_missing', sprintf( 'A field key is missing in %s.', basename( $file ) ) );
			}

			if ( isset( $field_keys[ $key ] ) ) {
				return new WP_Error( 'payam_acf_field_key_duplicate', sprintf( 'Duplicate field key %s in %s and %s.', $key, basename( $field_keys[ $key ] ), basename( $file ) ) );
			}

			$field_keys[ $key ] = $file;

			foreach ( [ 'sub_fields', 'layouts' ] as $child_key ) {
				$children = $field[ $child_key ] ?? [];
				if ( ! is_array( $children ) ) {
					continue;
				}

				if ( 'layouts' === $child_key ) {
					$children = array_values( $children );
				}

				$error = $collect_field_keys( $children, $file );
				if ( is_wp_error( $error ) ) {
					return $error;
				}
			}
		}

		return true;
	};

	foreach ( $files as $file ) {
		$data = json_decode( (string) file_get_contents( $file ), true );
		if ( JSON_ERROR_NONE !== json_last_error() || ! is_array( $data ) ) {
			return new WP_Error( 'payam_acf_json_invalid', sprintf( 'Invalid JSON in %s: %s', basename( $file ), json_last_error_msg() ) );
		}

		// Canonical Local JSON uses one ACF item per file, not an export array.
		if ( array_is_list( $data ) ) {
			return new WP_Error( 'payam_acf_json_export_array', sprintf( '%s contains an export array instead of one canonical item.', basename( $file ) ) );
		}

		$key = sanitize_key( (string) ( $data['key'] ?? '' ) );
		if ( '' === $key || pathinfo( $file, PATHINFO_FILENAME ) !== $key ) {
			return new WP_Error( 'payam_acf_json_filename', sprintf( 'The key and filename do not match for %s.', basename( $file ) ) );
		}

		if ( isset( $item_keys[ $key ] ) ) {
			return new WP_Error( 'payam_acf_item_key_duplicate', sprintf( 'Duplicate ACF item key %s.', $key ) );
		}
		$item_keys[ $key ] = $file;

		if ( isset( $data['fields'] ) && is_array( $data['fields'] ) ) {
			$error = $collect_field_keys( $data['fields'], $file );
			if ( is_wp_error( $error ) ) {
				return $error;
			}
		}
	}

	return true;
}

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	/**
	 * Creates and verifies a database backup before running ACF's official JSON sync.
	 */
	final class Payam_ACF_Safe_Sync_Command {
		private function print_command_output( $result ): void {
			$stdout = trim( (string) ( $result->stdout ?? '' ) );
			$stderr = trim( (string) ( $result->stderr ?? '' ) );

			if ( '' !== $stdout ) {
				\WP_CLI::log( $stdout );
			}
			if ( '' !== $stderr ) {
				\WP_CLI::warning( $stderr );
			}
		}

		/**
		 * Back up the database, preview the ACF changes, and sync after confirmation.
		 *
		 * ## OPTIONS
		 *
		 * [--yes]
		 * : Skip the final interactive confirmation.
		 */
		public function __invoke( array $args, array $assoc_args ): void {
			$validation = payam_validate_acf_json_schema();
			if ( is_wp_error( $validation ) ) {
				\WP_CLI::error( $validation->get_error_message() );
			}

			$backup_dir = defined( 'PAYAM_ACF_BACKUP_DIR' )
				? (string) PAYAM_ACF_BACKUP_DIR
				: dirname( untrailingslashit( ABSPATH ) ) . '/payam-private-backups';
			$backup_dir = wp_normalize_path( $backup_dir );
			$web_root   = trailingslashit( wp_normalize_path( ABSPATH ) );

			if ( str_starts_with( trailingslashit( $backup_dir ), $web_root ) ) {
				\WP_CLI::error( 'PAYAM_ACF_BACKUP_DIR must be outside the public WordPress root.' );
			}

			if ( ! is_dir( $backup_dir ) && ! wp_mkdir_p( $backup_dir ) ) {
				\WP_CLI::error( 'The private backup directory could not be created.' );
			}

			$filename = sprintf( 'acf-pre-sync-%s-%s.sql', gmdate( 'Ymd-His' ), wp_generate_password( 12, false, false ) );
			$backup   = trailingslashit( $backup_dir ) . $filename;
			$command  = 'db export ' . escapeshellarg( $backup ) . ' --add-drop-table';
			$result   = \WP_CLI::runcommand( $command, [
				'return'     => 'all',
				'exit_error' => false,
				'launch'     => true,
			] );
			$this->print_command_output( $result );

			if ( 0 !== (int) ( $result->return_code ?? 1 ) || ! is_file( $backup ) || filesize( $backup ) < 1024 ) {
				if ( is_file( $backup ) ) {
					unlink( $backup );
				}
				\WP_CLI::error( 'Database backup failed or produced an invalid file. ACF was not synchronized.' );
			}

			@chmod( $backup, 0600 );
			$checksum = hash_file( 'sha256', $backup );
			if ( ! is_string( $checksum ) || 64 !== strlen( $checksum ) ) {
				\WP_CLI::error( 'The database backup could not be verified. ACF was not synchronized.' );
			}

			\WP_CLI::success( sprintf( 'Verified private backup: %s (%s bytes, SHA-256: %s)', $backup, number_format_i18n( filesize( $backup ) ), $checksum ) );
			\WP_CLI::log( 'Previewing ACF JSON changes:' );
			$dry_run = \WP_CLI::runcommand( 'acf json sync --dry-run', [
				'return'     => 'all',
				'exit_error' => false,
				'launch'     => true,
			] );
			$this->print_command_output( $dry_run );

			if ( 0 !== (int) ( $dry_run->return_code ?? 1 ) ) {
				\WP_CLI::error( 'ACF dry-run failed. The verified backup was retained and no sync was performed.' );
			}

			if ( ! isset( $assoc_args['yes'] ) ) {
				\WP_CLI::confirm( 'Apply the displayed ACF schema changes now?' );
			}

			$sync = \WP_CLI::runcommand( 'acf json sync', [
				'return'     => 'all',
				'exit_error' => false,
				'launch'     => true,
			] );
			$this->print_command_output( $sync );

			if ( 0 !== (int) ( $sync->return_code ?? 1 ) ) {
				\WP_CLI::error( sprintf( 'ACF sync failed. Restore from %s if needed.', $backup ) );
			}

			\WP_CLI::success( 'ACF JSON synchronization completed.' );
		}
	}

	\WP_CLI::add_command( 'payam acf-safe-sync', Payam_ACF_Safe_Sync_Command::class );
}
