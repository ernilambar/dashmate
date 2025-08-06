<?php
/**
 * Widget_Dispatcher
 *
 * @package Dashmate
 */

declare(strict_types=1);

namespace Nilambar\Dashmate;

use Nilambar\Dashmate\Core\Dashboard_Manager;

/**
 * Widget_Dispatcher class.
 *
 * @since 1.0.0
 */
class Widget_Dispatcher {

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
		$id = $widget->get_id();

		if ( empty( $id ) ) {
			return false;
		}

		self::$widgets[ $id ] = $widget;

		return true;
	}

	/**
	 * Create a widget instance with ID.
	 *
	 * @since 1.0.0
	 *
	 * @param string $type Widget type.
	 * @param string $id   Widget instance ID.
	 *
	 * @return Abstract_Widget|null
	 */
	public static function create_widget_instance( $type, $id ) {
		// Get widget by ID from registered widgets.
		$widget = self::get_widget( $id );

		if ( null === $widget ) {
			return null;
		}

		// Return a new instance of the same class with the specified ID.
		$widget_class = get_class( $widget );
		return new $widget_class( $id );
	}

	/**
	 * Get widget class for a type.
	 *
	 * @since 1.0.0
	 *
	 * @param string $type Widget type.
	 * @param string $id   Widget ID.
	 *
	 * @return string|null
	 */
	private static function get_widget_class( $type, $id = null ) {
		// Get widget by ID from registered widgets.
		$widgets = self::get_widgets();

		if ( $id && isset( $widgets[ $id ] ) ) {
			return get_class( $widgets[ $id ] );
		}

		return null;
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
	 * Get active widgets (excluding inactive ones).
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public static function get_active_widgets() {
		$all_widgets = self::get_widgets();

		// Get disabled widgets from centralized method.
		$disabled_widgets = Widget_Manager::get_disabled_widgets();

		// Filter out disabled widgets.
		$active_widgets = [];
		foreach ( $all_widgets as $id => $widget ) {
			if ( ! in_array( $id, $disabled_widgets, true ) ) {
				$active_widgets[ $id ] = $widget;
			}
		}

		return $active_widgets;
	}

	/**
	 * Get a specific widget.
	 *
	 * @since 1.0.0
	 *
	 * @param string $id Widget ID.
	 *
	 * @return Abstract_Widget|null
	 */
	public static function get_widget( $id ) {
		$widgets = self::get_active_widgets();
		return $widgets[ $id ] ?? null;
	}

	/**
	 * Check if a widget is registered.
	 *
	 * @since 1.0.0
	 *
	 * @param string $id Widget ID.
	 *
	 * @return bool
	 */
	public static function is_widget_registered( $id ) {
		$widgets = self::get_active_widgets();
		return isset( $widgets[ $id ] );
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
	public static function get_widget_content( $type, $widget_id, $settings = [] ) {
		// Get widget by ID.
		$widget = self::get_widget( $widget_id );

		if ( null === $widget ) {
			return new \WP_Error( 'unknown_widget', 'Unknown widget: ' . $widget_id );
		}

		try {
			return $widget->get_validated_content( $settings );
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
		$widgets      = self::get_active_widgets();

		// First, get all available template types from the registry
		$templates = Widget_Template_Registry::get_templates();

		// Initialize with all available template types
		foreach ( $templates as $template_type => $template_config ) {
			$widget_types[ $template_type ] = [
				'name'            => $template_config['component'],
				'description'     => 'Widget using ' . $template_type . ' template',
				'icon'            => 'admin-generic',
				'template_type'   => $template_type,
				'settings_schema' => [],
				'output_schema'   => [],
			];
		}

		// Then, create individual widget entries with their own schemas
		foreach ( $widgets as $id => $widget ) {
			$template_type     = $widget->get_template_type();
			$widget_definition = $widget->get_definition();

			// Create a unique key for this widget using its ID
			$widget_key = $id;

			// Add the widget with its own schema
			$widget_types[ $widget_key ] = array_merge(
				$widget_definition,
				[
					'template_type' => $template_type,
					'widget_id'     => $id,
				]
			);
		}

		return $widget_types;
	}

	/**
	 * Unregister a widget.
	 *
	 * @since 1.0.0
	 *
	 * @param string $id Widget ID.
	 *
	 * @return bool
	 */
	public static function unregister_widget( $id ) {
		if ( ! self::is_widget_registered( $id ) ) {
			return false;
		}

		unset( self::$widgets[ $id ] );

		return true;
	}

	/**
	 * Update widget settings.
	 *
	 * @since 1.0.0
	 *
	 * @param string $widget_id Widget ID.
	 * @param array  $settings  New settings.
	 *
	 * @return bool|WP_Error
	 */
	public static function update_widget_settings( $widget_id, $settings ) {
		$dashboard_data = self::get_dashboard_data();

		if ( is_wp_error( $dashboard_data ) ) {
			return $dashboard_data;
		}

		$widgets = &$dashboard_data['widgets'];

		// Find the widget and update its settings.
		foreach ( $widgets as $index => $widget ) {
			if ( isset( $widget['id'] ) && $widget['id'] === $widget_id ) {
				$current_settings = $widget['settings'] ?? [];
				$new_settings     = array_merge( $current_settings, $settings );

				// Check if there are actual changes
				if ( $current_settings === $new_settings ) {
					// No changes made, return success without saving
					return true;
				}

				$widgets[ $index ]['settings'] = $new_settings;
				return self::save_dashboard_data( $dashboard_data );
			}
		}

		return new \WP_Error( 'widget_not_found', 'Widget not found: ' . $widget_id );
	}

	/**
	 * Get dashboard data from WordPress options.
	 *
	 * @since 1.0.0
	 *
	 * @return array|WP_Error
	 */
	private static function get_dashboard_data() {
		return Dashboard_Manager::get_dashboard_data();
	}

	/**
	 * Save dashboard data to WordPress options.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data Dashboard data.
	 *
	 * @return bool|WP_Error
	 */
	private static function save_dashboard_data( $data ) {
		return Dashboard_Manager::save_dashboard_data( $data );
	}

	/**
	 * Find widget by ID in dashboard data.
	 *
	 * @since 1.0.0
	 *
	 * @param string $widget_id      Widget ID.
	 * @param array  $dashboard_data Dashboard data.
	 *
	 * @return array|null
	 */
	private static function find_widget_by_id( $widget_id, $dashboard_data ) {
		$widgets = $dashboard_data['widgets'] ?? [];

		foreach ( $widgets as $widget ) {
			if ( isset( $widget['id'] ) && $widget['id'] === $widget_id ) {
				return $widget;
			}
		}

		return null;
	}

	/**
	 * Get widgets for a specific column.
	 *
	 * @since 1.0.0
	 *
	 * @param string $column_id      Column ID.
	 * @param array  $dashboard_data Dashboard data.
	 *
	 * @return array
	 */
	public static function get_widgets_for_column( $column_id, $dashboard_data = null ) {
		if ( null === $dashboard_data ) {
			$dashboard_data = self::get_dashboard_data();
		}

		if ( is_wp_error( $dashboard_data ) ) {
			return [];
		}

		$widgets        = $dashboard_data['widgets'] ?? [];
		$column_widgets = [];

		foreach ( $widgets as $widget ) {
			if ( isset( $widget['column_id'] ) && $widget['column_id'] === $column_id ) {
				$column_widgets[] = $widget;
			}
		}

		return $column_widgets;
	}
}
