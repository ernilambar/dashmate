/**
 * Dashmate Settings JavaScript
 *
 * @package Dashmate
 */

import './css/settings.css';

document.addEventListener( 'DOMContentLoaded', function () {
	'use strict';

	const applyButton = document.getElementById( 'dashmate-apply-layout-btn' );
	const statusDiv = document.getElementById( 'dashmate-apply-status' );

	if ( ! applyButton || ! statusDiv ) {
		return;
	}

	// Apply layout button functionality.
	applyButton.addEventListener( 'click', function ( e ) {
		e.preventDefault();

		// Get selected layout.
		const layoutSelect = document.getElementById( 'dashmate-layout' );
		const selectedLayout = layoutSelect ? layoutSelect.value : 'default';

		// Confirm action.
		if ( ! confirm( dashmateSettings.strings.confirmApply ) ) {
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
				applyButton.disabled = false;
				applyButton.innerHTML = dashmateSettings.strings.applyLayout;
			} );
	} );
} );
