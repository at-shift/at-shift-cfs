<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


class cfs_text extends cfs_field
{

    function __construct() {
        $this->name = 'text';
        $this->label = __( 'Text', 'at-shift-cfs' );
    }


    function options_html( $key, $field ) {
        $this->text_option_html( $key, $field, 'default_value', __( 'Default Value', 'at-shift-cfs' ), __( 'The value specified from the beginning.', 'at-shift-cfs' ) );
        $this->text_option_html( $key, $field, 'placeholder', __( 'Placeholder', 'at-shift-cfs' ), __( 'An input example or other helpful hint.', 'at-shift-cfs' ) );
    ?>
        <tr class="field_option field_option_<?php echo esc_attr( $this->name ); ?>">
            <td class="label">
                <label><?php esc_html_e( 'Validation', 'at-shift-cfs' ); ?></label>
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

}
