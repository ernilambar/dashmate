import React from 'react';

class HtmlWidget extends React.Component {
	constructor( props ) {
		super( props );
		this.state = {
			isHovered: false,
		};
	}

	handleClick = () => {
		console.log( 'HtmlWidget clicked:', this.props.data );
	};

	handleMouseEnter = () => {
		this.setState( { isHovered: true } );
	};

	handleMouseLeave = () => {
		this.setState( { isHovered: false } );
	};

	render() {
		const { data } = this.props;
		const { html_content, allow_scripts } = data || {};
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
				{ allow_scripts && (
					<div className="html-scripts-notice">
						<small>Scripts are enabled for this widget</small>
					</div>
				) }
			</div>
		);
	}
}

export default HtmlWidget;
