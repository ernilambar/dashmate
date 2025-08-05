/**
 * JSON Highlight Component.
 *
 * @package Dashmate
 */

import React from 'react';

/**
 * JSON Highlight Component.
 *
 * @param {Object} props - Component props.
 * @param {Object|string} props.data - Data to display (object will be stringified).
 * @param {string} props.className - Additional CSS classes.
 * @returns {JSX.Element} JSON highlight component.
 */
const JsonHighlight = ( { data, className = '' } ) => {
	// Convert data to JSON string if it's an object.
	const jsonStr = typeof data === 'object' ? JSON.stringify( data, null, 2 ) : data;

	// Function to highlight JSON with proper multilevel support.
	const highlightJson = ( jsonString ) => {
		// Split the JSON string into lines to preserve indentation.
		const lines = jsonString.split( '\n' );

		return lines
			.map( ( line ) => {
				// Handle object keys (property names).
				let highlightedLine = line.replace(
					/"([^"]+)":/g,
					'"<span class="json-key">$1</span>":'
				);

				// Handle array brackets and braces.
				highlightedLine = highlightedLine.replace(
					/([\[\]{}])/g,
					'<span class="json-bracket">$1</span>'
				);

				return highlightedLine;
			} )
			.join( '\n' );
	};

	return (
		<pre className={ `dashmate-layouts-json-content ${ className }` }>
			<code
				dangerouslySetInnerHTML={ {
					__html: highlightJson( jsonStr ),
				} }
			/>
		</pre>
	);
};

export default JsonHighlight;
