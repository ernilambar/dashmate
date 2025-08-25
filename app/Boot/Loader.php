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
		add_action( 'dashmate_action_after_page_title', [ $this, 'add_settings_app' ] );

		// Initialize widgets after init hook to ensure text domain is loaded.
		add_action( 'init', [ $this, 'init_widgets' ] );

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
}
