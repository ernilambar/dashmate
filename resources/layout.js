/**
 * Layout page functionality.
 *
 * @package Dashmate
 */

import './css/layout.css';
import { copyToClipboard } from './js/utils.js';

/**
 * Copy YAML content to clipboard.
 */
async function copyYamlContent() {
	const yamlElement = document.getElementById( 'dashmate-layout-content' );
	const codeElement = yamlElement.querySelector( 'code' );

	if ( codeElement ) {
		const yamlText = codeElement.textContent || codeElement.innerText;
		const button = event.target;

		const success = await copyToClipboard( yamlText );

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

			console.log( 'YAML content copied successfully' );
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

			console.error( 'Failed to copy YAML content' );
		}
	}
}

// Make function globally available.
window.copyYamlContent = copyYamlContent;
