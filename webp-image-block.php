<?php

/**
 *
 * @link              kodeknight.com
 * @since             1.0.0
 * @package           Webp_Image_Block
 *
 * @wordpress-plugin
 * Plugin Name:       Webp Image Block
 * Plugin URI:        kodeknight.com
 * Description:       This plugin adds an extra image widget in Elementor that converts the added image to webp and loads webp image on front end if the browsers supports webp images.
 * Version:           1.0.1
 * Author:            Jahan Zeb Khan
 * Author URI:        kodeknight.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       webp-image-block
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 */
define( 'WEBP_IMAGE_BLOCK_VERSION', '1.0.1' );
define('WEBP_IMAGES_DIR_NAME','kk-webp-block-images');
define('WEBP_IMAGES_DIR', wp_upload_dir()['basedir'].'/'.WEBP_IMAGES_DIR_NAME);
define('WEBP_IMAGES_URL', wp_upload_dir()['baseurl'].'/'.WEBP_IMAGES_DIR_NAME);

/**
 * The code that runs during plugin activation.
 */
function activate_webp_image_block() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-webp-image-block-activator.php';
	Webp_Image_Block_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_webp_image_block() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-webp-image-block-deactivator.php';
	Webp_Image_Block_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_webp_image_block' );
register_deactivation_hook( __FILE__, 'deactivate_webp_image_block' );



include_once(plugin_dir_path( __FILE__ ) . 'includes/class-gd-converter.php');
include_once(plugin_dir_path( __FILE__ ) . 'includes/class-imagick-converter.php');

if ( did_action( 'elementor/loaded' ) )
require plugin_dir_path( __FILE__ ) . 'includes/blocks/elementor/custom-el-widgets.php';



function author_admin_notice(){
    global $pagenow;

		$user = wp_get_current_user();
		if (!extension_loaded('imagick') || !class_exists('Imagick')) {
		echo '<div class="notice notice-error">'.
			'<p><b>Webp Image Block:</b> Required Imagick module is not available with this PHP installation.</p></div>';
		}

}
add_action('admin_notices', 'author_admin_notice');