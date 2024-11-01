<?php

/**
 * Fired during plugin activation
 *
 * @link       kodeknight.com
 * @since      1.0.0
 *
 * @package    Webp_Image_Block
 * @subpackage Webp_Image_Block/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Webp_Image_Block
 * @subpackage Webp_Image_Block/includes
 * @author     Jahan Zeb Khan <info@kodeknight.com>
 */
class Webp_Image_Block_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		$path = WEBP_IMAGES_DIR;
		if (!file_exists($path) && is_writable(dirname($path))) mkdir($path);

	}

}
