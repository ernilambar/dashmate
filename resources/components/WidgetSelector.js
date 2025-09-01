import { Component } from 'react';
import { __ } from '@wordpress/i18n';
import Icon from './Icon';

class WidgetSelector extends Component {
	constructor( props ) {
		super( props );
		this.state = {
			isOpen: false,
		};
	}

	componentDidMount() {
		// Close dropdown when clicking outside
		document.addEventListener( 'click', this.handleClickOutside );
	}

	componentWillUnmount() {
		document.removeEventListener( 'click', this.handleClickOutside );
	}

	handleClickOutside = ( event ) => {
		if ( this.dropdownRef && ! this.dropdownRef.contains( event.target ) ) {
			this.setState( { isOpen: false } );
		}
	};

	toggleDropdown = () => {
		this.setState( ( prevState ) => ( { isOpen: ! prevState.isOpen } ) );
	};

	handleWidgetSelect = ( widgetId ) => {
		const { onWidgetSelect } = this.props;
		if ( onWidgetSelect ) {
			onWidgetSelect( widgetId );
		}
		this.setState( { isOpen: false } );
	};

	render() {
		const { widgets, dashboard } = this.props;
		const { isOpen } = this.state;

		// Get all widget IDs currently in the dashboard
		const placedWidgetIds = new Set();
		if ( dashboard && dashboard.columns ) {
			dashboard.columns.forEach( ( column ) => {
				if ( column.widgets && Array.isArray( column.widgets ) ) {
					column.widgets.forEach( ( widget ) => {
						if ( widget.id ) {
							placedWidgetIds.add( widget.id );
						}
					} );
				}
			} );
		}

		// Filter available widgets
		const availableWidgets = [];
		if ( widgets && typeof widgets === 'object' ) {
			Object.keys( widgets ).forEach( ( widgetId ) => {
				const widget = widgets[ widgetId ];
				if ( widget && widgetId ) {
					// Only include actual widget instances that have a widget_id property
					// Widget types/templates (html, links, progress-circles, tabular) should NOT be included
					if ( widget.widget_id ) {
						availableWidgets.push( {
							id: widgetId,
							title: widget.name || widget.title || widgetId,
							description: widget.description || '',
							isPlaced: placedWidgetIds.has( widgetId ),
						} );
					}
				}
			} );
		}

		return (
			<div className="widget-selector" ref={ ( ref ) => ( this.dropdownRef = ref ) }>
				<button
					className="widget-selector-toggle"
					onClick={ this.toggleDropdown }
					title="Add Widget"
				>
					<Icon name="add-circle-line" size="2xl" />
				</button>
				{ isOpen && (
					<div className="widget-selector-dropdown">
						<div className="widget-selector-header">
							<h4>Add Widget</h4>
						</div>
						<div className="widget-selector-list">
							{ availableWidgets.length > 0 ? (
								availableWidgets.map( ( widget ) => (
									<button
										key={ widget.id }
										className={ `widget-selector-item ${
											widget.isPlaced ? 'disabled' : ''
										}` }
										onClick={ () => this.handleWidgetSelect( widget.id ) }
										disabled={ widget.isPlaced }
										title={
											widget.isPlaced
												? 'Widget already placed in dashboard'
												: `Add ${ widget.title }`
										}
									>
										<div className="widget-selector-item-content">
											<span className="widget-selector-item-title">
												{ widget.title }
											</span>
											{ widget.description && (
												<span className="widget-selector-item-description">
													{ widget.description }
												</span>
											) }
										</div>
										{ widget.isPlaced && (
											<span className="widget-selector-item-status">
												<Icon name="checkbox-circle-line" size="md" />
											</span>
										) }
									</button>
								) )
							) : (
								<div className="widget-selector-empty">
									<p>No widgets available</p>
								</div>
							) }
						</div>
					</div>
				) }
			</div>
		);
	}
}

export default WidgetSelector;
