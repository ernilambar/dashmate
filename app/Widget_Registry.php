<?php
/**
 * Widget_Registry
 *
 * @package Dashmate
 */

declare(strict_types=1);

namespace Nilambar\Dashmate;

use Nilambar\Dashmate\Widgets\Quick_Links_Widget;
use Nilambar\Dashmate\Widgets\Welcome_HTML_Widget;

/**
 * Widget_Registry class.
 *
 * @since 1.0.0
 */
class Widget_Registry {

	/**
	 * Initialize widget registry.
	 *
	 * @since 1.0.0
	 */
	public static function init() {
		self::register_default_widgets();
	}

	/**
	 * Register default widgets.
	 *
	 * @since 1.0.0
	 */
	private static function register_default_widgets() {
		// Register widget instances (each with their own ID).
		Widget_Dispatcher::register_widget( new Welcome_HTML_Widget( 'welcome-html-1' ) );
		Widget_Dispatcher::register_widget( new Quick_Links_Widget( 'quick-links-1' ) );
	}

	/**
	 * Create widget instance with ID.
	 *
	 * @since 1.0.0
	 *
	 * @param string $type Widget type.
	 * @param string $id   Widget instance ID.
	 *
	 * @return Abstract_Widget|null
	 */
	public static function create_widget_instance( $type, $id ) {
		return Widget_Dispatcher::create_widget_instance( $type, $id );
	}
}
