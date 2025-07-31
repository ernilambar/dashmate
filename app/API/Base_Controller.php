<?php
/**
 * Base_Controller
 *
 * @package Dashmate
 */

declare(strict_types=1);

namespace Nilambar\Dashmate\API;

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
		// Check if user is logged in and has manage_options capability.
		if ( ! is_user_logged_in() ) {
			return false;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return false;
		}

		// Verify nonce for POST/PUT/DELETE requests.
		$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
		if ( in_array( $method, [ 'POST', 'PUT', 'DELETE' ], true ) ) {
			$nonce = $_SERVER['HTTP_X_WP_NONCE'] ?? '';
			if ( ! wp_verify_nonce( $nonce, 'wp_rest' ) ) {
				return false;
			}
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
	 * Read JSON file.
	 *
	 * @since 1.0.0
	 *
	 * @param string $file_path File path.
	 *
	 * @return array|WP_Error
	 */
	protected function read_json_file( $file_path ) {
		$full_path = DASHMATE_DIR . '/data/' . $file_path;

		if ( ! file_exists( $full_path ) ) {
			return $this->error_response( 'File not found: ' . $file_path, 404, 'file_not_found' );
		}

		$content = file_get_contents( $full_path );

		if ( false === $content ) {
			return $this->error_response( 'Unable to read file: ' . $file_path, 500, 'file_read_error' );
		}

		$data = json_decode( $content, true );

		if ( null === $data && JSON_ERROR_NONE !== json_last_error() ) {
			return $this->error_response( 'Invalid JSON in file: ' . $file_path, 500, 'json_decode_error' );
		}

		return $data;
	}

	/**
	 * Write JSON file.
	 *
	 * @since 1.0.0
	 *
	 * @param string $file_path File path.
	 * @param array  $data      Data to write.
	 *
	 * @return bool|WP_Error
	 */
	protected function write_json_file( $file_path, $data ) {
		$full_path = DASHMATE_DIR . '/data/' . $file_path;
		$dir       = dirname( $full_path );

		if ( ! is_dir( $dir ) ) {
			if ( ! mkdir( $dir, 0755, true ) ) {
				return $this->error_response( 'Unable to create directory: ' . $dir, 500, 'directory_creation_error' );
			}
		}

		$json_content = wp_json_encode( $data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );

		if ( false === $json_content ) {
			return $this->error_response( 'Unable to encode data to JSON', 500, 'json_encode_error' );
		}

		$result = file_put_contents( $full_path, $json_content );

		if ( false === $result ) {
			return $this->error_response( 'Unable to write file: ' . $file_path, 500, 'file_write_error' );
		}

		return true;
	}
}
