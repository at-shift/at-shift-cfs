<?php

class cfs_url extends cfs_field
{

    function __construct() {
        $this->name = 'url';
        $this->label = __( 'URL', 'cfs' );
    }


    function html( $field ) {
    ?>
        <input type="url" name="<?php echo esc_attr( $field->input_name ); ?>" class="<?php echo esc_attr( $field->input_class ); ?>" value="<?php echo esc_url( $field->value ); ?>" placeholder="https://" />
    <?php
    }


    function options_html( $key, $field ) {
    ?>
        <tr class="field_option field_option_<?php echo esc_attr( $this->name ); ?>">
            <td class="label">
                <label><?php _e( 'Default Value', 'cfs' ); ?></label>
            </td>
            <td>
                <?php
                    CFS()->create_field( [
                        'type' => 'text',
                        'input_name' => 'cfs[fields][' . absint( $key ) . '][options][default_value]',
                        'value' => $this->get_option( $field, 'default_value' ),
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


    function pre_save( $value, $field = null ) {
        $value = esc_url_raw( $value );
        $scheme = wp_parse_url( $value, PHP_URL_SCHEME );

        return in_array( $scheme, [ 'http', 'https', 'mailto', 'tel' ], true ) ? $value : '';
    }
}
