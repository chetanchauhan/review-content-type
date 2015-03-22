<?php
/**
 * Plugin Name:       Review Content Type
 * Plugin URI:        https://github.com/chetanchauhan/review-content-type/
 * Description:       Create and manage reviews easily with this feature-rich, extendable and powerful WordPress plugin.
 * Version:           1.0.0
 * Author:            Chetan Chauhan
 * Author URI:        https://github.com/chetanchauhan/
 * Text Domain:       review-content-type
 * Domain Path:       /languages
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 *
 * Copyright (c) 2015 Chetan Chauhan (email : chetanchauhan1991@gmail.com)
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class Review_Content_Type
 */
final class Review_Content_Type {

	/**
	 * Holds the instance of this class.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    object
	 */
	private static $instance = null;

	/**
	 * Return an instance of this class.
	 *
	 * @since  1.0.0
	 * @return object A single instance of this class.
	 */
	public static function instance() {
		// If the single instance hasn't been set, set it now.
		if ( null === self::$instance ) {
			self::$instance = new self();
			self::$instance->define_constants();
			self::$instance->includes();

			register_activation_hook( __FILE__, array( 'RCT_Activate', 'activate' ) );
			register_deactivation_hook( __FILE__, array( 'RCT_Deactivate', 'deactivate' ) );
			add_action( 'plugins_loaded', array( self::$instance, 'load_plugin_textdomain' ) );

			do_action( 'rct_loaded', self::$instance );
		}

		return self::$instance;
	}

	/**
	 * Initializes the object instance.
	 *
	 * This is intentionally left public and empty.
	 *
	 * @since  1.0.0
	 */
	public function __construct() {
	}

	/**
	 * Throw error on object cloning.
	 *
	 * @since  1.0.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'review-content-type' ), '1.0.0' );
	}

	/**
	 * Throw error on unserializing instances of this class.
	 *
	 * @since  1.0.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'review-content-type' ), '1.0.0' );
	}

	/**
	 * Define constants for the plugin.
	 *
	 * @since  1.0.0
	 * @access private
	 */
	private function define_constants() {

		// Set the plugin version.
		define( 'RCT_VERSION', '1.0.0' );

		// Set the plugin root file.
		define( 'RCT_FILE', __FILE__ );

		// Set the plugin directory path.
		define( 'RCT_DIR', plugin_dir_path( __FILE__ ) );

		// Set the plugin directory URL.
		define( 'RCT_URL', plugin_dir_url( __FILE__ ) );

		// Set the plugin includes directory path.
		define( 'RCT_INCLUDES', RCT_DIR . trailingslashit( 'includes' ) );

		// Set the plugin admin directory path.
		define( 'RCT_ADMIN_INCLUDES', RCT_INCLUDES . trailingslashit( 'admin' ) );

	}

	/**
	 * Loads the required files.
	 *
	 * @since  1.0.0
	 * @access private
	 */
	private function includes() {
		require_once( RCT_INCLUDES . 'class-rct-activate.php' );
		require_once( RCT_INCLUDES . 'class-rct-deactivate.php' );
		require_once( RCT_INCLUDES . 'class-rct-scripts.php' );
		require_once( RCT_INCLUDES . 'rct-functions.php' );
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since  1.0.0
	 */
	public function load_plugin_textdomain() {
		$locale = apply_filters( 'plugin_locale', get_locale(), 'review-content-type' );
		load_textdomain( 'review-content-type', WP_LANG_DIR . '/review-content-type/review-content-type-' . $locale . '.mo' );
		load_plugin_textdomain( 'review-content-type', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

}

/**
 * Returns the one true Review_Content_Type instance.
 *
 * Use this function like you would a global variable, except
 * this prevents the need to use globals.
 *
 * Example: `<?php $rct = review_content_type(); ?>`
 *
 * @since  1.0.0
 * @return Review_Content_Type
 */
function review_content_type() {
	return Review_Content_Type::instance();
}

// Start the plugin.
review_content_type();
