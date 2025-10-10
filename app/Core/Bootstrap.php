<?php
/**
 * Bootstrap
 *
 * @package Dashmate
 */

namespace Nilambar\Dashmate\Core;

use Nilambar\Dashmate\Admin\Main_Dashboard_Page;
use Nilambar\Dashmate\API\API_Main;
use Nilambar\Dashmate\Widget_Initializer;

/**
 * Bootstrap class.
 *
 * @since 1.0.0
 */
class Bootstrap {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'init', [ $this, 'init_widgets' ] );
		add_filter( 'plugin_action_links_' . DASHMATE_BASE_FILENAME, [ $this, 'customize_action_links' ] );

		new API_Main();

		add_action(
			'init',
			function () {
				new Main_Dashboard_Page();
			}
		);
	}

	/**
	 * Initialize widgets.
	 *
	 * @since 1.0.0
	 */
	public function init_widgets() {
		Widget_Initializer::init();
	}

	/**
	 * Customize plugin action links.
	 *
	 * @since 1.0.0
	 *
	 * @param array $actions Action links.
	 * @return array Modified action links.
	 */
	public function customize_action_links( $actions ) {
		$url = add_query_arg( [ 'page' => 'dashmate' ], admin_url( 'admin.php' ) );

		$actions = [ 'dashmate' => '<a href="' . esc_url( $url ) . '">' . esc_html__( 'Dashmate', 'dashmate' ) . '</a>' ] + $actions;

		return $actions;
	}
}
