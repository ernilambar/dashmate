import React from 'react';

class TabularWidget extends React.Component {
	constructor( props ) {
		super( props );
		this.state = {
			// No hover state needed for now
		};
	}

	handleTableClick = ( table, tableIndex ) => {
		console.log( 'TabularWidget table clicked:', table, tableIndex );
	};

	handleRowClick = ( row, rowIndex, tableIndex ) => {
		console.log( 'TabularWidget row clicked:', row, rowIndex, tableIndex );
	};

	render() {
		const { data, settings = {} } = this.props;
		const { tables } = data || {};
		const { showHeaders = true, stripedRows = true } = settings;

		return (
			<div className="tabular-widget">
				{ ( tables || [] ).map( ( table, tableIndex ) => (
					<div
						key={ tableIndex }
						className="tabular-table"
						onClick={ () => this.handleTableClick( table, tableIndex ) }
					>
						{ table.title && <h4 className="table-title">{ table.title }</h4> }
						<div className="table-container">
							<table className="wp-list-table widefat fixed striped">
								{ showHeaders && table.headers && table.headers.length > 0 && (
									<thead>
										<tr>
											{ table.headers.map( ( header, index ) => (
												<th key={ index }>{ header.text }</th>
											) ) }
										</tr>
									</thead>
								) }
								<tbody>
									{ ( table.rows || [] ).map( ( row, rowIndex ) => (
										<tr
											key={ rowIndex }
											className={
												stripedRows && rowIndex % 2 === 1 ? 'alternate' : ''
											}
											onClick={ () =>
												this.handleRowClick( row, rowIndex, tableIndex )
											}
										>
											{ ( row.cells || [] ).map( ( cell, cellIndex ) => (
												<td key={ cellIndex }>{ cell.text }</td>
											) ) }
										</tr>
									) ) }
								</tbody>
							</table>
						</div>
					</div>
				) ) }
			</div>
		);
	}
}

export default TabularWidget;
