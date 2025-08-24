import { Component } from 'react';
import { __ } from '@wordpress/i18n';
import { DragDropContext, Droppable } from '@hello-pangea/dnd';
import Column from './Column';
import WidgetSelector from './WidgetSelector';
import LayoutSaver from './LayoutSaver';
import LayoutSelector from './LayoutSelector';

class Dashboard extends Component {
	constructor( props ) {
		super( props );
		this.state = {
			dashboard: null,
			widgets: null,
			layouts: null,
			loading: true,
			error: null,
		};
	}

	componentDidMount() {
		this.loadDashboard();
		this.loadWidgets();
		this.loadLayouts();
	}

	async loadDashboard() {
		try {
			const response = await fetch( `${ dashmateApiSettings.restUrl }dashboard`, {
				headers: {
					'X-WP-Nonce': dashmateApiSettings?.nonce || '',
				},
			} );
			const data = await response.json();

			if ( data.success ) {
				this.setState( { dashboard: data.data, loading: false } );
			} else {
				this.setState( { error: 'Failed to load dashboard', loading: false } );
			}
		} catch ( error ) {
			this.setState( { error: 'Error loading dashboard', loading: false } );
		}
	}

	async loadWidgets() {
		try {
			const response = await fetch( `${ dashmateApiSettings.restUrl }widgets`, {
				headers: {
					'X-WP-Nonce': dashmateApiSettings?.nonce || '',
				},
			} );
			const data = await response.json();

			if ( data.success ) {
				this.setState( { widgets: data.data } );
			}
		} catch ( error ) {
			// Handle error silently
		}
	}

	async loadLayouts() {
		try {
			const response = await fetch( `${ dashmateApiSettings.restUrl }layouts`, {
				headers: {
					'X-WP-Nonce': dashmateApiSettings?.nonce || '',
				},
			} );
			const data = await response.json();

			if ( data.success ) {
				this.setState( { layouts: data.data } );
			}
		} catch ( error ) {
			// Handle error silently
		}
	}

	async saveDashboard( dashboardData ) {
		try {
			const response = await fetch( `${ dashmateApiSettings.restUrl }dashboard`, {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					'X-WP-Nonce': dashmateApiSettings?.nonce || '',
				},
				body: JSON.stringify( dashboardData ),
			} );

			const data = await response.json();

			if ( data.success ) {
				this.setState( { dashboard: data.data } );
				return true;
			} else {
				return false;
			}
		} catch ( error ) {
			return false;
		}
	}

	handleDragEnd = async ( result ) => {
		const { source, destination, draggableId } = result;

		// If dropped outside a droppable area or no movement
		if (
			! destination ||
			( source.droppableId === destination.droppableId && source.index === destination.index )
		) {
			return;
		}

		const { dashboard } = this.state;
		if ( ! dashboard ) {
			return;
		}

		// Create a copy of the dashboard data
		const updatedDashboard = { ...dashboard };

		// Find source and destination columns
		const sourceColumn = updatedDashboard.columns.find(
			( col ) => col.id === source.droppableId
		);
		const destColumn = updatedDashboard.columns.find(
			( col ) => col.id === destination.droppableId
		);

		if ( ! sourceColumn || ! destColumn ) {
			return;
		}

		// Find the widget to move
		const widgetToMove = sourceColumn.widgets.find( ( widget ) => widget.id === draggableId );
		if ( ! widgetToMove ) {
			return;
		}

		// Remove widget from source column
		sourceColumn.widgets = sourceColumn.widgets.filter(
			( widget ) => widget.id !== draggableId
		);

		// Add widget to destination column at the correct position
		destColumn.widgets.splice( destination.index, 0, widgetToMove );

		// Update state immediately for UI responsiveness
		this.setState( { dashboard: updatedDashboard } );

		// Save the new order to the server
		try {
			const response = await fetch( `${ dashmateApiSettings.restUrl }dashboard`, {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					'X-WP-Nonce': dashmateApiSettings?.nonce || '',
				},
				body: JSON.stringify( updatedDashboard ),
			} );

			const data = await response.json();

			if ( ! data.success ) {
				// Reload dashboard to revert changes
				this.loadDashboard();
			}
		} catch ( error ) {
			// Reload dashboard to revert changes
			this.loadDashboard();
		}
	};

	handleWidgetSelect = async ( widgetId ) => {
		const { dashboard } = this.state;
		if ( ! dashboard || ! dashboard.columns || dashboard.columns.length === 0 ) {
			return;
		}

		// Get the first column
		const firstColumn = dashboard.columns[ 0 ];
		if ( ! firstColumn ) {
			return;
		}

		// Create a copy of the dashboard data
		const updatedDashboard = { ...dashboard };

		// Initialize widgets array if it doesn't exist
		if ( ! firstColumn.widgets ) {
			firstColumn.widgets = [];
		}

		// Add the widget to the end of the first column
		firstColumn.widgets.push( {
			id: widgetId,
			settings: {},
			collapsed: false,
		} );

		// Update state immediately for UI responsiveness
		this.setState( { dashboard: updatedDashboard } );

		// Save the new layout to the server
		try {
			const response = await fetch( `${ dashmateApiSettings.restUrl }dashboard`, {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					'X-WP-Nonce': dashmateApiSettings?.nonce || '',
				},
				body: JSON.stringify( updatedDashboard ),
			} );

			const data = await response.json();

			if ( ! data.success ) {
				// Reload dashboard to revert changes
				this.loadDashboard();
			}
		} catch ( error ) {
			console.error( 'Error adding widget:', error );
			// Reload dashboard to revert changes
			this.loadDashboard();
		}
	};

	handleWidgetPropertyUpdate = async ( widgetId, properties ) => {
		const { dashboard } = this.state;
		if ( ! dashboard ) {
			return;
		}

		// Create a copy of the dashboard data.
		const updatedDashboard = { ...dashboard };

		// Find and update the widget.
		let widgetFound = false;
		for ( const column of updatedDashboard.columns ) {
			if ( column.widgets ) {
				for ( const widget of column.widgets ) {
					if ( widget.id === widgetId ) {
						// Update widget properties.
						Object.assign( widget, properties );
						widgetFound = true;
						break;
					}
				}
				if ( widgetFound ) {
					break;
				}
			}
		}

		if ( ! widgetFound ) {
			return;
		}

		// Update state immediately for UI responsiveness.
		this.setState( { dashboard: updatedDashboard } );

		// Save the updated dashboard to the server.
		try {
			const response = await fetch( `${ dashmateApiSettings.restUrl }dashboard`, {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					'X-WP-Nonce': dashmateApiSettings?.nonce || '',
				},
				body: JSON.stringify( updatedDashboard ),
			} );

			const data = await response.json();

			if ( ! data.success ) {
				// Reload dashboard to revert changes.
				this.loadDashboard();
			}
		} catch ( error ) {
			// Reload dashboard to revert changes.
			this.loadDashboard();
		}
	};

	handleWidgetRemove = async ( widgetId ) => {
		const { dashboard } = this.state;
		if ( ! dashboard ) {
			return;
		}

		// Create a copy of the dashboard data.
		const updatedDashboard = { ...dashboard };

		// Find and remove the widget.
		let widgetFound = false;
		for ( const column of updatedDashboard.columns ) {
			if ( column.widgets ) {
				const widgetIndex = column.widgets.findIndex(
					( widget ) => widget.id === widgetId
				);
				if ( widgetIndex !== -1 ) {
					// Remove widget from the column.
					column.widgets.splice( widgetIndex, 1 );
					widgetFound = true;
					break;
				}
			}
		}

		if ( ! widgetFound ) {
			return;
		}

		// Update state immediately for UI responsiveness.
		this.setState( { dashboard: updatedDashboard } );

		// Save the updated dashboard to the server.
		try {
			const response = await fetch( `${ dashmateApiSettings.restUrl }dashboard`, {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					'X-WP-Nonce': dashmateApiSettings?.nonce || '',
				},
				body: JSON.stringify( updatedDashboard ),
			} );

			const data = await response.json();

			if ( ! data.success ) {
				// Reload dashboard to revert changes.
				this.loadDashboard();
			}
		} catch ( error ) {
			// Reload dashboard to revert changes.
			this.loadDashboard();
		}
	};

	handleLayoutSaved = () => {
		// Reload layouts to include the new one
		this.loadLayouts();
	};

	handleLayoutSelect = async ( layoutKey ) => {
		if ( layoutKey === 'current' ) {
			return; // Don't apply current layout
		}

		try {
			// Apply the selected layout
			const response = await fetch(
				`${ dashmateApiSettings.restUrl }layouts/${ layoutKey }/apply`,
				{
					method: 'POST',
					headers: {
						'Content-Type': 'application/json',
						'X-WP-Nonce': dashmateApiSettings?.nonce || '',
					},
				}
			);

			const data = await response.json();

			if ( data.success ) {
				// Reload the dashboard with the new layout
				this.loadDashboard();
			} else {
				console.error( 'Failed to apply layout:', data.message );
			}
		} catch ( error ) {
			console.error( 'Error applying layout:', error );
		}
	};

	render() {
		const { dashboard, widgets, loading, error } = this.state;

		if ( loading ) {
			return (
				<div className="dashmate-app">
					<div className="loading">
						<p>Loading dashboard...</p>
					</div>
				</div>
			);
		}

		if ( error ) {
			return (
				<div className="dashmate-app">
					<div className="error">
						<p>Error: { error }</p>
					</div>
				</div>
			);
		}

		// Ensure dashboard is an object
		if ( ! dashboard || typeof dashboard !== 'object' ) {
			return (
				<div className="dashmate-app">
					<div className="error">
						<p>Invalid dashboard data</p>
					</div>
				</div>
			);
		}

		// Get columns from the dashboard structure
		const columns = Array.isArray( dashboard?.columns ) ? dashboard.columns : [];

		// Always use grid layout with appropriate number of columns
		const maxColumns = window.dashmateApiSettings?.config?.maxColumns;
		const gridColumns = maxColumns ? Math.min( maxColumns, columns.length ) : columns.length;
		const gridClass = `grid-layout grid-${ gridColumns }`;
		const dashboardContentClass = `dashboard-content ${ gridClass }`;

		return (
			<div className="dashmate-app">
				{ /* Dashboard Controls */ }
				<div className="dashboard-controls">
					<WidgetSelector
						widgets={ widgets }
						dashboard={ dashboard }
						onWidgetSelect={ this.handleWidgetSelect }
					/>
					<LayoutSaver dashboard={ dashboard } onLayoutSaved={ this.handleLayoutSaved } />
					<LayoutSelector onLayoutSelect={ this.handleLayoutSelect } />
				</div>

				<DragDropContext onDragEnd={ this.handleDragEnd }>
					<div className={ dashboardContentClass }>
						{ columns.length > 0 ? (
							columns
								.map( ( column ) => {
									// Ensure column is an object with an id
									if ( ! column || typeof column !== 'object' || ! column.id ) {
										return null;
									}

									// Get widgets for this column directly from the column
									const columnWidgets = Array.isArray( column.widgets )
										? column.widgets
										: [];

									return (
										<Column
											key={ column.id }
											column={ column }
											widgets={ widgets }
											columnWidgets={ columnWidgets }
											onWidgetPropertyUpdate={
												this.handleWidgetPropertyUpdate
											}
											onWidgetRemove={ this.handleWidgetRemove }
										/>
									);
								} )
								.filter( Boolean ) // Remove any null columns
						) : (
							<div className="empty-dashboard">
								<p>No dashboard columns found</p>
							</div>
						) }
					</div>
				</DragDropContext>
			</div>
		);
	}
}

export default Dashboard;
