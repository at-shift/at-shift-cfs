<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


class Atshift_CFS_gallery extends Atshift_CFS_field
{

    function __construct() {
        $this->name = 'gallery';
        $this->label = __( 'Photo Gallery', 'atshift-fields-maintenance-for-custom-field-suite' );
    }


    function html( $field ) {
        $attachment_ids = $this->normalize_ids( $field->value );
        $value = implode( ',', $attachment_ids );
    ?>
        <div class="cfs-gallery-control">
            <div class="cfs-gallery-items">
                <?php foreach ( $attachment_ids as $attachment_id ) : ?>
                    <?php echo $this->render_gallery_item( $attachment_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Markup is generated from escaped attachment data. ?>
                <?php endforeach; ?>
            </div>
            <div class="cfs-gallery-actions">
                <input type="button" class="media button add" value="<?php esc_attr_e( 'Add Images', 'atshift-fields-maintenance-for-custom-field-suite' ); ?>" />
                <input type="button" class="button clear<?php echo empty( $attachment_ids ) ? ' hidden' : ''; ?>" value="<?php esc_attr_e( 'Clear Gallery', 'atshift-fields-maintenance-for-custom-field-suite' ); ?>" />
            </div>
            <input type="hidden" name="<?php echo esc_attr( $field->input_name ); ?>" class="gallery_value" value="<?php echo esc_attr( $value ); ?>" />
        </div>
    <?php
    }


    private function render_gallery_item( $attachment_id ) {
        $attachment_id = absint( $attachment_id );

        if ( 0 === $attachment_id || ! wp_attachment_is_image( $attachment_id ) ) {
            return '';
        }

        $image = wp_get_attachment_image( $attachment_id, 'thumbnail', false, [
            'class' => 'cfs-gallery-preview-image',
        ] );

        if ( empty( $image ) ) {
            return '';
        }

        return sprintf(
            '<div class="cfs-gallery-item" data-id="%1$d"><span class="cfs-gallery-drag" aria-hidden="true"></span>%2$s<button type="button" class="cfs-gallery-remove" aria-label="%3$s">&times;</button></div>',
            $attachment_id,
            $image,
            esc_attr__( 'Remove image', 'atshift-fields-maintenance-for-custom-field-suite' )
        );
    }


    function options_html( $key, $field ) {
        $image_sizes = $this->get_image_size_choices();
    ?>
        <tr class="field_option field_option_<?php echo esc_attr( $this->name ); ?>">
            <td class="label">
                <label><?php esc_html_e( 'Return Value', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></label>
            </td>
            <td>
                <?php
                    atshift_fields_maintenance_for_custom_field_suite()->create_field( [
                        'type' => 'select',
                        'input_name' => 'cfs[fields][' . absint( $key ) . '][options][return_value]',
                        'options' => [
                            'choices' => [
                                'ids'        => __( 'Attachment IDs', 'atshift-fields-maintenance-for-custom-field-suite' ),
                                'urls'       => __( 'Image URLs', 'atshift-fields-maintenance-for-custom-field-suite' ),
                                'image_data' => __( 'Image Data', 'atshift-fields-maintenance-for-custom-field-suite' ),
                                'html'       => __( 'Gallery HTML', 'atshift-fields-maintenance-for-custom-field-suite' ),
                            ],
                            'force_single' => true,
                        ],
                        'value' => $this->get_option( $field, 'return_value', 'image_data' ),
                    ] );
                ?>
            </td>
        </tr>
        <tr class="field_option field_option_<?php echo esc_attr( $this->name ); ?>">
            <td class="label">
                <label><?php esc_html_e( 'Image Size', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></label>
            </td>
            <td>
                <?php
                    atshift_fields_maintenance_for_custom_field_suite()->create_field( [
                        'type' => 'select',
                        'input_name' => 'cfs[fields][' . absint( $key ) . '][options][image_size]',
                        'options' => [
                            'choices' => $image_sizes,
                            'force_single' => true,
                        ],
                        'value' => $this->get_option( $field, 'image_size', 'medium' ),
                    ] );
                ?>
            </td>
        </tr>
        <tr class="field_option field_option_<?php echo esc_attr( $this->name ); ?>">
            <td class="label">
                <label><?php esc_html_e( 'Gallery Display', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></label>
                <p class="description"><?php esc_html_e( 'Used when the return value is Gallery HTML. Image file links include modal-friendly data attributes.', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></p>
            </td>
            <td>
                <input type="text" name="cfs[fields][<?php echo absint( $key ); ?>][options][columns]" value="<?php echo esc_attr( $this->get_option( $field, 'columns', 3 ) ); ?>" placeholder="<?php esc_attr_e( 'Columns', 'atshift-fields-maintenance-for-custom-field-suite' ); ?>" style="width:80px" />
                <input type="text" name="cfs[fields][<?php echo absint( $key ); ?>][options][css_class]" value="<?php echo esc_attr( $this->get_option( $field, 'css_class' ) ); ?>" placeholder="<?php esc_attr_e( 'CSS class', 'atshift-fields-maintenance-for-custom-field-suite' ); ?>" />
            </td>
        </tr>
        <tr class="field_option field_option_<?php echo esc_attr( $this->name ); ?>">
            <td class="label">
                <label><?php esc_html_e( 'Link To', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></label>
            </td>
            <td>
                <?php
                    atshift_fields_maintenance_for_custom_field_suite()->create_field( [
                        'type' => 'select',
                        'input_name' => 'cfs[fields][' . absint( $key ) . '][options][link_to]',
                        'options' => [
                            'choices' => [
                                'image_file'      => __( 'Image File', 'atshift-fields-maintenance-for-custom-field-suite' ),
                                'attachment_page' => __( 'Attachment Page', 'atshift-fields-maintenance-for-custom-field-suite' ),
                                'none'            => __( 'None', 'atshift-fields-maintenance-for-custom-field-suite' ),
                            ],
                            'force_single' => true,
                        ],
                        'value' => $this->get_option( $field, 'link_to', 'image_file' ),
                    ] );
                ?>
            </td>
        </tr>
        <tr class="field_option field_option_<?php echo esc_attr( $this->name ); ?>">
            <td class="label">
                <label><?php esc_html_e( 'Limits', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></label>
            </td>
            <td>
                <input type="text" name="cfs[fields][<?php echo absint( $key ); ?>][options][limit_min]" value="<?php echo esc_attr( $this->get_option( $field, 'limit_min' ) ); ?>" placeholder="min" style="width:60px" />
                <input type="text" name="cfs[fields][<?php echo absint( $key ); ?>][options][limit_max]" value="<?php echo esc_attr( $this->get_option( $field, 'limit_max' ) ); ?>" placeholder="max" style="width:60px" />
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
                        'input_name' => 'cfs[fields][' . absint( $key ) . '][options][required]',
                        'input_class' => 'true_false',
                        'value' => $this->get_option( $field, 'required' ),
                        'options' => [ 'message' => __( 'This is a required field', 'atshift-fields-maintenance-for-custom-field-suite' ) ],
                    ] );
                ?>
            </td>
        </tr>
    <?php
    }


    private function get_image_size_choices() {
        $choices = [];
        foreach ( get_intermediate_image_sizes() as $size ) {
            $choices[ $size ] = $size;
        }
        $choices['full'] = 'full';

        return $choices;
    }


    function input_head( $field = null ) {
        static $inserted = false;

        if ( $inserted ) {
            return;
        }

        $inserted = true;
        wp_enqueue_media();

        $script_path = ATSHIFT_CFS_DIR . '/assets/js/gallery.js';
        $script_url  = ATSHIFT_CFS_URL . '/assets/js/gallery.js';
        $version     = file_exists( $script_path ) ? ATSHIFT_CFS_VERSION . '.' . filemtime( $script_path ) : ATSHIFT_CFS_VERSION;

        wp_enqueue_script(
            'atshift-cfs-gallery',
            $script_url,
            [ 'jquery', 'jquery-ui-sortable', 'media-views' ],
            $version,
            true
        );

        wp_add_inline_script(
            'atshift-cfs-gallery',
            'window.ATSHIFT_CFS_GALLERY = ' . wp_json_encode( [
                'removeImage'  => __( 'Remove image', 'atshift-fields-maintenance-for-custom-field-suite' ),
                'selectImages' => __( 'Select Gallery Images', 'atshift-fields-maintenance-for-custom-field-suite' ),
                'addImages'    => __( 'Add Images', 'atshift-fields-maintenance-for-custom-field-suite' ),
            ] ) . ';',
            'before'
        );
    }


    function prepare_value( $value, $field = null ) {
        return $this->normalize_ids( $value );
    }


    function format_value_for_input( $value, $field = null ) {
        return implode( ',', $this->normalize_ids( $value ) );
    }


    function format_value_for_api( $value, $field = null ) {
        $attachment_ids = $this->normalize_ids( $value );
        $return_value = $this->get_option( $field, 'return_value', 'image_data' );
        $image_size = $this->get_option( $field, 'image_size', 'medium' );

        if ( 'ids' === $return_value ) {
            return $attachment_ids;
        }

        if ( 'urls' === $return_value ) {
            return array_values( array_filter( array_map( static function( $attachment_id ) use ( $image_size ) {
                $image = wp_get_attachment_image_src( $attachment_id, $image_size );
                return empty( $image[0] ) ? '' : $image[0];
            }, $attachment_ids ) ) );
        }

        if ( 'html' === $return_value ) {
            return $this->render_gallery_html( $attachment_ids, $field );
        }

        return array_values( array_filter( array_map( function( $attachment_id ) use ( $image_size, $field ) {
            return $this->get_image_data( $attachment_id, $image_size, $field );
        }, $attachment_ids ) ) );
    }


    private function get_image_data( $attachment_id, $image_size, $field = null ) {
        $image = wp_get_attachment_image_src( $attachment_id, $image_size );

        if ( empty( $image[0] ) ) {
            return null;
        }

        $full = wp_get_attachment_image_src( $attachment_id, 'full' );
        $sizes = [];

        foreach ( $this->get_image_size_choices() as $size => $label ) {
            $size_image = wp_get_attachment_image_src( $attachment_id, $size );

            if ( empty( $size_image[0] ) ) {
                continue;
            }

            $sizes[ $size ] = [
                'url'    => $size_image[0],
                'width'  => isset( $size_image[1] ) ? absint( $size_image[1] ) : 0,
                'height' => isset( $size_image[2] ) ? absint( $size_image[2] ) : 0,
            ];
        }

        $data = [
            'id'          => absint( $attachment_id ),
            'url'         => $image[0],
            'thumbnail_url' => isset( $sizes['thumbnail']['url'] ) ? $sizes['thumbnail']['url'] : $image[0],
            'large_url'   => isset( $sizes['large']['url'] ) ? $sizes['large']['url'] : ( empty( $full[0] ) ? $image[0] : $full[0] ),
            'width'       => isset( $image[1] ) ? absint( $image[1] ) : 0,
            'height'      => isset( $image[2] ) ? absint( $image[2] ) : 0,
            'full_url'    => empty( $full[0] ) ? $image[0] : $full[0],
            'modal_url'   => empty( $full[0] ) ? $image[0] : $full[0],
            'sizes'       => $sizes,
            'alt'         => get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ),
            'title'       => get_the_title( $attachment_id ),
            'caption'     => wp_get_attachment_caption( $attachment_id ),
            'description' => get_post_field( 'post_content', $attachment_id ),
        ];

        return apply_filters( 'atshift_cfs_gallery_image_data', $data, $attachment_id, $field );
    }


    private function render_gallery_html( $attachment_ids, $field ) {
        $image_size = $this->get_option( $field, 'image_size', 'medium' );
        $columns = absint( $this->get_option( $field, 'columns', 3 ) );
        $columns = min( 8, max( 1, $columns ) );
        $link_to = $this->get_option( $field, 'link_to', 'image_file' );
        $css_class = $this->sanitize_class_list( $this->get_option( $field, 'css_class' ) );
        $classes = trim( 'atshift-cfs-gallery atshift-cfs-gallery-columns-' . $columns . ' ' . $css_class );
        $items = [];

        foreach ( $attachment_ids as $attachment_id ) {
            if ( ! wp_attachment_is_image( $attachment_id ) ) {
                continue;
            }

            $image = wp_get_attachment_image( $attachment_id, $image_size, false, [
                'loading' => 'lazy',
            ] );

            if ( empty( $image ) ) {
                continue;
            }

            $caption = wp_get_attachment_caption( $attachment_id );
            $content = $image;

            if ( 'none' !== $link_to ) {
                $href = 'attachment_page' === $link_to ? get_attachment_link( $attachment_id ) : wp_get_attachment_image_url( $attachment_id, 'full' );
                $full_url = wp_get_attachment_image_url( $attachment_id, 'full' );

                if ( ! empty( $href ) && ! empty( $full_url ) ) {
                    $content = sprintf(
                        '<a class="atshift-cfs-gallery-link" href="%1$s" data-atshift-cfs-gallery-item data-image-id="%2$d" data-full-url="%3$s" data-caption="%4$s" data-alt="%5$s">%6$s</a>',
                        esc_url( $href ),
                        absint( $attachment_id ),
                        esc_url( $full_url ),
                        esc_attr( $caption ),
                        esc_attr( get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) ),
                        $image
                    );
                }
            }

            $items[] = '<figure class="atshift-cfs-gallery-item">' . $content . ( '' === $caption ? '' : '<figcaption>' . esc_html( $caption ) . '</figcaption>' ) . '</figure>';
        }

        if ( empty( $items ) ) {
            return '';
        }

        $html = sprintf(
            '<div class="%1$s" data-columns="%2$d" style="display:grid;grid-template-columns:repeat(%2$d,minmax(0,1fr));gap:var(--atshift-cfs-gallery-gap,16px)">%3$s</div>',
            esc_attr( $classes ),
            $columns,
            implode( '', $items )
        );

        return apply_filters( 'atshift_cfs_gallery_html', $html, $attachment_ids, $field );
    }


    private function sanitize_class_list( $class_list ) {
        $classes = preg_split( '/\s+/', (string) $class_list );
        $classes = array_filter( array_map( 'sanitize_html_class', $classes ) );

        return implode( ' ', $classes );
    }


    function pre_save( $value, $field = null ) {
        return $this->normalize_ids( $value );
    }


    function pre_save_field( $field ) {
        $return_values = [ 'ids', 'urls', 'image_data', 'html' ];
        $field['options']['return_value'] = isset( $field['options']['return_value'] ) && in_array( $field['options']['return_value'], $return_values, true ) ? $field['options']['return_value'] : 'image_data';
        $field['options']['image_size'] = isset( $field['options']['image_size'] ) ? sanitize_key( $field['options']['image_size'] ) : 'medium';
        $link_to_values = [ 'image_file', 'attachment_page', 'none' ];
        $field['options']['link_to'] = isset( $field['options']['link_to'] ) && in_array( $field['options']['link_to'], $link_to_values, true ) ? $field['options']['link_to'] : 'image_file';
        $field['options']['columns'] = isset( $field['options']['columns'] ) ? min( 8, max( 1, absint( $field['options']['columns'] ) ) ) : 3;
        $field['options']['css_class'] = isset( $field['options']['css_class'] ) ? $this->sanitize_class_list( $field['options']['css_class'] ) : '';
        $field['options']['limit_min'] = empty( $field['options']['limit_min'] ) ? '' : absint( $field['options']['limit_min'] );
        $field['options']['limit_max'] = empty( $field['options']['limit_max'] ) ? '' : absint( $field['options']['limit_max'] );

        return $field;
    }


    function normalize_ids( $value ) {
        if ( null === $value || '' === $value ) {
            return [];
        }

        if ( is_array( $value ) ) {
            $first_value = isset( $value[0] ) ? (string) $value[0] : '';
            if ( false !== strpos( $first_value, ',' ) ) {
                $value = $first_value;
            }
        }

        $value = is_array( $value ) ? $value : explode( ',', (string) $value );
        $value = array_map( 'absint', $value );
        $value = array_filter( $value, static function( $attachment_id ) {
            return 0 < $attachment_id && wp_attachment_is_image( $attachment_id );
        } );

        return array_values( array_unique( $value ) );
    }
}
