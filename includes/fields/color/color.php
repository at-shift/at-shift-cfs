<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


class cfs_color extends cfs_field
{

    function __construct() {
        $this->name = 'color';
        $this->label = __( 'Color', 'at-shift-cfs' );
    }


    function options_html( $key, $field ) {
    ?>
        <tr class="field_option field_option_<?php echo esc_attr( $this->name ); ?>">
            <td class="label">
                <label><?php esc_html_e( 'Default Value', 'at-shift-cfs' ); ?></label>
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
    <?php
    }


    function input_head( $field = null ) {
        wp_register_script( 'miniColors', esc_url( CFS_URL . '/includes/fields/color/jquery.miniColors.min.js' ), [ 'jquery' ], CFS_VERSION, true );
        wp_enqueue_script( 'miniColors' );
        wp_enqueue_style( 'miniColors', esc_url( CFS_URL . '/includes/fields/color/color.css' ), [], CFS_VERSION );
    ?>
        <script>
        (function($) {
            $(document).on('focus', '.cfs_color input.color', function() {
                if (!$(this).hasClass('ready')) {
                    $(this).addClass('ready').minicolors();
                }
            });

            $(function() {
                $('.cfs_color input.color').addClass('ready').minicolors();
            });
        })(jQuery);
        </script>
    <?php
    }
}
