import React from 'react';
import Icon from '../Icon';
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

		// Check if settings prop changed (when settings are updated from parent)
		if ( prevProps.settings !== this.props.settings ) {
			this.setState( { settings: this.props.settings || {} } );
		}

		// Check if data prop changed (when settings are saved and content is refetched)
		if ( prevProps.data !== this.props.data ) {
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
				// Handle error silently or log to server
			}
		} catch ( error ) {
			// Handle error silently or log to server
		}
	};

	handleLinkClick = ( link ) => {
		window.open( link.url, '_blank' );
	};

	handleSettingsChange = ( newSettings, needsRefresh = false ) => {
		// Update local state immediately for instant feedback
		this.setState( { settings: { ...this.state.settings, ...newSettings } } );

		// Propagate changes to parent component if onChange prop is provided
		if ( this.props.onSettingsChange ) {
			this.props.onSettingsChange( newSettings, needsRefresh );
		}
	};

	render() {
		const { settings, widgetSchemas } = this.state;
		const { data } = this.props; // Use data from props instead of state
		const schema = widgetSchemas?.[ 'links' ]?.settings_schema;

		// Get links from data and settings from widget settings
		const links = data?.links || [];
		const linkStyle = settings?.display_style || 'list';
		const hideIcon = settings?.hide_icon || false;

		// Show loading if data is not available yet
		if ( ! data ) {
			return <div>Loading...</div>;
		}

		// Ensure links is always an array
		const safeLinks = Array.isArray( links ) ? links : [];

		return (
			<div className="quick-links-widget">
				<div className={ `quick-links-${ linkStyle }` }>
					{ safeLinks.map( ( link, index ) => (
						<div
							key={ index }
							className="quick-link-item"
							onClick={ () => this.handleLinkClick( link ) }
						>
							{ ! hideIcon && link.icon && (
								<Icon name={ link.icon } size="small" className="quick-link-icon" />
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
