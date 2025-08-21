import React from 'react';

class BarChartWidget extends React.Component {
	constructor( props ) {
		super( props );
		this.state = {
			maxValue: 0,
		};
	}

	componentDidMount() {
		this.calculateMaxValue();
	}

	componentDidUpdate( prevProps ) {
		if ( prevProps.data?.items !== this.props.data?.items ) {
			this.calculateMaxValue();
		}
	}

	calculateMaxValue = () => {
		const { items } = this.props.data || {};
		if ( ! items || ! Array.isArray( items ) ) {
			return;
		}

		const maxValue = Math.max( ...items.map( item => item.value || 0 ) );
		this.setState( { maxValue } );
	};

	handleBarClick = ( index, item ) => {
		// Handle bar click if needed.
	};

	renderBar = ( item, index ) => {
		const { maxValue } = this.state;
		const { value, label, color } = item;
		const { settings = {} } = this.props;

		const hideLabels = settings.hide_labels || false;
		const showValues = settings.show_values !== false; // Default to true

		// Calculate bar height as percentage of max value.
		const heightPercentage = maxValue > 0 ? ( value / maxValue ) * 100 : 0;

		return (
			<div
				key={ index }
				className="dm-bar-chart-item"
				onClick={ () => this.handleBarClick( index, item ) }
			>
				<div className="dm-bar-chart-container">
					{ showValues && (
						<div className="dm-bar-chart-value">{ value }</div>
					) }
					<div className="dm-bar-chart-bar-wrapper">
						<div
							className="dm-bar-chart-bar"
							style={ {
								height: `${ heightPercentage }%`,
								backgroundColor: color || '#6facde',
							} }
						/>
					</div>
				</div>
				{ label && ! hideLabels && (
					<div className="dm-bar-chart-label">{ label }</div>
				) }
			</div>
		);
	};

	render() {
		const { data } = this.props;
		const { items } = data || {};

		if ( ! items || ! Array.isArray( items ) || items.length === 0 ) {
			return (
				<div className="dm-bar-chart-widget">
					<div className="widget-no-data">
						<p>No chart data available.</p>
					</div>
				</div>
			);
		}

		return (
			<div className="dm-bar-chart-widget">
				<div className="dm-bar-chart-grid">
					{ items.map( ( item, index ) => this.renderBar( item, index ) ) }
				</div>
			</div>
		);
	}
}

export default BarChartWidget;
