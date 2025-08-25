<?php
/**
 * Dashboard_Controller
 *
 * @package Dashmate
 */

declare(strict_types=1);

namespace Nilambar\Dashmate\API;

use Nilambar\Dashmate\Dashboard_Manager;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;

/**
 * Dashboard_Controller class.
 *
 * @since 1.0.0
 */
class Dashboard_Controller extends Base_Controller {

	/**
	 * Base route.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $base_route = 'dashboard';

	/**
	 * Register routes.
	 *
	 * @since 1.0.0
	 */
	public function register_routes() {
		register_rest_route(
			$this->get_namespace(),
			'/' . $this->get_base_route(),
			[
				[
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => [ $this, 'get_dashboard' ],
					'permission_callback' => [ $this, 'check_permissions' ],
					'args'                => [],
				],
				[
					'methods'             => \WP_REST_Server::EDITABLE,
					'callback'            => [ $this, 'save_dashboard' ],
					'permission_callback' => [ $this, 'check_permissions' ],
					'args'                => [
						'columns' => [
							'required'          => true,
							'type'              => 'array',
							'validate_callback' => [ $this, 'validate_columns' ],
						],
					],
				],
			]
		);

		// Batch reorder widgets endpoint.
		register_rest_route(
			$this->get_namespace(),
			'/' . $this->get_base_route() . '/reorder',
			[
				[
					'methods'             => \WP_REST_Server::EDITABLE,
					'callback'            => [ $this, 'reorder_widgets' ],
					'permission_callback' => [ $this, 'check_permissions' ],
					'args'                => [
						'column_widgets' => [
							'required' => true,
							'type'     => 'object',
						],
					],
				],
			]
		);
	}

	/**
	 * Get dashboard.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function get_dashboard( $request ) {
		$data = Dashboard_Manager::get_enhanced_dashboard_data();

		return $this->success_response( $data );
	}

	/**
	 * Save dashboard.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function save_dashboard( $request ) {
		$columns = $request->get_param( 'columns' );

		if ( ! is_array( $columns ) ) {
			return $this->error_response( 'Columns must be an array', 400, 'invalid_columns' );
		}

		// Build new layout structure with widgets nested in columns.
		$layout_columns = [];
		foreach ( $columns as $index => $column ) {
			$layout_columns[] = [
				'id'      => $column['id'] ?? 'col-' . ( $index + 1 ),
				'widgets' => $column['widgets'] ?? [],
			];
		}

		$dashboard_data = [
			'columns' => $layout_columns,
		];

		$result = Dashboard_Manager::save_dashboard_data( $dashboard_data );
		if ( is_wp_error( $result ) ) {
			return $this->error_response( 'Unable to save dashboard data: ' . $result->get_error_message(), 500, 'save_error' );
		}

		return $this->success_response( $dashboard_data, 201 );
	}

	/**
	 * Reorder widgets.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function reorder_widgets( $request ) {
		try {
			$column_widgets = $request->get_param( 'column_widgets' );

			if ( ! is_array( $column_widgets ) ) {
				return $this->error_response( 'Column widgets must be an array', 400, 'invalid_column_widgets' );
			}

			$dashboard_data = $this->get_dashboard_data();

			if ( is_wp_error( $dashboard_data ) ) {
				return $dashboard_data;
			}

			// Get existing widgets for reference.
			$existing_widgets_by_id = [];
			if ( isset( $dashboard_data['columns'] ) && is_array( $dashboard_data['columns'] ) ) {
				foreach ( $dashboard_data['columns'] as $column ) {
					if ( isset( $column['widgets'] ) && is_array( $column['widgets'] ) ) {
						foreach ( $column['widgets'] as $widget ) {
							if ( isset( $widget['id'] ) ) {
								$existing_widgets_by_id[ $widget['id'] ] = $widget;
							}
						}
					}
				}
			}

			// Update columns with new widget order.
			$updated_columns = [];

			foreach ( $column_widgets as $column_id => $widget_ids ) {
				$column = [
					'id'      => $column_id,
					'widgets' => [],
				];

				foreach ( $widget_ids as $widget_id ) {
					if ( isset( $existing_widgets_by_id[ $widget_id ] ) ) {
						$column['widgets'][] = $existing_widgets_by_id[ $widget_id ];
					} else {
						// Add new widget to the dashboard.
						$column['widgets'][] = [
							'id'        => $widget_id,
							'settings'  => [],
							'collapsed' => false,
						];
					}
				}

				$updated_columns[] = $column;
			}

			$dashboard_data['columns'] = $updated_columns;

			$current_data = $this->get_dashboard_data();

			if ( $current_data === $dashboard_data ) {
				return $this->success_response( [ 'message' => 'Widgets reordered successfully' ] );
			}

			$result = Dashboard_Manager::save_dashboard_data( $dashboard_data );
			if ( is_wp_error( $result ) ) {
				return $this->error_response( 'Unable to save widget order: ' . $result->get_error_message(), 500, 'save_error' );
			}

			return $this->success_response( [ 'message' => 'Widgets reordered successfully' ] );
		} catch ( Exception $e ) {
			return $this->error_response( 'Internal server error: ' . $e->getMessage(), 500, 'internal_error' );
		}
	}

	/**
	 * Validate columns.
	 *
	 * @since 1.0.0
	 *
	 * @param array $columns Columns data.
	 *
	 * @return bool|WP_Error
	 */
	public function validate_columns( $columns ) {
		if ( ! is_array( $columns ) ) {
			return new WP_Error( 'invalid_columns', 'Columns must be an array' );
		}

		foreach ( $columns as $column ) {
			if ( ! isset( $column['id'] ) ) {
				return new WP_Error( 'invalid_column', 'Each column must have an id' );
			}
		}

		return true;
	}
}
