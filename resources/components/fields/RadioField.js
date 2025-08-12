import React from 'react';
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
			<div style={ { marginTop: 4 } }>
				{ choices &&
					Array.isArray( choices ) &&
					choices.length > 0 &&
					choices.map( ( choice ) => (
						<label
							key={ choice.value }
							style={ {
								display: 'block',
								marginBottom: 4,
								cursor: 'pointer',
							} }
						>
							<input
								type="radio"
								name={ fieldKey }
								value={ choice.value }
								checked={ ( value || defaultValue ) === choice.value }
								onChange={ ( e ) => onChange( fieldKey, e.target.value ) }
								style={ { marginRight: 6 } }
							/>
							{ choice.label }
						</label>
					) ) }
				{ ( ! choices || ! Array.isArray( choices ) || choices.length === 0 ) && (
					<div
						style={ {
							color: '#666',
							fontStyle: 'italic',
							fontSize: '12px',
						} }
					>
						No choices defined for radio field
					</div>
				) }
			</div>
		</FieldWrapper>
	);
}
