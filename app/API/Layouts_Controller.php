<?php
/**
 * Layouts_Controller
 *
 * @package Dashmate
 */

declare(strict_types=1);

namespace Nilambar\Dashmate\API;

use Nilambar\Dashmate\Layout_Manager;
use Nilambar\Dashmate\Utils\JSON_Utils;
use Nilambar\Dashmate\Utils\Layout_Utils;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;

/**
 * Layouts_Controller class.
 *
 * @since 1.0.0
 */
class Layouts_Controller extends Base_Controller {

	/**
	 * Base route.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $base_route = 'layouts';

	/**
	 * Register routes.
	 *
	 * @since 1.0.0
	 */
	public function register_routes() {
		// Get all layouts.
		register_rest_route(
			$this->get_namespace(),
			'/' . $this->get_base_route(),
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'get_layouts' ],
				'permission_callback' => [ $this, 'check_permissions' ],
			]
		);

		// Get specific layout by key.
		register_rest_route(
			$this->get_namespace(),
			'/' . $this->get_base_route() . '/(?P<layout_key>[a-zA-Z0-9_-]+)',
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'get_layout' ],
				'permission_callback' => [ $this, 'check_permissions' ],
				'args'                => [
					'layout_key' => [
						'required'          => true,
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
						'validate_callback' => [ $this, 'validate_layout_key' ],
					],
				],
			]
		);

		// Apply layout.
		register_rest_route(
			$this->get_namespace(),
			'/' . $this->get_base_route() . '/(?P<layout_key>[a-zA-Z0-9_-]+)/apply',
			[
				'methods'             => 'POST',
				'callback'            => [ $this, 'apply_layout' ],
				'permission_callback' => [ $this, 'check_permissions' ],
				'args'                => [
					'layout_key' => [
						'required'          => true,
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
						'validate_callback' => [ $this, 'validate_layout_key' ],
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
		return ! empty( $param ) && is_string( $param );
	}

	/**
	 * Get all layouts.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function get_layouts( WP_REST_Request $request ) {
		// Get all registered layouts using Layout_Manager.
		$layouts = Layout_Manager::get_layouts();

		if ( is_wp_error( $layouts ) ) {
			return $this->error_response(
				$layouts->get_error_message(),
				500,
				'layouts_retrieval_error'
			);
		}

		$layouts_data = [];
		foreach ( $layouts as $key => $layout ) {
			// Get layout data for each layout.
			$layout_data = Layout_Manager::get_layout_data( $key );

			$layout_response = [
				'id'    => $layout['id'],
				'title' => $layout['title'],
				'type'  => $layout['type'] ?? 'file',
			];

			// Add path for file-based layouts.
			if ( 'file' === ( $layout['type'] ?? 'file' ) && isset( $layout['path'] ) ) {
				$layout_response['path'] = $layout['path'];
			}

			// Add layout data if available.
			if ( ! is_wp_error( $layout_data ) ) {
				$layout_response['layoutData'] = $layout_data;
			}

			$layouts_data[ $key ] = $layout_response;
		}

		return $this->success_response( $layouts_data );
	}

	/**
	 * Get layout by key.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function get_layout( WP_REST_Request $request ) {
		$layout_key = $request->get_param( 'layout_key' );

		// Get layout data using Layout_Manager.
		$layout_data = Layout_Manager::get_layout_data( $layout_key );

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
	 * Apply layout.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function apply_layout( WP_REST_Request $request ) {
		$layout_key = $request->get_param( 'layout_key' );

		// Prevent applying to current layout as it's read-only.
		if ( 'current' === $layout_key ) {
			return $this->error_response(
				esc_html__( 'Cannot apply current layout as it is read-only.', 'dashmate' ),
				400,
				'current_layout_readonly'
			);
		}

		// Get layout data using Layout_Manager.
		$layout_data = Layout_Manager::get_layout_data( $layout_key );

		if ( is_wp_error( $layout_data ) ) {
			return $this->error_response(
				$layout_data->get_error_message(),
				404,
				'layout_not_found'
			);
		}

		// Convert layout data to JSON and apply it.
		$json_data = JSON_Utils::encode_to_json( $layout_data );

		if ( is_wp_error( $json_data ) ) {
			return $this->error_response(
				$json_data->get_error_message(),
				500,
				'layout_json_conversion_failed'
			);
		}

		// Apply the layout data to the options table.
		$result = Layout_Utils::set_layout_from_json( $json_data );

		if ( is_wp_error( $result ) ) {
			return $this->error_response(
				$result->get_error_message(),
				500,
				'layout_apply_failed'
			);
		}

		return $this->success_response(
			[
				'message'    => sprintf(
					/* translators: %s: Layout title */
					esc_html__( 'Layout "%s" applied successfully!', 'dashmate' ),
					Layout_Manager::get_layout( $layout_key )['title'] ?? $layout_key
				),
				'layout_key' => $layout_key,
			]
		);
	}
}
