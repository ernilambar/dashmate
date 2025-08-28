import React from 'react';
import Icon from '../Icon';
import Mustache from 'mustache';

class TabularWidget extends React.Component {
	constructor( props ) {
		super( props );
		this.state = {
			loadingActions: {}, // Track loading state for each action.
			actionResults: {}, // Track success/error states.
			removedRows: {}, // Track rows that have been removed from UI.
			expandedRows: {}, // Track which rows are expanded.
			childRowData: {}, // Store child row HTML content.
			loadingChildRows: {}, // Track loading state for child rows.
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
	 * @param {string} action Action type.
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

			actionCompleted = await this.handleGenericAction( action, row, rowIndex, tableIndex );

			// Only set success state if action was actually completed (not cancelled).
			if ( actionCompleted ) {
				this.setState( ( prevState ) => ( {
					actionResults: {
						...prevState.actionResults,
						[ actionKey ]: {
							success: true,
							message: `${ action } action completed successfully.`,
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
	 * Generic action handler that uses configuration from PHP.
	 *
	 * @param {string} action Action type.
	 * @param {Object} row Row data.
	 * @param {number} rowIndex Row index.
	 * @param {number} tableIndex Table index.
	 * @returns {boolean} True if action was completed successfully, false if cancelled.
	 */
	handleGenericAction = async ( action, row, rowIndex, tableIndex ) => {
		// Get action configuration from row.
		const actionConfig = row.actions?.[ action ];
		if ( ! actionConfig || ! actionConfig.endpoint ) {
			throw new Error( `${ action } action not configured for this row.` );
		}

		// Check if confirmation is required.
		if ( actionConfig.requires_confirmation ) {
			const confirmationMessage = actionConfig.confirmation_text || 'Are you sure?';
			const confirmed = window.confirm( confirmationMessage );
			if ( ! confirmed ) {
				// Return false to indicate action was cancelled.
				return false;
			}
		}

		// Extract ID from the first cell.
		const id = this.extractIdFromRow( row );

		// Build request data.
		let requestData = {
			id: id,
			action: action,
			widget_id: this.props.widgetId,
			row_data: row,
			row_index: rowIndex,
			table_index: tableIndex,
			timestamp: new Date().toISOString(),
		};

		// Always merge extra_data if provided in action configuration.
		if ( actionConfig.extra_data ) {
			Object.assign( requestData, actionConfig.extra_data );
		}

		// For admin-ajax.php, override action with the one from extra_data if available.
		if ( actionConfig.endpoint && actionConfig.endpoint.includes( 'admin-ajax.php' ) ) {
			if ( actionConfig.extra_data?.action ) {
				requestData.action = actionConfig.extra_data.action;
			}
		}

		// Determine HTTP method from configuration.
		const method = actionConfig.methods || 'POST';

		// Check if we should use form data (for WordPress admin-ajax.php compatibility).
		const useFormData =
			actionConfig.endpoint && actionConfig.endpoint.includes( 'admin-ajax.php' );

		// Prepare fetch options.
		const fetchOptions = {
			method: method,
			headers: {},
		};

		// Add body for non-GET requests.
		if ( method !== 'GET' ) {
			if ( useFormData ) {
				// Use form data for WordPress admin-ajax.php.
				fetchOptions.headers[ 'Content-Type' ] = 'application/x-www-form-urlencoded';
				fetchOptions.body = new URLSearchParams( requestData );
			} else {
				// Use JSON for other endpoints.
				fetchOptions.headers[ 'Content-Type' ] = 'application/json';
				fetchOptions.body = JSON.stringify( requestData );
			}
		}

		// Call external API endpoint.
		const response = await fetch( actionConfig.endpoint, fetchOptions );

		if ( ! response.ok ) {
			const errorData = await response
				.json()
				.catch( () => ( { message: 'Network error.' } ) );
			throw new Error( errorData.message || `Failed to ${ action } item.` );
		}

		const result = await response.json();

		// Handle special case for delete action - remove row from UI.
		if ( action === 'delete' ) {
			this.handleSuccessfulDelete( rowIndex, tableIndex );
		}

		// Return true to indicate successful completion.
		return true;
	};

	/**
	 * Handle expand/collapse child row.
	 *
	 * @param {Object} row Row data.
	 * @param {number} rowIndex Row index.
	 * @param {number} tableIndex Table index.
	 */
	handleExpandRow = async ( row, rowIndex, tableIndex ) => {
		const rowKey = `${ tableIndex }-${ rowIndex }`;
		const isCurrentlyExpanded = this.state.expandedRows[ rowKey ];

		if ( isCurrentlyExpanded ) {
			// Collapse the row.
			this.setState( ( prevState ) => ( {
				expandedRows: {
					...prevState.expandedRows,
					[ rowKey ]: false,
				},
			} ) );
		} else {
			// Expand the row and fetch child data.
			this.setState( ( prevState ) => ( {
				expandedRows: {
					...prevState.expandedRows,
					[ rowKey ]: true,
				},
				loadingChildRows: {
					...prevState.loadingChildRows,
					[ rowKey ]: true,
				},
			} ) );

			try {
				// Fetch child row data from third-party API.
				const childData = await this.fetchChildRowData( row, rowIndex, tableIndex );

				this.setState( ( prevState ) => ( {
					childRowData: {
						...prevState.childRowData,
						[ rowKey ]: childData,
					},
				} ) );
			} catch ( error ) {
				console.error( 'Failed to fetch child row data:', error );
				// Set error state in child row data.
				this.setState( ( prevState ) => ( {
					childRowData: {
						...prevState.childRowData,
						[ rowKey ]: {
							error: true,
							message: error.message || 'Failed to load child row data.',
						},
					},
				} ) );
			} finally {
				// Clear loading state.
				this.setState( ( prevState ) => ( {
					loadingChildRows: {
						...prevState.loadingChildRows,
						[ rowKey ]: false,
					},
				} ) );
			}
		}
	};

	/**
	 * Fetch child row data from third-party API.
	 *
	 * @param {Object} row Row data.
	 * @param {number} rowIndex Row index.
	 * @param {number} tableIndex Table index.
	 * @returns {Promise<Object>} Child row data.
	 */
	fetchChildRowData = async ( row, rowIndex, tableIndex ) => {
		// Extract ID from the row for the API call.
		const id = this.extractIdFromRow( row );

		// Get API endpoint from settings.
		const { data } = this.props;
		const { tabular_settings = {} } = data || {};
		const { child_row_api_endpoint = '', child_row_html_template = '' } = tabular_settings;

		// Check if API endpoint is configured.
		if ( ! child_row_api_endpoint ) {
			throw new Error( 'API endpoint not configured for child rows.' );
		}

		// Construct the full API endpoint by appending the ID.
		const apiEndpoint = `${ child_row_api_endpoint }/${ id }`;

		const response = await fetch( apiEndpoint );

		if ( ! response.ok ) {
			throw new Error( `Failed to fetch child data: ${ response.statusText }` );
		}

		const responseData = await response.json();

		// Generate HTML content using the provided template.
		const html = this.generateChildRowHtml( responseData, id, child_row_html_template );

		return { html };
	};

	/**
	 * Generate HTML content for child row using provided template.
	 *
	 * @param {Object} data API response data.
	 * @param {string} id Row ID.
	 * @param {string} htmlTemplate HTML template string.
	 * @returns {string} Generated HTML.
	 */
	generateChildRowHtml = ( data, id, htmlTemplate ) => {
		if ( ! htmlTemplate ) {
			// Return empty content if no template provided.
			return '<div class="child-row-content"><p>No template configured.</p></div>';
		}

		try {
			// Pass data with id included.
			const templateData = { ...data, id };

			// Render template with Mustache.
			return Mustache.render( htmlTemplate, templateData );
		} catch ( error ) {
			console.error( 'Template compilation error:', error );
			return '<div class="child-row-content"><p>Template error. Please check your template syntax.</p></div>';
		}
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
	 * Check if a row is expanded.
	 *
	 * @param {number} rowIndex Row index.
	 * @param {number} tableIndex Table index.
	 * @returns {boolean} True if row is expanded.
	 */
	isRowExpanded = ( rowIndex, tableIndex ) => {
		const rowKey = `${ tableIndex }-${ rowIndex }`;
		return this.state.expandedRows[ rowKey ] || false;
	};

	/**
	 * Check if child row is loading.
	 *
	 * @param {number} rowIndex Row index.
	 * @param {number} tableIndex Table index.
	 * @returns {boolean} True if child row is loading.
	 */
	isChildRowLoading = ( rowIndex, tableIndex ) => {
		const rowKey = `${ tableIndex }-${ rowIndex }`;
		return this.state.loadingChildRows[ rowKey ] || false;
	};

	/**
	 * Get child row data.
	 *
	 * @param {number} rowIndex Row index.
	 * @param {number} tableIndex Table index.
	 * @returns {Object|null} Child row data.
	 */
	getChildRowData = ( rowIndex, tableIndex ) => {
		const rowKey = `${ tableIndex }-${ rowIndex }`;
		return this.state.childRowData[ rowKey ] || null;
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
					this.getActionIcon( action, row )
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
	 * Get action title from configuration.
	 */
	getActionTitle = ( action, row ) => {
		const actionConfig = row.actions && row.actions[ action ];

		if ( actionConfig && actionConfig.title ) {
			return actionConfig.title;
		}

		// Fallback to action name if no title configured.
		return action;
	};

	/**
	 * Get action icon from configuration.
	 */
	getActionIcon = ( action, row ) => {
		const actionConfig = row.actions && row.actions[ action ];

		if ( actionConfig && actionConfig.icon ) {
			return <Icon name={ actionConfig.icon } size="medium" />;
		}

		// Fallback to default icon if no icon configured.
		return <Icon name="settings" size="medium" />;
	};

	/**
	 * Render expand/collapse icon.
	 *
	 * @param {Object} row Row data.
	 * @param {number} rowIndex Row index.
	 * @param {number} tableIndex Table index.
	 * @returns {JSX.Element} Expand/collapse icon.
	 */
	renderExpandIcon = ( row, rowIndex, tableIndex ) => {
		const isExpanded = this.isRowExpanded( rowIndex, tableIndex );
		const isLoading = this.isChildRowLoading( rowIndex, tableIndex );

		let iconName = 'expand_more';
		if ( isExpanded ) {
			iconName = 'expand_less';
		}

		return (
			<button
				className="expand-row-btn"
				onClick={ ( e ) => {
					e.stopPropagation();
					this.handleExpandRow( row, rowIndex, tableIndex );
				} }
				title={ isExpanded ? 'Collapse Details' : 'Expand Details' }
				disabled={ isLoading }
			>
				{ isLoading ? (
					<span className="loading-spinner small"></span>
				) : (
					<Icon name={ iconName } size="small" />
				) }
			</button>
		);
	};

	/**
	 * Render child row content.
	 *
	 * @param {Object} row Row data.
	 * @param {number} rowIndex Row index.
	 * @param {number} tableIndex Table index.
	 * @returns {JSX.Element|null} Child row content.
	 */
	renderChildRow = ( row, rowIndex, tableIndex ) => {
		const isExpanded = this.isRowExpanded( rowIndex, tableIndex );
		const isLoading = this.isChildRowLoading( rowIndex, tableIndex );
		const childData = this.getChildRowData( rowIndex, tableIndex );

		if ( ! isExpanded ) {
			return null;
		}

		if ( isLoading ) {
			return (
				<tr className="child-row loading">
					<td colSpan="100%">
						<div className="child-row-loading">
							<span className="loading-spinner"></span>
							<span>Loading...</span>
						</div>
					</td>
				</tr>
			);
		}

		if ( childData && childData.error ) {
			return (
				<tr className="child-row error">
					<td colSpan="100%">
						<div className="child-row-error">
							<Icon name="error" size="small" />
							<span>{ childData.message }</span>
						</div>
					</td>
				</tr>
			);
		}

		if ( childData && childData.html ) {
			return (
				<tr className="child-row">
					<td colSpan="100%">
						<div
							className="child-row-content"
							dangerouslySetInnerHTML={ { __html: childData.html } }
						/>
					</td>
				</tr>
			);
		}

		return null;
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
				{ actionOrder.map( ( action ) => (
					<div key={ action }>
						{ this.renderActionButton( action, row, rowIndex, tableIndex ) }
					</div>
				) ) }
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

		// For the first column, add expand icon if child rows are enabled.
		if ( cellIndex === 0 ) {
			const { data } = this.props;
			const { tabular_settings = {} } = data || {};
			const { enable_child_rows = false } = tabular_settings;

			if ( enable_child_rows ) {
				return (
					<div className="cell-with-expand">
						{ this.renderExpandIcon( row, rowIndex, tableIndex ) }
						<span className="cell-content">
							{ hasHtmlTags ? (
								<span dangerouslySetInnerHTML={ { __html: cellContent } } />
							) : (
								cellContent
							) }
						</span>
					</div>
				);
			}
		}

		// Render other columns normally.
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

	/**
	 * Extract ID from row data.
	 *
	 * @param {Object} row Row data.
	 * @return {string} Extracted ID.
	 */
	extractIdFromRow = ( row ) => {
		if ( ! row.cells || ! row.cells[ 0 ] ) {
			throw new Error( 'Could not extract ID from row data.' );
		}

		const firstCell = row.cells[ 0 ];

		if ( firstCell.text ) {
			// Try to extract ID from HTML link text (e.g., <a>#001</a>).
			const linkMatch = firstCell.text.match( /<a[^>]*>#?(\d+)<\/a>/ );
			if ( linkMatch ) {
				return parseInt( linkMatch[ 1 ], 10 ).toString(); // Convert to number and back to remove leading zeros.
			}

			// Fallback: try to extract just the number (with or without #).
			const numberMatch = firstCell.text.match( /#?(\d+)/ );
			if ( numberMatch ) {
				return parseInt( numberMatch[ 1 ], 10 ).toString(); // Convert to number and back to remove leading zeros.
			}

			// Fallback: try to get ID from data-id attribute in the HTML link.
			const dataIdMatch = firstCell.text.match( /data-id="(\d+)"/ );
			if ( dataIdMatch ) {
				return parseInt( dataIdMatch[ 1 ], 10 ).toString(); // Convert to number and back to remove leading zeros.
			}
		}

		// If all else fails, throw an error.
		throw new Error(
			'Could not extract ID from row data. Please check the first cell content.'
		);
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

	render() {
		const { data, settings = {} } = this.props;
		const { tables, tabular_settings = {} } = data || {};
		const { showHeaders = true, stripedRows = true } = settings;
		const { enable_child_rows = true } = tabular_settings;

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
												<React.Fragment key={ rowIndex }>
													<tr
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
													{ this.renderChildRow(
														row,
														rowIndex,
														tableIndex
													) }
												</React.Fragment>
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
