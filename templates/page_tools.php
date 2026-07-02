<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


global $wpdb;

$sql = "
SELECT ID, post_title
FROM $wpdb->posts
WHERE post_type = 'cfs' AND post_status = 'publish'
ORDER BY post_title";
// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Query contains only WordPress table names and fixed post status/type values.
$results = $wpdb->get_results( $sql );
?>

<?php ob_start(); ?>
.cfs-tools {
    max-width: 980px;
}

.cfs-tools .nav-tab-wrapper {
    border-bottom: 0;
    margin-top: 18px;
    padding: 0;
}

.cfs-tools .nav-tab {
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

.cfs-tools .nav-tab.nav-tab-active {
    background: #fff;
    border-bottom-color: #fff;
    color: #1d2327;
}

.cfs-tools .content-container {
    background: #fff;
    border: 1px solid #c3c4c7;
    border-radius: 0 4px 4px 4px;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
    margin-top: -1px;
    padding: 24px;
}

.cfs-tools .tab-content {
    display: none;
}

.cfs-tools .tab-content.active {
    display: block;
}

.cfs-tools-description {
    max-width: 760px;
    margin: 0 0 18px;
    color: #50575e;
    font-size: 14px;
    line-height: 1.7;
}

.cfs-tools-grid {
    display: grid;
    grid-template-columns: minmax(260px, 320px) minmax(320px, 1fr);
    gap: 20px;
    align-items: start;
}

.cfs-tools-field label {
    display: block;
    margin: 0 0 7px;
    color: #1d2327;
    font-weight: 600;
}

.cfs-tools-field select,
.cfs-tools-field textarea {
    width: 100%;
    min-height: 150px;
    box-sizing: border-box;
    border-radius: 4px;
}

.cfs-tools-field textarea {
    font-family: Menlo, Consolas, Monaco, monospace;
}

.cfs-tools-actions {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-top: 12px;
}

.cfs-tools-reset {
    max-width: 760px;
}

.cfs-tools-reset h2 {
    margin-top: 0;
}

.cfs-tools-warning {
    margin: 0 0 16px;
    padding: 12px 14px;
    border-left: 4px solid #d63638;
    background: #fcf0f1;
    color: #1d2327;
    line-height: 1.7;
}

.cfs-tools-danger-button {
    border-color: #d63638 !important;
    color: #b32d2e !important;
}

@media screen and (max-width: 782px) {
    .cfs-tools .content-container {
        padding: 18px;
    }

    .cfs-tools-grid {
        grid-template-columns: 1fr;
    }
}
<?php wp_add_inline_style( 'cfs-fields', ob_get_clean() ); ?>

<?php ob_start(); ?>
(function($) {
    $(function() {
        var $tools = $('.cfs-tools');
        var cfs_nonce = '<?php echo esc_js( wp_create_nonce( 'cfs_admin_nonce' ) ); ?>';
        var reset_confirm_message = <?php echo wp_json_encode( __( 'This will delete all atshift Fields Maintenance for Custom Field Suite data. Are you sure?', 'at-shift-cfs' ) ); ?>;

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
                    action: 'cfs_ajax_handler',
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
                action: 'cfs_ajax_handler',
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
                    action: 'cfs_ajax_handler',
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
<?php wp_add_inline_script( 'cfs-fields', ob_get_clean() ); ?>

<div class="wrap cfs-tools">
    <h1><?php esc_html_e( 'atshift Fields Maintenance for Custom Field Suite Tools', 'at-shift-cfs' ); ?></h1>

    <h2 class="nav-tab-wrapper">
        <a class="nav-tab nav-tab-active" rel="export"><?php esc_html_e( 'Export', 'at-shift-cfs' ); ?></a>
        <a class="nav-tab" rel="import"><?php esc_html_e( 'Import', 'at-shift-cfs' ); ?></a>
        <a class="nav-tab" rel="reset"><?php esc_html_e( 'Reset', 'at-shift-cfs' ); ?></a>
    </h2>

    <div class="content-container">

        <!-- Export -->

        <div class="tab-content export active">
            <p class="cfs-tools-description"><?php esc_html_e( 'Select the field groups you want to export, press the button, then save the displayed code. You can select multiple items.', 'at-shift-cfs' ); ?></p>

            <div class="cfs-tools-grid">
                <div class="cfs-tools-field">
                    <label for="export-field-groups"><?php esc_html_e( 'Field Groups', 'at-shift-cfs' ); ?></label>
                    <select id="export-field-groups" multiple="multiple">
                        <?php foreach ($results as $result) : ?>
                        <option value="<?php echo absint( $result->ID ); ?>"><?php echo esc_html( $result->post_title ); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="cfs-tools-actions">
                        <input type="button" id="button-export" class="button button-primary" value="<?php esc_attr_e( 'Export', 'at-shift-cfs' ); ?>" />
                    </div>
                </div>

                <div id="export-area" class="cfs-tools-field" style="display:none">
                    <label for="export-output"><?php esc_html_e( 'Export Code', 'at-shift-cfs' ); ?></label>
                    <textarea id="export-output" readonly="readonly"></textarea>
                </div>
            </div>
        </div>

        <!-- Import -->

        <div class="tab-content import">
            <p class="cfs-tools-description"><?php esc_html_e( 'Paste the exported field group code into the input field below, then press the button.', 'at-shift-cfs' ); ?></p>

            <div class="cfs-tools-field">
                <label for="import-code"><?php esc_html_e( 'Import Code', 'at-shift-cfs' ); ?></label>
                <textarea id="import-code" placeholder="<?php esc_attr_e( 'Paste the import code here', 'at-shift-cfs' ); ?>"></textarea>
                <div class="cfs-tools-actions">
                    <input type="button" id="button-import" class="button button-primary" value="<?php esc_attr_e( 'Import', 'at-shift-cfs' ); ?>" />
                </div>
            </div>
            <div id="import-message"></div>
        </div>

        <!-- Reset -->

        <div class="tab-content reset cfs-tools-reset">
            <h2><?php esc_html_e( 'Reset and deactivate.', 'at-shift-cfs' ); ?></h2>
            <p class="cfs-tools-warning"><?php esc_html_e( 'This will delete all atshift Fields Maintenance for Custom Field Suite data and deactivate the plugin.', 'at-shift-cfs' ); ?></p>
            <input type="button" id="button-reset" class="button cfs-tools-danger-button" value="<?php esc_attr_e( 'Delete everything', 'at-shift-cfs' ); ?>" />
        </div>
    </div>
</div>
