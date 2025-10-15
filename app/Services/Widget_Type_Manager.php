<?php
/**
 * Widget_Type_Manager
 *
 * @package Dashmate
 */

declare(strict_types=1);

namespace Nilambar\Dashmate\Services;

use WP_Error;

/**
 * Widget_Type_Manager class.
 *
 * @since 1.0.0
 */
class Widget_Type_Manager {

	/**
	 * Registered widget types.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private static $widget_types = [];

	/**
	 * Widget type output schemas.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private static $output_schemas = [
		'html'             => [
			'html_content' => [
				'type'        => 'string',
				'required'    => true,
				'description' => 'HTML content to render.',
			],
		],
		'line-chart'       => [
			'items'          => [
				'type'        => 'array',
				'required'    => true,
				'description' => 'Chart data points.',
			],
			'chart_settings' => [
				'type'        => 'object',
				'required'    => true,
				'description' => 'Chart configuration.',
			],
		],
		'tabular'          => [
			'tables'           => [
				'type'        => 'array',
				'required'    => true,
				'description' => 'Array of table objects.',
			],
			'tabular_settings' => [
				'type'        => 'object',
				'required'    => false,
				'description' => 'Tabular widget configuration settings.',
			],
		],
		'links'            => [
			'links' => [
				'type'        => 'array',
				'required'    => true,
				'description' => 'Array of link objects.',
			],
		],
		'progress-circles' => [
			'items' => [
				'type'        => 'array',
				'required'    => true,
				'description' => 'Progress circle items.',
			],
		],
	];

	/**
	 * Get output schema for a specific widget type.
	 *
	 * @since 1.0.0
	 *
	 * @param string $type Widget type.
	 *
	 * @return array|null
	 */
	public static function get_widget_output_schema( $type ) {
		return self::$output_schemas[ $type ] ?? null;
	}





	/**
	 * Validate widget output against the widget type's output schema.
	 *
	 * @since 1.0.0
	 *
	 * @param string $widget_type Widget type identifier.
	 * @param array  $output      Widget output to validate.
	 *
	 * @return true|WP_Error True if valid, WP_Error if invalid.
	 */
	public static function validate_widget_output( $widget_type, $output ) {
		if ( ! isset( self::$output_schemas[ $widget_type ] ) ) {
			return new WP_Error(
				'invalid_widget_type',
				sprintf( 'Widget type "%s" has no output schema defined.', $widget_type ),
				[ 'widget_type' => $widget_type ]
			);
		}

		$schema = self::$output_schemas[ $widget_type ];
		$errors = [];

		// Validate that all required fields from the output schema are present in the output.
		foreach ( $schema as $field_name => $field_config ) {
			if ( ! isset( $output[ $field_name ] ) ) {
				$errors[] = sprintf( 'Required output field "%s" is missing.', $field_name );
			}
		}

		if ( ! empty( $errors ) ) {
			return new WP_Error(
				'invalid_widget_output',
				sprintf( 'Widget output validation failed for type "%s": %s', $widget_type, implode( '; ', $errors ) ),
				[
					'widget_type' => $widget_type,
					'errors'      => $errors,
				]
			);
		}

		return true;
	}
}
