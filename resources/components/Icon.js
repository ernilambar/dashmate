import React from 'react';

/**
 * Generic Icon Component
 *
 * @param {Object} props - Component props
 * @param {string} props.name - Icon name
 * @param {string} props.library - Icon library (e.g., 'remixicon', 'dashicons', 'material-icons', 'svg')
 * @param {string} props.className - Additional CSS classes
 * @param {Object} props.style - Inline styles
 * @param {string} props.size - Icon size ('small', 'medium', 'large', or custom)
 * @param {string} props.svgContent - Raw SVG markup (when library is 'svg')
 * @returns {JSX.Element} Icon component
 */
const Icon = ( {
	name,
	library = 'remixicon',
	className = '',
	style = {},
	size = 'medium',
	svgContent = null,
} ) => {
	// Size mappings
	const sizeMap = {
		small: { fontSize: '12px', width: '12px', height: '12px' },
		medium: { fontSize: '14px', width: '14px', height: '14px' },
		large: { fontSize: '20px', width: '20px', height: '20px' },
		xlarge: { fontSize: '28px', width: '28px', height: '28px' },
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

	// Handle SVG content from plugins
	if ( library === 'svg' && svgContent ) {
		const svgClasses = `dm-icon dm-svg-icon ${ className }`;
		return (
			<span
				className={ svgClasses }
				style={ combinedStyles }
				aria-hidden="true"
				dangerouslySetInnerHTML={ { __html: svgContent } }
			/>
		);
	}

	// If using Dashicons, render class-based glyph and omit ligature text
	if ( library === 'dashicons' ) {
		const dashiconsClasses = `dm-icon dashicons dashicons-${ name } ${ className }`;
		return <span className={ dashiconsClasses } style={ combinedStyles } aria-hidden="true" />;
	}

	// If using Remix Icons, render class-based glyph and omit ligature text
	if ( library === 'remixicon' ) {
		const remixiconClasses = `dm-icon ri ri-${ name }-line ${ className }`;
		return <span className={ remixiconClasses } style={ combinedStyles } aria-hidden="true" />;
	}

	// Default to ligature/text-based libraries (Remix Icons, Material Icons, etc.)
	const classes = `dm-icon dm-${ library } ${ className }`;
	return (
		<span className={ classes } style={ combinedStyles }>
			{ name }
		</span>
	);
};

export default Icon;
