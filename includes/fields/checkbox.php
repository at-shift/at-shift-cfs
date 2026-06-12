<?php

class cfs_checkbox extends cfs_field
{

    function __construct() {
        $this->name = 'checkbox';
        $this->label = __( 'Checkbox', 'at-shift-cfs' );
    }


    function html( $field ) {
        $choices = isset( $field->options['choices'] ) && is_array( $field->options['choices'] ) ? $field->options['choices'] : [];
        $values = array_map( 'strval', (array) $field->value );

        if ( '[]' != substr( $field->input_name, -2 ) ) {
            $field->input_name .= '[]';
        }
    ?>
        <input type="hidden" name="<?php echo esc_attr( $field->input_name ); ?>" value="" />
        <div class="cfs_choice_list cfs_checkbox_choices">
        <?php foreach ( $choices as $val => $label ) : ?>
            <?php $val = ( '{empty}' == $val ) ? '' : (string) $val; ?>
            <?php $checked = in_array( $val, $values, true ) ? ' checked' : ''; ?>
            <label class="cfs_choice">
                <input type="checkbox" name="<?php echo esc_attr( $field->input_name ); ?>" class="<?php echo esc_attr( $field->input_class ); ?>" value="<?php echo esc_attr( $val ); ?>"<?php echo $checked; ?> />
                <span><?php echo esc_html( $label ); ?></span>
            </label>
        <?php endforeach; ?>
        </div>
    <?php
    }


    function options_html( $key, $field ) {

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
                <label><?php _e( 'Choices', 'at-shift-cfs' ); ?></label>
                <p class="description"><?php _e( 'Enter one choice per line', 'at-shift-cfs' ); ?></p>
            </td>
            <td>
                <?php
                    CFS()->create_field( [
                        'type' => 'textarea',
                        'input_name' => 'cfs[fields][' . absint( $key ) . '][options][choices]',
                        'value' => $choices,
                    ] );
                ?>
            </td>
        </tr>
        <tr class="field_option field_option_<?php echo esc_attr( $this->name ); ?>">
            <td class="label">
                <label><?php _e( 'Validation', 'at-shift-cfs' ); ?></label>
            </td>
            <td>
                <?php
                    CFS()->create_field( [
                        'type' => 'true_false',
                        'input_name' => 'cfs[fields][' . absint( $key ) . '][options][required]',
                        'input_class' => 'true_false',
                        'value' => $this->get_option( $field, 'required' ),
                        'options' => [ 'message' => __( 'This is a required field', 'at-shift-cfs' ) ],
                    ] );
                ?>
            </td>
        </tr>
    <?php
    }


    function prepare_value( $value, $field = null ) {
        return array_values( array_filter( (array) $value, 'strlen' ) );
    }


    function format_value_for_api( $value, $field = null ) {
        return $value;
    }


    function pre_save( $value, $field = null ) {
        return array_values( array_filter( (array) $value, 'strlen' ) );
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
