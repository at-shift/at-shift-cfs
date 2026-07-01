<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


CFS()->form->load_assets();

$field_groups = isset( $metabox['args']['group_id'] ) ? array_map( 'absint', (array) $metabox['args']['group_id'] ) : [];

// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CFS()->form() renders the plugin's field input markup with per-field escaping.
echo CFS()->form( [
    'post_id'       => isset( $post->ID ) ? absint( $post->ID ) : 0,
    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Field group IDs are sanitized with absint() before rendering form markup.
    'field_groups'  => $field_groups,
    'front_end'     => false,
] );
