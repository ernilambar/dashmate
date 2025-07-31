import { createRoot } from 'react-dom/client';
import { __ } from '@wordpress/i18n';
import './css/main.css';

import { Component } from 'react';

class DashmateApp extends Component {
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
					'X-WP-Nonce': wpApiSettings?.nonce || '',
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
					'X-WP-Nonce': wpApiSettings?.nonce || '',
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
					'X-WP-Nonce': wpApiSettings.nonce,
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

	renderWidget( widget ) {
		const { widgets } = this.state;

		if ( ! widgets || ! widgets[ widget.type ] ) {
			return (
				<div key={ widget.id } className="widget widget-unknown">
					<h3>{ widget.title }</h3>
					<p>Unknown widget type: { widget.type }</p>
				</div>
			);
		}

		const widgetConfig = widgets[ widget.type ];

		return (
			<div key={ widget.id } className={ `widget widget-${ widget.type }` }>
				<div className="widget-header">
					<h3>{ widget.title }</h3>
					<div className="widget-actions">
						<button
							className="button button-small"
							onClick={ () => this.openWidgetSettings( widget ) }
						>
							Settings
						</button>
					</div>
				</div>
				<div className="widget-content">
					<WidgetContent widget={ widget } />
				</div>
			</div>
		);
	}

	openWidgetSettings( widget ) {
		// TODO: Implement widget settings modal
		console.log( 'Open settings for widget:', widget );
	}

	renderColumn( column ) {
		return (
			<div key={ column.id } className={ `dashboard-column column-${ column.width }` }>
				<div className="column-header">
					<h2>{ column.title }</h2>
				</div>
				<div className="column-content">
					{ column.widgets && column.widgets.length > 0 ? (
						column.widgets
							.sort( ( a, b ) => a.position - b.position )
							.map( ( widget ) => this.renderWidget( widget ) )
					) : (
						<div className="empty-column">
							<p>No widgets in this column</p>
						</div>
					) }
				</div>
			</div>
		);
	}

	render() {
		const { dashboard, loading, error } = this.state;

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

		return (
			<div className="dashmate-app">
				<div className="dashboard-header">
					<h1>{ __( 'Dashmate Dashboard', 'dashmate' ) }</h1>
				</div>
				<div className="dashboard-content">
					{ dashboard && dashboard.columns ? (
						dashboard.columns.map( ( column ) => this.renderColumn( column ) )
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

// Widget content component
class WidgetContent extends Component {
	constructor( props ) {
		super( props );
		this.state = {
			data: null,
			loading: true,
			error: null,
		};
	}

	componentDidMount() {
		this.loadWidgetData();
	}

	async loadWidgetData() {
		const { widget } = this.props;

		try {
			const response = await fetch( `/wp-json/dashmate/v1/widgets/${ widget.id }/data`, {
				headers: {
					'X-WP-Nonce': wpApiSettings?.nonce || '',
				},
			} );
			const data = await response.json();

			if ( data.success ) {
				this.setState( { data: data.data, loading: false } );
			} else {
				this.setState( { error: 'Failed to load widget data', loading: false } );
			}
		} catch ( error ) {
			this.setState( { error: 'Error loading widget data', loading: false } );
		}
	}

	renderChartWidget() {
		const { data } = this.state;
		const { widget } = this.props;

		if ( ! data || ! data.chart_data ) {
			return <p>No chart data available</p>;
		}

		return (
			<div className="chart-widget">
				<div className="chart-placeholder">
					<h4>Chart: { widget.title }</h4>
					<p>Chart type: { widget.settings?.chart_type || 'line' }</p>
					<p>Data points: { data.chart_data.datasets[ 0 ]?.data?.length || 0 }</p>
					{ data.summary && (
						<div className="chart-summary">
							<p>Total: { data.summary.total_sales }</p>
							<p>Growth: { data.summary.growth_rate }%</p>
						</div>
					) }
				</div>
			</div>
		);
	}

	renderMetricWidget() {
		const { data } = this.state;
		const { widget } = this.props;

		if ( ! data ) {
			return <p>No metric data available</p>;
		}

		return (
			<div className="metric-widget">
				<div className="metric-value">
					<span className="metric-current">{ data.current }</span>
					{ data.currency && <span className="metric-currency">{ data.currency }</span> }
				</div>
				{ data.change_percentage && (
					<div className={ `metric-change ${ data.trend }` }>
						{ data.change_percentage > 0 ? '+' : '' }{ data.change_percentage }%
					</div>
				) }
			</div>
		);
	}

	renderListWidget() {
		const { data } = this.state;
		const { widget } = this.props;

		if ( ! data || ! data.orders ) {
			return <p>No list data available</p>;
		}

		return (
			<div className="list-widget">
				<div className="list-items">
					{ data.orders.slice( 0, 5 ).map( ( order ) => (
						<div key={ order.id } className="list-item">
							<div className="item-main">
								<span className="item-id">{ order.id }</span>
								<span className="item-customer">{ order.customer }</span>
							</div>
							<div className="item-details">
								<span className="item-amount">${ order.amount }</span>
								<span className={ `item-status status-${ order.status }` }>
									{ order.status }
								</span>
							</div>
						</div>
					) ) }
				</div>
				{ data.summary && (
					<div className="list-summary">
						<p>Total Orders: { data.summary.total_orders }</p>
						<p>Total Revenue: ${ data.summary.total_revenue }</p>
					</div>
				) }
			</div>
		);
	}

	renderTableWidget() {
		const { data } = this.state;
		const { widget } = this.props;

		if ( ! data || ! data.orders ) {
			return <p>No table data available</p>;
		}

		return (
			<div className="table-widget">
				<table className="wp-list-table widefat fixed striped">
					<thead>
						<tr>
							<th>ID</th>
							<th>Customer</th>
							<th>Amount</th>
							<th>Status</th>
							<th>Date</th>
						</tr>
					</thead>
					<tbody>
						{ data.orders.slice( 0, 10 ).map( ( order ) => (
							<tr key={ order.id }>
								<td>{ order.id }</td>
								<td>{ order.customer }</td>
								<td>${ order.amount }</td>
								<td>
									<span className={ `status-${ order.status }` }>
										{ order.status }
									</span>
								</td>
								<td>{ new Date( order.date ).toLocaleDateString() }</td>
							</tr>
						) ) }
					</tbody>
				</table>
			</div>
		);
	}

	render() {
		const { loading, error } = this.state;
		const { widget } = this.props;

		if ( loading ) {
			return <div className="widget-loading">Loading...</div>;
		}

		if ( error ) {
			return <div className="widget-error">Error: { error }</div>;
		}

		switch ( widget.type ) {
			case 'chart':
				return this.renderChartWidget();
			case 'metric':
				return this.renderMetricWidget();
			case 'list':
				return this.renderListWidget();
			case 'table':
				return this.renderTableWidget();
			default:
				return <p>Unknown widget type: { widget.type }</p>;
		}
	}
}

document.addEventListener( 'DOMContentLoaded', () => {
	const domContainer = document.getElementById( 'dashmate-app' );

	if ( domContainer ) {
		const root = createRoot( domContainer );
		root.render( <DashmateApp /> );
	}
} );
