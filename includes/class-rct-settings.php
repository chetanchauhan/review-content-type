<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class RCT_Settings
 */
class RCT_Settings {

	/**
	 * Option name with which all the settings are stored in the database.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    string
	 */
	protected $option_name = 'rct_settings';

	/**
	 * Holds all the registered settings sections and fields.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    array
	 */
	protected $settings;

	/**
	 * Holds all the registered tabs for the Settings Page.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    array
	 */
	protected $tabs;

	/**
	 * Holds settings page default tab ID.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    string
	 */
	protected $default_tab = 'general';

	/**
	 * Initializes the object instance.
	 *
	 * @since   1.0.0
	 */
	public function __construct() {
		$this->settings = $this->get_registered_settings();
		$this->tabs     = $this->get_registered_tabs();

		// Prepare settings fields and sections.
		foreach ( $this->settings as &$section ) {
			$section = wp_parse_args( $section, array(
					'title'       => '',
					'description' => '',
					'tab'         => '',
					'fields'      => array(),
				)
			);

			foreach ( $section['fields'] as &$field ) {
				$field = wp_parse_args( $field, array(
						'label'       => '',
						'type'        => 'text',
						'description' => '',
						'default'     => null,
					)
				);
			}
		}

		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	/**
	 * Display all the registered settings sections and fields.
	 *
	 * @since   1.0.0
	 */
	public function display_settings() {
		$active_tab = $this->get_active_tab();
		?>
		<div class="wrap">
			<?php settings_errors( $this->option_name ); ?>

			<h2 class="nav-tab-wrapper">
				<?php
				foreach ( $this->tabs as $tab_id => $tab_title ) {
					$tab_url = add_query_arg(
						array(
							'settings-updated' => false,
							'tab'              => $tab_id,
						)
					);

					$active = $active_tab == $tab_id ? ' nav-tab-active' : '';

					echo '<a href="' . esc_url( $tab_url ) . '" title="' . esc_attr( $tab_title ) . '" class="nav-tab' . esc_attr( $active ) . '">';
					echo esc_html( $tab_title );
					echo '</a>';
				}
				?>
			</h2>

			<div id="tab-container">
				<form method="post" action="options.php">
					<?php
					settings_fields( $this->option_name );
					do_settings_sections( $this->option_name );
					submit_button();
					?>
				</form>
			</div>
		</div>
	<?php
	}

	/**
	 * Retrieves all the settings sections and fields to be registered.
	 *
	 * @since   1.0.0
	 * @access  protected
	 * @return array
	 */
	protected function get_registered_settings() {
		$settings = array();

		return apply_filters( 'rct_settings', $settings );
	}

	/**
	 * Retrieves all the settings page tabs to be registered.
	 *
	 * @since   1.0.0
	 * @access  protected
	 * @return array
	 */
	protected function get_registered_tabs() {
		$tabs = array(
			'general' => __( 'General', 'review-content-type' ),
		);

		return apply_filters( 'rct_settings_tabs', $tabs );
	}

	/**
	 * Register the settings sections and fields to WordPress.
	 *
	 * @since     1.0.0
	 */
	public function register_settings() {
		if ( false == get_option( $this->option_name ) ) {
			add_option( $this->option_name );
		}

		// Get the currently active tab.
		$active_tab = $this->get_active_tab();

		// Register settings sections and fields belonging to the currently active tab.
		foreach ( $this->settings as $section_id => $section_args ) {
			if ( $active_tab !== $section_args['tab'] ) {
				continue;
			}

			// Register settings sections.
			add_settings_section( $section_id, $section_args['title'], array(
				$this,
				'settings_section_callback',
			), $this->option_name );

			foreach ( $section_args['fields'] as $field_name => $field_args ) {
				// Arguments that are passed to the setting field callback function.
				$args = array(
					'id'        => $field_name,
					'section'   => $section_id,
					'label_for' => isset( $field_args['label_for'] ) ? $field_args['label_for'] : $this->option_name . '-' . $section_id . '-' . $field_name,
				);

				// Register settings fields.
				add_settings_field( $field_name, $field_args['label'], array(
					$this,
					'settings_field_callback',
				), $this->option_name, $section_id, $args );
			}
		}

		// Creates our settings in the options table.
		register_setting( $this->option_name, $this->option_name, array( $this, 'sanitize_settings' ) );
	}

	/**
	 * Callback for add_settings_section().
	 *
	 * Generic callback to output the section description for each
	 * settings section, if available.
	 *
	 * @since     1.0.0
	 *
	 * @param    array $args Array passed from add_settings_section()
	 */
	public function settings_section_callback( $args ) {
		$description = $this->settings[ $args['id'] ]['description'];
		if ( ! empty( $description ) ) {
			echo wpautop( $description );
		}
	}

	/**
	 * Callback for add_settings_field().
	 *
	 * Generic callback to output the settings field html.
	 *
	 * @since     1.0.0
	 *
	 * @param    array $args Settings field arguments
	 */
	public function settings_field_callback( $args ) {
		// Get the current setting field args and value.
		$field = $this->settings[ $args['section'] ]['fields'][ $args['id'] ];
		$value = $this->get( $args['id'], $args['section'] );

		// Generate the unique html id and html name attribute value for the current settings field.
		$html_name = $this->option_name . '[' . $args['section'] . '][' . $args['id'] . ']';
		$html_id   = $args['label_for'];

		switch ( $field['type'] ) {
			case 'text':
				$size = isset( $field['size'] ) && $field['size'] ? $field['size'] : 'regular';
				?>
				<input type="text" class="<?php echo esc_attr( $size ); ?>-text"
				       id="<?php echo esc_attr( $html_id ); ?>"
				       name="<?php echo esc_attr( $html_name ); ?>" value="<?php echo esc_attr( $value ); ?>"/>
				<p class="description"><?php echo $field['description']; ?></p>
				<?php
				break;
			case 'textarea': ?>
				<textarea class="large-text" cols="50" rows="5" id="<?php echo esc_attr( $html_id ); ?>"
				          name="<?php echo esc_attr( $html_name ); ?>"><?php echo esc_textarea( $value ); ?></textarea>
				<p class="description"><?php echo $field['description']; ?></p>
				<?php
				break;
			case 'select':
				$field['options'] = isset( $field['options'] ) ? $field['options'] : array();
				?>
				<select name="<?php echo esc_attr( $html_name ); ?>" id="<?php echo esc_attr( $html_id ); ?>">
					<?php foreach ( $field['options'] as $key => $val ) : ?>
						<option
							value="<?php echo esc_attr( $key ); ?>" <?php selected( $value, $key ); ?>><?php echo esc_html( $val ); ?></option>
					<?php endforeach; ?>
				</select>
				<p class="description"><?php echo $field['description']; ?></p>
				<?php
				break;
			case 'checkbox': ?>
				<input type="hidden" name="<?php echo esc_attr( $html_name ); ?>" value="0"/>
				<label>
					<input type="checkbox" class="checkbox" id="<?php echo esc_attr( $html_id ); ?>"
					       name="<?php echo esc_attr( $html_name ); ?>" value="1" <?php checked( $value, '1' ); ?> />
					<span class="description"><?php echo $field['description']; ?></span>
				</label>
				<?php
				break;
			default:
				do_action( 'rct_render_' . $field['type'] . '_settings', $args, $field, $value );
		}
	}

	/**
	 * Settings Sanitization and Validation.
	 *
	 * Validates and sanitizes the user-input data before updating the
	 * Options in the database.
	 *
	 * @since     1.0.0
	 * @link      http://codex.wordpress.org/Data_Validation  Codex Reference: Data Validation
	 *
	 * @param    array $input Raw data inputted in the settings form fields by the user
	 *
	 * @return   array $output Sanitized user-input data ready to be passed to the database
	 */
	public function sanitize_settings( $input ) {

		// Get the currently stored options.
		$output = get_option( $this->option_name, array() );
		if ( ! is_array( $output ) ) {
			$output = array();
		}

		// Loop through each of the options being saved and pass it through sanitization filters.
		foreach ( $input as $section => $values ) {
			// Skip if the current section is not registered.
			if ( ! isset( $this->settings[ $section ] ) ) {
				continue;
			}

			foreach ( $values as $key => $value ) {
				// Skip if the current field is not registered.
				if ( ! isset( $this->settings[ $section ]['fields'][ $key ] ) ) {
					continue;
				}

				// Get the current settings field type.
				$type = $this->settings[ $section ]['fields'][ $key ]['type'];

				// Do some default sanitization.
				switch ( $type ) {
					case 'text':
						$value = sanitize_text_field( $value );
						break;
					case 'textarea':
						$value = wp_kses_post( trim( $value ) );
						break;
				}

				// Apply a field type specific filter.
				$value = apply_filters( "rct_sanitize_{$type}_settings", $value, $key, $section );

				// Apply a general filter.
				$value = apply_filters( 'rct_sanitize_settings', $value, $key, $section );

				// Update the output array with the sanitized field value.
				$output[ $section ][ $key ] = $value;
			}
		}

		add_settings_error( $this->option_name, $this->option_name, __( 'Settings updated.', 'review-content-type' ), 'updated' );

		return $output;
	}

	/**
	 * Retrieves the currently active settings page tab ID.
	 *
	 * @since   1.0.0
	 * @return string
	 */
	public function get_active_tab() {
		return isset( $_GET['tab'] ) && array_key_exists( $_GET['tab'], $this->tabs ) ? $_GET['tab'] : $this->default_tab;
	}

	/**
	 * Gets the value of a specific settings field.
	 *
	 * @since   1.0.0
	 *
	 * @param string $field_id   Settings field ID
	 * @param string $section_id Settings section ID to which the passed settings field belongs.
	 *
	 * @return mixed Settings field value
	 */
	public function get( $field_id, $section_id ) {
		$options = get_option( $this->option_name, array() );

		if ( ! empty( $options[ $section_id ][ $field_id ] ) ) {
			return $options[ $section_id ][ $field_id ];
		}

		if ( isset( $this->settings[ $section_id ]['fields'][ $field_id ] ) ) {
			return $this->settings[ $section_id ]['fields'][ $field_id ]['default'];
		}

		return null;
	}

}
