<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


class cfs_conditional extends cfs_field
{
    function __construct() {
        $this->name = 'conditional';
        $this->label = __( 'Conditional Group', 'at-shift-cfs' );
    }


    function html( $field ) {
        global $post;

        $children = CFS()->api->get_input_fields( [
            'group_id' => $field->group_id,
            'parent_id' => $field->id,
        ] );
        $has_values = property_exists( $field, 'values' ) && is_array( $field->values );
        $values = $has_values ? $field->values : [];

        if ( ! $has_values && ! empty( $post->ID ) ) {
            $values = CFS()->api->get_fields( $post->ID, [ 'format' => 'input' ] );
        }

        $choices = $this->get_choices( $field );
        $display_type = 'select' === $this->get_option( $field, 'display_type', 'radio' ) ? 'select' : 'radio';
        $selected = is_scalar( $field->value ) ? (string) $field->value : '';

        if ( 'radio' === $display_type && ! isset( $choices[ $selected ] ) ) {
            $default_value = (string) $this->get_option( $field, 'default_value' );
            $selected = isset( $choices[ $default_value ] ) ? $default_value : (string) key( $choices );
        }

        $input_name_template = isset( $field->input_name_template ) ? (string) $field->input_name_template : 'cfs[input][%d][value]';
        ?>
        <div class="cfs-conditional" data-selected-value="<?php echo esc_attr( $selected ); ?>">
            <div class="cfs-conditional-control cfs-conditional-control-<?php echo esc_attr( $display_type ); ?>">
                <?php if ( 'select' === $display_type ) : ?>
                    <select name="<?php echo esc_attr( $field->input_name ); ?>" class="cfs-conditional-selector">
                        <option value=""><?php esc_html_e( 'Please select...', 'at-shift-cfs' ); ?></option>
                        <?php foreach ( $choices as $value => $label ) : ?>
                        <option value="<?php echo esc_attr( $value ); ?>"<?php selected( $selected, (string) $value ); ?>><?php echo esc_html( $label ); ?></option>
                        <?php endforeach; ?>
                    </select>
                <?php else : ?>
                    <input type="hidden" name="<?php echo esc_attr( $field->input_name ); ?>" value="" />
                    <div class="cfs_choice_list cfs_radio_choices">
                        <?php foreach ( $choices as $value => $label ) : ?>
                        <label class="cfs_choice">
                            <input type="radio" name="<?php echo esc_attr( $field->input_name ); ?>" class="cfs-conditional-selector" value="<?php echo esc_attr( $value ); ?>"<?php checked( $selected, (string) $value ); ?> />
                            <span><?php echo esc_html( $label ); ?></span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="cfs-conditional-fields">
                <?php foreach ( $children as $child ) : ?>
                    <?php
                    if ( ! isset( CFS()->fields[ $child->type ] ) ) {
                        continue;
                    }

                    $conditional_value = isset( $child->options['conditional_value'] ) ? (string) $child->options['conditional_value'] : '';
                    $is_visible = '' !== $selected && $conditional_value === $selected;
                    $args = [
                        'id' => $child->id,
                        'group_id' => $child->group_id,
                        'type' => $child->type,
                        'label' => $child->label,
                        'input_name' => sprintf( $input_name_template, $child->id ),
                        'input_class' => $child->type,
                        'input_name_template' => $input_name_template,
                        'options' => $child->options,
                        'value' => isset( $values[ $child->id ] ) ? $values[ $child->id ] : $this->get_option( $child, 'default_value' ),
                        'values' => $values,
                        'notes' => $child->notes,
                    ];
                    ?>
                    <div class="cfs-conditional-branch" data-conditional-value="<?php echo esc_attr( $conditional_value ); ?>"<?php echo $is_visible ? '' : ' hidden'; ?>>
                        <div class="field field-<?php echo esc_attr( $child->name ); ?>" data-type="<?php echo esc_attr( $child->type ); ?>" data-name="<?php echo esc_attr( $child->name ); ?>">
                            <?php if ( ! in_array( $child->type, [ 'accordion', 'tab' ], true ) && ! empty( $child->label ) ) : ?>
                            <label><?php echo esc_html( $child->label ); ?><?php echo cfs_field::is_required_field( $child ) ? wp_kses_post( cfs_field::required_badge() ) : ''; ?></label>
                            <?php endif; ?>

                            <?php if ( 'accordion' !== $child->type && ! empty( $child->notes ) ) : ?>
                            <p class="notes"><?php echo esc_html( $child->notes ); ?></p>
                            <?php endif; ?>

                            <div class="cfs_<?php echo esc_attr( $child->type ); ?>">
                                <?php CFS()->create_field( $args ); ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }


    function options_html( $key, $field ) {
        $choices = $this->get_choices( $field );
        $choice_lines = [];

        foreach ( $choices as $choice_key => $choice_label ) {
            $choice_lines[] = $choice_key . ' : ' . $choice_label;
        }
        ?>
        <tr class="field_option field_option_conditional">
            <td class="label">
                <label><?php esc_html_e( 'Display Type', 'at-shift-cfs' ); ?></label>
            </td>
            <td>
                <?php CFS()->create_field( [
                    'type' => 'select',
                    'input_name' => 'cfs[fields][' . absint( $key ) . '][options][display_type]',
                    'input_class' => 'cfs-conditional-display-type',
                    'options' => [
                        'choices' => [
                            'radio' => __( 'Radio Button', 'at-shift-cfs' ),
                            'select' => __( 'Dropdown', 'at-shift-cfs' ),
                        ],
                        'force_single' => true,
                    ],
                    'value' => $this->get_option( $field, 'display_type', 'radio' ),
                ] ); ?>
            </td>
        </tr>
        <tr class="field_option field_option_conditional">
            <td class="label">
                <label><?php esc_html_e( 'Choices', 'at-shift-cfs' ); ?></label>
                <p class="description"><?php esc_html_e( 'Enter one choice per line', 'at-shift-cfs' ); ?></p>
            </td>
            <td>
                <?php CFS()->create_field( [
                    'type' => 'textarea',
                    'input_name' => 'cfs[fields][' . absint( $key ) . '][options][choices]',
                    'input_class' => 'cfs-conditional-choices',
                    'value' => implode( "\n", $choice_lines ),
                ] ); ?>
            </td>
        </tr>
        <tr class="field_option field_option_conditional cfs-conditional-default-row">
            <td class="label">
                <label><?php esc_html_e( 'Default Value', 'at-shift-cfs' ); ?></label>
                <p class="description"><?php esc_html_e( 'Used for radio buttons. The first choice is used when left blank.', 'at-shift-cfs' ); ?></p>
            </td>
            <td>
                <input type="text" name="cfs[fields][<?php echo absint( $key ); ?>][options][default_value]" value="<?php echo esc_attr( $this->get_option( $field, 'default_value' ) ); ?>" />
            </td>
        </tr>
        <?php
    }


    function input_head( $field = null ) {
        ?>
        <script>
        (function($) {
            function refreshConditional($conditional) {
                var value = $conditional.find('.cfs-conditional-selector:checked').val();

                if (undefined === value) {
                    value = $conditional.find('select.cfs-conditional-selector').val() || '';
                }

                $conditional.attr('data-selected-value', value);
                $conditional.find('> .cfs-conditional-fields > .cfs-conditional-branch').each(function() {
                    $(this).prop('hidden', '' === value || String($(this).data('conditional-value')) !== String(value));
                });
            }

            $(document).on('change', '.cfs-conditional-selector', function() {
                refreshConditional($(this).closest('.cfs-conditional'));
            });

            $(function() {
                $('.cfs-conditional').each(function() {
                    refreshConditional($(this));
                });
            });
        })(jQuery);
        </script>
        <?php
    }


    function pre_save_field( $field ) {
        $field['options']['display_type'] = isset( $field['options']['display_type'] ) && 'select' === $field['options']['display_type'] ? 'select' : 'radio';
        $field['options']['default_value'] = isset( $field['options']['default_value'] ) ? sanitize_text_field( $field['options']['default_value'] ) : '';
        $field['options']['choices'] = $this->parse_choices( isset( $field['options']['choices'] ) ? $field['options']['choices'] : '' );

        return $field;
    }


    private function get_choices( $field ) {
        return isset( $field->options['choices'] ) && is_array( $field->options['choices'] ) ? $field->options['choices'] : [];
    }


    private function parse_choices( $choices ) {
        $parsed = [];

        if ( ! is_array( $choices ) ) {
            $choices = preg_split( '/\r\n|\r|\n/', trim( (string) $choices ) );
        }

        foreach ( (array) $choices as $key => $choice ) {
            if ( is_string( $key ) ) {
                $parsed[ sanitize_text_field( $key ) ] = sanitize_text_field( $choice );
                continue;
            }

            $choice = trim( (string) $choice );
            if ( '' === $choice ) {
                continue;
            }

            if ( false !== ( $position = strpos( $choice, ' : ' ) ) ) {
                $value = trim( substr( $choice, 0, $position ) );
                $label = trim( substr( $choice, $position + 3 ) );
            }
            else {
                $value = $choice;
                $label = $choice;
            }

            $parsed[ sanitize_text_field( $value ) ] = sanitize_text_field( $label );
        }

        return $parsed;
    }
}
