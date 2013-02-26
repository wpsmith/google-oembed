<?php
/**
 * Plugin Name: Google oEmbed
 * Plugin URI: https://wpsmith.net
 * Description: Adds Google Drive and Maps to the oEmbed functionality.
 * Version: 1.0.0
 * Author: Travis Smith, Samuel Wood (Otto)
 * Author URI: http://wpsmith.net
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU 
 * General Public License version 2, as published by the Free Software Foundation.  You may NOT assume 
 * that you can use any other version of the GPL.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without 
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 */

add_action('plugins_loaded','wpgo_add_google_maps_docs');
/**
 * Registers The Google Maps & The Google Drive oEmbed handlers.
 * Google Maps & Google Drive does not support oEmbed.
 *
 * @see WP_Embed::register_handler()
 * @see wp_embed_register_handler()
 *
 */
function wpgo_add_google_maps_docs() {
	wp_embed_register_handler( 'googlemaps', '#https?://maps.google.com/(maps)?.+#i', 'wpgo_embed_handler_googlemaps' );
	wp_embed_register_handler( 'googledocs', '#https?://docs.google.com/(document|spreadsheet|presentation)/.*#i', 'wpgo_embed_handler_googledrive' );
}

/**
 * The Google Maps embed handler callback. Google Maps does not support oEmbed.
 *
 * @see WP_Embed::register_handler()
 * @see WP_Embed::shortcode()
 *
 * @param array $matches The regex matches from the provided regex when calling {@link wp_embed_register_handler()}.
 * @param array $attr Embed attributes.
 * @param string $url The original URL that was matched by the regex.
 * @param array $rawattr The original unmodified attributes.
 * @return string The embed HTML.
 */
function wpgo_embed_handler_googlemaps( $matches, $attr, $url, $rawattr ) {
	if ( ! empty( $rawattr['width'] ) && ! empty( $rawattr['height'] ) ) {
		$width  = (int) $rawattr['width'];
		$height = (int) $rawattr['height'];
	} else {
		list( $width, $height ) = wp_expand_dimensions( 425, 326, $attr['width'], $attr['height'] );
	}
	return apply_filters( 'embed_googlemaps', "<iframe width='{$width}' height='{$height}' frameborder='0' scrolling='no' marginheight='0' marginwidth='0' src='{$url}&output=embed'></iframe>" );
}

/**
 * The Google Drive embed handler callback. Google Drive does not support oEmbed.
 * Handles documents, spreadsheets, and presentations from Google Drive.
 *
 * @see WP_Embed::register_handler()
 * @see WP_Embed::shortcode()
 *
 * @param array $matches The regex matches from the provided regex when calling {@link wp_embed_register_handler()}.
 * @param array $attr Embed attributes.
 * @param string $url The original URL that was matched by the regex.
 * @param array $rawattr The original unmodified attributes.
 * @return string The embed HTML.
 */
function wpgo_embed_handler_googledrive( $matches, $attr, $url, $rawattr ) {
	if ( !empty($rawattr['width']) && !empty($rawattr['height']) ) {
		$width  = (int) $rawattr['width'];
		$height = (int) $rawattr['height'];
	} else {
		list( $width, $height ) = wp_expand_dimensions( 425, 344, $attr['width'], $attr['height'] );
	}
	
	$extra = '';
	if ( $matches[1] == 'spreadsheet' ) {
		$url .= '&widget=true';
	} elseif ( $matches[1] == 'document' ) {
		$url .= '?embedded=true';
	} elseif ( $matches[1] == 'presentation' ) {
		$url = str_replace( '/pub', '/embed', $url);
		$extra = 'allowfullscreen="true" mozallowfullscreen="true" webkitallowfullscreen="true"';
	}

	return apply_filters( 'embed_googledrive', "<iframe width='{$width}' height='{$height}' frameborder='0' src='{$url}' {$extra}></iframe>" );
}