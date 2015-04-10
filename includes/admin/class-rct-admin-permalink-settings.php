<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class RCT_Admin_Permalink_Settings
 */
class RCT_Admin_Permalink_Settings {

	/**
	 * @since   1.0.0
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_init', array( $this, 'save_settings' ) );
	}

	/**
	 * Adds custom settings section to the permalinks page.
	 *
	 * @since   1.0.0
	 */
	public function register_settings() {
		add_settings_section( 'rct-permalink-settings', __( 'Review Permalink Settings', 'review-content-type' ), array( $this, 'display_settings' ), 'permalink' );
	}

	/**
	 * Display the permalink settings.
	 *
	 * @since   1.0.0
	 */
	public function display_settings() {
		$settings            = get_option( 'rct_permalink_settings' );
		$default_review_base = _x( 'reviews', 'slug', 'review-content-type' );
		$review_base         = empty( $settings['review_base'] ) ? $default_review_base : $settings['review_base'];
		$category_base       = isset( $settings['category_base'] ) ? $settings['category_base'] : '';
		$tag_base            = isset( $settings['tag_base'] ) ? $settings['tag_base'] : '';
		$structures          = array(
			0 => $default_review_base,
			1 => trailingslashit( $default_review_base ) . '%review_category%',
		);
		wp_nonce_field( 'rct_permalink_settings', '_rct_permalink_settings_nonce' );
		?>
		<table class="form-table">
			<tr>
				<th><?php _e( 'Review base', 'review-content-type' ); ?></th>
				<td>
					<ul id="rct-permalink-settings-selection" class="rct-inline-list">
						<li><label><input name="rct_permalink_settings[selection]" type="radio" value="<?php echo esc_attr( $structures[0] ); ?>" <?php checked( $structures[0], $review_base ); ?> /> <?php _e( 'Default', 'review-content-type' ); ?> </label></li>
						<li><label><input name="rct_permalink_settings[selection]" type="radio" value="<?php echo esc_attr( $structures[1] ); ?>" <?php checked( $structures[1], $review_base ); ?> /> <?php _e( 'Review with category', 'review-content-type' ); ?> </label></li>
						<li><label><input name="rct_permalink_settings[selection]" type="radio" value="" <?php checked( ! in_array( $review_base, $structures ), true ); ?> /> <?php _e( 'Custom', 'review-content-type' ); ?> </label></li>
					</ul>
					<input name="rct_permalink_settings[review_base]" id="rct-permalink-settings-review-base" class="regular-text code" type="text" value="<?php echo esc_attr( $review_base ); ?>" placeholder="<?php echo esc_attr( $default_review_base ) ?>" <?php if ( in_array( $review_base, $structures ) ) { echo 'readonly=readonly'; } ?> />
				</td>
			</tr>
			<tr>
				<th><label for="rct-permalink-settings-category-base"><?php _e( 'Review category base', 'review-content-type' ); ?></label></th>
				<td>
					<input name="rct_permalink_settings[category_base]" id="rct-permalink-settings-category-base" class="regular-text code" type="text" value="<?php echo esc_attr( $category_base ); ?>" placeholder="<?php echo esc_attr( _x( 'reviews/category', 'slug', 'review-content-type' ) ) ?>"/>
				</td>
			</tr>
			<tr>
				<th><label for="rct-permalink-settings-tag-base"><?php _e( 'Review tag base', 'review-content-type' ); ?></label></th>
				<td>
					<input name="rct_permalink_settings[tag_base]" id="rct-permalink-settings-tag-base" class="regular-text code" type="text" value="<?php echo esc_attr( $tag_base ); ?>" placeholder="<?php echo esc_attr( _x( 'reviews/tag', 'slug', 'review-content-type' ) ) ?>"/>
				</td>
			</tr>
		</table>
	<?php
	}

	/**
	 * Save the permalink settings.
	 *
	 * @since   1.0.0
	 */
	public function save_settings() {
		if ( ! isset( $_POST['rct_permalink_settings'] ) || ! isset( $_POST['_rct_permalink_settings_nonce'] ) || ! wp_verify_nonce( $_POST['_rct_permalink_settings_nonce'], 'rct_permalink_settings' ) ) {
			return;
		}

		$settings = array_map( 'sanitize_text_field', $_POST['rct_permalink_settings'] );
		update_option( 'rct_permalink_settings', $settings );
	}
}

new RCT_Admin_Permalink_Settings();
