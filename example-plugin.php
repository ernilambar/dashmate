<?php
/**
 * Example Plugin using Dashmate
 *
 * This is an example of how to use Dashmate as a Composer package
 * in your WordPress plugin.
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Include Composer autoloader
require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

use Nilambar\Dashmate\Abstract_Widget;
use Nilambar\Dashmate\Admin\Abstract_Dashboard_Page;
use Nilambar\Dashmate\Dashmate;
use Nilambar\Dashmate\Widget_Registry;

// Initialize Dashmate
Dashmate::init();

// Load assets
Dashmate::load_assets(
	plugin_dir_path( __FILE__ ) . 'vendor/ernilambar/dashmate',
	plugin_dir_url( __FILE__ ) . 'vendor/ernilambar/dashmate/'
);

// Example custom dashboard page
class Example_Dashboard_Page extends Abstract_Dashboard_Page {

	/**
	 * Initialize dashboard page properties.
	 */
	protected function init_properties() {
		$this->page_slug      = 'example-dashboard';
		$this->page_title     = esc_html__( 'Example Dashboard', 'example-plugin' );
		$this->menu_title     = esc_html__( 'Example Dashboard', 'example-plugin' );
		$this->capability     = 'manage_options';
		$this->menu_icon      = 'dashicons-dashboard';
		$this->menu_position  = 30;
		$this->template_name  = 'pages/app';
		$this->dashboard_id   = 'example_main';
		$this->starter_layout = 'default';
	}
}

// Example custom widget
class Example_Stats_Widget extends Abstract_Widget {

	/**
	 * Constructor.
	 */
	public function __construct( $id ) {
		parent::__construct( $id, 'example_stats', esc_html__( 'Example Stats', 'example-plugin' ) );
	}

	/**
	 * Define widget configuration.
	 */
	protected function define_widget() {
		$this->description = esc_html__( 'Display example statistics.', 'example-plugin' );
		$this->icon        = 'chart-bar';

		$this->settings_schema = [
			'show_posts' => [
				'type'        => 'checkbox',
				'label'       => esc_html__( 'Show Post Count', 'example-plugin' ),
				'description' => esc_html__( 'Display total post count.', 'example-plugin' ),
				'default'     => true,
			],
			'show_pages' => [
				'type'        => 'checkbox',
				'label'       => esc_html__( 'Show Page Count', 'example-plugin' ),
				'description' => esc_html__( 'Display total page count.', 'example-plugin' ),
				'default'     => false,
			],
		];
	}

	/**
	 * Get widget content.
	 */
	public function get_content( array $settings = [] ): array {
		$settings = $this->merge_settings_with_defaults( $settings );

		$content = '<h3>' . esc_html__( 'Example Statistics', 'example-plugin' ) . '</h3>';

		if ( $settings['show_posts'] ) {
			$post_count = wp_count_posts( 'post' )->publish;
			$content   .= '<p>' . sprintf(
				esc_html__( 'Published Posts: %d', 'example-plugin' ),
				$post_count
			) . '</p>';
		}

		if ( $settings['show_pages'] ) {
			$page_count = wp_count_posts( 'page' )->publish;
			$content   .= '<p>' . sprintf(
				esc_html__( 'Published Pages: %d', 'example-plugin' ),
				$page_count
			) . '</p>';
		}

		return [
			'html_content' => $content,
		];
	}
}

// Initialize everything
add_action(
	'init',
	function () {
		// Register dashboard page
		new Example_Dashboard_Page();

		// Register custom widget
		Widget_Registry::register_widget( 'example_stats', Example_Stats_Widget::class );
	}
);
