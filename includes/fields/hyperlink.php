<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


class cfs_hyperlink extends cfs_field
{


    function __construct() {
        $this->name = 'hyperlink';
        $this->label = __( 'Hyperlink', 'at-shift-cfs' );
    }


    function html( $field ) {
        $field->value = [
            'url'    => isset( $field->value['url'] ) ? $field->value['url'] : '',
            'text'   => isset( $field->value['text'] ) ? $field->value['text'] : '',
            'class'  => isset( $field->value['class'] ) ? $field->value['class'] : '',
            'target' => isset( $field->value['target'] ) ? $field->value['target'] : '',
        ];
    ?>
        <div class="cfs-hyperlink" style="overflow:hidden;">
            <div class="cfs-hyperlink-url" style="width:39%;float:left;">
                <div><?php esc_html_e( 'URL', 'at-shift-cfs' ); ?></div>
                <input type="text" name="<?php echo esc_attr( $field->input_name ); ?>[url]" class="link-url" value="<?php echo esc_url( $field->value['url'] ); ?>" placeholder="<?php echo esc_attr( $this->get_input_placeholder( $field, 'http://' ) ); ?>" />
            </div>
            <div class="cfs-hyperlink-text" style="width:39%;float:left;margin-left:1%;">
                <div><?php esc_html_e( 'Link Text', 'at-shift-cfs' ); ?></div>
                <input type="text" name="<?php echo esc_attr( $field->input_name ); ?>[text]" class="link-text" value="<?php echo esc_attr( $field->value['text'] ); ?>" />
            </div>
            <div class="cfs-hyperlink-target" style="width:19%;float:left;margin-left:1%;">
                <div><?php esc_html_e( 'Link Target', 'at-shift-cfs' ); ?></div>
                <select class="link-target widefat" name="<?php echo esc_attr( $field->input_name ); ?>[target]">
                    <option value="none" <?php selected( 'none', esc_attr( $field->value['target'] ) ); ?>>None</option>
                    <option value="_blank" <?php selected( '_blank', esc_attr( $field->value['target'] ) ); ?>>_blank</option>
                    <option value="_self" <?php selected( '_self', esc_attr( $field->value['target'] ) ); ?>>_self</option>
                    <option value="_top" <?php selected( '_top', esc_attr( $field->value['target'] ) ); ?>>_top</option>
                </select>
            </div>
        </div>
    <?php
    }


    function options_html( $key, $field = null ) {
        $this->text_option_html( $key, $field, 'placeholder', __( 'Placeholder', 'at-shift-cfs' ), __( 'An input example or other helpful hint.', 'at-shift-cfs' ) );
    ?>
        <tr class="field_option field_option_<?php echo esc_attr( $this->name ); ?>">
            <td class="label">
                <label><?php esc_html_e('Output format', 'at-shift-cfs' ); ?></label>
            </td>
            <td>
                <?php
                    CFS()->create_field( [
                        'type' => 'select',
                        'input_name' => 'cfs[fields][' . absint( $key ) . '][options][format]',
                        'options' => [
                            'choices' => [
                                'html' => __( 'HTML', 'at-shift-cfs' ),
                                'php' => __( 'PHP Array', 'at-shift-cfs' )
                            ],
                            'force_single' => true,
                        ],
                        'value' => $this->get_option( $field, 'format', 'html' ),
                    ] );
                ?>
            </td>
        </tr>
    <?php
    }


    function pre_save( $value, $field = null ) {
        // convert to a proper associative array when inside a Loop
        if ( isset( $value[0]['url'], $value[1]['text'], $value[2]['target'] ) ) {
            $value = [
                'url'    => $value[0]['url'],
                'text'   => $value[1]['text'],
                'target' => $value[2]['target'],
            ];
        }
        return serialize( $value );
    }


    function prepare_value( $value, $field = null ) {
        $output = @unserialize( $value[0], [ 'allowed_classes' => false ] );
        return is_array( $output ) ? $output : [];
    }


    function format_value_for_api( $value, $field = null ) {
        $url    = isset( $value['url'] ) ? $value['url'] : '';
        $text   = isset( $value['text'] ) ? $value['text'] : $value['url'];
        $target = isset( $value['target'] ) ? $value['target'] : '';
        $format = $this->get_option( $field, 'format', 'html' );
        
        // target="none" (sometimes?) opens a new tab
        if ( 'none' == $target ) {
            $target = '';
        }

        // Return an HTML string
        if ( 'html' == $format ) {
            $output = '';
            if ( ! empty( $url ) ) {
                $output = '<a class="cfs-hyperlink" href="' . esc_url( $url ) . '" target="' . esc_attr( $target ) . '"><span class="text">' . esc_html( $text ) . '</span></a>';
            }
        }

        // Return an associative array
        elseif ( 'php' == $format ) {
            $output = $value;
        }

        return $output;
    }
}
