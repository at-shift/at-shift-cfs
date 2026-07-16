<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


global $post, $wpdb, $wp_roles;

$text_domain = 'atshift-fields-maintenance-for-custom-field-suite';
$equals_text = __( 'equals', 'atshift-fields-maintenance-for-custom-field-suite' );
$not_equals_text = __( 'is not', 'atshift-fields-maintenance-for-custom-field-suite' );
$rules = (array) get_post_meta( $post->ID, 'cfs_rules', true );

// Populate rules if empty
$rule_types = [
    'post_types',
    'post_formats',
    'user_roles',
    'post_ids',
    'term_ids',
    'page_templates'
];

foreach ( $rule_types as $type ) {
    if ( ! isset( $rules[ $type ] ) ) {
        $rules[ $type ] = [ 'operator' => [ '==' ], 'values' => [] ];
    }
}

$get_post_type_rule_label = static function( $post_type ) {
    $post_type_object = get_post_type_object( $post_type );

    if ( $post_type_object && ! empty( $post_type_object->labels->singular_name ) ) {
        return $post_type_object->labels->singular_name;
    }

    return $post_type;
};

$get_taxonomy_rule_label = static function( $taxonomy ) {
    $taxonomy_object = get_taxonomy( $taxonomy );

    if ( $taxonomy_object && ! empty( $taxonomy_object->labels->singular_name ) ) {
        return $taxonomy_object->labels->singular_name;
    }

    return $taxonomy;
};

// Post types
$post_types = [];
$types = get_post_types();
foreach ( $types as $post_type ) {
    if ( ! in_array( $post_type, [ ATSHIFT_CFS_FIELD_GROUP_POST_TYPE, ATSHIFT_CFS_LEGACY_FIELD_GROUP_POST_TYPE, 'attachment', 'revision', 'nav_menu_item' ], true ) ) {
        $post_types[ $post_type ] = $get_post_type_rule_label( $post_type );
    }
}

// Post formats
$post_formats = [];
if ( current_theme_supports( 'post-formats' ) ) {
    $post_format_strings = get_post_format_strings();
    $standard_format_label = __( 'Standard', $text_domain );
    $post_formats = [ 'standard' => $standard_format_label ];
    $post_formats_slugs = get_theme_support( 'post-formats' );

    if ( is_array( $post_formats_slugs[0] ) ) {
        foreach ( $post_formats_slugs[0] as $post_format ) {
            $post_formats[ $post_format ] = $post_format_strings[ $post_format ] ?? get_post_format_string( $post_format );
        }
    }
}

// User roles
$user_roles = [];
foreach ( $wp_roles->roles as $key => $role ) {
    $role_name = $role['name'] ?? $key;
    $role_label = translate_user_role( $role_name );

    if ( $role_label === $role_name ) {
        $role_label = __( $role_name, $text_domain );
    }

    $user_roles[ $key ] = $role_label;
}

// Post IDs
$post_ids = [];
$json_posts = [];

if ( ! empty( $rules['post_ids']['values'] ) ) {
    $post_in = array_values( array_filter( array_map( 'absint', (array) $rules['post_ids']['values'] ) ) );

    $results = [];
    if ( ! empty( $post_in ) ) {
        $post_in_placeholders = implode( ',', array_fill( 0, count( $post_in ), '%d' ) );
        $sql = $wpdb->prepare(
            "
        SELECT ID, post_type, post_title, post_parent
        FROM $wpdb->posts
        WHERE ID IN ($post_in_placeholders)
        ORDER BY post_type, post_title",
            $post_in
        );
        // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- $sql is prepared above with sanitized post IDs.
        $results = $wpdb->get_results( $sql );
    }

    foreach ( $results as $result ) {
        $parent = '';

        if (
            isset( $result->post_parent ) &&
            absint( $result->post_parent ) > 0 &&
            $parent = get_post( $result->post_parent )
        ) {
            $parent = "$parent->post_title >";
        }

        $post_type_label = $get_post_type_rule_label( $result->post_type );

        $json_posts[] = [
            'id' => $result->ID,
            'text' => sprintf( '(%s) %s %s (#%d)', $post_type_label, $parent, $result->post_title, $result->ID ),
        ];
        $post_ids[] = $result->ID;
    }
}

// Term IDs
$sql = "
SELECT t.term_id, t.name, tt.taxonomy
FROM $wpdb->terms t
INNER JOIN $wpdb->term_taxonomy tt ON tt.term_id = t.term_id AND tt.taxonomy != 'post_tag'
ORDER BY tt.parent, tt.taxonomy, t.name";
// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Query contains only WordPress table names and fixed taxonomy filtering.
$results = $wpdb->get_results( $sql );

foreach ( $results as $result ) {
    $taxonomy_label = $get_taxonomy_rule_label( $result->taxonomy );

    $term_ids[ $result->term_id ] = sprintf( '(%s) %s', $taxonomy_label, $result->name );
}

// Page templates
$page_templates = [];
$templates = get_page_templates();

foreach ( $templates as $template_name => $filename ) {
    $page_templates[ $filename ] = $template_name;
}

?>

<?php wp_add_inline_script( 'atshift-cfs-fields', atshift_cfs_capture_output( function() use ( $json_posts ) { ?>
(function($) {
    $(function() {
        var cfs_nonce = '<?php echo esc_js( wp_create_nonce( 'atshift_cfs_admin_nonce' ) ); ?>';

        function updateRuleSelect2State($select) {
            var instance = $select.data('select2');
            var value = $select.val();
            var hasValue = $.isArray(value) ? value.length > 0 : !!value;

            if (instance && instance.$container) {
                instance.$container.toggleClass('cfs-select2-has-selection', hasValue);
            }
        }

        function activateRuleSelect2($select) {
            var instance = $select.data('select2');

            if (instance && instance.$container) {
                instance.$container.addClass('cfs-select2-is-active');
            }
        }

        function getRuleSelect2Dropdown($select) {
            var instance = $select.data('select2');
            var $dropdown = $();

            if (instance && instance.dropdown) {
                if (instance.dropdown.$dropdownContainer) {
                    $dropdown = instance.dropdown.$dropdownContainer;
                } else if (instance.dropdown.$dropdown) {
                    $dropdown = instance.dropdown.$dropdown;
                }
            }

            if (!$dropdown.length && instance && instance.$container) {
                $dropdown = $('.select2-container--open').filter(function() {
                    return !$(this).is(instance.$container) && 0 < $(this).find('.select2-dropdown').length;
                }).last();
            }

            return $dropdown;
        }

        function positionRuleSelect2Dropdown($select) {
            var instance = $select.data('select2');
            var $dropdown = getRuleSelect2Dropdown($select);
            var $dropdownContainer;
            var $dropdownPanel;
            var offset;

            if (!instance || !instance.$container || !$dropdown.length) {
                return;
            }

            $dropdownContainer = $dropdown.hasClass('select2-dropdown') ? $dropdown.parent() : $dropdown;
            $dropdownPanel = $dropdown.hasClass('select2-dropdown') ? $dropdown : $dropdown.find('.select2-dropdown').first();
            offset = instance.$container.offset();

            if (!offset || !$dropdownContainer.length) {
                return;
            }

            $dropdownContainer.css({
                left: Math.round(offset.left) + 'px',
                top: Math.round(offset.top + instance.$container.outerHeight(false)) + 'px',
                width: Math.round(instance.$container.outerWidth(false)) + 'px'
            });

            if ($dropdownPanel.length) {
                $dropdownPanel.removeClass('select2-dropdown--above').addClass('select2-dropdown--below');
            }
        }

        function keepRuleSelect2DropdownBelow($select) {
            positionRuleSelect2Dropdown($select);

            if ('function' === typeof window.requestAnimationFrame) {
                window.requestAnimationFrame(function() {
                    positionRuleSelect2Dropdown($select);
                });
            }

            setTimeout(function() {
                positionRuleSelect2Dropdown($select);
            }, 0);
        }

        function bindRuleSelect2State($select) {
            updateRuleSelect2State($select);

            $select.on('change select2:select select2:unselect select2:clear', function() {
                var $current = $(this);
                var instance = $current.data('select2');

                updateRuleSelect2State($current);

                if (instance && instance.$container && instance.$container.hasClass('cfs-select2-is-active')) {
                    keepRuleSelect2DropdownBelow($current);
                }
            });

            $select.on('select2:opening', function() {
                var $current = $(this);

                updateRuleSelect2State($current);
                activateRuleSelect2($current);
            });

            $select.on('select2:open', function() {
                var $current = $(this);

                activateRuleSelect2($current);
                keepRuleSelect2DropdownBelow($current);
            });

            $select.on('select2:close', function() {
                var instance = $(this).data('select2');

                if (instance && instance.$container) {
                    instance.$container.removeClass('cfs-select2-is-active');
                }

                updateRuleSelect2State($(this));
            });
        }

        var ruleSelect2MaxSelectedText = '<?php echo esc_js( __( '%s allowed', 'atshift-fields-maintenance-for-custom-field-suite' ) ); ?>';
        var formatRuleSelect2MaxSelected = function(maximum) {
            if (maximum && 'object' === typeof maximum && maximum.maximum) {
                maximum = maximum.maximum;
            }
            maximum = parseInt(maximum, 10);
            if (!maximum || 1 > maximum) {
                maximum = 1;
            }
            return ruleSelect2MaxSelectedText.replace('%s', maximum);
        };
        var ruleSelect2Language = {
            errorLoading: function() {
                return '<?php echo esc_js( __( 'The results could not be loaded.', 'atshift-fields-maintenance-for-custom-field-suite' ) ); ?>';
            },
            inputTooShort: function(args) {
                var remaining = args.minimum - args.input.length;

                if (1 === remaining) {
                    return '<?php echo esc_js( __( 'Please enter 1 more character', 'atshift-fields-maintenance-for-custom-field-suite' ) ); ?>';
                }

                return '<?php echo esc_js( __( 'Please enter %s or more characters', 'atshift-fields-maintenance-for-custom-field-suite' ) ); ?>'.replace('%s', remaining);
            },
            loadingMore: function() {
                return '<?php echo esc_js( __( 'Loading more results...', 'atshift-fields-maintenance-for-custom-field-suite' ) ); ?>';
            },
            noResults: function() {
                return '<?php echo esc_js( __( 'No results found', 'atshift-fields-maintenance-for-custom-field-suite' ) ); ?>';
            },
            searching: function() {
                return '<?php echo esc_js( __( 'Searching...', 'atshift-fields-maintenance-for-custom-field-suite' ) ); ?>';
            },
            maximumSelected: function(args) {
                return formatRuleSelect2MaxSelected(args.maximum);
            }
        };

        var ruleSelect2LegacyLanguage = {
            formatInputTooShort: function(input, minimum) {
                var remaining = minimum - input.length;

                if (1 === remaining) {
                    return '<?php echo esc_js( __( 'Please enter 1 more character', 'atshift-fields-maintenance-for-custom-field-suite' ) ); ?>';
                }

                return '<?php echo esc_js( __( 'Please enter %s or more characters', 'atshift-fields-maintenance-for-custom-field-suite' ) ); ?>'.replace('%s', remaining);
            },
            formatSelectionTooBig: function(limit) {
                return formatRuleSelect2MaxSelected(limit);
            },
            formatNoMatches: function() {
                return '<?php echo esc_js( __( 'No results found', 'atshift-fields-maintenance-for-custom-field-suite' ) ); ?>';
            },
            formatSearching: function() {
                return '<?php echo esc_js( __( 'Searching...', 'atshift-fields-maintenance-for-custom-field-suite' ) ); ?>';
            }
        };

        if ($.fn.select2 && $.fn.select2.defaults && $.fn.select2.defaults.set) {
            $.fn.select2.defaults.set('language', ruleSelect2Language);
        }

        $('.select2').select2($.extend({
            placeholder: '<?php echo esc_js( __( 'Leave blank to skip this rule', 'atshift-fields-maintenance-for-custom-field-suite' ) ); ?>',
            language: ruleSelect2Language,
            width: 'resolve'
        }, ruleSelect2LegacyLanguage)).each(function() {
            bindRuleSelect2State($(this));
        });

        $('.select2-ajax').select2($.extend({
            placeholder: '<?php echo esc_js( __( 'Leave blank to skip this rule', 'atshift-fields-maintenance-for-custom-field-suite' ) ); ?>',
            language: ruleSelect2Language,
            minimumInputLength: 2,
            width: '99.95%',
            ajax: {
                url: ajaxurl,
                type: 'POST',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term,
                        action: 'atshift_cfs_ajax_handler',
                        action_type: 'search_posts',
                        nonce: cfs_nonce
                    }
                },
                processResults: function(data) {
                    return { results: data };
                }
            }
        }, ruleSelect2LegacyLanguage)).each(function() {
            bindRuleSelect2State($(this));
        });
    });
})(jQuery);
<?php } ) ); ?>

<table>
    <tr>
        <td class="label cfs-rule-label">
            <label><?php esc_html_e( 'Post Types', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></label>
        </td>
        <td class="cfs-rule-operator">
            <?php
                atshift_fields_maintenance_for_custom_field_suite()->create_field( [
                    'type' => 'select',
                    'input_name' => "cfs[rules][operator][post_types]",
                    'options' => [
                        'choices' => [
                            '==' => $equals_text,
                            '!=' => $not_equals_text,
                        ],
                        'force_single' => true,
                    ],
                    'value' => $rules['post_types']['operator'],
                ] );
            ?>
        </td>
        <td class="cfs-rule-value">
            <?php
                atshift_fields_maintenance_for_custom_field_suite()->create_field( [
                    'type' => 'select',
                    'input_class' => 'select2',
                    'input_name' => "cfs[rules][post_types]",
                    'options' => [ 'multiple' => '1', 'choices' => $post_types ],
                    'value' => $rules['post_types']['values'],
                ] );
            ?>
        </td>
    </tr>
    <?php if ( current_theme_supports( 'post-formats' ) && count( $post_formats ) ) : ?>
        <tr>
            <td class="label cfs-rule-label">
                <label><?php esc_html_e( 'Post Formats', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></label>
            </td>
            <td class="cfs-rule-operator">
                <?php
                atshift_fields_maintenance_for_custom_field_suite()->create_field( [
                    'type' => 'select',
                    'input_name' => "cfs[rules][operator][post_formats]",
                    'options' => [
                        'choices' => [
                            '==' => $equals_text,
                            '!=' => $not_equals_text,
                        ],
                        'force_single' => true,
                    ],
                    'value' => $rules['post_formats']['operator'],
                ] );
                ?>
            </td>
            <td class="cfs-rule-value">
                <?php
                atshift_fields_maintenance_for_custom_field_suite()->create_field( [
                    'type' => 'select',
                    'input_class' => 'select2',
                    'input_name' => "cfs[rules][post_formats]",
                    'options' => [ 'multiple' => '1', 'choices' => $post_formats ],
                    'value' => $rules['post_formats']['values'],
                ] );
                ?>
            </td>
        </tr>
    <?php endif; ?>
    <tr>
        <td class="label cfs-rule-label">
            <label><?php esc_html_e( 'User Roles', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></label>
        </td>
        <td class="cfs-rule-operator">
            <?php
                atshift_fields_maintenance_for_custom_field_suite()->create_field( [
                    'type' => 'select',
                    'input_name' => "cfs[rules][operator][user_roles]",
                    'options' => [
                        'choices' => [
                            '==' => $equals_text,
                            '!=' => $not_equals_text,
                        ],
                        'force_single' => true,
                    ],
                    'value' => $rules['user_roles']['operator'],
                ] );
            ?>
        </td>
        <td class="cfs-rule-value">
            <?php
                atshift_fields_maintenance_for_custom_field_suite()->create_field( [
                    'type' => 'select',
                    'input_class' => 'select2',
                    'input_name' => "cfs[rules][user_roles]",
                    'options' => [ 'multiple' => '1', 'choices' => $user_roles ],
                    'value' => $rules['user_roles']['values'],
                ] );
            ?>
        </td>
    </tr>
    <tr>
        <td class="label cfs-rule-label">
            <label><?php esc_html_e('Posts', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></label>
        </td>
        <td class="cfs-rule-operator">
            <?php
                atshift_fields_maintenance_for_custom_field_suite()->create_field( [
                    'type' => 'select',
                    'input_name' => "cfs[rules][operator][post_ids]",
                    'options' => [
                        'choices' => [
                            '==' => $equals_text,
                            '!=' => $not_equals_text,
                        ],
                        'force_single' => true,
                    ],
                    'value' => $rules['post_ids']['operator'],
                ] );
            ?>
        </td>
        <td class="cfs-rule-value">
            <select name="cfs[rules][post_ids][]" class="select2-ajax" multiple="multiple" style="width:99.95%">
                <?php foreach ( $json_posts as $json_post ) : ?>
                    <option value="<?php echo esc_attr( absint( $json_post['id'] ) ); ?>" selected="selected"><?php echo esc_html( $json_post['text'] ); ?></option>
                <?php endforeach; ?>
            </select>
        </td>
    </tr>
    <tr>
        <td class="label cfs-rule-label">
            <label><?php esc_html_e( 'Taxonomy Terms', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></label>
        </td>
        <td class="cfs-rule-operator">
            <?php
                atshift_fields_maintenance_for_custom_field_suite()->create_field( [
                    'type' => 'select',
                    'input_name' => "cfs[rules][operator][term_ids]",
                    'options' => [
                        'choices' => [
                            '==' => $equals_text,
                            '!=' => $not_equals_text,
                        ],
                        'force_single' => true,
                    ],
                    'value' => $rules['term_ids']['operator'],
                ] );
            ?>
        </td>
        <td class="cfs-rule-value">
            <?php
                atshift_fields_maintenance_for_custom_field_suite()->create_field( [
                    'type' => 'select',
                    'input_class' => 'select2',
                    'input_name' => "cfs[rules][term_ids]",
                    'options' => [ 'multiple' => '1', 'choices' => $term_ids ],
                    'value' => $rules['term_ids']['values'],
                ] );
            ?>
        </td>
    </tr>
    <tr>
        <td class="label cfs-rule-label">
            <label><?php esc_html_e( 'Page Templates', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></label>
        </td>
        <td class="cfs-rule-operator">
            <?php
                atshift_fields_maintenance_for_custom_field_suite()->create_field( [
                    'type' => 'select',
                    'input_name' => "cfs[rules][operator][page_templates]",
                    'options' => [
                        'choices' => [
                            '==' => $equals_text,
                            '!=' => $not_equals_text,
                        ],
                        'force_single' => true,
                    ],
                    'value' => $rules['page_templates']['operator'],
                ] );
            ?>
        </td>
        <td class="cfs-rule-value">
            <?php
                atshift_fields_maintenance_for_custom_field_suite()->create_field( [
                    'type' => 'select',
                    'input_class' => 'select2',
                    'input_name' => "cfs[rules][page_templates]",
                    'options' => [ 'multiple' => '1', 'choices' => $page_templates ],
                    'value' => $rules['page_templates']['values'],
                ] );
            ?>
        </td>
    </tr>
</table>
