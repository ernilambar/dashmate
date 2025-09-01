import React, { useState, useEffect } from 'react';
import LayoutPreview from './LayoutPreview';
import Icon from './Icon';

const LayoutSelector = ( { onLayoutSelect, currentLayout = 'current' } ) => {
	const [ layouts, setLayouts ] = useState( {} );
	const [ selectedLayout, setSelectedLayout ] = useState( currentLayout );
	const [ layoutDataMap, setLayoutDataMap ] = useState( {} );
	const [ loading, setLoading ] = useState( true );
	const [ error, setError ] = useState( null );
	const [ showPopup, setShowPopup ] = useState( false );

	// Get settings from WordPress.
	const settings = window.dashmateApiSettings || {};

	// Fetch layouts on component mount.
	useEffect( () => {
		fetchLayouts();
	}, [] );

	// Sync selectedLayout with currentLayout prop when it changes.
	useEffect( () => {
		setSelectedLayout( currentLayout );
	}, [ currentLayout ] );

	// Handle click outside to close popup
	useEffect( () => {
		const handleClickOutside = ( event ) => {
			if ( showPopup && ! event.target.closest( '.layout-selector' ) ) {
				setShowPopup( false );
			}
		};

		document.addEventListener( 'mousedown', handleClickOutside );
		return () => {
			document.removeEventListener( 'mousedown', handleClickOutside );
		};
	}, [ showPopup ] );

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

				// Extract layout data from the response
				const layoutDataMap = {};
				Object.entries( data.data ).forEach( ( [ key, layout ] ) => {
					if ( layout.layoutData ) {
						layoutDataMap[ key ] = layout.layoutData;
					}
				} );
				setLayoutDataMap( layoutDataMap );
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
		setShowPopup( false ); // Close popup after selection
		if ( onLayoutSelect ) {
			onLayoutSelect( layoutKey );
		}
	};

	const handleCopyLayout = async () => {
		const currentLayoutData = layoutDataMap[ selectedLayout ];
		if ( ! currentLayoutData ) {
			return;
		}

		try {
			const jsonContent = JSON.stringify( currentLayoutData, null, 2 );
			await navigator.clipboard.writeText( jsonContent );
		} catch ( error ) {
			// Fallback for older browsers
			const textArea = document.createElement( 'textarea' );
			textArea.value = JSON.stringify( currentLayoutData, null, 2 );
			document.body.appendChild( textArea );
			textArea.select();
			document.execCommand( 'copy' );
			document.body.removeChild( textArea );
		}
	};

	if ( loading ) {
		return (
			<div className="layout-selector">
				<button
					type="button"
					className="layout-selector-button"
					disabled
					title="Loading layouts..."
				>
					<Icon name="layout-grid-2-fill" size="2xl" />
				</button>
			</div>
		);
	}

	if ( error ) {
		return (
			<div className="layout-selector">
				<button
					type="button"
					className="layout-selector-button"
					disabled
					title="Error loading layouts"
				>
					<Icon name="layout-grid-2-fill" size="2xl" />
				</button>
			</div>
		);
	}

	return (
		<div className="layout-selector">
			<button
				type="button"
				className="layout-selector-button"
				onClick={ () => setShowPopup( ! showPopup ) }
				title="Select layout"
			>
				<Icon name="layout-grid-2-fill" size="2xl" />
			</button>

			{ showPopup && (
				<div className="layout-selector-popup">
					<div className="layout-selector-popup-header">
						<h4 className="layout-selector-popup-title">Select Layout</h4>
					</div>
					<div className="layout-selector-popup-content">
						{ Object.entries( layouts ).map( ( [ key, layout ] ) => (
							<div
								key={ key }
								className={ `layout-selector-option ${
									selectedLayout === key ? 'layout-selector-option--selected' : ''
								}` }
								onClick={ () => handleLayoutChange( key ) }
								title={ `Apply ${ layout.title || key } layout` }
							>
								<div className="layout-selector-option-header">
									<h4 className="layout-selector-option-title">
										{ layout.title || key }
									</h4>
								</div>
								{ layoutDataMap[ key ] ? (
									<div className="layout-selector-option-content">
										<LayoutPreview
											layoutData={ layoutDataMap[ key ] }
											selectedLayout={ key }
										/>
										<div className="layout-selector-option-actions">
											{ selectedLayout === key && (
												<button
													type="button"
													className="layout-selector-copy-button"
													onClick={ ( e ) => {
														e.stopPropagation();
														handleCopyLayout();
													} }
													title="Copy layout JSON"
												>
													<Icon name="file-copy-line" size="16px" />
												</button>
											) }
										</div>
									</div>
								) : (
									<div className="layout-selector-option-no-preview">
										<p>No preview available</p>
									</div>
								) }
							</div>
						) ) }
					</div>
				</div>
			) }
		</div>
	);
};

export default LayoutSelector;
