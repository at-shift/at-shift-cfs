<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


class Atshift_CFS_file extends Atshift_CFS_field
{

    function __construct() {
        $this->name = 'file';
        $this->label = __( 'File Upload', 'atshift-fields-maintenance-for-custom-field-suite' );
    }


    function html( $field ) {
        $field->value = null === $field->value ? '' : $field->value;
        $file_url = $field->value;

        if ( ctype_digit( (string) $field->value ) ) {
            if ( wp_attachment_is_image( $field->value ) ) {
                $file_url = wp_get_attachment_image( $field->value, 'medium', false, [ 'class' => 'cfs-file-preview-image' ] );
                if ( empty( $file_url ) ) {
                    $image_src = wp_get_attachment_image_src( $field->value, 'full' );
                    $file_url = empty( $image_src[0] ) ? '' : '<img src="' . esc_url( $image_src[0] ) . '" class="cfs-file-preview-image" alt="" />';
                }
            }
            else
            {
                $file_url = wp_get_attachment_url( $field->value );
                $filename = substr( $file_url, strrpos( $file_url, '/' ) + 1 );
                $file_url = '<a href="' . esc_url( $file_url ) . '" target="_blank" rel="noopener noreferrer">' . esc_html( $filename ) . '</a>';
            }
        }

        // CSS logic for "Add" / "Remove" buttons
        $css = empty( $field->value ) ? [ '', ' hidden' ] : [ ' hidden', '' ];
        $file_type = $this->get_option( $field, 'file_type', 'file' );
    ?>
        <span class="file_url"><?php echo wp_kses_post( $file_url ); ?></span>
        <input type="button" class="media button add<?php echo esc_attr( $css[0] ); ?>" data-file-type="<?php echo esc_attr( $file_type ); ?>" value="<?php esc_attr_e( 'Add File', 'atshift-fields-maintenance-for-custom-field-suite' ); ?>" />
        <input type="button" class="media button remove<?php echo esc_attr( $css[1] ); ?>" value="<?php esc_attr_e( 'Remove', 'atshift-fields-maintenance-for-custom-field-suite' ); ?>" />
        <input type="hidden" name="<?php echo esc_attr( $field->input_name ); ?>" class="file_value" value="<?php echo esc_attr( $field->value ); ?>" />
    <?php
    }


    function options_html( $key, $field ) {
    ?>
        <tr class="field_option field_option_<?php echo esc_attr( $this->name ); ?>">
            <td class="label">
                <label><?php esc_html_e( 'File Type', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></label>
            </td>
            <td>
                <?php
                    atshift_fields_maintenance_for_custom_field_suite()->create_field( [
                        'type' => 'select',
                        'input_name' => 'cfs[fields]['  . $this->normalize_admin_key( $key ) . '][options][file_type]',
                        'options' => [
                            'choices' => [
                                'file'  => __( 'Any', 'atshift-fields-maintenance-for-custom-field-suite' ),
                                'image' => __( 'Image', 'atshift-fields-maintenance-for-custom-field-suite' ),
                                'audio' => __( 'Audio', 'atshift-fields-maintenance-for-custom-field-suite' ),
                                'video' => __( 'Video', 'atshift-fields-maintenance-for-custom-field-suite' )
                            ],
                            'force_single' => true,
                        ],
                        'value' => $this->get_option( $field, 'file_type', 'file' ),
                    ] );
                ?>
            </td>
        </tr>
        <tr class="field_option field_option_<?php echo esc_attr( $this->name ); ?>">
            <td class="label">
                <label><?php esc_html_e( 'Return Value', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></label>
            </td>
            <td>
                <?php
                    atshift_fields_maintenance_for_custom_field_suite()->create_field( [
                        'type' => 'select',
                        'input_name' => 'cfs[fields]['  . $this->normalize_admin_key( $key ) . '][options][return_value]',
                        'options' => [
                            'choices' => [
                                'url' => __( 'File URL', 'atshift-fields-maintenance-for-custom-field-suite' ),
                                'id' => __( 'Attachment ID', 'atshift-fields-maintenance-for-custom-field-suite' )
                            ],
                            'force_single' => true,
                        ],
                        'value' => $this->get_option( $field, 'return_value', 'url' ),
                    ] );
                ?>
            </td>
        </tr>
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


    function input_head( $field = null ) {
        static $inserted = false;

        if ( $inserted ) {
            return;
        }

        $inserted = true;
        wp_enqueue_media();

        $script_path = ATSHIFT_CFS_DIR . '/assets/js/file.js';
        $script_url  = ATSHIFT_CFS_URL . '/assets/js/file.js';
        $version     = file_exists( $script_path ) ? ATSHIFT_CFS_VERSION . '.' . filemtime( $script_path ) : ATSHIFT_CFS_VERSION;

        wp_enqueue_script(
            'atshift-cfs-file',
            $script_url,
            [ 'jquery', 'media-views' ],
            $version,
            true
        );

        wp_add_inline_script(
            'atshift-cfs-file',
            'window.ATSHIFT_CFS_FILE = ' . wp_json_encode( [
                'title'  => __( 'Add File', 'atshift-fields-maintenance-for-custom-field-suite' ),
                'button' => __( 'Add File', 'atshift-fields-maintenance-for-custom-field-suite' ),
            ] ) . ';',
            'before'
        );
    }


    function format_value_for_api( $value, $field = null ) {
        if ( ctype_digit( $value ) ) {
            $return_value = $this->get_option( $field, 'return_value', 'url' );
            return ( 'id' == $return_value ) ? (int) $value : wp_get_attachment_url( $value );
        }
        return $value;
    }
}
