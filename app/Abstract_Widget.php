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
	 * Widget output schema.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected $output_schema;

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
		$this->output_schema   = [];

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
	 * Get widget output schema.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_output_schema() {
		return $this->output_schema ?? [];
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
			'output_schema'   => $this->output_schema,
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

		// Then validate widget-specific schema.
		if ( empty( $this->output_schema ) ) {
			return true;
		}

		foreach ( $this->output_schema as $key => $schema ) {
			if ( isset( $schema['required'] ) && $schema['required'] && ! isset( $output[ $key ] ) ) {
				return false;
			}

			// Validate type if specified.
			if ( isset( $schema['type'] ) && isset( $output[ $key ] ) ) {
				$actual_type = gettype( $output[ $key ] );
				if ( $actual_type !== $schema['type'] ) {
					return false;
				}
			}
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
	 * Merge settings with defaults.
	 *
	 * @since 1.0.0
	 *
	 * @param array $settings Widget settings.
	 *
	 * @return array
	 */
	public function merge_settings_with_defaults( $settings ) {
		return wp_parse_args( $settings, $this->get_default_settings() );
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
			'output_schema'    => $this->output_schema,
			'essential_fields' => $this->get_essential_fields(),
		];
	}
}
