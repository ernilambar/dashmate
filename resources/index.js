import { createRoot } from 'react-dom/client';
import 'remixicon/fonts/remixicon.css';
import './css/index.css';
import Dashboard from './components/Dashboard';

document.addEventListener( 'DOMContentLoaded', () => {
	const domContainer = document.getElementById( 'dashmate-app' );

	if ( domContainer ) {
		const root = createRoot( domContainer );
		root.render( <Dashboard /> );
	}
} );
