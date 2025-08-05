/**
 * Layout page functionality.
 *
 * @package Dashmate
 */

import './css/layout.css';
import { copyToClipboard } from './js/utils.js';

/**
 * Copy layout content to clipboard.
 */
async function copyLayoutContent() {
	const layoutElement = document.getElementById( 'dashmate-layout-content' );
	const codeElement = layoutElement.querySelector( 'code' );

	if ( codeElement ) {
		const layoutText = codeElement.textContent || codeElement.innerText;
		const button = event.target;

		const success = await copyToClipboard( layoutText );

		if ( success ) {
			// Show success feedback.
			const originalText = button.textContent;
			const originalBackgroundColor = button.style.backgroundColor;
			const originalColor = button.style.color;

			button.textContent = 'Copied!';
			button.style.backgroundColor = '#46b450';
			button.style.color = '#fff';

			setTimeout( () => {
				button.textContent = originalText;
				button.style.backgroundColor = originalBackgroundColor;
				button.style.color = originalColor;
			}, 2000 );

			console.log( 'Layout content copied successfully' );
		} else {
			// Show error feedback.
			const originalText = button.textContent;
			const originalBackgroundColor = button.style.backgroundColor;
			const originalColor = button.style.color;

			button.textContent = 'Failed!';
			button.style.backgroundColor = '#dc3232';
			button.style.color = '#fff';

			setTimeout( () => {
				button.textContent = originalText;
				button.style.backgroundColor = originalBackgroundColor;
				button.style.color = originalColor;
			}, 2000 );

			console.error( 'Failed to copy layout content' );
		}
	}
}

// Make function globally available.
window.copyLayoutContent = copyLayoutContent;
