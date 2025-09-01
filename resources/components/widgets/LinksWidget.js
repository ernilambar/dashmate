import React from 'react';
import Icon from '../Icon';

class LinksWidget extends React.Component {
	constructor( props ) {
		super( props );
		this.state = {
			showSettings: false,
			settings: props.settings || {},
			widgetSchemas: null,
		};
	}

	componentDidMount() {
		// Only fetch schemas if we don't have them from props.
		if ( ! this.props.widgetSchemas ) {
			// Wait for dashmateApiSettings to be available before making the request.
			if ( typeof dashmateApiSettings !== 'undefined' && dashmateApiSettings?.nonce ) {
				this.fetchWidgetSchemas();
			} else {
				// Retry after a short delay if settings are not available yet.
				setTimeout( () => {
					if (
						typeof dashmateApiSettings !== 'undefined' &&
						dashmateApiSettings?.nonce
					) {
						this.fetchWidgetSchemas();
					} else {
						console.error(
							'Dashmate: LinksWidget - dashmateApiSettings not available'
						);
					}
				}, 100 );
			}
		}
	}

	componentDidUpdate( prevProps ) {
		// Check if settings prop changed (when settings are updated from parent).
		if ( prevProps.settings !== this.props.settings ) {
			this.setState( { settings: this.props.settings || {} } );
		}
	}

	fetchWidgetSchemas = async () => {
		// Check if required settings are available.
		if (
			typeof dashmateApiSettings === 'undefined' ||
			! dashmateApiSettings?.nonce ||
			! dashmateApiSettings?.restUrl
		) {
			return;
		}

		try {
			const response = await fetch( `${ dashmateApiSettings.restUrl }widgets`, {
				headers: {
					'X-WP-Nonce': dashmateApiSettings?.nonce || '',
				},
			} );
			const result = await response.json();

			if ( result.success ) {
				this.setState( { widgetSchemas: result.data } );
			}
		} catch ( error ) {
			// Handle error silently.
		}
	};

	handleLinkClick = ( link ) => {
		window.open( link.url, '_blank' );
	};

	handleSettingsChange = ( newSettings, needsRefresh = false ) => {
		// Update local state immediately for instant feedback.
		this.setState( { settings: { ...this.state.settings, ...newSettings } } );

		// Propagate changes to parent component if onChange prop is provided.
		if ( this.props.onSettingsChange ) {
			this.props.onSettingsChange( newSettings, needsRefresh );
		}
	};

	render() {
		const { settings, widgetSchemas } = this.state;
		const { data, widgetSchemas: propsWidgetSchemas } = this.props;
		// Use schemas from props if available, otherwise use state.
		const schemas = propsWidgetSchemas || widgetSchemas;
		const schema = schemas?.[ 'links' ]?.settings_schema;

		// Get links from data and settings from widget settings.
		const links = data?.links || [];
		const linkStyle = settings?.display_style || 'list';
		const hideIcon = settings?.hide_icon || false;
		const iconType = settings?.icon_type || 'remixicon';

		// Show loading if data is not available yet.
		if ( ! data ) {
			return <div>Loading...</div>;
		}

		// Ensure links is always an array.
		const safeLinks = Array.isArray( links ) ? links : [];

		// Helper function to get icon props based on icon type.
		const getIconProps = ( link ) => {
			if ( ! link.icon ) {
				return {
					name: 'links-line',
					library: 'remixicon',
				};
			}

			const linkIconType = link.icon_type || 'remixicon';

			if ( linkIconType === 'svg' && link.icon.startsWith( '<svg' ) ) {
				return {
					library: 'svg',
					svgContent: link.icon,
				};
			} else {
				return {
					library: 'remixicon',
					name: link.icon,
				};
			}
		};

		return (
			<div className="dm-links-widget">
				<div className={ `dm-links-${ linkStyle }` }>
					{ safeLinks.map( ( link, index ) => (
						<div
							key={ index }
							className="dm-link-item"
							onClick={ () => this.handleLinkClick( link ) }
						>
							{ ! hideIcon && (
								<Icon
									{ ...getIconProps( link ) }
									size="sm"
									className="dm-link-icon"
								/>
							) }
							<span className="dm-link-title">{ link.title }</span>
						</div>
					) ) }
				</div>
			</div>
		);
	}
}

export default LinksWidget;
