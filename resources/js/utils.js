/**
 * Copy text to clipboard.
 *
 * @param {string} text - Text to copy to clipboard.
 * @returns {Promise<boolean>} Promise that resolves to true if copy was successful, false otherwise.
 */
const copyToClipboard = async ( text ) => {
	try {
		await navigator.clipboard.writeText( text );
		return true;
	} catch ( err ) {
		console.error( 'Failed to copy: ', err );
		return false;
	}
};

/**
 * Create a debounced function.
 *
 * @param {Function} func - Function to debounce.
 * @param {number} delay - Delay in milliseconds.
 * @returns {Function} Debounced function wrapper.
 */
const debounce = ( func, delay ) => {
	let inDebounce;
	return function ( ...args ) {
		const context = this;
		const event = args[ 0 ];

		if ( event && typeof event.preventDefault === 'function' ) {
			event.preventDefault();
		}

		clearTimeout( inDebounce );
		inDebounce = setTimeout( () => func.apply( context, args ), delay );
	};
};

export { copyToClipboard, debounce };
