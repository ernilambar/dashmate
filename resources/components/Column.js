import { Component } from 'react';
import Widget from './Widget';

class Column extends Component {
	render() {
		const { column, widgets } = this.props;

		return (
			<div className={ `dashboard-column column-${ column.width }` }>
				<div className="column-content">
					{ column.widgets && column.widgets.length > 0 ? (
						column.widgets
							.sort( ( a, b ) => a.position - b.position )
							.map( ( widget ) => (
								<Widget key={ widget.id } widget={ widget } widgets={ widgets } />
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
