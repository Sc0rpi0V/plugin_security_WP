<?php

/**
 * Plugin Name:  Security
 * Description: Provide security to WordPress
 * Author:  - 
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

/**
 * CLASS NsError
 * Display notice wp and error's types
 * for  Security plugin and wp cli commands
 */
class NsError {
	/**
	 * Init class notice
	 *
	 * @param mixed $notice_type type of notice.
	 * @param mixed $notice_message notice message.
	 */
	public static function init_notice( $notice_type, $notice_message ) {
		add_action(
			'admin_notices',
			function () use ( $notice_type, $notice_message ) {
				?>
			<div class="wpwrap">
				<div class="notice <?php echo esc_html( $notice_type ); ?> is-dismissible">
					<p><?php echo esc_html( $notice_message ); ?></p>
				</div>
			</div>
				<?php
			},
			11
		);
	}


	/**
	 * Display error message's types for security features
	 *
	 * @param mixed $type error type.
	 */
	public static function init_error( $type ) {
		$ns_errors = array(
			1 => __( 'Error : submit form', 'security' ),
			2 => __( 'Error : wp-option, can\'t retrieve  security options', 'security' ),
			3 => __( 'Error : htaccess file', 'security' ),
			4 => __( 'Error : file creation', 'security' ),
		);

		if ( array_key_exists( $type, $ns_errors ) ) {
			return $ns_errors[ $type ];
		} else {
			return 'Error';
		}
	}

	/**
	 * Display error message's type for wp CLI commands
	 *
	 * @param mixed $type type of cli error.
	 */
	public static function init_cli_error( $type ) {
		$ns_cli_errors = array(
			1 => __( 'htaccess file', 'security' ),
			2 => __( 'file creation', 'security' ),
			3 => __( 'wp-option', 'security' ),
		);

		if ( array_key_exists( $type, $ns_cli_errors ) ) {
			return $ns_cli_errors[ $type ];
		} else {
			return 'Error';
		}
	}
}
