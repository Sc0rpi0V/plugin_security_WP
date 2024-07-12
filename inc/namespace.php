<?php
/*
Plugin Name:  Security
Description: Provide security to WordPress
Author:  - 
Version: 0.0.1
Author URI: 
Text Domain: security

GNU General Public License, Free Software Foundation <http://creativecommons.org/licenses/GPL/2.0/>

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/


namespace \Security;

/**
 * Bootstrap the plugin, adding required actions and filters
 *
 * @action init
 */
function bootstrap()
{

  // is_plugin_active_for_network can only be used once the plugin.php file is
  // included. More information can be found here:
  // https://codex.wordpress.org/Function_Reference/is_plugin_active_for_network
  // if (! function_exists('is_plugin_active_for_network')) {
  //     require_once(ABSPATH . '/wp-admin/includes/plugin.php');
  // }

  //activate limit login attemps
  if (!class_exists('LimitLoginAttempts')) {
    new LimitLoginAttempts();
  }

  add_filter('rest_authentication_errors', function ($result) {
    if (!empty($result)) {
      return $result;
    }
    if (!is_user_logged_in()) {
      return new WP_Error('rest_not_logged_in', __('You are not currently logged in.', __SECURITY__), array('status' => 401));
    }
    if (!current_user_can('administrator')) {
      return new WP_Error('rest_not_admin', __('You are not an administrator.', __SECURITY__), array('status' => 401));
    }
    return $result;
  });

  function no_wordpress_errors()
  {
    return __('Wrong login provided !', __SECURITY__);
  }
  add_filter('login_errors', 'no_wordpress_errors');
}
