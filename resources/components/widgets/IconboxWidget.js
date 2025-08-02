import React from 'react';

class IconboxWidget extends React.Component {
	constructor( props ) {
		super( props );
		this.state = {
			isHovered: false,
			isClicked: false,
		};
	}

	handleClick = () => {
		this.setState( { isClicked: true } );
		setTimeout( () => this.setState( { isClicked: false } ), 200 );
	};

	handleMouseEnter = () => {
		this.setState( { isHovered: true } );
	};

	handleMouseLeave = () => {
		this.setState( { isHovered: false } );
	};

	render() {
		const { data } = this.props;
		const { icon, title, subtitle, color } = data || {};
		const { isHovered, isClicked } = this.state;

		const colorClass = color || 'blue';

		return (
			<div
				className={ `iconbox-widget iconbox-${ colorClass } ${
					isHovered ? 'hovered' : ''
				} ${ isClicked ? 'clicked' : '' }` }
				onClick={ this.handleClick }
				onMouseEnter={ this.handleMouseEnter }
				onMouseLeave={ this.handleMouseLeave }
			>
				<div className="iconbox-icon">
					<span className={ `dashicons ${ icon || 'dashicons-admin-users' }` }></span>
				</div>
				<div className="iconbox-content">
					<div className="iconbox-title">{ title || 'Title' }</div>
					<div className="iconbox-subtitle">{ subtitle || 'Subtitle' }</div>
				</div>
			</div>
		);
	}
}

export default IconboxWidget;
