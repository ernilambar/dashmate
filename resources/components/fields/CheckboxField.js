import React from 'react';
import FieldWrapper from './FieldWrapper';

export default function CheckboxField( { label, description, value, onChange, fieldKey } ) {
	return (
		<FieldWrapper description={ description } fieldType="checkbox">
			<label>
				<input
					type="checkbox"
					checked={ !! value }
					onChange={ ( e ) => onChange( fieldKey, e.target.checked ) }
				/>
				{ label }
			</label>
		</FieldWrapper>
	);
}
