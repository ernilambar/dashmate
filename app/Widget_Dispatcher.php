<?php
/**
 * Widget_Dispatcher
 *
 * @package Dashmate
 */

declare(strict_types=1);

namespace Nilambar\Dashmate;

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
	 * Get a specific widget.
	 *
	 * @since 1.0.0
	 *
	 * @param string $id Widget ID.
	 *
	 * @return Abstract_Widget|null
	 */
	public static function get_widget( $id ) {
		$widgets = self::get_widgets();
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
		$widgets = self::get_widgets();
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
	public static function get_widget_content( $type, $widget_id = null, $settings = [] ) {
		// Get widget by ID.
		$widget = self::get_widget( $widget_id );

		if ( null === $widget ) {
			return new \WP_Error( 'unknown_widget', 'Unknown widget: ' . $widget_id );
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
		$widgets      = self::get_widgets();

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

		$widget = self::find_widget_by_id( $widget_id, $dashboard_data );

		if ( ! $widget ) {
			return new \WP_Error( 'widget_not_found', 'Widget not found: ' . $widget_id );
		}

		// Update settings.
		$widget['settings'] = array_merge( $widget['settings'] ?? [], $settings );

		// Save dashboard data.
		return self::save_dashboard_data( $dashboard_data );
	}

	/**
	 * Get dashboard data from WordPress options.
	 *
	 * @since 1.0.0
	 *
	 * @return array|WP_Error
	 */
	private static function get_dashboard_data() {
		$data = get_option( 'dashmate_dashboard_data', null );

		if ( null === $data ) {
			// Return default dashboard structure if no data exists.
			return self::get_default_dashboard_data();
		}

		return $data;
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
		$result = update_option( 'dashmate_dashboard_data', $data, false );

		if ( false === $result ) {
			return new \WP_Error( 'option_update_error', 'Unable to save dashboard data to WordPress options' );
		}

		return true;
	}

	/**
	 * Get default dashboard data.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	private static function get_default_dashboard_data() {
		return [
			'layout'  => [
				'columns' => [
					[
						'id'    => 'col-1',
						'order' => 1,
						'width' => '50%',
					],
					[
						'id'    => 'col-2',
						'order' => 2,
						'width' => '50%',
					],
				],
			],
			'widgets' => [
				[
					'id'        => 'welcome-html-1',
					'column_id' => 'col-1',
					'position'  => 1,
					'settings'  => [
						'allow_scripts' => false,
					],
				],
				[
					'id'        => 'quick-links-1',
					'column_id' => 'col-2',
					'position'  => 1,
					'settings'  => [
						'customTitle' => 'Quick Access',
						'filterLinks' => 'content',
						'hideIcon'    => false,
						'showTitle'   => true,
						'linkStyle'   => 'list',
					],
				],
			],
		];
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

		// Sort by position.
		usort(
			$column_widgets,
			function ( $a, $b ) {
				return ( $a['position'] ?? 0 ) - ( $b['position'] ?? 0 );
			}
		);

		return $column_widgets;
	}

	/**
	 * Move widget to a new position.
	 *
	 * @since 1.0.0
	 *
	 * @param string $widget_id Widget ID.
	 * @param string $column_id Column ID.
	 * @param int    $position  New position.
	 *
	 * @return bool|WP_Error
	 */
	public static function move_widget( $widget_id, $column_id, $position ) {
		$dashboard_data = self::get_dashboard_data();

		if ( is_wp_error( $dashboard_data ) ) {
			return $dashboard_data;
		}

		$widgets = &$dashboard_data['widgets'];

		// Find the widget.
		$widget_index = null;
		foreach ( $widgets as $index => $widget ) {
			if ( isset( $widget['id'] ) && $widget['id'] === $widget_id ) {
				$widget_index = $index;
				break;
			}
		}

		if ( null === $widget_index ) {
			return new \WP_Error( 'widget_not_found', 'Widget not found: ' . $widget_id );
		}

		// Update widget position and column.
		$widgets[ $widget_index ]['column_id'] = $column_id;
		$widgets[ $widget_index ]['position']  = $position;

		// Reorder other widgets in the same column.
		$column_widgets = self::get_widgets_for_column( $column_id, $dashboard_data );
		$new_position   = 1;

		foreach ( $column_widgets as $column_widget ) {
			if ( $column_widget['id'] !== $widget_id ) {
				// Find and update this widget's position.
				foreach ( $widgets as $index => $widget ) {
					if ( isset( $widget['id'] ) && $widget['id'] === $column_widget['id'] ) {
						$widgets[ $index ]['position'] = $new_position;
						break;
					}
				}
				++$new_position;
			}
		}

		return self::save_dashboard_data( $dashboard_data );
	}

	/**
	 * Get next available position for a column.
	 *
	 * @since 1.0.0
	 *
	 * @param string $column_id Column ID.
	 *
	 * @return int
	 */
	public static function get_next_position_for_column( $column_id ) {
		$dashboard_data = self::get_dashboard_data();

		if ( is_wp_error( $dashboard_data ) ) {
			return 1;
		}

		$column_widgets = self::get_widgets_for_column( $column_id, $dashboard_data );

		if ( empty( $column_widgets ) ) {
			return 1;
		}

		$max_position = 0;
		foreach ( $column_widgets as $widget ) {
			$position = $widget['position'] ?? 0;
			if ( $position > $max_position ) {
				$max_position = $position;
			}
		}

		return $max_position + 1;
	}
}
