<?php
/**
 * YML_Utils
 *
 * @package Dashmate
 */

declare(strict_types=1);

namespace Nilambar\Dashmate\Utils;

use Symfony\Component\Yaml\Yaml;

/**
 * YML_Utils class.
 *
 * @since 1.0.0
 */
class YML_Utils {

	/**
	 * Convert data to YAML string.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $data Data to convert.
	 * @param int   $indent Indentation level. Default 4.
	 * @param int   $inline Inline level. Default 2.
	 * @return string|false YAML string or false on failure.
	 */
	public static function to_yaml( $data, int $indent = 4, int $inline = 2 ) {
		try {
			$yaml = new Yaml();
			return $yaml->dump( $data, $indent, $inline );
		} catch ( \Exception $e ) {
			return false;
		}
	}

	/**
	 * Parse YAML string to data.
	 *
	 * @since 1.0.0
	 *
	 * @param string $yaml_string YAML string to parse.
	 * @return mixed Parsed data or null on failure.
	 */
	public static function from_yaml( string $yaml_string ) {
		try {
			$yaml = new Yaml();
			return $yaml->parse( $yaml_string );
		} catch ( \Exception $e ) {
			return null;
		}
	}

	/**
	 * Save data to YAML file.
	 *
	 * @since 1.0.0
	 *
	 * @param string $file_path File path to save.
	 * @param mixed  $data Data to save.
	 * @param int    $indent Indentation level. Default 4.
	 * @param int    $inline Inline level. Default 2.
	 * @return bool True on success, false on failure.
	 */
	public static function save_to_file( string $file_path, $data, int $indent = 4, int $inline = 2 ): bool {
		$yaml_string = self::to_yaml( $data, $indent, $inline );

		if ( false === $yaml_string ) {
			return false;
		}

		$result = file_put_contents( $file_path, $yaml_string );

		return false !== $result;
	}

	/**
	 * Load data from YAML file.
	 *
	 * @since 1.0.0
	 *
	 * @param string $file_path File path to load.
	 * @return mixed Parsed data or null on failure.
	 */
	public static function load_from_file( string $file_path ) {
		if ( ! file_exists( $file_path ) || ! is_readable( $file_path ) ) {
			return null;
		}

		$file_content = file_get_contents( $file_path );

		if ( false === $file_content ) {
			return null;
		}

		return self::from_yaml( $file_content );
	}

	/**
	 * Validate if string is valid YAML.
	 *
	 * @since 1.0.0
	 *
	 * @param string $yaml_string YAML string to validate.
	 * @return bool True if valid YAML, false otherwise.
	 */
	public static function is_valid_yaml( string $yaml_string ): bool {
		$parsed = self::from_yaml( $yaml_string );
		return null !== $parsed;
	}

	/**
	 * Get YAML file extension.
	 *
	 * @since 1.0.0
	 *
	 * @return string YAML file extension.
	 */
	public static function get_file_extension(): string {
		return 'yml';
	}

	/**
	 * Get YAML MIME type.
	 *
	 * @since 1.0.0
	 *
	 * @return string YAML MIME type.
	 */
	public static function get_mime_type(): string {
		return 'text/yaml';
	}
}
