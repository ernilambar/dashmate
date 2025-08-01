import { Component } from 'react';
import { Draggable } from '@hello-pangea/dnd';
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

	renderErrorWidget( error ) {
		const { widget, index } = this.props;

		return (
			<Draggable draggableId={ widget.id } index={ index }>
				{ ( provided, snapshot ) => (
					<div
						className={ `widget widget-error ${
							snapshot.isDragging ? 'dragging' : ''
						}` }
						ref={ provided.innerRef }
						{ ...provided.draggableProps }
						{ ...provided.dragHandleProps }
					>
						<div className="widget-header">
							<h3>
								<span className="dashicons dashicons-warning"></span>
								Widget Error
							</h3>
						</div>
						<div className="widget-content">
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
		const { collapsed, showSettings } = this.state;

		return (
			<Draggable draggableId={ widget.id } index={ index }>
				{ ( provided, snapshot ) => (
					<div
						className={ `widget widget-basic ${ collapsed ? 'collapsed' : '' } ${
							snapshot.isDragging ? 'dragging' : ''
						}` }
						ref={ provided.innerRef }
						{ ...provided.draggableProps }
						{ ...provided.dragHandleProps }
					>
						<div className="widget-header">
							<h3>
								<span className="dashicons dashicons-admin-generic"></span>
								{ widget.id }
							</h3>
							<div className="widget-actions">
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

	handleSettingsChange = async ( newSettings ) => {
		// Update widget settings in the parent component
		console.log( 'Settings changed:', newSettings );

		try {
			const response = await fetch(
				`/wp-json/dashmate/v1/widgets/${ this.props.widget.id }/settings`,
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
				console.log( 'Settings saved successfully' );
				// Reload widget data to reflect the new settings
				this.loadWidgetData();
			} else {
				console.error( 'Failed to save settings:', result );
			}
		} catch ( error ) {
			console.error( 'Error saving settings:', error );
		}
	};

	render() {
		const { widget, widgets, index } = this.props;
		const { collapsed, widgetData, loading, showSettings } = this.state;

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
							{ ...provided.dragHandleProps }
						>
							<div className="widget-header">
								<h3>Loading...</h3>
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
			console.warn( `No widget type found for widget: ${ widget.id }` );
			// Fallback to a basic widget display
			return this.renderBasicWidget();
		}

		if ( ! widgets || ! widgets[ widgetType ] ) {
			console.warn(
				`Widget type '${ widgetType }' is not registered. Available types: ${ Object.keys(
					widgets || {}
				).join( ', ' ) }`
			);
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
						{ ...provided.dragHandleProps }
					>
						<div className="widget-header">
							<h3>
								{ widgetIcon && (
									<span
										className={ `dashicons dashicons-${ widgetIcon }` }
									></span>
								) }
								{ widgetTitle }
							</h3>
							<div className="widget-actions">
								{ ! collapsed &&
									widgets[ widgetType ]?.settings_schema &&
									Object.keys( widgets[ widgetType ].settings_schema ).length >
										0 && (
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
								{ showSettings &&
									widgets[ widgetType ]?.settings_schema &&
									Object.keys( widgets[ widgetType ].settings_schema ).length >
										0 && (
										<div className="widget-settings-panel">
											<WidgetSettingsForm
												schema={ widgets[ widgetType ]?.settings_schema }
												values={ widget.settings || {} }
												onChange={ this.handleSettingsChange }
											/>
										</div>
									) }
								<WidgetContent
									widget={ { ...widget, type: widgetType } }
									widgetData={ widgetData }
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
