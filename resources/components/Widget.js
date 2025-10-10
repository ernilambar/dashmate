import { Component } from 'react';
import WidgetContent from './WidgetContent';
import WidgetSettingsForm from './WidgetSettingsForm';
import Icon from './Icon';

class Widget extends Component {
	constructor( props ) {
		super( props );
		this.state = {
			collapsed: props.widget.collapsed || false,
			widgetData: null,
			loading: true,
			showSettings: false,
			reloading: false,
			fadeState: 'normal', // 'normal', 'fading', 'faded'
			settingsVersion: 0, // Force re-render when settings change
			settingsSaveStatus: null, // 'success', 'error', null
		};
	}

	componentDidMount() {
		this.loadWidgetData();
	}

	componentDidUpdate( prevProps ) {
		// Update collapsed state if it changes from parent.
		if ( prevProps.widget.collapsed !== this.props.widget.collapsed ) {
			this.setState( { collapsed: this.props.widget.collapsed || false } );
		}
	}

	async loadWidgetData() {
		const { widget } = this.props;

		try {
			// Start fade out
			this.setState( { reloading: true, fadeState: 'fading' } );

			// Wait a bit for fade out animation
			await new Promise( ( resolve ) => setTimeout( resolve, 150 ) );

			// Set to faded state
			this.setState( { fadeState: 'faded' } );

			const url = new URL( `${ dashmateApiSettings.restUrl }widgets/${ widget.id }/data` );
			if ( this.props.dashboardId ) {
				url.searchParams.set( 'dashboard_id', this.props.dashboardId );
			}

			const response = await fetch( url.toString(), {
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

			// Fade back in
			this.setState( { fadeState: 'fading' } );
			await new Promise( ( resolve ) => setTimeout( resolve, 150 ) );
			this.setState( { reloading: false, fadeState: 'normal' } );
		} catch ( error ) {
			console.error( 'Error loading widget data:', error );
			this.setState( { loading: false, reloading: false, fadeState: 'normal' } );
		}
	}

	handleReload = () => {
		this.loadWidgetData();
	};

	renderErrorWidget( error ) {
		const { widget } = this.props;
		const { fadeState } = this.state;

		return (
			<div
				className={ this.getWidgetClasses( 'error', widget.id, [], {
					state: 'error',
				} ) }
			>
				<div className="widget-header">
					<h3>
						<Icon name="error-warning-line" size="md" />
						Widget Error
					</h3>
				</div>
				<div className={ `widget-content ${ fadeState !== 'normal' ? fadeState : '' }` }>
					<div className="widget-error-message">
						<p>
							<strong>Error:</strong> { error.message }
						</p>
						<p>
							<strong>Widget ID:</strong> { widget.id }
						</p>
					</div>
				</div>
			</div>
		);
	}

	renderBasicWidget() {
		const { widget, widgets } = this.props;
		const { collapsed, fadeState } = this.state;

		// Use title and icon from widget data (now provided by API) or fallback to schema.
		const widgetTitle =
			widget.title || widgets[ widget.id ]?.name || widgets[ widget.id ]?.title || widget.id;
		const widgetIcon = widget.icon || widgets[ widget.id ]?.icon || 'settings';

		return (
			<div
				className={ this.getWidgetClasses( 'basic', widget.id, [], {
					collapsed,
				} ) }
			>
				<div className="widget-header">
					<h3>
						<Icon name={ widgetIcon } size="md" />
						{ widgetTitle }
					</h3>
					<div className="widget-actions">
						<button
							className="dm-icon-button dm-icon-button--small widget-toggle"
							onClick={ this.toggleCollapse }
						>
							<Icon
								name={
									collapsed ? 'arrow-down-double-line' : 'arrow-up-double-line'
								}
								size="sm"
							/>
						</button>
					</div>
				</div>
				{ ! collapsed && (
					<div
						className={ `widget-content ${ fadeState !== 'normal' ? fadeState : '' }` }
					>
						<div className="widget-basic-message">
							<p>Widget configuration not available.</p>
							<p>Check console for details.</p>
						</div>
					</div>
				) }
			</div>
		);
	}

	toggleCollapse = async () => {
		const { widget, onPropertyUpdate } = this.props;
		const newCollapsedState = ! this.state.collapsed;

		// Update local state immediately for UI responsiveness.
		this.setState( { collapsed: newCollapsedState } );

		// Update the widget data in the parent component.
		if ( onPropertyUpdate ) {
			await onPropertyUpdate( widget.id, { collapsed: newCollapsedState } );
		}
	};

	openWidgetSettings = () => {
		this.setState( { showSettings: ! this.state.showSettings } );
	};

	handleSettingsChange = async ( newSettings, needsRefresh = false ) => {
		// Update widget settings in the parent component

		// Update local widget settings immediately for instant feedback
		this.props.widget.settings = { ...this.props.widget.settings, ...newSettings };
		// Force re-render by updating settings version
		this.setState( { settingsVersion: this.state.settingsVersion + 1 } );

		try {
			const url = new URL(
				`${ dashmateApiSettings.restUrl }widgets/${ this.props.widget.id }/settings`
			);
			if ( this.props.dashboardId ) {
				url.searchParams.set( 'dashboard_id', this.props.dashboardId );
			}

			const response = await fetch( url.toString(), {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					'X-WP-Nonce': dashmateApiSettings?.nonce || '',
				},
				body: JSON.stringify( {
					settings: newSettings,
				} ),
			} );

			const result = await response.json();

			if ( result.success ) {
				// Set success status for cog icon animation
				this.setState( { settingsSaveStatus: 'success' } );
				// Clear status after animation duration
				setTimeout( () => {
					this.setState( { settingsSaveStatus: null } );
				}, 2000 );

				// Only reload widget data if refresh is needed
				if ( needsRefresh ) {
					this.loadWidgetData();
				}
			} else {
				// Set error status for cog icon animation
				this.setState( { settingsSaveStatus: 'error' } );
				// Clear status after animation duration
				setTimeout( () => {
					this.setState( { settingsSaveStatus: null } );
				}, 2000 );
			}
		} catch ( error ) {
			// Set error status for cog icon animation
			this.setState( { settingsSaveStatus: 'error' } );
			// Clear status after animation duration
			setTimeout( () => {
				this.setState( { settingsSaveStatus: null } );
			}, 2000 );
			// Handle error silently or log to server
		}
	};

	handleSettingsSaveStatus = ( status ) => {
		this.setState( { settingsSaveStatus: status } );
		// Clear status after animation duration
		setTimeout( () => {
			this.setState( { settingsSaveStatus: null } );
		}, 2000 );
	};

	/**
	 * Generate widget wrapper classes consistently.
	 *
	 * @param {string} widgetType - Widget type (html, tabular, etc.).
	 * @param {string} widgetId - Widget ID.
	 * @param {Array} customClasses - Custom classes from metadata.
	 * @param {Object} options - Additional options.
	 * @returns {string} Combined class string.
	 */
	getWidgetClasses = ( widgetType, widgetId, customClasses = [], options = {} ) => {
		const { collapsed = false, isDragging = false, state = '' } = options;

		const baseClasses = [ 'widget', `widget-${ widgetType }`, `widget-${ widgetId }` ];

		if ( collapsed ) {
			baseClasses.push( 'collapsed' );
		}

		if ( isDragging ) {
			baseClasses.push( 'dragging' );
		}

		if ( state ) {
			baseClasses.push( `widget-${ state }` );
		}

		if ( customClasses.length > 0 ) {
			baseClasses.push( ...customClasses );
		}

		return baseClasses.join( ' ' );
	};

	render() {
		const { widget, widgets, index, onRemove } = this.props;
		const {
			collapsed,
			widgetData,
			loading,
			showSettings,
			reloading,
			fadeState,
			settingsSaveStatus,
		} = this.state;

		// Show loading state while data is being fetched (but not for collapsed widgets).
		if ( loading && ! collapsed ) {
			return (
				<div
					className={ this.getWidgetClasses( 'loading', widget.id, [], {
						state: 'loading',
					} ) }
				>
					<div className="widget-header">
						<h3>
							<Icon name="refresh-line" size="md" className="widget-loading-icon" />
						</h3>
					</div>
					<div className="widget-content">
						<div className="widget-loading">
							<p>Loading widget data...</p>
						</div>
					</div>
				</div>
			);
		}

		// For collapsed widgets, use basic widget display with title from dashboard data.
		if ( collapsed ) {
			return this.renderBasicWidget();
		}

		// Get widget type from API response instead of guessing from ID.
		const widgetType = widgetData?.type;
		const widgetTitle = widgetData?.title || widget.id;
		const widgetIcon = widgetData?.icon || '';
		const metadata = widgetData?.metadata || {};
		const customClasses = metadata.classes || [];
		const customAttributes = metadata.attributes || {};

		// Validate that we have a widget type and it's supported.
		if ( ! widgetType ) {
			// Fallback to a basic widget display.
			return this.renderBasicWidget();
		}

		// Get widget schema using widget ID.
		const widgetSchema = widgets[ widget.id ];

		if ( ! widgetSchema ) {
			// Fallback to a basic widget display.
			return this.renderBasicWidget();
		}

		return (
			<div
				className={ this.getWidgetClasses( widgetType, widget.id, customClasses, {
					collapsed,
				} ) }
				{ ...customAttributes }
			>
				<div className="widget-header">
					<h3>
						{ widgetIcon && <Icon name={ widgetIcon } size="md" /> }
						{ widgetTitle }
					</h3>
					<div className="widget-actions">
						{ ! collapsed && (
							<button
								className={ `dm-icon-button dm-icon-button--small widget-reload ${
									reloading ? 'reloading' : ''
								}` }
								onClick={ this.handleReload }
								title="Reload Widget"
								disabled={ reloading }
							>
								<Icon name="loop-right-line" size="sm" />
							</button>
						) }
						{ ! collapsed &&
							widgetSchema?.settings_schema &&
							Object.keys( widgetSchema.settings_schema ).length > 0 && (
								<button
									className={ `dm-icon-button dm-icon-button--small widget-settings ${
										settingsSaveStatus ? `settings-${ settingsSaveStatus }` : ''
									}` }
									onClick={ this.openWidgetSettings }
									title="Settings"
								>
									<Icon name="settings-4-line" size="sm" />
								</button>
							) }
						<button
							className="dm-icon-button dm-icon-button--small widget-toggle"
							onClick={ this.toggleCollapse }
						>
							<Icon
								name={
									collapsed ? 'arrow-down-double-line' : 'arrow-up-double-line'
								}
								size="sm"
							/>
						</button>
					</div>
				</div>
				{ ! collapsed && (
					<div
						className={ `widget-content ${ fadeState !== 'normal' ? fadeState : '' }` }
					>
						{ showSettings && (
							<div className="widget-settings-panel">
								<WidgetSettingsForm
									schema={ widgetSchema?.settings_schema || {} }
									values={ widgetData?.settings || widget.settings || {} }
									onChange={ this.handleSettingsChange }
									onClose={ this.openWidgetSettings }
									onSaveStatus={ this.handleSettingsSaveStatus }
									onRemove={ onRemove }
									widgetId={ widget.id }
								/>
							</div>
						) }
						<WidgetContent
							widget={ { ...widget, type: widgetType } }
							widgetData={ widgetData }
							settings={ widgetData?.settings || widget.settings || {} }
							widgetSchemas={ widgets }
							onSettingsChange={ this.handleSettingsChange }
							onReloadWidget={ this.handleReload }
						/>
					</div>
				) }
			</div>
		);
	}
}

export default Widget;
