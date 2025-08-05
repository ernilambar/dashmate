<?php
/**
 * Layouts_Controller
 *
 * @package Dashmate
 */

declare(strict_types=1);

namespace Nilambar\Dashmate\API;

use Nilambar\Dashmate\Layout_Manager;
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
			$layouts_data[ $key ] = [
				'id'    => $layout['id'],
				'title' => $layout['title'],
				'path'  => $layout['path'],
				'url'   => rest_url( $this->get_namespace() . '/layouts/' . $key ),
			];
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
}
