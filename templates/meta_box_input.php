<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


atshift_fields_maintenance_for_custom_field_suite()->form->load_assets();

$field_groups = isset( $metabox['args']['group_id'] ) ? array_map( 'absint', (array) $metabox['args']['group_id'] ) : [];

// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- atshift_fields_maintenance_for_custom_field_suite()->form() renders the plugin's field input markup with per-field escaping.
echo atshift_fields_maintenance_for_custom_field_suite()->form( [
    'post_id'       => isset( $post->ID ) ? absint( $post->ID ) : 0,
    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Field group IDs are sanitized with absint() before rendering form markup.
    'field_groups'  => $field_groups,
    'front_end'     => false,
] );
