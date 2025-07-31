import { Component } from 'react';

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
					'X-WP-Nonce': dashmateApiSettings?.nonce || '',
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

	renderHtmlWidget() {
		const { widget } = this.props;
		const settings = widget.settings || {};
		const htmlContent = settings.html_content || '<p>No HTML content provided</p>';

		return (
			<div className="html-widget">
				<div className="html-content" dangerouslySetInnerHTML={ { __html: htmlContent } } />
			</div>
		);
	}

	renderIconboxWidget() {
		const { widget } = this.props;
		const settings = widget.settings || {};

		return (
			<div className={ `iconbox-widget iconbox-${ settings.color || 'blue' }` }>
				<div className="iconbox-icon">
					<span className={ settings.icon || 'dashicons-admin-users' }></span>
				</div>
				<div className="iconbox-content">
					<h4 className="iconbox-title">{ settings.title || 'Title' }</h4>
					<p className="iconbox-subtitle">{ settings.subtitle || 'Subtitle' }</p>
				</div>
			</div>
		);
	}

	renderProgressCircleWidget() {
		const { widget } = this.props;
		const settings = widget.settings || {};
		const percentage = settings.percentage || 0;
		const label = settings.label || `${ percentage }%`;
		const caption = settings.caption || 'Progress';
		const color = settings.color || 'blue';

		// Calculate circle dimensions
		const radius = 60;
		const circumference = 2 * Math.PI * radius;
		const strokeDasharray = circumference;
		const strokeDashoffset = circumference - ( percentage / 100 ) * circumference;

		return (
			<div className={ `progress-circle-widget progress-circle-${ color }` }>
				<div className="progress-circle-container">
					<svg className="progress-circle" width="150" height="150">
						<circle
							className="progress-circle-bg"
							cx="75"
							cy="75"
							r={ radius }
							fill="none"
							stroke="#e1e1e1"
							strokeWidth="8"
						/>
						<circle
							className="progress-circle-fill"
							cx="75"
							cy="75"
							r={ radius }
							fill="none"
							strokeWidth="8"
							strokeDasharray={ strokeDasharray }
							strokeDashoffset={ strokeDashoffset }
							transform="rotate(-90 75 75)"
						/>
					</svg>
					<div className="progress-circle-label">{ label }</div>
				</div>
				<div className="progress-circle-caption">{ caption }</div>
			</div>
		);
	}

	renderQuickLinksWidget() {
		const { widget } = this.props;
		const settings = widget.settings || {};
		const links = settings.links || [];

		return (
			<div className="quick-links-widget">
				<div className="quick-links-list">
					{ links.map( ( link, index ) => (
						<a
							key={ index }
							href={ link.url }
							className="quick-link-item"
							target="_blank"
							rel="noopener noreferrer"
						>
							<span
								className={ `quick-link-icon ${
									link.icon || 'dashicons-admin-links'
								}` }
							></span>
							<span className="quick-link-title">{ link.title }</span>
						</a>
					) ) }
				</div>
			</div>
		);
	}

	renderTableWidget() {
		const { widget } = this.props;
		const settings = widget.settings || {};
		const headers = settings.headers || [];
		const rows = settings.rows || [];

		return (
			<div className="table-widget">
				{ settings.title && <h4 className="table-title">{ settings.title }</h4> }
				<table className="wp-list-table widefat fixed striped">
					<thead>
						<tr>
							{ headers.map( ( header, index ) => (
								<th key={ index }>{ header.text }</th>
							) ) }
						</tr>
					</thead>
					<tbody>
						{ rows.map( ( row, rowIndex ) => (
							<tr key={ rowIndex }>
								{ row.cells.map( ( cell, cellIndex ) => (
									<td key={ cellIndex }>{ cell.text }</td>
								) ) }
							</tr>
						) ) }
					</tbody>
				</table>
			</div>
		);
	}

	renderTabularWidget() {
		const { widget } = this.props;
		const settings = widget.settings || {};
		const tables = settings.tables || [];

		return (
			<div className="tabular-widget">
				{ tables.map( ( table, tableIndex ) => (
					<div key={ tableIndex } className="tabular-table">
						{ table.title && <h4 className="table-title">{ table.title }</h4> }
						<table className="wp-list-table widefat fixed striped">
							<thead>
								<tr>
									{ table.headers.map( ( header, index ) => (
										<th key={ index }>{ header.text }</th>
									) ) }
								</tr>
							</thead>
							<tbody>
								{ table.rows.map( ( row, rowIndex ) => (
									<tr key={ rowIndex }>
										{ row.cells.map( ( cell, cellIndex ) => (
											<td key={ cellIndex }>{ cell.text }</td>
										) ) }
									</tr>
								) ) }
							</tbody>
						</table>
					</div>
				) ) }
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
			case 'html':
				return this.renderHtmlWidget();
			case 'iconbox':
				return this.renderIconboxWidget();
			case 'progress-circle':
				return this.renderProgressCircleWidget();
			case 'quick-links':
				return this.renderQuickLinksWidget();
			case 'table':
				return this.renderTableWidget();
			case 'tabular':
				return this.renderTabularWidget();
			default:
				return <p>Unknown widget type: { widget.type }</p>;
		}
	}
}

export default WidgetContent;
