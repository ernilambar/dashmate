import { Component } from 'react';
import { __ } from '@wordpress/i18n';
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
			const response = await fetch( '/wp-json/dashmate/v1/dashboard', {
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
			const response = await fetch( '/wp-json/dashmate/v1/widgets', {
				headers: {
					'X-WP-Nonce': dashmateApiSettings?.nonce || '',
				},
			} );
			const data = await response.json();

			if ( data.success ) {
				this.setState( { widgets: data.data } );
			}
		} catch ( error ) {
			console.error( 'Error loading widgets:', error );
		}
	}

	async saveDashboard( dashboardData ) {
		try {
			const response = await fetch( '/wp-json/dashmate/v1/dashboard', {
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
				console.error( 'Failed to save dashboard:', data );
				return false;
			}
		} catch ( error ) {
			console.error( 'Error saving dashboard:', error );
			return false;
		}
	}

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

		// Get columns from the new structure
		const columns = dashboard?.layout?.columns || [];
		const allWidgets = dashboard?.widgets || [];

		return (
			<div className="dashmate-app">
				<div className="dashboard-header">
					<h1>{ __( 'Dashmate Dashboard', 'dashmate' ) }</h1>
				</div>
				<div className="dashboard-content">
					{ columns.length > 0 ? (
						columns.map( ( column ) => {
							// Get widgets for this column
							const columnWidgets = allWidgets
								.filter( ( widget ) => widget.column_id === column.id )
								.sort( ( a, b ) => ( a.position || 0 ) - ( b.position || 0 ) );

							return (
								<Column
									key={ column.id }
									column={ column }
									widgets={ widgets }
									columnWidgets={ columnWidgets }
								/>
							);
						} )
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
