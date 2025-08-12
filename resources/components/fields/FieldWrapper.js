import React from 'react';

// Helper function to render description
const renderDescription = ( description ) => {
	if ( ! description ) {
		return null;
	}
	return <div className="description">{ description }</div>;
};

export default function FieldWrapper( { label, description, children, fieldType } ) {
	const fieldClasses = [ 'dm-field', fieldType ? `dm-field-type-${ fieldType }` : '' ]
		.filter( Boolean )
		.join( ' ' );

	return (
		<div className={ fieldClasses }>
			{ label && <label>{ label }</label> }
			{ renderDescription( description ) }
			{ children }
		</div>
	);
}
