<?php
/**
 * Widget_Manager_New
 *
 * @package Dashmate
 */

declare(strict_types=1);

namespace Nilambar\Dashmate;

/**
 * Widget_Manager_New class.
 *
 * @since 1.0.0
 */
class Widget_Manager_New {

	/**
	 * Registered widgets.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private static $widgets = [];

	/**
	 * Register a widget.
	 *
	 * @since 1.0.0
	 *
	 * @param Abstract_Widget $widget Widget instance.
	 *
	 * @return bool
	 */
	public static function register_widget( Abstract_Widget $widget ) {
		$type = $widget->get_type();

		if ( empty( $type ) ) {
			return false;
		}

		self::$widgets[ $type ] = $widget;

		return true;
	}

	/**
	 * Get all registered widgets.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public static function get_widgets() {
		/**
		 * Filter the registered widgets.
		 *
		 * This filter allows other plugins and addons to add their own widgets
		 * to the Dashmate dashboard.
		 *
		 * @since 1.0.0
		 *
		 * @param array $widgets Array of registered widgets.
		 */
		return apply_filters( 'dashmate_widgets', self::$widgets );
	}

	/**
	 * Get a specific widget.
	 *
	 * @since 1.0.0
	 *
	 * @param string $type Widget type.
	 *
	 * @return Abstract_Widget|null
	 */
	public static function get_widget( $type ) {
		$widgets = self::get_widgets();
		return $widgets[ $type ] ?? null;
	}

	/**
	 * Check if a widget is registered.
	 *
	 * @since 1.0.0
	 *
	 * @param string $type Widget type.
	 *
	 * @return bool
	 */
	public static function is_widget_registered( $type ) {
		$widgets = self::get_widgets();
		return isset( $widgets[ $type ] );
	}

	/**
	 * Get widget content.
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
		$widget = self::get_widget( $type );

		if ( null === $widget ) {
			return new \WP_Error( 'unknown_widget_type', 'Unknown widget type: ' . $type );
		}

		try {
			return $widget->get_validated_content( $widget_id, $settings );
		} catch ( \Exception $e ) {
			return new \WP_Error( 'widget_error', 'Widget error: ' . $e->getMessage() );
		}
	}

	/**
	 * Get widget types for frontend.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public static function get_widget_types_for_frontend() {
		$widget_types = [];
		$widgets = self::get_widgets();

		foreach ( $widgets as $type => $widget ) {
			$widget_types[ $type ] = $widget->get_definition();
		}

		return $widget_types;
	}

	/**
	 * Unregister a widget.
	 *
	 * @since 1.0.0
	 *
	 * @param string $type Widget type.
	 *
	 * @return bool
	 */
	public static function unregister_widget( $type ) {
		if ( ! self::is_widget_registered( $type ) ) {
			return false;
		}

		unset( self::$widgets[ $type ] );

		return true;
	}
}
