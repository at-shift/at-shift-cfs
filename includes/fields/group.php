<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


class cfs_group extends cfs_field
{

    function __construct() {
        $this->name = 'group';
        $this->label = __( 'Horizontal Group', 'at-shift-cfs' );
    }


    function html( $field ) {
        global $post;

        $children = CFS()->api->get_input_fields( [
            'group_id' => $field->group_id,
            'parent_id' => $field->id,
        ] );

        if ( 2 > count( $children ) ) {
            ?>
            <div class="cfs-group-warning">
                <?php esc_html_e( 'Add two or more fields to this horizontal group.', 'at-shift-cfs' ); ?>
            </div>
            <?php
        }

        $has_values = property_exists( $field, 'values' ) && is_array( $field->values );
        $values = $has_values ? $field->values : [];

        if ( ! $has_values && ! empty( $post->ID ) ) {
            $values = CFS()->api->get_fields( $post->ID, [ 'format' => 'input' ] );
        }

        $input_name_template = isset( $field->input_name_template ) ? (string) $field->input_name_template : 'cfs[input][%d][value]';
        $columns = $this->get_columns( $this->get_option( $field, 'columns', 'auto' ) );
        $alignment = $this->get_alignment( $this->get_option( $field, 'alignment', 'stretch' ) );
    ?>
        <div class="cfs-group-fields cfs-group-columns-<?php echo esc_attr( $columns ); ?> cfs-group-align-<?php echo esc_attr( $alignment ); ?>">
            <?php foreach ( $children as $child ) : ?>
                <?php
                if ( ! isset( CFS()->fields[ $child->type ] ) ) {
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
                    <?php if ( ! empty( $child->label ) ) : ?>
                    <label><?php echo esc_html( $child->label ); ?><?php echo cfs_field::is_required_field( $child ) ? wp_kses_post( cfs_field::required_badge() ) : ''; ?></label>
                    <?php endif; ?>

                    <?php if ( ! empty( $child->notes ) ) : ?>
                    <p class="notes"><?php echo esc_html( $child->notes ); ?></p>
                    <?php endif; ?>

                    <div class="cfs_<?php echo esc_attr( $child->type ); ?>">
                        <?php CFS()->create_field( $args ); ?>
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
                <label><?php esc_html_e( 'Columns', 'at-shift-cfs' ); ?></label>
            </td>
            <td>
                <?php
                    CFS()->create_field( [
                        'type' => 'select',
                        'input_name' => 'cfs[fields][' . absint( $key ) . '][options][columns]',
                        'options' => [
                            'choices' => [
                                'auto' => __( 'Auto', 'at-shift-cfs' ),
                                '2' => __( '2 columns', 'at-shift-cfs' ),
                                '3' => __( '3 columns', 'at-shift-cfs' ),
                                '4' => __( '4 columns', 'at-shift-cfs' ),
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
                <label><?php esc_html_e( 'Alignment', 'at-shift-cfs' ); ?></label>
            </td>
            <td>
                <?php
                    CFS()->create_field( [
                        'type' => 'select',
                        'input_name' => 'cfs[fields][' . absint( $key ) . '][options][alignment]',
                        'options' => [
                            'choices' => [
                                'stretch' => __( 'Evenly distributed', 'at-shift-cfs' ),
                                'left' => __( 'Left aligned', 'at-shift-cfs' ),
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

        return in_array( $value, [ '2', '3', '4' ], true ) ? $value : 'auto';
    }


    private function get_alignment( $value ) {
        return 'left' === $value ? 'left' : 'stretch';
    }
}
