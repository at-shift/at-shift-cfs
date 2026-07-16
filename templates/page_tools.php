<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


global $wpdb;

$results = $wpdb->get_results(
    $wpdb->prepare(
        "
SELECT ID, post_title
FROM $wpdb->posts
WHERE post_type = %s AND post_status = 'publish'
ORDER BY post_title",
        ATSHIFT_CFS_FIELD_GROUP_POST_TYPE
    )
);
?>

<div class="wrap atshift-cfs-tools">
    <h1><?php esc_html_e( 'atshift Fields Tool', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></h1>

    <h2 class="nav-tab-wrapper">
        <a class="nav-tab nav-tab-active" rel="export"><?php esc_html_e( 'Export', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></a>
        <a class="nav-tab" rel="import"><?php esc_html_e( 'Import', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></a>
        <a class="nav-tab" rel="reset"><?php esc_html_e( 'Reset', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></a>
    </h2>

    <div class="content-container">

        <!-- Export -->

        <div class="tab-content export active">
            <p class="atshift-cfs-tools-description"><?php esc_html_e( 'Select the field groups you want to export, press the button, then save the displayed code. You can select multiple items.', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></p>

            <div class="atshift-cfs-tools-grid">
                <div class="atshift-cfs-tools-field">
                    <label for="export-field-groups"><?php esc_html_e( 'Field Groups', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></label>
                    <select id="export-field-groups" multiple="multiple">
                        <?php foreach ($results as $result) : ?>
                        <option value="<?php echo absint( $result->ID ); ?>"><?php echo esc_html( $result->post_title ); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="atshift-cfs-tools-actions">
                        <input type="button" id="button-export" class="button button-primary" value="<?php esc_attr_e( 'Export', 'atshift-fields-maintenance-for-custom-field-suite' ); ?>" />
                    </div>
                </div>

                <div id="export-area" class="atshift-cfs-tools-field" style="display:none">
                    <label for="export-output"><?php esc_html_e( 'Export Code', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></label>
                    <textarea id="export-output" readonly="readonly"></textarea>
                </div>
            </div>
        </div>

        <!-- Import -->

        <div class="tab-content import">
            <p class="atshift-cfs-tools-description"><?php esc_html_e( 'Paste the exported field group code into the input field below, then press the button.', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></p>

            <div class="atshift-cfs-tools-field">
                <label for="import-code"><?php esc_html_e( 'Import Code', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></label>
                <textarea id="import-code" placeholder="<?php esc_attr_e( 'Paste the import code here', 'atshift-fields-maintenance-for-custom-field-suite' ); ?>"></textarea>
                <div class="atshift-cfs-tools-actions">
                    <input type="button" id="button-import" class="button button-primary" value="<?php esc_attr_e( 'Import', 'atshift-fields-maintenance-for-custom-field-suite' ); ?>" />
                </div>
            </div>
            <div id="import-message"></div>
        </div>

        <!-- Reset -->

        <div class="tab-content reset atshift-cfs-tools-reset">
            <h2><?php esc_html_e( 'Reset and deactivate.', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></h2>
            <p class="atshift-cfs-tools-warning"><?php esc_html_e( 'This will delete all atshift Fields data and deactivate the plugin.', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></p>
            <input type="button" id="button-reset" class="button atshift-cfs-tools-danger-button" value="<?php esc_attr_e( 'Delete everything', 'atshift-fields-maintenance-for-custom-field-suite' ); ?>" />
        </div>
    </div>
</div>
