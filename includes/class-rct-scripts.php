<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class RCT_Scripts
 */
class RCT_Scripts {

	/**
	 * Script suffix to use.
	 *
	 * @since   1.0.0
	 * @access  protected
	 * @var string
	 */
	protected $suffix = '';

	/**
	 * Initializes the object instance.
	 *
	 * @since     1.0.0
	 */
	public function __construct() {
		if ( defined( 'SCRIPT_DEBUG' ) && ! SCRIPT_DEBUG ) {
			$this->suffix = '.min';
		}

		// Load public-facing style sheet and JavaScript.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since  1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( 'review-content-type-frontend', RCT_URL . "assets/css/frontend{$this->suffix}.css", array( 'dashicons' ), RCT_VERSION, 'all' );
	}

	/**
	 * Register and enqueue public-facing JavaScript files.
	 *
	 * @since  1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( 'review-content-type-frontend', RCT_URL . "assets/js/frontend{$this->suffix}.js", array( 'jquery' ), RCT_VERSION, false );
	}

	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @since       1.0.0
	 */
	public function enqueue_admin_styles() {
		wp_enqueue_style( 'review-content-type-admin', RCT_URL . "assets/css/admin{$this->suffix}.css", array(), RCT_VERSION, 'all' );
		wp_enqueue_style( 'rct-jquery-ui-slider', RCT_URL . "assets/css/jquery-ui-slider{$this->suffix}.css", array(), RCT_VERSION, 'all' );
	}

	/**
	 * Register and enqueue admin-specific JavaScript.
	 *
	 * @since     1.0.0
	 */
	public function enqueue_admin_scripts() {
		wp_enqueue_script( 'review-content-type-admin', RCT_URL . "assets/js/admin{$this->suffix}.js", array(
			'jquery',
			'jquery-ui-slider',
		), RCT_VERSION, false );
	}

}

new RCT_Scripts();
