<?php
/**
 * Base_Controller
 *
 * @package Dashmate
 */

declare(strict_types=1);

namespace Nilambar\Dashmate\API;

use Nilambar\Dashmate\Services\Dashboard_Manager;
use WP_Error;
use WP_REST_Response;

/**
 * Base_Controller class.
 *
 * @since 1.0.0
 */
abstract class Base_Controller {

	/**
	 * Namespace.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $namespace = 'dashmate/v1';

	/**
	 * Base route.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $base_route = '';

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'rest_api_init', [ $this, 'register_routes' ] );
	}

	/**
	 * Register routes.
	 *
	 * @since 1.0.0
	 */
	abstract public function register_routes();

	/**
	 * Get namespace.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	protected function get_namespace() {
		return $this->namespace;
	}

	/**
	 * Get base route.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	protected function get_base_route() {
		return $this->base_route;
	}

	/**
	 * Get full route.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	protected function get_route() {
		return '/' . $this->get_namespace() . '/' . $this->get_base_route();
	}

	/**
	 * Check permissions.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function check_permissions() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Success response.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $data    Response data.
	 * @param int   $status  HTTP status code.
	 * @param array $headers Response headers.
	 *
	 * @return WP_REST_Response
	 */
	protected function success_response( $data, $status = 200, $headers = [] ) {
		return new WP_REST_Response(
			[
				'success' => true,
				'data'    => $data,
			],
			$status,
			$headers
		);
	}

	/**
	 * Error response.
	 *
	 * @since 1.0.0
	 *
	 * @param string $message Error message.
	 * @param int    $status  HTTP status code.
	 * @param string $code    Error code.
	 *
	 * @return WP_Error
	 */
	protected function error_response( $message, $status = 400, $code = 'error' ) {
		return new WP_Error(
			$code,
			$message,
			[ 'status' => $status ]
		);
	}

	/**
	 * Get dashboard data from WordPress options.
	 *
	 * @since 1.0.0
	 *
	 * @param string $dashboard_id Dashboard ID. Defaults to 'main'.
	 * @return array|WP_Error
	 */
	protected function get_dashboard_data( string $dashboard_id = 'main' ) {
		$data = Dashboard_Manager::get_dashboard_data( $dashboard_id );

		return $data;
	}
}
