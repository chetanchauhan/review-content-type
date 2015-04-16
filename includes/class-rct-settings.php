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
		$currency_options = rct_get_currencies();
		foreach ( $currency_options as $currency_code => $currency_label ) {
			$currency_options[ $currency_code ] = $currency_label . ' (' . rct_get_currency_symbol( $currency_code ) . ')';
		}

		// Get the currently saved currency using default currency as fallback.
		$currency = $this->get( 'currency', 'currency' );
		if ( ! isset( $currency ) ) {
			$currency = 'USD';
		}

		$currency_symbol = rct_get_currency_symbol( $currency );

		$settings['currency'] = array(
			'tab'         => 'general',
			'title'       => __( 'Currency Settings', 'review-content-type' ),
			'description' => __( 'Manage the settings that affect how prices of the reviewed item get displayed on the frontend.', 'review-content-type' ),
			'fields'      => array(
				'currency'          => array(
					'label'       => __( 'Currency', 'review-content-type' ),
					'description' => __( 'Select currency to use for displaying prices.', 'review-content-type' ),
					'options'     => $currency_options,
					'default'     => 'USD',
					'type'        => 'select',
				),
				'currency_position' => array(
					'label'       => __( 'Currency Position', 'review-content-type' ),
					'description' => __( 'Select position of the currency symbol.', 'review-content-type' ),
					'options'     => array(
						'before' => __( 'Before', 'review-content-type' ) . ' (' . $currency_symbol . '99.99)',
						'after'  => __( 'After', 'review-content-type' ) . ' (99.99' . $currency_symbol . ')',
					),
					'default'     => 'before',
					'type'        => 'select',
				),
			),
		);

		$settings['rating'] = array(
			'tab'    => 'rating',
			'fields' => array(
				'rating_type'  => array(
					'label'       => __( 'Default Rating Type', 'review-content-type' ),
					'description' => __( 'Select the default rating type.', 'review-content-type' ),
					'options'     => rct_get_rating_types(),
					'default'     => 'star',
					'type'        => 'select',
				),
				'rating_scale' => array(
					'label'       => __( 'Rating Scale', 'review-content-type' ),
					'description' => __( 'Customize the scale for all available rating types as per your likings and all the existing ratings will get adjusted automatically to the new scale when gets displayed.', 'review-content-type' ),
					'type'        => 'rating_scale',
				),
			),
		);

		$settings['display'] = array(
			'tab'    => 'display',
			'fields' => array(
				'pros_heading'    => array(
					'label'       => __( 'Pros Heading Text', 'review-content-type' ),
					'description' => __( 'The heading text used by default for displaying review pros.', 'review-content-type' ),
					'default'     => __( 'Pros', 'review-content-type' ),
					'type'        => 'text',
				),
				'cons_heading'    => array(
					'label'       => __( 'Cons Heading Text', 'review-content-type' ),
					'description' => __( 'The heading text used by default for displaying review cons.', 'review-content-type' ),
					'default'     => __( 'Cons', 'review-content-type' ),
					'type'        => 'text',
				),
				'summary_heading' => array(
					'label'       => __( 'Summary Heading Text', 'review-content-type' ),
					'description' => __( 'The heading text used by default for displaying review summary.', 'review-content-type' ),
					'default'     => __( 'Summary', 'review-content-type' ),
					'type'        => 'text',
				),
				'link_text'       => array(
					'label'       => __( 'Default Link Text', 'review-content-type' ),
					'description' => __( 'Default text used for displaying the call to action links.', 'review-content-type' ),
					'default'     => __( 'Buy Now', 'review-content-type' ),
					'type'        => 'text',
				),
				'link_style'      => array(
					'label'       => __( 'Default Link Style', 'review-content-type' ),
					'description' => __( 'Select the style you want to use for all the links by default.', 'review-content-type' ),
					'options'     => rct_get_link_styles(),
					'default'     => 'button',
					'type'        => 'select',
				),
			)
		);

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
			'rating'  => __( 'Rating', 'review-content-type' ),
			'display' => __( 'Display', 'review-content-type' ),
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
			case 'rating_scale': ?>
				<table class="rct-rating-scale widefat">
					<thead>
					<tr>
						<?php
						$columns = array(
							'type' => __( 'Rating Type', 'review-content-type' ),
							'min'  => __( 'Minimum', 'review-content-type' ),
							'max'  => __( 'Maximum', 'review-content-type' ),
							'step' => __( 'Step', 'review-content-type' ),
						);
						foreach ( $columns as $key => $column ) {
							echo '<th class="' . esc_attr( $key ) . '">' . esc_html( $column ) . '</th>';
						}
						?>
					</tr>
					</thead>
					<tbody>
					<?php
					$rating_types = rct_get_rating_types();
					foreach ( $rating_types as $rating_type => $rating_title ) {
						$scale = rct_get_rating_scale( $rating_type );
						echo '<tr>';
						foreach ( $columns as $key => $column ) {
							switch ( $key ) {
								case 'type' :
									echo '<td class="type">
											' . esc_html( $rating_title ) . '
										</td>';
									break;
								case 'min' :
								case 'max':
									echo '<td class="' . $key . '">
											<input type="number" min="0" name="' . esc_attr( $html_name . '[' . $rating_type . '][' . $key . ']' ) . '" value="' . esc_attr( $scale[ $key ] ) . '" >
										</td>';
									break;
								case 'step':
									echo '<td class="step">
											<input type="number" min="0" step="any" name="' . esc_attr( $html_name . '[' . $rating_type . '][step]' ) . '" value="' . esc_attr( $scale['step'] ) . '" >
										</td>';
									break;
							}
						}
						echo '</tr>';
					}
					?>
					</tbody>
				</table>
				<p class="description"><?php echo $field['description']; ?></p>
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

