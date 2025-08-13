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
				/>
				{ choices && Array.isArray( choices ) && choices.length > 0 && (
					<div className="number-choices">
						{ choices.map( ( choice ) => {
							const isActive = currentValue === choice.value;

							return (
								<Button
									key={ choice.value }
									isSmall
									isPrimary={ isActive }
									isSecondary={ ! isActive }
									onClick={ () => onChange( fieldKey, choice.value ) }
									title={ `Set to ${ choice.label }` }
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
