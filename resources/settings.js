/**
 * Dashmate Settings JavaScript
 *
 * @package Dashmate
 */

document.addEventListener( 'DOMContentLoaded', function () {
	'use strict';

	const resetButton = document.getElementById( 'dashmate-reset-layout-btn' );
	const statusDiv = document.getElementById( 'dashmate-reset-status' );

	if ( ! resetButton || ! statusDiv ) {
		return;
	}

	// Reset layout button functionality.
	resetButton.addEventListener( 'click', function ( e ) {
		e.preventDefault();

		// Confirm action.
		if ( ! confirm( dashmateSettings.strings.confirmReset ) ) {
			return;
		}

		// Disable button and show loading state.
		resetButton.disabled = true;
		resetButton.innerHTML = dashmateSettings.strings.resetting;

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
				action: 'dashmate_reset_layout',
				nonce: dashmateSettings.nonce,
			} ),
		} )
			.then( ( response ) => response.json() )
			.then( ( data ) => {
				if ( data.success ) {
					statusDiv.className = 'notice notice-success';
					statusDiv.innerHTML =
						'<p><strong>' +
						dashmateSettings.strings.success +
						'</strong></p><p>' +
						data.data +
						'</p>';
				} else {
					statusDiv.className = 'notice notice-error';
					statusDiv.innerHTML =
						'<p><strong>' +
						dashmateSettings.strings.error +
						'</strong></p><p>' +
						( data.data || 'Unknown error occurred.' ) +
						'</p>';
				}
				statusDiv.style.display = 'block';
			} )
			.catch( ( error ) => {
				statusDiv.className = 'notice notice-error';
				statusDiv.innerHTML =
					'<p><strong>' +
					dashmateSettings.strings.error +
					'</strong></p><p>' +
					error.message +
					'</p>';
				statusDiv.style.display = 'block';
			} )
			.finally( () => {
				// Re-enable button and restore original text.
				resetButton.disabled = false;
				resetButton.innerHTML = 'Reset Layout';
			} );
	} );
} );
