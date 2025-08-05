<?php
/**
 * Dashboard_Controller
 *
 * @package Dashmate
 */

declare(strict_types=1);

namespace Nilambar\Dashmate\API;

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
		$data = $this->get_dashboard_data();

		if ( is_wp_error( $data ) ) {
			return $data;
		}

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

		// Build new layout structure.
		$layout_columns = [];
		foreach ( $columns as $index => $column ) {
			$layout_columns[] = [
				'id'    => $column['id'] ?? 'col-' . ( $index + 1 ),
				'order' => $index + 1,
			];
		}

		$dashboard_data = [
			'layout'  => [
				'columns' => $layout_columns,
			],
			'widgets' => [],
		];

		$result = update_option( 'dashmate_dashboard_data', $dashboard_data, false );
		if ( false === $result ) {
			return $this->error_response( 'Unable to save dashboard data', 500, 'save_error' );
		}

		return $this->success_response( $dashboard_data, 201 );
	}

	/**
	 * Reorder widgets using column_widgets structure.
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
			$existing_widgets       = $dashboard_data['widgets'] ?? [];
			$existing_widgets_by_id = [];

			foreach ( $existing_widgets as $existing_widget ) {
				if ( isset( $existing_widget['id'] ) ) {
					$existing_widgets_by_id[ $existing_widget['id'] ] = $existing_widget;
				}
			}

			// Update widgets with new column_id and position based on column_widgets.
			$updated_widgets        = [];
			$updated_column_widgets = [];

			foreach ( $column_widgets as $column_id => $widget_ids ) {
				$updated_column_widgets[ $column_id ] = $widget_ids;

				foreach ( $widget_ids as $position => $widget_id ) {
					if ( isset( $existing_widgets_by_id[ $widget_id ] ) ) {
						$widget              = $existing_widgets_by_id[ $widget_id ];
						$widget['column_id'] = $column_id;
						$widget['position']  = $position + 1; // Convert to 1-based position.
						$updated_widgets[]   = $widget;
					}
				}
			}

			$dashboard_data['widgets']        = $updated_widgets;
			$dashboard_data['column_widgets'] = $updated_column_widgets;

			$current_option = get_option( 'dashmate_dashboard_data' );

			if ( $current_option === $dashboard_data ) {
				return $this->success_response( [ 'message' => 'Widgets reordered successfully' ] );
			}

			$result = update_option( 'dashmate_dashboard_data', $dashboard_data, true );
			if ( false === $result ) {
				return $this->error_response( 'Unable to save widget positions', 500, 'save_error' );
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
