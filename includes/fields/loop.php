<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


class Atshift_CFS_loop extends Atshift_CFS_field
{
    public $values;

    function __construct() {
        $this->name = 'loop';
        $this->label = __( 'Loop', 'atshift-fields-maintenance-for-custom-field-suite' );
        $this->values = [];
    }


    /*
    ================================================================
        html
    ================================================================
    */
    function html( $field ) {
        global $post;

        if ( atshift_fields_maintenance_for_custom_field_suite()->form->has_submission_errors() ) {
            $this->values = atshift_fields_maintenance_for_custom_field_suite()->form->get_submitted_values();
        }
        else {
            $this->values = atshift_fields_maintenance_for_custom_field_suite()->api->get_fields( $post->ID, [ 'format' => 'input' ] );
        }
        $this->recursive_clone( $field->group_id, $field->id );
        $this->recursive_html( $field->group_id, $field->id );
    }


    /*
    ================================================================
        options_html
    ================================================================
    */
    function options_html( $key, $field ) {
    ?>
        <tr class="field_option field_option_<?php echo esc_attr( $this->name ); ?>">
            <td class="label">
                <label><?php esc_html_e( 'Row Display', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></label>
            </td>
            <td>
                <?php
                    atshift_fields_maintenance_for_custom_field_suite()->create_field( [
                        'type' => 'true_false',
                        'input_name' => "cfs[fields][$key][options][row_display]",
                        'input_class' => 'true_false',
                        'value' => $this->get_option( $field, 'row_display' ),
                        'options' => [ 'message' => __( 'Show the values by default', 'atshift-fields-maintenance-for-custom-field-suite' ) ],
                    ] );
                ?>
            </td>
        </tr>
        <tr class="field_option field_option_<?php echo esc_attr( $this->name ); ?>">
            <td class="label">
                <label><?php esc_html_e( 'Row Label', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></label>
            </td>
            <td>
                <?php
                    atshift_fields_maintenance_for_custom_field_suite()->create_field( [
                        'type' => 'text',
                        'input_name' => "cfs[fields][$key][options][row_label]",
                        'value' => $this->get_option( $field, 'row_label', __( 'Loop Row', 'atshift-fields-maintenance-for-custom-field-suite' ) ),
                    ] );
                ?>
            </td>
        </tr>
        <tr class="field_option field_option_<?php echo esc_attr( $this->name ); ?>">
            <td class="label">
                <label><?php esc_html_e( 'Button Label', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></label>
            </td>
            <td>
                <?php
                    atshift_fields_maintenance_for_custom_field_suite()->create_field( [
                        'type' => 'text',
                        'input_name' => "cfs[fields][$key][options][button_label]",
                        'value' => $this->get_option( $field, 'button_label', __( 'Add Row', 'atshift-fields-maintenance-for-custom-field-suite' ) ),
                    ] );
                ?>
            </td>
        </tr>

        <tr class="field_option field_option_<?php echo esc_attr( $this->name ); ?>">
            <td class="label">
                <label><?php esc_html_e( 'Limits', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></label>
                <div class="cfs_tooltip">?
                    <div class="tooltip_inner"><?php esc_html_e( 'Minimum sets the fewest rows that can be added. Maximum sets the most rows that can be added.', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></div>
                </div>
            </td>
            <td>
                <input type="text" name="cfs[fields][<?php echo $this->admin_key_attr( $key ); ?>][options][limit_min]" value="<?php echo esc_attr( $this->get_option( $field, 'limit_min' ) ); ?>" placeholder="<?php esc_attr_e( 'Minimum', 'atshift-fields-maintenance-for-custom-field-suite' ); ?>" style="width:80px" />
                <input type="text" name="cfs[fields][<?php echo $this->admin_key_attr( $key ); ?>][options][limit_max]" value="<?php echo esc_attr( $this->get_option( $field, 'limit_max' ) ); ?>" placeholder="<?php esc_attr_e( 'Maximum', 'atshift-fields-maintenance-for-custom-field-suite' ); ?>" style="width:80px" />
            </td>
        </tr>

    <?php
    }


    /*
    ================================================================
        recursive_clone
    ================================================================
    */
    function recursive_clone( $group_id, $field_id ) {
        $loop_field_ids = [];

        // Get loop field
        $loop_field = atshift_fields_maintenance_for_custom_field_suite()->api->get_input_fields( [
            'field_id' => $field_id
        ] );

        // Get sub-fields
        $results = atshift_fields_maintenance_for_custom_field_suite()->api->get_input_fields( [
            'group_id' => $group_id,
            'parent_id' => $field_id
        ] );

        $row_label = $this->dynamic_label(
            $this->get_option( $loop_field[ $field_id ], 'row_label', __( 'Loop Row', 'atshift-fields-maintenance-for-custom-field-suite' ) )
        );

        $buffer = atshift_cfs_capture_output( function() use ( $row_label, $results, &$loop_field_ids ) {
    ?>
        <div class="loop_wrapper">
            <div class="cfs_loop_head open">
                <a class="cfs_delete_field" href="javascript:;"></a>
                <a class="cfs_toggle_field" href="javascript:;"></a>
                <a class="cfs_insert_field" href="javascript:;"></a>
                <span class="label"><?php echo esc_attr( $row_label ); ?></span>
            </div>
            <div class="cfs_loop_body open">
            <?php
                $this->render_loop_fields( $results, function( $field ) use ( &$loop_field_ids ) {
                    $this->render_clone_field( $field, $loop_field_ids );
                } );
            ?>
            </div>
        </div>
    <?php
        } );

        wp_add_inline_script( 'atshift-cfs-validation', 'CFS.loop_buffer[' . absint( $field_id ) . '] = ' . wp_json_encode( $buffer ) . ';' );

        foreach ( $loop_field_ids as $loop_field_id ) {
            $this->recursive_clone( $group_id, $loop_field_id );
        }
    }


    /*
    ================================================================
        recursive_html
    ================================================================
    */
    function recursive_html( $group_id, $field_id, $parent_tag = '', $parent_weight = 0 ) {

        // Get loop field
        $loop_field = atshift_fields_maintenance_for_custom_field_suite()->api->get_input_fields( [
            'field_id' => $field_id
        ] );

        // Get sub-fields
        $results = atshift_fields_maintenance_for_custom_field_suite()->api->get_input_fields( [
            'group_id' => $group_id,
            'parent_id' => $field_id
        ] );

        $parent_tag = empty( $parent_tag ) ? "[$field_id]" : $parent_tag;
        $values = $this->get_values_by_tag( $parent_tag );

        // Row options
        $row_display = $this->get_option( $loop_field[ $field_id ], 'row_display', 0 );
        $row_label = $this->get_option( $loop_field[ $field_id ], 'row_label', __( 'Loop Row', 'atshift-fields-maintenance-for-custom-field-suite' ) );
        $button_label = $this->get_option( $loop_field[ $field_id ], 'button_label', __( 'Add Row', 'atshift-fields-maintenance-for-custom-field-suite' ) );
        $css_class = ( 0 < (int) $row_display ) ? ' open' : '';
        $label_fields = $this->get_label_fields( $group_id, $results );

        // Do the dirty work
        $row_offset = -1;

        if ( $values ) :
            foreach ( $values as $i => $value ) :
                $row_offset = max( $i, $row_offset );
    ?>
        <div class="loop_wrapper">
            <div class="cfs_loop_head<?php echo esc_attr( $css_class ); ?>">
                <a class="cfs_delete_field" href="javascript:;"></a>
                <a class="cfs_toggle_field" href="javascript:;"></a>
                <a class="cfs_insert_field" href="javascript:;"></a>
                <span class="label"><?php echo esc_attr( $this->dynamic_label( $row_label, $label_fields, $values[ $i ] ) ); ?>&nbsp;</span>
            </div>
            <div class="cfs_loop_body<?php echo esc_attr( $css_class ); ?>">
            <?php
                $this->render_loop_fields( $results, function( $field ) use ( $group_id, $parent_tag, $i, $values ) {
                    $this->render_value_field( $field, $group_id, $parent_tag, $i, $values );
                } );
            ?>
            </div>
        </div>

        <?php endforeach; endif; ?>

        <div class="table_footer">
            <input type="button" class="button-primary cfs_add_field" value="<?php echo esc_attr( $button_label ); ?>" data-loop-tag="<?php echo esc_attr( $parent_tag ); ?>" data-rows="<?php echo absint( $row_offset + 1 ); ?>" />
        </div>
    <?php
	    }


    private function render_loop_fields( $fields, $render_field ) {
        $fields = array_values( $fields );
        $tabs = [];
        $first_tab_index = false;

        foreach ( $fields as $index => $field ) {
            if ( 'tab' == $field->type ) {
                $tabs[] = $field;
                if ( false === $first_tab_index ) {
                    $first_tab_index = $index;
                }
            }
        }

        if ( 2 > count( $tabs ) ) {
            foreach ( $fields as $field ) {
                if ( 'tab' != $field->type && ! Atshift_CFS_field::should_hide_input_field( $field ) ) {
                    $render_field( $field );
                }
            }
            return;
        }

        foreach ( $fields as $index => $field ) {
            if ( $index >= $first_tab_index ) {
                break;
            }

            if ( 'tab' != $field->type && ! Atshift_CFS_field::should_hide_input_field( $field ) ) {
                $render_field( $field );
            }
        }

        echo '<div class="cfs-tabbed-fields">';
        echo '<div class="cfs-tabs">';
        foreach ( $tabs as $tab ) {
            $tab_key = 'field-' . $tab->id;
            echo '<div class="cfs-tab" rel="' . esc_attr( $tab_key ) . '" data-tab-key="' . esc_attr( $tab_key ) . '">' . esc_html( $tab->label ) . '</div>';
        }
        echo '</div>';

        $content_open = false;
        foreach ( $fields as $index => $field ) {
            if ( $index < $first_tab_index ) {
                continue;
            }

            if ( 'tab' == $field->type ) {
                if ( $content_open ) {
                    echo '</div>';
                }

                $tab_key = 'field-' . $field->id;
                echo '<div class="cfs-tab-content cfs-tab-content-' . esc_attr( $tab_key ) . '" data-tab-key="' . esc_attr( $tab_key ) . '">';

                if ( ! empty( $field->notes ) ) {
                    echo '<div class="cfs-tab-notes">' . esc_html( $field->notes ) . '</div>';
                }

                $content_open = true;
            }
            else {
                if ( ! Atshift_CFS_field::should_hide_input_field( $field ) ) {
                    $render_field( $field );
                }
            }
        }

        if ( $content_open ) {
            echo '</div>';
        }

        echo '</div>';
    }


    private function render_clone_field( $field, &$loop_field_ids ) {
    ?>
        <div class="field field-<?php echo esc_attr( $field->name ); ?>" data-type="<?php echo esc_attr( $field->type ); ?>" data-name="<?php echo esc_attr( $field->name ); ?>">
        <?php if ( 'accordion' !== $field->type && ! empty( $field->label ) ) : ?>
            <label><?php echo esc_html( $field->label ); ?><?php echo Atshift_CFS_field::is_required_field( $field ) ? wp_kses_post( Atshift_CFS_field::required_badge() ) : ''; ?></label>
        <?php endif; ?>

        <?php if ( 'accordion' !== $field->type && ! empty( $field->notes ) ) : ?>
            <p class="notes"><?php echo esc_html( $field->notes ); ?></p>
        <?php endif; ?>

            <div class="cfs_<?php echo esc_attr( $field->type ); ?>">
        <?php
        if ( 'loop' == $field->type ) :
            $loop_field_ids[] = $field->id;
        ?>
            <div class="table_footer">
                <input type="button" class="button-primary cfs_add_field" value="<?php echo esc_attr( $this->get_option( $field, 'button_label', __( 'Add Row', 'atshift-fields-maintenance-for-custom-field-suite' ) ) ); ?>" data-loop-tag="[clone][<?php echo absint( $field->id ); ?>]" data-rows="0" />
            </div>
        <?php elseif ( in_array( $field->type, [ 'group', 'accordion', 'conditional' ], true ) ) : ?>
        <?php
            atshift_fields_maintenance_for_custom_field_suite()->create_field( [
                'id' => $field->id,
                'group_id' => $field->group_id,
                'type' => $field->type,
                'label' => $field->label,
                'input_class' => $field->type,
                'options' => $field->options,
                'input_name_template' => 'cfs[input][clone][%d][value][]',
            ] );
        ?>
        <?php else : ?>
        <?php
            atshift_fields_maintenance_for_custom_field_suite()->create_field( [
                'type' => $field->type,
                'input_name' => "cfs[input][clone][$field->id][value][]",
                'input_class' => $field->type,
                'options' => $field->options,
                'value' => $this->get_option( $field, 'default_value' ),
            ] );
        ?>
        <?php endif; ?>
            </div>
        </div>
    <?php
    }


    private function render_value_field( $field, $group_id, $parent_tag, $row_index, $values ) {
    ?>
        <div class="field field-<?php echo esc_attr( $field->name ); ?>" data-type="<?php echo esc_attr( $field->type ); ?>" data-name="<?php echo esc_attr( $field->name ); ?>">
        <?php if ( 'accordion' !== $field->type && ! empty( $field->label ) ) : ?>
            <label><?php echo esc_html( $field->label ); ?><?php echo Atshift_CFS_field::is_required_field( $field ) ? wp_kses_post( Atshift_CFS_field::required_badge() ) : ''; ?></label>
        <?php endif; ?>

        <?php if ( 'accordion' !== $field->type && ! empty( $field->notes ) ) : ?>
            <p class="notes"><?php echo esc_html( $field->notes ); ?></p>
        <?php endif; ?>

            <div class="cfs_<?php echo esc_attr( $field->type ); ?>">
        <?php if ( 'loop' == $field->type ) : ?>
            <?php $this->recursive_html( $group_id, $field->id, "{$parent_tag}[$row_index][$field->id]", $row_index ); ?>
        <?php elseif ( in_array( $field->type, [ 'group', 'accordion', 'conditional' ], true ) ) : ?>
        <?php
            atshift_fields_maintenance_for_custom_field_suite()->create_field( [
                'id' => $field->id,
                'group_id' => $field->group_id,
                'type' => $field->type,
                'label' => $field->label,
                'input_class' => $field->type,
                'options' => $field->options,
                'values' => isset( $values[ $row_index ] ) && is_array( $values[ $row_index ] ) ? $values[ $row_index ] : [],
                'input_name_template' => "cfs[input]{$parent_tag}[$row_index][%d][value][]",
            ] );
        ?>
        <?php else : ?>
        <?php
            $args = [
                'type' => $field->type,
                'input_name' => "cfs[input]{$parent_tag}[$row_index][$field->id][value][]",
                'input_class' => $field->type,
                'options' => $field->options,
            ];

            if ( isset( $values[ $row_index ][ $field->id ] ) ) {
                $args['value'] = $values[ $row_index ][ $field->id ];
            }
            elseif ( isset( $field->options['default_value'] ) ) {
                $args['value'] = $field->options['default_value'];
            }

            atshift_fields_maintenance_for_custom_field_suite()->create_field( $args );
        ?>
        <?php endif; ?>
            </div>
        </div>
    <?php
    }
	    private function get_values_by_tag( $parent_tag ) {
        preg_match_all( '/\[([0-9]+)\]/', $parent_tag, $matches );

        $values = $this->values;
        foreach ( $matches[1] as $key ) {
            if ( ! isset( $values[ $key ] ) ) {
                return false;
            }
            $values = $values[ $key ];
        }

        return $values;
    }


    private function get_label_fields( $group_id, $fields ) {
        $label_fields = [];

        foreach ( $fields as $field ) {
            $label_fields[] = $field;

            if ( ! in_array( $field->type, [ 'group', 'accordion' ], true ) ) {
                continue;
            }

            $children = atshift_fields_maintenance_for_custom_field_suite()->api->get_input_fields( [
                'group_id' => $group_id,
                'parent_id' => $field->id,
            ] );

            foreach ( $children as $child ) {
                $label_fields[] = $child;
            }
        }

        return $label_fields;
    }


    /*---------------------------------------------------------------------------------------------
        input_head
    ---------------------------------------------------------------------------------------------*/

    function input_head( $field = null ) {
    ?>
        <?php wp_add_inline_script( 'atshift-cfs-validation', atshift_cfs_capture_output( function() { ?>
        (function($) {
            $(function() {
                var remove_loop_row_message = <?php echo wp_json_encode( __( 'Remove this row?', 'atshift-fields-maintenance-for-custom-field-suite' ) ); ?>;

                $(document).on('click', '.cfs_add_field', function() {
                    var num_rows = $(this).attr('data-rows');
                    var loop_tag = $(this).attr('data-loop-tag');
                    var loop_id = loop_tag.match(/.*\[(.*?)\]/)[1];
                    var html = CFS.loop_buffer[loop_id].replace(/\[clone\]/g, loop_tag + '[' + num_rows + ']');
                    $(this).attr('data-rows', parseInt(num_rows)+1);
                    $(html).insertBefore( $(this).closest('.table_footer') ).addClass('loop_wrapper_new');
                    $(this).trigger('cfs/ready');
                });

                $(document).on('click', '.cfs_insert_field', function(event) {
                    event.stopPropagation();
                    var $add_field = $('.cfs_add_field');
                    var num_rows = $add_field.attr('data-rows');
                    var loop_tag = $add_field.attr('data-loop-tag');
                    var loop_id = loop_tag.match(/.*\[(.*?)\]/)[1];
                    var html = CFS.loop_buffer[loop_id].replace(/\[clone\]/g, loop_tag + '[' + num_rows + ']');
                    $add_field.attr('data-rows', parseInt(num_rows)+1);
                    $(html).insertAfter( $(this).closest('.loop_wrapper') ).addClass('loop_wrapper_new');
                    $add_field.trigger('cfs/ready');
                });

                $(document).on('click', '.cfs_delete_field', function(event) {
                    if (confirm(remove_loop_row_message)) {
                        $(this).closest('.loop_wrapper').remove();
                    }
                    event.stopPropagation();
                });

                $(document).on('click', '.cfs_loop_head', function() {
                    $(this).toggleClass('open');
                    $(this).siblings('.cfs_loop_body').toggleClass('open');
                });

                // Hide or show all rows
                // The HTML is located in includes/form.php
                $(document).on('click', '.cfs_loop_toggle', function() {
                    $(this).closest('.field').find('.cfs_loop_head').toggleClass('open');
                    $(this).closest('.field').find('.cfs_loop_body').toggleClass('open');
                });

                $('.cfs_loop').sortable({
                    axis: 'y',
                    containment: 'parent',
                    items: '.loop_wrapper',
                    handle: '.cfs_loop_head',
                    update: function(event, ui) {

                        // To re-order field names:
                        // 1. Get the depth of the dragged element
                        // 2. Loop through each input field within the dragged element
                        // 3. Reset the array index within the name attribute
                        var $container = ui.item.closest('.field');
                        var depth = $container.closest('.cfs_loop').parents('.cfs_loop').length;
                        var array_element = 3 + (depth * 2);

                        var counter = -1;
                        var last_index = -1;
                        $container.find('[name^="cfs[input]"]').each(function() {
                            var name_attr = $(this).attr('name').split('[');
                            var current_index = parseInt( name_attr[array_element] );
                            if (current_index != last_index) {
                                counter += 1;
                            }
                            name_attr[array_element] = counter + ']';
                            last_index = current_index;
                            $(this).attr('name', name_attr.join('['));
                        });
                    }
                });
            });
        })(jQuery);
        <?php } ) ); ?>
    <?php
    }


    /*
    ================================================================
        dynamic_label
    ================================================================
    */
    function dynamic_label( $row_label, $fields = [], $values = [] ) {

        // Exit stage left
        if ( '{' != substr( $row_label, 0, 1 ) || '}' != substr( $row_label, -1 ) ) {
            return $row_label;
        }

        $field = false;
        $fallback = false;
        $field_name = substr( $row_label, 1, -1 );

        // Check for fallback value
        if ( false !== strpos( $field_name, ':' ) ) {
            list( $field_name, $fallback ) = explode( ':', $field_name );
        }

        // Get all field names and IDs
        foreach ( $fields as $f ) {
            if ( $field_name == $f->name ) {
                $field = $f;
                break;
            }
        }

        if ( ! empty( $field ) && isset( $values[ $field->id ] ) ) {
            if ( 'select' == $field->type ) {
                $select_key = reset( $values[ $field->id ] );
                $row_label = $field->options['choices'][ $select_key ];
            }
            else {
                $row_label = $values[ $field->id ];
            }
        }
        elseif ( false !== $fallback ) {
             $row_label = $fallback;
        }

        return $row_label;
    }


    /*
    ================================================================
        prepare_value
    ================================================================
    */
    function prepare_value( $value, $field = null ) {
        return $value;
    }
}
