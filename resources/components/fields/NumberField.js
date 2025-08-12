import React from 'react';
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
	choices = [],
} ) {
	return (
		<FieldWrapper label={ label } description={ description } fieldType="number">
			<div className="number-with-choices">
				<input
					type="number"
					min={ min }
					max={ max }
					value={ value || defaultValue || '' }
					onChange={ ( e ) => onChange( fieldKey, parseInt( e.target.value ) || 0 ) }
				/>
				{ choices && Array.isArray( choices ) && choices.length > 0 && (
					<div className="number-choices">
						{ choices.map( ( choice ) => {
							// Support both simple values and {value, label} objects
							const choiceValue = typeof choice === 'object' ? choice.value : choice;
							const choiceLabel = typeof choice === 'object' ? choice.label : choice;
							const isActive = ( value || defaultValue ) === choiceValue;

							return (
								<button
									key={ choiceValue }
									type="button"
									onClick={ () => onChange( fieldKey, choiceValue ) }
									className={ isActive ? 'active' : '' }
									title={ `Set to ${ choiceLabel }` }
								>
									{ choiceLabel }
								</button>
							);
						} ) }
					</div>
				) }
			</div>
		</FieldWrapper>
	);
}
