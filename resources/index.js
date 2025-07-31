import { createRoot } from 'react-dom/client';
import { __ } from '@wordpress/i18n';
import './css/main.css';

import { Component } from 'react';

class DashmateApp extends Component {
	render() {
		return (
			<div className="wp-react-plugin">
				<h1>Hello from React in WordPress!</h1>
				<p>This content is rendered by React.</p>
			</div>
		);
	}
}

document.addEventListener( 'DOMContentLoaded', () => {
	const domContainer = document.getElementById( 'dashmate-app' );

	const root = createRoot( domContainer );
	root.render( <DashmateApp /> );
} );
