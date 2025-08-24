import React, { useState, useEffect } from 'react';

const LayoutSelector = ( { onLayoutSelect, currentLayout = 'current' } ) => {
	const [ layouts, setLayouts ] = useState( {} );
	const [ selectedLayout, setSelectedLayout ] = useState( currentLayout );
	const [ loading, setLoading ] = useState( true );
	const [ error, setError ] = useState( null );

	// Get settings from WordPress.
	const settings = window.dashmateApiSettings || {};

	// Fetch layouts on component mount.
	useEffect( () => {
		fetchLayouts();
	}, [] );

	const fetchLayouts = async () => {
		try {
			setLoading( true );
			setError( null );

			// Ensure REST URL ends with slash
			const restUrl = settings.restUrl?.endsWith( '/' )
				? settings.restUrl
				: settings.restUrl + '/';

			// Prepare headers with nonce for security.
			const headers = {};

			// Add nonce header for authentication.
			if ( settings.nonce ) {
				headers[ 'X-WP-Nonce' ] = settings.nonce;
			}

			const response = await fetch( `${ restUrl }layouts`, {
				headers: headers,
				credentials: 'same-origin',
			} );

			if ( ! response.ok ) {
				throw new Error( 'Failed to fetch layouts' );
			}

			const data = await response.json();
			if ( data.success ) {
				setLayouts( data.data );
			} else {
				throw new Error( data.message || 'Failed to fetch layouts' );
			}
		} catch ( error ) {
			setError( error.message );
		} finally {
			setLoading( false );
		}
	};

	const handleLayoutChange = ( layoutKey ) => {
		setSelectedLayout( layoutKey );
		if ( onLayoutSelect ) {
			onLayoutSelect( layoutKey );
		}
	};

	if ( loading ) {
		return (
			<div className="layout-selector">
				<select disabled className="layout-selector-dropdown">
					<option>Loading layouts...</option>
				</select>
			</div>
		);
	}

	if ( error ) {
		return (
			<div className="layout-selector">
				<select disabled className="layout-selector-dropdown">
					<option>Error loading layouts</option>
				</select>
			</div>
		);
	}

	return (
		<div className="layout-selector">
			<label className="layout-selector-label">Layout:</label>
			<select
				className="layout-selector-dropdown"
				value={ selectedLayout }
				onChange={ ( e ) => handleLayoutChange( e.target.value ) }
			>
				{ Object.entries( layouts ).map( ( [ key, layout ] ) => (
					<option key={ key } value={ key }>
						{ layout.title || key }
					</option>
				) ) }
			</select>
		</div>
	);
};

export default LayoutSelector;
