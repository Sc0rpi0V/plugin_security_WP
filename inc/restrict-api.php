<?php

/**
 * CLASS RestrictApi
 * Restrict access to the wp rest api
 */
class RestrictApi {

    /**
     * Restrict API  access to logout visitors(!is_user_logged_in()) and allow only administrator.
     */
    public static function firstLevelAccess() {

        add_filter( 'rest_authentication_errors', function ( $result ) {
            if ( ! empty( $result ) ) {
                return $result;
            }
            if ( ! is_user_logged_in() ) {
                return new \WP_Error( 'rest_not_logged_in', 'You are not currently logged in.', array('status' => 401 ) );
            }
            if ( ! current_user_can( 'activate_plugins' ) && ! current_user_can( 'update_core' ) ) {
                return new \WP_Error( 'rest_cannot_access', 'You do not have access rights.', array('status' => 401 ) );
            }
            return $result;
        });
    }

    /**
     * Restrict API  access to logout visitors(!is_user_logged_in()) and allow only administrator and editor.
     */
    public static function secondLevelAccess() {
        add_filter('rest_authentication_errors', function( $result ) {

            if ( ! empty( $result ) ) {
                return $result;
            }
            if ( ! is_user_logged_in() ) {
                return new \WP_Error( 'rest_not_logged_in', 'You are not currently logged in.', array('status' => 401 ) );
            }
            if ( ! current_user_can( 'edit_others_pages' ) ) {

                return new \WP_Error( 'rest_cannot_access', 'You do not have access rights.', array('status' => 401 ) );
            }
            return $result;}
        );
    }

    /**
     * Restrict API  access to logout visitors(!is_user_logged_in()) and allow only administrator, editor and author.
     */
    public static function thirdLevelAccess() {

        add_filter( 'rest_authentication_errors', function ( $result ) {
            if ( ! empty( $result ) ) {
                return $result;
            }
            if ( ! is_user_logged_in() ) {
                return new \WP_Error( 'rest_not_logged_in', 'You are not currently logged in.', array('status' => 401 ) );
            }
            if ( ! current_user_can( 'delete_published_posts' ) ) {
                return new \WP_Error( 'rest_cannot_access', 'You do not have access rights.', array('status' => 401 ) );
            }
            return $result;}
        );
    }

    /**
     * Restrict API  access to logout visitors(!is_user_logged_in()) and allow only administrator, editor, author and contributor.
     */
    public static function fourthLevelAccess() {

        add_filter( 'rest_authentication_errors', function ( $result ) {
            if ( ! empty( $result ) ) {
                return $result;
            }
            if ( ! is_user_logged_in() ) {
                return new \WP_Error( 'rest_not_logged_in', 'You are not currently logged in.', array('status' => 401 ) );
            }
            if ( ! current_user_can( 'edit_posts' ) ) {
                return new \WP_Error( 'rest_cannot_access', 'You do not have access rights.', array('status' => 401 ) );
            }
            return $result;
        });
    }
}
