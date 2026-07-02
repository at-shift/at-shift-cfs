<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


global $wpdb;

// Post types
$post_types = [];
$types = get_post_types( [ 'public' => true ] );

foreach ( $types as $post_type ) {
    if ( ! in_array( $post_type, [ 'cfs', 'attachment' ] ) ) {
        $post_types[] = $post_type;
    }
}

$extras = (array) get_post_meta( $post->ID, 'cfs_extras', true );

if ( ! isset( $extras['hide_editor'] ) ) {
    $extras['hide_editor'] = '';
}
if ( ! isset( $extras['order'] ) ) {
    $extras['order'] = 0;
}
if ( ! isset( $extras['context'] ) ) {
    $extras['context'] = 'normal';
}

?>

<table>
    <tr>
        <td class="label">
            <label>
                <?php esc_html_e( 'Order', 'atshift-fields-maintenance-for-custom-field-suite' ); ?>
                <div class="cfs_tooltip">?
                    <div class="tooltip_inner"><?php esc_html_e( 'The field group with the lowest order will appear first.', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></div>
                </div>
            </label>
        </td>
        <td style="vertical-align:top">
            <input type="text" name="cfs[extras][order]" value="<?php echo esc_attr( $extras['order'] ); ?>" style="width:80px" />
        </td>
    </tr>
    <tr>
        <td class="label">
            <label><?php esc_html_e( 'Position', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></label>
        </td>
        <td style="vertical-align:top">
            <input type="radio" name="cfs[extras][context]" value="normal"<?php echo ( $extras['context'] == 'normal' ) ? ' checked' : ''; ?> /> <?php esc_html_e( 'Normal', 'atshift-fields-maintenance-for-custom-field-suite' ); ?> &nbsp; &nbsp;
            <input type="radio" name="cfs[extras][context]" value="side"<?php echo ( $extras['context'] == 'side' ) ? ' checked' : ''; ?> /> <?php esc_html_e( 'Side', 'atshift-fields-maintenance-for-custom-field-suite' ); ?>
        </td>
    </tr>
    <tr>
        <td class="label">
            <label><?php esc_html_e( 'Display Settings', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></label>
        </td>
        <td style="vertical-align:top">
            <div>
                <?php
                    atshift_fields_maintenance_for_custom_field_suite()->create_field( [
                        'type'          => 'true_false',
                        'input_name'    => "cfs[extras][hide_editor]",
                        'input_class'   => 'true_false',
                        'value'         => $extras['hide_editor'],
                        'options'       => [ 'message' => __( 'Hide the content editor (does not apply to the Gutenberg / block editor content area)', 'atshift-fields-maintenance-for-custom-field-suite' ) ],
                    ] );
                ?>
            </div>
        </td>
    </tr>

</table>
