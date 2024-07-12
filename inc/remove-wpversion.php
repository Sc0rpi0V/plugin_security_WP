<?php

/**
 * CLASS removeWpVersion
 * Hide Wordpress version into the project. Prevent attack targeted on version vulnerabilities.
 */


class RemoveWpVersion
{

    /**
     * Remove Wp version to the header and RSS feed
     */
    public static function removeToHeaderRss()
    {
        return '';
    }

    /**
     * Remove Wp version to the Scripts and CSS
     */
    public static function removeToScriptCss($src)
    {
        if (strpos($src, '?ver=')) {
            $src = remove_query_arg('ver', $src);
        }
        return $src;
    }
}
