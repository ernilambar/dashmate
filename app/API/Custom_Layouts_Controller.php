<?php
/**
 * Custom_Layouts_Controller
 *
 * @package Dashmate
 */

declare(strict_types=1);

namespace Nilambar\Dashmate\API;

use Nilambar\Dashmate\Models\Custom_Layout_Model;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;

/**
 * Custom_Layouts_Controller class.
 *
 * @since 1.0.0
 */
class Custom_Layouts_Controller extends Base_Controller {

	/**
	 * Base route.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $base_route = 'custom-layouts';

	/**
	 * Register routes.
	 *
	 * @since 1.0.0
	 */
	public function register_routes() {
		// Get all custom layouts.
		register_rest_route(
			$this->get_namespace(),
			'/' . $this->get_base_route(),
			[
				[
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => [ $this, 'get_custom_layouts' ],
					'permission_callback' => [ $this, 'check_permissions' ],
				],
				[
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => [ $this, 'create_custom_layout' ],
					'permission_callback' => [ $this, 'check_permissions' ],
					'args'                => [
						'key'  => [
							'required'          => true,
							'type'              => 'string',
							'sanitize_callback' => 'sanitize_key',
							'validate_callback' => [ $this, 'validate_layout_key' ],
						],
						'data' => [
							'required' => true,
							'type'     => 'object',
						],
					],
				],
			]
		);

		// Get, update, delete specific custom layout.
		register_rest_route(
			$this->get_namespace(),
			'/' . $this->get_base_route() . '/(?P<layout_key>[a-zA-Z0-9_-]+)',
			[
				[
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => [ $this, 'get_custom_layout' ],
					'permission_callback' => [ $this, 'check_permissions' ],
					'args'                => [
						'layout_key' => [
							'required'          => true,
							'type'              => 'string',
							'sanitize_callback' => 'sanitize_key',
							'validate_callback' => [ $this, 'validate_layout_key' ],
						],
					],
				],
				[
					'methods'             => \WP_REST_Server::EDITABLE,
					'callback'            => [ $this, 'update_custom_layout' ],
					'permission_callback' => [ $this, 'check_permissions' ],
					'args'                => [
						'layout_key' => [
							'required'          => true,
							'type'              => 'string',
							'sanitize_callback' => 'sanitize_key',
							'validate_callback' => [ $this, 'validate_layout_key' ],
						],
						'data'       => [
							'required' => true,
							'type'     => 'object',
						],
					],
				],
				[
					'methods'             => \WP_REST_Server::DELETABLE,
					'callback'            => [ $this, 'delete_custom_layout' ],
					'permission_callback' => [ $this, 'check_permissions' ],
					'args'                => [
						'layout_key' => [
							'required'          => true,
							'type'              => 'string',
							'sanitize_callback' => 'sanitize_key',
							'validate_callback' => [ $this, 'validate_layout_key' ],
						],
					],
				],
			]
		);
	}

	/**
	 * Validate layout key.
	 *
	 * @since 1.0.0
	 *
	 * @param string $param Layout key parameter.
	 *
	 * @return bool
	 */
	public function validate_layout_key( $param ) {
		return ! empty( $param ) && is_string( $param ) && preg_match( '/^[a-zA-Z0-9_-]+$/', $param );
	}

	/**
	 * Get all custom layouts.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function get_custom_layouts( WP_REST_Request $request ) {
		$layouts = Custom_Layout_Model::get_all_layouts();

		return $this->success_response( $layouts );
	}

	/**
	 * Create custom layout.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function create_custom_layout( WP_REST_Request $request ) {
		$key  = $request->get_param( 'key' );
		$data = $request->get_param( 'data' );

		// Check if layout already exists.
		if ( Custom_Layout_Model::data_exists( $key ) ) {
			return $this->error_response(
				esc_html__( 'Custom layout already exists: ', 'dashmate' ) . $key,
				409,
				'layout_already_exists'
			);
		}

		// Validate data structure.
		if ( ! is_array( $data ) || ! isset( $data['columns'] ) ) {
			return $this->error_response(
				esc_html__( 'Invalid layout data structure. Must include columns array.', 'dashmate' ),
				400,
				'invalid_data_structure'
			);
		}

		$result = Custom_Layout_Model::set_data( $key, $data );

		if ( is_wp_error( $result ) ) {
			return $this->error_response(
				$result->get_error_message(),
				500,
				'create_failed'
			);
		}

		return $this->success_response(
			[
				'message' => sprintf(
					/* translators: %s: Layout key */
					esc_html__( 'Custom layout "%s" created successfully!', 'dashmate' ),
					$key
				),
				'key'     => $key,
				'data'    => $data,
			],
			201
		);
	}

	/**
	 * Get specific custom layout.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function get_custom_layout( WP_REST_Request $request ) {
		$layout_key = $request->get_param( 'layout_key' );

		$layout_data = Custom_Layout_Model::get_data( $layout_key );

		if ( is_wp_error( $layout_data ) ) {
			return $this->error_response(
				$layout_data->get_error_message(),
				404,
				'layout_not_found'
			);
		}

		return $this->success_response( $layout_data );
	}

	/**
	 * Update custom layout.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function update_custom_layout( WP_REST_Request $request ) {
		$layout_key = $request->get_param( 'layout_key' );
		$data       = $request->get_param( 'data' );

		// Check if layout exists.
		if ( ! Custom_Layout_Model::data_exists( $layout_key ) ) {
			return $this->error_response(
				esc_html__( 'Custom layout not found: ', 'dashmate' ) . $layout_key,
				404,
				'layout_not_found'
			);
		}

		// Validate data structure.
		if ( ! is_array( $data ) || ! isset( $data['columns'] ) ) {
			return $this->error_response(
				esc_html__( 'Invalid layout data structure. Must include columns array.', 'dashmate' ),
				400,
				'invalid_data_structure'
			);
		}

		$result = Custom_Layout_Model::set_data( $layout_key, $data );

		if ( is_wp_error( $result ) ) {
			return $this->error_response(
				$result->get_error_message(),
				500,
				'update_failed'
			);
		}

		return $this->success_response(
			[
				'message' => sprintf(
					/* translators: %s: Layout key */
					esc_html__( 'Custom layout "%s" updated successfully!', 'dashmate' ),
					$layout_key
				),
				'key'     => $layout_key,
				'data'    => $data,
			]
		);
	}

	/**
	 * Delete custom layout.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function delete_custom_layout( WP_REST_Request $request ) {
		$layout_key = $request->get_param( 'layout_key' );

		// Check if layout exists.
		if ( ! Custom_Layout_Model::data_exists( $layout_key ) ) {
			return $this->error_response(
				esc_html__( 'Custom layout not found: ', 'dashmate' ) . $layout_key,
				404,
				'layout_not_found'
			);
		}

		$result = Custom_Layout_Model::delete_data( $layout_key );

		if ( is_wp_error( $result ) ) {
			return $this->error_response(
				$result->get_error_message(),
				500,
				'delete_failed'
			);
		}

		return $this->success_response(
			[
				'message' => sprintf(
					/* translators: %s: Layout key */
					esc_html__( 'Custom layout "%s" deleted successfully!', 'dashmate' ),
					$layout_key
				),
				'key'     => $layout_key,
			]
		);
	}
}
