import { Component } from 'react';
import { Droppable } from '@hello-pangea/dnd';
import Widget from './Widget';

class Column extends Component {
	render() {
		const { column, widgets, columnWidgets, onWidgetPropertyUpdate } = this.props;

		return (
			<div className="dashboard-column">
				<Droppable droppableId={ column.id }>
					{ ( provided, snapshot ) => (
						<div
							className={ `column-content ${
								snapshot.isDraggingOver ? 'dragging-over' : ''
							}` }
							ref={ provided.innerRef }
							{ ...provided.droppableProps }
						>
							{ columnWidgets && columnWidgets.length > 0 ? (
								columnWidgets.map( ( widget, index ) => (
									<Widget
										key={ widget.id }
										widget={ widget }
										widgets={ widgets }
										index={ index }
										onPropertyUpdate={ onWidgetPropertyUpdate }
									/>
								) )
							) : (
								<div className="empty-column">
									<p>No widgets in this column</p>
								</div>
							) }
							{ provided.placeholder }
						</div>
					) }
				</Droppable>
			</div>
		);
	}
}

export default Column;
