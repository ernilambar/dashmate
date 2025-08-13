import React from 'react';
import { TextControl } from '@wordpress/components';
import FieldWrapper from './FieldWrapper';

export default function TextField( { label, description, value, onChange, fieldKey } ) {
	return (
		<FieldWrapper label={ label } description={ description } fieldType="text">
			<TextControl
				value={ value || '' }
				onChange={ ( newValue ) => onChange( fieldKey, newValue ) }
			/>
		</FieldWrapper>
	);
}
