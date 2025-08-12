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
		<FieldWrapper label={ label } description={ description }>
			<div style={ { marginTop: 4 } }>
				{ choices &&
					Array.isArray( choices ) &&
					choices.length > 0 &&
					choices.map( ( choice ) => {
						// Ensure value is always an array, use default if value is not provided
						const currentValue = Array.isArray( value )
							? value
							: Array.isArray( defaultValue )
							? defaultValue
							: [];
						const isChecked = currentValue.includes( choice.value );

						return (
							<label
								key={ choice.value }
								style={ {
									display: 'block',
									marginBottom: 4,
									cursor: 'pointer',
								} }
							>
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
									style={ { marginRight: 6 } }
								/>
								{ choice.label }
							</label>
						);
					} ) }
				{ ( ! choices || ! Array.isArray( choices ) || choices.length === 0 ) && (
					<div
						style={ {
							color: '#666',
							fontStyle: 'italic',
							fontSize: '12px',
						} }
					>
						No choices defined for multi-check field
					</div>
				) }
			</div>
		</FieldWrapper>
	);
}
