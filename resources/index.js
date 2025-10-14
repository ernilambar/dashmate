import { createRoot } from 'react-dom/client';
import 'remixicon/fonts/remixicon.css';
import './css/index.css';
import Dashboard from './components/Dashboard';

document.addEventListener( 'DOMContentLoaded', () => {
	const domContainer = document.getElementById( 'dashmate-app' );

	if ( domContainer ) {
		const dashboardId = domContainer.getAttribute( 'data-dashboard-id' ) || 'main';
		const root = createRoot( domContainer );
		root.render( <Dashboard dashboardId={ dashboardId } /> );
	} else {
		console.log( 'Dashmate: Container not found' );
	}
} );
