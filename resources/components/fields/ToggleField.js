import React from 'react';
import { ToggleControl } from '@wordpress/components';
import FieldWrapper from './FieldWrapper';

export default function ToggleField( { label, description, message, value, onChange, fieldKey } ) {
	return (
		<FieldWrapper label={ label } description={ description } fieldType="toggle">
			<ToggleControl
				label={ message || '' }
				checked={ !! value }
				onChange={ ( newValue ) => onChange( fieldKey, newValue ) }
			/>
		</FieldWrapper>
	);
}
