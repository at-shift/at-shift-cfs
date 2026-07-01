<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<input type="hidden" name="cfs[save]" value="<?php echo esc_attr( wp_create_nonce( 'cfs_save_fields' ) ); ?>" />

<ul class="fields">
<?php

global $post;

$results = CFS()->api->get_input_fields( [ 'group_id' => $post->ID ] );

/*---------------------------------------------------------------------------------------------
    Create <ul> based on field structure
---------------------------------------------------------------------------------------------*/

$fields_by_parent = [];

foreach ( $results as $field ) {
    if ( ! isset( CFS()->fields[ $field->type ] ) ) {
        continue;
    }

    $parent_id = (int) $field->parent_id;
    $fields_by_parent[ $parent_id ][] = $field;
}

$render_fields = function( $parent_id ) use ( &$render_fields, $fields_by_parent ) {
    $parent_id = (int) $parent_id;

    if ( empty( $fields_by_parent[ $parent_id ] ) ) {
        return;
    }

    foreach ( $fields_by_parent[ $parent_id ] as $field ) {
        $classes = [];

        if ( in_array( $field->type, [ 'loop', 'group', 'accordion', 'conditional' ], true ) ) {
            $classes[] = 'loop';
        }

        if ( in_array( $field->type, [ 'tab', 'loop', 'group', 'accordion', 'conditional' ], true ) ) {
            $classes[] = 'cfs-structure-' . $field->type;
        }

        echo '<li' . ( empty( $classes ) ? '' : ' class="' . esc_attr( implode( ' ', $classes ) ) . '"' ) . '>';

        CFS()->field_html( $field );

        if ( ! empty( $fields_by_parent[ (int) $field->id ] ) ) {
            echo '<ul>';
            $render_fields( $field->id );
            echo '</ul>';
        }

        echo '</li>';
    }
};

$render_fields( 0 );

?>
</ul>

<div class="table_footer">
    <input type="button" class="button-primary cfs_add_field" value="<?php esc_attr_e( 'Add New Field', 'at-shift-cfs' ); ?>" />
</div>
