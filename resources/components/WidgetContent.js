import React, { Component } from 'react';
import HtmlWidget from './widgets/HtmlWidget';
import IconboxWidget from './widgets/IconboxWidget';
import ProgressCircleWidget from './widgets/ProgressCircleWidget';
import QuickLinksWidget from './widgets/QuickLinksWidget';
import TabularWidget from './widgets/TabularWidget';

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

	render() {
		const { loading, error, data } = this.state;
		const { widget } = this.props;

		if ( loading ) {
			return <div className="widget-loading">Loading...</div>;
		}

		if ( error ) {
			return <div className="widget-error">Error: { error }</div>;
		}

		// Route to appropriate widget component
		switch ( widget.type ) {
			case 'html':
				return <HtmlWidget widgetId={ widget.id } data={ data } />;
			case 'iconbox':
				return <IconboxWidget widgetId={ widget.id } data={ data } />;
			case 'progress-circle':
				return <ProgressCircleWidget widgetId={ widget.id } data={ data } />;
			case 'quick-links':
				return <QuickLinksWidget widgetId={ widget.id } data={ data } />;
			case 'tabular':
				return <TabularWidget widgetId={ widget.id } data={ data } />;
			default:
				return <p>Unknown widget type: { widget.type }</p>;
		}
	}
}

export default WidgetContent;
