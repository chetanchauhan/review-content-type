<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class RCT_Admin_Meta_Boxes
 */
class RCT_Admin_Meta_Boxes {

	/**
	 * @since   1.0.0
	 */
	public function __construct() {
		add_action( 'add_meta_boxes_review', array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save_review_data' ), 10, 2 );
	}

	/**
	 * Retrieves all the fields for the review data meta box.
	 *
	 * @since   1.0.0
	 * @access  protected
	 * @return  array
	 */
	protected function get_review_data_fields() {
		$fields = array(
			'name'                => array(
				'label'       => __( 'Name', 'review-content-type' ),
				'description' => __( 'Name of the item that is being reviewed.', 'review-content-type' ),
				'type'        => 'text',
				'required'    => true,
				'priority'    => 10,
			),
			'pros_heading'        => array(
				'label'       => __( 'Pros Heading', 'review-content-type' ),
				'description' => __( 'Leave blank to use default pros heading text.', 'review-content-type' ),
				'type'        => 'text',
				'placeholder' => review_content_type()->settings->get( 'pros_heading', 'display' ),
				'priority'    => 20,
			),
			'pros'                => array(
				'label'       => __( 'Pros', 'review-content-type' ),
				'description' => __( 'Mention all the good things about the item being reviewed.', 'review-content-type' ),
				'type'        => 'text',
				'repeatable'  => true,
				'label_for'   => 'rct_review_data-pros-0',
				'default'     => '',
				'priority'    => 30,
			),
			'cons_heading'        => array(
				'label'       => __( 'Cons Heading', 'review-content-type' ),
				'description' => __( 'Leave blank to use default cons heading text.', 'review-content-type' ),
				'type'        => 'text',
				'placeholder' => review_content_type()->settings->get( 'cons_heading', 'display' ),
				'priority'    => 40,
			),
			'cons'                => array(
				'label'       => __( 'Cons', 'review-content-type' ),
				'description' => __( 'Mention all the bad things about the item being reviewed.', 'review-content-type' ),
				'type'        => 'text',
				'repeatable'  => true,
				'label_for'   => 'rct_review_data-cons-0',
				'default'     => '',
				'priority'    => 50,
			),
			'summary_heading'     => array(
				'label'       => __( 'Summary Heading', 'review-content-type' ),
				'description' => __( 'Leave blank to use default summary heading text.', 'review-content-type' ),
				'type'        => 'text',
				'placeholder' => review_content_type()->settings->get( 'summary_heading', 'display' ),
				'priority'    => 60,
			),
			'summary'             => array(
				'label'       => __( 'Summary', 'review-content-type' ),
				'description' => __( 'Brief description of the item being reviewed.', 'review-content-type' ),
				'type'        => 'editor',
				'options'     => array(
					'textarea_rows' => 6,
					'tinymce'       => false,
					'media_buttons' => false,
				),
				'priority'    => 70,
			),
			'link_url'            => array(
				'label'       => __( 'Link URL', 'review-content-type' ),
				'description' => __( 'Leave blank to <strong>disable</strong> the call to action link.', 'review-content-type' ),
				'type'        => 'url',
				'placeholder' => __( 'Enter link url here', 'review-content-type' ),
				'priority'    => 80,
			),
			'featured_image_link' => array(
				'label'       => __( 'Link Featured Image To', 'review-content-type' ),
				'description' => __( 'Select where you want to link featured image.', 'review-content-type' ),
				'type'        => 'featured_image_link',
				'default'     => array(
					'type' => '',
					'url'  => '',
				),
				'priority'    => 90,
			),
			'link_text'           => array(
				'label'       => __( 'Link Text', 'review-content-type' ),
				'description' => __( 'Leave blank to use default link text.', 'review-content-type' ),
				'type'        => 'text',
				'placeholder' => review_content_type()->settings->get( 'link_text', 'display' ),
				'priority'    => 100,
			),
			'link_style'          => array(
				'label'       => __( 'Link Style', 'review-content-type' ),
				'description' => __( 'Select style for displaying the above call to action link.', 'review-content-type' ),
				'type'        => 'select',
				'options'     => rct_get_link_styles(),
				'default'     => review_content_type()->settings->get( 'link_style', 'display' ),
				'priority'    => 110,
			),
		);

		return apply_filters( 'rct_review_data_fields', $fields );
	}

	/**
	 * Adds the required meta boxes.
	 *
	 * @since   1.0.0
	 */
	public function add_meta_boxes() {
		add_meta_box( 'rct_review_data', __( 'Review Data', 'review-content-type' ), array(
			$this,
			'display_review_data',
		), 'review', 'normal', 'high' );
	}

	/**
	 * Display the review data meta box content.
	 *
	 * @since   1.0.0
	 *
	 * @param   WP_Post $post
	 */
	public function display_review_data( $post ) {
		$fields = $this->get_review_data_fields();
		uasort( $fields, array( $this, 'cmp_priority' ) );

		wp_nonce_field( 'rct_save_review_data', '_rct_review_data_nonce' );

		echo '<div class="rct-fields-wrapper">';

		foreach ( $fields as $id => $field ) {
			$html_id   = "rct_review_data-{$id}";
			$html_name = "rct_review_data[$id]";
			$field     = wp_parse_args( $field, array(
					'type'        => 'text',
					'default'     => null,
					'label'       => '',
					'label_for'   => $html_id,
					'description' => '',
					'required'    => false,
					'placeholder' => '',
				)
			);

			$value = get_post_meta( $post->ID, '_rct_' . $id, true );
			if ( '' === $value || array() === $value ) {
				$value = $field['default'];
			}

			printf( '<div class="rct-field rct-%1$s-field">', esc_attr( $field['type'] ) );

			if ( ! empty( $field['label'] ) ) {
				echo '<div class="rct-field-label">';
				$label = $field['required'] ? $field['label'] . ' <span class="required">*</span>' : $field['label'];
				if ( ! empty( $field['label_for'] ) ) {
					echo '<label for="' . esc_attr( $field['label_for'] ) . '">' . $label . '</label>';
				} else {
					echo $label;
				}
				echo '</div>';
			}

			echo '<div class="rct-field-input">';
			switch ( $field['type'] ) {
				case 'url':
				case 'email':
				case 'password':
				case 'text':
					if ( isset( $field['repeatable'] ) && $field['repeatable'] ) { ?>
						<table class="rct-repeatable">
							<?php foreach ( (array) $value as $index => $val ) : ?>
								<tr class="rct-field-input-row">
									<td style="width: 1%">
										<span class="rct-sorthandle dashicons dashicons-menu"></span>
									</td>
									<td>
										<?php printf( '<input type="%1$s" id="%2$s" name="%3$s" value="%4$s" placeholder="%5$s" %6$s>', esc_attr( $field['type'] ), esc_attr( "{$html_id}-{$index}" ), esc_attr( $html_name . '[]' ), esc_attr( $val ), esc_attr( $field['placeholder'] ), $field['required'] ? 'required' : '' ); ?>
									</td>
									<td style="width: 2%">
										<a href="#"
										   class="button button-secondary rct-remove-input"
										   title="<?php _e( 'Remove', 'review-content-type' ); ?>"><?php _e( '-', 'review-content-type' ); ?></a>
									</td>
									<td style="width: 2%">
										<a href="#"
										   class="button button-primary rct-add-input"
										   title="<?php _e( 'Add', 'review-content-type' ); ?>"><?php _e( '+', 'review-content-type' ); ?></a>
									</td>
								</tr>
							<?php endforeach; ?>
						</table>
					<?php
					} else {
						printf( '<input type="%1$s" id="%2$s" name="%3$s" value="%4$s" placeholder="%5$s" %6$s>', esc_attr( $field['type'] ), esc_attr( $html_id ), esc_attr( $html_name ), esc_attr( $value ), esc_attr( $field['placeholder'] ), $field['required'] ? 'required' : '' );
					}
					break;
				case 'editor':
					$options                  = isset( $field['options'] ) ? (array) $field['options'] : array();
					$options['textarea_name'] = $html_name;
					wp_editor( $value, $html_id, $options );
					break;
				case 'select':
					if ( ! isset( $field['options'] ) ) {
						$field['options'] = array();
					}
					?>
					<select name="<?php echo esc_attr( $html_name ); ?>" id="<?php echo esc_attr( $html_id ); ?>">
						<?php foreach ( $field['options'] as $key => $val ) : ?>
							<option
								value="<?php echo esc_attr( $key ); ?>" <?php selected( $value, $key ); ?>><?php echo esc_html( $val ); ?></option>
						<?php endforeach; ?>
					</select>
					<?php
					break;
				case 'featured_image_link':
					$options = array(
						'file'   => __( 'Media File', 'review-content-type' ),
						'review' => __( 'Review Page', 'review-content-type' ),
						'custom' => __( 'Custom URL', 'review-content-type' ),
						'none'   => __( 'None', 'review-content-type' ),
					);
					?>
					<select name="<?php echo esc_attr( $html_name . '[type]' ); ?>"
					        id="<?php echo esc_attr( $html_id ); ?>">
						<?php foreach ( $options as $key => $val ) : ?>
							<option
								value="<?php echo esc_attr( $key ); ?>" <?php selected( $value['type'], $key ); ?>><?php echo esc_html( $val ); ?></option>
						<?php endforeach; ?>
					</select>
					<input type="text" id="<?php echo esc_attr( $html_id . '-url' ); ?>" class="hide-if-js"
					       name="<?php echo esc_attr( $html_name . '[url]' ); ?>"
					       value="<?php echo esc_attr( $value['url'] ); ?>"
					       placeholder="<?php _e( 'Enter custom url here', 'review-content-type' ); ?>">
					<?php
					break;
				default:
					do_action( 'rct_render_review_data_' . $field['type'] . '_fields', $id, $field );
			}

			if ( ! empty( $field['description'] ) ) {
				echo '<p class="description">' . $field['description'] . '</p>';
			}
			echo '</div></div>';
		}
		echo '</div>';
	}

	/**
	 * Save review data fields when the `save_post` action is called.
	 *
	 * @since 1.0
	 *
	 * @param int $post_id Review ID
	 */
	public function save_review_data( $post_id ) {
		if ( empty( $_POST['rct_review_data'] ) || ! isset( $_POST['_rct_review_data_nonce'] ) || ! wp_verify_nonce( $_POST['_rct_review_data_nonce'], 'rct_save_review_data' ) ) {
			return;
		}
		if ( ! current_user_can( 'edit_review', $post_id ) ) {
			return;
		}

		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) ) {
			return;
		}

		$fields = $this->get_review_data_fields();

		foreach ( $fields as $id => $args ) {
			if ( ! isset( $_POST['rct_review_data'][ $id ] ) ) {
				continue;
			}

			$value = stripslashes_deep( $_POST['rct_review_data'][ $id ] );
			$type  = empty( $args['type'] ) ? 'text' : $args['type'];

			switch ( $type ) {
				case 'url':
				case 'email':
				case 'password':
				case 'text':
				case 'select':
					$sanitization_cb = 'sanitize_text_field';
					if ( 'url' === $type ) {
						$sanitization_cb = 'esc_url_raw';
					} elseif ( 'email' === $type ) {
						$sanitization_cb = 'sanitize_email';
					}
					$value = is_array( $value ) ? array_filter( array_map( $sanitization_cb, $value ) ) : call_user_func( $sanitization_cb, $value );
					break;
				case 'editor':
					$value = wp_kses_post( $value );
					break;
				case 'featured_image_link':
					$value = array(
						'type' => isset( $value['type'] ) ? sanitize_text_field( $value['type'] ) : '',
						'url'  => isset( $value['url'] ) ? esc_url_raw( $value['url'] ) : '',
					);
					break;
			}

			$value = apply_filters( 'rct_sanitize_review_data_' . $args['type'] . '_fields', $value, $id );
			$value = apply_filters( 'rct_sanitize_review_data_' . $id . '_field', $value, $id );

			if ( has_action( 'rct_save_review_data_' . $type . '_fields' ) ) {
				do_action( 'rct_save_review_data_' . $type . '_fields', $value, $id );
			} else {
				update_post_meta( $post_id, '_rct_' . $id, $value );
			}
		}
	}

	/**
	 * Helper to sort review data fields by priority.
	 *
	 * @param array $a
	 * @param array $b
	 *
	 * @return int
	 */
	protected function cmp_priority( $a, $b ) {
		$ap = isset( $a['priority'] ) ? $a['priority'] : 10;
		$bp = isset( $b['priority'] ) ? $b['priority'] : 10;

		return $ap - $bp;
	}

}

new RCT_Admin_Meta_Boxes();
