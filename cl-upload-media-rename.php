<?php
/**
 * @link              https://tabernawp.com/
 * @since             0.1.0
 * @package           cl-upload-media-rename
 *
 * @wordpress-plugin
 * Plugin Name:       CL Upload Media Rename
 * Plugin URI:        https://tabernawp.com/
 * Description:       Sanitize media file names on upload and set ALT for image with previous file name.
 * Version:           0.1.0
 * Author:            Carlos Longarela
 * Author URI:        https://tabernawp.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       cl-upload-media-rename
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Change media file name to a safe and sanitized name.
 *
 * @param string $filename     File name once WP first filter applied.
 * @param string $filename_raw Raw file name on upload.
 *
 * @return string
 *
 * @since 0.1.0
 */
function cl_uir_file_name( $filename, $filename_raw ) {
	$info           = pathinfo( $filename_raw );
	$file_name = $info['filename'];

	if ( ! empty( $info['extension'] ) ) {
		$ext = $info['extension'];
	} else {
		$ext = '';
	}

	$file_name = remove_accents( $file_name );
	$file_name = str_replace( '_', '-', $file_name );
	$file_name = str_replace( '%20', '-', $file_name );
	$file_name = sanitize_title( $file_name );
	$file_name = $file_name . '.' . $ext;

	return $file_name;
}
add_filter( 'sanitize_file_name', 'cl_uir_file_name', 10, 2 );

/**
 * Add ALT to images with previous photo file name.
 *
 * @param int    $meta_id    Metadata Id.
 * @param int    $post_id    Post Id del.
 * @param string $meta_key   Meta key.
 * @param mixed  $meta_value Meta value.
 *
 * @return void
 *
 * @since 0.1.0
 */
function cl_uir_alt_after_post_meta( $meta_id, $post_id, $meta_key, $meta_value ) {
	if ( '_wp_attachment_metadata' === $meta_key ) {
		$title = get_the_title( $post_id ); // Get file title.

		// Update ALT text.
		update_post_meta( $post_id, '_wp_attachment_image_alt', $title );
	}
}
add_action( 'added_post_meta', 'cl_uir_alt_after_post_meta', 10, 4 );
