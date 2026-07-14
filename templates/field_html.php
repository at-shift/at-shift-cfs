<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

global $post;

$child_count = 0;
$structure_types = [ 'tab', 'loop', 'group', 'accordion', 'conditional' ];
$generated_name_types = [
    'tab',
    'group',
    'accordion',
    'conditional',
    'post_title',
    'post_content',
    'post_publish',
    'wp_category',
    'wp_tag',
    'featured_image',
];
$uses_generated_name = in_array( $field->type, $generated_name_types, true );
$field_name_value = empty( $field->id ) ? '' : (string) $field->name;
$field_name_display = '' === $field_name_value ? __( 'Automatically named when saved', 'atshift-fields-maintenance-for-custom-field-suite' ) : $field_name_value;
$field_name_input_value = $uses_generated_name ? '' : $field_name_value;
$field_weight = 'clone' === (string) $field->weight ? 'clone' : (string) absint( $field->weight );
$field_weight_attr = esc_attr( $field_weight );
$field_key_value = 'clone' === $field_weight ? 0 : absint( $field->weight );
$structure_badges = [
    'tab'         => __( 'TAB', 'atshift-fields-maintenance-for-custom-field-suite' ),
    'loop'        => __( 'LOOP', 'atshift-fields-maintenance-for-custom-field-suite' ),
    'group'       => __( 'GROUP', 'atshift-fields-maintenance-for-custom-field-suite' ),
    'accordion'   => __( 'ACCORDION', 'atshift-fields-maintenance-for-custom-field-suite' ),
    'conditional' => __( 'CONDITION', 'atshift-fields-maintenance-for-custom-field-suite' ),
];
$structure_badge = isset( $structure_badges[ $field->type ] ) ? $structure_badges[ $field->type ] : strtoupper( $field->type );

if ( 'group' === $field->type && ! empty( $field->id ) ) {
    $child_fields = atshift_fields_maintenance_for_custom_field_suite()->api->get_input_fields( [
        'group_id'  => $post->ID,
        'parent_id' => $field->id,
    ] );
    $child_count = is_array( $child_fields ) ? count( $child_fields ) : 0;
}
?>
<div class="field<?php echo $uses_generated_name ? ' cfs-field-generated-name' : ''; ?>">
    <div class="field_meta">
        <table class="widefat">
            <tr>
                <td class="field_order">

                </td>
                <td class="field_label">
                    <a class="cfs_edit_field row-title">
                        <?php if ( in_array( $field->type, $structure_types, true ) ) : ?>
                        <span class="cfs-structure-badge cfs-structure-badge-<?php echo esc_attr( $field->type ); ?>"><?php echo esc_html( $structure_badge ); ?></span>
                        <?php endif; ?>
                        <span class="cfs-field-label-text"><?php echo esc_html( $field->label ); ?></span>
                    </a>
                </td>
                <td class="field_name">
                    <?php echo esc_html( $uses_generated_name ? $field_name_display : $field->name ); ?>
                </td>
                <td class="field_type">
                    <a class="cfs_edit_field"><?php echo esc_html( $field->type ); ?></a>
                </td>
            </tr>
            <?php if ( 'group' === $field->type && 2 > $child_count ) : ?>
            <tr class="field_warning">
                <td></td>
                <td colspan="3">
                    <?php esc_html_e( 'Add two or more fields to this horizontal group.', 'atshift-fields-maintenance-for-custom-field-suite' ); ?>
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
                                        <?php esc_html_e( 'Label', 'atshift-fields-maintenance-for-custom-field-suite' ); ?>
                                        <div class="cfs_tooltip">?
                                            <div class="tooltip_inner"><?php esc_html_e( 'The field label that editors will see.', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></div>
                                        </div>
                                    </label>
                                    <input type="text" name="cfs[fields][<?php echo $field_weight_attr; ?>][label]" value="<?php echo empty( $field->id ) ? '' : esc_attr( $field->label ); ?>" />
                                </td>
                                <td class="field_name">
                                    <label>
                                        <?php esc_html_e( 'Name', 'atshift-fields-maintenance-for-custom-field-suite' ); ?>
                                        <div class="cfs_tooltip">?
                                            <div class="tooltip_inner">
                                                <?php esc_html_e( 'The field name is passed into get() to retrieve values. Use only lowercase letters, numbers, and underscores.', 'atshift-fields-maintenance-for-custom-field-suite' ); ?>
                                            </div>
                                        </div>
                                    </label>
                                    <input
                                        type="text"
                                        class="cfs-editable-field-name"
                                        name="cfs[fields][<?php echo $field_weight_attr; ?>][name]"
                                        value="<?php echo esc_attr( $field_name_input_value ); ?>"
                                        <?php disabled( $uses_generated_name ); ?>
                                        <?php echo $uses_generated_name ? 'style="display:none;"' : ''; ?>
                                    />
                                    <input
                                        type="hidden"
                                        class="cfs-generated-field-name-input"
                                        name="cfs[fields][<?php echo $field_weight_attr; ?>][name]"
                                        value="<?php echo esc_attr( $field_name_input_value ); ?>"
                                        <?php disabled( ! $uses_generated_name ); ?>
                                    />
                                    <span class="cfs-generated-field-name-display" data-current-name="<?php echo esc_attr( $field_name_value ); ?>" <?php echo $uses_generated_name ? '' : 'hidden'; ?>>
                                        <?php echo esc_html( $field_name_display ); ?>
                                    </span>
                                </td>
                                <td class="field_type">
                                    <label><?php esc_html_e( 'Field Type', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></label>
                                    <select name="cfs[fields][<?php echo $field_weight_attr; ?>][type]">
                                        <?php foreach ( atshift_fields_maintenance_for_custom_field_suite()->fields as $type ) : ?>
                                        <option value="<?php echo esc_attr( $type->name ); ?>"<?php selected( $type->name, $field->type ); ?>><?php echo esc_html( $type->label ); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <?php atshift_fields_maintenance_for_custom_field_suite()->fields[ $field->type ]->options_html( $field->weight, $field ); ?>

                <tr class="field_conditional_value">
                    <td class="label">
                        <label><?php esc_html_e( 'Display for choice', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></label>
                        <p class="description"><?php esc_html_e( 'Choose the parent Conditional Group value that displays this field.', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></p>
                    </td>
                    <td>
                        <select name="cfs[fields][<?php echo $field_weight_attr; ?>][options][conditional_value]" class="cfs-conditional-value">
                            <?php if ( ! empty( $field->options['conditional_value'] ) ) : ?>
                            <option value="<?php echo esc_attr( $field->options['conditional_value'] ); ?>" selected><?php echo esc_html( $field->options['conditional_value'] ); ?></option>
                            <?php endif; ?>
                        </select>
                    </td>
                </tr>
                <tr class="field_conditional_separator">
                    <td colspan="2"><hr /></td>
                </tr>

                <tr class="field_notes">
                    <td class="label">
                        <label>
                            <?php esc_html_e( 'Notes', 'atshift-fields-maintenance-for-custom-field-suite' ); ?>
                            <div class="cfs_tooltip">?
                                <div class="tooltip_inner"><?php esc_html_e( 'Notes for editors during data entry', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></div>
                            </div>
                        </label>
                    </td>
                    <td>
                        <textarea name="cfs[fields][<?php echo $field_weight_attr; ?>][notes]"><?php echo esc_textarea( $field->notes ); ?></textarea>
                    </td>
                </tr>
                <tr class="field_actions">
                    <td class="label"></td>
                    <td style="vertical-align:middle">
                        <input type="hidden" name="cfs[fields][<?php echo $field_weight_attr; ?>][id]" class="field_id" value="<?php echo absint( $field->id ); ?>" />
                        <input type="hidden" name="cfs[fields][<?php echo $field_weight_attr; ?>][key]" class="field_key" value="<?php echo esc_attr( $field_key_value ); ?>" />
                        <input type="hidden" name="cfs[fields][<?php echo $field_weight_attr; ?>][parent_id]" class="parent_id" value="<?php echo absint( $field->parent_id ); ?>" />
                        <input type="hidden" name="cfs[fields][<?php echo $field_weight_attr; ?>][parent_key]" class="parent_key" value="" />
                        <input type="hidden" name="cfs[fields][<?php echo $field_weight_attr; ?>][options][outside_tabs]" class="outside_tabs" value="<?php echo empty( $field->options['outside_tabs'] ) ? 0 : 1; ?>" />
                        <div class="cfs-field-actions">
                            <input type="button" value="<?php esc_attr_e( 'Close', 'atshift-fields-maintenance-for-custom-field-suite' ); ?>" class="button-secondary cfs_edit_field" />
                            <input type="button" value="<?php esc_attr_e( 'Add new field below', 'atshift-fields-maintenance-for-custom-field-suite' ); ?>" class="button-primary cfs_add_field_below" />
                            <div class="cfs-field-action-menu">
                                <button type="button" class="button-secondary cfs_field_actions_toggle" aria-haspopup="true" aria-expanded="false">
                                    <?php esc_html_e( 'Actions', 'atshift-fields-maintenance-for-custom-field-suite' ); ?>
                                    <span class="dashicons dashicons-arrow-down-alt2" aria-hidden="true"></span>
                                </button>
                                <ul class="cfs-field-action-menu-list" hidden>
                                    <li>
                                        <button type="button" class="button-link cfs_duplicate_field">
                                            <?php esc_html_e( 'Duplicate', 'atshift-fields-maintenance-for-custom-field-suite' ); ?>
                                        </button>
                                    </li>
                                    <li>
                                        <button type="button" class="button-link cfs_delete_field">
                                            <?php esc_html_e( 'delete', 'atshift-fields-maintenance-for-custom-field-suite' ); ?>
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
