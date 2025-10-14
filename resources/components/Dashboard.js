import { Component } from 'react';
import { __ } from '@wordpress/i18n';
import Column from './Column';
import LayoutSettings from './LayoutSettings';
import Icon from './Icon';

class Dashboard extends Component {
	constructor( props ) {
		super( props );
		this.state = {
			dashboard: null,
			widgets: {},
			loading: true,
			error: null,
			showLayoutSettings: false,
		};
	}

	componentDidMount() {
		this.loadDashboard();
		this.loadWidgets();
	}

	async loadDashboard() {
		try {
			const dashboardId = this.props.dashboardId || 'main';
			const url = `${ dashmateApiSettings.restUrl }dashboards/${ dashboardId }`;

			const response = await fetch( url, {
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
			const dashboardId = this.props.dashboardId || 'main';
			const url = `${ dashmateApiSettings.restUrl }dashboards/${ dashboardId }`;

			const response = await fetch( url, {
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
			const dashboardId = this.props.dashboardId || 'main';
			const url = `${ dashmateApiSettings.restUrl }dashboards/${ dashboardId }`;

			const response = await fetch( url, {
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
			const dashboardId = this.props.dashboardId || 'main';
			const url = `${ dashmateApiSettings.restUrl }dashboards/${ dashboardId }`;

			const response = await fetch( url, {
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

	handleLayoutSettingsToggle = () => {
		this.setState( ( prevState ) => ( {
			showLayoutSettings: ! prevState.showLayoutSettings,
		} ) );
	};

	handleLayoutSettingsClose = () => {
		this.setState( { showLayoutSettings: false } );
	};

	render() {
		const { dashboard, widgets, loading, error, showLayoutSettings } = this.state;

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

		// Get max columns from localStorage, fallback to 2 if not set
		const dashboardId = this.props.dashboardId || 'main';
		const storageKey = `dashmate_layout_settings_${ dashboardId }`;
		const maxColumns = parseInt(
			localStorage.getItem( storageKey )
				? JSON.parse( localStorage.getItem( storageKey ) ).max_columns
				: '2',
			10
		);
		const gridColumns = Math.min( maxColumns, columns.length );
		const gridClass = `grid-layout grid-${ gridColumns }`;
		const dashboardContentClass = `dashboard-content ${ gridClass }`;

		return (
			<div className="dashmate-app">
				{ /* Dashboard Controls */ }
				<div className="dashboard-controls">
					<button
						type="button"
						className="layout-settings-button"
						onClick={ this.handleLayoutSettingsToggle }
						title="Layout Settings"
					>
						<Icon name="equalizer-fill" size="xl" />
					</button>
				</div>

				{ showLayoutSettings && (
					<div className="layout-settings-modal">
						<LayoutSettings
							onClose={ this.handleLayoutSettingsClose }
							dashboardId={ this.props.dashboardId }
						/>
					</div>
				) }

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
										onWidgetPropertyUpdate={ this.handleWidgetPropertyUpdate }
										onWidgetRemove={ this.handleWidgetRemove }
										dashboardId={ this.props.dashboardId }
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
			</div>
		);
	}
}

export default Dashboard;
