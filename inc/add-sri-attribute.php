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
 * Class to add sri attribute on script and css
 */
class SriAttribute {

    const PREFIX = 'wp_ns_';

    /**
     * Options array of excluded asset URLs.
     *
     * @var integer
     */
    private $sri_exclude;


    /**
     * Constructor
     */
    public function __construct() {

        // Grab our exclusion array from the options table.
        $this->sri_exclude = get_option( self::PREFIX . 'excluded_hashes', array() );
        add_filter( 'style_loader_tag', array($this, 'filterTag'), 999999, 3 );
        add_filter( 'script_loader_tag', array($this, 'filterTag'), 999999, 3 );
    }

    /**
     * Filters a given tag, possibly adding an `integrity` attribute.
     * original code by https://github.com/fabacab/wp-sri/blob/master/wp-sri.php
     *
     * @see https://developer.wordpress.org/reference/hooks/style_loader_tag/
     * @see https://developer.wordpress.org/reference/hooks/script_loader_tag/
     *
     * @param string $tag tag.
     * @param string $handle handle.
     * @param string $url url.
     *
     * @return string The original HTML tag or its augmented version.
     */
    public function filterTag( $tag, $handle, $url ) {
        // Only do the thing if it makes sense to do so.
        // (It doesn't make sense for non-ssl pages or local resources on live sites,
        // but it always makes sense to do so in debug mode.).
        if ( ! WP_DEBUG && ( ! is_ssl() || $this->isLocalResource( $url ) ) ) {
            return $tag;
        }

        $known_hashes = get_option( self::PREFIX . 'known_hashes', array() );
        if ( empty( $known_hashes[ $url ] ) ) {
            $resp = $this->fetchResource( $url );
            if ( is_wp_error( $resp ) ) {
                return $tag; // TODO: Handle this in some other way?
            } else {
                $known_hashes[ $url ] = $this->hashResource( $resp['body'] );
                update_option( self::PREFIX . 'known_hashes', $known_hashes );
            }
        }

        return $this->addIntegrityAttribute( $tag, $url );
    }

    /**
     * Appends a proper SRI attribute to an element's attribute list.
     *
     * @param string $tag The HTML tag to add the attribute to.
     * @param string $url The URL of the resource to find the hash for.
     * @return string The HTML tag with an integrity attribute added.
     */
    public function addIntegrityAttribute( $tag, $url ) {
        // If $url is found in our excluded array, return $tag unchanged.
        if ( false !== array_search( esc_url( $url ), $this->sri_exclude, true ) ) {
            return $tag;
        }
        $known_hashes  = get_option( self::PREFIX . 'known_hashes', array() );
        $sri_att       = ' crossorigin="anonymous" integrity="sha256-' . $known_hashes[ $url ] . '"';
        $insertion_pos = strpos( $tag, '>' );
        // account for self-closing tags.
        if ( 0 === strpos( $tag, '<link ' ) ) {
            --$insertion_pos;
            $sri_att .= ' ';
        }
        return substr( $tag, 0, $insertion_pos ) . $sri_att . substr( $tag, $insertion_pos );
    }

    /**
     * Checks a URL to determine whether or not the resource is "remote"
     * (served by a third-party) or whether the resource is local (and
     * is being served by the same webserver as this plugin is run on.)
     *
     * @param string $uri The URI of the resource to inspect.
     * @return bool True if the resource is local, false if the resource is remote.
     */
    public static function isLocalResource( $uri ) {
        $rsrc_host = wp_parse_url( $uri, PHP_URL_HOST );
        $this_host = wp_parse_url( get_site_url(), PHP_URL_HOST );
        return ( 0 === strpos( $rsrc_host, $this_host ) ) ? true : false;
    }

    /**
     * Fetch external ressources
     *
     * @param mixed $rsrc_url string.
     *
     * @return base64 content
     */
    public function fetchResource( $rsrc_url ) {
        $url = ( 0 === strpos( $rsrc_url, '//' ) )
            ? ( ( is_ssl() ) ? "https:$rsrc_url" : "http:$rsrc_url" )
            : $rsrc_url;
        return wp_remote_get( $url );
    }

    /**
     * Hash resources.
     *
     * @param mixed $content content.
     *
     * @return [type]
     */
    public function hashResource( $content ) {
        return base64_encode( hash( 'sha256', $content, true ) );
    }
}

new SecurityAdminPanel();
