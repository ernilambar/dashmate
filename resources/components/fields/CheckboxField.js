import React from 'react';
import { CheckboxControl } from '@wordpress/components';
import FieldWrapper from './FieldWrapper';

export default function CheckboxField( {
	label,
	description,
	message,
	value,
	onChange,
	fieldKey,
} ) {
	return (
		<FieldWrapper label={ label } description={ description } fieldType="checkbox">
			<CheckboxControl
				label={ message || '' }
				checked={ !! value }
				onChange={ ( newValue ) => onChange( fieldKey, newValue ) }
			/>
		</FieldWrapper>
	);
}
