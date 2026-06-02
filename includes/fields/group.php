<?php

class cfs_group extends cfs_field
{

    function __construct() {
        $this->name = 'group';
        $this->label = __( 'Horizontal Group', 'cfs' );
    }


    function html( $field ) {
        global $post;

        $children = CFS()->api->get_input_fields( [
            'group_id' => $field->group_id,
            'parent_id' => $field->id,
        ] );

        if ( empty( $children ) ) {
            return;
        }

        $values = CFS()->api->get_fields( $post->ID, [ 'format' => 'input' ] );
        $columns = $this->get_columns( $this->get_option( $field, 'columns', 'auto' ) );
    ?>
        <div class="cfs-group-fields cfs-group-columns-<?php echo esc_attr( $columns ); ?>">
            <?php foreach ( $children as $child ) : ?>
                <?php
                if ( ! isset( CFS()->fields[ $child->type ] ) ) {
                    continue;
                }

                $args = [
                    'id' => $child->id,
                    'group_id' => $child->group_id,
                    'type' => $child->type,
                    'input_name' => "cfs[input][$child->id][value]",
                    'input_class' => $child->type,
                    'options' => $child->options,
                    'value' => isset( $values[ $child->id ] ) ? $values[ $child->id ] : $this->get_option( $child, 'default_value' ),
                    'notes' => $child->notes,
                ];
                ?>
                <div class="field field-<?php echo esc_attr( $child->name ); ?>" data-type="<?php echo esc_attr( $child->type ); ?>" data-name="<?php echo esc_attr( $child->name ); ?>">
                    <?php if ( ! empty( $child->label ) ) : ?>
                    <label><?php echo esc_html( $child->label ); ?></label>
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
                <label><?php _e( 'Columns', 'cfs' ); ?></label>
            </td>
            <td>
                <?php
                    CFS()->create_field( [
                        'type' => 'select',
                        'input_name' => 'cfs[fields][' . absint( $key ) . '][options][columns]',
                        'options' => [
                            'choices' => [
                                'auto' => __( 'Auto', 'cfs' ),
                                '2' => __( '2 columns', 'cfs' ),
                                '3' => __( '3 columns', 'cfs' ),
                                '4' => __( '4 columns', 'cfs' ),
                            ],
                            'force_single' => true,
                        ],
                        'value' => $this->get_option( $field, 'columns', 'auto' ),
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
}
