<?php
/*
Plugin Name: Merge query string
Plugin URI: https://fs.fnkr.net/f7d0bb6630134aa19394
Description: Merge query string of request and target URL.
Version: 2.2
Author: fnkr
Author URI: https://www.fnkr.net
License: Creative Commons Attribution 3.0 Unported: https://creativecommons.org/licenses/by/3.0/
*/

yourls_add_action( 'redirect_shorturl', 'fnkr_merge_query_string_enable' );
yourls_add_filter( 'redirect_location', 'fnkr_merge_query_string' );

// redirect_location applies to every redirect, so only rewrite the next redirect after a short URL is resolved
function fnkr_merge_query_string_enable() {
    global $fnkr_merge_query_string_enabled;
    $fnkr_merge_query_string_enabled = true;
}

/**
 * Adds the short URL request's query parameters to the target URL. Parameters already present in the
 * target URL take precedence, and any fragment on the target URL is preserved.
 */
function fnkr_merge_query_string($location) {
    global $fnkr_merge_query_string_enabled;
    if (empty($fnkr_merge_query_string_enabled))
        return $location;
    $fnkr_merge_query_string_enabled = false;

    if (empty($_SERVER['QUERY_STRING']))
        return $location;

    parse_str($_SERVER['QUERY_STRING'], $query);

    $fragment = '';
    if (($pos = strpos($location, '#')) !== false) {
        $fragment = substr($location, $pos);
        $location = substr($location, 0, $pos);
    }

    $url_query = [];
    if (($pos = strpos($location, '?')) !== false) {
        parse_str(substr($location, $pos + 1), $url_query);
        $location = substr($location, 0, $pos);
    }

    return $location.'?'.http_build_query($url_query + $query).$fragment;
}
