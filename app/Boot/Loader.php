<?php
/**
 * Loader
 *
 * @package Dashmate
 */

namespace Nilambar\Dashmate\Boot;

use Nilambar\Dashmate\Admin\Admin_Page;
use Nilambar\Dashmate\API\API_Main;
use Nilambar\Dashmate\Widget_Initializer;

/**
 * Loader class.
 *
 * @since 1.0.0
 */
class Loader {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'init', [ $this, 'init_widgets' ] );
		add_filter( 'plugin_action_links_' . DASHMATE_BASE_FILENAME, [ $this, 'customize_action_links' ] );

		new API_Main();
		new Admin_Page();
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
