<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


class Atshift_CFS_external_metabox extends Atshift_CFS_field
{

    function __construct() {
        $this->name = 'external_metabox';
        $this->label = __( 'Classic Meta Box Placement', 'atshift-fields-maintenance-for-custom-field-suite' );
    }


    function html( $field ) {
        $meta_box_id = $this->get_meta_box_id( $field );
        $display_width = $this->get_display_width( $field );
        $slot_id = 'atshift-cfs-external-metabox-slot-' . absint( $field->id );
    ?>
        <div
            id="<?php echo esc_attr( $slot_id ); ?>"
            class="atshift-cfs-external-metabox-slot atshift-cfs-external-metabox-width-<?php echo esc_attr( $display_width ); ?>"
            data-metabox-id="<?php echo esc_attr( $meta_box_id ); ?>"
            data-message-missing-id="<?php esc_attr_e( 'Meta box ID is not set.', 'atshift-fields-maintenance-for-custom-field-suite' ); ?>"
            data-message-not-found="<?php esc_attr_e( 'The selected meta box was not found on this edit screen.', 'atshift-fields-maintenance-for-custom-field-suite' ); ?>"
            data-message-unsupported="<?php esc_attr_e( 'This meta box is not recommended to move.', 'atshift-fields-maintenance-for-custom-field-suite' ); ?>"
            data-message-duplicate="<?php esc_attr_e( 'This meta box is already placed in another slot.', 'atshift-fields-maintenance-for-custom-field-suite' ); ?>"
        >
            <div class="atshift-cfs-external-metabox-container"></div>
            <p class="notes atshift-cfs-external-metabox-message" hidden></p>
        </div>
    <?php
    }


    function options_html( $key, $field ) {
    ?>
        <tr class="field_option field_option_<?php echo esc_attr( $this->name ); ?>">
            <td class="label">
                <label>
                    <?php esc_html_e( 'Meta Box ID', 'atshift-fields-maintenance-for-custom-field-suite' ); ?>
                    <div class="cfs_tooltip">?
                        <div class="tooltip_inner"><?php esc_html_e( 'Detect a classic third-party meta box from the post types selected in Placement Rules, or enter its HTML source id manually. The original plugin keeps handling saving.', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></div>
                    </div>
                </label>
            </td>
            <td>
                <div class="atshift-cfs-external-metabox-option">
                    <?php
                        atshift_fields_maintenance_for_custom_field_suite()->create_field( [
                            'type' => 'text',
                            'input_name' => 'cfs[fields][' . $this->normalize_admin_key( $key ) . '][options][meta_box_id]',
                            'value' => $this->get_meta_box_id( $field ),
                        ] );
                    ?>
                    <button
                        type="button"
                        class="button atshift-cfs-discover-metaboxes"
                        data-nonce="<?php echo esc_attr( wp_create_nonce( 'atshift_cfs_admin_nonce' ) ); ?>"
                    ><?php esc_html_e( 'Detect Meta Boxes', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></button>
                </div>
                <select
                    class="atshift-cfs-external-metabox-picker"
                    aria-label="<?php esc_attr_e( 'Detected Meta Boxes', 'atshift-fields-maintenance-for-custom-field-suite' ); ?>"
                    hidden
                ></select>
                <p class="description atshift-cfs-external-metabox-detection-status" aria-live="polite"></p>
                <p class="description"><?php esc_html_e( 'Classic meta boxes rendered in the edit screen can be moved. Block Editor-only panels are not supported.', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></p>
            </td>
        </tr>
        <tr class="field_option field_option_<?php echo esc_attr( $this->name ); ?>">
            <td class="label">
                <label><?php esc_html_e( 'Display Width', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></label>
            </td>
            <td>
                <?php
                    atshift_fields_maintenance_for_custom_field_suite()->create_field( [
                        'type' => 'select',
                        'input_name' => 'cfs[fields][' . $this->normalize_admin_key( $key ) . '][options][display_width]',
                        'options' => [
                            'choices' => [
                                'side' => __( 'Side width (320px)', 'atshift-fields-maintenance-for-custom-field-suite' ),
                                '50' => __( '50%', 'atshift-fields-maintenance-for-custom-field-suite' ),
                                '75' => __( '75%', 'atshift-fields-maintenance-for-custom-field-suite' ),
                                '100' => __( '100%', 'atshift-fields-maintenance-for-custom-field-suite' ),
                            ],
                            'force_single' => true,
                        ],
                        'value' => $this->get_display_width( $field ),
                    ] );
                ?>
                <p class="description"><?php esc_html_e( 'On narrow screens, the meta box is always displayed at 100% width.', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></p>
            </td>
        </tr>
    <?php
    }


    function input_head( $field = null ) {
        wp_add_inline_script( 'atshift-cfs-validation', atshift_cfs_capture_output( function() { ?>
        (function($) {
            var unsupportedIds = [
                'submitdiv',
                'categorydiv',
                'tagsdiv-post_tag',
                'postimagediv',
                'pageparentdiv',
                'formatdiv',
                'commentsdiv',
                'commentstatusdiv',
                'postexcerpt',
                'trackbacksdiv',
                'slugdiv',
                'authordiv',
                'postcustom',
                'revisionsdiv'
            ];

            function escapeId(id) {
                if (window.CSS && 'function' === typeof window.CSS.escape) {
                    return window.CSS.escape(id);
                }
                return String(id).replace(/([ #;?%&,.+*~\':"!^$[\]()=>|\/@])/g, '\\$1');
            }

            function showMessage($slot, message) {
                $slot
                    .addClass('atshift-cfs-external-metabox-slot-has-message')
                    .find('.atshift-cfs-external-metabox-message')
                    .text(message)
                    .prop('hidden', false);
            }

            function isUnsupportedId(id) {
                return -1 !== $.inArray(id, unsupportedIds) || /^cfs_/.test(id);
            }

            function placeExternalMetaBoxes() {
                $('.atshift-cfs-external-metabox-slot').each(function() {
                    var $slot = $(this);
                    var metaBoxId = $.trim($slot.attr('data-metabox-id') || '');
                    var $container = $slot.find('.atshift-cfs-external-metabox-container').first();
                    var $metaBox;
                    var message;

                    $slot.removeClass('atshift-cfs-external-metabox-slot-has-message');
                    $slot.find('.atshift-cfs-external-metabox-message').text('').prop('hidden', true);

                    if (!metaBoxId) {
                        showMessage($slot, $slot.attr('data-message-missing-id'));
                        return;
                    }

                    if (isUnsupportedId(metaBoxId)) {
                        showMessage($slot, $slot.attr('data-message-unsupported'));
                        return;
                    }

                    $metaBox = $('#' + escapeId(metaBoxId));

                    if (!$metaBox.length || !$metaBox.hasClass('postbox')) {
                        message = $slot.attr('data-message-not-found') + ' ' + $slot.attr('data-message-unsupported');
                        showMessage($slot, message);
                        return;
                    }

                    if ($metaBox.closest('.atshift-cfs-external-metabox-slot').length && !$metaBox.closest('.atshift-cfs-external-metabox-slot').is($slot)) {
                        showMessage($slot, $slot.attr('data-message-duplicate'));
                        return;
                    }

                    $metaBox
                        .detach()
                        .addClass('atshift-cfs-external-metabox-mounted')
                        .removeClass('closed')
                        .show()
                        .appendTo($container);

                    $metaBox
                        .children('.postbox-header')
                        .find('.handle-actions button')
                        .prop('disabled', true)
                        .attr({
                            'aria-hidden': 'true',
                            'tabindex': '-1'
                        });

                    $metaBox
                        .children('.handlediv')
                        .prop('disabled', true)
                        .attr({
                            'aria-expanded': 'true',
                            'aria-hidden': 'true',
                            'tabindex': '-1'
                        });

                    $slot.addClass('atshift-cfs-external-metabox-slot-mounted');
                });
            }

            $(placeExternalMetaBoxes);
            $(window).on('load', placeExternalMetaBoxes);
        })(jQuery);
        <?php } ) ); ?>
    <?php
    }


    function pre_save( $value, $field = null ) {
        return [];
    }


    function pre_save_field( $field ) {
        $field['options']['meta_box_id'] = isset( $field['options']['meta_box_id'] ) ? trim( sanitize_text_field( (string) $field['options']['meta_box_id'] ) ) : '';
        $display_width = isset( $field['options']['display_width'] ) ? sanitize_key( (string) $field['options']['display_width'] ) : 'side';
        $field['options']['display_width'] = in_array( $display_width, [ 'side', '50', '75', '100' ], true ) ? $display_width : 'side';
        unset( $field['options']['required'] );

        return $field;
    }


    function should_skip_input_validation( $field ) {
        return true;
    }


    protected function get_meta_box_id( $field = null ) {
        return trim( sanitize_text_field( (string) $this->get_option( $field, 'meta_box_id', '' ) ) );
    }


    protected function get_display_width( $field = null ) {
        $display_width = sanitize_key( (string) $this->get_option( $field, 'display_width', 'side' ) );

        return in_array( $display_width, [ 'side', '50', '75', '100' ], true ) ? $display_width : 'side';
    }
}
