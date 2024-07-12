<?php /*
Plugin Name:  security
Description: Provide security functions to wordpress
Author:  - 
Version: 0.0.1
Author URI: 
Text Domain: security
Domain Path: /language

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

global $secure_htfiles_box;
global $secure_wpconfig_box;
global $secure_accesslog_box;
global $hide_index_box;
global $secure_cxm_box;
global $prevent_finject_box;
global $ip_stored;
global $restrict_access_box;
global $block_user_enum_box;
global $access_slug_input;

$ip_stored = (!empty($ip_stored)) ? implode($ip_stored, PHP_EOL) : "";


?>
<div class="wpwrap">
    <div class="section-title" style="display:flex; flex-direction:row; justify-content:end; padding:20px 20px 0 0;">
        <a href="<?php echo admin_url() . "admin.php?page=home-plugin" ?>" style="text-decoration: inherit;height: 100px;">
            <svg width="100" height="100" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M0 0h100v100H0V0Z" fill="#06F" />
                <path d="M28.6666 62.2167H17.6v-.95c1.8 0 1.9833-.45 1.9833-2.65V45.9833c0-2.2-.1833-2.65-1.9833-2.65v-.95h9.0833V45.8c.9-1.9333 3.2333-3.8667 6.0667-3.8667 4.1333 0 6.3833 2.9167 6.3833 6.5167v10.1667c0 2.2.1833 2.65 1.9833 2.65v.95H30.05v-.95c1.8 0 1.9833-.45 1.9833-2.65V49.3c0-3.0167-1.2167-4.1833-2.9167-4.1833-.95 0-1.8833.5833-2.4333 1.8v11.7c0 2.2.2167 2.65 1.9833 2.65v.95Zm15.3333-18.8834v-.95h9.0834v16.2334c0 2.2.2166 2.65 1.9833 2.65v.95H43.9999v-.95c1.8 0 1.9834-.45 1.9834-2.65V45.9833c0-2.2-.1834-2.65-1.9834-2.65Z" fill="#fff" />
                <path d="M59.65 45.9835c0-2.2-.2167-2.65-1.9833-2.65v-.95H66.75v20.6c0 5.75-3.8667 8.2334-8.5833 8.2334-2.4334 0-4.05-.6334-4.9-1.35l.4-.85c.7666.5 1.4.7666 2.4666.7666 2.0667 0 3.5-1.35 3.5-4.45v-19.35h.0167Zm11.6833-2.65v-.95h9.0834v16.2334c0 2.2.2166 2.65 1.9833 2.65v.95H71.3333v-.95c1.8 0 1.9834-.45 1.9834-2.65V45.9835c0-2.2-.1834-2.65-1.9834-2.65Zm-21.7999-6.9167c2.1079 0 3.8166-1.7088 3.8166-3.8167s-1.7087-3.8167-3.8166-3.8167-3.8167 1.7088-3.8167 3.8167 1.7088 3.8167 3.8167 3.8167Zm13.6666 0c2.1079 0 3.8167-1.7088 3.8167-3.8167s-1.7088-3.8167-3.8167-3.8167-3.8168 1.7088-3.8168 3.8167 1.7089 3.8167 3.8168 3.8167Zm13.6666 0c2.1078 0 3.8166-1.7088 3.8166-3.8167s-1.7088-3.8167-3.8166-3.8167c-2.1079 0-3.8167 1.7088-3.8167 3.8167s1.7088 3.8167 3.8167 3.8167Z" fill="#fff" />
            </svg>
        </a>
    </div>

    <h2 class="nav-tab-wrapper">
        <a href="<?php echo admin_url() . "admin.php?page=security-" ?>" class="nav-tab"><?php _e('Info', __SECURITY__); ?></a>
        <a href="<?php echo admin_url() . "admin.php?page=security-&tab=ht" ?>" class="nav-tab nav-tab-active"><?php _e('Htaccess', __SECURITY__); ?></a>
        <a href="<?php echo admin_url() . "admin.php?page=security-&tab=lt" ?>" class="nav-tab"><?php _e('Limit login', __SECURITY__); ?></a>
        <a href="<?php echo admin_url() . "admin.php?page=security-&tab=ot" ?>" class="nav-tab"><?php _e('...', __SECURITY__); ?></a>
    </h2>
    <div class="tabs-content" style="display:flex; flex-direction:column;">
        <h3><?php _e('Securise your website by the htaccess file', __SECURITY__); ?></h3>
        <p><?php _e('In this part, you will be able to allow access, protect access to certain files, protect against attacks and also prevent access to wp-login and wp-admin.<br>
    Each action that will be done, update the restrictions you have requested. We will see in detail each part that makes up this tab.<br><br>

	- <b>"Manage access by ips"</b> : Allows you to give access to the BackOffice for certain IP addresses. The ones who will be in the list to have access, which is not present, will not be able to have access to it. <br><br>
		> <i><b>Examples of @IP </b>: "192.168.1.1" / "127.0.0.1" / "172.16.2.5"</i><br><br>

	- <b>"Protect access to file"</b> : Allows you to protect access files called "sensitive files" against somes attacks.This data cannot be accessed by another person.<br><br>

		> <b>"Secure .htaccess and .htpasswords files"</b> : Protect files access files called "sensitive files"<br>
		> <b>"Secure Wp-config"</b> : Protect file because this file is the link between with your website and database.<br>
		> <b>"Secure access of logs"</b> : Protect files with access about attacks.<br>
		> <b>"Hide index of server side"</b> : Hide the version of server.<br><br>

	- "<b>Prevent attacks"</b> : Helps to avoid the various known attacks in order to strengthen your system. These actions you will allow you not to be attacked by these threats and you will avoid leaving your actions visible.<br>
        Not activating these options, would give access to the following information :  <br><br>

		> <b>"Clickjacking"</b> : Makes stealing sensitive personal information as quick and easy as logging into an app.<br>
		> <b>"XSS"</b>: Attacks on websites that dynamically display user content without controlling and encoding the information entered by users.<br>
		> <b>"MIME-type sniffing"</b> : Review the content of a particular resource. This is done for the purpose of determining the file format of an asset.<br>
		> <b>"File Injection"</b> : In an injection attack, an attacker provides untrusted input to a program.  This input is processed by an interpreter as part of a command or query. In turn, this changes the execution of this program.<br>
		> <b>"XML-RPC"</b> : Transmission protocol (sending and receiving information between two remote sites).<br>
		> <b>"WinHttpRequest_Agent"</b> : Sleeper requesting information on your website.<br><br>

	- "<b>Prevent access to wp-login and wp-admin"</b> : Allows you to protect access files. Changes login to access on website. <b><i>If you activate and change the name of wp-login, you change the name directly on your URL website </b></i><br><br>

    - "<b> Block the enumeration of site users by uri"</b> : Corrects the possibility of displaying the names of site users via the url and therefore prevents access to this private data.', __SECURITY__); ?> </p>
        <form action="<?= admin_url('admin-post.php'); ?>" method="post">
            <?php wp_nonce_field('secure_htaccess_token'); ?>
            <input type="hidden" name="action" value="secure_htaccess">
            <h4><?php _e("Manage access by ip's", __SECURITY__); ?></h4>
            <label for="ip-filtre"><?php _e('Insert the ip that are allowed to access to the BO ', __SECURITY__) ?></label>

            <div class="aside-blocs" style="display: flex; align-items: center;">
                <textarea name="ip-filtre" id="ip-filtre" cols="30" rows="10" placeholder="192.168.1.1"><?= $ip_stored; ?></textarea>
                <p class="aside-txtarea" style="margin-left:5px;"><?php _e('One IP per line.', __SECURITY__) ?></p>
            </div>
            <div class="tab_subcontent" style="display:flex; flex-direction:column;">
                <h4><?php _e("Protect access to files", __SECURITY__); ?> </h4>
                <div class="form_group" style="display:flex; justify-content:space-between; width: 350px; align-items:end;">
                    <label for="active-secure-htfiles"><?php _e('Secure .htaccess and .htpasswords files ', __SECURITY__) ?></label>
                    <input type="checkbox" id="active-secure-htfiles" name="active-secure-htfiles" <?= $secure_htfiles_box; ?>>
                </div>
                <div class="form_group" style="display:flex;justify-content:space-between; width: 350px; align-items:end;">
                    <label for="active-secure-wpconfig"><?php _e('Secure wp-config ', __SECURITY__) ?></label>

                    <input type="checkbox" id="active-secure-wpconfig" name="active-secure-wpconfig" <?= $secure_wpconfig_box; ?>>
                </div>
                <div class="form_group" style="display:flex;justify-content:space-between; width: 350px; align-items:end;">
                    <label for="active-secure-logs"><?php _e('Secure access of logs ', __SECURITY__) ?></label>
                    <input type="checkbox" id="active-secure-logs" name="active-secure-logs" <?= $secure_accesslog_box; ?>>
                </div>
                <div class="form_group" style="display:flex;justify-content:space-between; width: 350px; align-items:end;">
                    <label for="active-hide-index"><?php _e('Hide index of server side ', __SECURITY__) ?></label>
                    <input type="checkbox" id="active-hide-index" name="active-hide-index" <?= $hide_index_box; ?>>
                </div>
            </div>
            <div class="tab_subcontent" style="display:flex; flex-direction:column; ">
                <h4><?php _e("Prevent attacks", __SECURITY__); ?> </h4>
                <div class="form_group" style="display:flex;justify-content:space-between; width: 350px; align-items:end;">
                    <label for="active-secure-cxm"><?php _e('Secure against Clickjacking/XSS/ MIME-Type sniffing ', __SECURITY__) ?></label>
                    <input type="checkbox" id="active-secure-cxm" name="active-secure-cxm" <?= $secure_cxm_box; ?>>
                </div>
                <div class="form_group" style="display:flex;justify-content:space-between; width: 350px; align-items:end;">
                    <label for="active-secure-finject"><?php _e('Prevent file injection ', __SECURITY__) ?></label>
                    <input type="checkbox" id="active-secure-finject" name="active-secure-finject" <?= $prevent_finject_box; ?>>
                </div>
            </div>
            <div class="tab_subcontent" style="display:flex; flex-direction:column; ">
                <h4><?php _e("Prevent access to wp-login and wp-admin", __SECURITY__); ?> </h4>

                <div class="form_group" style="display:flex;  align-items:end;">
                    <label for="restrict-access"><?php _e('Active protection ', __SECURITY__) ?></label>
                    <input type="checkbox" id="restrict-access" name="restrict-access" <?= $restrict_access_box; ?> style="margin-left: 10px">
                </div>
                <div class="form_group" style="display:flex;  align-items:end;">
                    <label for="restrict-access-input"><?php _e('Insert slug who replace "login" in login.php url ', __SECURITY__) ?></label>
                    <input type="text" id="restrict-access-input" name="restrict-access-input" style="margin-left: 10px" value="<?php echo $access_slug_input ?>">
                </div>
            </div>
            <br>
            <div class="tab_subcontent" style="display:flex; flex-direction:column; ">
                <h4><?php _e("Block the enumeration of site users by uri", __SECURITY__); ?> </h4>
                <div class="form_group" style="display:flex;  align-items:end;">
                    <label for="block-user-enum"><?php _e('Active protection ', __SECURITY__) ?></label>
                    <input type="checkbox" id="block-user-enum" name="block-user-enum" <?= $block_user_enum_box; ?> style="margin-left: 10px">
                </div>
                <br>
            </div>
            <br>

            <input class="button button-primary" type="submit" value="Submit">
        </form>
    </div>
</div>