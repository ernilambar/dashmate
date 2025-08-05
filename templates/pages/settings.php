<?php
/**
 * Template: settings
 *
 * @package Dashmate
 */

use Nilambar\Optify\Panel_Manager;

Panel_Manager::render_panel(
	'dashmate-settings',
	[
		'show_title' => false,
		'display'    => 'inline',
	]
);
