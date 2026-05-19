<?php

class cfs_radio extends cfs_field
{

    function __construct() {
        $this->name = 'radio';
        $this->label = __( 'Radio Button', 'cfs' );
    }


    function html( $field ) {
        $choices = isset( $field->options['choices'] ) && is_array( $field->options['choices'] ) ? $field->options['choices'] : [];
        $value = (string) $field->value;
    ?>
        <input type="hidden" name="<?php echo esc_attr( $field->input_name ); ?>" value="" />
        <div class="cfs_choice_list cfs_radio_choices">
        <?php foreach ( $choices as $val => $label ) : ?>
            <?php $val = ( '{empty}' == $val ) ? '' : (string) $val; ?>
            <?php $checked = ( $val === $value ) ? ' checked' : ''; ?>
            <label class="cfs_choice">
                <input type="radio" name="<?php echo esc_attr( $field->input_name ); ?>" class="<?php echo esc_attr( $field->input_class ); ?>" value="<?php echo esc_attr( $val ); ?>"<?php echo $checked; ?> />
                <span><?php echo esc_html( $label ); ?></span>
            </label>
        <?php endforeach; ?>
        </div>
    <?php
    }


    function options_html( $key, $field ) {

        if ( isset( $field->options['choices'] ) && is_array( $field->options['choices'] ) ) {
            foreach ( $field->options['choices'] as $choice_key => $choice_val ) {
                $field->options['choices'][ $choice_key ] = "$choice_key : $choice_val";
            }

            $field->options['choices'] = implode( "\n", $field->options['choices'] );
        }
        else {
            $field->options['choices'] = '';
        }
    ?>
        <tr class="field_option field_option_<?php echo esc_attr( $this->name ); ?>">
            <td class="label">
                <label><?php _e( 'Choices', 'cfs' ); ?></label>
                <p class="description"><?php _e( 'Enter one choice per line', 'cfs' ); ?></p>
            </td>
            <td>
                <?php
                    CFS()->create_field( [
                        'type' => 'textarea',
                        'input_name' => 'cfs[fields][' . absint( $key ) . '][options][choices]',
                        'value' => $this->get_option( $field, 'choices' ),
                    ] );
                ?>
            </td>
        </tr>
        <tr class="field_option field_option_<?php echo esc_attr( $this->name ); ?>">
            <td class="label">
                <label><?php _e( 'Validation', 'cfs' ); ?></label>
            </td>
            <td>
                <?php
                    CFS()->create_field( [
                        'type' => 'true_false',
                        'input_name' => 'cfs[fields][' . absint( $key ) . '][options][required]',
                        'input_class' => 'true_false',
                        'value' => $this->get_option( $field, 'required' ),
                        'options' => [ 'message' => __( 'This is a required field', 'cfs' ) ],
                    ] );
                ?>
            </td>
        </tr>
    <?php
    }


    function format_value_for_api( $value, $field = null ) {
        return $value;
    }


    function pre_save_field( $field ) {
        $new_choices = [];
        $choices = trim( $field['options']['choices'] );

        if ( ! empty( $choices ) ) {
            $choices = str_replace( "\r\n", "\n", $choices );
            $choices = str_replace( "\r", "\n", $choices );
            $choices = ( false !== strpos( $choices, "\n" ) ) ? explode( "\n", $choices ) : (array) $choices;

            foreach ( $choices as $choice ) {
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
