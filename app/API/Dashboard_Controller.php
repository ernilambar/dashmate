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
				'width' => $column['width'] ?? '50%',
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

			// Width is optional, but if provided it should be a string
			if ( isset( $column['width'] ) && ! is_string( $column['width'] ) ) {
				return new WP_Error( 'invalid_width', 'Column width must be a string' );
			}
		}

		return true;
	}
}
