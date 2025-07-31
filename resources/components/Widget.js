import { Component } from 'react';
import WidgetContent from './WidgetContent';
import WidgetSettingsForm from './WidgetSettingsForm';

class Widget extends Component {
	constructor( props ) {
		super( props );
		this.state = {
			collapsed: false,
			widgetData: null,
			loading: true,
			showSettings: false,
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
				this.setState( { widgetData: data.data, loading: false } );
			} else {
				this.setState( { loading: false } );
			}
		} catch ( error ) {
			this.setState( { loading: false } );
		}
	}

	toggleCollapse = () => {
		this.setState( ( prevState ) => ( {
			collapsed: ! prevState.collapsed,
		} ) );
	};

	openWidgetSettings = () => {
		this.setState( { showSettings: ! this.state.showSettings } );
	};

	handleSettingsChange = ( newSettings ) => {
		// Update widget settings in the parent component
		console.log( 'Settings changed:', newSettings );
		// Here you could also save settings to backend
	};

	render() {
		const { widget, widgets } = this.props;
		const { collapsed, widgetData, loading, showSettings } = this.state;

		// Get widget type from widget ID since JSON no longer contains type field.
		const getWidgetType = ( widgetId ) => {
			if ( widgetId.includes( 'html' ) ) {
				return 'html';
			}
			if ( widgetId.includes( 'links' ) ) {
				return 'links';
			}
			return 'unknown';
		};

		const widgetType = getWidgetType( widget.id );
		const widgetTitle = widgetData?.title || widget.id;

		if ( ! widgets || ! widgets[ widgetType ] ) {
			return (
				<div className="widget widget-unknown">
					<div className="widget-header">
						<h3>{ widgetTitle }</h3>
						<div className="widget-actions">
							<button
								className="button button-small widget-toggle"
								onClick={ this.toggleCollapse }
								title={ collapsed ? 'Expand' : 'Collapse' }
							>
								<span className="dashicons dashicons-{ collapsed ? 'arrow-down-alt2' : 'arrow-up-alt2' }"></span>
							</button>
						</div>
					</div>
					{ ! collapsed && (
						<div className="widget-content">
							<p>Unknown widget type: { widgetType }</p>
						</div>
					) }
				</div>
			);
		}

		return (
			<div className={ `widget widget-${ widgetType } ${ collapsed ? 'collapsed' : '' }` }>
				<div className="widget-header">
					<h3>{ widgetTitle }</h3>
					<div className="widget-actions">
						{ ! collapsed && (
							<button
								className="button button-small widget-settings"
								onClick={ this.openWidgetSettings }
								title="Settings"
							>
								<span className="dashicons dashicons-admin-generic"></span>
							</button>
						) }
						<button
							className="button button-small widget-toggle"
							onClick={ this.toggleCollapse }
							title={ collapsed ? 'Expand' : 'Collapse' }
						>
							<span
								className={ `dashicons dashicons-${
									collapsed ? 'arrow-down-alt2' : 'arrow-up-alt2'
								}` }
							></span>
						</button>
					</div>
				</div>
				{ ! collapsed && (
					<div className="widget-content">
						{ showSettings && widgetType === 'links' && (
							<div className="widget-settings-panel">
								<WidgetSettingsForm
									schema={ widgets[ widgetType ]?.settings_schema }
									values={ widget.settings || {} }
									onChange={ this.handleSettingsChange }
								/>
							</div>
						) }
						<WidgetContent widget={ { ...widget, type: widgetType } } />
					</div>
				) }
			</div>
		);
	}
}

export default Widget;
