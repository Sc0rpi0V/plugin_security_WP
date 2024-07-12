<?php

class SecurityCli
{

	/*
* -----------------------------------------------HTACCESS CLI COMMANDS
*/

	/**
	 * erase the admin htaccess and all the options within
	 * arguments : 	'main' -> for erasing the .htaccess file
	 * 				'backup' -> for erasing the backup.htaccess file
	 * 
	 * @since  0.0.1
	 * @author 
	 */
	public function cleanAdminHtaccess($args, $assoc_args)
	{
		$welcome_message = 'Clean admin .htaccess file. Use --main for erasing the file and --backup for erasing the backup file.';
		WP_CLI::log($welcome_message);
		if ($assoc_args['main']) {
			unlink(__SECURITY_HTACCESS_ADMIN__);
			WP_CLI::success('wp-admin .htaccess cleaned');
		}
		if ($assoc_args['backup']) {
			unlink(__SECURITY_HTACCESS_ADMIN_BACKUP__);
			WP_CLI::success('wp-admin backup.htaccess cleaned');
		}
	}

	/**
	 * erase the general htaccess and all the options within
	 * arguments : 	'main' -> for erasing the .htaccess file
	 * 				'backup' -> for erasing the backup.htaccess file
	 * 
	 * @since  0.0.1
	 * @author 
	 */
	public function cleanHtaccess($args, $assoc_args)
	{
		$welcome_message = 'Clean main .htaccess file. Use --main for erasing the file and --backup for erasing the backup file.';
		WP_CLI::log($welcome_message);
		if ($assoc_args['main']) {
			unlink(__SECURITY_HTACCESS__);
			WP_CLI::success('wp-admin .htaccess cleaned');
		}
		if ($assoc_args['backup']) {
			unlink(__SECURITY_HTACCESS_BACKUP__);
			WP_CLI::success('wp-admin backup.htaccess cleaned');
		}
	}

	/**
	 * restore the admin htaccess from the backup
	 * no arguments
	 * 
	 * @since  0.0.1
	 * @author 
	 */
	public function restoreBackupAdminHtaccess()
	{
		if (!file_exists(__SECURITY_HTACCESS_ADMIN__)) {
			WP_CLI::error('no .htaccess found');
			die;
		}
		unlink(__SECURITY_HTACCESS_ADMIN__);
		copy(__SECURITY_HTACCESS_ADMIN_BACKUP__, __SECURITY_HTACCESS_ADMIN__);
		WP_CLI::success('htaccess backup restored');
	}

	/**
	 * restore the general  htaccess from the backup
	 * no arguments
	 * 
	 * @since  0.0.1
	 * @author 
	 */
	public function restoreBackupHtaccess()
	{
		if (!file_exists(__SECURITY_HTACCESS__)) {
			WP_CLI::error('no .htaccess found');
			die;
		}
		unlink(__SECURITY_HTACCESS__);
		copy(__SECURITY_HTACCESS_BACKUP__, __SECURITY_HTACCESS__);
		WP_CLI::success('htaccess backup restored');
	}

	/**
	 * add ip restriction
	 * arguments : 	'save-ip' -> if you want to save the allowed ip already stored
	 * 				'ip' -> the ip you want to push, string with ip separate by comma (ex : --ip='172.168.1.1, 172.168.1.2')
	 * 
	 * @since  0.0.1
	 * @author 
	 */
	public function ipInsertion($args, $assoc_args)
	{
		if (file_exists(__SECURITY_HTACCESS_ADMIN__)) {
			$ip = explode(',', str_replace(' ', '', $assoc_args['ip']));
			$option_data = unserialize(get_option('_security'));
			if ($assoc_args['save-ip']) {
				$ip_stored = $option_data['ip-limit'];
				$ip = array_merge($ip_stored, $ip);
			}
			$option_data['ip-limit'] = $ip;
			if (!file_exists(__SECURITY_HTACCESS_ADMIN__)) {
				fopen(__SECURITY_HTACCESS_ADMIN__, "w");
			}
			$file = file_get_contents(__SECURITY_HTACCESS_ADMIN__);
			if (strpos($file, "IP restriction end")) {
				$file = HtaccessController::deleteAllBetween("# IP restriction begin don't write within", "# IP restriction end", $file);
				file_put_contents(__SECURITY_HTACCESS_ADMIN__, $file);
			}
			if (!strpos($file, "# IP restriction begin don't write within")) {
				$file = $file . "# IP restriction begin don't write within";
				$file .= "\n";
				$file .= "\norder deny,allow";
				$file .= "\ndeny from all";
				foreach ($ip as $ip_to_write) {
					$file .= "\nallow from " . $ip_to_write;
				}
				$file .= "\n# IP restriction end";
				file_put_contents(__SECURITY_HTACCESS_ADMIN__, $file);
			}

			update_option('_security', serialize($option_data));
			WP_CLI::success('ip stored in .htaccess and wp-option');
		} else {
			WP_CLI::error(NsError::init_cli_error(1));
		}
	}

	/**
	 * Secure access for .htaccess and .htpassword files 
	 * cecurity secureHtfiles to desactivate
	 * argument : 'active'-> bool : activate feature
	 * 			  'desactive'-> bool : desactivate feature
	 * 
	 * @since  0.0.1
	 * @author 
	 */

	public function secureHtfiles($args, $assoc_args)
	{
		if (get_option('_security')) {
			$option_data = unserialize(get_option('_security'));
			$option_data['secure-htfiles'] = false;
			$welcome_message = 'Secure access of wp-config.php. Turn on with --active and off with --desactive.';
			WP_CLI::log($welcome_message);

			if (!file_exists(__SECURITY_HTACCESS__)) {
				fopen(__SECURITY_HTACCESS__, "w");
			}
			$file = file_get_contents(__SECURITY_HTACCESS__);
			if (strpos($file, "Active secure htfiles end")) {
				$file = HtAccessController::deleteAllBetween("\n# Active secure htfiles begin don't write within", "# Active secure htfiles end", $file);
				file_put_contents(__SECURITY_HTACCESS__, $file);
			}

			if ($assoc_args['desactive']) {
				$option_data['secure-htfiles'] = false;
				$message = 'Secure htfiles desactivated';
			}

			if (file_exists(__SECURITY_HTACCESS__)) {

				if ($assoc_args['active']) {
					$file = $file . "\n# Active secure htfiles begin don't write within";
					$file .= "\n<Files ~ \"^.*\.([Hh][Tt][AaPp])\">";
					$file .= "\norder allow,deny";
					$file .= "\ndeny from all";
					$file .= "\nsatisfy all";
					$file .= "\n</Files>";
					$file .= "\n# Active secure htfiles end";
					file_put_contents(__SECURITY_HTACCESS__, $file);
					$option_data['secure-htfiles'] = true;
					$message = 'Secure htfiles activated';
					WP_CLI::success($message);
				}
			} else {
				WP_CLI::error(NsError::init_cli_error(1));
			}
			update_option('_security', serialize($option_data));
		} else {
			WP_CLI::error(NsError::init_cli_error(3));
		}
	}

	/**
	 * Secure access of wp-config.php
	 * cecurity secureWpconfig to desactivate
	 * argument : 'active' -> bool : activate feature
	 * 			  'desactive' -> bool : desactivate feature
	 * 
	 * @since  0.0.1
	 * @author 
	 */

	public function secureWpconfig($args, $assoc_args)
	{
		if (get_option('_security')) {
			$option_data = unserialize(get_option('_security'));
			$option_data['secure-wpconfig'] = false;
			$welcome_message = 'Secure access of wp-config.php. Turn on with --active and off with --desactive.';
			WP_CLI::log($welcome_message);

			if (!file_exists(__SECURITY_HTACCESS__)) {
				fopen(__SECURITY_HTACCESS__, "w");
			}
			$file = file_get_contents(__SECURITY_HTACCESS__);
			if (strpos($file, "Active secure wpconfig end")) {
				$file = HtAccessController::deleteAllBetween("\n# Active secure wpconfig begin don't write within", "# Active secure wpconfig end", $file);
				file_put_contents(__SECURITY_HTACCESS__, $file);
			}

			if ($assoc_args['desactive']) {
				$option_data['secure-wpconfig'] = false;
				$message = 'Secure wp-config.php desactivated';

				WP_CLI::success($message);
			}

			if (file_exists(__SECURITY_HTACCESS__)) {

				if ($assoc_args['active']) {
					$file = $file . "\n# Active secure wpconfig begin don't write within";
					$file .= "\n<Files wp-config.php>";
					$file .= "\norder allow,deny";
					$file .= "\ndeny from all";
					$file .= "\nsatisfy all";
					$file .= "\n</Files>";
					$file .= "\n# Active secure wpconfig end";
					file_put_contents(__SECURITY_HTACCESS__, $file);
					$option_data['secure-wpconfig'] = true;
					$message = 'Secure wp-config.php activated';
					WP_CLI::success($message);
				}
			} else {
				WP_CLI::error(NsError::init_cli_error(1));
			}
			update_option('_security', serialize($option_data));
		} else {
			WP_CLI::error(NsError::init_cli_error(3));
		}
	}

	/**
	 * Secure access of logs
	 * cecurity secureLogs to desactivate
	 * argument : 'active' -> bool : activate feature
	 * 			  'desactive' -> bool : desactivate feature
	 * 
	 * @since  0.0.1
	 * @author 
	 */

	public function secureLogs($args, $assoc_args)
	{
		if (get_option('_security')) {
			$option_data = unserialize(get_option('_security'));
			$option_data['secure-logs'] = false;
			$welcome_message = 'Secure access to logs. Turn on with --active and off with --desactive.';
			WP_CLI::log($welcome_message);

			if (!file_exists(__SECURITY_HTACCESS__)) {
				fopen(__SECURITY_HTACCESS__, "w");
			}
			$file = file_get_contents(__SECURITY_HTACCESS__);
			if (strpos($file, "Active secure logs end")) {
				$file = HtAccessController::deleteAllBetween("\n# Active secure logs begin don't write within", "# Active secure logs end", $file);
				file_put_contents(__SECURITY_HTACCESS__, $file);
			}
			if ($assoc_args['desactive']) {
				$option_data['secure-logs'] = false;
				$message = "Secure logs access desactivated";
				WP_CLI::success($message);
			}
			if (file_exists(__SECURITY_HTACCESS__)) {

				if ($assoc_args['active']) {
					$file = $file . "\n# Active secure logs begin don't write within";
					$file .= "\n<Files ~ \"\.log$\">";
					$file .= "\norder allow,deny";
					$file .= "\ndeny from all";
					$file .= "\n</Files>";
					$file .= "\n# Active secure logs end";
					file_put_contents(__SECURITY_HTACCESS__, $file);
					$option_data['secure-logs'] = true;
					$message = 'Secure logs access activated';
					WP_CLI::success($message);
				}
			} else {
				WP_CLI::error(NsError::init_cli_error(1));
			}
			update_option('_security', serialize($option_data));
		} else {
			WP_CLI::error(NsError::init_cli_error(3));
		}
	}

	/**
	 * Desactivate indexof server side
	 * cecurity hideIndex to desactivate
	 * argument : 'active' -> bool : activate feature
	 * 			  'desactive' -> bool : desactivate feature
	 * 
	 * @since  0.0.1
	 * @author 
	 */

	public function hideIndex($args, $assoc_args)
	{
		if (get_option('_security')) {
			$option_data = unserialize(get_option('_security'));
			$option_data['hide-indexof'] = false;
			$welcome_message = 'Hide index server side. Turn on with --active and off with --desactive.';
			WP_CLI::log($welcome_message);

			if (!file_exists(__SECURITY_HTACCESS__)) {
				fopen(__SECURITY_HTACCESS__, "w");
			}
			$file = file_get_contents(__SECURITY_HTACCESS__);
			if (strpos($file, "Active hide indexof end")) {
				$file = HtAccessController::deleteAllBetween("\n# Active hide indexof begin don't write within", "# Active hide indexof end", $file);
				file_put_contents(__SECURITY_HTACCESS__, $file);
			}

			if ($assoc_args['desactive']) {

				$option_data['hide-indexof'] = false;
				$message = "Hide index desactivated";
				WP_CLI::success($message);
			}

			if (file_exists(__SECURITY_HTACCESS__)) {

				if ($assoc_args['active']) {
					$file = $file . "\n# Active hide indexof begin don't write within";
					$file .= "\nOptions All -Indexes";
					$file .= "\n# Active hide indexof end";
					file_put_contents(__SECURITY_HTACCESS__, $file);
					$option_data['hide-indexof'] = true;
					$message = 'Hide indexof access activated';
					WP_CLI::success($message);
				}
			} else {
				WP_CLI::error(NsError::init_cli_error(1));
			}
			update_option('_security', serialize($option_data));
		} else {
			WP_CLI::error(NsError::init_cli_error(3));
		}
	}

	/**
	 * Prevent Clickjacking/XSS/ MIME-Type sniffing
	 * cecurity preventCXM to desactivate
	 * argument : 'active' -> bool : activate feature
	 * 			  'desactive' -> bool : desactivate feature
	 * 
	 * @since  0.0.1
	 * @author 
	 */

	public function preventCXM($args, $assoc_args)
	{
		if (get_option('_security')) {

			$option_data = unserialize(get_option('_security'));
			$option_data['secure-against-CXM'] = false;
			$welcome_message = 'Prevent Clickjacking/XSS/ MIME-Type sniffing. Turn on with --active and off with --desactive.';
			WP_CLI::log($welcome_message);

			if (!file_exists(__SECURITY_HTACCESS__)) {
				fopen(__SECURITY_HTACCESS__, "w");
			}
			$file = file_get_contents(__SECURITY_HTACCESS__);
			if (strpos($file, "Active secure against C/X/M attack end")) {
				$file = HtAccessController::deleteAllBetween("\n# Active secure against C/X/M attack begin don't write within", "# Active secure against C/X/M attack end", $file);
				file_put_contents(__SECURITY_HTACCESS__, $file);
			}
			if ($assoc_args['desactive']) {

				$option_data['secure-against-CXM'] = false;
				$message = "Prevent CXM attack desactivated";

				WP_CLI::success($message);
			}

			if (file_exists(__SECURITY_HTACCESS__)) {

				if ($assoc_args['active']) {

					$file = $file . "\n# Active secure against C/X/M attack begin don't write within";
					$file .= "\n<ifModule mod_headers.c>";
					$file .= "\nHeader set X-XSS-Protection \"1; mode=block\"";
					$file .= "\nHeader always append X-Frame-Options SAMEORIGIN";
					$file .= "\nHeader set X-Content-Type-Options: \"nosniff\"";
					$file .= "\n</ifModule>";
					$file .= "\n# Active secure against C/X/M attack end";
					file_put_contents(__SECURITY_HTACCESS__, $file);
					$option_data['secure-against-CXM'] = true;
					$message = 'Prevent CXM attacks activated';
					WP_CLI::success($message);
				}
			} else {

				WP_CLI::error(NsError::init_cli_error(1));
			}
			update_option('_security', serialize($option_data));
		} else {
			WP_CLI::error(NsError::init_cli_error(3));
		}
	}

	/**
	 * Prevent file injection
	 * cecurity preventFinjection to desactivate
	 * argument : 'active' -> bool : activate feature
	 *            'desactive' -> bool : desactivate feature
	 * 
	 * @since  0.0.1
	 * @author 
	 */

	public function preventFinjection($args, $assoc_args)
	{
		if (get_option('_security')) {

			$option_data = unserialize(get_option('_security'));
			$option_data['secure-against-finject'] = false;
			$welcome_message = 'Prevent file injection. Turn on with --active and off with --desactive.';
			WP_CLI::log($welcome_message);

			if ($assoc_args['desactive']) {
				if (!file_exists(__SECURITY_HTACCESS__)) {
					fopen(__SECURITY_HTACCESS__, "w");
				}
				$file = file_get_contents(__SECURITY_HTACCESS__);
				if (strpos($file, "Active secure against file injection attack end")) {
					$file = HtAccessController::deleteAllBetween("\n# Active secure against file injection attack begin don't write within", "# Active secure against file injection attack end", $file);
					file_put_contents(__SECURITY_HTACCESS__, $file);
				}
				$option_data['secure-against-finject'] = false;
				$message = "Prevent file injection desactivated";
				WP_CLI::success($message);
			}


			if (file_exists(__SECURITY_HTACCESS__)) {
				if ($assoc_args['active']) {

					$file = file_get_contents(__SECURITY_HTACCESS__);
					if (strpos($file, "Active secure against file injection attack end")) {
						$file = HtAccessController::deleteAllBetween("\n# Active secure against file injection attack begin don't write within", "# Active secure against file injection attack end", $file);
						file_put_contents(__SECURITY_HTACCESS__, $file);
					}

					$file = $file . "\n# Active secure against file injection attack begin don't write within";
					$file .= "\nRewriteCond %{REQUEST_METHOD} GET";
					$file .= "\nRewriteCond %{QUERY_STRING} [a-zA-Z0-9_]=http:// [OR]";
					$file .= "\nRewriteCond %{QUERY_STRING} [a-zA-Z0-9_]=(\.\.//?)+ [OR]";
					$file .= "\nRewriteCond %{QUERY_STRING} [a-zA-Z0-9_]=/([a-z0-9_.]//?)+ [NC]";
					$file .= "\nRewriteRule .* - [F]";
					$file .= "\n# Active secure against file injection attack end";
					file_put_contents(__SECURITY_HTACCESS__, $file);
					$option_data['secure-against-finject'] = true;
					$message = 'Prevent file injection attacks activated';
					WP_CLI::success($message);
				}
			} else {
				fopen(__SECURITY_HTACCESS__, "w");
				WP_CLI::error(NsError::init_cli_error(1));
			}
			update_option('_security', serialize($option_data));
		} else {
			WP_CLI::error(NsError::init_cli_error(3));
		}
	}

	/**
	 * Restrict access to wp-login with a cookie  set in a new page choosen by the user
	 * security restrictAccess to desactivate
	 * arguments : 'active' -> bool : activate feature
	 * 			   'slug' -> string : name who replace wp-login url (ex: string.php)
	 * 			   'desactive' -> bool : desactivate feature
	 * 
	 * @since  0.0.1
	 * @author 
	 */

	public function restrictLoginAccess($args, $assoc_args)
	{
		if ($assoc_args['info']) {
			$message = 'To activate restriction, add a slug who remplace your wp-login page --slug= (string) after --activate; Turn off with --desactive. ';
			WP_CLI::halt($message);
		}

		if (get_option('_security')) {

			$option_data = unserialize(get_option('_security'));
			$option_data['restrict-access'] = false;
			$welcome_message = 'Restrict access to wp-login. You must use --active with args. Use --info for more informations.';
			WP_CLI::log($welcome_message);

			if ($assoc_args['desactive']) {
				if ($option_data['restrict-slug']) {
					$file_to_delete = ABSPATH . '/' . $option_data['restrict-slug'] . '.php';
					unlink($file_to_delete);
					$option_data['restrict-slug'] = false;
				}

				$htfile = file_get_contents(__SECURITY_HTACCESS__);
				if (strpos($htfile, "Active restrict file access end")) {
					$htfile = HtAccessController::deleteAllBetween("\n# Active restrict file access begin don't write within", "# Active restrict file access end", $htfile);
					file_put_contents(__SECURITY_HTACCESS__, $htfile);
				}
				$option_data['restrict-access'] = false;
				$message = "Restrict access to wp-login desactivated";
				WP_CLI::success($message);
			}
			if ($assoc_args['active'] && $assoc_args['slug']) {

				if (file_exists(__SECURITY_HTACCESS__)) {
					$slug = $assoc_args['slug'];
					if ($option_data['restrict-slug']) {
						$file_to_delete = ABSPATH . '/' . $option_data['restrict-slug'] . '.php';
						unlink($file_to_delete);
						$option_data['restrict-slug'] = false;
					}

					if (!file_exists(__SECURITY_HTACCESS__)) {
						fopen(__SECURITY_HTACCESS__, "w");
					}
					$htfile = file_get_contents(__SECURITY_HTACCESS__);
					if (strpos($htfile, "Active restrict file access end")) {
						$htfile = HtAccessController::deleteAllBetween("\n# Active restrict file access begin don't write within", "# Active restrict file access end", $htfile);
						file_put_contents(__SECURITY_HTACCESS__, $htfile);
					}
					$file_name = ABSPATH . '/' . $slug . '.php';
					$file_name_exist = ABSPATH . '/' . 'wp-' . $slug . '.php';
					$file_content = "<?php";
					$file_content .= "\nsetcookie('wp-{$slug}', 2021051519,  time() + 86400);";
					$file_content .= "\nheader('Location: wp-login.php');";

					if (!file_exists($file_name) && !file_exists($file_name_exist)) {
						file_put_contents(
							$file_name,
							$file_content
						);

						$htfile = $htfile . "\n# Active restrict file access begin don't write within";
						$htfile .= "\n<IfModule mod_rewrite.c>";
						$htfile .= "\nRewriteEngine On";
						$htfile .= "\nRewriteBase /";
						$htfile .= "\nRewriteCond %{HTTP_COOKIE} !^.*wp\-{$slug}=2021051519.*$ [NC]";
						$htfile .= "\nRewriteRule wp-login.php - [F]";
						$htfile .= "\n</IfModule>";
						$htfile .= "\n# Active restrict file access end";
						file_put_contents(__SECURITY_HTACCESS__, $htfile);
					} else {
						WP_CLI::error(NsError::init_cli_error(2));
					}
					$option_data['restrict-access'] = true;
					$option_data['restrict-slug'] = $slug;
					$message = "Restrict access to wp-login activated";
					WP_CLI::success($message);
				} else {
					WP_CLI::error(NsError::init_cli_error(1));
				}
			}
			update_option('_security', serialize($option_data));
		} else {
			WP_CLI::error(NsError::init_cli_error(3));
		}
	}



	/*
*---------------------------------------------LIMIT LOGIN CLI COMMANDS
*/

	/**
	 * Activate the limitation logins
	 * arguments : 	'active' -> bool
	 *              'desactive' -> bool
	 * 				'limit-number' -> int between 0 and 50
	 * 				'limit-time' -> int between 0 and 60
	 * 
	 * @since  0.0.1
	 * @author 
	 */

	public function activeLimit($args, $assoc_args)
	{
		if ($assoc_args['info']) {
			$message = 'To activate limitation, add a limit number of connection --limit-number= (0 to 50) and a time limit --limit-time= (0 to 60 min) after --activate;';
			$message .= ' Turn off with --desactive. ';
			WP_CLI::halt($message);
		}
		if (get_option('_security')) {

			$option_data = unserialize(get_option('_security'));
			$option_data['active-limit'] = false;

			$welcome_message = 'Active limit. You must use --active with args.';
			$welcome_message .= ' Use --info for more informations';

			WP_CLI::log($welcome_message);

			if ($assoc_args['desactive']) {
				$option_data['active-limit'] = false;
				$message = 'Login limitation desactivated';
				WP_CLI::success($message);
			}

			if ($assoc_args['active'] && $assoc_args['limit-number'] && $assoc_args['limit-time']) {

				$option_data['limit-number'] = intval($assoc_args['limit-number']);
				WP_CLI::line(sprintf('number of attempts : %d', $assoc_args['limit-number']));

				$option_data['limit-time'] = intval($assoc_args['limit-time']);
				WP_CLI::line(sprintf('duration of blocking : %d minutes', $assoc_args['limit-time']));

				$option_data['active-limit'] = true;
				$message = 'Login limitation activated';
				WP_CLI::success($message);
			}

			update_option('_security', serialize($option_data));
		} else {
			WP_CLI::error(NsError::init_cli_error(3));
		}
	}

	/*
*--------------------------------------------------OTHERS CLI COMMANDS
*/

	/**
	 * Remove wp version  to the header and RSS feed
	 * cecurity rmWpVersion to desactivate
	 * arguments : 'active' -> bool : activate feature
	 *             'desactive' -> bool : desactivate feature
	 * @since  0.0.1
	 * @author 
	 */

	public function rmWpVersion($args, $assoc_args)
	{
		if (get_option('_security')) {

			$option_data = unserialize(get_option('_security'));
			$option_data['active-remove'] = false;
			$welcome_message = 'Remove Wp Version. Turn on with --active and off with --desactive.';

			WP_CLI::log($welcome_message);
			if ($assoc_args['desactive']) {
				$option_data['active-remove'] = false;
				$message = 'Remove Wp version desactivated';
				WP_CLI::success($message);
			}

			if ($assoc_args['active']) {
				$option_data['active-remove'] = true;
				$message = 'Remove Wp version activated';
				WP_CLI::success($message);
			}
			update_option('_security', serialize($option_data));
		} else {
			WP_CLI::error(NsError::init_cli_error(3));
		}
	}


	/**
	 * Restrict API  access to logout visitors and allow roles
	 * cecurity restrictApi to desactivate
	 * arguments : 
	 * 'active' -> bool : activate feature 
	 * 'desactive' -> bool : desactivate feature 
	 * 'firstLevel' -> bool : allow access to administrator role
	 * 'secondLevel' -> bool : allow access to administrator and editor roles
	 * 'thirdLevel' -> bool :  allow access to administrator, editor and author roles
	 * 'fourthLevel' -> bool :  allow access to administrator, editor, author and contributor roles
	 * 			    
	 * 
	 * 
	 * @since  0.0.1
	 * @author 
	 */

	public function restrictApi($args, $assoc_args)
	{
		if ($assoc_args['info']) {
			$message = 'Turn off restriction with --desactive. Choose your restriction level. --firstLevel : access to Administrator; --secondLevel : access to Administrator and Editor; --thirdLevel : access to Administrator, Editor and Author; --fourthLevel : access to Administrator, Editor, Author and Contributor';
			WP_CLI::halt($message);
		}
		if (get_option('_security')) {

			$option_data = unserialize(get_option('_security'));

			$welcome_message = 'Restrict Wp API. You must use --active with a restriction level. Use --info for more informations';
			WP_CLI::log($welcome_message);

			if ($assoc_args['desactive']) {
				$option_data['restrict-api'] = false;
				$option_data['roles-api'] = '';

				$message = ' Restrict access to API desactivated';
				WP_CLI::success($message);
			}

			if ($assoc_args['active']) {
				if ($assoc_args['firstLevel']) {
					$option_data['restrict-api'] = true;
					$option_data['roles-api'] = 'first-level';
					$message = 'Restrict access to API activated : Administrator only';
					WP_CLI::success($message);
				}
				if ($assoc_args['secondLevel']) {
					$option_data['restrict-api'] = true;
					$option_data['roles-api'] = 'second-level';
					$message = 'Restrict access to API activated : Administrator and Editor only';
					WP_CLI::success($message);
				}
				if ($assoc_args['thirdLevel']) {
					$option_data['restrict-api'] = true;
					$option_data['roles-api'] = 'third-level';
					$message = 'Restrict access to API activated : Administrator, Editor and Author only';
					WP_CLI::success($message);
				}
				if ($assoc_args['fourthLevel']) {
					$option_data['restrict-api'] = true;
					$option_data['roles-api'] = 'fourth-level';
					$message = 'Restrict access to API activated : Administrator, Editor, Author et Contributor only';
					WP_CLI::success($message);
				}
			}
			update_option('_security', serialize($option_data));
		} else {
			WP_CLI::error(NsError::init_cli_error(3));
		}
	}
}
