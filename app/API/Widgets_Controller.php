<?php
/**
 * Widgets_Controller
 *
 * @package Dashmate
 */

declare(strict_types=1);

namespace Nilambar\Dashmate\API;

use Nilambar\Dashmate\Widget_Dispatcher;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;

/**
 * Widgets_Controller class.
 *
 * @since 1.0.0
 */
class Widgets_Controller extends Base_Controller {

	/**
	 * Base route.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $base_route = 'widgets';

	/**
	 * Register routes.
	 *
	 * @since 1.0.0
	 */
	public function register_routes() {
		// Get available widgets.
		register_rest_route(
			$this->get_namespace(),
			'/' . $this->get_base_route(),
			[
				[
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => [ $this, 'get_widgets' ],
					'permission_callback' => [ $this, 'check_permissions' ],
					'args'                => [],
				],
			]
		);

		// Get widget data.
		register_rest_route(
			$this->get_namespace(),
			'/' . $this->get_base_route() . '/(?P<id>[a-zA-Z0-9_-]+)/data',
			[
				[
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => [ $this, 'get_widget_data' ],
					'permission_callback' => [ $this, 'check_permissions' ],
					'args'                => [
						'id' => [
							'required'          => true,
							'validate_callback' => [ $this, 'validate_widget_id' ],
						],
					],
				],
			]
		);

		// Save widget settings.
		register_rest_route(
			$this->get_namespace(),
			'/' . $this->get_base_route() . '/(?P<id>[a-zA-Z0-9_-]+)/settings',
			[
				[
					'methods'             => \WP_REST_Server::EDITABLE,
					'callback'            => [ $this, 'save_widget_settings' ],
					'permission_callback' => [ $this, 'check_permissions' ],
					'args'                => [
						'id'       => [
							'required'          => true,
							'validate_callback' => [ $this, 'validate_widget_id' ],
						],
						'settings' => [
							'required' => true,
							'type'     => 'object',
						],
					],
				],
			]
		);



		// Get widget content by widget ID.
		register_rest_route(
			$this->get_namespace(),
			'/' . $this->get_base_route() . '/content/(?P<widget_id>[a-zA-Z0-9_-]+)',
			[
				[
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => [ $this, 'get_widget_content' ],
					'permission_callback' => [ $this, 'check_permissions' ],
					'args'                => [
						'widget_id' => [
							'required'          => true,
							'validate_callback' => [ $this, 'validate_widget_id' ],
						],
					],
				],
				[
					'methods'             => \WP_REST_Server::EDITABLE,
					'callback'            => [ $this, 'get_widget_content_with_settings' ],
					'permission_callback' => [ $this, 'check_permissions' ],
					'args'                => [
						'widget_id' => [
							'required'          => true,
							'validate_callback' => [ $this, 'validate_widget_id' ],
						],
						'settings'  => [
							'required' => false,
							'type'     => 'object',
						],
					],
				],
			]
		);
	}

	/**
	 * Get widgets.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function get_widgets( $request ) {
		$data = Widget_Dispatcher::get_widget_types_for_frontend();

		return $this->success_response( $data );
	}

	/**
	 * Get widget data.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function get_widget_data( $request ) {
		$widget_id = $request->get_param( 'id' );

		// Get widget from the new system.
		$widget = Widget_Dispatcher::get_widget( $widget_id );

		if ( null === $widget ) {
			return $this->error_response( 'Widget not found: ' . $widget_id, 404, 'widget_not_found' );
		}

		// Get widget settings from WordPress options.
		$dashboard_data = $this->get_dashboard_data();

		if ( is_wp_error( $dashboard_data ) ) {
			return $dashboard_data;
		}

		$widget_data = $this->find_widget_by_id( $widget_id, $dashboard_data );
		$settings    = $widget_data['settings'] ?? [];

		// Merge settings with defaults to ensure frontend gets complete settings
		$merged_settings = $widget->merge_settings_with_defaults( $settings );

		// Get content using the new system.
		$content = Widget_Dispatcher::get_widget_content( $widget->get_template_type(), $widget_id, $settings );

		if ( is_wp_error( $content ) ) {
			return $content;
		}

		// Add widget type and merged settings to the response
		$content['type']     = $widget->get_template_type();
		$content['settings'] = $merged_settings;

		return $this->success_response( $content );
	}

	/**
	 * Get widget content by widget ID.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function get_widget_content( $request ) {
		$widget_id = $request->get_param( 'widget_id' );

		// Get widget from the new system.
		$widget = Widget_Dispatcher::get_widget( $widget_id );

		if ( null === $widget ) {
			return $this->error_response( 'Widget not found: ' . $widget_id, 404, 'widget_not_found' );
		}

		$content = Widget_Dispatcher::get_widget_content( $widget->get_template_type(), $widget_id );

		if ( is_wp_error( $content ) ) {
			return $content;
		}

		// Add widget type to the response
		$content['type'] = $widget->get_template_type();

		return $this->success_response( $content );
	}

	/**
	 * Get widget content with settings (POST method).
	 *
	 * @since 1.0.0
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function get_widget_content_with_settings( $request ) {
		$widget_id = $request->get_param( 'widget_id' );
		$settings  = $request->get_param( 'settings' ) ?? [];

		// Get widget from the new system.
		$widget = Widget_Dispatcher::get_widget( $widget_id );

		if ( null === $widget ) {
			return $this->error_response( 'Widget not found: ' . $widget_id, 404, 'widget_not_found' );
		}

		$content = Widget_Dispatcher::get_widget_content( $widget->get_template_type(), $widget_id, $settings );

		if ( is_wp_error( $content ) ) {
			return $content;
		}

		// Add widget type to the response
		$content['type'] = $widget->get_template_type();

		return $this->success_response( $content );
	}

	/**
	 * Save widget settings.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function save_widget_settings( $request ) {
		$widget_id = $request->get_param( 'id' );
		$settings  = $request->get_param( 'settings' );

		$result = Widget_Dispatcher::update_widget_settings( $widget_id, $settings );

		if ( is_wp_error( $result ) ) {
			return $this->error_response( $result->get_error_message(), 400, $result->get_error_code() );
		}

		return $this->success_response( [ 'message' => 'Settings saved successfully' ] );
	}



	/**
	 * Validate widget ID.
	 *
	 * @since 1.0.0
	 *
	 * @param string $widget_id Widget ID.
	 *
	 * @return bool
	 */
	public function validate_widget_id( $widget_id ) {
		return ! empty( $widget_id ) && preg_match( '/^[a-zA-Z0-9_-]+$/', $widget_id );
	}

	/**
	 * Find widget by ID in dashboard data.
	 *
	 * @since 1.0.0
	 *
	 * @param string $widget_id      Widget ID.
	 * @param array  $dashboard_data Dashboard data.
	 *
	 * @return array|null
	 */
	private function find_widget_by_id( $widget_id, $dashboard_data ) {
		$widgets = $dashboard_data['widgets'] ?? [];

		foreach ( $widgets as $widget ) {
			if ( isset( $widget['id'] ) && $widget['id'] === $widget_id ) {
				return $widget;
			}
		}

		return null;
	}
}
