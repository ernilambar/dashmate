import React from 'react';

/**
 * Generic Icon Component
 *
 * @param {Object} props - Component props
 * @param {string} props.name - Icon name
 * @param {string} props.library - Icon library (e.g., 'material-icons', 'dashicons')
 * @param {string} props.className - Additional CSS classes
 * @param {Object} props.style - Inline styles
 * @param {string} props.size - Icon size ('small', 'medium', 'large', or custom)
 * @returns {JSX.Element} Icon component
 */
const Icon = ( {
	name,
	library = 'material-icons',
	className = '',
	style = {},
	size = 'medium',
} ) => {
	// Size mappings
	const sizeMap = {
		small: { fontSize: '12px', width: '12px', height: '12px' },
		medium: { fontSize: '16px', width: '16px', height: '16px' },
		large: { fontSize: '24px', width: '24px', height: '24px' },
		xlarge: { fontSize: '32px', width: '32px', height: '32px' },
	};

	// Get size styles
	const sizeStyles = sizeMap[ size ] || { fontSize: size, width: size, height: size };

	// Combine styles
	const combinedStyles = {
		...sizeStyles,
		lineHeight: 1,
		display: 'inline-flex',
		alignItems: 'center',
		justifyContent: 'center',
		...style,
	};

	// If using Dashicons, render class-based glyph and omit ligature text
	if ( library === 'dashicons' ) {
		const dashiconsClasses = `icon dashicons dashicons-${ name } ${ className }`;
		return <span className={ dashiconsClasses } style={ combinedStyles } aria-hidden="true" />;
	}

	// Default to ligature/text-based libraries (Material Icons, etc.)
	const classes = `icon ${ library } ${ className }`;
	return (
		<span className={ classes } style={ combinedStyles }>
			{ name }
		</span>
	);
};

export default Icon;
