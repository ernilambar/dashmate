/**
 * Copy text to clipboard.
 *
 * @param {string} text - Text to copy to clipboard.
 * @returns {Promise<boolean>} Promise that resolves to true if copy was successful, false otherwise.
 */
export async function copyToClipboard( text ) {
	try {
		await navigator.clipboard.writeText( text );
		return true;
	} catch ( err ) {
		console.error( 'Failed to copy: ', err );
		return false;
	}
}
