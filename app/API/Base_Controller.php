<?php
/**
 * Base_Controller
 *
 * @package Dashmate
 */

declare(strict_types=1);

namespace Nilambar\Dashmate\API;

use Nilambar\Dashmate\Core\Dashboard_Manager;
use Nilambar\Dashmate\Widget_Manager;
use WP_Error;
use WP_REST_Response;

/**
 * Base_Controller class.
 *
 * @since 1.0.0
 */
abstract class Base_Controller {

	/**
	 * Namespace.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $namespace = 'dashmate/v1';

	/**
	 * Base route.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $base_route = '';

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'rest_api_init', [ $this, 'register_routes' ] );
	}

	/**
	 * Register routes.
	 *
	 * @since 1.0.0
	 */
	abstract public function register_routes();

	/**
	 * Get namespace.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	protected function get_namespace() {
		return $this->namespace;
	}

	/**
	 * Get base route.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	protected function get_base_route() {
		return $this->base_route;
	}

	/**
	 * Get full route.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	protected function get_route() {
		return '/' . $this->get_namespace() . '/' . $this->get_base_route();
	}

	/**
	 * Check permissions.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function check_permissions() {
		// Allow all requests for development.
		return true;
	}

	/**
	 * Success response.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $data    Response data.
	 * @param int   $status  HTTP status code.
	 * @param array $headers Response headers.
	 *
	 * @return WP_REST_Response
	 */
	protected function success_response( $data, $status = 200, $headers = [] ) {
		return new WP_REST_Response(
			[
				'success' => true,
				'data'    => $data,
			],
			$status,
			$headers
		);
	}

	/**
	 * Error response.
	 *
	 * @since 1.0.0
	 *
	 * @param string $message Error message.
	 * @param int    $status  HTTP status code.
	 * @param string $code    Error code.
	 *
	 * @return WP_Error
	 */
	protected function error_response( $message, $status = 400, $code = 'error' ) {
		return new WP_Error(
			$code,
			$message,
			[ 'status' => $status ]
		);
	}

	/**
	 * Get dashboard data from WordPress options.
	 *
	 * @since 1.0.0
	 *
	 * @return array|WP_Error
	 */
	protected function get_dashboard_data() {
		$data = Dashboard_Manager::get_dashboard_data();

		// Filter out disabled widgets from the dashboard data.
		$data = $this->filter_disabled_widgets_from_dashboard( $data );

		return $data;
	}

	/**
	 * Filter out disabled widgets from dashboard data.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data Dashboard data.
	 *
	 * @return array Filtered dashboard data.
	 */
	protected function filter_disabled_widgets_from_dashboard( $data ) {
		// Get disabled widgets from centralized method.
		$disabled_widgets = Widget_Manager::get_disabled_widgets();

		if ( empty( $disabled_widgets ) ) {
			// Convert widgets object to array for frontend consistency.
			if ( isset( $data['widgets'] ) && is_array( $data['widgets'] ) ) {
				$data['widgets'] = array_values( $data['widgets'] );
			}
			return $data;
		}

		// Handle widgets as object and convert to array for filtering.
		if ( isset( $data['widgets'] ) && is_array( $data['widgets'] ) ) {
			// Convert object to array if needed.
			$widgets_array = array_values( $data['widgets'] );

			// Filter out disabled widgets.
			$filtered_widgets = array_filter(
				$widgets_array,
				function ( $widget ) use ( $disabled_widgets ) {
					return ! isset( $widget['id'] ) || ! in_array( $widget['id'], $disabled_widgets, true );
				}
			);

			// Convert back to array for frontend.
			$data['widgets'] = array_values( $filtered_widgets );
		}

		// Create a list of active widget IDs for reference.
		$active_widget_ids = [];
		if ( isset( $data['widgets'] ) && is_array( $data['widgets'] ) ) {
			foreach ( $data['widgets'] as $widget ) {
				if ( isset( $widget['id'] ) ) {
					$active_widget_ids[] = $widget['id'];
				}
			}
		}

		// Filter out disabled widgets from column_widgets and ensure consistency.
		if ( isset( $data['column_widgets'] ) && is_array( $data['column_widgets'] ) ) {
			foreach ( $data['column_widgets'] as $column_id => &$widget_ids ) {
				if ( is_array( $widget_ids ) ) {
					// Filter out disabled widgets and ensure they exist in the widgets array.
					$widget_ids = array_values(
						array_filter(
							$widget_ids,
							function ( $widget_id ) use ( $disabled_widgets, $active_widget_ids ) {
								// Remove if widget is disabled or doesn't exist in widgets array.
								return ! in_array( $widget_id, $disabled_widgets, true ) &&
										in_array( $widget_id, $active_widget_ids, true );
							}
						)
					);
				} else {
					// Ensure it's always an array.
					$widget_ids = [];
				}
			}
		}

		return $data;
	}
}
