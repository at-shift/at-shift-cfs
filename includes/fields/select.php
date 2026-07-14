<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


class Atshift_CFS_select extends Atshift_CFS_field
{
    public $select2_inserted;

    function __construct() {
        $this->name = 'select';
        $this->label = __( 'Select', 'atshift-fields-maintenance-for-custom-field-suite' );
        $this->select2_inserted = false;
    }

    function html( $field ) {
        $multiple = '';
        $field->input_class = empty( $field->input_class ) ? '' : $field->input_class;

        // Multi-select
        if ( isset( $field->options['multiple'] ) && '1' == $field->options['multiple'] ) {
            $multiple = ' multiple';
            $field->input_class .= ' multiple';
        }

        // Select2
        if ( isset( $field->options['select2'] ) && '1' == $field->options['select2'] ) {
            $field->input_class .= ' select2';

            add_action( 'admin_footer', [ $this, 'select2_code' ] );
        }

        // Select boxes should return arrays (unless "force_single" is true)
        if ( '[]' != substr( $field->input_name, -2 ) && empty( $field->options['force_single'] ) ) {
            $field->input_name .= '[]';
        }

        $choices = isset( $field->options['choices'] ) && is_array( $field->options['choices'] ) ? $field->options['choices'] : [];
        $selected_values = array_map( 'strval', (array) $field->value );
        $has_empty_choice = isset( $choices[''] ) || isset( $choices['{empty}'] );
        $show_placeholder = '' === $multiple && empty( $field->options['force_single'] ) && ! $has_empty_choice;
    ?>
        <?php $this->input_suffix_open( $field ); ?>
        <select name="<?php echo esc_attr( $field->input_name ); ?>" class="<?php echo esc_attr( $field->input_class ); ?>"<?php if ( $multiple ) : ?> multiple="multiple"<?php endif; ?><?php if ( ! empty( $field->options['placeholder'] ) ) : ?> data-placeholder="<?php echo esc_attr( $field->options['placeholder'] ); ?>"<?php endif; ?>>
        <?php if ( $show_placeholder ) : ?>
            <option value=""<?php selected( in_array( '', $selected_values, true ) ); ?>><?php esc_html_e( 'Please select...', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></option>
        <?php endif; ?>
        <?php foreach ( $choices as $val => $label ) : ?>
            <?php $val = ( '{empty}' == $val ) ? '' : $val; ?>
            <option value="<?php echo esc_attr( $val ); ?>"<?php selected( in_array( (string) $val, $selected_values, true ) ); ?>><?php echo esc_html( $label ); ?></option>
        <?php endforeach; ?>
        </select>
        <?php $this->input_suffix_close( $field ); ?>
    <?php
    }

    function select2_code() {

        // Exit early if the select2 code has already been inserted
        if ( $this->select2_inserted ) {
            return;
        }

        wp_enqueue_script( 'atshift-cfs-select2', esc_url( ATSHIFT_CFS_URL . '/assets/js/select2/select2.min.js' ), [ 'jquery' ], ATSHIFT_CFS_VERSION, true );
        wp_enqueue_style( 'atshift-cfs-select2', esc_url( ATSHIFT_CFS_URL . '/assets/js/select2/select2.css' ), [], ATSHIFT_CFS_VERSION );

        // Don't insert select2 code twice
        $this->select2_inserted = true;
    }

    function input_head( $field = null ) {
    ?>
        <?php wp_add_inline_script( 'atshift-cfs-select2', atshift_cfs_capture_output( function() { ?>
        (function($) {
            $(function() {
                $(document).on('cfs/ready', '.cfs_add_field', function() {
                    $('.cfs_select:not(.ready)').init_select();
                });
                $('.cfs_select').init_select();
            });

            $.fn.init_select = function() {
                this.each(function() {
                    var $this = $(this);
                    $this.addClass('ready');

                    if ( $this.find( 'select' ).hasClass( 'select2' ) ) {
                        $this.find( 'select' ).select2();
                    }
                });
            }
        })(jQuery);
        <?php } ) ); ?>
    <?php
    }


    function options_html( $key, $field ) {

        // Convert choices to textarea-friendly format
        $choices = $this->get_option( $field, 'choices' );
        if ( isset( $field->options['choices'] ) && is_array( $field->options['choices'] ) ) {
            foreach ( $choices as $choice_key => $choice_val ) {
                $choices[ $choice_key ] = "$choice_key : $choice_val";
            }

            $choices = implode( "\n", $choices );
        }
        else {
            $choices = '';
        }
    ?>
        <tr class="field_option field_option_<?php echo esc_attr( $this->name ); ?>">
            <td class="label">
                <label><?php esc_html_e( 'Choices', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></label>
                <p class="description"><?php esc_html_e( 'Enter one choice per line', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></p>
            </td>
            <td>
                <?php
                    atshift_fields_maintenance_for_custom_field_suite()->create_field( [
                        'type' => 'textarea',
                        'input_name' => 'cfs[fields]['  . $this->normalize_admin_key( $key ) . '][options][choices]',
                        'value' => $choices,
                    ] );
                ?>
            </td>
        </tr>
        <tr class="field_option field_option_<?php echo esc_attr( $this->name ); ?>">
            <td class="label">
                <label><?php esc_html_e( 'Multi-select?', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></label>
            </td>
            <td>
                <?php
                    atshift_fields_maintenance_for_custom_field_suite()->create_field( [
                        'type' => 'true_false',
                        'input_name' => 'cfs[fields]['  . $this->normalize_admin_key( $key ) . '][options][multiple]',
                        'input_class' => 'true_false',
                        'value' => $this->get_option( $field, 'multiple' ),
                        'options' => [ 'message' => __( 'This is a multi-select field', 'atshift-fields-maintenance-for-custom-field-suite' ) ],
                     ] );
                ?>
            </td>
        </tr>
        <tr class="field_option field_option_<?php echo esc_attr( $this->name ); ?>">
            <td class="label">
                <label><?php esc_html_e( 'Select2', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></label>
                <div class="cfs_tooltip">?
                    <div class="tooltip_inner"><?php esc_html_e( 'Displays the select field with Select2. This makes long choice lists easier to search and select on edit screens.', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></div>
                </div>
            </td>
            <td>
                <?php
                    atshift_fields_maintenance_for_custom_field_suite()->create_field( [
                        'type' => 'true_false',
                        'input_name' => 'cfs[fields]['  . $this->normalize_admin_key( $key ) . '][options][select2]',
                        'input_class' => 'true_false',
                        'value' => $this->get_option( $field, 'select2' ),
                        'options' => [ 'message' => __( 'Render this field with Select2', 'atshift-fields-maintenance-for-custom-field-suite' ) ],
                    ] );
                ?>
            </td>
        </tr>
        <?php $this->input_suffix_option_html( $key, $field ); ?>
        <tr class="field_option field_option_<?php echo esc_attr( $this->name ); ?>">
            <td class="label">
                <label><?php esc_html_e( 'Validation', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></label>
            </td>
            <td>
                <?php
                    atshift_fields_maintenance_for_custom_field_suite()->create_field( [
                        'type' => 'true_false',
                        'input_name' => 'cfs[fields]['  . $this->normalize_admin_key( $key ) . '][options][required]',
                        'input_class' => 'true_false',
                        'value' => $this->get_option( $field, 'required' ),
                        'options' => [ 'message' => __( 'This is a required field', 'atshift-fields-maintenance-for-custom-field-suite' ) ],
                    ] );
                ?>
            </td>
        </tr>
    <?php
    }


    function format_value_for_api( $value, $field = null ) {
        $value_array = [];
        $choices = $field->options['choices'];

        // Return an associative array (value, label)
        if ( is_array( $value ) ) {
            foreach ( $value as $val ) {
                $value_array[ $val ] = isset( $choices[ $val ] ) ? $choices[ $val ] : $val;
            }
        }

        return $value_array;
    }


    function prepare_value( $value, $field = null ) {
        return $value;
    }


    function pre_save_field( $field ) {
        $new_choices = [];
        $choices = isset( $field['options']['choices'] ) ? $field['options']['choices'] : '';

        if ( ! empty( $choices ) ) {
            if ( ! is_array( $choices ) ) {
                $choices = trim( $choices );
                $choices = str_replace( "\r\n", "\n", $choices );
                $choices = str_replace( "\r", "\n", $choices );
                $choices = ( false !== strpos( $choices, "\n" ) ) ? explode( "\n", $choices ) : (array) $choices;
            }

            foreach ( $choices as $key => $choice ) {
                if ( is_string( $key ) ) {
                    $new_choices[ $key ] = $choice;
                    continue;
                }

                $choice = trim( $choice );
                if ( false !== ( $pos = strpos( $choice, ' : ' ) ) ) {
                    $array_key = substr( $choice, 0, $pos );
                    $array_value = substr( $choice, $pos + 3 );
                    $new_choices[ $array_key ] = $array_value;
                }
                else {
                    $new_choices[ $choice ] = $choice;
                }
            }
        }

        $field['options']['choices'] = $new_choices;

        return $field;
    }
}
