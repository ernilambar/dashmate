import React from 'react';
import { CheckboxControl } from '@wordpress/components';
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
							<CheckboxControl
								key={ choice.value }
								label={ choice.label }
								checked={ isChecked }
								onChange={ ( checked ) => {
									const newValue = checked
										? [ ...currentValue, choice.value ]
										: currentValue.filter( ( v ) => v !== choice.value );
									onChange( fieldKey, newValue );
								} }
							/>
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
