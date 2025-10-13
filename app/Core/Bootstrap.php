<?php
/**
 * Bootstrap
 *
 * @package Dashmate
 */

namespace Nilambar\Dashmate\Core;

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
