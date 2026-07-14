<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


class Atshift_CFS_accordion extends Atshift_CFS_field
{

    function __construct() {
        $this->name = 'accordion';
        $this->label = __( 'Accordion Group', 'atshift-fields-maintenance-for-custom-field-suite' );
    }


    function html( $field ) {
        global $post;

        $children = atshift_fields_maintenance_for_custom_field_suite()->api->get_input_fields( [
            'group_id' => $field->group_id,
            'parent_id' => $field->id,
        ] );
        $has_values = property_exists( $field, 'values' ) && is_array( $field->values );
        $values = $has_values ? $field->values : [];

        if ( ! $has_values && ! empty( $post->ID ) ) {
            $values = atshift_fields_maintenance_for_custom_field_suite()->api->get_fields( $post->ID, [ 'format' => 'input' ] );
        }

        $input_name_template = isset( $field->input_name_template ) ? (string) $field->input_name_template : 'cfs[input][%d][value]';
        $is_open = 0 < (int) $this->get_option( $field, 'open', 0 );
    ?>
        <div class="cfs-accordion<?php echo $is_open ? ' open' : ''; ?>">
            <button type="button" class="cfs-accordion-toggle" aria-expanded="<?php echo $is_open ? 'true' : 'false'; ?>">
                <span class="cfs-accordion-title"><?php echo esc_html( $field->label ); ?></span>
                <span class="cfs-accordion-icon" aria-hidden="true"></span>
            </button>
            <div class="cfs-accordion-body">
                <?php if ( ! empty( $field->notes ) ) : ?>
                <p class="cfs-accordion-notes"><?php echo esc_html( $field->notes ); ?></p>
                <?php endif; ?>

                <?php foreach ( $children as $child ) : ?>
                    <?php
                    if ( ! isset( atshift_fields_maintenance_for_custom_field_suite()->fields[ $child->type ] ) ) {
                        continue;
                    }

                    $args = [
                        'id' => $child->id,
                        'group_id' => $child->group_id,
                        'type' => $child->type,
                        'label' => $child->label,
                        'input_name' => sprintf( $input_name_template, $child->id ),
                        'input_class' => $child->type,
                        'input_name_template' => $input_name_template,
                        'options' => $child->options,
                        'value' => isset( $values[ $child->id ] ) ? $values[ $child->id ] : $this->get_option( $child, 'default_value' ),
                        'values' => $values,
                        'notes' => $child->notes,
                    ];
                    ?>
                    <div class="field field-<?php echo esc_attr( $child->name ); ?>" data-type="<?php echo esc_attr( $child->type ); ?>" data-name="<?php echo esc_attr( $child->name ); ?>">
                        <?php if ( ! in_array( $child->type, [ 'accordion', 'tab' ], true ) && ! empty( $child->label ) ) : ?>
                        <label><?php echo esc_html( $child->label ); ?><?php echo Atshift_CFS_field::is_required_field( $child ) ? wp_kses_post( Atshift_CFS_field::required_badge() ) : ''; ?></label>
                        <?php endif; ?>

                        <?php if ( 'accordion' !== $child->type && ! empty( $child->notes ) ) : ?>
                        <p class="notes"><?php echo esc_html( $child->notes ); ?></p>
                        <?php endif; ?>

                        <div class="cfs_<?php echo esc_attr( $child->type ); ?>">
                            <?php atshift_fields_maintenance_for_custom_field_suite()->create_field( $args ); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php
    }


    function options_html( $key, $field ) {
    ?>
        <tr class="field_option field_option_<?php echo esc_attr( $this->name ); ?>">
            <td class="label">
                <label><?php esc_html_e( 'Initial State', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></label>
            </td>
            <td>
                <?php
                    atshift_fields_maintenance_for_custom_field_suite()->create_field( [
                        'type' => 'true_false',
                        'input_name' => 'cfs[fields]['  . $this->normalize_admin_key( $key ) . '][options][open]',
                        'input_class' => 'true_false',
                        'value' => $this->get_option( $field, 'open', 0 ),
                        'options' => [ 'message' => __( 'Open by default', 'atshift-fields-maintenance-for-custom-field-suite' ) ],
                    ] );
                ?>
            </td>
        </tr>
    <?php
    }


    function input_head( $field = null ) {
    ?>
        <?php wp_add_inline_script( 'atshift-cfs-validation', atshift_cfs_capture_output( function() { ?>
        (function($) {
            $(document).on('click', '.cfs-accordion-toggle', function() {
                var $accordion = $(this).closest('.cfs-accordion');
                var isOpen = !$accordion.hasClass('open');

                $accordion.toggleClass('open', isOpen);
                $(this).attr('aria-expanded', isOpen ? 'true' : 'false');
            });
        })(jQuery);
        <?php } ) ); ?>
    <?php
    }
}
