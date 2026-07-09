<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


global $post;

/*---------------------------------------------------------------------------------------------
    Field management screen
---------------------------------------------------------------------------------------------*/

if ( ATSHIFT_CFS_FIELD_GROUP_POST_TYPE == $screen->post_type ) {
    $options_html = [];

    foreach ( atshift_fields_maintenance_for_custom_field_suite()->fields as $field_name => $field_data ) {
        $options_html[ $field_name ] = atshift_cfs_capture_output( function() use ( $field_name, $field_data ) {
            atshift_fields_maintenance_for_custom_field_suite()->fields[ $field_name ]->options_html( 'clone', $field_data );
        } );
    }

    $field_count = get_post_meta( $post->ID, 'cfs_fields', true );
    $field_count = is_array( $field_count ) ? count( $field_count ) : 0;

    // Build clone HTML
    $field = (object) [
        'id'            => 0,
        'parent_id'     => 0,
        'name'          => 'new_field',
        'label'         => __( 'New Field', 'atshift-fields-maintenance-for-custom-field-suite' ),
        'type'          => 'text',
        'notes'         => '',
        'weight'        => 'clone',
    ];

    $field_clone = atshift_cfs_capture_output( function() use ( $field ) {
        atshift_fields_maintenance_for_custom_field_suite()->field_html( $field );
    } );

    wp_add_inline_script(
        'atshift-cfs-fields',
        sprintf(
            "var CFS = CFS || {};\nCFS['field_index'] = %d;\nCFS['field_clone'] = %s;\nCFS['options_html'] = %s;",
            (int) $field_count,
            wp_json_encode( $field_clone ),
            wp_json_encode( $options_html )
        ),
        'before'
    );

    wp_add_inline_script(
        'atshift-cfs-fields',
        'CFS.messages = ' . wp_json_encode( [
            'disallowed_group_child' => __( 'Tabs, loops, accordions, conditional groups, and horizontal groups cannot be placed inside a horizontal group.', 'atshift-fields-maintenance-for-custom-field-suite' ),
            'disallowed_accordion_child' => __( 'Tabs cannot be placed inside an accordion.', 'atshift-fields-maintenance-for-custom-field-suite' ),
            'disallowed_conditional_child' => __( 'Tabs and conditional groups cannot be placed inside a Conditional Group.', 'atshift-fields-maintenance-for-custom-field-suite' ),
            'add_field_below'        => __( 'Add new field below', 'atshift-fields-maintenance-for-custom-field-suite' ),
            'add_field_inside'       => __( 'Add field inside', 'atshift-fields-maintenance-for-custom-field-suite' ),
            'duplicate_field_name_inline' => __( 'This field name is duplicated. Use a unique field name.', 'atshift-fields-maintenance-for-custom-field-suite' ),
            /* translators: %s: comma-separated duplicate field names. */
            'duplicate_field_names_alert' => __( 'Duplicate field names found: %s. Field names must be unique before saving.', 'atshift-fields-maintenance-for-custom-field-suite' ),
            'move_here'         => __( 'Move here', 'atshift-fields-maintenance-for-custom-field-suite' ),
            /* translators: %s: destination field label. */
            'outdent_to_container' => __( 'Move here: inside %s', 'atshift-fields-maintenance-for-custom-field-suite' ),
            'outdent_to_tab'       => __( 'Move here: inside the current Tab', 'atshift-fields-maintenance-for-custom-field-suite' ),
            'structure_badges'       => [
                'tab'         => __( 'TAB', 'atshift-fields-maintenance-for-custom-field-suite' ),
                'loop'        => __( 'LOOP', 'atshift-fields-maintenance-for-custom-field-suite' ),
                'group'       => __( 'GROUP', 'atshift-fields-maintenance-for-custom-field-suite' ),
                'accordion'   => __( 'ACCORDION', 'atshift-fields-maintenance-for-custom-field-suite' ),
                'conditional' => __( 'CONDITION', 'atshift-fields-maintenance-for-custom-field-suite' ),
            ],
        ] ) . ';',
        'before'
    );

}

/*---------------------------------------------------------------------------------------------
    Field input
---------------------------------------------------------------------------------------------*/

else {
    $hide_editor = false;
    $admin_styles = [];
    $print_admin_styles = static function( $styles ) {
        if ( empty( $styles ) ) {
            return;
        }

        echo '<style id="atshift-cfs-admin-inline-css">' . esc_html( implode( "\n", $styles ) ) . '</style>';
    };
    $field_groups = atshift_fields_maintenance_for_custom_field_suite()->api->get_matching_groups( $post->ID );
    $initial_field_group_ids = array_map( 'intval', array_keys( $field_groups ) );
    $term_placement_groups = [];

    // Taxonomy placement needs to react before the post is saved. Include groups
    // that match every other rule, then show/hide them as terms are selected.
    $candidate_groups = atshift_fields_maintenance_for_custom_field_suite()->api->get_matching_groups( [
        'post_ids' => [ $post->ID ],
        '_ignore_rule_types' => [ 'term_ids' ],
    ] );
    $all_field_groups = atshift_fields_maintenance_for_custom_field_suite()->field_group->load_field_groups();

    foreach ( $candidate_groups as $group_id => $group_title ) {
        $rules = isset( $all_field_groups[ $group_id ]['rules'] ) ? $all_field_groups[ $group_id ]['rules'] : [];
        if ( empty( $rules['term_ids']['values'] ) ) {
            continue;
        }

        $term_values = array_values( array_filter( array_map( 'absint', (array) $rules['term_ids']['values'] ) ) );
        if ( empty( $term_values ) ) {
            continue;
        }

        $operator = isset( $rules['term_ids']['operator'] ) ? (array) $rules['term_ids']['operator'] : [ '==' ];
        $term_placement_groups[ (int) $group_id ] = [
            'operator' => isset( $operator[0] ) && '!=' === $operator[0] ? '!=' : '==',
            'values' => $term_values,
            'hideEditor' => ! empty( $all_field_groups[ $group_id ]['extras']['hide_editor'] ),
        ];
        $field_groups[ $group_id ] = $group_title;
    }

    if ( ! empty( $field_groups ) ) {

        // Store field group IDs as an array for front-end forms
        atshift_fields_maintenance_for_custom_field_suite()->group_ids = array_keys( $field_groups );
        $native_field_group_ids = $initial_field_group_ids;
        if ( ! empty( $term_placement_groups ) ) {
            $native_field_group_ids = array_merge( $native_field_group_ids, array_map( 'intval', array_keys( $term_placement_groups ) ) );
        }
        $native_field_group_ids = array_values( array_unique( $native_field_group_ids ) );

        $native_fields = empty( $native_field_group_ids ) ? [] : atshift_fields_maintenance_for_custom_field_suite()->api->find_input_fields( [
            'group_id' => $native_field_group_ids,
            'field_type' => [ 'wp_category', 'wp_tag', 'featured_image' ],
        ] );
        $hide_native = [];

        foreach ( $native_fields as $native_field ) {
            $hide_native[ $native_field['type'] ] = true;
        }

        if ( ! empty( $hide_native ) ) {
            if ( isset( $hide_native['wp_category'] ) && empty( $term_placement_groups ) ) {
                remove_meta_box( 'categorydiv', $post->post_type, 'side' );
            }
            if ( isset( $hide_native['wp_tag'] ) && empty( $term_placement_groups ) ) {
                remove_meta_box( 'tagsdiv-post_tag', $post->post_type, 'side' );
            }
            if ( isset( $hide_native['featured_image'] ) && empty( $term_placement_groups ) ) {
                remove_meta_box( 'postimagediv', $post->post_type, 'side' );
            }

            $selectors = [];
            if ( isset( $hide_native['wp_category'] ) && empty( $term_placement_groups ) ) {
                $selectors[] = '#categorydiv';
            }
            if ( isset( $hide_native['wp_tag'] ) && empty( $term_placement_groups ) ) {
                $selectors[] = '#tagsdiv-post_tag';
            }
            if ( isset( $hide_native['featured_image'] ) && empty( $term_placement_groups ) ) {
                $selectors[] = '#postimagediv';
            }

            if ( ! empty( $selectors ) ) {
                $admin_styles[] = implode( ',', $selectors ) . '{display:none!important;}';
            }
        }

        if (
            function_exists( 'use_block_editor_for_post' ) &&
            use_block_editor_for_post( $post ) &&
            apply_filters( 'atshift_cfs_hide_metaboxes_in_block_editor', false, $post, $field_groups )
        ) {
            $print_admin_styles( $admin_styles );
            return;
        }

        // Support for multiple metaboxes
        foreach ( $field_groups as $group_id => $title ) {

            // Get field group options
            $extras = get_post_meta( $group_id, 'cfs_extras', true );
            $context = isset( $extras['context'] ) ? $extras['context'] : 'normal';
            $priority = ( 'normal' == $context ) ? 'high' : 'core';

            if (
                isset( $extras['hide_editor'] )
                && 0 < (int) $extras['hide_editor']
                && ! isset( $term_placement_groups[ (int) $group_id ] )
            ) {
                $hide_editor = true;
            }

            $args = [ 'box' => 'input', 'group_id' => $group_id ];
            add_meta_box( "cfs_input_$group_id", $title, [ $this, 'meta_box' ], $post->post_type, $context, $priority, $args );
            add_filter( "postbox_classes_{$post->post_type}_cfs_input_{$group_id}", 'atshift_cfs_postbox_classes' );
        }

        if ( ! empty( $term_placement_groups ) ) {
            if ( ! wp_script_is( 'atshift-cfs-term-placement', 'registered' ) ) {
                wp_register_script( 'atshift-cfs-term-placement', false, [ 'jquery' ], ATSHIFT_CFS_VERSION, true );
            }

            wp_enqueue_script( 'atshift-cfs-term-placement' );
            wp_add_inline_script(
                'atshift-cfs-term-placement',
                'jQuery(function($) {
                    var groups = ' . wp_json_encode( $term_placement_groups ) . ';
                    var nativeSelectors = ' . wp_json_encode( [
                        'wp_category'    => '#categorydiv',
                        'wp_tag'         => '#tagsdiv-post_tag',
                        'featured_image' => '#postimagediv',
                    ] ) . ';

                    function selectedTermIds() {
                        var selected = [];
                        $(\'ul.categorychecklist input[type="checkbox"]:checked, .cfs-wp-category-list input[type="checkbox"]:checked\').each(function() {
                            var value = parseInt(this.value, 10);
                            if (!isNaN(value)) {
                                selected.push(value);
                            }
                        });
                        return selected;
                    }

                    function visibleReplacementField(type) {
                        return $(\'.cfs_input:visible .field[data-type="\' + type + \'"]\').length > 0;
                    }

                    function refreshNativePanels() {
                        $.each(nativeSelectors, function(type, selector) {
                            var $panel = $(selector);
                            if ($panel.length) {
                                $panel.toggle(!visibleReplacementField(type));
                            }
                        });
                    }

                    function refreshTermPlacementGroups() {
                        var selected = selectedTermIds();
                        var hideEditor = false;
                        $.each(groups, function(groupId, rule) {
                            var matched = rule.values.some(function(termId) {
                                return selected.indexOf(parseInt(termId, 10)) !== -1;
                            });
                            var visible = rule.operator === "!=" ? !matched : matched;
                            var $box = $("#cfs_input_" + groupId);
                            $box.toggle(visible);
                            $box.find(":input").prop("disabled", !visible);
                            if (visible && rule.hideEditor) {
                                hideEditor = true;
                            }
                        });
                        $("#postdivrich, #poststuff .postarea").toggle(!hideEditor);
                        refreshNativePanels();
                    }

                    $(document).on("change", "ul.categorychecklist input[type=\"checkbox\"], .cfs-wp-category-list input[type=\"checkbox\"]", refreshTermPlacementGroups);
                    refreshTermPlacementGroups();
                });'
            );
        }

        // Force editor support
        $has_editor = post_type_supports( $post->post_type, 'editor' );
        add_post_type_support( $post->post_type, 'editor' );

        if ( ! $has_editor || $hide_editor ) {
            $admin_styles[] = '#postdivrich,#poststuff .postarea{display:none!important;}';
        }

        $print_admin_styles( $admin_styles );
    }
}

function atshift_cfs_postbox_classes( $classes ) {
    $classes[] = 'cfs_input';
    return $classes;
}
