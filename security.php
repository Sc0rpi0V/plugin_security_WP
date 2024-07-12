<?php

/**
 * Plugin Name:  Security
 * Description: Provide security to WordPress
 * Author: 
 * Version: 0.0.1
 * Author URI: 
 * Text Domain: security

 * GNU General Public License,  Free Software Foundation <http://creativecommons.org/licenses/GPL/2.0/>

 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @package security
 */

use Security\NsFormValidator;

/**
 * CLASS Security
 * Init and load plugin content
 */
class Security {
	/**
	 * Constructor
	 */
	public function __construct() {

		register_activation_hook( __FILE__, array( __CLASS__, 'security_plugin_activate' ) ); // register activation function.
		register_deactivation_hook( __FILE__, array( __CLASS__, 'security_plugin_deactivate' ) ); // register deactivation function.
		register_uninstall_hook( __FILE__, array( __CLASS__, 'security_plugin_uninstall' ) ); // register uninstall function.
		define( '__SECURITY__', 'security' );
		define( '__SECURITY_DIR__', __DIR__ );
		define( '__SECURITY_URL__', plugin_dir_url( __FILE__ ) );
		define( '__SECURITY_HTACCESS_ADMIN_BACKUP__', ABSPATH . '/wp-admin/htaccess.backup' );
		define( '__SECURITY_HTACCESS_ADMIN__', ABSPATH . '/wp-admin/.htaccess' );
		define( '__SECURITY_HTACCESS__', ABSPATH . '/.htaccess' );
		define( '__SECURITY_HTACCESS_BACKUP__', ABSPATH . '/htaccess.backup' );

		$this->autoload();
		$this->verif_option();
		new NsFormValidator();
		new LimitLoginAttempts();
		new RemoveWpVersion();
		new SriAttribute();
	}


	/**
	 * Hook triggered when activating the plugin
	 * Add entry in options if its doesn t exist
	 */
	public static function security_plugin_activate() {
		// if no options available, we create ones.
		if ( ! get_option( 'security' ) ) {
			add_option( 'security' );
		}
		$option_data = json_decode( get_option( 'security' ) );

	}

	/**
	 * Hook triggered when activating the plugin
	 */
	public static function security_plugin_deactivate() {
	}

	/**
	 * Hook triggered when removing the plugin
	 * remove entries from options
	 */
	public static function security_plugin_uninstall() {
		// we delete options.
		if ( get_option( 'security' ) ) {
			remove_option( 'security' );
		}
	}

	/**
	 * Init class
	 */
	public static function init() {
		new self();
	}

	/**
	 * Add security in wp_options
	 */
	public function verif_option() {
		if ( ! get_option( 'security' ) ) {
			add_option( 'security' );
		}
	}

	/**
	 * Load classes
	 */
	public static function autoload() {
		require_once __DIR__ . '/inc/namespace.php';
		require_once __DIR__ . '/inc/ns-form-validator.php';
		require_once __DIR__ . '/inc/admin-controller.php';
		require_once __DIR__ . '/inc/htaccess-controller.php';
		require_once __DIR__ . '/inc/LimitLoginAttempts.php';
		require_once __DIR__ . '/inc/remove-wpversion.php';
		require_once __DIR__ . '/inc/ns-error.php';
		require_once __DIR__ . '/inc/restrict-api.php';
		require_once __DIR__ . '/inc/add-sri-attribute.php';
	}

	/**
	 * Add cli command
	 */
	public static function wds_cli_register_commands() {
		require_once __DIR__ . '/inc/cli-commands.php';
		new SecurityCli();
		WP_CLI::add_command( 'security', 'SecurityCli' );
	}
}


add_action( 'init', array( 'Security', 'init' ) );

add_action( 'cli_init', array( 'Security', 'wds_cli_register_commands' ) );
