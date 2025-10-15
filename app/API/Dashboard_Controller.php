<?php
/**
 * Dashboard_Controller
 *
 * @package Dashmate
 */

declare(strict_types=1);

namespace Nilambar\Dashmate\API;

use Nilambar\Dashmate\Services\Dashboard_Manager;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

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
	protected $base_route = 'dashboards';

	/**
	 * Register routes.
	 *
	 * @since 1.0.0
	 */
	public function register_routes() {
		// Get dashboard data.
		register_rest_route(
			$this->get_namespace(),
			'/' . $this->get_base_route() . '/(?P<dashboard_id>[a-zA-Z0-9_-]+)',
			[
				[
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => [ $this, 'get_dashboard' ],
					'permission_callback' => [ $this, 'check_permissions' ],
					'args'                => [
						'dashboard_id' => [
							'required'          => true,
							'validate_callback' => [ $this, 'validate_dashboard_id' ],
						],
					],
				],
				[
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => [ $this, 'save_dashboard' ],
					'permission_callback' => [ $this, 'check_permissions' ],
					'args'                => [
						'dashboard_id' => [
							'required'          => true,
							'validate_callback' => [ $this, 'validate_dashboard_id' ],
						],
						'columns'      => [
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
		$dashboard_id = $request->get_param( 'dashboard_id' );
		$data         = Dashboard_Manager::get_enhanced_dashboard_data( $dashboard_id );

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
		$dashboard_id = $request->get_param( 'dashboard_id' );
		$columns      = $request->get_param( 'columns' );

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

		$result = Dashboard_Manager::save_dashboard_data( $dashboard_data, $dashboard_id );
		if ( is_wp_error( $result ) ) {
			return $this->error_response( 'Unable to save dashboard data: ' . $result->get_error_message(), 500, 'save_error' );
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
		}

		return true;
	}

	/**
	 * Validate dashboard ID.
	 *
	 * @since 1.0.0
	 *
	 * @param string $dashboard_id Dashboard ID.
	 *
	 * @return bool|WP_Error
	 */
	public function validate_dashboard_id( $dashboard_id ) {
		if ( empty( $dashboard_id ) ) {
			return new WP_Error( 'invalid_dashboard_id', 'Dashboard ID is required' );
		}

		if ( ! preg_match( '/^[a-zA-Z0-9_-]+$/', $dashboard_id ) ) {
			return new WP_Error( 'invalid_dashboard_id', 'Dashboard ID contains invalid characters' );
		}

		return true;
	}
}
