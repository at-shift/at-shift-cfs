<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


class Atshift_CFS_code_view extends Atshift_CFS_field
{

    function __construct() {
        $this->name = 'code_view';
        $this->label = __( 'Code View', 'atshift-fields-maintenance-for-custom-field-suite' );

        add_action( 'wp_enqueue_scripts', [ $this, 'maybe_enqueue_assets' ] );
    }


    function html( $field ) {
        $field->value = $this->normalize_value_with_default( $field->value, '' );
    ?>
        <div class="atshift-cfs-code-view-language-control" style="margin-bottom:10px;">
            <label style="display:block;margin-bottom:3px;"><?php esc_html_e( 'Language', 'atshift-fields-maintenance-for-custom-field-suite' ); ?><?php echo Atshift_CFS_field::is_required_field( $field ) ? wp_kses_post( Atshift_CFS_field::required_badge() ) : ''; ?></label>
            <select name="<?php echo esc_attr( $field->input_name ); ?>[language]" class="atshift-cfs-code-view-language">
                <?php foreach ( $this->get_input_languages() as $language => $label ) : ?>
                    <option value="<?php echo esc_attr( $language ); ?>"<?php selected( $field->value['language'], $language ); ?>><?php echo esc_html( $label ); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <textarea name="<?php echo esc_attr( $field->input_name ); ?>[code]" class="<?php echo esc_attr( $field->input_class ); ?>" rows="8" spellcheck="false"><?php echo esc_textarea( $field->value['code'] ); ?></textarea>
    <?php
    }


    function options_html( $key, $field ) {
    ?>
        <tr class="field_option field_option_<?php echo esc_attr( $this->name ); ?>">
            <td class="label">
                <label><?php esc_html_e( 'Default Value', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></label>
            </td>
            <td>
                <?php
                    atshift_fields_maintenance_for_custom_field_suite()->create_field( [
                        'type' => 'textarea',
                        'input_name' => 'cfs[fields][' . absint( $key ) . '][options][default_value]',
                        'value' => $this->get_option( $field, 'default_value' ),
                    ] );
                ?>
            </td>
        </tr>
        <tr class="field_option field_option_<?php echo esc_attr( $this->name ); ?>">
            <td class="label">
                <label><?php esc_html_e( 'Copy Button', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></label>
            </td>
            <td>
                <?php
                    atshift_fields_maintenance_for_custom_field_suite()->create_field( [
                        'type' => 'true_false',
                        'input_name' => 'cfs[fields][' . absint( $key ) . '][options][copy_button]',
                        'input_class' => 'true_false',
                        'value' => $this->get_option( $field, 'copy_button', 1 ),
                        'options' => [ 'message' => __( 'Show copy button', 'atshift-fields-maintenance-for-custom-field-suite' ) ],
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


    function enqueue_assets() {
        wp_enqueue_style( 'atshift-cfs-code-view', ATSHIFT_CFS_URL . '/assets/css/code-view.css', [], ATSHIFT_CFS_VERSION );
        wp_enqueue_script( 'atshift-cfs-code-view', ATSHIFT_CFS_URL . '/assets/js/code-view.js', [], ATSHIFT_CFS_VERSION, true );
    }


    function maybe_enqueue_assets() {
        global $wp_query;

        $post_ids = [];
        if ( isset( $wp_query->posts ) && is_array( $wp_query->posts ) ) {
            foreach ( $wp_query->posts as $post ) {
                if ( $post instanceof WP_Post ) {
                    $post_ids[] = (int) $post->ID;
                }
            }
        }

        foreach ( array_unique( $post_ids ) as $post_id ) {
            $fields = atshift_fields_maintenance_for_custom_field_suite()->api->find_input_fields( [
                'post_id'    => $post_id,
                'field_type' => 'code_view',
            ] );

            if ( ! empty( $fields ) ) {
                $this->enqueue_assets();
                return;
            }
        }
    }


    function pre_save( $value, $field = null ) {
        $value = $this->normalize_value( $this->normalize_posted_value( $value ) );

        return serialize( $value );
    }


    function prepare_value( $value, $field = null ) {
        $stored_value = isset( $value[0] ) ? $value[0] : '';
        $unserialized = @unserialize( $stored_value, [ 'allowed_classes' => false ] );

        return $this->normalize_value( is_array( $unserialized ) ? $unserialized : $stored_value );
    }


    function format_value_for_api( $value, $field = null ) {
        $value = $this->normalize_value( $value );

        if ( '' === $value['code'] ) {
            return '';
        }

        $this->enqueue_assets();

        $language = $this->sanitize_language( $value['language'] );
        $language_label = $this->get_language_label( $language );
        $language_class = sanitize_html_class( 'language-' . str_replace( '_', '-', $language ) );
        $copy_button = 0 < (int) $this->get_option( $field, 'copy_button', 1 );

        $button = '';
        if ( $copy_button ) {
            $button = sprintf(
                '<button type="button" class="atshift-cfs-code-view-copy" data-label="%1$s" data-copied="%2$s">%1$s</button>',
                esc_attr__( 'Copy', 'atshift-fields-maintenance-for-custom-field-suite' ),
                esc_attr__( 'Copied', 'atshift-fields-maintenance-for-custom-field-suite' )
            );
        }

        return sprintf(
            '<div class="atshift-cfs-code-view"><div class="atshift-cfs-code-view-header"><span class="atshift-cfs-code-view-language-label">%1$s</span>%2$s</div><pre><code class="%3$s">%4$s</code></pre></div>',
            esc_html( $language_label ),
            $button,
            esc_attr( $language_class ),
            esc_html( $value['code'] )
        );
    }


    function format_value_for_input( $value, $field = null ) {
        return $this->normalize_value( $value );
    }


    private function normalize_posted_value( $value ) {
        if ( isset( $value['code'] ) || isset( $value['language'] ) ) {
            return $value;
        }

        if ( is_array( $value ) ) {
            $normalized = [];

            foreach ( $value as $item ) {
                if ( ! is_array( $item ) ) {
                    continue;
                }

                if ( isset( $item['code'] ) ) {
                    $normalized['code'] = $item['code'];
                }

                if ( isset( $item['language'] ) ) {
                    $normalized['language'] = $item['language'];
                }
            }

            if ( ! empty( $normalized ) ) {
                return $normalized;
            }

            return reset( $value );
        }

        return $value;
    }


    private function normalize_value( $value ) {
        return $this->normalize_value_with_default( $value, 'plain_text' );
    }


    private function normalize_value_with_default( $value, $default_language ) {
        if ( is_array( $value ) ) {
            return [
                'code'     => isset( $value['code'] ) && is_scalar( $value['code'] ) ? (string) $value['code'] : '',
                'language' => $this->sanitize_language( isset( $value['language'] ) ? $value['language'] : $default_language, $default_language ),
            ];
        }

        return [
            'code'     => is_scalar( $value ) ? (string) $value : '',
            'language' => $default_language,
        ];
    }


    private function sanitize_language( $language, $default = 'plain_text' ) {
        $language = is_scalar( $language ) ? (string) $language : $default;
        $languages = $this->get_languages();

        return isset( $languages[ $language ] ) ? $language : $default;
    }


    private function get_language_label( $language ) {
        $languages = $this->get_languages();

        return isset( $languages[ $language ] ) ? $languages[ $language ] : $languages['plain_text'];
    }


    private function get_languages() {
        return [
            'plain_text' => __( 'Plain text', 'atshift-fields-maintenance-for-custom-field-suite' ),
            'html'       => 'HTML',
            'css'        => 'CSS',
            'javascript' => 'JavaScript',
            'php'        => 'PHP',
            'bash'       => 'Bash',
            'json'       => 'JSON',
        ];
    }


    private function get_input_languages() {
        return [ '' => __( 'Please select a language...', 'atshift-fields-maintenance-for-custom-field-suite' ) ] + $this->get_languages();
    }
}
