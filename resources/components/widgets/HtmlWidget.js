import React from 'react';

class HtmlWidget extends React.Component {
	constructor( props ) {
		super( props );
	}

	render() {
		const { data } = this.props;
		const { html_content } = data || {};

		return (
			<div className="dm-html-widget">
				<div
					className="dm-html-content"
					dangerouslySetInnerHTML={ {
						__html: html_content || '<p>No HTML content provided.</p>',
					} }
				/>
			</div>
		);
	}
}

export default HtmlWidget;
