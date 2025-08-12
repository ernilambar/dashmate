import React from 'react';

// Helper function to render description
const renderDescription = ( description ) => {
	if ( ! description ) {
		return null;
	}
	return <div className="description">{ description }</div>;
};

export default function FieldWrapper( { label, description, children } ) {
	return (
		<div className="field-wrapper">
			{ label && <label>{ label }</label> }
			{ renderDescription( description ) }
			{ children }
		</div>
	);
}
