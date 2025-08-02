<?php
/**
 * Widget_Template_Registry
 *
 * @package Dashmate
 */

declare(strict_types=1);

namespace Nilambar\Dashmate;

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
			'component'     => 'HtmlWidget',
			'capabilities'  => ['html-content', 'script-support'],
			'default_settings' => [
				'allow_scripts' => false,
			],
		],
		'links'            => [
			'component'     => 'LinksWidget',
			'capabilities'  => ['grid', 'list', 'icons'],
			'default_settings' => [
				'hideIcon'  => false,
				'linkStyle' => 'list',
			],
		],
		'progress-circles' => [
			'component'     => 'ProgressCirclesWidget',
			'capabilities'  => ['animation', 'color-coding'],
			'default_settings' => [
				'hideCaption' => false,
			],
		],
		'tabular'          => [
			'component'     => 'TabularWidget',
			'capabilities'  => ['sorting', 'pagination', 'filtering', 'search'],
			'default_settings' => [
				'showHeaders' => true,
				'stripedRows' => true,
			],
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
	 * Get template capabilities.
	 *
	 * @since 1.0.0
	 *
	 * @param string $template_type Template type.
	 *
	 * @return array
	 */
	public static function get_template_capabilities( $template_type ) {
		$template = self::get_template( $template_type );
		return $template['capabilities'] ?? [];
	}

	/**
	 * Get template default settings.
	 *
	 * @since 1.0.0
	 *
	 * @param string $template_type Template type.
	 *
	 * @return array
	 */
	public static function get_template_default_settings( $template_type ) {
		$template = self::get_template( $template_type );
		return $template['default_settings'] ?? [];
	}

	/**
	 * Get template component name.
	 *
	 * @since 1.0.0
	 *
	 * @param string $template_type Template type.
	 *
	 * @return string|null
	 */
	public static function get_template_component( $template_type ) {
		$template = self::get_template( $template_type );
		return $template['component'] ?? null;
	}

	/**
	 * Get widget templates for frontend.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public static function get_templates_for_frontend() {
		$templates          = self::get_templates();
		$frontend_templates = [];

		foreach ( $templates as $type => $template ) {
			$frontend_templates[ $type ] = [
				'component'     => $template['component'],
				'capabilities'  => $template['capabilities'],
				'default_settings' => $template['default_settings'],
			];
		}

		return $frontend_templates;
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
