import React from 'react';
import { RadioControl } from '@wordpress/components';
import FieldWrapper from './FieldWrapper';

export default function RadioField( {
	label,
	description,
	value,
	onChange,
	fieldKey,
	choices = [],
	defaultValue,
} ) {
	return (
		<FieldWrapper label={ label } description={ description } fieldType="radio">
			<RadioControl
				selected={ value || defaultValue }
				options={ choices }
				onChange={ ( newValue ) => onChange( fieldKey, newValue ) }
			/>
		</FieldWrapper>
	);
}
