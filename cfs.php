<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/*
Plugin Name: atshift Fields (Maintenance for Custom Field Suite)
Description: This plugin is a maintenance build of Custom Field Suite that lets you visually add custom fields to your WordPress edit pages.
Version: 2.6.7.42.1.6
Author: Matt Gibbs / Maintenance: @shift Yoshiya Tsuchisaka
Author URI: https://at-shift.net
Text Domain: atshift-fields-maintenance-for-custom-field-suite
License: GPLv2
License URI: https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/

if ( class_exists( 'Atshift_Fields_Maintenance_For_Custom_Field_Suite', false ) ) {
    return;
}


if ( ! class_exists( 'Atshift_Fields_Maintenance_For_Custom_Field_Suite', false ) ) {
class Atshift_Fields_Maintenance_For_Custom_Field_Suite
{

    public $api;
    public $form;
    public $fields;
    public $field_group;
    public $group_ids = [];
    public $validators = [];
    private static $instance;


    function __construct() {

        self::$instance = $this;

        // setup variables
        define( 'ATSHIFT_CFS_VERSION', '2.6.7.42.1.6' );
        define( 'ATSHIFT_CFS_DIR', dirname( __FILE__ ) );
        define( 'ATSHIFT_CFS_URL', plugins_url( '', __FILE__ ) );
        define( 'ATSHIFT_CFS_FIELD_GROUP_POST_TYPE', 'atshift_cfs' );
        define( 'ATSHIFT_CFS_LEGACY_FIELD_GROUP_POST_TYPE', 'cfs' );
        define( 'ATSHIFT_CFS_VERSION_OPTION', 'atshift_cfs_version' );
        define( 'ATSHIFT_CFS_LEGACY_VERSION_OPTION', 'cfs_version' );
        define( 'ATSHIFT_CFS_NEXT_FIELD_ID_OPTION', 'atshift_cfs_next_field_id' );
        define( 'ATSHIFT_CFS_LEGACY_NEXT_FIELD_ID_OPTION', 'cfs_next_field_id' );
        define( 'ATSHIFT_CFS_BLOCK_CATEGORY', 'atshift-cfs' );
        define( 'ATSHIFT_CFS_BLOCK_NAMESPACE', 'atshift-cfs' );

        // get the gears turning
        include( ATSHIFT_CFS_DIR . '/includes/init.php' );
    }


    /**
     * Singleton
     */
    public static function instance() {
        if ( ! isset( self::$instance ) ) {
            self::$instance = new self;
        }
        return self::$instance;
    }


    /**
     * Public API methods
     */
    function get( $field_name = false, $post_id = false, $options = [] ) {
        return atshift_fields_maintenance_for_custom_field_suite()->api->get( $field_name, $post_id, $options );
    }


    function get_field_info( $field_name = false, $post_id = false ) {
        return atshift_fields_maintenance_for_custom_field_suite()->api->get_field_info( $field_name, $post_id );
    }


    function get_reverse_related( $post_id, $options = [] ) {
        return atshift_fields_maintenance_for_custom_field_suite()->api->get_reverse_related( $post_id, $options );
    }


    function save( $field_data = [], $post_data = [], $options = [] ) {
        return atshift_fields_maintenance_for_custom_field_suite()->api->save_fields( $field_data, $post_data, $options );
    }


    function find_fields( $params = [] ) {
        return atshift_fields_maintenance_for_custom_field_suite()->api->find_input_fields( $params );
    }


    function form( $params = [] ) {
        return atshift_cfs_capture_output( function() use ( $params ) {
            atshift_fields_maintenance_for_custom_field_suite()->form->render( $params );
        } );
    }


    /**
     * Render a field's admin settings HTML
     */
    function field_html( $field ) {
        include( ATSHIFT_CFS_DIR . '/templates/field_html.php' );
    }


    /**
     * Trigger the field type "html" method
     */
    function create_field( $field ) {
        $defaults = [
            'type'          => 'text',
            'input_name'    => '',
            'input_class'   => '',
            'options'       => [],
            'value'         => '',
        ];

        $field = (object) array_merge( $defaults, (array) $field );
        atshift_fields_maintenance_for_custom_field_suite()->fields[ $field->type ]->html( $field );
    }
}
}


if ( ! class_exists( 'Custom_Field_Suite', false ) ) {
    class_alias( 'Atshift_Fields_Maintenance_For_Custom_Field_Suite', 'Custom_Field_Suite' );
}


function atshift_fields_maintenance_for_custom_field_suite() {
    return Atshift_Fields_Maintenance_For_Custom_Field_Suite::instance();
}


function atshift_cfs_capture_output( $callback ) {
    ob_start();

    try {
        $callback();
        return ob_get_clean();
    } catch ( Throwable $e ) {
        ob_end_clean();
        throw $e;
    }
}


if ( function_exists( 'CFS' ) ) {
    function atshift_fields_maintenance_for_custom_field_suite_conflict_notice() {
        if ( ! current_user_can( 'activate_plugins' ) ) {
            return;
        }

        echo '<div class="notice notice-error"><p>';
        echo esc_html__( 'atshift Fields (Maintenance for Custom Field Suite) was not loaded because another Custom Field Suite plugin is already active. Deactivate the other Custom Field Suite plugin before activating this maintenance build.', 'atshift-fields-maintenance-for-custom-field-suite' );
        echo '</p></div>';
    }

    add_action( 'admin_notices', 'atshift_fields_maintenance_for_custom_field_suite_conflict_notice' );
    return;
}


if ( ! function_exists( 'CFS' ) ) {
    function CFS() {
        return atshift_fields_maintenance_for_custom_field_suite();
    }
}


$cfs = atshift_fields_maintenance_for_custom_field_suite();
