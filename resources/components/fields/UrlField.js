import React from 'react';
import { TextControl } from '@wordpress/components';
import FieldWrapper from './FieldWrapper';

export default function UrlField( { label, description, value, onChange, fieldKey } ) {
	return (
		<FieldWrapper label={ label } description={ description } fieldType="url">
			<TextControl
				type="url"
				value={ value || '' }
				onChange={ ( newValue ) => onChange( fieldKey, newValue ) }
			/>
		</FieldWrapper>
	);
}
