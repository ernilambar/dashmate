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
		return ! empty( $widget_id ) && preg_match( '/^[a-zA-Z0-9_-]+$/', $widget_id );
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
		// Fetch data from external API or WordPress functions.
		$links = $this->get_quick_links_from_source();

		return [
			'title' => 'Quick Links',
			'links' => $links,
		];
	}

	/**
	 * Get quick links from source (WordPress admin links).
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	private function get_quick_links_from_source() {
		$links = [
			[
				'title' => 'Dashboard',
				'url'   => admin_url(),
				'icon'  => 'dashicons-admin-home',
			],
			[
				'title' => 'Posts',
				'url'   => admin_url( 'edit.php' ),
				'icon'  => 'dashicons-admin-post',
			],
			[
				'title' => 'Pages',
				'url'   => admin_url( 'edit.php?post_type=page' ),
				'icon'  => 'dashicons-admin-page',
			],
			[
				'title' => 'Media',
				'url'   => admin_url( 'upload.php' ),
				'icon'  => 'dashicons-admin-media',
			],
			[
				'title' => 'Comments',
				'url'   => admin_url( 'edit-comments.php' ),
				'icon'  => 'dashicons-admin-comments',
			],
			[
				'title' => 'Appearance',
				'url'   => admin_url( 'themes.php' ),
				'icon'  => 'dashicons-admin-appearance',
			],
			[
				'title' => 'Plugins',
				'url'   => admin_url( 'plugins.php' ),
				'icon'  => 'dashicons-admin-plugins',
			],
			[
				'title' => 'Users',
				'url'   => admin_url( 'users.php' ),
				'icon'  => 'dashicons-admin-users',
			],
			[
				'title' => 'Tools',
				'url'   => admin_url( 'tools.php' ),
				'icon'  => 'dashicons-admin-tools',
			],
			[
				'title' => 'Settings',
				'url'   => admin_url( 'options-general.php' ),
				'icon'  => 'dashicons-admin-generic',
			],
		];

		return $links;
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

	// Content methods for generic endpoint.

	/**
	 * Get HTML content.
	 *
	 * @since 1.0.0
	 *
	 * @param string $widget_id Widget ID (optional).
	 *
	 * @return array
	 */
	private function get_html_content( $widget_id = null ) {
		return [
			'html_content' => '<p>Sample HTML content from API</p>',
		];
	}

	/**
	 * Get HTML content with settings.
	 *
	 * @since 1.0.0
	 *
	 * @param string $widget_id Widget ID.
	 * @param array  $settings  Widget settings.
	 *
	 * @return array
	 */
	private function get_html_content_with_settings( $widget_id, $settings ) {
		$html_content = $settings['html_content'] ?? '<p>Sample HTML content from API</p>';

		return [
			'html_content' => $html_content,
		];
	}

	/**
	 * Get iconbox content.
	 *
	 * @since 1.0.0
	 *
	 * @param string $widget_id Widget ID (optional).
	 *
	 * @return array
	 */
	private function get_iconbox_content( $widget_id = null ) {
		return [
			'icon'     => 'dashicons-admin-users',
			'title'    => 'Sample Icon Box',
			'subtitle' => 'This content comes from API',
		];
	}

	/**
	 * Get iconbox content with settings.
	 *
	 * @since 1.0.0
	 *
	 * @param string $widget_id Widget ID.
	 * @param array  $settings  Widget settings.
	 *
	 * @return array
	 */
	private function get_iconbox_content_with_settings( $widget_id, $settings ) {
		return [
			'icon'     => $settings['icon'] ?? 'dashicons-admin-users',
			'title'    => $settings['title'] ?? 'Sample Icon Box',
			'subtitle' => $settings['subtitle'] ?? 'This content comes from API',
		];
	}

	/**
	 * Get progress circle content.
	 *
	 * @since 1.0.0
	 *
	 * @param string $widget_id Widget ID (optional).
	 *
	 * @return array
	 */
	private function get_progress_circle_content( $widget_id = null ) {
		return [
			'percentage' => 75,
			'label'      => '75%',
			'caption'    => 'Sample Progress',
		];
	}

	/**
	 * Get progress circle content with settings.
	 *
	 * @since 1.0.0
	 *
	 * @param string $widget_id Widget ID.
	 * @param array  $settings  Widget settings.
	 *
	 * @return array
	 */
	private function get_progress_circle_content_with_settings( $widget_id, $settings ) {
		return [
			'percentage' => $settings['percentage'] ?? 75,
			'label'      => $settings['label'] ?? '75%',
			'caption'    => $settings['caption'] ?? 'Sample Progress',
		];
	}

			/**
			 * Get quick links content.
			 *
			 * @since 1.0.0
			 *
			 * @param string $widget_id Widget ID (optional).
			 *
			 * @return array
			 */
	private function get_quick_links_content( $widget_id = null ) {
		// Get widget settings if widget_id is provided.
		$settings = [];
		if ( $widget_id ) {
			$dashboard_data = $this->get_dashboard_data();
			if ( ! is_wp_error( $dashboard_data ) ) {
				$widget = $this->find_widget_by_id( $widget_id, $dashboard_data );
				if ( $widget ) {
					$settings = $widget['settings'] ?? [];
				}
			}
		}

		// Get base links from source.
		$links = $this->get_quick_links_from_source();

		// Apply widget-specific customizations based on settings.
		$title = 'Quick Links';
		if ( ! empty( $settings['customTitle'] ) ) {
			$title = $settings['customTitle'];
		}

		// Filter links based on settings if needed.
		if ( ! empty( $settings['filterLinks'] ) && $settings['filterLinks'] !== 'all' ) {
			switch ( $settings['filterLinks'] ) {
				case 'content':
					$allowed_titles = [ 'Posts', 'Pages', 'Media' ];
					break;
				case 'admin':
					$allowed_titles = [ 'Users', 'Settings', 'Tools', 'Appearance', 'Plugins' ];
					break;
				default:
					$allowed_titles = [];
			}

			if ( ! empty( $allowed_titles ) ) {
				$links = array_filter(
					$links,
					function ( $link ) use ( $allowed_titles ) {
						return in_array( $link['title'], $allowed_titles, true );
					}
				);
			}
		}

		return [
			'title' => $title,
			'links' => $links,
		];
	}

	/**
	 * Get quick links content with settings.
	 *
	 * @since 1.0.0
	 *
	 * @param string $widget_id Widget ID.
	 * @param array  $settings  Widget settings.
	 *
	 * @return array
	 */
	private function get_quick_links_content_with_settings( $widget_id, $settings ) {
		// Get base links from source.
		$links = $this->get_quick_links_from_source();

		// Apply widget-specific customizations based on settings.
		$title = 'Quick Links';
		if ( ! empty( $settings['customTitle'] ) ) {
			$title = $settings['customTitle'];
		}

		// Filter links based on settings if needed.
		if ( ! empty( $settings['filterLinks'] ) && $settings['filterLinks'] !== 'all' ) {
			switch ( $settings['filterLinks'] ) {
				case 'content':
					$allowed_titles = [ 'Posts', 'Pages', 'Media' ];
					break;
				case 'admin':
					$allowed_titles = [ 'Users', 'Settings', 'Tools', 'Appearance', 'Plugins' ];
					break;
				default:
					$allowed_titles = [];
			}

			if ( ! empty( $allowed_titles ) ) {
				$links = array_filter(
					$links,
					function ( $link ) use ( $allowed_titles ) {
						return in_array( $link['title'], $allowed_titles, true );
					}
				);
			}
		}

		return [
			'title' => $title,
			'links' => $links,
		];
	}

	/**
	 * Get tabular content.
	 *
	 * @since 1.0.0
	 *
	 * @param string $widget_id Widget ID (optional).
	 *
	 * @return array
	 */
	private function get_tabular_content( $widget_id = null ) {
		return [
			'tables' => [
				[
					'title' => 'Sample Table',
					'data'  => [
						[ 'Name', 'Email', 'Role' ],
						[ 'John Doe', 'john@example.com', 'Admin' ],
						[ 'Jane Smith', 'jane@example.com', 'Editor' ],
					],
				],
			],
		];
	}

	/**
	 * Get tabular content with settings.
	 *
	 * @since 1.0.0
	 *
	 * @param string $widget_id Widget ID.
	 * @param array  $settings  Widget settings.
	 *
	 * @return array
	 */
	private function get_tabular_content_with_settings( $widget_id, $settings ) {
		return [
			'tables' => $settings['tables'] ?? [
				[
					'title' => 'Sample Table',
					'data'  => [
						[ 'Name', 'Email', 'Role' ],
						[ 'John Doe', 'john@example.com', 'Admin' ],
						[ 'Jane Smith', 'jane@example.com', 'Editor' ],
					],
				],
			],
		];
	}
}
