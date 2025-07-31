import React from 'react';

class ProgressCircleWidget extends React.Component {
	constructor( props ) {
		super( props );
		this.state = {
			isAnimating: false,
			currentValue: 0,
			isHovered: false,
		};
	}

	componentDidMount() {
		this.animateProgress();
	}

	componentDidUpdate( prevProps ) {
		if ( prevProps.data?.percentage !== this.props.data?.percentage ) {
			this.animateProgress();
		}
	}

	animateProgress = () => {
		const { percentage } = this.props.data || {};
		const targetValue = percentage || 0;

		this.setState( { isAnimating: true, currentValue: 0 } );

		const duration = 1000; // 1 second
		const steps = 60;
		const increment = targetValue / steps;
		let currentStep = 0;

		const animate = () => {
			currentStep++;
			const newValue = Math.min( currentStep * increment, targetValue );

			this.setState( { currentValue: newValue } );

			if ( currentStep < steps ) {
				setTimeout( animate, duration / steps );
			} else {
				this.setState( { isAnimating: false } );
			}
		};

		setTimeout( animate, 100 );
	};

	handleClick = () => {
		this.animateProgress();
		console.log( 'ProgressCircleWidget clicked:', this.props.data );
	};

	handleMouseEnter = () => {
		this.setState( { isHovered: true } );
	};

	handleMouseLeave = () => {
		this.setState( { isHovered: false } );
	};

	render() {
		const { data } = this.props;
		const { percentage, label, caption, color } = data || {};
		const { currentValue, isAnimating, isHovered } = this.state;

		const colorClass = color || 'blue';
		const radius = 60;
		const circumference = 2 * Math.PI * radius;
		const strokeDasharray = circumference;
		const strokeDashoffset = circumference - ( currentValue / 100 ) * circumference;

		return (
			<div
				className={ `progress-circle-widget ${ isHovered ? 'hovered' : '' } ${
					isAnimating ? 'animating' : ''
				}` }
				onClick={ this.handleClick }
				onMouseEnter={ this.handleMouseEnter }
				onMouseLeave={ this.handleMouseLeave }
			>
				<div className="progress-circle-container">
					<svg className="progress-circle" width="150" height="150">
						<circle
							className="progress-circle-bg"
							cx="75"
							cy="75"
							r={ radius }
							fill="none"
							stroke="#f0f0f0"
							strokeWidth="8"
						/>
						<circle
							className={ `progress-circle-fill progress-circle-${ colorClass }` }
							cx="75"
							cy="75"
							r={ radius }
							fill="none"
							stroke="currentColor"
							strokeWidth="8"
							strokeDasharray={ strokeDasharray }
							strokeDashoffset={ strokeDashoffset }
							strokeLinecap="round"
							transform="rotate(-90 75 75)"
						/>
					</svg>
					<div className="progress-circle-label">
						{ label || `${ Math.round( currentValue ) }%` }
					</div>
				</div>
				<div className="progress-circle-caption">{ caption || 'Progress' }</div>
			</div>
		);
	}
}

export default ProgressCircleWidget;
