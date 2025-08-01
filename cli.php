<?php
/**
 * CLI
 *
 * @package Dashmate
 */

use Nilambar\Dashmate\CLI\Layout_Command;

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	return;
}

$layout_command = new Layout_Command();

WP_CLI::add_command( 'dashmate layout', $layout_command );
