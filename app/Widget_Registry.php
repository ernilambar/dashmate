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
	 * @internal This method is for internal use only.
	 */
	private static function register_default_widgets() {
		// Register widget instances.
		self::register_widget_internal( new Sample_HTML( 'sample-html' ) );
		self::register_widget_internal( new Sample_Links( 'sample-links' ) );
		self::register_widget_internal( new Sample_Progress_Circles( 'sample-progress-circles' ) );
		self::register_widget_internal( new Sample_Tabular( 'sample-tabular' ) );
		self::register_widget_internal( new Sample_Line_Chart( 'sample-line-chart' ) );
	}

	/**
	 * Register a widget internally.
	 *
	 * @since 1.0.0
	 * @internal This method is for internal use only. External widgets should be registered via the dashmate_widgets filter.
	 *
	 * @param Abstract_Widget $widget Widget instance.
	 *
	 * @return bool
	 */
	private static function register_widget_internal( Abstract_Widget $widget ) {
		return Widget_Dispatcher::register_widget( $widget );
	}

	/**
	 * Create widget instance with ID.
	 *
	 * @since 1.0.0
	 * @internal This method is for internal use only.
	 *
	 * @param string $id Widget instance ID.
	 *
	 * @return Abstract_Widget|null
	 */
	private static function create_widget_instance( $id ) {
		return Widget_Dispatcher::get_widget( $id );
	}
}
