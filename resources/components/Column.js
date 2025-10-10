import { Component } from 'react';
import Widget from './Widget';

class Column extends Component {
	render() {
		const {
			column,
			widgets,
			columnWidgets,
			onWidgetPropertyUpdate,
			onWidgetRemove,
			dashboardId,
		} = this.props;

		return (
			<div className="dashboard-column">
				<div className="column-content">
					{ columnWidgets && columnWidgets.length > 0 ? (
						columnWidgets.map( ( widget, index ) => (
							<Widget
								key={ widget.id }
								widget={ widget }
								widgets={ widgets }
								index={ index }
								onPropertyUpdate={ onWidgetPropertyUpdate }
								onRemove={ onWidgetRemove }
								dashboardId={ dashboardId }
							/>
						) )
					) : (
						<div className="empty-column">
							<p>No widgets in this column</p>
						</div>
					) }
				</div>
			</div>
		);
	}
}

export default Column;
