<?php
/**
 * Plugin Name:       Review Content Type
 * Plugin URI:        https://github.com/chetanchauhan/review-content-type/
 * Description:       Create and manage reviews easily with this feature-rich, extendable, powerful and free WordPress review plugin the right way.
 * Version:           1.0.2
 * Author:            Chetan Chauhan
 * Author URI:        https://github.com/chetanchauhan/
 * Text Domain:       review-content-type
 * Domain Path:       /languages
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 *
 * Copyright (c) 2015-2016 Chetan Chauhan (email : chetanchauhan1991@gmail.com)
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
	 * @since  1.0.0
	 * @access public
	 * @var    RCT_Settings
	 */
	public $settings = null;

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
			self::$instance->add_image_sizes();

			register_activation_hook( __FILE__, array( 'RCT_Activate', 'activate' ) );
			register_deactivation_hook( __FILE__, array( 'RCT_Deactivate', 'deactivate' ) );
			add_action( 'plugins_loaded', array( self::$instance, 'load_plugin_textdomain' ) );
			add_action( 'init', array( self::$instance, 'init' ), 0 );
			add_filter( 'post_type_link', array( self::$instance, 'review_post_type_link' ), 10, 2 );

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
		_doing_it_wrong( __FUNCTION__, esc_html( __( 'Cheatin&#8217; huh?', 'review-content-type' ) ), '1.0.0' );
	}

	/**
	 * Throw error on unserializing instances of this class.
	 *
	 * @since  1.0.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, esc_html( __( 'Cheatin&#8217; huh?', 'review-content-type' ) ), '1.0.0' );
	}

	/**
	 * Define constants for the plugin.
	 *
	 * @since  1.0.0
	 * @access private
	 */
	private function define_constants() {
		// Set the plugin version.
		define( 'RCT_VERSION', '1.0.2' );

		// Set the plugin root file.
		define( 'RCT_FILE', __FILE__ );

		// Set the plugin directory path.
		define( 'RCT_DIR', plugin_dir_path( __FILE__ ) );

		// Set the plugin directory URL.
		define( 'RCT_URL', plugin_dir_url( __FILE__ ) );
	}

	/**
	 * Loads the required files.
	 *
	 * @since  1.0.0
	 * @access private
	 */
	private function includes() {
		require_once( RCT_DIR . 'includes/class-rct-settings.php' );
		require_once( RCT_DIR . 'includes/class-rct-activate.php' );
		require_once( RCT_DIR . 'includes/class-rct-deactivate.php' );
		require_once( RCT_DIR . 'includes/class-rct-post-types.php' );
		require_once( RCT_DIR . 'includes/class-rct-scripts.php' );
		require_once( RCT_DIR . 'includes/rct-template-functions.php' );
		require_once( RCT_DIR . 'includes/rct-functions.php' );

		if ( is_admin() ) {
			require_once( RCT_DIR . 'includes/admin/class-rct-admin.php' );
		}
	}

	/**
	 * @since 1.0.0
	 */
	public function init() {
		self::$instance->settings = new RCT_Settings();

		// Upgrade plugin to latest version.
		if ( get_option( 'rct_version' ) !== RCT_VERSION ) {
			RCT_Activate::activate();
		}

		do_action( 'rct_init', self::$instance );
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

	/**
	 * Add custom image sizes.
	 *
	 * @since  1.0.0
	 * @access private
	 */
	private function add_image_sizes() {
		add_image_size( 'rct_featured_image', 320, 200, true );
		add_image_size( 'rct_small', 60, 60, true );
	}

	/**
	 * Filters the permalink for review post type.
	 *
	 * @since   1.0.0
	 *
	 * @param string  $permalink The post's permalink.
	 * @param WP_Post $post The post in question.
	 *
	 * @return string
	 */
	public function review_post_type_link( $permalink, $post ) {
		if ( 'review' !== $post->post_type || false === strpos( $permalink, '%' ) ) {
			return $permalink;
		}

		$review_category = '';
		if ( false !== strpos( $permalink, '%review_category%' ) ) {
			$terms = get_the_terms( $post->ID, 'review_category' );
			if ( $terms && ! is_wp_error( $terms ) ) {
				$term_object     = get_term( array_shift( $terms ), 'review_category' );
				$review_category = $term_object->slug;
				while ( $term_object->parent != '0' ) {
					$term_object     = get_term( $term_object->parent, 'review_category' );
					$review_category = $term_object->slug . '/' . $review_category;
				}
			}
			if ( empty( $review_category ) ) {
				$review_category = _x( 'uncategorized', 'slug', 'review-content-type' );
			}
		}

		$find      = array(
			'%year%',
			'%monthnum%',
			'%day%',
			'%hour%',
			'%minute%',
			'%second%',
			'%post_id%',
			'%category%',
			'%review_category%',
		);
		$post_date = strtotime( $post->post_date );
		$replace   = array(
			date_i18n( 'Y', $post_date ),
			date_i18n( 'm', $post_date ),
			date_i18n( 'd', $post_date ),
			date_i18n( 'H', $post_date ),
			date_i18n( 'i', $post_date ),
			date_i18n( 's', $post_date ),
			$post->ID,
			$review_category,
			$review_category,
		);
		$permalink = str_replace( $find, $replace, $permalink );

		return apply_filters( 'rct_review_post_type_link', $permalink, $post );
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
