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
		$debug_info = [
			'user_logged_in'    => is_user_logged_in(),
			'current_user_id'   => get_current_user_id(),
			'user_can_manage'   => current_user_can( 'manage_options' ),
			'nonce_verified'    => wp_verify_nonce( $_SERVER['HTTP_X_WP_NONCE'] ?? '', 'wp_rest' ),
			'request_method'    => $_SERVER['REQUEST_METHOD'] ?? 'GET',
			'headers'           => getallheaders(),
			'data_files_exist'  => [
				'dashboard.json' => file_exists( DASHMATE_DIR . '/data/dashboard.json' ),
				'widgets.json'   => file_exists( DASHMATE_DIR . '/data/widgets.json' ),
				'sales.json'     => file_exists( DASHMATE_DIR . '/data/sales.json' ),
				'revenue.json'   => file_exists( DASHMATE_DIR . '/data/revenue.json' ),
				'orders.json'    => file_exists( DASHMATE_DIR . '/data/orders.json' ),
			],
			'plugin_version'    => DASHMATE_VERSION,
			'wordpress_version' => get_bloginfo( 'version' ),
		];

		return $this->success_response( $debug_info );
	}
}
