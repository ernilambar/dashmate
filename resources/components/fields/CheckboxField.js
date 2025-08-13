import React from 'react';
import { CheckboxControl } from '@wordpress/components';
import FieldWrapper from './FieldWrapper';

export default function CheckboxField( { label, description, value, onChange, fieldKey } ) {
	return (
		<FieldWrapper description={ description } fieldType="checkbox">
			<CheckboxControl
				label={ label }
				checked={ !! value }
				onChange={ ( newValue ) => onChange( fieldKey, newValue ) }
			/>
		</FieldWrapper>
	);
}
