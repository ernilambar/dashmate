import React from 'react';

class TabularWidget extends React.Component {
	constructor( props ) {
		super( props );
		this.state = {
			// No hover state needed for now
		};
	}

	handleTableClick = ( table, tableIndex ) => {
		// Handle table click if needed
	};

	handleRowClick = ( row, rowIndex, tableIndex ) => {
		// Handle row click if needed
	};

	handleActionClick = ( action, row, rowIndex, tableIndex ) => {
		// Handle action click if needed
	};

	/**
	 * Render action icons.
	 *
	 * @param {Object} row Row data.
	 * @param {number} rowIndex Row index.
	 * @param {number} tableIndex Table index.
	 * @returns {JSX.Element} Action icons.
	 */
	renderActions = ( row, rowIndex, tableIndex ) => {
		return (
			<div className="table-actions">
				<button
					className="action-btn view-btn"
					onClick={ ( e ) => {
						e.stopPropagation();
						this.handleActionClick( 'view', row, rowIndex, tableIndex );
					} }
					title="View"
				>
					<svg
						width="16"
						height="16"
						viewBox="0 0 24 24"
						fill="none"
						stroke="currentColor"
						strokeWidth="2"
					>
						<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
						<circle cx="12" cy="12" r="3" />
					</svg>
				</button>
				<button
					className="action-btn sync-btn"
					onClick={ ( e ) => {
						e.stopPropagation();
						this.handleActionClick( 'sync', row, rowIndex, tableIndex );
					} }
					title="Sync"
				>
					<span className="dashicons dashicons-update"></span>
				</button>
			</div>
		);
	};

	/**
	 * Render cell content with special handling for actions column.
	 *
	 * @param {Object} cell Cell data.
	 * @param {number} cellIndex Cell index.
	 * @param {Object} row Row data.
	 * @param {number} rowIndex Row index.
	 * @param {number} tableIndex Table index.
	 * @returns {JSX.Element} Cell content.
	 */
	renderCell = ( cell, cellIndex, row, rowIndex, tableIndex ) => {
		// Check if this is the last column for actions
		if ( cellIndex === row.cells.length - 1 ) {
			// Last column - render actions
			return this.renderActions( row, rowIndex, tableIndex );
		}

		// Get the cell content
		const cellContent = cell.text || cell.value || cell.content || '';

		// Check if the content contains HTML tags
		const hasHtmlTags = /<[^>]*>/g.test( cellContent );

		if ( hasHtmlTags ) {
			// Render HTML content properly
			return <span dangerouslySetInnerHTML={ { __html: cellContent } } />;
		}

		// Render plain text content
		return cellContent;
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
												<td
													key={ cellIndex }
													className={
														cellIndex === 0 ? 'first-column' : ''
													}
												>
													{ this.renderCell(
														cell,
														cellIndex,
														row,
														rowIndex,
														tableIndex
													) }
												</td>
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
