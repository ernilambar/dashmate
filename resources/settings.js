/**
 * Dashmate Settings JavaScript
 *
 * @package Dashmate
 */

import './css/settings.css';

/**
 * Initialize settings functionality.
 */
function initSettings() {
	'use strict';

	const applyButton = document.getElementById( 'dashmate-apply-layout-btn' );
	const statusDiv = document.getElementById( 'dashmate-apply-status' );

	if ( ! applyButton || ! statusDiv ) {
		return;
	}

	/**
	 * Show notice with specified type and content.
	 *
	 * @param {string} type - Notice type (success, error, warning, info).
	 * @param {string} title - Notice title.
	 * @param {string} message - Notice message.
	 */
	function showNotice( type, title, message ) {
		const markup = '<p><strong>' + title + '</strong></p><p>' + message + '</p>';
		statusDiv.className = 'notice notice-' + type;
		statusDiv.innerHTML = markup;
		statusDiv.style.display = 'block';
	}

	// Apply layout button functionality.
	applyButton.addEventListener( 'click', function ( e ) {
		e.preventDefault();

		// Get selected layout.
		const layoutSelect = document.getElementById( 'dashmate-layout' );
		const selectedLayout = layoutSelect ? layoutSelect.value : '';

		// Validate layout selection.
		if ( ! selectedLayout ) {
			showNotice(
				'error',
				dashmateSettings.strings.error,
				dashmateSettings.strings.selectLayout
			);
			return;
		}

		// Disable button and show loading state.
		applyButton.disabled = true;
		applyButton.innerHTML = dashmateSettings.strings.applying;

		// Clear previous status.
		statusDiv.className = '';
		statusDiv.style.display = 'none';

		// Make AJAX request.
		fetch( dashmateSettings.ajaxUrl, {
			method: 'POST',
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded',
			},
			body: new URLSearchParams( {
				action: 'dashmate_apply_layout',
				nonce: dashmateSettings.nonce,
				layout: selectedLayout,
			} ),
		} )
			.then( ( response ) => response.json() )
			.then( ( data ) => {
				if ( data.success ) {
					showNotice( 'success', dashmateSettings.strings.success, data.data );
				} else {
					showNotice(
						'error',
						dashmateSettings.strings.error,
						data.data || dashmateSettings.strings.unknownError
					);
				}
			} )
			.catch( ( error ) => {
				showNotice( 'error', dashmateSettings.strings.error, error.message );
			} )
			.finally( () => {
				// Re-enable button and restore original text.
				applyButton.disabled = false;
				applyButton.innerHTML = dashmateSettings.strings.applyLayout;
			} );
	} );
}

// Initialize settings when DOM is ready.
document.addEventListener( 'DOMContentLoaded', initSettings );
