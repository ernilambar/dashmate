<?php
/**
 * API_Main
 *
 * @package Dashmate
 */

declare(strict_types=1);

namespace Nilambar\Dashmate\API;

use Nilambar\Dashmate\Widget_Registry;

/**
 * API_Main class.
 *
 * @since 1.0.0
 */
class API_Main {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		// Initialize widget registry before registering API routes.
		Widget_Registry::init();

		$this->init_controllers();
	}

	/**
	 * Initialize controllers.
	 *
	 * @since 1.0.0
	 */
	private function init_controllers() {
		new Dashboard_Controller();
		new Widgets_Controller();
		new Columns_Controller();
	}
}
