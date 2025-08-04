import React from 'react';

class TabularWidget extends React.Component {
	constructor( props ) {
		super( props );
		this.state = {
			loadingActions: {}, // Track loading state for each action
			actionResults: {}, // Track success/error states
		};
	}

	handleTableClick = ( table, tableIndex ) => {
		// Handle table click if needed
	};

	handleRowClick = ( row, rowIndex, tableIndex ) => {
		// Handle row click if needed
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

		// Check if action is defined for this row
		if ( ! this.isActionDefined( action, row ) ) {
			return;
		}

		// Set loading state
		this.setState( ( prevState ) => ( {
			loadingActions: {
				...prevState.loadingActions,
				[ actionKey ]: true,
			},
		} ) );

		try {
			// Handle different action types
			switch ( action ) {
				case 'delete':
					await this.handleDeleteAction( row, rowIndex, tableIndex );
					break;
				case 'sync':
					await this.handleSyncAction( row, rowIndex, tableIndex );
					break;
				default:
					throw new Error( `Unknown action: ${ action }` );
			}

			// Set success state
			this.setState( ( prevState ) => ( {
				actionResults: {
					...prevState.actionResults,
					[ actionKey ]: {
						success: true,
						message: `${ action } action completed successfully`,
					},
				},
			} ) );
		} catch ( error ) {
			// Set error state
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
			// Clear loading state
			this.setState( ( prevState ) => ( {
				loadingActions: {
					...prevState.loadingActions,
					[ actionKey ]: false,
				},
			} ) );

			// Clear result after 3 seconds
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

		// Try to extract ID from HTML link
		const linkMatch = cellText.match( /<a[^>]*>(\d+)<\/a>/ );
		if ( linkMatch ) {
			return linkMatch[ 1 ]; // Extract the number inside the link
		}

		// Fallback: try to extract just the number
		const numberMatch = cellText.match( /(\d+)/ );
		if ( numberMatch ) {
			return numberMatch[ 1 ];
		}

		throw new Error( 'Could not extract ID from row data' );
	};

	/**
	 * Handle delete action with confirmation.
	 */
	handleDeleteAction = async ( row, rowIndex, tableIndex ) => {
		// Get action configuration from row
		const actionConfig = row.actions?.delete;
		if ( ! actionConfig || ! actionConfig.endpoint ) {
			throw new Error( 'Delete action not configured for this row' );
		}

		// Check if confirmation is required
		if ( actionConfig.requires_confirmation ) {
			const confirmationMessage =
				actionConfig.message || 'Are you sure you want to delete this item?';
			const confirmed = window.confirm( confirmationMessage );
			if ( ! confirmed ) {
				throw new Error( 'Action cancelled by user' );
			}
		}

		// Extract ID from the first cell
		const id = this.extractIdFromRow( row );

		// Build request data
		const requestData = {
			id: id,
			action: 'delete',
			widget_id: this.props.widgetId,
			row_data: row,
			row_index: rowIndex,
			table_index: tableIndex,
			timestamp: new Date().toISOString(),
		};

		// Call external API endpoint
		const response = await fetch( actionConfig.endpoint, {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
			},
			body: JSON.stringify( requestData ),
		} );

		if ( ! response.ok ) {
			const errorData = await response.json().catch( () => ( { message: 'Network error' } ) );
			throw new Error( errorData.message || 'Failed to delete item' );
		}

		const result = await response.json();
	};

	/**
	 * Handle sync action.
	 */
	handleSyncAction = async ( row, rowIndex, tableIndex ) => {
		// Get action configuration from row
		const actionConfig = row.actions?.sync;
		if ( ! actionConfig || ! actionConfig.endpoint ) {
			throw new Error( 'Sync action not configured for this row' );
		}

		// Extract ID from the first cell
		const id = this.extractIdFromRow( row );

		// Build request data
		const requestData = {
			id: id,
			widget_id: this.props.widgetId,
			row_data: row,
			row_index: rowIndex,
			table_index: tableIndex,
			timestamp: new Date().toISOString(),
		};

		// Call external API endpoint
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
					<span className={ `action-result ${ result.success ? 'success' : 'error' }` }>
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
				return (
					<svg
						width="16"
						height="16"
						viewBox="0 0 24 24"
						fill="none"
						stroke="currentColor"
						strokeWidth="2"
					>
						<polyline points="3,6 5,6 21,6" />
						<path d="m19,6v14a2,2 0 0,1 -2,2H7a2,2 0 0,1 -2,-2V6m3,0V4a2,2 0 0,1 2,-2h4a2,2 0 0,1 2,2v2" />
					</svg>
				);
			case 'sync':
				return (
					<svg
						width="16"
						height="16"
						viewBox="0 0 24 24"
						fill="none"
						stroke="currentColor"
						strokeWidth="2"
					>
						<path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8" />
						<path d="M21 3v5h-5" />
						<path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16" />
						<path d="M3 21v-5h5" />
					</svg>
				);
			default:
				return <span className="dashicons dashicons-admin-generic"></span>;
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
		return (
			<div className="table-actions">
				{ this.isActionDefined( 'delete', row ) &&
					this.renderActionButton( 'delete', row, rowIndex, tableIndex ) }
				{ this.isActionDefined( 'sync', row ) &&
					this.renderActionButton( 'sync', row, rowIndex, tableIndex ) }
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

	/**
	 * Get cell class name.
	 *
	 * @param {number} cellIndex Cell index.
	 * @param {number} totalCells Total number of cells.
	 * @param {Array} headers Table headers.
	 * @returns {string} Cell class name.
	 */
	getCellClassName = ( cellIndex, totalCells, headers = [] ) => {
		const classes = [];

		// Add header-based classes
		if ( headers[ cellIndex ] && headers[ cellIndex ].text ) {
			const headerText = headers[ cellIndex ].text.toLowerCase().replace( /\s+/g, '-' );
			classes.push( `column-${ headerText }` );
		}

		if ( cellIndex === 0 ) {
			classes.push( 'first-column' );
		}

		if ( cellIndex === totalCells - 1 ) {
			classes.push( 'action-column', 'last-column' );
		}

		return classes.join( ' ' );
	};

	/**
	 * Get header class name.
	 *
	 * @param {number} headerIndex Header index.
	 * @param {number} totalHeaders Total number of headers.
	 * @param {Array} headers Table headers.
	 * @returns {string} Header class name.
	 */
	getHeaderClassName = ( headerIndex, totalHeaders, headers = [] ) => {
		const classes = [];

		// Add header-based classes
		if ( headers[ headerIndex ] && headers[ headerIndex ].text ) {
			const headerText = headers[ headerIndex ].text.toLowerCase().replace( /\s+/g, '-' );
			classes.push( `column-${ headerText }` );
		}

		if ( headerIndex === 0 ) {
			classes.push( 'first-column' );
		}

		if ( headerIndex === totalHeaders - 1 ) {
			classes.push( 'action-column', 'last-column' );
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

					return (
						<div
							key={ tableIndex }
							className="tabular-table"
							onClick={ () => this.handleTableClick( table, tableIndex ) }
						>
							{ table.title && <h4 className="table-title">{ table.title }</h4> }
							{ hasRows ? (
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
																table.headers
															) }
														>
															{ header.text }
														</th>
													) ) }
												</tr>
											</thead>
										) }
										<tbody>
											{ table.rows.map( ( row, rowIndex ) => (
												<tr
													key={ rowIndex }
													className={
														stripedRows && rowIndex % 2 === 1
															? 'alternate'
															: ''
													}
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
																	table.headers
																) }
															>
																{ this.renderCell(
																	cell,
																	cellIndex,
																	row,
																	rowIndex,
																	tableIndex
																) }
															</td>
														)
													) }
												</tr>
											) ) }
										</tbody>
									</table>
								</div>
							) : (
								<div className="empty-container">
									<p>No data available</p>
								</div>
							) }
						</div>
					);
				} ) }
			</div>
		);
	}
}

export default TabularWidget;
