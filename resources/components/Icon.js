import React from 'react';

/**
 * Generic Icon Component
 *
 * @param {Object} props - Component props
 * @param {string} props.name - Icon name
 * @param {string} props.library - Icon library (e.g., 'remixicon', 'svg')
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
	const sizeMap = {
		// New T-shirt Sizing System (Primary).
		// These are defined to directly match the legacy names.
		xs: { fontSize: '10px', width: '10px', height: '10px' },
		sm: { fontSize: '12px', width: '12px', height: '12px' }, // Matches 'small'
		md: { fontSize: '14px', width: '14px', height: '14px' }, // Matches 'medium'
		lg: { fontSize: '16px', width: '16px', height: '16px' },
		xl: { fontSize: '18px', width: '18px', height: '18px' },
		'2xl': { fontSize: '20px', width: '20px', height: '20px' }, // Matches 'large'
		'3xl': { fontSize: '28px', width: '28px', height: '28px' }, // Matches 'xlarge'
		'4xl': { fontSize: '32px', width: '32px', height: '32px' },

		// Legacy Names (Aliases).
		// These point to the exact same values as the new names.
		small: { fontSize: '12px', width: '12px', height: '12px' }, // alias for 'sm'
		medium: { fontSize: '14px', width: '14px', height: '14px' }, // alias for 'md'
		large: { fontSize: '20px', width: '20px', height: '20px' }, // alias for '2xl'
		xlarge: { fontSize: '28px', width: '28px', height: '28px' }, // alias for '3xl'
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

	// If using Remix Icons, render class-based glyph and omit ligature text
	if ( library === 'remixicon' ) {
		const remixiconClasses = `dm-icon ri ri-${ name } ${ className }`;
		return <span className={ remixiconClasses } style={ combinedStyles } aria-hidden="true" />;
	}

	// Default to ligature/text-based libraries (Remix Icons, etc.)
	const classes = `dm-icon dm-${ library } ${ className }`;
	return (
		<span className={ classes } style={ combinedStyles }>
			{ name }
		</span>
	);
};

export default Icon;
