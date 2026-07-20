<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


class Atshift_CFS_group extends Atshift_CFS_field
{

    function __construct() {
        $this->name = 'group';
        $this->label = __( 'Horizontal Group', 'atshift-fields-maintenance-for-custom-field-suite' );
    }


    function html( $field ) {
        global $post;

        $children = atshift_fields_maintenance_for_custom_field_suite()->api->get_input_fields( [
            'group_id' => $field->group_id,
            'parent_id' => $field->id,
        ] );

        if ( 2 > count( $children ) ) {
            ?>
            <div class="cfs-group-warning">
                <?php esc_html_e( 'Add two or more fields to this horizontal group.', 'atshift-fields-maintenance-for-custom-field-suite' ); ?>
            </div>
            <?php
        }

        $has_values = property_exists( $field, 'values' ) && is_array( $field->values );
        $values = $has_values ? $field->values : [];

        if ( ! $has_values && ! empty( $post->ID ) ) {
            $values = atshift_fields_maintenance_for_custom_field_suite()->api->get_fields( $post->ID, [ 'format' => 'input' ] );
        }

        $input_name_template = isset( $field->input_name_template ) ? (string) $field->input_name_template : 'cfs[input][%d][value]';
        $columns = $this->get_columns( $this->get_option( $field, 'columns', 'auto' ) );
        $alignment = $this->get_alignment( $this->get_option( $field, 'alignment', 'stretch' ) );
    ?>
        <div class="cfs-group-fields cfs-group-columns-<?php echo esc_attr( $columns ); ?> cfs-group-align-<?php echo esc_attr( $alignment ); ?>">
            <?php foreach ( $children as $child ) : ?>
                <?php
                if ( ! isset( atshift_fields_maintenance_for_custom_field_suite()->fields[ $child->type ] ) ) {
                    continue;
                }

                if ( Atshift_CFS_field::should_hide_input_field( $child ) ) {
                    continue;
                }

                $args = [
                    'id' => $child->id,
                    'group_id' => $child->group_id,
                    'type' => $child->type,
                    'input_name' => sprintf( $input_name_template, $child->id ),
                    'input_class' => $child->type,
                    'options' => $child->options,
                    'value' => isset( $values[ $child->id ] ) ? $values[ $child->id ] : $this->get_option( $child, 'default_value' ),
                    'notes' => $child->notes,
                ];
                ?>
                <div class="field field-<?php echo esc_attr( $child->name ); ?>" data-type="<?php echo esc_attr( $child->type ); ?>" data-name="<?php echo esc_attr( $child->name ); ?>">
                    <?php if ( Atshift_CFS_field::should_render_field_label( $child ) ) : ?>
                    <label><?php echo wp_kses_post( Atshift_CFS_field::field_label_html( $child ) ); ?></label>
                    <?php endif; ?>

                    <?php if ( ! empty( $child->notes ) ) : ?>
                    <p class="notes"><?php echo esc_html( $child->notes ); ?></p>
                    <?php endif; ?>

                    <div class="cfs_<?php echo esc_attr( $child->type ); ?>">
                        <?php atshift_fields_maintenance_for_custom_field_suite()->create_field( $args ); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php
    }


    function options_html( $key, $field ) {
    ?>
        <tr class="field_option field_option_<?php echo esc_attr( $this->name ); ?>">
            <td class="label">
                <label><?php esc_html_e( 'Columns', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></label>
            </td>
            <td>
                <?php
                    atshift_fields_maintenance_for_custom_field_suite()->create_field( [
                        'type' => 'select',
                        'input_name' => 'cfs[fields]['  . $this->normalize_admin_key( $key ) . '][options][columns]',
                        'options' => [
                            'choices' => [
                                'none' => __( 'Not specified', 'atshift-fields-maintenance-for-custom-field-suite' ),
                                'auto' => __( 'Auto', 'atshift-fields-maintenance-for-custom-field-suite' ),
                                '2' => __( '2 columns', 'atshift-fields-maintenance-for-custom-field-suite' ),
                                '3' => __( '3 columns', 'atshift-fields-maintenance-for-custom-field-suite' ),
                                '4' => __( '4 columns', 'atshift-fields-maintenance-for-custom-field-suite' ),
                            ],
                            'force_single' => true,
                        ],
                        'value' => $this->get_option( $field, 'columns', 'auto' ),
                    ] );
                ?>
            </td>
        </tr>
        <tr class="field_option field_option_<?php echo esc_attr( $this->name ); ?>">
            <td class="label">
                <label><?php esc_html_e( 'Alignment', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></label>
            </td>
            <td>
                <?php
                    atshift_fields_maintenance_for_custom_field_suite()->create_field( [
                        'type' => 'select',
                        'input_name' => 'cfs[fields]['  . $this->normalize_admin_key( $key ) . '][options][alignment]',
                        'options' => [
                            'choices' => [
                                'stretch' => __( 'Evenly distributed', 'atshift-fields-maintenance-for-custom-field-suite' ),
                                'left' => __( 'Left aligned', 'atshift-fields-maintenance-for-custom-field-suite' ),
                            ],
                            'force_single' => true,
                        ],
                        'value' => $this->get_option( $field, 'alignment', 'stretch' ),
                    ] );
                ?>
            </td>
        </tr>
    <?php
    }


    private function get_columns( $value ) {
        $value = (string) $value;

        if ( 'none' === $value ) {
            return 'none';
        }

        return in_array( $value, [ '2', '3', '4' ], true ) ? $value : 'auto';
    }


    private function get_alignment( $value ) {
        return 'left' === $value ? 'left' : 'stretch';
    }
}
