import React from 'react';
import { TextareaControl } from '@wordpress/components';
import FieldWrapper from './FieldWrapper';

export default function TextareaField( {
	label,
	description,
	placeholder,
	value,
	onChange,
	fieldKey,
} ) {
	return (
		<FieldWrapper label={ label } description={ description } fieldType="textarea">
			<TextareaControl
				value={ value || '' }
				placeholder={ placeholder }
				onChange={ ( newValue ) => onChange( fieldKey, newValue ) }
				__next40pxDefaultSize={ true }
			/>
		</FieldWrapper>
	);
}
