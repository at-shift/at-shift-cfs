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
            'automatically_named_when_saved' => __( 'Automatically named when saved', 'atshift-fields-maintenance-for-custom-field-suite' ),
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
    $hide_page_attributes = false;
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
            'hidePageAttributes' => ! empty( $all_field_groups[ $group_id ]['extras']['hide_page_attributes'] ),
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
            'field_type' => [ 'post_title', 'post_publish', 'wp_category', 'wp_tag', 'featured_image' ],
        ] );
        $hide_native = [];
        $native_panel_rules = [];
        $taxonomy_meta_box_id = static function( $taxonomy_name ) {
            $taxonomy = get_taxonomy( $taxonomy_name );

            if ( ! $taxonomy ) {
                return '';
            }

            return $taxonomy->hierarchical ? $taxonomy_name . 'div' : 'tagsdiv-' . $taxonomy_name;
        };
        $taxonomy_meta_box_selector = static function( $taxonomy_name ) use ( $taxonomy_meta_box_id ) {
            $meta_box_id = $taxonomy_meta_box_id( $taxonomy_name );
            return '' === $meta_box_id ? '' : '#' . $meta_box_id;
        };
        $category_taxonomy_name = static function( $field ) {
            $field_object = (object) $field;

            if ( isset( atshift_fields_maintenance_for_custom_field_suite()->fields['wp_category'] ) ) {
                return atshift_fields_maintenance_for_custom_field_suite()->fields['wp_category']->get_taxonomy_name( $field_object );
            }

            if ( isset( $field['options']['taxonomy'] ) ) {
                $taxonomy_name = sanitize_key( $field['options']['taxonomy'] );
                return taxonomy_exists( $taxonomy_name ) ? $taxonomy_name : '';
            }

            return taxonomy_exists( 'category' ) ? 'category' : '';
        };
        $add_native_panel_rule = static function( $type, $selector ) use ( &$native_panel_rules ) {
            if ( '' === $selector ) {
                return;
            }

            if ( ! isset( $native_panel_rules[ $selector ] ) ) {
                $native_panel_rules[ $selector ] = [
                    'selector' => $selector,
                    'types'    => [],
                ];
            }

            $native_panel_rules[ $selector ]['types'][] = $type;
            $native_panel_rules[ $selector ]['types'] = array_values( array_unique( $native_panel_rules[ $selector ]['types'] ) );
        };

        foreach ( $native_fields as $native_field ) {
            $hide_native[ $native_field['type'] ] = true;

            if ( 'wp_category' === $native_field['type'] ) {
                $add_native_panel_rule( 'wp_category', $taxonomy_meta_box_selector( $category_taxonomy_name( $native_field ) ) );
            }
            elseif ( 'wp_tag' === $native_field['type'] ) {
                $add_native_panel_rule( 'wp_tag', $taxonomy_meta_box_selector( 'post_tag' ) );
            }
            elseif ( 'featured_image' === $native_field['type'] ) {
                $add_native_panel_rule( 'featured_image', '#postimagediv' );
            }
            elseif ( 'post_title' === $native_field['type'] ) {
                $add_native_panel_rule( 'post_title', '#titlediv' );
            }
            elseif ( 'post_publish' === $native_field['type'] ) {
                $add_native_panel_rule( 'post_publish', '#submitdiv' );
            }
        }
        $native_panel_rules = array_values( $native_panel_rules );

        if ( ! empty( $hide_native ) ) {
            $selectors = [];

            if ( empty( $term_placement_groups ) ) {
                foreach ( $native_panel_rules as $native_panel_rule ) {
                    $selectors[] = $native_panel_rule['selector'];

                    foreach ( (array) $native_panel_rule['types'] as $type ) {
                        if ( 'featured_image' === $type ) {
                            remove_meta_box( 'postimagediv', $post->post_type, 'side' );
                        }
                    }

                    $selector = $native_panel_rule['selector'];
                    if ( 0 === strpos( $selector, '#' ) && ! in_array( $selector, [ '#postimagediv', '#submitdiv' ], true ) ) {
                        remove_meta_box( substr( $selector, 1 ), $post->post_type, 'side' );
                    }
                }
            }

            if ( ! empty( $selectors ) ) {
                $admin_styles[] = implode( ',', array_unique( $selectors ) ) . '{display:none!important;}';
            }
        }

        if (
            function_exists( 'use_block_editor_for_post' ) &&
            use_block_editor_for_post( $post ) &&
            atshift_cfs_apply_filters_compat( 'cfs_hide_metaboxes_in_block_editor', 'atshift_cfs_hide_metaboxes_in_block_editor', false, $post, $field_groups )
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

            if (
                isset( $extras['hide_page_attributes'] )
                && 0 < (int) $extras['hide_page_attributes']
                && ! isset( $term_placement_groups[ (int) $group_id ] )
            ) {
                $hide_page_attributes = true;
            }

            $args = [
                'box' => 'input',
                'group_id' => $group_id,
                '__block_editor_compatible_meta_box' => true,
            ];
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
                    var nativePanelRules = ' . wp_json_encode( $native_panel_rules ) . ';

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

                    function visibleReplacementField(types) {
                        var visible = false;
                        $.each(types || [], function(index, type) {
                            if ($(\'.cfs_input:visible .field[data-type="\' + type + \'"]\').length > 0) {
                                visible = true;
                                return false;
                            }
                        });
                        return visible;
                    }

                    function refreshNativePanels() {
                        $.each(nativePanelRules, function(index, rule) {
                            var $panel = $(rule.selector);
                            if ($panel.length) {
                                $panel.toggle(!visibleReplacementField(rule.types));
                            }
                        });
                    }

                    function refreshTermPlacementGroups() {
                        var selected = selectedTermIds();
                        var hideEditor = false;
                        var hidePageAttributes = false;
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
                            if (visible && rule.hidePageAttributes) {
                                hidePageAttributes = true;
                            }
                        });
                        $("#postdivrich, #poststuff .postarea").toggle(!hideEditor);
                        $("#pageparentdiv").toggle(!hidePageAttributes);
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

        if ( $hide_page_attributes ) {
            $admin_styles[] = '#pageparentdiv{display:none!important;}';
        }

        $print_admin_styles( $admin_styles );
    }
}

if ( ! function_exists( 'atshift_cfs_postbox_classes' ) ) {
    function atshift_cfs_postbox_classes( $classes ) {
        $classes[] = 'cfs_input';
        return $classes;
    }
}
