<?php
/**
 * Widget_Template_Registry
 *
 * @package Dashmate
 */

declare(strict_types=1);

namespace Nilambar\Dashmate\Services;

/**
 * Widget_Template_Registry class.
 *
 * @since 1.0.0
 */
class Widget_Template_Registry {

	/**
	 * Widget template types.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private static $templates = [
		'html'             => [
			'component' => 'HtmlWidget',
		],
		'links'            => [
			'component' => 'LinksWidget',
		],
		'progress-circles' => [
			'component' => 'ProgressCirclesWidget',
		],
		'line-chart'       => [
			'component' => 'LineChartWidget',
		],
		'tabular'          => [
			'component' => 'TabularWidget',
		],
	];

	/**
	 * Get all registered widget templates.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public static function get_templates() {
		/**
		 * Filter the registered widget templates.
		 *
		 * This filter allows other plugins and addons to add their own widget templates
		 * to the Dashmate dashboard.
		 *
		 * @since 1.0.0
		 *
		 * @param array $templates Array of registered widget templates.
		 */
		return apply_filters( 'dashmate_templates', self::$templates );
	}

	/**
	 * Get a specific widget template.
	 *
	 * @since 1.0.0
	 *
	 * @param string $template_type Template type.
	 *
	 * @return array|null
	 */
	public static function get_template( $template_type ) {
		$templates = self::get_templates();
		return $templates[ $template_type ] ?? null;
	}

	/**
	 * Check if a widget template is registered.
	 *
	 * @since 1.0.0
	 *
	 * @param string $template_type Template type.
	 *
	 * @return bool
	 */
	public static function is_template_registered( $template_type ) {
		$templates = self::get_templates();
		return isset( $templates[ $template_type ] );
	}

	/**
	 * Register a new template.
	 *
	 * @since 1.0.0
	 *
	 * @param string $template_type Template type.
	 * @param array  $config        Template configuration.
	 *
	 * @return bool
	 */
	public static function register_template( $template_type, $config ) {
		if ( empty( $template_type ) || empty( $config ) ) {
			return false;
		}

		self::$templates[ $template_type ] = $config;
		return true;
	}

	/**
	 * Unregister a template.
	 *
	 * @since 1.0.0
	 *
	 * @param string $template_type Template type.
	 *
	 * @return bool
	 */
	public static function unregister_template( $template_type ) {
		if ( ! self::is_template_registered( $template_type ) ) {
			return false;
		}

		unset( self::$templates[ $template_type ] );
		return true;
	}
}
