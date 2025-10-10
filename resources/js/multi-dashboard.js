/**
 * Multi-Dashboard JavaScript Integration
 *
 * This file shows how to integrate multiple dashboards in your React app
 */

// Example React component initialization for multiple dashboards
function initializeMultiDashboards() {
	const appElement = document.getElementById( 'dashmate-app' );
	const dashboardConfigs = JSON.parse( appElement.dataset.dashboards );

	// Initialize each dashboard instance
	dashboardConfigs.forEach( ( config ) => {
		const container = document.querySelector( config.container );
		if ( container ) {
			// Initialize dashboard for this container with its slug
			initializeDashboard( container, config.slug, config );
		}
	} );
}

// Example API call function that includes app_slug
async function apiCall( endpoint, options = {}, appSlug = 'default' ) {
	const url = new URL( `/wp-json/dashmate/v1/${ endpoint }`, window.location.origin );
	url.searchParams.set( 'app_slug', appSlug );

	return fetch( url, {
		...options,
		headers: {
			'X-WP-Nonce': dashmateApiSettings.nonce,
			'Content-Type': 'application/json',
			...options.headers,
		},
	} );
}

// Example dashboard initialization function
function initializeDashboard( container, appSlug, config ) {
	// This would be your React component initialization
	console.log( `Initializing dashboard: ${ config.title } with slug: ${ appSlug }` );

	// Example API calls for this specific dashboard
	loadDashboardData( appSlug, container );
}

// Example function to load dashboard data
async function loadDashboardData( appSlug, container ) {
	try {
		const response = await apiCall( 'dashboard', {}, appSlug );
		const data = await response.json();

		if ( data.success ) {
			// Render dashboard data in the container
			const contentDiv = container.querySelector( '.dashboard-content' );
			if ( contentDiv ) {
				contentDiv.innerHTML = `<p>Dashboard "${ appSlug }" loaded with ${
					data.data.columns?.length || 0
				} columns</p>`;
			}
		}
	} catch ( error ) {
		console.error( `Error loading dashboard ${ appSlug }:`, error );
	}
}

// Initialize when DOM is ready
document.addEventListener( 'DOMContentLoaded', initializeMultiDashboards );
