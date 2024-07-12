<?php

namespace \Security;

use HtAccessController;
use NsError;
use RestrictApi;

/**
 * CLASS NsFormValidator
 * Prevent Mass WordPress Login Attacks by setting locking the system when login fail.
 * To be added in functions.php or as an external file.
 */
class NsFormValidator {
	/**
	 * Set max failed login limit.
	 *
	 * @var default 5
	 */
	public static $failed_login_limit = 5; // Login Attempt.
	/**
	 * Set lockout duration in seconds
	 *
	 * @var default 1800
	 */
	public static $lockout_duration = 1800; // Duration in seconds. 30 minute(s): 60*30 = 1800.
	/**
	 * Define transition name to store fail2ban
	 *
	 * @var string
	 */
	public static $transient_name = 'ns_attempted_login'; // Transient used.

	/**
	 * The constructor
	 */
	public function __construct() {
		global $active_limit_box;
		global $remove_version_box;
		global $secure_htfiles_box;
		global $secure_wpconfig_box;
		global $secure_accesslog_box;
		global $hide_index_box;
		global $secure_cxm_box;
		global $prevent_finject_box;
		global $restrict_access_box;
		global $restrict_api_box;
		global $block_user_enum_box;
		global $number_limit_input;
		global $time_limit_input;
		global $access_slug_input;
		global $role_allowed;
		global $ip_stored;

		// Init variables use into template-tab-login.
		$ip_stored                = ( isset( $option_data['ip-limit'] ) ) ? $option_data['ip-limit'] : null;
		$number_limit_input       = ( isset( $option_data['limit-number'] ) ) ? $option_data['limit-number'] : 2;
		$this->failed_login_limit = $number_limit_input;
		$time_limit_input         = ( isset( $option_data['limit-time'] ) ) ? $option_data['limit-time'] : 0;
		$this->lockout_duration   = $time_limit_input * 60;
		$access_slug_input        = ( isset( $option_data['restrict-slug'] ) ) ? $option_data['restrict-slug'] : '';
		$notice_message           = __( ' Security : Options updated', 'security' );

		// Update and Check/ Uncheck connexion limit.
		if ( isset( $option_data['active-limit'] ) ) {
			add_filter( 'authenticate', array( 'LimitLoginAttempts', 'check_attempted_login' ), 30, 3 );
			add_action( 'wp_login_failed', array( 'LimitLoginAttempts', 'login_failed' ), 10, 1 );
			$active_limit_box = 'checked';
		}

		// Update and Check/ Uncheck hide version WP in wp-option table.
		if ( isset( $option_data['active-remove'] ) ) {
			// Active filter wp version into css/script files and header.
			add_filter( 'the_generator', array( 'RemoveWpVersion', 'removeToHeaderRss' ) );
			add_filter( 'style_loader_src', array( 'RemoveWpVersion', 'removeToScriptCss' ), 10, 1 );
			add_filter( 'script_loader_src', array( 'RemoveWpVersion', 'removeToScriptCss' ), 10, 1 );
			$remove_version_box = 'checked';
		}

		// Check / Uncheck secure htfiles.
		if ( isset( $option_data['secure-htfiles'] ) ) {
			$secure_htfiles_box = 'checked';
		}

		// Check / Uncheck secure wp-config.
		if ( isset( $option_data['secure-wpconfig'] ) ) {
			$secure_wpconfig_box = 'checked';
		}

		// Check / Uncheck hide access to logs.
		if ( isset( $option_data['secure-logs'] ) ) {
			$secure_accesslog_box = 'checked';
		}
		// Check / Uncheck hide  index of  server side.
		if ( isset( $option_data['hide-indexof'] ) ) {
			$hide_index_box = 'checked';
		}

		// Check / Uncheck prevent CXM attacks.
		if ( isset( $option_data['secure-against-CXM'] ) ) {
			$secure_cxm_box = 'checked';
		}

		// Check / Uncheck prevent file injection.
		if ( isset( $option_data['secure-against-finject'] ) ) {
			$prevent_finject_box = 'checked';
		}

		// Check / Uncheck  restrict access to wp-login.
		if ( isset( $option_data['restrict-access'] ) ) {

			// Delete restrictAccess cookie after deconnexion.
			add_action( 'wp_logout', array( 'HtAccessController', 'cleanRestrictAccessCookie' ) );
			$restrict_access_box = 'checked';
		}

		// Check / Uncheck block users enumeration.
		if ( isset( $option_data['block-user-enum'] ) ) {
			$block_user_enum_box = 'checked';
		}

		// Check / Uncheck  restrict access to wp api rest.
		if ( isset( $option_data['restrict-api'] ) ) {

			switch ( $option_data['roles-api'] ) {

				case 'first-level':
					RestrictApi::firstLevelAccess();
					$role_allowed = 'Admin';
					break;
				case 'second-level':
					RestrictApi::secondLevelAccess();
					$role_allowed = 'Admin - Editor';

					break;
				case 'third-level':
					RestrictApi::thirdLevelAccess();
					$role_allowed = 'Admin - Editor - Author';

					break;
				case 'fourth-level':
					RestrictApi::fourthLevelAccess();
					$role_allowed = 'Admin - Editor - Author - Contributor';

					break;
			}

			$restrict_api_box = 'checked';
		}

		// adding the notification after redirection when form is completed.
		if ( htmlspecialchars(filter_input( INPUT_GET, 'redirected')) ) {
			NsError::init_notice( 'notice-success', $notice_message );
		}

		// adding the notification after redirection when data form are invalid.
		if ( htmlspecialchars(filter_input( INPUT_GET, 'wrong-validation') ) ) {
			NsError::init_notice( 'notice-error', NsError::init_error( 1 ) );
		}

		// Activate hooks when forms are post (check token into form to secure).
		add_action( 'admin_post_active_limit', array( __CLASS__, 'limit_login_form_validator' ) );
		add_action( 'admin_post_nopriv_active_limit', array( __CLASS__, 'limit_login_form_validator' ) );

		add_action( 'admin_post_secure_htaccess', array( __CLASS__, 'htaccess_form_validator' ) );
		add_action( 'admin_post_nopriv_secure_htaccess', array( __CLASS__, 'htaccess_form_validator' ) );

		add_action( 'admin_post_active_other', array( __CLASS__, 'other_options_form_validator' ) );
		add_action( 'admin_post_nopriv_active_other', array( __CLASS__, 'other_options_form_validator' ) );
	}

	/**
	 * Limit login form treatment
	 */
	public static function limit_login_form_validator() {
		$option_data = json_decode( get_option( '_security' ), true );

		if ( check_admin_referer( 'active_limit_token' ) ) {
			$limit_number = filter_input( INPUT_POST, 'number-limit', FILTER_VALIDATE_INT );
			$time_limit   = filter_input( INPUT_POST, 'time-limit', FILTER_VALIDATE_INT );

			// Secure type of limit_number and time_limit and redirect if it isn't.
			if ( ! $limit_number || ! $time_limit ) {

				$limit_number = 0;
				$time_limit   = 0;
			}
			if ( ( $limit_number < 0 || $limit_number > 50 ) || ( $time_limit > 60 || $time_limit < 0 ) ) {

				wp_safe_redirect(
					add_query_arg(
						array(
							'page'             => 'security-',
							'wrong-validation' => 'true',
						),
						admin_url( 'admin.php' )
					)
				);
				exit;
			} else {

				if ( filter_input( INPUT_POST, 'active-limit' ) ) {
					$option_data['active-limit'] = true;
				} else {
					$option_data['active-limit'] = false;
				}

				$option_data['limit-number'] = $limit_number;
				$option_data['limit-time']   = $time_limit;
			}

			// push serialized data to wp-option.
			update_option( '_security', wp_json_encode( $option_data ) );
		}
		wp_safe_redirect(
			add_query_arg(
				array(
					'page'       => 'security-',
					'redirected' => 'true',
				),
				admin_url( 'admin.php' )
			)
		);
		exit;
	}


	/**
	 * Htaccess form treatment.
	 */
	public static function htaccess_form_validator() {

		$option_data   = json_decode( get_option( '_security' ), true );
		$restrict_slug = htmlspecialchars(filter_input( INPUT_POST, 'restrict-access-input'));

		if ( isset( $option_data['restrict-slug'] ) ) {
			$file_to_delete = ABSPATH . '/' . $option_data['restrict-slug'] . '.php';
			wp_delete_file( $file_to_delete );
		}

		if ( check_admin_referer( 'secure_htaccess_token' ) ) {

			$option_data['restrict-slug'] = $restrict_slug;
			if ( ! $restrict_slug ) {

				$option_data['restrict-access'] = false;
			}

			if ( filter_input( INPUT_POST, 'restrict-access' ) ) {
				$option_data['restrict-access'] = true;
			} else {
				$option_data['restrict-access'] = false;
			}

			// start/close ip filter treatment when form is submit.
			if ( filter_input( INPUT_POST, 'ip-filtre' ) ) {

				HtAccessController::htaccessFileCreation( __SECURITY_HTACCESS_ADMIN__, __SECURITY_HTACCESS_BACKUP__ );
				$option_data = HtAccessController::ipWriting( __SECURITY_HTACCESS_ADMIN__, $option_data );
			} else {
				wp_delete_file( __SECURITY_HTACCESS_ADMIN_BACKUP__ );
			}

			// start/close limit access (htaccess/htpassword/ini/log) treatment when form is submit.
			if ( filter_input( INPUT_POST, 'active-secure-htfiles' ) ) {

				HtAccessController::htaccessFileCreation( __SECURITY_HTACCESS__, __SECURITY_HTACCESS_BACKUP__ );
				$option_data = HtAccessController::secureAccessHtfiles( __SECURITY_HTACCESS__, $option_data, true );
			} else {
				$option_data = HtAccessController::secureAccessHtfiles( __SECURITY_HTACCESS__, $option_data, false );
				wp_delete_file( __SECURITY_HTACCESS_BACKUP__ );
			}

			// start/close secure access of wp-config.php treatment when form is submit.
			if ( filter_input( INPUT_POST, 'active-secure-wpconfig' ) ) {

				HtAccessController::htaccessFileCreation( __SECURITY_HTACCESS__, __SECURITY_HTACCESS_BACKUP__ );
				$option_data = HtAccessController::secureAccessWpconfig( __SECURITY_HTACCESS__, $option_data, true );
			} else {
				$option_data = HtAccessController::secureAccessWpconfig( __SECURITY_HTACCESS__, $option_data, false );
				wp_delete_file( __SECURITY_HTACCESS_BACKUP__ );
			}

			// start/close secure access of logs treatment when form is submit.
			if ( filter_input( INPUT_POST, 'active-secure-logs' ) ) {

				HtAccessController::htaccessFileCreation( __SECURITY_HTACCESS__, __SECURITY_HTACCESS_BACKUP__ );
				$option_data = HtAccessController::secureAccessLogs( __SECURITY_HTACCESS__, $option_data, true );
			} else {
				$option_data = HtAccessController::secureAccessLogs( __SECURITY_HTACCESS__, $option_data, false );
				wp_delete_file( __SECURITY_HTACCESS_BACKUP__ );
			}

			// start/close secure hide index of treatment when form is submit.
			if ( filter_input( INPUT_POST, 'active-hide-index' ) ) {

				HtAccessController::htaccessFileCreation( __SECURITY_HTACCESS__, __SECURITY_HTACCESS_BACKUP__ );
				$option_data = HtAccessController::hideIndexOf( __SECURITY_HTACCESS__, $option_data, true );
			} else {
				$option_data = HtAccessController::hideIndexOf( __SECURITY_HTACCESS__, $option_data, false );
				wp_delete_file( __SECURITY_HTACCESS_BACKUP__ );
			}

			// start/close Secure against Clickjacking/XSS/ MIME-Type sniffing treatment when form is submit.
			if ( filter_input( INPUT_POST, 'active-secure-cxm' ) ) {

				HtAccessController::htaccessFileCreation( __SECURITY_HTACCESS__, __SECURITY_HTACCESS_BACKUP__ );
				$option_data = HtAccessController::secureAgainstCXM( __SECURITY_HTACCESS__, $option_data, true );
			} else {
				$option_data = HtAccessController::secureAgainstCXM( __SECURITY_HTACCESS__, $option_data, false );
				wp_delete_file( __SECURITY_HTACCESS_BACKUP__ );
			}
			// start/close prevent file injection treatment when form is submit.
			if ( filter_input( INPUT_POST, 'active-secure-finject' ) ) {
				
				HtAccessController::htaccessFileCreation( __SECURITY_HTACCESS__, __SECURITY_HTACCESS_BACKUP__ );
				$option_data = HtAccessController::secureAgainstFinject( __SECURITY_HTACCESS__, $option_data, true );
			} else {
				$option_data = HtAccessController::secureAgainstFinject( __SECURITY_HTACCESS__, $option_data, false );
				wp_delete_file( __SECURITY_HTACCESS_BACKUP__ );
			}
			// start/close prevent access wp-admin and login.php with cookie when form is submit.
			if ( filter_input( INPUT_POST, 'restrict-access' ) ) {
				
				HtAccessController::htaccessFileCreation( __SECURITY_HTACCESS__, __SECURITY_HTACCESS_BACKUP__ );
				var_dump($restrict_slug);
				$option_data = HtAccessController::restrictAccess( __SECURITY_HTACCESS__, $option_data, $restrict_slug );
				var_dump($option_data);
			} else {
				$htfile = file_get_contents( __SECURITY_HTACCESS__ );
				if ( strpos( $htfile, 'Active restrict file access end' ) ) {
					$htfile = HtAccessController::deleteAllBetween( "\n# Active restrict file access begin don't write within", '# Active restrict file access end', $htfile );
					file_put_contents( __SECURITY_HTACCESS__, $htfile );
				}
				if ( filter_input( INPUT_POST, 'restrict-access-input' ) ) {
					$del_file = ABSPATH . '/' . filter_input( INPUT_POST, 'restrict-access-input' ) . '.php';
					wp_delete_file( $del_file );
				}
				wp_delete_file( __SECURITY_HTACCESS_BACKUP__ );
			}
			// start/close block user enumeration when form is submit.
			if ( filter_input( INPUT_POST, 'block-user-enum' ) ) {
				
				HtAccessController::htaccessFileCreation( __SECURITY_HTACCESS__, __SECURITY_HTACCESS_BACKUP__ );
				$option_data = HtAccessController::blockUserEnum( __SECURITY_HTACCESS__, $option_data, true );
			} else {
				$option_data = HtAccessController::blockUserEnum( __SECURITY_HTACCESS__, $option_data, false );
				wp_delete_file( __SECURITY_HTACCESS_BACKUP__ );
			}

			// push serialized data to wp-option.
			update_option( '_security', wp_json_encode( $option_data ) );
		}

		

		wp_safe_redirect(
			add_query_arg(
				array(
					'page'       => 'security-',
					'redirected' => 'true',
				),
				admin_url( 'admin.php' )
			)
		);
		exit;
	}



	/**
	 * Other options form treatment .
	 */
	public static function other_options_form_validator() {

		$option_data = json_decode( get_option( '_security' ) );

		if ( check_admin_referer( 'active_other_token' ) ) {

			// Write removed Wp version in _security option.
			if ( filter_input( INPUT_POST, 'active-remove' ) ) {
				$option_data['active-remove'] = true;
			} else {
				$option_data['active-remove'] = false;
			}

			// Write restrict access to api in _security option.
			if ( filter_input( INPUT_POST, 'restrict-api' ) ) {

				$option_data['restrict-api'] = true;
			} else {
				$option_data['restrict-api'] = false;
			}

			// Write roles allowed to connect to API in _security option.
			if ( filter_input( INPUT_POST, 'roles-api' ) ) {
				$option_data['roles-api'] = filter_input( INPUT_POST, 'roles-api' );
			}

			// push serialized data to wp-option.
			update_option( '_security', wp_json_encode( $option_data ) );
		}

		wp_safe_redirect(
			add_query_arg(
				array(
					'page'       => 'security-',
					'redirected' => 'true',
				),
				admin_url( 'admin.php' )
			)
		);
		exit;
	}
}
