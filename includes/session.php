<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


class cfs_session
{
    public $session_id;
    public $session_data;
    public $expires = 14400; // 4 hours


    /**
     * Constructor
     */
    public function __construct() {
        $session_id = '';
        if ( isset( $_POST['cfs']['session_id'] ) ) {
            $session_id = sanitize_text_field( wp_unslash( $_POST['cfs']['session_id'] ) );
        }

        if ( $this->is_valid( $session_id ) ) {
            $this->session_id = $session_id;
        }
        else {
            $this->session_id = $this->generate_session_id();
        }
    }


    /**
     * Load the session (expired sessions return an empty array)
     * @return array
     */
    public function get() {
        global $wpdb;

        $now = time();
        $output = [];
        $session_data = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT data FROM {$wpdb->prefix}cfs_sessions WHERE id = %s AND expires > %d",
                $this->session_id,
                $now
            )
        );
        if ( ! empty( $session_data ) ) {
            $output = @unserialize( $session_data, [ 'allowed_classes' => false ] );
            $output = is_array( $output ) ? $output : [];
        }

        return $output;
    }


    /**
     * Update the session
     * @param array $session_data 
     */
    public function set( $session_data ) {
        global $wpdb;

        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$wpdb->prefix}cfs_sessions WHERE id = %s LIMIT 1",
                $this->session_id
            )
        );

        $wpdb->query(
            $wpdb->prepare(
                "INSERT INTO {$wpdb->prefix}cfs_sessions VALUES (%s, %s, %s)",
                $this->session_id, serialize( $session_data ), time() + $this->expires
            )
        );
    }


    /**
     * Remove expired sessions
     */
    public function cleanup() {
        global $wpdb;

        $now = time();
        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$wpdb->prefix}cfs_sessions WHERE expires <= %d",
                $now
            )
        );
    }


    /**
     * Validate the MD5 session hash
     * @param string $session_id 
     * @return boolean
     */
    public function is_valid( $session_id ) {
        return preg_match( "/^([a-f0-9]{32})$/", $session_id ) ? true : false;
    }


    private function generate_session_id() {
        if ( function_exists( 'random_bytes' ) ) {
            try {
                return bin2hex( random_bytes( 16 ) );
            }
            catch ( Exception $e ) {
                // Fall through to the compatibility fallback.
            }
        }

        return md5( wp_generate_uuid4() . microtime( true ) );
    }
}
