import React from 'react';

class ProgressCirclesWidget extends React.Component {
	constructor( props ) {
		super( props );
		this.state = {
			animatedValues: {},
		};
	}

	componentDidMount() {
		this.animateAllProgress();
	}

	componentDidUpdate( prevProps ) {
		if ( prevProps.data?.items !== this.props.data?.items ) {
			this.animateAllProgress();
		}
	}

	animateAllProgress = () => {
		const { items } = this.props.data || {};
		if ( ! items || ! Array.isArray( items ) ) {
			return;
		}

		// Initialize animated values
		const animatedValues = {};
		items.forEach( ( item, index ) => {
			animatedValues[ index ] = 0;
		} );
		this.setState( { animatedValues } );

		// Animate each progress circle
		items.forEach( ( item, index ) => {
			this.animateProgress( index, item.percentage || 0 );
		} );
	};

	animateProgress = ( index, targetValue ) => {
		const duration = 1000; // 1 second
		const steps = 60;
		const increment = targetValue / steps;
		let currentStep = 0;

		const animate = () => {
			currentStep++;
			const newValue = Math.min( currentStep * increment, targetValue );

			this.setState( ( prevState ) => ( {
				animatedValues: {
					...prevState.animatedValues,
					[ index ]: newValue,
				},
			} ) );

			if ( currentStep < steps ) {
				setTimeout( animate, duration / steps );
			}
		};

		setTimeout( animate, index * 200 ); // Stagger animations
	};

	handleCircleClick = ( index, item ) => {
		this.animateProgress( index, item.percentage || 0 );
		console.log( 'ProgressCircle clicked:', item );
	};

	renderProgressCircle = ( item, index ) => {
		const { animatedValues } = this.state;
		const { percentage, value, caption, color } = item;

		const currentValue = animatedValues[ index ] || 0;
		const colorClass = color || 'blue';
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
							className={ `progress-circle-fill progress-circle-${ colorClass }` }
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
					<div className="progress-circle-label">
						{ value || `${ Math.round( currentValue ) }%` }
					</div>
				</div>
				<div className="progress-circle-caption">{ caption || 'Progress' }</div>
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
