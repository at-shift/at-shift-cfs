<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


class Atshift_CFS_post_content extends Atshift_CFS_field
{

    function __construct() {
        $this->name = 'post_content';
        $this->label = __( 'Post Content (Native)', 'atshift-fields-maintenance-for-custom-field-suite' );
    }


    function html( $field ) {
        global $post;

        if ( ! ( $post instanceof WP_Post ) ) {
            return;
        }

        $post_id = absint( $post->ID );
        $can_edit_content = current_user_can( 'edit_post', $post_id );
        $editor_type = $this->get_editor_type( $field );
        $content = $post->post_content;

        if ( isset( $field->value ) && is_scalar( $field->value ) && '' !== (string) $field->value ) {
            $content = (string) $field->value;
        }

        if ( ! $can_edit_content ) {
            echo '<p class="notes">' . esc_html__( 'You do not have permission to change the native content.', 'atshift-fields-maintenance-for-custom-field-suite' ) . '</p>';
            return;
        }

        if ( 'wysiwyg' === $editor_type && function_exists( 'wp_editor' ) ) {
            $editor_id = 'atshift_cfs_post_content_' . absint( $field->id );
            $content = $this->normalize_editor_input_value( $content );
            $tinymce_settings = [
                'content_style' => $this->get_tinymce_content_style(),
            ];

            wp_editor(
                $content,
                $editor_id,
                [
                    'textarea_name' => $field->input_name,
                    'textarea_rows' => 10,
                    'media_buttons' => true,
                    'teeny'         => false,
                    'quicktags'     => true,
                    'tinymce'       => $tinymce_settings,
                    'editor_class'  => trim( $field->input_class . ' post_content_value' ),
                ]
            );
            return;
        }
    ?>
        <textarea name="<?php echo esc_attr( $field->input_name ); ?>" class="<?php echo esc_attr( trim( $field->input_class . ' post_content_value' ) ); ?>" rows="10"><?php echo esc_textarea( $content ); ?></textarea>
    <?php
    }


    function options_html( $key, $field ) {
    ?>
        <tr class="field_option field_option_<?php echo esc_attr( $this->name ); ?>">
            <td class="label">
                <label>
                    <?php esc_html_e( 'Content Editor', 'atshift-fields-maintenance-for-custom-field-suite' ); ?>
                    <div class="cfs_tooltip">?
                        <div class="tooltip_inner"><?php esc_html_e( 'Choose how editors edit the native WordPress content. This field saves to post_content and does not use the Gutenberg / block editor.', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></div>
                    </div>
                </label>
            </td>
            <td>
                <?php
                    atshift_fields_maintenance_for_custom_field_suite()->create_field( [
                        'type' => 'select',
                        'input_name' => 'cfs[fields]['  . $this->normalize_admin_key( $key ) . '][options][editor_type]',
                        'options' => [
                            'choices' => [
                                'textarea' => __( 'Textarea', 'atshift-fields-maintenance-for-custom-field-suite' ),
                                'wysiwyg'  => __( 'Visual editor', 'atshift-fields-maintenance-for-custom-field-suite' ),
                            ],
                            'force_single' => true,
                        ],
                        'value' => $this->get_editor_type( $field ),
                    ] );
                ?>
            </td>
        </tr>
    <?php
        parent::options_html( $key, $field );
    }


    function input_head( $field = null ) {
        if ( function_exists( 'wp_enqueue_editor' ) ) {
            wp_enqueue_editor();
        }

        if ( function_exists( 'wp_enqueue_media' ) ) {
            wp_enqueue_media();
        }
    ?>
        <?php wp_add_inline_script( 'atshift-cfs-validation', atshift_cfs_capture_output( function() { ?>
        (function($) {
            function triggerEditorSave() {
                if (window.tinyMCE && 'function' === typeof tinyMCE.triggerSave) {
                    tinyMCE.triggerSave();
                }
            }

            function syncPostContent(value) {
                var content = value || '';
                $('#content, textarea[name="content"]').val(content).trigger('input').trigger('change');

                if (window.wp && wp.data && wp.data.dispatch) {
                    var editor = wp.data.dispatch('core/editor');
                    if (editor && 'function' === typeof editor.editPost) {
                        editor.editPost({ content: content });
                    }
                }
            }

            function syncAllPostContentFields() {
                triggerEditorSave();

                $('.cfs_post_content .post_content_value').each(function() {
                    syncPostContent($(this).val());
                });
            }

            $(document).on('input change', '.cfs_post_content .post_content_value', function() {
                syncPostContent($(this).val());
            });

            $(document).on('submit', 'form#post, form.cfs-post-content-form', syncAllPostContentFields);

            $(function() {
                syncAllPostContentFields();
            });
        })(jQuery);
        <?php } ) ); ?>
    <?php
    }


    function pre_save( $value, $field = null ) {
        $post_id = isset( $field->post_id ) ? absint( $field->post_id ) : 0;
        $content = is_array( $value ) ? reset( $value ) : $value;
        $content = is_scalar( $content ) ? (string) $content : '';
        $content = $this->normalize_editor_input_value( $content );

        if ( 0 < $post_id && current_user_can( 'edit_post', $post_id ) ) {
            $post = get_post( $post_id );

            if ( $post instanceof WP_Post && $post->post_content !== $content ) {
                wp_update_post( [
                    'ID'           => $post_id,
                    'post_content' => $content,
                ] );
            }
        }

        return [];
    }


    function pre_save_field( $field ) {
        $field['options']['editor_type'] = isset( $field['options']['editor_type'] ) && 'wysiwyg' === $field['options']['editor_type'] ? 'wysiwyg' : 'textarea';
        return $field;
    }


    protected function get_editor_type( $field = null ) {
        $editor_type = $this->get_option( $field, 'editor_type', 'textarea' );
        return 'wysiwyg' === $editor_type ? 'wysiwyg' : 'textarea';
    }
}
