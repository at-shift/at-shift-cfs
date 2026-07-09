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

<?php wp_add_inline_style( 'atshift-cfs-fields', atshift_cfs_capture_output( function() { ?>
.atshift-cfs-tools {
    max-width: 980px;
}

.atshift-cfs-tools .nav-tab-wrapper {
    border-bottom: 0;
    margin-top: 18px;
    padding: 0;
}

.atshift-cfs-tools .nav-tab {
    cursor: pointer;
    margin: 0 6px 0 0;
    padding: 9px 18px;
    border: 1px solid #c3c4c7;
    border-radius: 4px 4px 0 0;
    background: #f6f7f7;
    color: #50575e;
    font-size: 14px;
    font-weight: 600;
}

.atshift-cfs-tools .nav-tab.nav-tab-active {
    background: #fff;
    border-bottom-color: #fff;
    color: #1d2327;
}

.atshift-cfs-tools .content-container {
    background: #fff;
    border: 1px solid #c3c4c7;
    border-radius: 0 4px 4px 4px;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
    margin-top: -1px;
    padding: 24px;
}

.atshift-cfs-tools .tab-content {
    display: none;
}

.atshift-cfs-tools .tab-content.active {
    display: block;
}

.atshift-cfs-tools-description {
    max-width: 760px;
    margin: 0 0 18px;
    color: #50575e;
    font-size: 14px;
    line-height: 1.7;
}

.atshift-cfs-tools-grid {
    display: grid;
    grid-template-columns: minmax(260px, 320px) minmax(320px, 1fr);
    gap: 20px;
    align-items: start;
}

.atshift-cfs-tools-field label {
    display: block;
    margin: 0 0 7px;
    color: #1d2327;
    font-weight: 600;
}

.atshift-cfs-tools-field select,
.atshift-cfs-tools-field textarea {
    width: 100%;
    min-height: 150px;
    box-sizing: border-box;
    border-radius: 4px;
}

.atshift-cfs-tools-field textarea {
    font-family: Menlo, Consolas, Monaco, monospace;
}

.atshift-cfs-tools-actions {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-top: 12px;
}

.atshift-cfs-tools-reset {
    max-width: 760px;
}

.atshift-cfs-tools-reset h2 {
    margin-top: 0;
}

.atshift-cfs-tools-warning {
    margin: 0 0 16px;
    padding: 12px 14px;
    border-left: 4px solid #d63638;
    background: #fcf0f1;
    color: #1d2327;
    line-height: 1.7;
}

.atshift-cfs-tools-danger-button {
    border-color: #d63638 !important;
    color: #b32d2e !important;
}

@media screen and (max-width: 782px) {
    .atshift-cfs-tools .content-container {
        padding: 18px;
    }

    .atshift-cfs-tools-grid {
        grid-template-columns: 1fr;
    }
}
<?php } ) ); ?>

<?php wp_add_inline_script( 'atshift-cfs-fields', atshift_cfs_capture_output( function() { ?>
(function($) {
    $(function() {
        var $tools = $('.atshift-cfs-tools');
        var cfs_nonce = '<?php echo esc_js( wp_create_nonce( 'atshift_cfs_admin_nonce' ) ); ?>';
        var reset_confirm_message = <?php echo wp_json_encode( __( 'This will delete all atshift Fields data. Are you sure?', 'atshift-fields-maintenance-for-custom-field-suite' ) ); ?>;

        $tools.find('.nav-tab').click(function(e) {
            e.preventDefault();
            $tools.find('.tab-content').removeClass('active');
            $tools.find('.nav-tab').removeClass('nav-tab-active');
            $tools.find('.tab-content.' + $(this).attr('rel')).addClass('active');
            $(this).addClass('nav-tab-active');
        });

        $('#button-export').click(function() {
            var groups = $('#export-field-groups').val();
            if (null != groups) {
                $.post(ajaxurl, {
                    action: 'atshift_cfs_ajax_handler',
                    action_type: 'export',
                    nonce: cfs_nonce,
                    field_groups: $('#export-field-groups').val()
                },
                function(response) {
                    $('#export-output').text(response);
                    $('#export-area').show();
                });
            }
        });

        $('#button-import').click(function() {
            $.post(ajaxurl, {
                action: 'atshift_cfs_ajax_handler',
                action_type: 'import',
                nonce: cfs_nonce,
                import_code: $('#import-code').val()
            },
            function(response) {
                $('#import-message').html(response);
            });
        });

        $('#button-reset').click(function() {
            if (confirm(reset_confirm_message)) {
                $.post(ajaxurl, {
                    action: 'atshift_cfs_ajax_handler',
                    action_type: 'reset',
                    nonce: cfs_nonce
                },
                function(response) {
                    window.location.replace(response);
                });
            }
        });
    });
})(jQuery);
<?php } ) ); ?>

<div class="wrap atshift-cfs-tools">
    <h1><?php esc_html_e( 'atshift Fields Tools', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></h1>

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
