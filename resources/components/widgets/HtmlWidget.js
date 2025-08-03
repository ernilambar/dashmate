import React from 'react';

class HtmlWidget extends React.Component {
	constructor( props ) {
		super( props );
		this.state = {
			isHovered: false,
		};
	}

	handleClick = () => {
		// Handle HTML widget click if needed
	};

	handleMouseEnter = () => {
		this.setState( { isHovered: true } );
	};

	handleMouseLeave = () => {
		this.setState( { isHovered: false } );
	};

	render() {
		const { data } = this.props;
		const { html_content } = data || {};
		const { isHovered } = this.state;

		return (
			<div
				className={ `html-widget ${ isHovered ? 'hovered' : '' }` }
				onClick={ this.handleClick }
				onMouseEnter={ this.handleMouseEnter }
				onMouseLeave={ this.handleMouseLeave }
			>
				<div
					className="html-content"
					dangerouslySetInnerHTML={ {
						__html: html_content || '<p>No HTML content provided</p>',
					} }
				/>
			</div>
		);
	}
}

export default HtmlWidget;
