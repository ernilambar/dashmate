<?php
/**
 * Widget_Registry
 *
 * @package Dashmate
 */

declare(strict_types=1);

namespace Nilambar\Dashmate;

use Nilambar\Dashmate\Widgets\Sample_HTML;
use Nilambar\Dashmate\Widgets\Sample_Line_Chart;
use Nilambar\Dashmate\Widgets\Sample_Links;
use Nilambar\Dashmate\Widgets\Sample_Progress_Circles;
use Nilambar\Dashmate\Widgets\Sample_Tabular;

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
		// Register widget instances.
		Widget_Dispatcher::register_widget( new Sample_HTML( 'sample-html' ) );
		Widget_Dispatcher::register_widget( new Sample_Links( 'sample-links' ) );
		Widget_Dispatcher::register_widget( new Sample_Progress_Circles( 'sample-progress-circles' ) );
		Widget_Dispatcher::register_widget( new Sample_Tabular( 'sample-tabular' ) );
		Widget_Dispatcher::register_widget( new Sample_Line_Chart( 'sample-line-chart' ) );
	}

	/**
	 * Create widget instance with ID.
	 *
	 * @since 1.0.0
	 *
	 * @param string $id Widget instance ID.
	 *
	 * @return Abstract_Widget|null
	 */
	public static function create_widget_instance( $id ) {
		return Widget_Dispatcher::get_widget( $id );
	}
}
