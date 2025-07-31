import React from 'react';

class QuickLinksWidget extends React.Component {
	constructor( props ) {
		super( props );
		this.state = {
			// No hover state needed for now
		};
	}

	handleLinkClick = ( link ) => {
		console.log( 'QuickLinksWidget link clicked:', link );
		// Could open in new tab or handle navigation
		window.open( link.url, '_blank' );
	};

	render() {
		const { data } = this.props;
		const { title, links } = data || {};

		return (
			<div className="quick-links-widget">
				{ title && <h4 className="quick-links-title">{ title }</h4> }
				<div className="quick-links-list">
					{ ( links || [] ).map( ( link, index ) => (
						<div
							key={ index }
							className="quick-link-item"
							onClick={ () => this.handleLinkClick( link ) }
						>
							<span
								className={ `quick-link-icon dashicons ${
									link.icon || 'dashicons-admin-links'
								}` }
							></span>
							<span className="quick-link-title">{ link.title }</span>
						</div>
					) ) }
				</div>
			</div>
		);
	}
}

export default QuickLinksWidget;
