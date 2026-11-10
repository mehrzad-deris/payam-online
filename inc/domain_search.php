<?php
defined('ABSPATH') || exit;

/** Remove retired controls from schemas previously saved through the ACF editor. */
function payam_remove_retired_domain_fields(array $field): array {
    foreach ($field['layouts'] ?? [] as $index => $layout) {
        $field['layouts'][$index]['sub_fields'] = array_values(array_filter(
            $layout['sub_fields'] ?? [],
            static fn(array $subfield): bool => ($subfield['name'] ?? '') !== 'domain_bundle_tlds'
                && ($subfield['_name'] ?? '') !== 'domain_bundle_tlds'
                && !str_starts_with((string) ($subfield['key'] ?? ''), 'field_payam_domain_bundle_tlds_')
        ));
    }
    return $field;
}
add_filter('acf/update_field/type=flexible_content', 'payam_remove_retired_domain_fields', 5);

// Shared editor option, including future layouts, with a unique key per layout.
add_filter('acf/load_field/type=flexible_content', static function (array $field): array {
    $field = payam_remove_retired_domain_fields($field);
    foreach ($field['layouts'] ?? [] as $index => $layout) {
        $fields = $layout['sub_fields'] ?? [];
        if (!in_array('hide_on_domain_results', array_column($fields, 'name'), true)) {
            array_unshift($fields, ['key' => 'field_payam_hide_domain_' . md5($layout['key']), 'name' => 'hide_on_domain_results', 'label' => 'پنهان‌شدن هنگام نمایش نتایج دامنه', 'type' => 'true_false', 'ui' => 1, 'default_value' => 0]);
        }
        if (in_array($layout['name'], ['hero_section', 'domain_check_section'], true)) {
            foreach (['domain_result_tlds' => 'پسوندهای سایر نتایج'] as $name => $label) {
                if (!in_array($name, array_column($fields, 'name'), true)) array_unshift($fields, ['key' => 'field_payam_' . $name . '_' . md5($layout['key']), 'name' => $name, 'label' => $label, 'type' => 'relationship', 'post_type' => ['whmcs_domain_tld'], 'filters' => ['search'], 'return_format' => 'id', 'max' => 40]);
            }
        }
        // These controls are injected after ACF has loaded its normal subfields.
        // Run the same validation step so format_value can use the original _name.
        foreach ($fields as &$subfield) {
            if (is_array($subfield) && !isset($subfield['_name'])) {
                $subfield['parent'] = $field['key'];
                $subfield['parent_layout'] = $layout['key'];
                unset($subfield['_valid']);
                $subfield = acf_validate_field($subfield);
            }
            if (is_array($subfield) && preg_match('/^field_payam_(?:hide_domain_|domain_result_tlds_)[a-f0-9]{32}$/D', (string) ($subfield['key'] ?? ''))) {
                acf_add_local_field($subfield, true);
            }
        }
        unset($subfield);
        $field['layouts'][$index]['sub_fields'] = $fields;
    }
    return $field;
}, 30);

// Relationship queries are separate requests: load their parent schemas before
// ACF resolves the generated field key. Normal frontend requests need no scan.
add_action('acf/init', static function (): void {
    if (!wp_doing_ajax() || !current_user_can('edit_posts')) return;
    $key = isset($_POST['field_key']) && is_string($_POST['field_key']) ? wp_unslash($_POST['field_key']) : '';
    if (!preg_match('/^field_payam_domain_result_tlds_[a-f0-9]{32}$/D', $key)) return;
    $walk = static function (array $fields) use (&$walk): void {
        foreach ($fields as $field) {
            if (!empty($field['sub_fields'])) $walk($field['sub_fields']);
            foreach ($field['layouts'] ?? [] as $layout) $walk($layout['sub_fields'] ?? []);
        }
    };
    foreach (acf_get_field_groups() as $group) $walk(acf_get_fields($group) ?: []);
}, 30);

function payam_domain_search_config(): array {
    $all = get_posts(['post_type' => 'whmcs_domain_tld', 'post_status' => 'publish', 'posts_per_page' => -1, 'fields' => 'ids']);
    $catalog = [];
    foreach ($all as $id) {
        $data = payam_get_whmcs_domain_tld_data((int) $id);
        if (!$data || !is_numeric($data['register_price'] ?? null)) continue;
        $catalog[(string) $id] = ['id' => (int) $id, 'extension' => strtolower(ltrim($data['extension'], '.')), 'price' => (float) $data['register_price'], 'regular' => is_numeric($data['regular_price'] ?? null) ? (float) $data['regular_price'] : null, 'currency' => $data['currency'] ?? '', 'suffix' => $data['price_suffix'] ?? ''];
    }
    $choose = static function ($value) use ($catalog): array {
        $items = [];
        foreach (is_array($value) ? $value : [] as $id) {
            $id = $id instanceof WP_Post ? $id->ID : absint($id);
            if (isset($catalog[(string) $id])) $items[] = $catalog[(string) $id];
        }
        return $items;
    };
    return ['endpoint' => rest_url('payam/v1/domains/'), 'orderUrl' => function_exists('payam_whmcs_catalog_domain_order_url') ? payam_whmcs_catalog_domain_order_url() : '', 'catalog' => array_values($catalog), 'suggestions' => array_slice($choose(get_sub_field('domain_result_tlds')) ?: array_values($catalog), 0, 40)];
}
