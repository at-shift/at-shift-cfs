<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


class Atshift_CFS_upgrade
{

    public $version;
    public $last_version;


    public function __construct() {
        $this->version = ATSHIFT_CFS_VERSION;
        $this->last_version = get_option( ATSHIFT_CFS_VERSION_OPTION, get_option( ATSHIFT_CFS_LEGACY_VERSION_OPTION, '0' ) );

        if ( version_compare( $this->last_version, $this->version, '<' ) ) {
            if ( version_compare( $this->last_version, '1.0.0', '<' ) ) {
                require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
                $this->clean_install();
            }
            else {
                $this->run_upgrade();
            }

            $this->migrate_legacy_identifiers();
            update_option( ATSHIFT_CFS_VERSION_OPTION, $this->version );
        }
    }

    private function clean_install() {
        global $wpdb;

        $sql = "
        CREATE TABLE {$wpdb->prefix}cfs_values (
            id INT unsigned not null auto_increment,
            field_id INT unsigned,
            meta_id INT unsigned,
            post_id INT unsigned,
            base_field_id INT unsigned default 0,
            hierarchy TEXT,
            depth INT unsigned default 0,
            weight INT unsigned default 0,
            sub_weight INT unsigned default 0,
            PRIMARY KEY (id),
            INDEX field_id_idx (field_id),
            INDEX post_id_idx (post_id)
        ) DEFAULT CHARSET=utf8";
        dbDelta( $sql );

        $sql = "
        CREATE TABLE {$wpdb->prefix}cfs_sessions (
            id VARCHAR(32),
            data TEXT,
            expires VARCHAR(10),
            PRIMARY KEY (id)
        ) DEFAULT CHARSET=utf8";
        dbDelta( $sql );

        // Set the field counter
        update_option( ATSHIFT_CFS_NEXT_FIELD_ID_OPTION, 1 );
    }

    private function run_upgrade() {
        $this->migrate_legacy_identifiers();
    }

    private function migrate_legacy_identifiers() {
        global $wpdb;

        $next_field_id = get_option( ATSHIFT_CFS_NEXT_FIELD_ID_OPTION, null );
        if ( null === $next_field_id ) {
            $legacy_next_field_id = get_option( ATSHIFT_CFS_LEGACY_NEXT_FIELD_ID_OPTION, null );
            if ( null !== $legacy_next_field_id ) {
                update_option( ATSHIFT_CFS_NEXT_FIELD_ID_OPTION, (int) $legacy_next_field_id );
            }
        }

        $wpdb->update(
            $wpdb->posts,
            [ 'post_type' => ATSHIFT_CFS_FIELD_GROUP_POST_TYPE ],
            [ 'post_type' => ATSHIFT_CFS_LEGACY_FIELD_GROUP_POST_TYPE ],
            [ '%s' ],
            [ '%s' ]
        );
    }
}

new Atshift_CFS_upgrade();
