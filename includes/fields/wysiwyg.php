<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


class Atshift_CFS_wysiwyg extends Atshift_CFS_field
{

    function __construct() {
        $this->name = 'wysiwyg';
        $this->label = __( 'Wysiwyg Editor', 'atshift-fields-maintenance-for-custom-field-suite' );

        // add the "code" button
        add_filter( 'mce_external_plugins', [ $this, 'mce_external_plugins' ], 20 );
    }


    function html( $field ) {
        $field->value = null === $field->value ? '' : $field->value;
    ?>
        <div class="wp-editor-wrap">
            <div class="wp-media-buttons">
                <?php do_action( 'media_buttons' ); ?>
            </div>
            <div class="wp-editor-container">
                <textarea name="<?php echo esc_attr( $field->input_name ); ?>" class="wp-editor-area <?php echo esc_attr( $field->input_class ); ?>" style="height:300px"><?php echo esc_textarea( $field->value ); ?></textarea>
            </div>
        </div>
    <?php
    }


    function options_html( $key, $field ) {
    ?>
        <tr class="field_option field_option_<?php echo esc_attr( $this->name ); ?>">
            <td class="label">
                <label><?php esc_html_e( 'Formatting', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></label>
            </td>
            <td>
                <?php
                    atshift_fields_maintenance_for_custom_field_suite()->create_field( [
                        'type' => 'select',
                        'input_name' => 'cfs[fields]['  . $this->normalize_admin_key( $key ) . '][options][formatting]',
                        'options' => [
                            'choices' => [
                                'default' => __( 'Default', 'atshift-fields-maintenance-for-custom-field-suite' ),
                                'none' => __( 'None (bypass filters)', 'atshift-fields-maintenance-for-custom-field-suite' )
                            ],
                            'force_single' => true,
                        ],
                        'value' => $this->get_option( $field, 'formatting', 'default' ),
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

        // make sure the user has WYSIWYG enabled
        if ( 'true' == get_user_meta( get_current_user_id(), 'rich_editing', true ) ) {
            if ( ! is_admin() ) {
    ?>
        <div class="hidden"><?php wp_editor( '', 'cfswysi' ); ?></div>
    <?php
            }
    ?>
        <?php wp_add_inline_script( 'atshift-cfs-validation', atshift_cfs_capture_output( function() { ?>
        (function($) {

            var wpautop;
            var resize;
            var wysiwyg_count = 0;
            var cfsCodePluginUrl = '<?php echo esc_js( ATSHIFT_CFS_URL . '/assets/js/tinymce/code.min.js' ); ?>';

            function ensurePlugin(settings, plugin, url) {
                var plugins = settings.plugins ? settings.plugins.split(',') : [];

                if (plugins.indexOf(plugin) < 0) {
                    plugins.push(plugin);
                }
                settings.plugins = plugins.join(',');

                if (url) {
                    settings.external_plugins = settings.external_plugins || {};
                    settings.external_plugins[plugin] = url;
                }
            }

            $(function() {
                $(document).on('cfs/ready', '.cfs_add_field', function() {
                    $('.cfs_wysiwyg:not(.ready)').init_wysiwyg();
                });
                $('.cfs_wysiwyg').init_wysiwyg();

                // set the active editor
                $(document).on('click', 'a.add_media', function() {
                    var editor_id = $(this).closest('.wp-editor-wrap').find('.wp-editor-area').attr('id');
                    wpActiveEditor = editor_id;
                });
            });

            $.fn.init_wysiwyg = function() {
                this.each(function() {
                    $(this).addClass('ready');

                    // generate css id
                    wysiwyg_count = wysiwyg_count + 1;
                    var input_id = 'cfs_wysiwyg_' + wysiwyg_count;

                    // set the wysiwyg css id
                    $(this).find('.wysiwyg').attr('id', input_id);
                    $(this).find('a.add_media').attr('data-editor', input_id);

                    // if all editors on page are in 'text' tab, tinyMCE.settings will not be set
                    if ('undefined' === typeof tinyMCE.settings || Object.keys(tinyMCE.settings).length === 0) {

                        // let's pull from tinyMCEPreInit for main content area (if it's set)
                        if ('undefined' !== typeof tinyMCEPreInit && 'undefined' !== typeof tinyMCEPreInit.mceInit.content) {
                            tinyMCE.settings = tinyMCEPreInit.mceInit.content;
                        }
                        // otherwise, setup basic settings object
                        else {
                            tinymce.settings = {
                                wpautop : true,
                                resize : 'vertical',
                                toolbar2 : 'code'
                            };
                        }
                    }

                    // add the "code" button
                    if ('undefined' !== typeof tinyMCE.settings.toolbar2) {
                        if (tinyMCE.settings.toolbar2.indexOf('code') < 0) {
                            tinyMCE.settings.toolbar2 += ',code';
                        }
                    }

                    // create wysiwyg
                    wpautop = tinyMCE.settings.wpautop;
                    resize = tinyMCE.settings.resize;

                    ensurePlugin(tinyMCE.settings, 'code', cfsCodePluginUrl);
                    ensurePlugin(tinyMCE.settings, 'link');

                    tinyMCE.settings.wpautop = false;
                    tinyMCE.settings.resize = 'vertical';
                    tinyMCE.execCommand('mceAddEditor', false, input_id);
                    tinyMCE.settings.wpautop = wpautop;
                    tinyMCE.settings.resize = resize;
                });
            };

            $('.meta-box-sortables, .cfs_loop').on('sortstart', function(event, ui) {
                tinyMCE.settings.wpautop = false;
                tinyMCE.settings.resize = 'vertical';
                $(this).find('.wysiwyg').each(function() {
                    tinyMCE.execCommand('mceRemoveEditor', false, $(this).attr('id'));
                });
            });

            $('.meta-box-sortables, .cfs_loop').on('sortstop', function(event, ui) {
                $(this).find('.wysiwyg').each(function() {
                    tinyMCE.execCommand('mceAddEditor', false, $(this).attr('id'));
                });
                tinyMCE.settings.wpautop = wpautop;
                tinyMCE.settings.resize = resize;
            });
        })(jQuery);
        <?php } ) ); ?>
    <?php
        }
    }


    function mce_external_plugins( $plugins ) {
        if ( version_compare( get_bloginfo( 'version' ), '3.9', '>=' ) ) {
            $plugins['code'] = ATSHIFT_CFS_URL . '/assets/js/tinymce/code.min.js';
        }
        return $plugins;
    }


    function format_value_for_input( $value, $field = null ) {
        return format_for_editor( $value );
    }


    function format_value_for_api( $value, $field = null ) {
        $formatting = $this->get_option( $field, 'formatting', 'default' );
        return ( 'none' == $formatting ) ? $value : apply_filters( 'the_content', $value );
    }
}
