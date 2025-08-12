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
		this.fetchWidgetSchemas();
	}

	componentDidUpdate( prevProps ) {
		// Check if settings prop changed (when settings are updated from parent).
		if ( prevProps.settings !== this.props.settings ) {
			this.setState( { settings: this.props.settings || {} } );
		}
	}

	fetchWidgetSchemas = async () => {
		try {
			const response = await fetch( `${ dashmateApiSettings.restUrl }widgets` );
			const result = await response.json();

			if ( result.success ) {
				this.setState( { widgetSchemas: result.data } );
			} else {
				// Handle error silently.
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
		const { data } = this.props;
		const schema = widgetSchemas?.[ 'links' ]?.settings_schema;

		// Get links from data and settings from widget settings.
		const links = data?.links || [];
		const linkStyle = settings?.display_style || 'list';
		const hideIcon = settings?.hide_icon || false;
		const iconType = settings?.icon_type || 'material-icons';

		// Show loading if data is not available yet.
		if ( ! data ) {
			return <div>Loading...</div>;
		}

		// Ensure links is always an array.
		const safeLinks = Array.isArray( links ) ? links : [];

		// Helper function to get icon props based on icon type.
		const getIconProps = ( link ) => {
			if ( ! link.icon ) return {};

			const linkIconType = link.icon_type || 'material-icons';

			if ( linkIconType === 'svg' && link.icon.startsWith( '<svg' ) ) {
				return {
					library: 'svg',
					svgContent: link.icon,
				};
			} else if ( linkIconType === 'dashicons' ) {
				return {
					library: 'dashicons',
					name: link.icon,
				};
			} else {
				return {
					library: 'material-icons',
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
							{ ! hideIcon && link.icon && (
								<Icon
									{ ...getIconProps( link ) }
									size="small"
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
