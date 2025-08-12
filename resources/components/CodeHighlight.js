/**
 * Code Highlight Component.
 *
 * @package Dashmate
 */

import React from 'react';

/**
 * Code Highlight Component.
 *
 * @param {Object} props - Component props.
 * @param {string} props.code - Code string to display.
 * @param {string} props.language - Language for syntax highlighting (default: 'json').
 * @param {string} props.className - Additional CSS classes.
 * @param {boolean} props.showLineNumbers - Whether to show line numbers.
 * @returns {JSX.Element} Code highlight component.
 */
const CodeHighlight = ( {
	code = '',
	language = 'json',
	className = '',
	showLineNumbers = false,
} ) => {
	// Function to highlight JSON with proper multilevel support.
	const highlightJson = ( jsonString ) => {
		// Split the JSON string into lines to preserve indentation.
		const lines = jsonString.split( '\n' );

		return lines
			.map( ( line ) => {
				// Handle object keys (property names).
				let highlightedLine = line.replace(
					/"([^"]+)":/g,
					'"<span class="dm-code-key">$1</span>":'
				);

				// Handle array brackets and braces.
				highlightedLine = highlightedLine.replace(
					/([\[\]{}])/g,
					'<span class="dm-code-bracket">$1</span>'
				);

				return highlightedLine;
			} )
			.join( '\n' );
	};

	// Currently only supporting JSON highlighting.
	const highlightedCode = language === 'json' ? highlightJson( code ) : code;

	// Generate line numbers if requested.
	const generateLineNumbers = ( codeString ) => {
		if ( ! showLineNumbers ) {
			return null;
		}

		const lines = codeString.split( '\n' );
		return (
			<div className="dm-code-line-numbers">
				{ lines.map( ( _, index ) => (
					<div key={ index } className="dm-code-line-number">
						{ index + 1 }
					</div>
				) ) }
			</div>
		);
	};

	return (
		<div
			className={ `dm-code-highlight dm-code-${ language } ${
				showLineNumbers ? 'dm-code-highlight--with-lines' : ''
			} ${ className }` }
		>
			{ showLineNumbers && generateLineNumbers( code ) }
			<pre className="dm-code-content">
				<code
					className={ `language-${ language }` }
					dangerouslySetInnerHTML={ {
						__html: highlightedCode,
					} }
				/>
			</pre>
		</div>
	);
};

export default CodeHighlight;
