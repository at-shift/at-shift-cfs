<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


global $wpdb;

// Post types
$post_types = [];
$types = get_post_types( [ 'public' => true ] );

foreach ( $types as $post_type ) {
    if ( ! in_array( $post_type, [ ATSHIFT_CFS_FIELD_GROUP_POST_TYPE, ATSHIFT_CFS_LEGACY_FIELD_GROUP_POST_TYPE, 'attachment' ], true ) ) {
        $post_types[] = $post_type;
    }
}

$extras = (array) get_post_meta( $post->ID, 'cfs_extras', true );
$display_setting_keys = [
    'hide_editor',
    'force_single_column_layout',
    'hide_categories',
    'hide_tags',
    'hide_featured_image',
    'hide_page_attributes',
    'hide_format',
    'hide_comments',
    'hide_discussion',
    'hide_excerpt',
    'hide_trackbacks',
    'hide_permalink',
    'hide_slug',
    'hide_author',
    'hide_custom_fields',
    'side_section_role_mode',
    'main_section_role_mode',
    'side_section_roles',
    'main_section_roles',
];

foreach ( $display_setting_keys as $display_setting_key ) {
    if ( ! isset( $extras[ $display_setting_key ] ) ) {
        $extras[ $display_setting_key ] = '';
    }
}
if ( ! isset( $extras['order'] ) ) {
    $extras['order'] = 0;
}
if ( ! isset( $extras['context'] ) ) {
    $extras['context'] = 'normal';
}

$role_choices = atshift_fields_maintenance_for_custom_field_suite()->init->get_role_choices();
$role_mode_choices = [
    'all'           => __( 'All roles', 'atshift-fields-maintenance-for-custom-field-suite' ),
    'except_admins' => __( 'All roles except Administrators / Web administrators', 'atshift-fields-maintenance-for-custom-field-suite' ),
    'selected'      => __( 'Selected roles only', 'atshift-fields-maintenance-for-custom-field-suite' ),
];

$native_field_display_setting_key = static function( $field ) {
    $field_type = isset( $field['type'] ) ? (string) $field['type'] : '';

    if ( 'post_content' === $field_type ) {
        return 'hide_editor';
    }

    if ( 'wp_category' === $field_type ) {
        $taxonomy_name = isset( $field['options']['taxonomy'] ) ? sanitize_key( $field['options']['taxonomy'] ) : 'category';
        return 'category' === $taxonomy_name ? 'hide_categories' : '';
    }

    if ( 'wp_tag' === $field_type ) {
        return 'hide_tags';
    }

    if ( 'featured_image' === $field_type ) {
        return 'hide_featured_image';
    }

    return '';
};

$normalize_placement_rules = static function( $rules ) {
    $normalized = [];

    foreach ( [ 'post_types', 'post_formats', 'user_roles', 'post_ids', 'term_ids', 'page_templates' ] as $rule_type ) {
        $rule = isset( $rules[ $rule_type ] ) && is_array( $rules[ $rule_type ] ) ? $rules[ $rule_type ] : [];
        $operator = isset( $rule['operator'] ) ? (array) $rule['operator'] : [ '==' ];
        $operator = isset( $operator[0] ) && '!=' === $operator[0] ? '!=' : '==';
        $values = isset( $rule['values'] ) ? array_filter( array_map( 'strval', (array) $rule['values'] ) ) : [];
        $values = array_values( array_unique( $values ) );
        sort( $values, SORT_STRING );

        $normalized[ $rule_type ] = [
            'operator' => empty( $values ) ? '==' : $operator,
            'values'   => $values,
        ];
    }

    return $normalized;
};

$display_setting_locks = [];
$current_fields = (array) get_post_meta( $post->ID, 'cfs_fields', true );

foreach ( $current_fields as $current_field ) {
    $display_setting_key = $native_field_display_setting_key( $current_field );

    if ( '' !== $display_setting_key ) {
        $display_setting_locks[ $display_setting_key ] = [
            'type' => 'current_native_field',
        ];
    }
}

$current_rules = (array) get_post_meta( $post->ID, 'cfs_rules', true );
$normalized_current_rules = $normalize_placement_rules( $current_rules );
$field_groups = atshift_fields_maintenance_for_custom_field_suite()->field_group->load_field_groups();

foreach ( $field_groups as $other_group_id => $field_group ) {
    if ( (int) $other_group_id === (int) $post->ID ) {
        continue;
    }

    $other_rules = isset( $field_group['rules'] ) && is_array( $field_group['rules'] ) ? $field_group['rules'] : [];

    if ( $normalized_current_rules !== $normalize_placement_rules( $other_rules ) ) {
        continue;
    }

    $other_extras = isset( $field_group['extras'] ) && is_array( $field_group['extras'] ) ? $field_group['extras'] : [];
    $other_fields = isset( $field_group['fields'] ) && is_array( $field_group['fields'] ) ? $field_group['fields'] : [];

    foreach ( $other_fields as $other_field ) {
        $display_setting_key = $native_field_display_setting_key( $other_field );

        if ( '' !== $display_setting_key && ! isset( $display_setting_locks[ $display_setting_key ] ) ) {
            $display_setting_locks[ $display_setting_key ] = [
                'type'     => 'field_group',
                'group_id' => (int) $other_group_id,
                'title'    => isset( $field_group['title'] ) ? $field_group['title'] : sprintf( '#%d', (int) $other_group_id ),
            ];
        }
    }

    foreach ( $display_setting_keys as $display_setting_key ) {
        if ( 0 !== strpos( $display_setting_key, 'hide_' ) && 'force_single_column_layout' !== $display_setting_key ) {
            continue;
        }

        if ( empty( $extras[ $display_setting_key ] ) && ! empty( $other_extras[ $display_setting_key ] ) && ! isset( $display_setting_locks[ $display_setting_key ] ) ) {
            $display_setting_locks[ $display_setting_key ] = [
                'type'     => 'field_group',
                'group_id' => (int) $other_group_id,
                'title'    => isset( $field_group['title'] ) ? $field_group['title'] : sprintf( '#%d', (int) $other_group_id ),
            ];
        }
    }
}

$render_display_setting = static function( $key, $setting ) use ( $extras, $display_setting_locks ) {
    $setting = is_array( $setting ) ? $setting : [ 'message' => $setting ];
    $message = isset( $setting['message'] ) ? $setting['message'] : '';
    $tooltip = isset( $setting['tooltip'] ) ? $setting['tooltip'] : '';
    $lock = isset( $display_setting_locks[ $key ] ) ? $display_setting_locks[ $key ] : null;

    if ( $lock ) {
        ?>
        <div class="cfs-extras-display-settings-option cfs-extras-display-settings-option-disabled">
            <label>
                <input type="checkbox" checked disabled />
                <span>
                    <?php echo esc_html( $message ); ?>
                    <?php if ( '' !== $tooltip ) : ?>
                        <span class="cfs_tooltip">?
                            <span class="tooltip_inner"><?php echo esc_html( $tooltip ); ?></span>
                        </span>
                    <?php endif; ?>
                    <em>
                        <?php
                        if ( 'current_native_field' === $lock['type'] ) {
                            esc_html_e( 'Already controlled by a native field in this field group.', 'atshift-fields-maintenance-for-custom-field-suite' );
                        }
                        else {
                            printf(
                                /* translators: %s: Field group title. */
                                esc_html__( 'Already controlled by "%s".', 'atshift-fields-maintenance-for-custom-field-suite' ),
                                esc_html( $lock['title'] )
                            );
                        }
                        ?>
                    </em>
                </span>
            </label>
        </div>
        <?php
        return;
    }

    ?>
    <div class="cfs-extras-display-settings-option">
        <label>
            <input type="hidden" name="cfs[extras][<?php echo esc_attr( $key ); ?>]" value="0" />
            <input type="checkbox" name="cfs[extras][<?php echo esc_attr( $key ); ?>]" value="1"<?php checked( ! empty( $extras[ $key ] ) ); ?> />
            <span>
                <?php echo esc_html( $message ); ?>
                <?php if ( '' !== $tooltip ) : ?>
                    <span class="cfs_tooltip">?
                        <span class="tooltip_inner"><?php echo esc_html( $tooltip ); ?></span>
                    </span>
                <?php endif; ?>
            </span>
        </label>
    </div>
    <?php
};

$render_role_controls = static function( $section, $label ) use ( $extras, $role_choices, $role_mode_choices ) {
    $mode_key = $section . '_section_role_mode';
    $roles_key = $section . '_section_roles';
    $selected_mode = isset( $extras[ $mode_key ] ) && '' !== $extras[ $mode_key ] ? $extras[ $mode_key ] : 'all';
    $selected_roles = isset( $extras[ $roles_key ] ) ? array_filter( array_map( 'sanitize_key', (array) $extras[ $roles_key ] ) ) : [];
    ?>
    <div class="cfs-extras-display-settings-roles">
        <label class="cfs-extras-display-settings-roles-label"><?php echo esc_html( $label ); ?></label>
        <?php
            atshift_fields_maintenance_for_custom_field_suite()->create_field( [
                'type'        => 'select',
                'input_name'  => "cfs[extras][$mode_key]",
                'value'       => $selected_mode,
                'options'     => [
                    'choices' => $role_mode_choices,
                ],
            ] );
        ?>
        <input type="hidden" name="cfs[extras][<?php echo esc_attr( $roles_key ); ?>][]" value="" />
        <?php
            atshift_fields_maintenance_for_custom_field_suite()->create_field( [
                'type'        => 'select',
                'input_class' => 'select2 cfs-extra-display-role-select',
                'input_name'  => "cfs[extras][$roles_key]",
                'value'       => $selected_roles,
                'options'     => [
                    'multiple'    => '1',
                    'choices'     => $role_choices,
                    'placeholder' => __( 'Select roles when using selected roles only', 'atshift-fields-maintenance-for-custom-field-suite' ),
                ],
            ] );
        ?>
    </div>
    <?php
};

$render_display_section = static function( $title, $items, $roles_callback = null ) use ( $render_display_setting ) {
    ?>
    <div class="cfs-extras-display-settings-section">
        <h4><?php echo esc_html( $title ); ?></h4>
        <div class="cfs-extras-display-settings-options">
            <?php
            foreach ( $items as $key => $message ) {
                $render_display_setting( $key, $message );
            }
            ?>
        </div>
        <?php
        if ( is_callable( $roles_callback ) ) {
            $roles_callback();
        }
        ?>
    </div>
    <?php
};

?>

<table>
    <tr>
        <td class="label">
            <label>
                <?php esc_html_e( 'Order', 'atshift-fields-maintenance-for-custom-field-suite' ); ?>
                <div class="cfs_tooltip">?
                    <div class="tooltip_inner"><?php esc_html_e( 'The field group with the lowest order will appear first.', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></div>
                </div>
            </label>
        </td>
        <td style="vertical-align:top">
            <input type="text" name="cfs[extras][order]" value="<?php echo esc_attr( $extras['order'] ); ?>" style="width:80px" />
        </td>
    </tr>
    <tr>
        <td class="label">
            <label><?php esc_html_e( 'Field Group Position', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></label>
        </td>
        <td style="vertical-align:top">
            <input type="radio" name="cfs[extras][context]" value="normal"<?php echo ( $extras['context'] == 'normal' ) ? ' checked' : ''; ?> /> <?php esc_html_e( 'Normal', 'atshift-fields-maintenance-for-custom-field-suite' ); ?> &nbsp; &nbsp;
            <input type="radio" name="cfs[extras][context]" value="side"<?php echo ( $extras['context'] == 'side' ) ? ' checked' : ''; ?> /> <?php esc_html_e( 'Side', 'atshift-fields-maintenance-for-custom-field-suite' ); ?>
            <p class="description cfs-extras-setting-description">
                <?php esc_html_e( 'Controls where this field group appears on the edit screen.', 'atshift-fields-maintenance-for-custom-field-suite' ); ?>
            </p>
        </td>
    </tr>
    <tr class="cfs-extras-display-settings-row">
        <td colspan="2">
            <details class="cfs-extras-display-settings-details">
                <summary class="cfs-extras-display-settings-summary">
                    <span><?php esc_html_e( 'Native Editor Display Settings', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></span>
                </summary>
                <div class="cfs-extras-display-settings">
                    <p class="description cfs-extras-setting-description">
                        <?php esc_html_e( 'These settings hide native WordPress editor sections for matching posts, regardless of where this field group is displayed.', 'atshift-fields-maintenance-for-custom-field-suite' ); ?>
                    </p>
                    <?php
                        $render_display_section(
                            __( 'Screen Layout', 'atshift-fields-maintenance-for-custom-field-suite' ),
                            [
                                'hide_editor'                => __( 'Hide the content editor (also hides the Gutenberg / block editor for matching posts)', 'atshift-fields-maintenance-for-custom-field-suite' ),
                                'force_single_column_layout' => [
                                    'message' => __( 'Force the post edit screen layout to 1 column', 'atshift-fields-maintenance-for-custom-field-suite' ),
                                    'tooltip' => __( 'Third-party side meta boxes are not hidden; in the 1-column layout, they appear below the main editor area.', 'atshift-fields-maintenance-for-custom-field-suite' ),
                                ],
                            ]
                        );

                        $render_display_section(
                            __( 'Side Sections', 'atshift-fields-maintenance-for-custom-field-suite' ),
                            [
                                'hide_categories'      => __( 'Hide categories', 'atshift-fields-maintenance-for-custom-field-suite' ),
                                'hide_tags'            => __( 'Hide tags', 'atshift-fields-maintenance-for-custom-field-suite' ),
                                'hide_featured_image'  => __( 'Hide featured image', 'atshift-fields-maintenance-for-custom-field-suite' ),
                                'hide_page_attributes' => __( 'Hide page attributes', 'atshift-fields-maintenance-for-custom-field-suite' ),
                                'hide_format'          => __( 'Hide format', 'atshift-fields-maintenance-for-custom-field-suite' ),
                            ],
                            static function() use ( $render_role_controls ) {
                                $render_role_controls( 'side', __( 'Hiding behavior', 'atshift-fields-maintenance-for-custom-field-suite' ) );
                            }
                        );

                        $render_display_section(
                            __( 'Main Sections', 'atshift-fields-maintenance-for-custom-field-suite' ),
                            [
                                'hide_permalink'     => __( 'Hide permalink', 'atshift-fields-maintenance-for-custom-field-suite' ),
                                'hide_excerpt'       => __( 'Hide excerpt', 'atshift-fields-maintenance-for-custom-field-suite' ),
                                'hide_discussion'    => __( 'Hide discussion', 'atshift-fields-maintenance-for-custom-field-suite' ),
                                'hide_trackbacks'    => __( 'Hide trackbacks', 'atshift-fields-maintenance-for-custom-field-suite' ),
                                'hide_comments'      => __( 'Hide comments', 'atshift-fields-maintenance-for-custom-field-suite' ),
                                'hide_author'        => __( 'Hide author', 'atshift-fields-maintenance-for-custom-field-suite' ),
                                'hide_slug'          => __( 'Hide slug', 'atshift-fields-maintenance-for-custom-field-suite' ),
                                'hide_custom_fields' => __( 'Hide custom fields', 'atshift-fields-maintenance-for-custom-field-suite' ),
                            ],
                            static function() use ( $render_role_controls ) {
                                $render_role_controls( 'main', __( 'Hiding behavior', 'atshift-fields-maintenance-for-custom-field-suite' ) );
                            }
                        );
                    ?>
                </div>
            </details>
        </td>
    </tr>

</table>
