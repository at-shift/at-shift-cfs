<?php

global $post;

/*---------------------------------------------------------------------------------------------
    Field management screen
---------------------------------------------------------------------------------------------*/

if ( 'cfs' == $screen->post_type ) {
    foreach ( CFS()->fields as $field_name => $field_data ) {
        ob_start();
        CFS()->fields[ $field_name ]->options_html( 'clone', $field_data );
        $options_html[ $field_name ] = ob_get_clean();
    }

    $field_count = get_post_meta( $post->ID, 'cfs_fields', true );
    $field_count = is_array( $field_count ) ? count( $field_count ) : 0;

    // Build clone HTML
    $field = (object) [
        'id'            => 0,
        'parent_id'     => 0,
        'name'          => 'new_field',
        'label'         => __( 'New Field', 'cfs' ),
        'type'          => 'text',
        'notes'         => '',
        'weight'        => 'clone',
    ];

    ob_start();
    CFS()->field_html( $field );
    $field_clone = ob_get_clean();

    wp_add_inline_script(
        'cfs-fields',
        sprintf(
            "var CFS = CFS || {};\nCFS['field_index'] = %d;\nCFS['field_clone'] = %s;\nCFS['options_html'] = %s;",
            (int) $field_count,
            wp_json_encode( $field_clone ),
            wp_json_encode( $options_html )
        ),
        'before'
    );

    wp_add_inline_script(
        'cfs-fields',
        'CFS.messages = ' . wp_json_encode( [
            'disallowed_group_child' => __( 'Tabs, loops, and horizontal groups cannot be placed inside a horizontal group.', 'cfs' ),
        ] ) . ';',
        'before'
    );

}

/*---------------------------------------------------------------------------------------------
    Field input
---------------------------------------------------------------------------------------------*/

else {
    $hide_editor = false;
    $field_groups = CFS()->api->get_matching_groups( $post->ID );

    if ( ! empty( $field_groups ) ) {

        // Store field group IDs as an array for front-end forms
        CFS()->group_ids = array_keys( $field_groups );
        $native_fields = CFS()->api->find_input_fields( [
            'group_id' => CFS()->group_ids,
            'field_type' => [ 'wp_category', 'wp_tag', 'featured_image' ],
        ] );
        $hide_native = [];

        foreach ( $native_fields as $native_field ) {
            $hide_native[ $native_field['type'] ] = true;
        }

        if ( ! empty( $hide_native ) ) {
            if ( isset( $hide_native['wp_category'] ) ) {
                remove_meta_box( 'categorydiv', $post->post_type, 'side' );
            }
            if ( isset( $hide_native['wp_tag'] ) ) {
                remove_meta_box( 'tagsdiv-post_tag', $post->post_type, 'side' );
            }
            if ( isset( $hide_native['featured_image'] ) ) {
                remove_meta_box( 'postimagediv', $post->post_type, 'side' );
            }

            $selectors = [];
            if ( isset( $hide_native['wp_category'] ) ) {
                $selectors[] = '#categorydiv';
            }
            if ( isset( $hide_native['wp_tag'] ) ) {
                $selectors[] = '#tagsdiv-post_tag';
            }
            if ( isset( $hide_native['featured_image'] ) ) {
                $selectors[] = '#postimagediv';
            }

            if ( ! empty( $selectors ) ) {
                echo '<style type="text/css">' . esc_html( implode( ',', $selectors ) ) . '{display:none!important;}</style>';
            }
        }

        if (
            function_exists( 'use_block_editor_for_post' ) &&
            use_block_editor_for_post( $post ) &&
            apply_filters( 'cfs_hide_metaboxes_in_block_editor', false, $post, $field_groups )
        ) {
            return;
        }

        // Support for multiple metaboxes
        foreach ( $field_groups as $group_id => $title ) {

            // Get field group options
            $extras = get_post_meta( $group_id, 'cfs_extras', true );
            $context = isset( $extras['context'] ) ? $extras['context'] : 'normal';
            $priority = ( 'normal' == $context ) ? 'high' : 'core';

            if ( isset( $extras['hide_editor'] ) && 0 < (int) $extras['hide_editor'] ) {
                $hide_editor = true;
            }

            $args = [ 'box' => 'input', 'group_id' => $group_id ];
            add_meta_box( "cfs_input_$group_id", $title, [ $this, 'meta_box' ], $post->post_type, $context, $priority, $args );
            add_filter( "postbox_classes_{$post->post_type}_cfs_input_{$group_id}", 'cfs_postbox_classes' );
        }

        // Force editor support
        $has_editor = post_type_supports( $post->post_type, 'editor' );
        add_post_type_support( $post->post_type, 'editor' );

        if ( ! $has_editor || $hide_editor ) {
            echo '<style type="text/css">#poststuff .postarea { display: none; }</style>';
        }
    }
}

function cfs_postbox_classes( $classes ) {
    $classes[] = 'cfs_input';
    return $classes;
}
