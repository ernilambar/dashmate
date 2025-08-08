import { Component } from 'react';
import { Draggable } from '@hello-pangea/dnd';
import WidgetContent from './WidgetContent';
import WidgetSettingsForm from './WidgetSettingsForm';
import Icon from './Icon';

class Widget extends Component {
	constructor( props ) {
		super( props );
		this.state = {
			collapsed: false,
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

	async loadWidgetData() {
		const { widget } = this.props;

		try {
			// Start fade out
			this.setState( { reloading: true, fadeState: 'fading' } );

			// Wait a bit for fade out animation
			await new Promise( ( resolve ) => setTimeout( resolve, 150 ) );

			// Set to faded state
			this.setState( { fadeState: 'faded' } );

			const url = `${ dashmateApiSettings.restUrl }widgets/${ widget.id }/data`;

			const response = await fetch( url, {
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
		const { widget, index } = this.props;
		const { fadeState } = this.state;

		return (
			<Draggable draggableId={ widget.id } index={ index }>
				{ ( provided, snapshot ) => (
					<div
						className={ `widget widget-error ${
							snapshot.isDragging ? 'dragging' : ''
						}` }
						ref={ provided.innerRef }
						{ ...provided.draggableProps }
					>
						<div className="widget-header" { ...provided.dragHandleProps }>
							<h3>
								<Icon name="warning" size="medium" />
								Widget Error
							</h3>
						</div>
						<div
							className={ `widget-content ${
								fadeState !== 'normal' ? fadeState : ''
							}` }
						>
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
				) }
			</Draggable>
		);
	}

	renderBasicWidget() {
		const { widget, widgets, index } = this.props;
		const { collapsed, showSettings, fadeState } = this.state;

		return (
			<Draggable draggableId={ widget.id } index={ index }>
				{ ( provided, snapshot ) => (
					<div
						className={ `widget widget-basic ${ collapsed ? 'collapsed' : '' } ${
							snapshot.isDragging ? 'dragging' : ''
						}` }
						ref={ provided.innerRef }
						{ ...provided.draggableProps }
					>
						<div className="widget-header" { ...provided.dragHandleProps }>
							<h3>
								<Icon name="settings" size="medium" />
								{ widget.id }
							</h3>
							<div className="widget-actions">
								<button
									className="button button-small widget-toggle"
									onClick={ this.toggleCollapse }
									title={ collapsed ? 'Expand' : 'Collapse' }
								>
									<Icon
										name={ collapsed ? 'expand_more' : 'expand_less' }
										size="small"
									/>
								</button>
							</div>
						</div>
						{ ! collapsed && (
							<div
								className={ `widget-content ${
									fadeState !== 'normal' ? fadeState : ''
								}` }
							>
								<div className="widget-basic-message">
									<p>Widget configuration not available.</p>
									<p>Check console for details.</p>
								</div>
							</div>
						) }
					</div>
				) }
			</Draggable>
		);
	}

	toggleCollapse = () => {
		this.setState( ( prevState ) => ( {
			collapsed: ! prevState.collapsed,
		} ) );
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
			const response = await fetch(
				`${ dashmateApiSettings.restUrl }widgets/${ this.props.widget.id }/settings`,
				{
					method: 'POST',
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify( {
						settings: newSettings,
					} ),
				}
			);

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

	render() {
		const { widget, widgets, index } = this.props;
		const {
			collapsed,
			widgetData,
			loading,
			showSettings,
			reloading,
			fadeState,
			settingsSaveStatus,
		} = this.state;

		// Show loading state while data is being fetched
		if ( loading ) {
			return (
				<Draggable draggableId={ widget.id } index={ index }>
					{ ( provided, snapshot ) => (
						<div
							className={ `widget widget-loading ${
								snapshot.isDragging ? 'dragging' : ''
							}` }
							ref={ provided.innerRef }
							{ ...provided.draggableProps }
						>
							<div className="widget-header" { ...provided.dragHandleProps }>
								<h3>
									<Icon
										name="refresh"
										size="medium"
										className="widget-loading-icon"
									/>
								</h3>
							</div>
							<div className="widget-content">
								<div className="widget-loading">
									<p>Loading widget data...</p>
								</div>
							</div>
						</div>
					) }
				</Draggable>
			);
		}

		// Get widget type from API response instead of guessing from ID
		const widgetType = widgetData?.type;
		const widgetTitle = widgetData?.title || widget.id;
		const widgetIcon = widgetData?.icon || '';

		// Validate that we have a widget type and it's supported
		if ( ! widgetType ) {
			// Fallback to a basic widget display
			return this.renderBasicWidget();
		}

		// Get widget schema using widget ID
		const widgetSchema = widgets[ widget.id ];

		if ( ! widgetSchema ) {
			// Fallback to a basic widget display
			return this.renderBasicWidget();
		}

		return (
			<Draggable draggableId={ widget.id } index={ index }>
				{ ( provided, snapshot ) => (
					<div
						className={ `widget widget-${ widgetType } ${
							collapsed ? 'collapsed' : ''
						} ${ snapshot.isDragging ? 'dragging' : '' }` }
						ref={ provided.innerRef }
						{ ...provided.draggableProps }
					>
						<div className="widget-header" { ...provided.dragHandleProps }>
							<h3>
								{ widgetIcon && <Icon name={ widgetIcon } size="medium" /> }
								{ widgetTitle }
							</h3>
							<div className="widget-actions">
								{ ! collapsed && (
									<button
										className={ `button button-small widget-reload ${
											reloading ? 'reloading' : ''
										}` }
										onClick={ this.handleReload }
										title="Reload Widget"
										disabled={ reloading }
									>
										<Icon name="refresh" size="small" />
									</button>
								) }
								{ ! collapsed &&
									widgetSchema?.settings_schema &&
									Object.keys( widgetSchema.settings_schema ).length > 0 && (
										<button
											className={ `button button-small widget-settings ${
												settingsSaveStatus
													? `settings-${ settingsSaveStatus }`
													: ''
											}` }
											onClick={ this.openWidgetSettings }
											title="Settings"
										>
											<Icon name="settings" size="small" />
										</button>
									) }
								<button
									className="button button-small widget-toggle"
									onClick={ this.toggleCollapse }
									title={ collapsed ? 'Expand' : 'Collapse' }
								>
									<Icon
										name={ collapsed ? 'expand_more' : 'expand_less' }
										size="small"
									/>
								</button>
							</div>
						</div>
						{ ! collapsed && (
							<div
								className={ `widget-content ${
									fadeState !== 'normal' ? fadeState : ''
								}` }
							>
								{ showSettings &&
									widgetSchema?.settings_schema &&
									Object.keys( widgetSchema.settings_schema ).length > 0 && (
										<div className="widget-settings-panel">
											<WidgetSettingsForm
												schema={ widgetSchema.settings_schema }
												values={
													widgetData?.settings || widget.settings || {}
												}
												onChange={ this.handleSettingsChange }
												onClose={ this.openWidgetSettings }
												onSaveStatus={ this.handleSettingsSaveStatus }
											/>
										</div>
									) }
								<WidgetContent
									widget={ { ...widget, type: widgetType } }
									widgetData={ widgetData }
									settings={ widgetData?.settings || widget.settings || {} }
									onSettingsChange={ this.handleSettingsChange }
								/>
							</div>
						) }
					</div>
				) }
			</Draggable>
		);
	}
}

export default Widget;
