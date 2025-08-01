import React from 'react';
import WidgetSettingsForm from '../WidgetSettingsForm';

class Links extends React.Component {
	constructor( props ) {
		super( props );
		this.state = {
			showSettings: false,
			settings: props.settings || {},
			widgetSchemas: null,
		};
	}

	componentDidMount() {
		this.fetchWidgetSchemas();
	}

	componentDidUpdate( prevProps ) {
		// Check if widgetId changed
		if ( prevProps.widgetId !== this.props.widgetId ) {
			// Widget ID changed, but data will be fetched by parent component
		}

		// Check if data prop changed (when settings are saved and content is refetched)
		if ( prevProps.data !== this.props.data ) {
			console.log( 'QuickLinksWidget: Data updated', this.props.data );
			// Data has been updated by parent component, no need to update state
			// as we'll use props.data directly in render
		}
	}

	fetchWidgetSchemas = async () => {
		try {
			const response = await fetch( `${ dashmateApiSettings.restUrl }widgets` );
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

	handleLinkClick = ( link ) => {
		console.log( 'QuickLinksWidget link clicked:', link );
		window.open( link.url, '_blank' );
	};

	handleSettingsChange = ( newSettings ) => {
		this.setState( { settings: newSettings } );
		// Here you could also save settings to backend
	};

	render() {
		const { settings, widgetSchemas } = this.state;
		const { data } = this.props; // Use data from props instead of state
		const schema = widgetSchemas?.[ 'links' ]?.settings_schema;

		// Handle nested data structure: data.links contains {links, linkStyle, hideIcon}
		const linksData = data?.links || {};
		const { links, linkStyle, hideIcon } = linksData;

		// Show loading if data is not available yet
		if ( ! data ) {
			return <div>Loading...</div>;
		}

		// Ensure links is always an array
		const safeLinks = Array.isArray( links ) ? links : [];

		return (
			<div className="quick-links-widget">
				<div className={ `quick-links-list quick-links-${ linkStyle || 'list' }` }>
					{ safeLinks.map( ( link, index ) => (
						<div
							key={ index }
							className="quick-link-item"
							onClick={ () => this.handleLinkClick( link ) }
						>
							{ ! hideIcon && link.icon && (
								<span
									className={ `quick-link-icon dashicons ${ link.icon }` }
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

export default Links;
