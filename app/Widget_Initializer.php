<?php
/**
 * Widget_Initializer
 *
 * @package Dashmate
 */

declare(strict_types=1);

namespace Nilambar\Dashmate;

/**
 * Widget_Initializer class.
 *
 * @since 1.0.0
 */
class Widget_Initializer {

	/**
	 * Initialize the widget system.
	 *
	 * @since 1.0.0
	 */
	public static function init() {
		// Initialize the new widget system.
		Widget_Registry::init();
	}
}
