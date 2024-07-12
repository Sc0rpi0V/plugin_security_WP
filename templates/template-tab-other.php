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
global $remove_version_box;
global $restrict_api_box;
global $role_allowed;
?>
<div class="wpwrap">
    <div class="section-title" style="display:flex; flex-direction:row; justify-content:end;; padding:20px 20px 0 0;">
        <a href="<?php echo admin_url() . "admin.php?page=home-plugin" ?>"
           style="text-decoration: inherit; height: 100px;">
            <svg width="100" height="100" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M0 0h100v100H0V0Z" fill="#06F"/>
                <path d="M28.6666 62.2167H17.6v-.95c1.8 0 1.9833-.45 1.9833-2.65V45.9833c0-2.2-.1833-2.65-1.9833-2.65v-.95h9.0833V45.8c.9-1.9333 3.2333-3.8667 6.0667-3.8667 4.1333 0 6.3833 2.9167 6.3833 6.5167v10.1667c0 2.2.1833 2.65 1.9833 2.65v.95H30.05v-.95c1.8 0 1.9833-.45 1.9833-2.65V49.3c0-3.0167-1.2167-4.1833-2.9167-4.1833-.95 0-1.8833.5833-2.4333 1.8v11.7c0 2.2.2167 2.65 1.9833 2.65v.95Zm15.3333-18.8834v-.95h9.0834v16.2334c0 2.2.2166 2.65 1.9833 2.65v.95H43.9999v-.95c1.8 0 1.9834-.45 1.9834-2.65V45.9833c0-2.2-.1834-2.65-1.9834-2.65Z"
                      fill="#fff"/>
                <path d="M59.65 45.9835c0-2.2-.2167-2.65-1.9833-2.65v-.95H66.75v20.6c0 5.75-3.8667 8.2334-8.5833 8.2334-2.4334 0-4.05-.6334-4.9-1.35l.4-.85c.7666.5 1.4.7666 2.4666.7666 2.0667 0 3.5-1.35 3.5-4.45v-19.35h.0167Zm11.6833-2.65v-.95h9.0834v16.2334c0 2.2.2166 2.65 1.9833 2.65v.95H71.3333v-.95c1.8 0 1.9834-.45 1.9834-2.65V45.9835c0-2.2-.1834-2.65-1.9834-2.65Zm-21.7999-6.9167c2.1079 0 3.8166-1.7088 3.8166-3.8167s-1.7087-3.8167-3.8166-3.8167-3.8167 1.7088-3.8167 3.8167 1.7088 3.8167 3.8167 3.8167Zm13.6666 0c2.1079 0 3.8167-1.7088 3.8167-3.8167s-1.7088-3.8167-3.8167-3.8167-3.8168 1.7088-3.8168 3.8167 1.7089 3.8167 3.8168 3.8167Zm13.6666 0c2.1078 0 3.8166-1.7088 3.8166-3.8167s-1.7088-3.8167-3.8166-3.8167c-2.1079 0-3.8167 1.7088-3.8167 3.8167s1.7088 3.8167 3.8167 3.8167Z"
                      fill="#fff"/>
            </svg>
        </a>
    </div>
    <h2 class="nav-tab-wrapper">
        <a href="<?php echo admin_url() . "admin.php?page=security-" ?>"
           class="nav-tab <?php echo $active_tab == 'display_options' ? 'nav-tab-active' : ''; ?>"><?php _e('Info', __SECURITY__); ?></a>
        <a href="<?php echo admin_url() . "admin.php?page=security-&tab=ht" ?>"
           class="nav-tab <?php echo $active_tab == 'display_options' ? 'nav-tab-active' : ''; ?>"><?php _e('Htaccess', __SECURITY__); ?></a>
        <a href="<?php echo admin_url() . "admin.php?page=security-&tab=lt" ?>"
           class="nav-tab <?php echo $active_tab == 'display_options' ? 'nav-tab-active' : ''; ?>"><?php _e('Limit login', __SECURITY__); ?></a>
        <a href="<?php echo admin_url() . "admin.php?page=security-&tab=ot" ?>"
           class="nav-tab nav-tab-active"> <?php _e('...', __SECURITY__); ?></a>

    </h2>
    <div class="tabs-content " style="display:flex; flex-direction:column">
        <h3><?php _e('Configure other options', __SECURITY__); ?></h3>
        <p><?php _e('In this part, you will be able to configure some options about access restrict with role/group of user. <br>
        And you have the possibility to hide the version of Wordpress on your website.<br><br>

	- "<b>Active process"</b> : Hide your version Wordpress on website.<br>
	- "<b>Active restriction"</b> : Active restrcition on user/group allowed to connect to API.<br><br>
		> "Administrator"<br>
		> "Administrator" / "Editor"<br>
		> "Administrator" / "Editor" / "Author"<br>
		> "Administrator" / "Editor" / "Author" / "Contributor"<br><br>

        <i><b>If you want to change access, see with your administrator about modification.</i></b>', __SECURITY__); ?> </p>

        <form action="<?= admin_url('admin-post.php'); ?>" method="post" id="form-others">
            <?php wp_nonce_field('active_other_token'); ?>
            <input type="hidden" name="action" value="active_other">

            <h4><?php _e('Hide Wordpress version on your website', __SECURITY__); ?></h4>
            <div class="form_group">
                <label for="active-remove"><?php _e('Active process', __SECURITY__) ?></label>
                <input type="checkbox" id="active-remove" name="active-remove" <?= $remove_version_box; ?>>
            </div>
            <h4><?php _e('Restrict access to Wordpress API', __SECURITY__); ?></h4>

            <div class="form_group" style="display:flex;  align-items:end;">
                <label for="restrict-access"><?php _e('Active restriction', __SECURITY__) ?></label>
                <input type="checkbox" id="restrict-api" name="restrict-api" <?= $restrict_api_box; ?>
                       style="margin-left: 10px">
            </div>
            <br>
            <legend style="font-style:italic;"><?php _e("Role(s) : $role_allowed ", __SECURITY__) ?></legend>
            <div class="form_group" style="display:flex; align-items:center;">

                <select name="roles-api" id="roles-api">
                    <option value="first-level"><?php _e('Choose role/group allowed to connect to API', __SECURITY__) ?></option>
                    <option value="first-level"><?php _e('Administrator', __SECURITY__) ?></option>
                    <option value="second-level"><?php _e('Administrator - Editor', __SECURITY__) ?></option>
                    <option value="third-level"><?php _e('Administrator - Editor - Author', __SECURITY__) ?></option>
                    <option value="fourth-level"><?php _e('Administrator - Editor - Author - Contributor', __SECURITY__) ?></option>
                </select>

            </div>
            <br>
            <?php _e('<b>/!\ All the modification made on your side, make change on access or restriction. /!\<br></b> 
            <b>/!\ Take care of what you do on this plugin and do not hesitate to see with your administrator if you have some questions. /!\</b><br><br>', __SECURITY__); ?>
            <input class="button button-primary" type="submit" value="Submit">
        </form>
    </div>
</div>