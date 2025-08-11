<?php
/**
 * Layout_Command
 *
 * @package Dashmate
 */

namespace Nilambar\Dashmate\CLI;

use Nilambar\Dashmate\Models\Dashboard_Model;
use Nilambar\Dashmate\Utils\JSON_Utils;
use WP_CLI;

/**
 * Layout_Command class.
 *
 * @since 1.0.0
 */
final class Layout_Command {

	/**
	 * Export layout.
	 *
	 * ## OPTIONS
	 *
	 * [--dir=<dirname>]
	 * : Full path to directory where export files should be stored. Defaults to current working directory.
	 *
	 * ## EXAMPLES
	 *
	 *     # Export layout to current directory.
	 *     $ wp dashmate layout export
	 *
	 *     # Export layout to specific directory.
	 *     $ wp dashmate layout export --dir=/path/to/exports
	 *
	 * @since 1.0.0
	 *
	 * @param array $args       List of the positional arguments.
	 * @param array $assoc_args List of the associative arguments.
	 *
	 * @when after_wp_load
	 * @subcommand export
	 */
	public function export_( $args, $assoc_args = [] ) {
		$defaults = [
			'dir' => null,
		];

		$assoc_args = wp_parse_args( $assoc_args, $defaults );

		if ( ! Dashboard_Model::data_exists() ) {
			WP_CLI::error( 'No dashboard data found.' );
			return;
		}

		$dashboard_data = Dashboard_Model::get_data();

		if ( empty( $dashboard_data ) ) {
			WP_CLI::error( 'No dashboard data found.' );
			return;
		}

		$output = JSON_Utils::encode_to_json( $dashboard_data );

		if ( is_wp_error( $output ) ) {
			WP_CLI::error( $output->get_error_message() );
			return;
		}

		// Determine export directory.
		$export_dir = $assoc_args['dir'];

		if ( empty( $export_dir ) ) {
			$export_dir = getcwd();
		}

		// Ensure directory exists and is writable.
		if ( ! is_dir( $export_dir ) ) {
			WP_CLI::error( sprintf( 'Directory "%s" does not exist.', $export_dir ) );
			return;
		}

		if ( ! is_writable( $export_dir ) ) {
			WP_CLI::error( sprintf( 'Directory "%s" is not writable.', $export_dir ) );
			return;
		}

		// Create file path.
		$file_path = trailingslashit( $export_dir ) . 'layout.json';

		// Write data to file.
		$result = file_put_contents( $file_path, $output );

		if ( false === $result ) {
			WP_CLI::error( sprintf( 'Failed to write file "%s".', $file_path ) );
			return;
		}

		WP_CLI::success( sprintf( 'Layout exported successfully to "%s".', $file_path ) );
	}

	/**
	 * Import layout.
	 *
	 * ## OPTIONS
	 *
	 * <file>
	 * : Path to the JSON file to import.
	 *
	 * ## EXAMPLES
	 *
	 *     # Import layout from JSON file.
	 *     $ wp dashmate layout import layout.json
	 *
	 *     # Import layout from specific path.
	 *     $ wp dashmate layout import /path/to/layout.json
	 *
	 * @since 1.0.0
	 *
	 * @param array $args       List of the positional arguments.
	 * @param array $assoc_args List of the associative arguments.
	 *
	 * @when after_wp_load
	 * @subcommand import
	 */
	public function import_( $args, $assoc_args = [] ) {
		if ( empty( $args[0] ) ) {
			WP_CLI::error( 'File path is required.' );
			return;
		}

		$file_path = $args[0];

		// Check if file exists.
		if ( ! file_exists( $file_path ) ) {
			WP_CLI::error( sprintf( 'File "%s" does not exist.', $file_path ) );
			return;
		}

		// Check if file is readable.
		if ( ! is_readable( $file_path ) ) {
			WP_CLI::error( sprintf( 'File "%s" is not readable.', $file_path ) );
			return;
		}

		// Parse JSON file.
		$dashboard_data = JSON_Utils::parse_file( $file_path );

		if ( is_wp_error( $dashboard_data ) ) {
			WP_CLI::error( sprintf( 'Failed to parse file "%s": %s', $file_path, $dashboard_data->get_error_message() ) );
			return;
		}

		// Validate layout format.
		$validation_result = $this->validate_layout_format( $dashboard_data );
		if ( is_wp_error( $validation_result ) ) {
			WP_CLI::error( sprintf( 'Invalid layout format in file "%s": %s', $file_path, $validation_result->get_error_message() ) );
			return;
		}

		// Set dashboard data.
		$result = Dashboard_Model::set_data( $dashboard_data );

		if ( is_wp_error( $result ) ) {
			WP_CLI::error( sprintf( 'Failed to import layout from file "%s": %s', $file_path, $result->get_error_message() ) );
			return;
		}

		WP_CLI::success( sprintf( 'Layout imported successfully from "%s".', $file_path ) );
	}

	/**
	 * Validate layout format.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data Layout data.
	 *
	 * @return true|WP_Error True if valid, WP_Error if invalid.
	 */
	private function validate_layout_format( $data ) {
		if ( ! is_array( $data ) ) {
			return new \WP_Error( 'invalid_format', 'Layout data must be an array.' );
		}

		if ( ! isset( $data['columns'] ) || ! is_array( $data['columns'] ) ) {
			return new \WP_Error( 'invalid_format', 'Layout must have a "columns" array.' );
		}

		foreach ( $data['columns'] as $index => $column ) {
			if ( ! is_array( $column ) ) {
				return new \WP_Error( 'invalid_format', sprintf( 'Column %d must be an array.', $index ) );
			}

			if ( ! isset( $column['id'] ) || ! is_string( $column['id'] ) ) {
				return new \WP_Error( 'invalid_format', sprintf( 'Column %d must have a string "id".', $index ) );
			}

			// Validate column ID format.
			if ( ! preg_match( '/^col-\d+$/', $column['id'] ) ) {
				return new \WP_Error( 'invalid_format', sprintf( 'Column ID "%s" must match pattern "col-{number}".', $column['id'] ) );
			}

			// Check if widgets array exists and is valid.
			if ( isset( $column['widgets'] ) ) {
				if ( ! is_array( $column['widgets'] ) ) {
					return new \WP_Error( 'invalid_format', sprintf( 'Widgets in column "%s" must be an array.', $column['id'] ) );
				}

				foreach ( $column['widgets'] as $widget_index => $widget ) {
					if ( ! is_array( $widget ) ) {
						return new \WP_Error( 'invalid_format', sprintf( 'Widget %d in column "%s" must be an array.', $widget_index, $column['id'] ) );
					}

					if ( ! isset( $widget['id'] ) || ! is_string( $widget['id'] ) ) {
						return new \WP_Error( 'invalid_format', sprintf( 'Widget %d in column "%s" must have a string "id".', $widget_index, $column['id'] ) );
					}

					// Validate widget ID format.
					if ( ! preg_match( '/^[a-z-]+[a-z0-9-]*$/', $widget['id'] ) ) {
						return new \WP_Error( 'invalid_format', sprintf( 'Widget ID "%s" must contain only lowercase letters, numbers, and hyphens.', $widget['id'] ) );
					}

					// Validate settings if present.
					if ( isset( $widget['settings'] ) && ! is_array( $widget['settings'] ) ) {
						return new \WP_Error( 'invalid_format', sprintf( 'Settings for widget "%s" must be an array.', $widget['id'] ) );
					}

					// Validate collapsed if present.
					if ( isset( $widget['collapsed'] ) && ! is_bool( $widget['collapsed'] ) ) {
						return new \WP_Error( 'invalid_format', sprintf( 'Collapsed property for widget "%s" must be a boolean.', $widget['id'] ) );
					}
				}
			}
		}

		return true;
	}

	/**
	 * Test command.
	 *
	 * ## EXAMPLES
	 *
	 *     # Run test command.
	 *     $ wp dashmate layout test
	 *
	 * @since 1.0.0
	 *
	 * @param array $args       List of the positional arguments.
	 * @param array $assoc_args List of the associative arguments.
	 *
	 * @when after_wp_load
	 * @subcommand test
	 */
	public function test_( $args, $assoc_args = [] ) {
		WP_CLI::success( 'Test.' );
	}
}
