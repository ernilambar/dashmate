import React from 'react';
import { SelectControl } from '@wordpress/components';
import FieldWrapper from './FieldWrapper';

export default function SelectField( {
	label,
	description,
	value,
	onChange,
	fieldKey,
	choices = [],
	defaultValue,
} ) {
	return (
		<FieldWrapper label={ label } description={ description } fieldType="select">
			<SelectControl
				value={ value || defaultValue }
				options={ choices }
				onChange={ ( newValue ) => onChange( fieldKey, newValue ) }
			/>
		</FieldWrapper>
	);
}
