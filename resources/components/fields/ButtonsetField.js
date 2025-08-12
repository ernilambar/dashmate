import React from 'react';
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
	return (
		<FieldWrapper label={ label } description={ description }>
			<div className="buttonset-container">
				{ choices &&
					Array.isArray( choices ) &&
					choices.length > 0 &&
					choices.map( ( choice, index ) => {
						const isActive = ( value || defaultValue ) === choice.value;

						return (
							<button
								key={ choice.value }
								type="button"
								onClick={ () => onChange( fieldKey, choice.value ) }
								className={ isActive ? 'active' : '' }
								title={ choice.label }
							>
								{ choice.label }
							</button>
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
						No choices defined for buttonset field
					</div>
				) }
			</div>
		</FieldWrapper>
	);
}
