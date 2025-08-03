import { Component } from 'react';
import { __ } from '@wordpress/i18n';
import { DragDropContext, Droppable } from '@hello-pangea/dnd';
import Column from './Column';

class Dashboard extends Component {
	constructor( props ) {
		super( props );
		this.state = {
			dashboard: null,
			widgets: null,
			loading: true,
			error: null,
		};
	}

	componentDidMount() {
		this.loadDashboard();
		this.loadWidgets();
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
		const updatedColumnWidgets = { ...updatedDashboard.column_widgets };

		// Remove widget from source column
		if ( updatedColumnWidgets[ source.droppableId ] ) {
			updatedColumnWidgets[ source.droppableId ] = updatedColumnWidgets[
				source.droppableId
			].filter( ( widgetId ) => widgetId !== draggableId );
		}

		// Add widget to destination column at the correct position
		if ( ! updatedColumnWidgets[ destination.droppableId ] ) {
			updatedColumnWidgets[ destination.droppableId ] = [];
		}

		updatedColumnWidgets[ destination.droppableId ].splice( destination.index, 0, draggableId );

		// Update the column_widgets in dashboard
		updatedDashboard.column_widgets = updatedColumnWidgets;

		// Update state immediately for UI responsiveness
		this.setState( { dashboard: updatedDashboard } );

		// Save the new order to the server using column_widgets structure
		try {
			const response = await fetch( `${ dashmateApiSettings.restUrl }dashboard/reorder`, {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					'X-WP-Nonce': dashmateApiSettings?.nonce || '',
				},
				body: JSON.stringify( {
					column_widgets: updatedColumnWidgets,
				} ),
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

		// Get columns from the new structure with proper type checking
		const columns = Array.isArray( dashboard?.layout?.columns ) ? dashboard.layout.columns : [];
		const allWidgets = Array.isArray( dashboard?.widgets ) ? dashboard.widgets : [];
		const columnWidgets =
			dashboard?.column_widgets && typeof dashboard.column_widgets === 'object'
				? dashboard.column_widgets
				: {};

		// Determine if we should use grid layout (more than max columns)
		const maxColumns = window.dashmateApiSettings?.config?.maxColumns || 2;
		const shouldUseGridLayout = columns.length > maxColumns;
		const gridClass = shouldUseGridLayout ? `grid-layout grid-${ maxColumns }` : '';
		const dashboardContentClass = `dashboard-content${ gridClass ? ' ' + gridClass : '' }`;

		return (
			<div className="dashmate-app">
				<DragDropContext onDragEnd={ this.handleDragEnd }>
					<div className={ dashboardContentClass }>
						{ columns.length > 0 ? (
							columns
								.map( ( column ) => {
									// Ensure column is an object with an id
									if ( ! column || typeof column !== 'object' || ! column.id ) {
										return null;
									}

									// Get widgets for this column using column_widgets structure with robust error handling
									const columnWidgetIds = Array.isArray(
										columnWidgets[ column.id ]
									)
										? columnWidgets[ column.id ]
										: [];
									const columnWidgetsList = columnWidgetIds
										.map( ( widgetId ) => {
											// Ensure widgetId is a string
											if ( typeof widgetId !== 'string' ) {
												return null;
											}

											// Ensure allWidgets is an array before calling find
											if ( ! Array.isArray( allWidgets ) ) {
												return null;
											}
											return allWidgets.find(
												( widget ) => widget && widget.id === widgetId
											);
										} )
										.filter( Boolean );

									return (
										<Column
											key={ column.id }
											column={ column }
											widgets={ widgets }
											columnWidgets={ columnWidgetsList }
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
