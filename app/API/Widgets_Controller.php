<?php
/**
 * Widgets_Controller
 *
 * @package Dashmate
 */

declare(strict_types=1);

namespace Nilambar\Dashmate\API;

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
			'/' . $this->get_base_route() . '/(?P<id>[a-zA-Z0-9-]+)/data',
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
			'/' . $this->get_base_route() . '/(?P<id>[a-zA-Z0-9-]+)/settings',
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
		$data = $this->read_json_file( 'widgets.json' );

		if ( is_wp_error( $data ) ) {
			return $data;
		}

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

		// Get widget info from dashboard to determine type.
		$dashboard_data = $this->read_json_file( 'dashboard.json' );

		if ( is_wp_error( $dashboard_data ) ) {
			return $dashboard_data;
		}

		$widget = $this->find_widget_by_id( $widget_id, $dashboard_data );

		if ( ! $widget ) {
			return $this->error_response( 'Widget not found: ' . $widget_id, 404, 'widget_not_found' );
		}

		$widget_type = $widget['type'];
		$settings    = $widget['settings'] ?? [];

		// Get data based on widget type and settings.
		$data = $this->get_widget_data_by_type( $widget_type, $settings );

		if ( is_wp_error( $data ) ) {
			return $data;
		}

		return $this->success_response( $data );
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

		// Get current dashboard data.
		$dashboard_data = $this->read_json_file( 'dashboard.json' );

		if ( is_wp_error( $dashboard_data ) ) {
			return $dashboard_data;
		}

		// Find and update widget settings.
		$updated = $this->update_widget_settings( $widget_id, $settings, $dashboard_data );

		if ( ! $updated ) {
			return $this->error_response( 'Widget not found: ' . $widget_id, 404, 'widget_not_found' );
		}

		// Save updated dashboard data.
		$result = $this->write_json_file( 'dashboard.json', $dashboard_data );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		return $this->success_response( $settings );
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
		return ! empty( $widget_id ) && preg_match( '/^[a-zA-Z0-9-]+$/', $widget_id );
	}

	/**
	 * Find widget by ID.
	 *
	 * @since 1.0.0
	 *
	 * @param string $widget_id      Widget ID.
	 * @param array  $dashboard_data Dashboard data.
	 *
	 * @return array|null
	 */
	private function find_widget_by_id( $widget_id, $dashboard_data ) {
		if ( ! isset( $dashboard_data['columns'] ) ) {
			return null;
		}

		foreach ( $dashboard_data['columns'] as $column ) {
			if ( ! isset( $column['widgets'] ) ) {
				continue;
			}

			foreach ( $column['widgets'] as $widget ) {
				if ( isset( $widget['id'] ) && $widget['id'] === $widget_id ) {
					return $widget;
				}
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
	 * @return array|WP_Error
	 */
	private function get_widget_data_by_type( $widget_type, $settings ) {
		switch ( $widget_type ) {
			case 'chart':
				return $this->get_chart_data( $settings );
			case 'metric':
				return $this->get_metric_data( $settings );
			case 'list':
				return $this->get_list_data( $settings );
			case 'html':
				return $this->get_html_data( $settings );
			case 'iconbox':
				return $this->get_iconbox_data( $settings );
			case 'progress-circle':
				return $this->get_progress_circle_data( $settings );
			case 'quick-links':
				return $this->get_quick_links_data( $settings );
			case 'tabular':
				return $this->get_tabular_data( $settings );
			default:
				return $this->error_response( 'Unknown widget type: ' . $widget_type, 400, 'unknown_widget_type' );
		}
	}

	/**
	 * Get chart data.
	 *
	 * @since 1.0.0
	 *
	 * @param array $settings Widget settings.
	 *
	 * @return array|WP_Error
	 */
	private function get_chart_data( $settings ) {
		$data_source = $settings['data_source'] ?? 'sales';

		switch ( $data_source ) {
			case 'sales':
				return $this->read_json_file( 'sales.json' );
			default:
				return $this->error_response( 'Unknown data source: ' . $data_source, 400, 'unknown_data_source' );
		}
	}

	/**
	 * Get metric data.
	 *
	 * @since 1.0.0
	 *
	 * @param array $settings Widget settings.
	 *
	 * @return array|WP_Error
	 */
	private function get_metric_data( $settings ) {
		$data_source = $settings['data_source'] ?? 'revenue';

		switch ( $data_source ) {
			case 'revenue':
				return $this->read_json_file( 'revenue.json' );
			default:
				return $this->error_response( 'Unknown data source: ' . $data_source, 400, 'unknown_data_source' );
		}
	}

	/**
	 * Get list data.
	 *
	 * @since 1.0.0
	 *
	 * @param array $settings Widget settings.
	 *
	 * @return array|WP_Error
	 */
	private function get_list_data( $settings ) {
		$list_type = $settings['list_type'] ?? 'orders';

		switch ( $list_type ) {
			case 'orders':
				return $this->read_json_file( 'orders.json' );
			default:
				return $this->error_response( 'Unknown list type: ' . $list_type, 400, 'unknown_list_type' );
		}
	}



	/**
	 * Get HTML data.
	 *
	 * @since 1.0.0
	 *
	 * @param array $settings Widget settings.
	 *
	 * @return array
	 */
	private function get_html_data( $settings ) {
		return [
			'html_content'  => $settings['html_content'] ?? '<p>No HTML content provided</p>',
			'allow_scripts' => $settings['allow_scripts'] ?? false,
		];
	}

	/**
	 * Get iconbox data.
	 *
	 * @since 1.0.0
	 *
	 * @param array $settings Widget settings.
	 *
	 * @return array
	 */
	private function get_iconbox_data( $settings ) {
		return [
			'icon'     => $settings['icon'] ?? 'dashicons-admin-users',
			'title'    => $settings['title'] ?? 'Title',
			'subtitle' => $settings['subtitle'] ?? 'Subtitle',
			'color'    => $settings['color'] ?? 'blue',
		];
	}

	/**
	 * Get progress circle data.
	 *
	 * @since 1.0.0
	 *
	 * @param array $settings Widget settings.
	 *
	 * @return array
	 */
	private function get_progress_circle_data( $settings ) {
		return [
			'percentage' => $settings['percentage'] ?? 0,
			'label'      => $settings['label'] ?? '0%',
			'caption'    => $settings['caption'] ?? 'Progress',
			'color'      => $settings['color'] ?? 'blue',
		];
	}

	/**
	 * Get quick links data.
	 *
	 * @since 1.0.0
	 *
	 * @param array $settings Widget settings.
	 *
	 * @return array
	 */
	private function get_quick_links_data( $settings ) {
		return [
			'title' => $settings['title'] ?? 'Quick Links',
			'links' => $settings['links'] ?? [],
		];
	}

	/**
	 * Get tabular data.
	 *
	 * @since 1.0.0
	 *
	 * @param array $settings Widget settings.
	 *
	 * @return array
	 */
	private function get_tabular_data( $settings ) {
		return [
			'tables' => $settings['tables'] ?? [],
		];
	}
}
