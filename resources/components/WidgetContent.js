import React, { Component } from 'react';
import HtmlWidget from './widgets/HtmlWidget';
import ProgressCirclesWidget from './widgets/ProgressCirclesWidget';
import LinksWidget from './widgets/LinksWidget';
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
		// If widgetData is provided as prop, use it; otherwise fetch data.
		if ( this.props.widgetData ) {
			this.setState( { data: this.props.widgetData, loading: false } );
		} else {
			this.loadWidgetData();
		}
	}

	componentDidUpdate( prevProps ) {
		// Check if widgetData prop changed (when settings are saved and content is refetched).
		if ( prevProps.widgetData !== this.props.widgetData ) {
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

	handleSettingsChange = ( newSettings, needsRefresh = false ) => {
		// Propagate settings changes to parent Widget component.
		if ( this.props.onSettingsChange ) {
			this.props.onSettingsChange( newSettings, needsRefresh );
		}
	};

	render() {
		const { loading, error, data } = this.state;
		const { widget, settings = {} } = this.props;

		if ( loading ) {
			return <div className="widget-loading">Loading...</div>;
		}

		if ( error ) {
			return <div className="widget-error">Error: { error }</div>;
		}

		// Route to appropriate widget component.
		switch ( widget.type ) {
			case 'html':
				return <HtmlWidget widgetId={ widget.id } data={ data } settings={ settings } />;
			case 'links':
				return (
					<LinksWidget
						widgetId={ widget.id }
						data={ data }
						settings={ settings }
						onSettingsChange={ this.handleSettingsChange }
					/>
				);
			case 'progress-circles':
				return (
					<ProgressCirclesWidget
						widgetId={ widget.id }
						data={ data }
						settings={ settings }
					/>
				);
			case 'tabular':
				return <TabularWidget widgetId={ widget.id } data={ data } settings={ settings } />;
			default:
				return <p>Unknown widget type: { widget.type }</p>;
		}
	}
}

export default WidgetContent;
