import { Component } from 'react';
import WidgetContent from './WidgetContent';

class Widget extends Component {
	constructor( props ) {
		super( props );
		this.state = {
			collapsed: false,
		};
	}

	toggleCollapse = () => {
		this.setState( ( prevState ) => ( {
			collapsed: ! prevState.collapsed,
		} ) );
	};

	openWidgetSettings = () => {
		// TODO: Implement widget settings modal
		console.log( 'Open settings for widget:', this.props.widget );
	};

	render() {
		const { widget, widgets } = this.props;
		const { collapsed } = this.state;

		if ( ! widgets || ! widgets[ widget.type ] ) {
			return (
				<div className="widget widget-unknown">
					<div className="widget-header">
						<h3>{ widget.title }</h3>
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
							<p>Unknown widget type: { widget.type }</p>
						</div>
					) }
				</div>
			);
		}

		return (
			<div className={ `widget widget-${ widget.type } ${ collapsed ? 'collapsed' : '' }` }>
				<div className="widget-header">
					<h3>{ widget.title }</h3>
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
						<WidgetContent widget={ widget } />
					</div>
				) }
			</div>
		);
	}
}

export default Widget;
