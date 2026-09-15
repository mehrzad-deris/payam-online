<?php
/** Shared request-security helpers for public endpoints. */

defined( 'ABSPATH' ) || exit;

/**
 * Return a privacy-preserving client identifier.
 *
 * Forwarded addresses are trusted only when the direct peer is explicitly
 * listed in PAYAM_TRUSTED_PROXY_IPS.
 */
function payam_client_fingerprint(): string {
	$remote_address = filter_var( (string) ( $_SERVER['REMOTE_ADDR'] ?? '' ), FILTER_VALIDATE_IP ) ?: 'unknown';
	$trusted_proxies = defined( 'PAYAM_TRUSTED_PROXY_IPS' ) && is_array( PAYAM_TRUSTED_PROXY_IPS )
		? PAYAM_TRUSTED_PROXY_IPS
		: [];

	if ( in_array( $remote_address, $trusted_proxies, true ) ) {
		$forwarded = explode( ',', (string) ( $_SERVER['HTTP_X_FORWARDED_FOR'] ?? '' ) );
		$candidate = filter_var( trim( $forwarded[0] ?? '' ), FILTER_VALIDATE_IP );
		if ( $candidate ) {
			$remote_address = $candidate;
		}
	}

	return hash_hmac( 'sha256', $remote_address, wp_salt( 'auth' ) );
}

/**
 * Consume one request from a bounded, serialized transient counter.
 */
function payam_rate_limit_consume( string $scope, int $limit, int $window = 600 ): bool {
	$limit  = max( 1, $limit );
	$window = max( 60, $window );
	$key    = 'payam_rl_' . md5( sanitize_key( $scope ) . '|' . payam_client_fingerprint() );
	$lock   = $key . '_lock';
	$now    = time();

	$acquired = add_option( $lock, $now, '', false );
	if ( ! $acquired && $now - (int) get_option( $lock, 0 ) > 5 ) {
		delete_option( $lock );
		$acquired = add_option( $lock, $now, '', false );
	}

	// Fail closed while another PHP worker owns the counter lock.
	if ( ! $acquired ) {
		return false;
	}

	try {
		$state = get_transient( $key );
		if ( ! is_array( $state ) || (int) ( $state['reset'] ?? 0 ) <= $now ) {
			$state = [ 'count' => 0, 'reset' => $now + $window ];
		}

		if ( (int) $state['count'] >= $limit ) {
			return false;
		}

		$state['count'] = (int) $state['count'] + 1;
		set_transient( $key, $state, max( 1, (int) $state['reset'] - $now ) );
		return true;
	} finally {
		delete_option( $lock );
	}
}
