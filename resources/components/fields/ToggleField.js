import React from 'react';
import { ToggleControl } from '@wordpress/components';
import FieldWrapper from './FieldWrapper';

export default function ToggleField( { label, description, value, onChange, fieldKey } ) {
	return (
		<FieldWrapper description={ description } fieldType="toggle">
			<ToggleControl
				label={ label }
				checked={ !! value }
				onChange={ ( newValue ) => onChange( fieldKey, newValue ) }
			/>
		</FieldWrapper>
	);
}
