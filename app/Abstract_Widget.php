<?php
/**
 * Abstract_Widget
 *
 * @package Dashmate
 */

declare(strict_types=1);

namespace Nilambar\Dashmate;

/**
 * Abstract_Widget class.
 *
 * @since 1.0.0
 */
abstract class Abstract_Widget {

	/**
	 * Widget template type.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $template_type;

	/**
	 * Widget instance ID.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $id;

	/**
	 * Widget name.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * Widget description.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $description;

	/**
	 * Widget icon.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $icon;

	/**
	 * Widget settings schema.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected $settings_schema;



	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param string $id             Widget instance ID.
	 * @param string $template_type  Widget template type.
	 * @param string $name           Widget name.
	 */
	public function __construct( $id, $template_type, $name ) {
		$this->id              = $id;
		$this->template_type   = $template_type;
		$this->name            = $name;
		$this->description     = '';
		$this->icon            = '';
		$this->settings_schema = [];

		// Define widget configuration.
		$this->define_widget();
	}

	/**
	 * Define widget configuration.
	 *
	 * Each widget must implement this method to define its own
	 * settings schema, output schema, description, and icon.
	 *
	 * @since 1.0.0
	 */
	abstract protected function define_widget();

	/**
	 * Get widget content.
	 *
	 * @since 1.0.0
	 *
	 * @param array $settings Widget settings.
	 * @return array Widget content.
	 */
	abstract public function get_content( array $settings = [] ): array;

	/**
	 * Get widget template type.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_template_type() {
		return $this->template_type;
	}

	/**
	 * Get widget ID.
	 *
	 * @since 1.0.0
	 *
	 * @return string|null
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Get widget name.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * Get widget description.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_description() {
		return $this->description;
	}

	/**
	 * Get widget icon.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_icon() {
		return $this->icon;
	}

	/**
	 * Get widget settings schema.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_settings_schema() {
		return $this->settings_schema ?? [];
	}



	/**
	 * Get widget definition.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_definition() {
		return [
			'name'            => $this->name,
			'description'     => $this->description,
			'icon'            => $this->icon,
			'template_type'   => $this->template_type,
			'settings_schema' => $this->settings_schema,

		];
	}

	/**
	 * Validate widget settings.
	 *
	 * @since 1.0.0
	 *
	 * @param array $settings Widget settings.
	 *
	 * @return bool
	 */
	protected function validate_settings( $settings ) {
		if ( empty( $this->settings_schema ) ) {
			return true;
		}

		foreach ( $this->settings_schema as $key => $schema ) {
			if ( isset( $schema['required'] ) && $schema['required'] && ! isset( $settings[ $key ] ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Sanitize widget settings based on field types.
	 *
	 * @since 1.0.0
	 *
	 * @param array $settings Widget settings to sanitize.
	 *
	 * @return array Sanitized settings.
	 */
	public function sanitize_settings( array $settings ): array {
		if ( empty( $this->settings_schema ) ) {
			return $settings;
		}

		$sanitized_settings = [];

		foreach ( $this->settings_schema as $key => $schema ) {
			if ( ! isset( $settings[ $key ] ) ) {
				continue;
			}

			$value      = $settings[ $key ];
			$field_type = $schema['type'] ?? 'text';

			$sanitized_settings[ $key ] = $this->sanitize_field_value( $value, $field_type, $schema );
		}

		return $sanitized_settings;
	}

	/**
	 * Sanitize individual field value based on field type.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed  $value      Field value to sanitize.
	 * @param string $field_type Field type from schema.
	 * @param array  $schema     Field schema.
	 *
	 * @return mixed Sanitized value.
	 */
	protected function sanitize_field_value( $value, string $field_type, array $schema ) {
		switch ( $field_type ) {
			case 'text':
				return sanitize_text_field( $value );

			case 'textarea':
				return sanitize_textarea_field( $value );

			case 'url':
				return esc_url_raw( $value );

			case 'email':
				return sanitize_email( $value );

			case 'password':
				return is_string( $value ) ? trim( $value ) : '';

			case 'number':
				return is_numeric( $value ) ? intval( $value ) : 0;

			case 'checkbox':
				return (bool) $value;

			case 'toggle':
				return (bool) $value;

			case 'select':
				if ( isset( $schema['choices'] ) && is_array( $schema['choices'] ) ) {
					$valid_choices = array_column( $schema['choices'], 'value' );
					return in_array( $value, $valid_choices, true ) ? sanitize_text_field( $value ) : '';
				}
				return sanitize_text_field( $value );

			case 'radio':
				if ( isset( $schema['choices'] ) && is_array( $schema['choices'] ) ) {
					$valid_choices = array_column( $schema['choices'], 'value' );
					return in_array( $value, $valid_choices, true ) ? sanitize_text_field( $value ) : '';
				}
				return sanitize_text_field( $value );

			case 'buttonset':
				if ( isset( $schema['choices'] ) && is_array( $schema['choices'] ) ) {
					$valid_choices = array_column( $schema['choices'], 'value' );
					return in_array( $value, $valid_choices, true ) ? sanitize_text_field( $value ) : '';
				}
				return sanitize_text_field( $value );

			case 'multicheckbox':
				// Handle array of selected values.
				if ( ! is_array( $value ) ) {
					return [];
				}

				$sanitized_array = [];
				if ( isset( $schema['choices'] ) && is_array( $schema['choices'] ) ) {
					$valid_choices = array_column( $schema['choices'], 'value' );
					foreach ( $value as $item ) {
						if ( in_array( $item, $valid_choices, true ) ) {
							$sanitized_array[] = sanitize_text_field( $item );
						}
					}
				} else {
					foreach ( $value as $item ) {
						$sanitized_array[] = sanitize_text_field( $item );
					}
				}
				return $sanitized_array;

			case 'sortable':
				// Handle array of sortable items.
				if ( ! is_array( $value ) ) {
					return [];
				}

				$sanitized_array = [];
				if ( isset( $schema['choices'] ) && is_array( $schema['choices'] ) ) {
					$valid_choices = array_column( $schema['choices'], 'value' );
					foreach ( $value as $item ) {
						if ( in_array( $item, $valid_choices, true ) ) {
							$sanitized_array[] = sanitize_text_field( $item );
						}
					}
				} else {
					foreach ( $value as $item ) {
						$sanitized_array[] = sanitize_text_field( $item );
					}
				}
				return $sanitized_array;

			case 'hidden':
				return sanitize_text_field( $value );

			default:
				return sanitize_text_field( $value );
		}
	}

	/**
	 * Validate widget output against universal schema.
	 *
	 * @since 1.0.0
	 *
	 * @param array $output Widget output data.
	 *
	 * @return bool
	 */
	protected function validate_universal_output( $output ) {
		$universal_schema = $this->get_universal_output_schema();

		foreach ( $universal_schema as $key => $schema ) {
			if ( isset( $schema['required'] ) && $schema['required'] && ! isset( $output[ $key ] ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Validate widget output against schema.
	 *
	 * @since 1.0.0
	 *
	 * @param array $output Widget output data.
	 *
	 * @return bool
	 */
	protected function validate_output( $output ) {
		// First validate universal schema (title and icon are required for all widgets).
		if ( ! $this->validate_universal_output( $output ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Get default settings.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	protected function get_default_settings() {
		$defaults = [];

		if ( empty( $this->settings_schema ) ) {
			return $defaults;
		}

		foreach ( $this->settings_schema as $key => $schema ) {
			if ( isset( $schema['default'] ) ) {
				$defaults[ $key ] = $schema['default'];
			}
		}

		return $defaults;
	}

	/**
	 * Merge settings with defaults and sanitize.
	 *
	 * @since 1.0.0
	 *
	 * @param array $settings Widget settings.
	 *
	 * @return array
	 */
	public function merge_settings_with_defaults( $settings ) {
		$merged_settings = wp_parse_args( $settings, $this->get_default_settings() );
		return $this->sanitize_settings( $merged_settings );
	}

	/**
	 * Get validated content with universal fields.
	 *
	 * @since 1.0.0
	 *
	 * @param array $settings Widget settings.
	 *
	 * @return array|WP_Error
	 */
	public function get_validated_content( array $settings = [] ) {
		// Get content from the widget implementation.
		$content = $this->get_content( $settings );

		// Add universal fields (title and icon) if not already present.
		$content = $this->add_universal_fields( $content );

		// Validate output against schema.
		if ( ! $this->validate_output( $content ) ) {
			return new \WP_Error( 'invalid_output', 'Widget output does not match schema for widget type: ' . $this->template_type );
		}

		return $content;
	}

	/**
	 * Add universal fields to widget content.
	 *
	 * @since 1.0.0
	 *
	 * @param array $content Widget content.
	 *
	 * @return array
	 */
	protected function add_universal_fields( $content ) {
		// Add title if not present.
		if ( ! isset( $content['title'] ) ) {
			$content['title'] = $this->name;
		}

		// Add icon if not present.
		if ( ! isset( $content['icon'] ) ) {
			$content['icon'] = $this->icon;
		}

		return $content;
	}

	/**
	 * Get essential fields for frontend rendering.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_essential_fields() {
		return [
			'id'       => [
				'type'     => 'string',
				'required' => true,
			],
			'type'     => [
				'type'     => 'string',
				'required' => true,
			],
			'title'    => [
				'type'     => 'string',
				'required' => true,
			],
			'icon'     => [
				'type'     => 'string',
				'required' => true,
			],
			'settings' => [
				'type'     => 'object',
				'required' => true,
			],
		];
	}

	/**
	 * Get universal output schema that all widgets must follow.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	protected function get_universal_output_schema() {
		return [
			'title' => [
				'type'     => 'string',
				'required' => true,
			],
			'icon'  => [
				'type'     => 'string',
				'required' => true,
			],
		];
	}

	/**
	 * Get widget definition with essential fields.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_definition_with_essentials() {
		return [
			'name'             => $this->name,
			'description'      => $this->description,
			'icon'             => $this->icon,
			'template_type'    => $this->template_type,
			'settings_schema'  => $this->settings_schema,

			'essential_fields' => $this->get_essential_fields(),
		];
	}

	/**
	 * Get custom CSS classes for this widget.
	 *
	 * Child plugins can override this method to add custom classes.
	 * Alternatively, they can use the 'dashmate_widget_custom_classes' filter.
	 *
	 * @since 1.0.0
	 *
	 * @param array $settings Widget settings.
	 * @return array Array of CSS class strings.
	 */
	public function get_custom_classes( array $settings = [] ) {
		$classes = [];

		/**
		 * Filter custom CSS classes for a specific widget.
		 *
		 * This filter allows other plugins and addons to add custom CSS classes
		 * to specific widgets based on their ID, type, and settings.
		 *
		 * @since 1.0.0
		 *
		 * @param array  $classes  Array of CSS class strings.
		 * @param string $widget_id Widget ID.
		 * @param string $widget_type Widget template type.
		 * @param array  $settings Widget settings.
		 */
		return apply_filters( 'dashmate_widget_custom_classes', $classes, $this->id, $this->template_type, $settings );
	}

	/**
	 * Get widget metadata (classes and attributes).
	 *
	 * Child plugins can override this method to add custom metadata.
	 * Alternatively, they can use the 'dashmate_widget_metadata' filter.
	 *
	 * @since 1.0.0
	 *
	 * @param array $settings Widget settings.
	 * @return array Widget metadata with 'classes' and 'attributes' keys.
	 */
	public function get_metadata( array $settings = [] ) {
		$metadata = [
			'classes'    => $this->get_custom_classes( $settings ),
			'attributes' => [],
		];

		/**
		 * Filter widget metadata for a specific widget.
		 *
		 * This filter allows other plugins and addons to add custom metadata
		 * (classes and attributes) to specific widgets.
		 *
		 * @since 1.0.0
		 *
		 * @param array  $metadata Widget metadata array with 'classes' and 'attributes' keys.
		 * @param string $widget_id Widget ID.
		 * @param string $widget_type Widget template type.
		 * @param array  $settings Widget settings.
		 */
		return apply_filters( 'dashmate_widget_metadata', $metadata, $this->id, $this->template_type, $settings );
	}
}
