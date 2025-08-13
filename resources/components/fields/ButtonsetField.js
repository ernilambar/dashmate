import React from 'react';
import { ButtonGroup, Button } from '@wordpress/components';
import FieldWrapper from './FieldWrapper';

export default function ButtonsetField( {
	label,
	description,
	value,
	onChange,
	fieldKey,
	choices = [],
	defaultValue,
} ) {
	const currentValue = value || defaultValue;

	return (
		<FieldWrapper label={ label } description={ description } fieldType="buttonset">
			<ButtonGroup>
				{ choices &&
					Array.isArray( choices ) &&
					choices.length > 0 &&
					choices.map( ( choice ) => {
						const isActive = currentValue === choice.value;

						return (
							<Button
								key={ choice.value }
								isPrimary={ isActive }
								isSecondary={ ! isActive }
								onClick={ () => onChange( fieldKey, choice.value ) }
								title={ choice.label }
							>
								{ choice.label }
							</Button>
						);
					} ) }
			</ButtonGroup>
			{ ( ! choices || ! Array.isArray( choices ) || choices.length === 0 ) && (
				<div className="no-choices-message">No choices defined for buttonset field</div>
			) }
		</FieldWrapper>
	);
}
