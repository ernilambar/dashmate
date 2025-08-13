import React from 'react';
import { TextControl, Button } from '@wordpress/components';
import FieldWrapper from './FieldWrapper';

export default function NumberField( {
	label,
	description,
	value,
	onChange,
	fieldKey,
	min,
	max,
	defaultValue,
	choices,
} ) {
	const currentValue = value || defaultValue || '';

	return (
		<FieldWrapper label={ label } description={ description } fieldType="number">
			<div className="number-with-choices">
				<TextControl
					type="number"
					value={ currentValue }
					min={ min }
					max={ max }
					onChange={ ( newValue ) => onChange( fieldKey, newValue ) }
					__next40pxDefaultSize={ true }
				/>
				{ choices && Array.isArray( choices ) && choices.length > 0 && (
					<div className="number-choices">
						{ choices.map( ( choice ) => {
							const isActive = String( currentValue ) === String( choice.value );

							return (
								<Button
									key={ choice.value }
									variant={ isActive ? 'primary' : 'secondary' }
									onClick={ () => onChange( fieldKey, choice.value ) }
								>
									{ choice.label }
								</Button>
							);
						} ) }
					</div>
				) }
			</div>
		</FieldWrapper>
	);
}
