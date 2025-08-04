<?php
/**
 * Widget_Manager
 *
 * @package Dashmate
 */

declare(strict_types=1);

namespace Nilambar\Dashmate;

use WP_Error;

/**
 * Widget_Manager class.
 *
 * @since 1.0.0
 */
class Widget_Manager {



	/**
	 * Create a new widget instance.
	 *
	 * @since 1.0.0
	 *
	 * @param string $type      Widget type.
	 * @param array  $settings  Initial settings.
	 * @param string $column_id Column ID to add widget to.
	 *
	 * @return array|WP_Error
	 */
	public static function create_widget( $type, $settings = [], $column_id = null ) {
		// Validate widget type.
		if ( ! Widget_Type_Manager::is_widget_type_registered( $type ) ) {
			return new WP_Error( 'unknown_widget_type', 'Unknown widget type: ' . $type );
		}

		// Generate unique widget ID.
		$widget_id = self::generate_widget_id( $type );

		// Create widget data.
		$widget = [
			'id'       => $widget_id,
			'type'     => $type,
			'settings' => $settings,
			'created'  => current_time( 'mysql' ),
			'updated'  => current_time( 'mysql' ),
		];

		// Add to dashboard if column_id provided.
		if ( $column_id ) {
			$result = self::add_widget_to_column( $widget, $column_id );
			if ( is_wp_error( $result ) ) {
				return $result;
			}
		}

		return $widget;
	}

	/**
	 * Get widget by ID.
	 *
	 * @since 1.0.0
	 *
	 * @param string $widget_id Widget ID.
	 *
	 * @return array|null
	 */
	public static function get_widget( $widget_id ) {
		$dashboard_data = self::get_dashboard_data();

		if ( is_wp_error( $dashboard_data ) ) {
			return null;
		}

		return self::find_widget_by_id( $widget_id, $dashboard_data );
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
			return new WP_Error( 'widget_not_found', 'Widget not found: ' . $widget_id );
		}

		// Update settings.
		$widget['settings'] = array_merge( $widget['settings'] ?? [], $settings );
		$widget['updated']  = current_time( 'mysql' );

		// Save dashboard data.
		return self::save_dashboard_data( $dashboard_data );
	}

	/**
	 * Delete widget.
	 *
	 * @since 1.0.0
	 *
	 * @param string $widget_id Widget ID.
	 *
	 * @return bool|WP_Error
	 */
	public static function delete_widget( $widget_id ) {
		$dashboard_data = self::get_dashboard_data();

		if ( is_wp_error( $dashboard_data ) ) {
			return $dashboard_data;
		}

		$widget_removed = false;

		// Remove widget from all columns.
		foreach ( $dashboard_data['columns'] ?? [] as &$column ) {
			if ( isset( $column['widgets'] ) ) {
				foreach ( $column['widgets'] as $key => $widget ) {
					if ( isset( $widget['id'] ) && $widget['id'] === $widget_id ) {
						unset( $column['widgets'][ $key ] );
						$widget_removed = true;
						break;
					}
				}
			}
		}

		if ( ! $widget_removed ) {
			return new WP_Error( 'widget_not_found', 'Widget not found: ' . $widget_id );
		}

		// Reindex widgets array.
		foreach ( $dashboard_data['columns'] ?? [] as &$column ) {
			if ( isset( $column['widgets'] ) ) {
				$column['widgets'] = array_values( $column['widgets'] );
			}
		}

		return self::save_dashboard_data( $dashboard_data );
	}

	/**
	 * Get widget content.
	 *
	 * @since 1.0.0
	 *
	 * @param string $widget_id Widget ID.
	 * @param array  $settings  Override settings (optional).
	 *
	 * @return array|WP_Error
	 */
	public static function get_widget_content( $widget_id, $settings = [] ) {
		// Use the new Widget_Dispatcher system instead of the legacy Widget_Type_Manager.
		return Widget_Dispatcher::get_widget_content( '', $widget_id, $settings );
	}

	/**
	 * Move widget to different column.
	 *
	 * @since 1.0.0
	 *
	 * @param string $widget_id  Widget ID.
	 * @param string $column_id  Target column ID.
	 * @param int    $position   Position in column (optional).
	 *
	 * @return bool|WP_Error
	 */
	public static function move_widget( $widget_id, $column_id, $position = null ) {
		$dashboard_data = self::get_dashboard_data();

		if ( is_wp_error( $dashboard_data ) ) {
			return $dashboard_data;
		}

		// Find widget and remove it from current location.
		$widget         = null;
		$widget_removed = false;

		foreach ( $dashboard_data['columns'] ?? [] as &$column ) {
			if ( isset( $column['widgets'] ) ) {
				foreach ( $column['widgets'] as $key => $column_widget ) {
					if ( isset( $column_widget['id'] ) && $column_widget['id'] === $widget_id ) {
						$widget = $column_widget;
						unset( $column['widgets'][ $key ] );
						$widget_removed = true;
						break 2;
					}
				}
			}
		}

		if ( ! $widget_removed ) {
			return new WP_Error( 'widget_not_found', 'Widget not found: ' . $widget_id );
		}

		// Find target column.
		$target_column = null;
		foreach ( $dashboard_data['columns'] ?? [] as &$column ) {
			if ( isset( $column['id'] ) && $column['id'] === $column_id ) {
				$target_column = &$column;
				break;
			}
		}

		if ( ! $target_column ) {
			return new WP_Error( 'column_not_found', 'Column not found: ' . $column_id );
		}

		// Add widget to target column.
		if ( ! isset( $target_column['widgets'] ) ) {
			$target_column['widgets'] = [];
		}

		if ( $position !== null && is_numeric( $position ) ) {
			array_splice( $target_column['widgets'], $position, 0, [ $widget ] );
		} else {
			$target_column['widgets'][] = $widget;
		}

		return self::save_dashboard_data( $dashboard_data );
	}

		/**
		 * Generate unique widget ID.
		 *
		 * @since 1.0.0
		 *
		 * @param string $type Widget type.
		 *
		 * @return string
		 */
	private static function generate_widget_id( $type ) {
		$prefix    = str_replace( '-', '_', $type );
		$timestamp = time();
		$random    = self::generate_random_string( 4 );

		return $prefix . '_' . $timestamp . '_' . $random;
	}

	/**
	 * Generate random string.
	 *
	 * @since 1.0.0
	 *
	 * @param int $length String length.
	 *
	 * @return string
	 */
	private static function generate_random_string( $length = 4 ) {
		$characters    = 'abcdefghijklmnopqrstuvwxyz0123456789';
		$random_string = '';

		for ( $i = 0; $i < $length; $i++ ) {
			$random_string .= $characters[ rand( 0, strlen( $characters ) - 1 ) ];
		}

		return $random_string;
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
		foreach ( $dashboard_data['columns'] ?? [] as $column ) {
			if ( isset( $column['widgets'] ) ) {
				foreach ( $column['widgets'] as $widget ) {
					if ( isset( $widget['id'] ) && $widget['id'] === $widget_id ) {
						return $widget;
					}
				}
			}
		}

		return null;
	}

	/**
	 * Add widget to column.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $widget    Widget data.
	 * @param string $column_id Column ID.
	 *
	 * @return bool|WP_Error
	 */
	private static function add_widget_to_column( $widget, $column_id ) {
		$dashboard_data = self::get_dashboard_data();

		if ( is_wp_error( $dashboard_data ) ) {
			return $dashboard_data;
		}

		// Find target column.
		$target_column = null;
		foreach ( $dashboard_data['columns'] ?? [] as &$column ) {
			if ( isset( $column['id'] ) && $column['id'] === $column_id ) {
				$target_column = &$column;
				break;
			}
		}

		if ( ! $target_column ) {
			return new WP_Error( 'column_not_found', 'Column not found: ' . $column_id );
		}

		// Add widget to column.
		if ( ! isset( $target_column['widgets'] ) ) {
			$target_column['widgets'] = [];
		}

		$target_column['widgets'][] = $widget;

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
			// Trigger Widget_Initializer to create default data.
			Widget_Initializer::create_default_widgets();

			// Get the data again after initialization.
			$data = get_option( 'dashmate_dashboard_data', null );

			if ( null === $data ) {
				return [
					'layout'         => [
						'columns' => [],
					],
					'widgets'        => [],
					'column_widgets' => [],
				];
			}
		}

		// Ensure we always return the new structure.
		if ( ! isset( $data['layout'] ) || ! isset( $data['widgets'] ) ) {
			// Trigger Widget_Initializer to create default data.
			Widget_Initializer::create_default_widgets();

			// Get the data again after initialization.
			$data = get_option( 'dashmate_dashboard_data', null );

			if ( null === $data || ! isset( $data['layout'] ) || ! isset( $data['widgets'] ) ) {
				return [
					'layout'         => [
						'columns' => [],
					],
					'widgets'        => [],
					'column_widgets' => [],
				];
			}
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
			return new WP_Error( 'option_save_error', 'Could not save dashboard data to WordPress options' );
		}

		return true;
	}
}
