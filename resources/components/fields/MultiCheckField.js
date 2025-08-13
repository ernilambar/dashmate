import React from 'react';
import FieldWrapper from './FieldWrapper';

export default function MultiCheckField( {
	label,
	description,
	value,
	onChange,
	fieldKey,
	choices = [],
	defaultValue,
} ) {
	return (
		<FieldWrapper label={ label } description={ description } fieldType="multi-check">
			<div className="choices-container">
				{ choices &&
					Array.isArray( choices ) &&
					choices.length > 0 &&
					choices.map( ( choice ) => {
						// Ensure value is always an array, use default if value is not provided.
						const currentValue = Array.isArray( value )
							? value
							: Array.isArray( defaultValue )
							? defaultValue
							: [];
						const isChecked = currentValue.includes( choice.value );

						return (
							<label key={ choice.value } className="choice-item">
								<input
									type="checkbox"
									value={ choice.value }
									checked={ isChecked }
									onChange={ ( e ) => {
										const newValue = e.target.checked
											? [ ...currentValue, choice.value ]
											: currentValue.filter( ( v ) => v !== choice.value );
										onChange( fieldKey, newValue );
									} }
								/>
								{ choice.label }
							</label>
						);
					} ) }
				{ ( ! choices || ! Array.isArray( choices ) || choices.length === 0 ) && (
					<div className="no-choices-message">
						No choices defined for multi-check field
					</div>
				) }
			</div>
		</FieldWrapper>
	);
}
