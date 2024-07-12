<?php


use NsError;

/**
 * CLASS HtAccessController
 * Display plugin features impacting htaccess
 */

class HtAccessController
{

    // Check and stock .htaccess and create a backup 


    public static function htAccessFileCreation($htaccess_file, $backup_htaccess_file)
    {

        if (!file_exists($htaccess_file)) {
            fopen($htaccess_file, "w");
        }
        if ($backup_htaccess_file) {
            if (file_exists($backup_htaccess_file)) {
                unlink($backup_htaccess_file);
            }
            copy($htaccess_file, $backup_htaccess_file);
        }
    }

    // Secure access for .htaccess and .htpassword files 

    public static function ipWriting($htaccess_file, $option_data)
    {
        if (file_exists(__SECURITY_HTACCESS_ADMIN__)) {
            $ip_to_filter = preg_split('/\r\n|[\r\n]/', filter_input(INPUT_POST, 'ip-filtre'));

            $option_data['ip-limit'] = $ip_to_filter;
            $file = file_get_contents($htaccess_file);
            if (strpos($file, "IP restriction end")) {
                $file = HtAccessController::deleteAllBetween("# IP restriction begin don't write within", "# IP restriction end", $file);
                file_put_contents($htaccess_file, $file);
            }
            if (!strpos($file, "# IP restriction begin don't write within")) {
                $file = $file . "# IP restriction begin don't write within";
                $file .= "\n";
                $file .= "\nOrder Deny,Allow";
                $file .= "\nDeny from All";
                foreach ($ip_to_filter as $ip_to_write) {
                    $file .= "\nAllow from " . $ip_to_write;
                }
                $file .= "\n# IP restriction end";
                file_put_contents($htaccess_file, $file);
            }
            return $option_data;
        } else {
            return NsError::init_notice('error', NsError::init_error(3));
        }
    }

    // Secure access for .htaccess and .htpassword files 

    public static function secureAccessHtfiles($htaccess_file, $option_data, $secure_htfiles)
    {

        if (file_exists(__SECURITY_HTACCESS__)) {
            $option_data['secure-htfiles'] = $secure_htfiles;

            $file = file_get_contents($htaccess_file);

            if (strpos($file, "Active secure htfiles end")) {
                $file = HtAccessController::deleteAllBetween("\n# Active secure htfiles begin don't write within", "# Active secure htfiles end", $file);
                file_put_contents($htaccess_file, $file);
            }
            if ($secure_htfiles) {
                $file = $file . "\n# Active secure htfiles begin don't write within";
                $file .= "\n<Files ~ \"^.*\.([Hh][Tt][AaPp])\">";
                $file .= "\norder allow,deny";
                $file .= "\ndeny from all";
                $file .= "\nsatisfy all";
                $file .= "\n</Files>";
                $file .= "\n# Active secure htfiles end";
                file_put_contents($htaccess_file, $file);
            }

            return $option_data;
        } else {
            return NsError::init_notice('error', NsError::init_error(3));
        }
    }

    // Secure access of wp-config.php

    public static function secureAccessWpconfig($htaccess_file, $option_data, $secure_wpconfig)
    {
        if (file_exists(__SECURITY_HTACCESS__)) {
            $option_data['secure-wpconfig'] = $secure_wpconfig;
            $file = file_get_contents($htaccess_file);

            if (strpos($file, "Active secure wpconfig end")) {
                $file = HtAccessController::deleteAllBetween("\n# Active secure wpconfig begin don't write within", "# Active secure wpconfig end", $file);
                file_put_contents($htaccess_file, $file);
            }
            if ($secure_wpconfig) {
                $file = $file . "\n# Active secure wpconfig begin don't write within";
                $file .= "\n<Files wp-config.php>";
                $file .= "\norder allow,deny";
                $file .= "\ndeny from all";
                $file .= "\nsatisfy all";
                $file .= "\n</Files>";
                $file .= "\n# Active secure wpconfig end";

                file_put_contents($htaccess_file, $file);
            }
            return $option_data;
        } else {
            return NsError::init_notice('error', NsError::init_error(3));
        }
    }

    // Secure access of logs

    public static function secureAccessLogs($htaccess_file, $option_data, $secure_logs)
    {
        if (file_exists(__SECURITY_HTACCESS__)) {
            $option_data['secure-logs'] = $secure_logs;
            $file = file_get_contents($htaccess_file);

            if (strpos($file, "Active secure logs end")) {
                $file = HtAccessController::deleteAllBetween("\n# Active secure logs begin don't write within", "# Active secure logs end", $file);
                file_put_contents($htaccess_file, $file);
            }
            if ($secure_logs) {
                $file = $file . "\n# Active secure logs begin don't write within";
                $file .= "\n<Files ~ \"\.log$\">";
                $file .= "\norder allow,deny";
                $file .= "\ndeny from all";
                $file .= "\n</Files>";
                $file .= "\n# Active secure logs end";
                file_put_contents($htaccess_file, $file);
            }
            return $option_data;
        } else {
            return NsError::init_notice('error', NsError::init_error(3));
        }
    }

    // Desactivate indexof server side

    public static function hideIndexOf($htaccess_file, $option_data, $hide_index)
    {
        if (file_exists(__SECURITY_HTACCESS__)) {
            $option_data['hide-indexof'] = $hide_index;
            $file = file_get_contents($htaccess_file);

            if (strpos($file, "Active hide indexof end")) {
                $file = HtAccessController::deleteAllBetween("\n# Active hide indexof begin don't write within", "# Active hide indexof end", $file);
                file_put_contents($htaccess_file, $file);
            }
            if ($hide_index) {
                $file = $file . "\n# Active hide indexof begin don't write within";
                $file .= "\nOptions All -Indexes";
                $file .= "\n# Active hide indexof end";
                file_put_contents($htaccess_file, $file);
            }
            return $option_data;
        } else {
            return NsError::init_notice('error', NsError::init_error(3));
        }
    }

    // Prevent Clickjacking/XSS/ MIME-Type sniffing

    public static function secureAgainstCXM($htaccess_file, $option_data, $secure_against_CXM)
    {
        if (file_exists(__SECURITY_HTACCESS__)) {
            $option_data['secure-against-CXM'] = $secure_against_CXM;
            $file = file_get_contents($htaccess_file);

            if (strpos($file, "Active secure against C/X/M attack end")) {
                $file = HtAccessController::deleteAllBetween("\n# Active secure against C/X/M attack begin don't write within", "# Active secure against C/X/M attack end", $file);
                file_put_contents($htaccess_file, $file);
            }
            if ($secure_against_CXM) {
                $file = $file . "\n# Active secure against C/X/M attack begin don't write within";
                $file .= "\n<ifModule mod_headers.c>";
                $file .= "\nHeader set X-XSS-Protection \"1; mode=block\"";
                $file .= "\nHeader always append X-Frame-Options SAMEORIGIN";
                $file .= "\nHeader set X-Content-Type-Options: \"nosniff\"";
                $file .= "\n</ifModule>";
                $file .= "\n# Active secure against C/X/M attack end";
                file_put_contents($htaccess_file, $file);
            }
            return $option_data;
        } else {
            return NsError::init_notice('error', NsError::init_error(3));
        }
    }

    // Prevent file injection

    public static function secureAgainstFinject($htaccess_file, $option_data, $secure_finject)
    {
        if (file_exists(__SECURITY_HTACCESS__)) {
            $option_data['secure-against-finject'] = $secure_finject;
            $file = file_get_contents($htaccess_file);

            if (strpos($file, "Active secure against file injection attack end")) {
                $file = HtAccessController::deleteAllBetween("\n# Active secure against file injection attack begin don't write within", "# Active secure against file injection attack end", $file);
                file_put_contents($htaccess_file, $file);
            }
            if ($secure_finject) {
                $file = $file . "\n# Active secure against file injection attack begin don't write within";
                $file .= "\nRewriteCond %{REQUEST_METHOD} GET";
                $file .= "\nRewriteCond %{QUERY_STRING} [a-zA-Z0-9_]=http:// [OR]";
                $file .= "\nRewriteCond %{QUERY_STRING} [a-zA-Z0-9_]=(\.\.//?)+ [OR]";
                $file .= "\nRewriteCond %{QUERY_STRING} [a-zA-Z0-9_]=([a-z0-9_.]//?)+ [NC]";
                $file .= "\nRewriteRule .* - [F]";
                $file .= "\n# Active secure against file injection attack end";
                file_put_contents($htaccess_file, $file);
            }
            return $option_data;
        } else {
            return NsError::init_notice('error', NsError::init_error(3));
        }
    }

    // Delete all string between elements choosen $beginning and $end

    public static function deleteAllBetween($beginning, $end, $string)
    {
        $beginningPos = strpos($string, $beginning);
        $endPos = strpos($string, $end);
        if ($beginningPos === false || $endPos === false) {
            return $string;
        }

        $textToDelete = substr($string, $beginningPos, ($endPos + strlen($end)) - $beginningPos);

        return HtAccessController::deleteAllBetween($beginning, $end, str_replace($textToDelete, '', $string));
    }

    //Protect wp-admin with cookie : Autorize access by a personalize file (url)

    public static function restrictAccess($htaccess_file, $option_data, $restrict_slug)
    {
        if (file_exists(__SECURITY_HTACCESS__)) {
            $slug = $restrict_slug;
            $option_data['restrict-access'] = true;
            $file_name = ABSPATH . '/' . $slug . '.php';
            $file_name_exist = ABSPATH . '/' . 'wp-' . $slug . '.php';
            $file_content = "<?php";
            $file_content .= "\n\$_COOKIE['wp-{$slug}'] = 2021051519;";
            $file_content .= "\nsetcookie('wp-{$slug}', 2021051519,  time() + 86400);";
            $file_content .= "\nheader('Location: wp-login.php');";

            $htfile = file_get_contents($htaccess_file);
            if (strpos($htfile, "Active restrict file access end")) {
                $htfile = HtAccessController::deleteAllBetween("\n# Active restrict file access begin don't write within", "# Active restrict file access end", $htfile);
                file_put_contents($htaccess_file, $htfile);
            }

            if (!file_exists($file_name) && !file_exists($file_name_exist)) {
                file_put_contents(
                    $file_name,
                    $file_content
                );
                $oldhtfile = $htfile;
                $htfile = "\n# Active restrict file access begin don't write within";
                $htfile .= "\n<IfModule mod_rewrite.c>";
                $htfile .= "\nRewriteEngine On";
                $htfile .= "\nRewriteBase /";
                $htfile .= "\nRewriteCond %{HTTP_COOKIE} !^.*wp\-{$slug}=2021051519.*$ [NC]";
                $htfile .= "\nRewriteRule wp-login.php - [F]";
                $htfile .= "\n</IfModule>";
                $htfile .= "\nRewriteRule ^$slug$ /{$slug}.php [L]";
                $htfile .= "\n# Active restrict file access end";
                $htfile .= "\n" . $oldhtfile; 
                file_put_contents($htaccess_file, $htfile);
                return $option_data;
            } else {
                NsError::init_notice('error', NsError::init_error(4));
                return $option_data;
            }
        } else {
            return NsError::init_notice('error', NsError::init_error(3));
        }
    }

    // Clear the restrictAccess cookie after logout

    public static function cleanRestrictAccessCookie()
    {
        $option_data = unserialize(get_option('_security'));
        $slug = 'wp-' . $option_data['restrict-slug'];
        if (isset($_COOKIE[$slug])) {
            unset($_COOKIE[$slug]);
            setcookie($slug, null, -1);
        }
    }

    /* Block user enum via uri */

    public static function blockUserEnum($htaccess_file, $option_data, $bool) {
        
        if (file_exists(__SECURITY_HTACCESS__)) {
            $option_data['block-user-enum'] = $bool;
            $file = file_get_contents($htaccess_file);

            if (strpos($file, "Active block user enumeration end")) {
                $file = HtAccessController::deleteAllBetween("\n# Active block user enumeration begin don't write within", "# Active block user enumeration end", $file);
                file_put_contents($htaccess_file, $file);
            }
            if ($bool) {
                $file = $file . "\n# Active block user enumeration begin don't write within";
                $file .= "\n<IfModule mod_rewrite.c>";
                $file .= "\nRewriteCond %{QUERY_STRING} ^author=([0-9]*) [NC]";
                $file .= "\nRewriteRule .* ". site_url() ."/? [L,R=302]";
                $file .= "\n</IfModule>";
                $file .= "\n# Active block user enumeration end";
                file_put_contents($htaccess_file, $file);
            }
            return $option_data;
        } else {
            return NsError::init_notice('error', NsError::init_error(3));
        }

    }


}
