<?php
/**
 * Debug_Controller
 *
 * @package Dashmate
 */

declare(strict_types=1);

namespace Nilambar\Dashmate\API;

use WP_REST_Request;
use WP_REST_Response;

/**
 * Debug_Controller class.
 *
 * @since 1.0.0
 */
class Debug_Controller extends Base_Controller {

	/**
	 * Base route.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $base_route = 'debug';

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
					'callback'            => [ $this, 'get_debug_info' ],
					'permission_callback' => [ $this, 'check_permissions' ],
					'args'                => [],
				],
			]
		);
	}

	/**
	 * Get debug information.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_REST_Response
	 */
	public function get_debug_info( $request ) {
		$method      = $_SERVER['REQUEST_METHOD'] ?? 'GET';
		$nonce       = $_SERVER['HTTP_X_WP_NONCE'] ?? '';
		$all_headers = getallheaders();

		$debug_info = [
			'user_logged_in'    => is_user_logged_in(),
			'current_user_id'   => get_current_user_id(),
			'user_can_manage'   => current_user_can( 'manage_options' ),
			'request_method'    => $method,
			'nonce_received'    => $nonce,
			'nonce_verified'    => wp_verify_nonce( $nonce, 'wp_rest' ),
			'nonce_length'      => strlen( $nonce ),
			'headers'           => $all_headers,
			'permission_check'  => $this->check_permissions(),
			'data_files_exist'  => [
				'dashboard.json' => file_exists( DASHMATE_DIR . '/data/dashboard.json' ),
				'widgets.json'   => file_exists( DASHMATE_DIR . '/data/widgets.json' ),
			],
			'plugin_version'    => DASHMATE_VERSION,
			'wordpress_version' => get_bloginfo( 'version' ),
			'rest_url'          => rest_url(),
			'namespace'         => $this->get_namespace(),
			'base_route'        => $this->get_base_route(),
		];

		return $this->success_response( $debug_info );
	}
}
