import React from 'react';

class ProgressCirclesWidget extends React.Component {
	constructor( props ) {
		super( props );
		this.state = {
			currentValues: {},
		};
	}

	componentDidMount() {
		this.setProgressValues();
	}

	componentDidUpdate( prevProps ) {
		if ( prevProps.data?.items !== this.props.data?.items ) {
			this.setProgressValues();
		}
	}

	setProgressValues = () => {
		const { items } = this.props.data || {};
		if ( ! items || ! Array.isArray( items ) ) {
			return;
		}

		// Set current values directly
		const currentValues = {};
		items.forEach( ( item, index ) => {
			currentValues[ index ] = item.percentage || 0;
		} );
		this.setState( { currentValues } );
	};

	handleCircleClick = ( index, item ) => {
		console.log( 'ProgressCircle clicked:', item );
	};

	renderProgressCircle = ( item, index ) => {
		const { currentValues } = this.state;
		const { percentage, value, caption } = item;

		const currentValue = currentValues[ index ] || 0;
		const radius = 40;
		const circumference = 2 * Math.PI * radius;
		const strokeDasharray = circumference;
		const strokeDashoffset = circumference - ( currentValue / 100 ) * circumference;

		return (
			<div
				key={ index }
				className="progress-circle-item"
				onClick={ () => this.handleCircleClick( index, item ) }
			>
				<div className="progress-circle-container">
					<svg className="progress-circle" width="100" height="100">
						<circle
							className="progress-circle-bg"
							cx="50"
							cy="50"
							r={ radius }
							fill="none"
							stroke="#f0f0f0"
							strokeWidth="6"
						/>
						<circle
							className="progress-circle-fill"
							cx="50"
							cy="50"
							r={ radius }
							fill="none"
							stroke="currentColor"
							strokeWidth="6"
							strokeDasharray={ strokeDasharray }
							strokeDashoffset={ strokeDashoffset }
							strokeLinecap="round"
							transform="rotate(-90 50 50)"
						/>
					</svg>
					{ value && <div className="progress-circle-label">{ value }</div> }
				</div>
				{ caption && <div className="progress-circle-caption">{ caption }</div> }
			</div>
		);
	};

	render() {
		const { data } = this.props;
		const { items } = data || {};

		if ( ! items || ! Array.isArray( items ) || items.length === 0 ) {
			return (
				<div className="progress-circles-widget">
					<div className="widget-no-data">
						<p>No progress data available.</p>
					</div>
				</div>
			);
		}

		return (
			<div className="progress-circles-widget">
				<div className="progress-circles-grid">
					{ items.map( ( item, index ) => this.renderProgressCircle( item, index ) ) }
				</div>
			</div>
		);
	}
}

export default ProgressCirclesWidget;
