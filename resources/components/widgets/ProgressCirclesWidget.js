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

		// Set current values directly.
		const currentValues = {};
		items.forEach( ( item, index ) => {
			currentValues[ index ] = item.percentage || 0;
		} );

		this.setState( { currentValues } );
	};

	handleCircleClick = ( index, item ) => {
		// Handle circle click if needed.
	};

	renderProgressCircle = ( item, index ) => {
		const { currentValues } = this.state;
		const { percentage, value, caption } = item;
		const { settings = {} } = this.props;

		const currentValue = currentValues[ index ] || 0;
		const hideCaption = settings.hide_caption || false;

		// Responsive radius calculation - smaller circles for smaller containers.
		const baseRadius = 40;
		const minRadius = 25;
		const maxRadius = 50;

		// Calculate responsive radius based on container width.
		const containerWidth = this.props.containerWidth || 400;
		const responsiveRadius = Math.max( minRadius, Math.min( maxRadius, containerWidth / 12 ) );

		const radius = responsiveRadius;
		const circumference = 2 * Math.PI * radius;
		const strokeDasharray = circumference;
		const strokeDashoffset = circumference - ( currentValue / 100 ) * circumference;

		// Calculate responsive stroke width based on radius.
		const minStrokeWidth = 2;
		const maxStrokeWidth = 8;
		const strokeWidth = Math.max( minStrokeWidth, Math.min( maxStrokeWidth, radius / 8 ) );

		// Calculate SVG size based on radius.
		const svgSize = radius * 2 + 20; // Add padding.
		const center = svgSize / 2;

		return (
			<div
				key={ index }
				className="dm-progress-circle-item"
				onClick={ () => this.handleCircleClick( index, item ) }
			>
				<div className="dm-progress-circle-container">
					<svg className="dm-progress-circle" width={ svgSize } height={ svgSize }>
						<circle
							className="dm-progress-circle-bg"
							cx={ center }
							cy={ center }
							r={ radius }
							fill="none"
							stroke="#f0f0f0"
							strokeWidth={ strokeWidth }
						/>
						<circle
							className="dm-progress-circle-fill"
							cx={ center }
							cy={ center }
							r={ radius }
							fill="none"
							stroke="currentColor"
							strokeWidth={ strokeWidth }
							strokeDasharray={ strokeDasharray }
							strokeDashoffset={ strokeDashoffset }
							strokeLinecap="round"
							transform={ `rotate(-90 ${ center } ${ center })` }
						/>
					</svg>
					{ value && <div className="dm-progress-circle-label">{ value }</div> }
				</div>
				{ caption && ! hideCaption && (
					<div className="dm-progress-circle-caption">{ caption }</div>
				) }
			</div>
		);
	};

	render() {
		const { data } = this.props;
		const { items } = data || {};

		if ( ! items || ! Array.isArray( items ) || items.length === 0 ) {
			return (
				<div className="dm-progress-circles-widget">
					<div className="widget-no-data">
						<p>No progress data available.</p>
					</div>
				</div>
			);
		}

		return (
			<div className="dm-progress-circles-widget">
				<div className="dm-progress-circles-grid">
					{ items.map( ( item, index ) => this.renderProgressCircle( item, index ) ) }
				</div>
			</div>
		);
	}
}

export default ProgressCirclesWidget;
