import React from 'react';
import Icon from '../Icon';

class TabularWidget extends React.Component {
	constructor( props ) {
		super( props );
		this.state = {
			loadingActions: {}, // Track loading state for each action.
			actionResults: {}, // Track success/error states.
			removedRows: {}, // Track rows that have been removed from UI.
		};
	}

	handleTableClick = ( table, tableIndex ) => {
		// Handle table click if needed.
	};

	handleRowClick = ( row, rowIndex, tableIndex ) => {
		// Handle row click if needed.
	};

	/**
	 * Handle action click with proper state management.
	 *
	 * @param {string} action Action type (sync, delete).
	 * @param {Object} row Row data.
	 * @param {number} rowIndex Row index.
	 * @param {number} tableIndex Table index.
	 */
	handleActionClick = async ( action, row, rowIndex, tableIndex ) => {
		const actionKey = `${ tableIndex }-${ rowIndex }-${ action }`;

		// Check if action is defined for this row.
		if ( ! this.isActionDefined( action, row ) ) {
			return;
		}

		// Set loading state.
		this.setState( ( prevState ) => ( {
			loadingActions: {
				...prevState.loadingActions,
				[ actionKey ]: true,
			},
		} ) );

		try {
			let actionCompleted = false;

			switch ( action ) {
				case 'delete':
					actionCompleted = await this.handleDeleteAction( row, rowIndex, tableIndex );
					break;
				case 'sync':
					await this.handleSyncAction( row, rowIndex, tableIndex );
					actionCompleted = true; // Sync actions don't have cancellation
					break;
				default:
					throw new Error( `Unknown action: ${ action }` );
			}

			// Only set success state if action was actually completed (not cancelled).
			if ( actionCompleted ) {
				this.setState( ( prevState ) => ( {
					actionResults: {
						...prevState.actionResults,
						[ actionKey ]: {
							success: true,
							message: `${ action } action completed successfully`,
						},
					},
				} ) );
			}
		} catch ( error ) {
			// Set error state.
			this.setState( ( prevState ) => ( {
				actionResults: {
					...prevState.actionResults,
					[ actionKey ]: {
						success: false,
						message: error.message || `Failed to ${ action }`,
					},
				},
			} ) );
		} finally {
			// Clear loading state.
			this.setState( ( prevState ) => ( {
				loadingActions: {
					...prevState.loadingActions,
					[ actionKey ]: false,
				},
			} ) );

			// Clear result after few seconds.
			setTimeout( () => {
				this.setState( ( prevState ) => {
					const newResults = { ...prevState.actionResults };
					delete newResults[ actionKey ];
					return { actionResults: newResults };
				} );
			}, 3000 );
		}
	};

	/**
	 * Extract ID from row data.
	 *
	 * @param {Object} row Row data.
	 * @return {string} Extracted ID.
	 */
	extractIdFromRow = ( row ) => {
		if ( ! row.cells || ! row.cells[ 0 ] || ! row.cells[ 0 ].text ) {
			throw new Error( 'Could not extract ID from row data' );
		}

		const cellText = row.cells[ 0 ].text;

		// Try to extract ID from HTML link.
		const linkMatch = cellText.match( /<a[^>]*>(\d+)<\/a>/ );
		if ( linkMatch ) {
			return linkMatch[ 1 ]; // Extract the number inside the link.
		}

		// Fallback: try to extract just the number.
		const numberMatch = cellText.match( /(\d+)/ );
		if ( numberMatch ) {
			return numberMatch[ 1 ];
		}

		throw new Error( 'Could not extract ID from row data.' );
	};

	/**
	 * Handle delete action with confirmation.
	 *
	 * @returns {boolean} True if action was completed successfully, false if cancelled.
	 */
	handleDeleteAction = async ( row, rowIndex, tableIndex ) => {
		// Get action configuration from row.
		const actionConfig = row.actions?.delete;
		if ( ! actionConfig || ! actionConfig.endpoint ) {
			throw new Error( 'Delete action not configured for this row.' );
		}

		// Check if confirmation is required
		if ( actionConfig.requires_confirmation ) {
			const confirmationMessage =
				actionConfig.message || 'Are you sure you want to delete this item?';
			const confirmed = window.confirm( confirmationMessage );
			if ( ! confirmed ) {
				// Return false to indicate action was cancelled.
				return false;
			}
		}

		// Extract ID from the first cell.
		const id = this.extractIdFromRow( row );

		// Build request data.
		const requestData = {
			id: id,
			action: 'delete',
			widget_id: this.props.widgetId,
			row_data: row,
			row_index: rowIndex,
			table_index: tableIndex,
			timestamp: new Date().toISOString(),
		};

		// Call external API endpoint.
		const response = await fetch( actionConfig.endpoint, {
			method: 'DELETE',
			headers: {
				'Content-Type': 'application/json',
			},
			body: JSON.stringify( requestData ),
		} );

		if ( ! response.ok ) {
			const errorData = await response
				.json()
				.catch( () => ( { message: 'Network error.' } ) );
			throw new Error( errorData.message || 'Failed to delete item.' );
		}

		const result = await response.json();

		// On successful deletion, trigger fade-out animation and remove row.
		this.handleSuccessfulDelete( rowIndex, tableIndex );

		// Return true to indicate successful completion.
		return true;
	};

	/**
	 * Handle successful delete action - simply remove row from UI.
	 *
	 * @param {number} rowIndex Row index.
	 * @param {number} tableIndex Table index.
	 */
	handleSuccessfulDelete = ( rowIndex, tableIndex ) => {
		const rowKey = `${ tableIndex }-${ rowIndex }`;

		// Mark row as removed to hide it from UI.
		this.setState( ( prevState ) => ( {
			removedRows: {
				...prevState.removedRows,
				[ rowKey ]: true,
			},
		} ) );
	};

	/**
	 * Check if a row has been removed.
	 *
	 * @param {number} rowIndex Row index.
	 * @param {number} tableIndex Table index.
	 * @returns {boolean} True if row has been removed.
	 */
	isRowRemoved = ( rowIndex, tableIndex ) => {
		const rowKey = `${ tableIndex }-${ rowIndex }`;
		return this.state.removedRows[ rowKey ] || false;
	};

	/**
	 * Handle sync action.
	 */
	handleSyncAction = async ( row, rowIndex, tableIndex ) => {
		// Get action configuration from row.
		const actionConfig = row.actions?.sync;

		if ( ! actionConfig || ! actionConfig.endpoint ) {
			throw new Error( 'Sync action not configured for this row.' );
		}

		// Extract ID from the first cell.
		const id = this.extractIdFromRow( row );

		// Build request data.
		const requestData = {
			id: id,
			widget_id: this.props.widgetId,
			row_data: row,
			row_index: rowIndex,
			table_index: tableIndex,
			timestamp: new Date().toISOString(),
		};

		// Call external API endpoint.
		const response = await fetch( actionConfig.endpoint, {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
			},
			body: JSON.stringify( requestData ),
		} );

		if ( ! response.ok ) {
			const errorData = await response.json().catch( () => ( { message: 'Network error' } ) );
			throw new Error( errorData.message || 'Sync failed' );
		}

		const result = await response.json();
	};

	/**
	 * Check if action is defined for the row.
	 */
	isActionDefined = ( action, row ) => {
		return row.actions && row.actions[ action ];
	};

	/**
	 * Check if a column should be treated as an actions column.
	 *
	 * @param {Object} cell Cell data.
	 * @param {Object} row Row data.
	 * @param {number} cellIndex Cell index.
	 * @param {Array} headers Table headers.
	 * @returns {boolean} True if this is an actions column.
	 */
	isActionsColumn = ( cell, row, cellIndex, headers = [] ) => {
		// Check if the row has actions defined.
		if ( ! row.actions || Object.keys( row.actions ).length === 0 ) {
			return false;
		}

		// Simple check: if the header text is "Actions", treat it as actions column.
		if ( headers[ cellIndex ] && headers[ cellIndex ].text ) {
			const headerText = headers[ cellIndex ].text.toLowerCase();
			if ( headerText === 'actions' || headerText === 'action' ) {
				return true;
			}
		}

		return false;
	};

	/**
	 * Check if action is loading.
	 */
	isActionLoading = ( action, rowIndex, tableIndex ) => {
		const actionKey = `${ tableIndex }-${ rowIndex }-${ action }`;
		return this.state.loadingActions[ actionKey ] || false;
	};

	/**
	 * Get action result.
	 */
	getActionResult = ( action, rowIndex, tableIndex ) => {
		const actionKey = `${ tableIndex }-${ rowIndex }-${ action }`;
		return this.state.actionResults[ actionKey ] || null;
	};

	/**
	 * Render action button with proper states.
	 */
	renderActionButton = ( action, row, rowIndex, tableIndex ) => {
		const isLoading = this.isActionLoading( action, rowIndex, tableIndex );
		const result = this.getActionResult( action, rowIndex, tableIndex );

		const buttonClass = `action-btn ${ action }-btn ${ isLoading ? 'loading' : '' } ${
			result ? ( result.success ? 'success' : 'error' ) : ''
		}`;
		const title = this.getActionTitle( action, row );

		return (
			<button
				className={ buttonClass }
				onClick={ ( e ) => {
					e.stopPropagation();
					if ( ! isLoading ) {
						this.handleActionClick( action, row, rowIndex, tableIndex );
					}
				} }
				title={ title }
				disabled={ isLoading }
			>
				{ isLoading ? (
					<span className="loading-spinner"></span>
				) : (
					this.getActionIcon( action )
				) }
				{ result && (
					<span
						className={ `action-result ${
							result.success ? 'result-success' : 'result-error'
						}` }
					>
						{ result.success ? '✓' : '✗' }
					</span>
				) }
			</button>
		);
	};

	/**
	 * Get action title.
	 */
	getActionTitle = ( action, row ) => {
		const actionConfig = row.actions && row.actions[ action ];

		if ( actionConfig && actionConfig.title ) {
			return actionConfig.title;
		}

		const titles = {
			delete: 'Delete',
			sync: 'Sync',
		};

		return titles[ action ] || action;
	};

	/**
	 * Get action icon.
	 */
	getActionIcon = ( action ) => {
		switch ( action ) {
			case 'delete':
				return <Icon name="delete_outline" size="medium" />;
			case 'sync':
				return <Icon name="sync" size="medium" />;
			default:
				return <Icon name="settings" size="medium" />;
		}
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
		// Get the order of actions from the row configuration.
		const actionOrder = row.actions ? Object.keys( row.actions ) : [];

		return (
			<div className="table-actions">
				{ actionOrder.map( ( action ) =>
					this.renderActionButton( action, row, rowIndex, tableIndex )
				) }
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
	 * @param {Array} headers Table headers.
	 * @returns {JSX.Element} Cell content.
	 */
	renderCell = ( cell, cellIndex, row, rowIndex, tableIndex, headers = [] ) => {
		// Check if this is an actions column by looking at the header text.
		const isActionsColumn = this.isActionsColumn( cell, row, cellIndex, headers );

		if ( isActionsColumn ) {
			// Render actions only if this is actually an actions column.
			return this.renderActions( row, rowIndex, tableIndex );
		}

		// Get the cell content.
		const cellContent = cell.text || cell.value || cell.content || '';

		// Check if the content contains HTML tags.
		const hasHtmlTags = /<[^>]*>/g.test( cellContent );

		if ( hasHtmlTags ) {
			// Render HTML content properly.
			return <span dangerouslySetInnerHTML={ { __html: cellContent } } />;
		}

		// Render plain text content.
		return cellContent;
	};

	/**
	 * Get cell class name.
	 *
	 * @param {number} cellIndex Cell index.
	 * @param {number} totalCells Total number of cells.
	 * @param {Array} headers Table headers.
	 * @param {Object} row Row data.
	 * @returns {string} Cell class name.
	 */
	getCellClassName = ( cellIndex, totalCells, headers = [], row = null ) => {
		const classes = [];

		// Add header-based classes.
		if ( headers[ cellIndex ] && headers[ cellIndex ].text ) {
			const headerText = headers[ cellIndex ].text.toLowerCase().replace( /\s+/g, '-' );
			classes.push( `column-${ headerText }` );
		}

		if ( cellIndex === 0 ) {
			classes.push( 'first-column' );
		}

		if ( cellIndex === totalCells - 1 ) {
			classes.push( 'last-column' );
		}

		// Only add action-column class if this is actually an actions column.
		if ( row && row.cells && row.cells[ cellIndex ] ) {
			const isActionsColumn = this.isActionsColumn(
				row.cells[ cellIndex ],
				row,
				cellIndex,
				headers
			);
			if ( isActionsColumn ) {
				classes.push( 'action-column' );
			}
		}

		return classes.join( ' ' );
	};

	/**
	 * Get header class name.
	 *
	 * @param {number} headerIndex Header index.
	 * @param {number} totalHeaders Total number of headers.
	 * @param {Array} headers Table headers.
	 * @param {Array} rows Table rows to check for actions.
	 * @returns {string} Header class name.
	 */
	getHeaderClassName = ( headerIndex, totalHeaders, headers = [], rows = [] ) => {
		const classes = [];

		// Add header-based classes.
		if ( headers[ headerIndex ] && headers[ headerIndex ].text ) {
			const headerText = headers[ headerIndex ].text.toLowerCase().replace( /\s+/g, '-' );
			classes.push( `column-${ headerText }` );
		}

		if ( headerIndex === 0 ) {
			classes.push( 'first-column' );
		}

		if ( headerIndex === totalHeaders - 1 ) {
			classes.push( 'last-column' );
		}

		// Only add action-column class if this header corresponds to an actions column.
		// Check if any row has actions and if the header text is "Actions".
		if ( rows.length > 0 && rows[ 0 ] && rows[ 0 ].cells && rows[ 0 ].cells[ headerIndex ] ) {
			const isActionsColumn = this.isActionsColumn(
				rows[ 0 ].cells[ headerIndex ],
				rows[ 0 ],
				headerIndex,
				headers
			);
			if ( isActionsColumn ) {
				classes.push( 'action-column' );
			}
		}

		return classes.join( ' ' );
	};

	render() {
		const { data, settings = {} } = this.props;
		const { tables } = data || {};
		const { showHeaders = true, stripedRows = true } = settings;

		return (
			<div className="tabular-widget">
				{ ( tables || [] ).map( ( table, tableIndex ) => {
					const hasRows = table.rows && table.rows.length > 0;
					const hasHeaders = table.headers && table.headers.length > 0;

					return hasRows ? (
						<div
							key={ tableIndex }
							className="tabular-table"
							onClick={ () => this.handleTableClick( table, tableIndex ) }
						>
							{ table.title && <h4 className="table-title">{ table.title }</h4> }
							<div className="table-container">
								<table className="striped">
									{ showHeaders && hasHeaders && (
										<thead>
											<tr>
												{ table.headers.map( ( header, index ) => (
													<th
														key={ index }
														className={ this.getHeaderClassName(
															index,
															table.headers.length,
															table.headers,
															table.rows
														) }
													>
														{ header.text }
													</th>
												) ) }
											</tr>
										</thead>
									) }
									<tbody>
										{ table.rows.map( ( row, rowIndex ) => {
											const isRemoved = this.isRowRemoved(
												rowIndex,
												tableIndex
											);

											// Skip rendering if row has been removed.
											if ( isRemoved ) {
												return null;
											}

											const rowClasses = [];

											if ( stripedRows && rowIndex % 2 === 1 ) {
												rowClasses.push( 'alternate' );
											}

											return (
												<tr
													key={ rowIndex }
													className={ rowClasses.join( ' ' ) }
													onClick={ () =>
														this.handleRowClick(
															row,
															rowIndex,
															tableIndex
														)
													}
												>
													{ ( row.cells || [] ).map(
														( cell, cellIndex ) => (
															<td
																key={ cellIndex }
																className={ this.getCellClassName(
																	cellIndex,
																	row.cells.length,
																	table.headers,
																	row
																) }
															>
																{ this.renderCell(
																	cell,
																	cellIndex,
																	row,
																	rowIndex,
																	tableIndex,
																	table.headers
																) }
															</td>
														)
													) }
												</tr>
											);
										} ) }
									</tbody>
								</table>
							</div>
						</div>
					) : null;
				} ) }
			</div>
		);
	}
}

export default TabularWidget;
