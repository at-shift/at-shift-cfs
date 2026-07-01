<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


class cfs_true_false extends cfs_field
{

    function __construct() {
        $this->name = 'true_false';
        $this->label = __('True / False', 'at-shift-cfs' );
    }




    function html( $field ) {
        $field->value = ( 0 < (int) $field->value ) ? 1 : 0;
        $message = isset( $field->options['message'] ) ? $field->options['message'] : '';
    ?>
		<label>
			<input type="checkbox" <?php echo $field->value ? ' checked' : ''; ?>>
				<span><?php echo wp_kses_post( $message ); ?></span>
			<input type="hidden" name="<?php echo esc_attr( $field->input_name ); ?>" class="<?php echo esc_attr( $field->input_class ); ?>" value="<?php echo esc_attr( $field->value ); ?>" />
		</label>
    <?php
    }




    function options_html( $key, $field ) {
    ?>
        <tr class="field_option field_option_<?php echo esc_attr( $this->name ); ?>">
            <td class="label">
                <label><?php esc_html_e( 'Message', 'at-shift-cfs' ); ?></label>
                <p class="description"><?php esc_html_e( 'The text beside the checkbox', 'at-shift-cfs' ); ?></p>
            </td>
            <td>
                <?php
                    CFS()->create_field( [
                        'type' => 'text',
                        'input_name' => 'cfs[fields][' . absint( $key ) . '][options][message]',
                        'value' => $this->get_option( $field, 'message' ),
                    ] );
                ?>
            </td>
        </tr>
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




    function input_head( $field = null ) {
    ?>
        <script>
        (function($) {
            $(function() {
                $(document).on('cfs/ready', '.cfs_add_field', function() {
                    $('.cfs_true_false:not(.ready)').init_true_false();
                });
                $('.cfs_true_false').init_true_false();
            });

            $.fn.init_true_false = function() {
                this.each(function() {
                    var $this = $(this);
                    $this.addClass('ready');

                    // handle click
                    $this.find('input[type="checkbox"]').on('change click', function() {
                        var val = $(this).prop('checked') ? 1 : 0;
                        $(this).siblings('.true_false').val(val);
                    });
                });
            }
        })(jQuery);
        </script>
    <?php
    }




    function format_value_for_api( $value, $field = null ) {
        return ( 0 < (int) $value ) ? 1 : 0;
    }
}
