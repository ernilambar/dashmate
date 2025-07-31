<?php
/**
 * Loader
 *
 * @package Dashmate
 */

namespace Nilambar\Dashmate\Boot;

use Nilambar\Dashmate\Admin\Admin_Page;

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
		new Admin_Page();
	}
}
