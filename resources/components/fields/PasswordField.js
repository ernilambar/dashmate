import React from 'react';
import { TextControl } from '@wordpress/components';
import FieldWrapper from './FieldWrapper';

export default function PasswordField( {
	label,
	description,
	placeholder,
	value,
	onChange,
	fieldKey,
} ) {
	return (
		<FieldWrapper label={ label } description={ description } fieldType="password">
			<TextControl
				type="password"
				value={ value || '' }
				placeholder={ placeholder }
				onChange={ ( newValue ) => onChange( fieldKey, newValue ) }
				__next40pxDefaultSize={ true }
			/>
		</FieldWrapper>
	);
}
