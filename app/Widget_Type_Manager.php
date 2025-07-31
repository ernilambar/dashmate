<?php
/**
 * Widget_Type_Manager
 *
 * @package Dashmate
 */

declare(strict_types=1);

namespace Nilambar\Dashmate;

/**
 * Widget_Type_Manager class.
 *
 * @since 1.0.0
 */
class Widget_Type_Manager {

	/**
	 * Registered widget types.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private static $widget_types = [];

	/**
	 * Content handlers for widget types.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private static $content_handlers = [];

	/**
	 * Register a new widget type.
	 *
	 * @since 1.0.0
	 *
	 * @param string   $type        Widget type identifier.
	 * @param array    $definition  Widget definition.
	 * @param callable $content_handler Content handler callback.
	 *
	 * @return bool
	 */
	public static function register_widget_type( $type, $definition, $content_handler = null ) {
		if ( empty( $type ) || ! is_string( $type ) ) {
			return false;
		}

		// Validate required fields.
		$required_fields = [ 'name', 'description', 'icon', 'settings_schema' ];
		foreach ( $required_fields as $field ) {
			if ( ! isset( $definition[ $field ] ) ) {
				return false;
			}
		}

		// Register widget type.
		self::$widget_types[ $type ] = $definition;

		// Register content handler if provided.
		if ( is_callable( $content_handler ) ) {
			self::$content_handlers[ $type ] = $content_handler;
		}

		return true;
	}

	/**
	 * Get all registered widget types.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public static function get_widget_types() {
		/**
		 * Filter the registered widget types.
		 *
		 * This filter allows other plugins and addons to add their own widget types
		 * to the Dashmate dashboard.
		 *
		 * @since 1.0.0
		 *
		 * @param array $widget_types Array of registered widget types.
		 */
		return apply_filters( 'dashmate_widget_types', self::$widget_types );
	}

	/**
	 * Get a specific widget type definition.
	 *
	 * @since 1.0.0
	 *
	 * @param string $type Widget type.
	 *
	 * @return array|null
	 */
	public static function get_widget_type( $type ) {
		$filtered_widget_types = self::get_widget_types();
		return $filtered_widget_types[ $type ] ?? null;
	}

	/**
	 * Check if a widget type is registered.
	 *
	 * @since 1.0.0
	 *
	 * @param string $type Widget type.
	 *
	 * @return bool
	 */
	public static function is_widget_type_registered( $type ) {
		$filtered_widget_types = self::get_widget_types();
		return isset( $filtered_widget_types[ $type ] );
	}

	/**
	 * Get content for a widget type.
	 *
	 * @since 1.0.0
	 *
	 * @param string $type      Widget type.
	 * @param string $widget_id Widget ID.
	 * @param array  $settings  Widget settings.
	 *
	 * @return array|WP_Error
	 */
	public static function get_widget_content( $type, $widget_id = null, $settings = [] ) {
		if ( ! self::is_widget_type_registered( $type ) ) {
			return new \WP_Error( 'unknown_widget_type', 'Unknown widget type: ' . $type );
		}

		// Check if content handler exists.
		if ( ! isset( self::$content_handlers[ $type ] ) ) {
			// Try to get content via filter for external widget types.
			$filtered_content = apply_filters( 'dashmate_widget_content_' . $type, null, $widget_id, $settings );

			if ( null !== $filtered_content ) {
				return $filtered_content;
			}

			return new \WP_Error( 'no_content_handler', 'No content handler registered for widget type: ' . $type );
		}

		// Call the content handler.
		$handler = self::$content_handlers[ $type ];
		return call_user_func( $handler, $widget_id, $settings );
	}

	/**
	 * Get all content handlers.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public static function get_content_handlers() {
		return self::$content_handlers;
	}

	/**
	 * Unregister a widget type.
	 *
	 * @since 1.0.0
	 *
	 * @param string $type Widget type.
	 *
	 * @return bool
	 */
	public static function unregister_widget_type( $type ) {
		if ( ! self::is_widget_type_registered( $type ) ) {
			return false;
		}

		unset( self::$widget_types[ $type ] );
		unset( self::$content_handlers[ $type ] );

		return true;
	}

	/**
	 * Get widget types as JSON for frontend.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public static function get_widget_types_for_frontend() {
		$widget_types = [];
		$filtered_widget_types = self::get_widget_types();

		foreach ( $filtered_widget_types as $type => $definition ) {
			$widget_types[ $type ] = [
				'name'                => $definition['name'],
				'description'         => $definition['description'],
				'icon'                => $definition['icon'],
				'settings_schema'     => $definition['settings_schema'],
				'has_content_handler' => isset( self::$content_handlers[ $type ] ),
			];
		}

		return $widget_types;
	}
}
