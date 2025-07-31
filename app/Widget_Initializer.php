<?php
/**
 * Widget_Initializer
 *
 * @package Dashmate
 */

declare(strict_types=1);

namespace Nilambar\Dashmate;

/**
 * Widget_Initializer class.
 *
 * @since 1.0.0
 */
class Widget_Initializer {

	/**
	 * Initialize the widget system.
	 *
	 * @since 1.0.0
	 */
	public static function init() {
		// Initialize the new widget system.
		Widget_Registry::init();

		// Create default widget instances if they don't exist.
		self::create_default_widgets();
	}

	/**
	 * Create default widget instances.
	 *
	 * @since 1.0.0
	 */
	private static function create_default_widgets() {
		// Check if dashboard already has widgets.
		$dashboard_data = self::get_dashboard_data();

		if ( ! is_wp_error( $dashboard_data ) && ! empty( $dashboard_data['columns'] ) ) {
			// Check if any column has widgets.
			$has_widgets = false;
			foreach ( $dashboard_data['columns'] as $column ) {
				if ( ! empty( $column['widgets'] ) ) {
					$has_widgets = true;
					break;
				}
			}

			if ( $has_widgets ) {
				// Widgets already exist, don't create defaults.
				return;
			}
		}

		// Create default dashboard structure if it doesn't exist.
		if ( is_wp_error( $dashboard_data ) || empty( $dashboard_data['columns'] ) ) {
			$dashboard_data = [
				'columns' => [
					[
						'id'      => 'col-1',
						'widgets' => [],
					],
					[
						'id'      => 'col-2',
						'widgets' => [],
					],
				],
			];
		}

		// Create widget instances.
		$widgets = self::create_widget_instances();

		// Add widgets to columns.
		$widget_index = 0;
		foreach ( $widgets as $widget ) {
			$column_index = $widget_index % 2; // Distribute between 2 columns.
			$dashboard_data['columns'][ $column_index ]['widgets'][] = $widget;
			++$widget_index;
		}

		// Save dashboard data.
		self::save_dashboard_data( $dashboard_data );
	}

	/**
	 * Create widget instances for each type.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	private static function create_widget_instances() {
		$widgets = [];

		// HTML Widget
		$widgets[] = [
			'id'       => 'html-1',
			'type'     => 'html',
			'settings' => [
				'allow_scripts' => false,
			],
		];

		return $widgets;
	}

	/**
	 * Get dashboard data.
	 *
	 * @since 1.0.0
	 *
	 * @return array|WP_Error
	 */
	private static function get_dashboard_data() {
		$data = get_option( 'dashmate_dashboard_data', null );
		if ( null === $data ) {
			return [
				'columns' => [
					[
						'id'      => 'col-1',
						'widgets' => [],
					],
					[
						'id'      => 'col-2',
						'widgets' => [],
					],
				],
			];
		}
		return $data;
	}

	/**
	 * Save dashboard data.
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
			return new \WP_Error( 'option_update_error', 'Could not save dashboard data to options' );
		}
		return true;
	}
}
