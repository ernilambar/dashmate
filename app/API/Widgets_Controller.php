<?php
/**
 * Widgets_Controller
 *
 * @package Dashmate
 */

declare(strict_types=1);

namespace Nilambar\Dashmate\API;

use Nilambar\Dashmate\Widget_Blueprint_Manager;
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

		// Move widget to new position.
		register_rest_route(
			$this->get_namespace(),
			'/' . $this->get_base_route() . '/(?P<id>[a-zA-Z0-9_-]+)/move',
			[
				[
					'methods'             => \WP_REST_Server::EDITABLE,
					'callback'            => [ $this, 'move_widget' ],
					'permission_callback' => [ $this, 'check_permissions' ],
					'args'                => [
						'id'        => [
							'required'          => true,
							'validate_callback' => [ $this, 'validate_widget_id' ],
						],
						'column_id' => [
							'required' => true,
							'type'     => 'string',
						],
						'position'  => [
							'required' => true,
							'type'     => 'integer',
							'minimum'  => 1,
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
		$data = Widget_Blueprint_Manager::get_widget_blueprints_for_frontend();

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

		// Get content using the new system.
		$content = Widget_Dispatcher::get_widget_content( $widget->get_blueprint_type(), $widget_id, $settings );

		if ( is_wp_error( $content ) ) {
			return $content;
		}

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

		$content = Widget_Dispatcher::get_widget_content( $widget->get_blueprint_type(), $widget_id );

		if ( is_wp_error( $content ) ) {
			return $content;
		}

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

		$content = Widget_Dispatcher::get_widget_content( $widget->get_blueprint_type(), $widget_id, $settings );

		if ( is_wp_error( $content ) ) {
			return $content;
		}

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
	 * Move widget to new position.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function move_widget( $request ) {
		$widget_id = $request->get_param( 'id' );
		$column_id = $request->get_param( 'column_id' );
		$position  = $request->get_param( 'position' );

		$result = Widget_Dispatcher::move_widget( $widget_id, $column_id, $position );

		if ( is_wp_error( $result ) ) {
			return $this->error_response( $result->get_error_message(), 400, $result->get_error_code() );
		}

		return $this->success_response( [ 'message' => 'Widget moved successfully' ] );
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

	/**
	 * Update widget settings.
	 *
	 * @since 1.0.0
	 *
	 * @param string $widget_id      Widget ID.
	 * @param array  $settings      New settings.
	 * @param array  $dashboard_data Dashboard data.
	 *
	 * @return bool
	 */
	private function update_widget_settings( $widget_id, $settings, &$dashboard_data ) {
		if ( ! isset( $dashboard_data['columns'] ) ) {
			return false;
		}

		foreach ( $dashboard_data['columns'] as &$column ) {
			if ( ! isset( $column['widgets'] ) ) {
				continue;
			}

			foreach ( $column['widgets'] as &$widget ) {
				if ( isset( $widget['id'] ) && $widget['id'] === $widget_id ) {
					$widget['settings'] = $settings;
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Get widget data by type.
	 *
	 * @since 1.0.0
	 *
	 * @param string $widget_type Widget type.
	 * @param array  $settings   Widget settings.
	 *
	 * @return array
	 */
	private function get_widget_data_by_type( $widget_type, $settings ) {
		// This method should not exist in controller.
		// Data should come from the widget itself via Widget_Dispatcher.
		return [];
	}

	/**
	 * Get widget content by type.
	 *
	 * @since 1.0.0
	 *
	 * @param string $widget_type Widget type.
	 * @param string $widget_id   Widget ID.
	 *
	 * @return array|WP_Error
	 */
	private function get_widget_content_by_type( $widget_type, $widget_id = null ) {
		// Get widget by ID and get its blueprint type.
		$widget = Widget_Dispatcher::get_widget( $widget_id );

		if ( null === $widget ) {
			return new \WP_Error( 'unknown_widget', 'Unknown widget: ' . $widget_id );
		}

		// Get the blueprint type from the widget.
		$blueprint_type = $widget->get_blueprint_type();

		// Use the new widget system.
		$content = Widget_Dispatcher::get_widget_content( $blueprint_type, $widget_id );

		if ( is_wp_error( $content ) ) {
			return $content;
		}

		// Add widget ID to content.
		$content['id']   = $widget_id;
		$content['type'] = $blueprint_type;

		return $content;
	}

	/**
	 * Get widget content by type with settings.
	 *
	 * @since 1.0.0
	 *
	 * @param string $widget_type Widget type.
	 * @param string $widget_id   Widget ID.
	 * @param array  $settings    Widget settings.
	 *
	 * @return array|WP_Error
	 */
	private function get_widget_content_by_type_with_settings( $widget_type, $widget_id, $settings ) {
		// Get widget by ID and get its blueprint type.
		$widget = Widget_Dispatcher::get_widget( $widget_id );

		if ( null === $widget ) {
			return new \WP_Error( 'unknown_widget', 'Unknown widget: ' . $widget_id );
		}

		// Get the blueprint type from the widget.
		$blueprint_type = $widget->get_blueprint_type();

		// Use the new widget system with settings.
		$content = Widget_Dispatcher::get_widget_content( $blueprint_type, $widget_id, $settings );

		if ( is_wp_error( $content ) ) {
			return $content;
		}

		// Add widget ID to content.
		$content['id']   = $widget_id;
		$content['type'] = $blueprint_type;

		return $content;
	}
}
