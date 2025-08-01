import React, { Component } from 'react';
import HtmlWidget from './widgets/HtmlWidget';
// import IconboxWidget from './widgets/IconboxWidget'; // For reference
import ProgressCirclesWidget from './widgets/ProgressCirclesWidget';
import Links from './widgets/Links';
// import TabularWidget from './widgets/TabularWidget'; // For reference

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
		// If widgetData is provided as prop, use it; otherwise fetch data
		if ( this.props.widgetData ) {
			this.setState( { data: this.props.widgetData, loading: false } );
		} else {
			this.loadWidgetData();
		}
	}

	componentDidUpdate( prevProps ) {
		// Check if widgetData prop changed (when settings are saved and content is refetched)
		if ( prevProps.widgetData !== this.props.widgetData ) {
			console.log( 'WidgetContent: widgetData updated', this.props.widgetData );
			if ( this.props.widgetData ) {
				this.setState( { data: this.props.widgetData, loading: false } );
			}
		}
	}

	async loadWidgetData() {
		const { widget } = this.props;

		try {
			const response = await fetch(
				`${ dashmateApiSettings.restUrl }widgets/${ widget.id }/data`,
				{
					headers: {
						'X-WP-Nonce': dashmateApiSettings?.nonce || '',
					},
				}
			);
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
			case 'links':
				return <Links widgetId={ widget.id } data={ data } />;
			case 'progress-circles':
				return <ProgressCirclesWidget widgetId={ widget.id } data={ data } />;
			// case 'iconbox':
			// 	return <IconboxWidget widgetId={ widget.id } data={ data } />;
			// case 'tabular':
			// 	return <TabularWidget widgetId={ widget.id } data={ data } />;
			default:
				return <p>Unknown widget type: { widget.type }</p>;
		}
	}
}

export default WidgetContent;
