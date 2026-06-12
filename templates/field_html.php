<?php
global $post;

$child_count = 0;
$structure_types = [ 'tab', 'loop', 'group', 'accordion' ];

if ( 'group' === $field->type && ! empty( $field->id ) ) {
    $child_fields = CFS()->api->get_input_fields( [
        'group_id'  => $post->ID,
        'parent_id' => $field->id,
    ] );
    $child_count = is_array( $child_fields ) ? count( $child_fields ) : 0;
}
?>
<div class="field">
    <div class="field_meta">
        <table class="widefat">
            <tr>
                <td class="field_order">

                </td>
                <td class="field_label">
                    <a class="cfs_edit_field row-title">
                        <?php if ( in_array( $field->type, $structure_types, true ) ) : ?>
                        <span class="cfs-structure-badge cfs-structure-badge-<?php echo esc_attr( $field->type ); ?>"><?php echo esc_html( strtoupper( $field->type ) ); ?></span>
                        <?php endif; ?>
                        <span class="cfs-field-label-text"><?php echo esc_html( $field->label ); ?></span>
                    </a>
                </td>
                <td class="field_name">
                    <?php echo esc_html( $field->name ); ?>
                </td>
                <td class="field_type">
                    <a class="cfs_edit_field"><?php echo esc_html( $field->type ); ?></a>
                </td>
            </tr>
            <?php if ( 'group' === $field->type && 2 > $child_count ) : ?>
            <tr class="field_warning">
                <td></td>
                <td colspan="3">
                    <?php esc_html_e( 'Add two or more fields to this horizontal group.', 'at-shift-cfs' ); ?>
                </td>
            </tr>
            <?php endif; ?>
        </table>
    </div>

    <div class="field_form">
        <table class="widefat">
            <tbody>
                <tr class="field_basics">
                    <td colspan="2">
                        <table>
                            <tr>
                                <td class="field_label">
                                    <label>
                                        <?php _e( 'Label', 'at-shift-cfs' ); ?>
                                        <div class="cfs_tooltip">?
                                            <div class="tooltip_inner"><?php _e( 'The field label that editors will see.', 'at-shift-cfs' ); ?></div>
                                        </div>
                                    </label>
                                    <input type="text" name="cfs[fields][<?php echo $field->weight; ?>][label]" value="<?php echo empty( $field->id ) ? '' : esc_attr( $field->label ); ?>" />
                                </td>
                                <td class="field_name">
                                    <label>
                                        <?php _e( 'Name', 'at-shift-cfs' ); ?>
                                        <div class="cfs_tooltip">?
                                            <div class="tooltip_inner">
                                                <?php _e( 'The field name is passed into get() to retrieve values. Use only lowercase letters, numbers, and underscores.', 'at-shift-cfs' ); ?>
                                            </div>
                                        </div>
                                    </label>
                                    <input type="text" name="cfs[fields][<?php echo $field->weight; ?>][name]" value="<?php echo empty( $field->id ) ? '' : esc_attr( $field->name ); ?>" />
                                </td>
                                <td class="field_type">
                                    <label><?php _e( 'Field Type', 'at-shift-cfs' ); ?></label>
                                    <select name="cfs[fields][<?php echo $field->weight; ?>][type]">
                                        <?php foreach ( CFS()->fields as $type ) : ?>
                                        <?php $selected = ($type->name == $field->type) ? ' selected' : ''; ?>
                                        <option value="<?php echo esc_attr( $type->name ); ?>"<?php echo $selected; ?>><?php echo esc_html( $type->label ); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <?php CFS()->fields[ $field->type ]->options_html( $field->weight, $field ); ?>

                <tr class="field_notes">
                    <td class="label">
                        <label>
                            <?php _e( 'Notes', 'at-shift-cfs' ); ?>
                            <div class="cfs_tooltip">?
                                <div class="tooltip_inner"><?php _e( 'Notes for editors during data entry', 'at-shift-cfs' ); ?></div>
                            </div>
                        </label>
                    </td>
                    <td>
                        <textarea name="cfs[fields][<?php echo $field->weight; ?>][notes]"><?php echo esc_textarea( $field->notes ); ?></textarea>
                    </td>
                </tr>
                <tr class="field_actions">
                    <td class="label"></td>
                    <td style="vertical-align:middle">
                        <input type="hidden" name="cfs[fields][<?php echo esc_attr( $field->weight ); ?>][id]" class="field_id" value="<?php echo absint( $field->id ); ?>" />
                        <input type="hidden" name="cfs[fields][<?php echo esc_attr( $field->weight ); ?>][key]" class="field_key" value="<?php echo absint( $field->weight ); ?>" />
                        <input type="hidden" name="cfs[fields][<?php echo esc_attr( $field->weight ); ?>][parent_id]" class="parent_id" value="<?php echo absint( $field->parent_id ); ?>" />
                        <input type="hidden" name="cfs[fields][<?php echo esc_attr( $field->weight ); ?>][parent_key]" class="parent_key" value="" />
                        <input type="hidden" name="cfs[fields][<?php echo esc_attr( $field->weight ); ?>][options][outside_tabs]" class="outside_tabs" value="<?php echo empty( $field->options['outside_tabs'] ) ? 0 : 1; ?>" />
                        <input type="button" value="<?php _e( 'Close', 'at-shift-cfs' ); ?>" class="button-secondary cfs_edit_field" />
                        &nbsp; -<?php _e( 'or', 'at-shift-cfs' ); ?>- &nbsp; <span class="cfs_delete_field"><?php _e( 'delete', 'at-shift-cfs' ); ?></span>
                        &nbsp; -<?php _e( 'or', 'at-shift-cfs' ); ?>- &nbsp; <input type="button" value="<?php esc_attr_e( 'Add new field below', 'at-shift-cfs' ); ?>" class="button-primary cfs_add_field_below" />
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
