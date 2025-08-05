<?php
/**
 * JSON_Utils
 *
 * @package Dashmate
 */

declare(strict_types=1);

namespace Nilambar\Dashmate\Utils;

/**
 * JSON_Utils class.
 *
 * @since 1.0.0
 */
class JSON_Utils {

	/**
	 * Parse JSON file.
	 *
	 * @since 1.0.0
	 *
	 * @param string $file_path File path to parse.
	 * @return mixed|WP_Error Parsed data or WP_Error on failure.
	 */
	public static function parse_file( string $file_path ) {
		if ( ! file_exists( $file_path ) ) {
			return new WP_Error( 'file_not_found', sprintf( 'File does not exist: %s', $file_path ) );
		}

		if ( ! is_readable( $file_path ) ) {
			return new WP_Error( 'file_not_readable', sprintf( 'File is not readable: %s', $file_path ) );
		}

		$file_content = file_get_contents( $file_path );

		if ( false === $file_content ) {
			return new WP_Error( 'file_read_error', sprintf( 'Failed to read file: %s', $file_path ) );
		}

		return self::decode_from_json( $file_content );
	}

	/**
	 * Save JSON data to file.
	 *
	 * @since 1.0.0
	 *
	 * @param string $file_path File path to save.
	 * @param mixed  $data Data to save.
	 * @return bool|WP_Error True on success, WP_Error on failure.
	 */
	public static function save_json_to_file( string $file_path, $data ) {
		$json_string = self::encode_to_json( $data );

		if ( is_wp_error( $json_string ) ) {
			return $json_string;
		}

		$result = file_put_contents( $file_path, $json_string );

		if ( false === $result ) {
			return new WP_Error( 'file_write_error', sprintf( 'Failed to write file: %s', $file_path ) );
		}

		return true;
	}

	/**
	 * Encode data to JSON string.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $data Data to encode.
	 * @return string|WP_Error JSON string or WP_Error on failure.
	 */
	public static function encode_to_json( $data ) {
		$json_string = wp_json_encode( $data, JSON_PRETTY_PRINT );

		if ( false === $json_string ) {
			$error_message = json_last_error_msg();
			return new WP_Error( 'json_encode_error', sprintf( 'Failed to encode data to JSON: %s', $error_message ) );
		}

		return $json_string;
	}

	/**
	 * Decode JSON string to data.
	 *
	 * @since 1.0.0
	 *
	 * @param string $json_string JSON string to decode.
	 * @return mixed|WP_Error Parsed data or WP_Error on failure.
	 */
	public static function decode_from_json( string $json_string ) {
		$data = json_decode( $json_string, true );

		if ( json_last_error() !== JSON_ERROR_NONE ) {
			$error_message = json_last_error_msg();
			return new WP_Error( 'json_decode_error', sprintf( 'JSON decode error: %s', $error_message ) );
		}

		return $data;
	}

	/**
	 * Validate if string is valid JSON.
	 *
	 * @since 1.0.0
	 *
	 * @param string $json_string JSON string to validate.
	 * @return bool True if valid JSON, false otherwise.
	 */
	public static function is_valid_json( string $json_string ): bool {
		$parsed = self::decode_from_json( $json_string );
		return ! is_wp_error( $parsed );
	}
}
