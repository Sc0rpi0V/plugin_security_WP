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
 * CLASS AdminPanel to create an admin panel for the admin role
 */
class SecurityAdminPanel {

    /**
     * Constructor
     */
    public function __construct() {
        add_action( 'admin_menu', array( $this, 'adminMenu') );
        add_action( 'wp_dashboard_setup', array($this, 'mozillaSecurityWidget') );
    }

    /**
     * Provide Mozilla foundation security analysis
     */
    public function mozillaSecurityWidget() {
        global $wp_meta_boxes;
        wp_add_dashboard_widget( 'custom_help_widget', __( ' security', 'security' ), array( $this, 'mozillaSecurityWidgetDisplay' ) );
    }

    /**
     * Display Mozilla foundation widget
     */
    public function mozillaSecurityWidgetDisplay() {
        $url        = preg_replace( '(^https?://)', '', site_url() );
        $result     = wp_remote_post( 'https://http-observatory.security.mozilla.org/api/v1/analyze?host=' . $url, array('hidden' => true, 'rescan' => true ) );
        $grade      = json_decode( $result['body'] )->grade;
        $color      = 'red';
        $sentence   = __( 'Your website requires security improvements !', 'security' );
        $fullReport = "https://observatory.mozilla.org/analyze/$url";

        switch ( $grade ) {
            case 'A':
                $color    = 'green';
                $sentence = __( 'All goods !', 'security' );
                break;
            case 'B':
            case 'C':
                $color = 'orange';
                break;
        }

        if ( $grade !== null ) {
            echo '<h2>' . __( 'Mozilla observatory report:', 'security' ) . '</h2>';
            echo esc_html( "<span style='color:$color;font-size:60px;text-align:center'>Grade " ) . esc_html( $grade ) . '</span><br/><a href="' . esc_html( $fullReport ) . '">' . __( 'Click here to see report', 'security' );
        } else {
            echo __( 'Error : can\'t retrieve data from Mozilla Observatory', 'security' );
        }
    }


    /**
     * Add menu Options  and his submenu(s) and erase the duplicate submenu
     */
    public function adminMenu() {
        global $menu;
        global $submenu;

        if ( ! array_search( 'options-security', array_column( $menu, 2 ), true ) ) {
            add_menu_page(
                __( 'Options ', 'security' ),
                __( 'Options ', 'security' ),
                'activate_plugins',
                'options-security',
                array( __CLASS__, 'nOptionsPage'),
                __SECURITY_URL__ . 'assets/img/IconB.png',
                76
            );
        }
        add_submenu_page(
            __( 'options-security', 'security' ),
            __( 'Home ', 'security' ),
            'Home',
            'activate_plugins',
            'home-plugin',
            array( __CLASS__, 'PluginsListPage'),
            null
        );

        add_submenu_page(
            __( 'options-security', 'security' ),
            __( 'Security ', 'security' ),
            ' Security',
            'activate_plugins',
            'security-',
            array( __CLASS__, 'SecurityPage' ),
            null
        );

        if ( ! array_search( 'options-security', array_column( $submenu, 2 ), true ) ) {
            remove_submenu_page( 'options-security', 'options-security' );
        }
    }
    /**
     * Load  options template
     */
    public static function PluginsListPage() {
        require_once __SECURITY_DIR__ . '/templates/template-options.php';
    }

    /**
     * Load security page template
     */
    public static function SecurityPage() {
        switch ( filter_input( INPUT_GET, 'tab', FILTER_SANITIZE_STRING ) ) {
            case 'lt':
                require_once __SECURITY_DIR__ . '/templates/template-tab-login.php';
                break;
            case 'ht':
                require_once __SECURITY_DIR__ . '/templates/template-tab-htaccess.php';
                break;
            case 'ot':
                require_once __SECURITY_DIR__ . '/templates/template-tab-other.php';
                break;
            default:
                require_once __SECURITY_DIR__ . '/templates/template-security.php';
                break;
        }
    }
}
