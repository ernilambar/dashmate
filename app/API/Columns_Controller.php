<?php
/**
 * Columns_Controller
 *
 * @package Dashmate
 */

declare(strict_types=1);

namespace Nilambar\Dashmate\API;

use Nilambar\Dashmate\Core\Dashboard_Manager;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;

/**
 * Columns_Controller class.
 *
 * @since 1.0.0
 */
class Columns_Controller extends Base_Controller {

	/**
	 * Base route.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $base_route = 'columns';

	/**
	 * Register routes.
	 *
	 * @since 1.0.0
	 */
	public function register_routes() {
		// Get all columns.
		register_rest_route(
			$this->get_namespace(),
			'/' . $this->get_base_route(),
			[
				[
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => [ $this, 'get_columns' ],
					'permission_callback' => [ $this, 'check_permissions' ],
					'args'                => [],
				],
				[
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => [ $this, 'create_column' ],
					'permission_callback' => [ $this, 'check_permissions' ],
					'args'                => [
						'title' => [
							'required' => true,
							'type'     => 'string',
						],
					],
				],
			]
		);

		// Get, update, delete specific column.
		register_rest_route(
			$this->get_namespace(),
			'/' . $this->get_base_route() . '/(?P<id>[a-zA-Z0-9-]+)',
			[
				[
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => [ $this, 'get_column' ],
					'permission_callback' => [ $this, 'check_permissions' ],
					'args'                => [
						'id' => [
							'required'          => true,
							'validate_callback' => [ $this, 'validate_column_id' ],
						],
					],
				],
				[
					'methods'             => \WP_REST_Server::EDITABLE,
					'callback'            => [ $this, 'update_column' ],
					'permission_callback' => [ $this, 'check_permissions' ],
					'args'                => [
						'id'    => [
							'required'          => true,
							'validate_callback' => [ $this, 'validate_column_id' ],
						],
						'title' => [
							'required' => false,
							'type'     => 'string',
						],
					],
				],
				[
					'methods'             => \WP_REST_Server::DELETABLE,
					'callback'            => [ $this, 'delete_column' ],
					'permission_callback' => [ $this, 'check_permissions' ],
					'args'                => [
						'id' => [
							'required'          => true,
							'validate_callback' => [ $this, 'validate_column_id' ],
						],
					],
				],
			]
		);
	}

	/**
	 * Get columns.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function get_columns( $request ) {
		$dashboard_data = $this->get_dashboard_data();

		if ( is_wp_error( $dashboard_data ) ) {
			return $dashboard_data;
		}

		$columns = $dashboard_data['columns'] ?? [];

		return $this->success_response( $columns );
	}

	/**
	 * Create column.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function create_column( $request ) {
		$title = $request->get_param( 'title' );

		// Get current dashboard data.
		$dashboard_data = $this->get_dashboard_data();

		if ( is_wp_error( $dashboard_data ) ) {
			return $dashboard_data;
		}

		$columns = $dashboard_data['columns'] ?? [];

		// Generate unique ID.
		$column_id = 'col-' . uniqid();

		$new_column = [
			'id'      => $column_id,
			'title'   => $title,
			'widgets' => [],
		];

		$columns[]                 = $new_column;
		$dashboard_data['columns'] = $columns;

		// Save updated dashboard data.
		$result = Dashboard_Manager::save_dashboard_data( $dashboard_data );

		if ( is_wp_error( $result ) ) {
			return $this->error_response( 'Unable to save dashboard data: ' . $result->get_error_message(), 500, 'save_error' );
		}

		return $this->success_response( $new_column, 201 );
	}

	/**
	 * Get column.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function get_column( $request ) {
		$column_id = $request->get_param( 'id' );

		$dashboard_data = $this->get_dashboard_data();

		if ( is_wp_error( $dashboard_data ) ) {
			return $dashboard_data;
		}

		$column = $this->find_column_by_id( $column_id, $dashboard_data );

		if ( ! $column ) {
			return $this->error_response( 'Column not found: ' . $column_id, 404, 'column_not_found' );
		}

		return $this->success_response( $column );
	}

	/**
	 * Update column.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function update_column( $request ) {
		$column_id = $request->get_param( 'id' );
		$title     = $request->get_param( 'title' );

		$dashboard_data = $this->get_dashboard_data();

		if ( is_wp_error( $dashboard_data ) ) {
			return $dashboard_data;
		}

		$column = $this->find_column_by_id( $column_id, $dashboard_data );

		if ( ! $column ) {
			return $this->error_response( 'Column not found: ' . $column_id, 404, 'column_not_found' );
		}

		$this->update_column_data( $column_id, $title, $dashboard_data );

		$result = Dashboard_Manager::save_dashboard_data( $dashboard_data );

		if ( is_wp_error( $result ) ) {
			return $this->error_response( 'Unable to save dashboard data: ' . $result->get_error_message(), 500, 'save_error' );
		}

		return $this->success_response( $column );
	}

	/**
	 * Delete column.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function delete_column( $request ) {
		$column_id = $request->get_param( 'id' );

		$dashboard_data = $this->get_dashboard_data();

		if ( is_wp_error( $dashboard_data ) ) {
			return $dashboard_data;
		}

		$column = $this->find_column_by_id( $column_id, $dashboard_data );

		if ( ! $column ) {
			return $this->error_response( 'Column not found: ' . $column_id, 404, 'column_not_found' );
		}

		$this->remove_column( $column_id, $dashboard_data );

		$result = Dashboard_Manager::save_dashboard_data( $dashboard_data );

		if ( is_wp_error( $result ) ) {
			return $this->error_response( 'Unable to save dashboard data: ' . $result->get_error_message(), 500, 'save_error' );
		}

		return $this->success_response( [ 'message' => 'Column deleted successfully' ] );
	}

	/**
	 * Validate column ID.
	 *
	 * @since 1.0.0
	 *
	 * @param string $column_id Column ID.
	 *
	 * @return bool
	 */
	public function validate_column_id( $column_id ) {
		return ! empty( $column_id ) && preg_match( '/^[a-zA-Z0-9-]+$/', $column_id );
	}

	/**
	 * Find column by ID.
	 *
	 * @since 1.0.0
	 *
	 * @param string $column_id      Column ID.
	 * @param array  $dashboard_data Dashboard data.
	 *
	 * @return array|null
	 */
	private function find_column_by_id( $column_id, $dashboard_data ) {
		if ( ! isset( $dashboard_data['columns'] ) ) {
			return null;
		}

		foreach ( $dashboard_data['columns'] as $column ) {
			if ( isset( $column['id'] ) && $column['id'] === $column_id ) {
				return $column;
			}
		}

		return null;
	}

	/**
	 * Update column data.
	 *
	 * @since 1.0.0
	 *
	 * @param string $column_id      Column ID.
	 * @param string $title          Column title.
	 * @param array  $dashboard_data Dashboard data.
	 *
	 * @return bool
	 */
	private function update_column_data( $column_id, $title, &$dashboard_data ) {
		if ( ! isset( $dashboard_data['columns'] ) ) {
			return false;
		}

		foreach ( $dashboard_data['columns'] as &$column ) {
			if ( isset( $column['id'] ) && $column['id'] === $column_id ) {
				if ( null !== $title ) {
					$column['title'] = $title;
				}
				return true;
			}
		}

		return false;
	}

	/**
	 * Remove column.
	 *
	 * @since 1.0.0
	 *
	 * @param string $column_id      Column ID.
	 * @param array  $dashboard_data Dashboard data.
	 *
	 * @return bool
	 */
	private function remove_column( $column_id, &$dashboard_data ) {
		if ( ! isset( $dashboard_data['columns'] ) ) {
			return false;
		}

		foreach ( $dashboard_data['columns'] as $key => $column ) {
			if ( isset( $column['id'] ) && $column['id'] === $column_id ) {
				unset( $dashboard_data['columns'][ $key ] );
				$dashboard_data['columns'] = array_values( $dashboard_data['columns'] );
				return true;
			}
		}

		return false;
	}
}
