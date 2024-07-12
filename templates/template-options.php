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

?>


<div class="wpwrap">
    <div class="section-title"
         style="display:flex; flex-direction:row; justify-content:space-between; padding:20px 20px 0 0;">
        <h1 style="align-self:center;"><?php esc_html_e( 'Options ', 'security' ); ?></h1>
        <svg width="100" height="100" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0 0h100v100H0V0Z" fill="#06F"/>
            <path d="M28.6666 62.2167H17.6v-.95c1.8 0 1.9833-.45 1.9833-2.65V45.9833c0-2.2-.1833-2.65-1.9833-2.65v-.95h9.0833V45.8c.9-1.9333 3.2333-3.8667 6.0667-3.8667 4.1333 0 6.3833 2.9167 6.3833 6.5167v10.1667c0 2.2.1833 2.65 1.9833 2.65v.95H30.05v-.95c1.8 0 1.9833-.45 1.9833-2.65V49.3c0-3.0167-1.2167-4.1833-2.9167-4.1833-.95 0-1.8833.5833-2.4333 1.8v11.7c0 2.2.2167 2.65 1.9833 2.65v.95Zm15.3333-18.8834v-.95h9.0834v16.2334c0 2.2.2166 2.65 1.9833 2.65v.95H43.9999v-.95c1.8 0 1.9834-.45 1.9834-2.65V45.9833c0-2.2-.1834-2.65-1.9834-2.65Z"
                  fill="#fff"/>
            <path d="M59.65 45.9835c0-2.2-.2167-2.65-1.9833-2.65v-.95H66.75v20.6c0 5.75-3.8667 8.2334-8.5833 8.2334-2.4334 0-4.05-.6334-4.9-1.35l.4-.85c.7666.5 1.4.7666 2.4666.7666 2.0667 0 3.5-1.35 3.5-4.45v-19.35h.0167Zm11.6833-2.65v-.95h9.0834v16.2334c0 2.2.2166 2.65 1.9833 2.65v.95H71.3333v-.95c1.8 0 1.9834-.45 1.9834-2.65V45.9835c0-2.2-.1834-2.65-1.9834-2.65Zm-21.7999-6.9167c2.1079 0 3.8166-1.7088 3.8166-3.8167s-1.7087-3.8167-3.8166-3.8167-3.8167 1.7088-3.8167 3.8167 1.7088 3.8167 3.8167 3.8167Zm13.6666 0c2.1079 0 3.8167-1.7088 3.8167-3.8167s-1.7088-3.8167-3.8167-3.8167-3.8168 1.7088-3.8168 3.8167 1.7089 3.8167 3.8168 3.8167Zm13.6666 0c2.1078 0 3.8166-1.7088 3.8166-3.8167s-1.7088-3.8167-3.8166-3.8167c-2.1079 0-3.8167 1.7088-3.8167 3.8167s1.7088 3.8167 3.8167 3.8167Z"
                  fill="#fff"/>
        </svg>
    </div>

    <h2 class="nav-tab-wrapper">
        <a href="<?php echo esc_html( admin_url() . 'admin.php?page=home-plugin' ); ?>" class="nav-tab nav-tab-active">Home</a>
        <?php
        if ( get_option( '_security' ) !== false ) {
            ?>
            <a href="<?php echo esc_html( admin_url() . 'admin.php?page=security-' ); ?>" class="nav-tab"> Security</a>
            <?php
        }
        ?>
    </h2>

    <div class="tabs-content">

        <h3><?php esc_html_e( 'Thank you for activating our  solutions', 'security' ); ?></h3>
        <p>
            <?php
            echo __(
                'This plugin will allow you to update the security level on your system.<br>
                On this plugin you will be able to find different information to enter to increase your security. <br>
                To do this, you just need to go to the " Security" tab to understand how the plugin works with all the required information.',
                'security'
            );
            ?>
        </p>
    </div>

</div>