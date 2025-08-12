import React from 'react';
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
			<select
				value={ value || defaultValue }
				onChange={ ( e ) => onChange( fieldKey, e.target.value ) }
			>
				{ choices &&
					Array.isArray( choices ) &&
					choices.map( ( choice ) => (
						<option key={ choice.value } value={ choice.value }>
							{ choice.label }
						</option>
					) ) }
			</select>
		</FieldWrapper>
	);
}
