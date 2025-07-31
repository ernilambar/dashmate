import React from 'react';
import WidgetSettingsForm from '../WidgetSettingsForm';

class QuickLinksWidget extends React.Component {
	constructor( props ) {
		super( props );
		this.state = {
			showSettings: false,
			settings: props.settings || {},
			data: null,
			loading: true,
			widgetSchemas: null,
		};
	}

	componentDidMount() {
		this.fetchWidgetSchemas();
		this.fetchWidgetData();
	}

	componentDidUpdate( prevProps ) {
		if ( prevProps.widgetId !== this.props.widgetId ) {
			this.fetchWidgetData();
		}
	}

	fetchWidgetSchemas = async () => {
		try {
			const response = await fetch( '/wp-json/dashmate/v1/widgets' );
			const result = await response.json();

			if ( result.success ) {
				this.setState( { widgetSchemas: result.data } );
			} else {
				console.error( 'Failed to fetch widget schemas:', result );
			}
		} catch ( error ) {
			console.error( 'Error fetching widget schemas:', error );
		}
	};

	fetchWidgetData = async () => {
		try {
			this.setState( { loading: true } );

			// Use POST to send settings with the request
			const response = await fetch(
				`/wp-json/dashmate/v1/widgets/content/${ this.props.widgetId }`,
				{
					method: 'POST',
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify( {
						settings: this.state.settings,
					} ),
				}
			);
			const result = await response.json();

			if ( result.success ) {
				this.setState( { data: result.data, loading: false } );
			} else {
				console.error( 'Failed to fetch widget data:', result );
				this.setState( { loading: false } );
			}
		} catch ( error ) {
			console.error( 'Error fetching widget data:', error );
			this.setState( { loading: false } );
		}
	};

	handleLinkClick = ( link ) => {
		console.log( 'QuickLinksWidget link clicked:', link );
		window.open( link.url, '_blank' );
	};

	handleSettingsChange = ( newSettings ) => {
		this.setState( { settings: newSettings } );
		// Here you could also save settings to backend
	};

	render() {
		const { settings, data, loading, widgetSchemas } = this.state;
		const schema = widgetSchemas?.[ 'links' ]?.settings_schema;
		const { title, links } = data || {};
		const { hideIcon = false, linkStyle = 'list' } = settings;

		if ( loading ) {
			return <div>Loading...</div>;
		}

		// Ensure links is always an array
		const safeLinks = Array.isArray( links ) ? links : [];

		return (
			<div className="quick-links-widget">
				<div className={ `quick-links-list quick-links-${ linkStyle }` }>
					{ safeLinks.map( ( link, index ) => (
						<div
							key={ index }
							className="quick-link-item"
							onClick={ () => this.handleLinkClick( link ) }
						>
							{ ! hideIcon && (
								<span
									className={ `quick-link-icon dashicons ${
										link.icon || 'dashicons-admin-links'
									}` }
								></span>
							) }
							<span className="quick-link-title">{ link.title }</span>
						</div>
					) ) }
				</div>
			</div>
		);
	}
}

export default QuickLinksWidget;
