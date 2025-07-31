<?php
/**
 * Loader
 *
 * @package Dashmate
 */

namespace Nilambar\Dashmate\Boot;

use Nilambar\Dashmate\Admin\Admin_Page;
use Nilambar\Dashmate\API\API_Main;

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
		new API_Main();
		new Admin_Page();
	}
}
