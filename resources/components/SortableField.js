import React, { useState } from 'react';
import { DragDropContext, Droppable, Draggable } from '@hello-pangea/dnd';

export default function SortableField( { label, choices = [], value = [], onChange } ) {
	const [ items, setItems ] = useState( () => {
		// Initialize items preserving the saved order from value
		const savedOrder = value || [];
		const choicesMap = new Map( choices.map( ( choice ) => [ choice.value, choice ] ) );

		// Create items in the saved order, with enabled state
		const orderedItems = savedOrder
			.map( ( value ) => {
				const choice = choicesMap.get( value );
				if ( choice ) {
					return { ...choice, enabled: true };
				}
				return null;
			} )
			.filter( ( item ) => item !== null );

		// Add any remaining choices that weren't in the saved order
		const remainingChoices = choices.filter(
			( choice ) => ! savedOrder.includes( choice.value )
		);
		const allItems = [
			...orderedItems,
			...remainingChoices.map( ( choice ) => ( { ...choice, enabled: false } ) ),
		];

		return allItems;
	} );

	// Update items when choices changes (but preserve user's custom order)
	React.useEffect( () => {
		// Only update if choices have changed, not when value changes
		// This preserves the user's custom order
		const savedOrder = value || [];
		const choicesMap = new Map( choices.map( ( choice ) => [ choice.value, choice ] ) );

		// Create items in the saved order, with enabled state
		const orderedItems = savedOrder
			.map( ( value ) => {
				const choice = choicesMap.get( value );
				if ( choice ) {
					return { ...choice, enabled: true };
				}
				return null;
			} )
			.filter( ( item ) => item !== null );

		// Add any remaining choices that weren't in the saved order
		const remainingChoices = choices.filter(
			( choice ) => ! savedOrder.includes( choice.value )
		);
		const allItems = [
			...orderedItems,
			...remainingChoices.map( ( choice ) => ( { ...choice, enabled: false } ) ),
		];

		setItems( allItems );
	}, [ choices, value ] ); // Need to include value here to handle initial load

	const handleDragEnd = ( result ) => {
		if ( ! result.destination ) {
			return;
		}

		// Don't update if the item was dropped in the same position
		if ( result.destination.index === result.source.index ) {
			return;
		}

		const newItems = Array.from( items );
		const [ reorderedItem ] = newItems.splice( result.source.index, 1 );
		newItems.splice( result.destination.index, 0, reorderedItem );

		setItems( newItems );

		// Update parent with enabled values in new order
		const enabledValues = newItems
			.filter( ( item ) => item.enabled )
			.map( ( item ) => item.value );

		onChange( enabledValues );
	};

	const handleToggle = ( index ) => {
		const newItems = [ ...items ];
		newItems[ index ].enabled = ! newItems[ index ].enabled;
		setItems( newItems );

		// Update parent with enabled values
		const enabledValues = newItems
			.filter( ( item ) => item.enabled )
			.map( ( item ) => item.value );
		onChange( enabledValues );
	};

	return (
		<div className="sortable-field-container">
			<label>{ label }</label>
			<div style={ { marginTop: 8 } }>
				<DragDropContext onDragEnd={ handleDragEnd }>
					<Droppable droppableId="sortable-list" direction="vertical">
						{ ( provided ) => (
							<div
								{ ...provided.droppableProps }
								ref={ provided.innerRef }
								className="sortable-list"
							>
								{ items.map( ( item, index ) => (
									<Draggable
										key={ `${ item.value }-${ index }` }
										draggableId={ item.value }
										index={ index }
									>
										{ ( provided, snapshot ) => (
											<div
												ref={ provided.innerRef }
												{ ...provided.draggableProps }
												{ ...provided.dragHandleProps }
												className={ `sortable-item ${
													! item.enabled ? 'disabled' : ''
												} ${ snapshot.isDragging ? 'dragging' : '' }` }
												style={ provided.draggableProps.style }
											>
												<div className="item-content">
													<div className="drag-handle">⋮⋮</div>
													<span className="item-label">
														{ item.label }
													</span>
												</div>
												<label className="item-toggle">
													<input
														type="checkbox"
														checked={ item.enabled }
														onChange={ () => handleToggle( index ) }
													/>
													<span>
														{ item.enabled ? 'Enabled' : 'Disabled' }
													</span>
												</label>
											</div>
										) }
									</Draggable>
								) ) }
								{ provided.placeholder }
								{ items.length === 0 && (
									<div className="empty-state">No items available</div>
								) }
							</div>
						) }
					</Droppable>
				</DragDropContext>
				{ items.length > 0 && (
					<div className="help-text">Drag to reorder • Toggle to enable/disable</div>
				) }
			</div>
		</div>
	);
}
