/**
 * Layout page functionality.
 *
 * @package Dashmate
 */

import { useState, useEffect } from 'react';
import { createRoot } from 'react-dom/client';
import LayoutPreview from './components/LayoutPreview';
import CodeHighlight from './components/CodeHighlight';
import './css/layouts.css';
import './css/layout-preview.css';
import './css/components/code-highlight.css';

const LayoutsApp = () => {
	const [ layouts, setLayouts ] = useState( [] );
	const [ selectedLayout, setSelectedLayout ] = useState( 'current' );
	const [ layoutData, setLayoutData ] = useState( null );
	const [ layoutDataMap, setLayoutDataMap ] = useState( {} );
	const [ loading, setLoading ] = useState( true );
	const [ applying, setApplying ] = useState( false );

	// Get settings from WordPress.
	const settings = window.dashmateLayouts || {};

	// Fetch layouts on component mount.
	useEffect( () => {
		fetchLayouts();
	}, [] );

	// Update layout data when selected layout changes.
	useEffect( () => {
		if ( selectedLayout && layoutDataMap[ selectedLayout ] ) {
			setLayoutData( layoutDataMap[ selectedLayout ] );
		} else if ( selectedLayout === 'current' ) {
			// For current layout, we need to fetch it separately
			fetchLayoutData( selectedLayout );
		}
	}, [ selectedLayout, layoutDataMap ] );

	const fetchLayouts = async () => {
		try {
			// Ensure REST URL ends with slash
			const restUrl = settings.restUrl.endsWith( '/' )
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
				throw new Error( settings.strings?.failedToFetch || 'Failed to fetch layouts' );
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

				// Set initial layout data for current layout
				if ( layoutDataMap[ 'current' ] ) {
					setLayoutData( layoutDataMap[ 'current' ] );
				}
			} else {
				throw new Error(
					data.message || settings.strings?.failedToFetch || 'Failed to fetch layouts'
				);
			}
		} catch ( error ) {
			setApplyButtonMessage( { type: 'error', text: error.message } );
			setTimeout( () => {
				setApplyButtonMessage( { type: '', text: '' } );
			}, 3000 );
		} finally {
			setLoading( false );
		}
	};

	const fetchLayoutData = async ( layoutKey ) => {
		try {
			setLoading( true );
			// Ensure REST URL ends with slash
			const restUrl = settings.restUrl.endsWith( '/' )
				? settings.restUrl
				: settings.restUrl + '/';

			// Prepare headers with nonce for security.
			const headers = {};

			// Add nonce header for authentication.
			if ( settings.nonce ) {
				headers[ 'X-WP-Nonce' ] = settings.nonce;
			}

			const response = await fetch( `${ restUrl }layouts/${ layoutKey }`, {
				headers: headers,
				credentials: 'same-origin',
			} );
			if ( ! response.ok ) {
				throw new Error(
					settings.strings?.failedToFetchData || 'Failed to fetch layout data'
				);
			}
			const data = await response.json();
			if ( data.success ) {
				setLayoutData( data.data );
				// Update the layoutDataMap with the fetched data
				setLayoutDataMap( ( prev ) => ( {
					...prev,
					[ layoutKey ]: data.data,
				} ) );
			} else {
				throw new Error(
					data.message ||
						settings.strings?.failedToFetchData ||
						'Failed to fetch layout data'
				);
			}
		} catch ( error ) {
			// Use inline message for errors
			setApplyButtonMessage( { type: 'error', text: error.message } );
			setTimeout( () => {
				setApplyButtonMessage( { type: '', text: '' } );
			}, 3000 );
		} finally {
			setLoading( false );
		}
	};

	const applyLayout = async () => {
		if ( selectedLayout === 'current' ) {
			setApplyButtonMessage( {
				type: 'error',
				text:
					settings.strings?.currentReadOnly ||
					'Cannot apply current layout as it is read-only.',
			} );
			// Clear error message after 3 seconds
			setTimeout( () => {
				setApplyButtonMessage( { type: '', text: '' } );
			}, 3000 );
			return;
		}

		try {
			setApplying( true );
			setApplyButtonMessage( { type: '', text: '' } );

			// Prepare headers with nonce for security.
			const headers = {
				'Content-Type': 'application/json',
			};

			// Add nonce header for authentication.
			if ( settings.nonce ) {
				headers[ 'X-WP-Nonce' ] = settings.nonce;
			}

			// Ensure REST URL ends with slash
			const restUrl = settings.restUrl.endsWith( '/' )
				? settings.restUrl
				: settings.restUrl + '/';
			const applyUrl = `${ restUrl }layouts/${ selectedLayout }/apply`;

			const response = await fetch( applyUrl, {
				method: 'POST',
				headers: headers,
				credentials: 'same-origin',
			} );

			const data = await response.json();

			if ( data.success ) {
				// Decode HTML entities in the message.
				const decodedMessage = decodeHTMLEntities( data.data.message );
				setApplyButtonMessage( { type: 'success', text: decodedMessage } );
				// Clear success message after 3 seconds
				setTimeout( () => {
					setApplyButtonMessage( { type: '', text: '' } );
				}, 3000 );
			} else {
				throw new Error(
					data.message || settings.strings?.failedToApply || 'Failed to apply layout'
				);
			}
		} catch ( error ) {
			setApplyButtonMessage( { type: 'error', text: error.message } );
			// Clear error message after 3 seconds
			setTimeout( () => {
				setApplyButtonMessage( { type: '', text: '' } );
			}, 3000 );
		} finally {
			setApplying( false );
		}
	};

	// Function to decode HTML entities.
	const decodeHTMLEntities = ( text ) => {
		const textarea = document.createElement( 'textarea' );
		textarea.innerHTML = text;
		return textarea.value;
	};

	const [ copyButtonText, setCopyButtonText ] = useState( settings.strings?.copyJson || 'Copy' );
	const [ applyButtonMessage, setApplyButtonMessage ] = useState( { type: '', text: '' } );
	const [ copyButtonMessage, setCopyButtonMessage ] = useState( { type: '', text: '' } );

	const copyToClipboard = async () => {
		if ( ! layoutData ) return;

		try {
			const jsonString = JSON.stringify( layoutData, null, 2 );
			await navigator.clipboard.writeText( jsonString );

			// Change button text to "Copied" for 2 seconds
			setCopyButtonText( 'Copied' );
			setTimeout( () => {
				setCopyButtonText( settings.strings?.copyJson || 'Copy' );
			}, 2000 );
		} catch ( error ) {
			setCopyButtonMessage( {
				type: 'error',
				text: settings.strings?.failedToCopy || 'Failed to copy to clipboard',
			} );
			// Clear error message after 3 seconds
			setTimeout( () => {
				setCopyButtonMessage( { type: '', text: '' } );
			}, 3000 );
		}
	};

	const handleLayoutSelect = ( layoutKey ) => {
		setSelectedLayout( layoutKey );
	};

	if ( loading && Object.keys( layouts ).length === 0 ) {
		return (
			<div className="dashmate-layouts">
				<div className="dashmate-layouts-json-loading">
					{ settings.strings?.loading || 'Loading layouts...' }
				</div>
			</div>
		);
	}

	return (
		<div className="dashmate-layouts">
			<div className="dashmate-layouts-controls">
				<div className="dashmate-layouts-selector">
					<label className="dashmate-layouts-selector-label">
						{ settings.strings?.selectLayout || 'Select Layout:' }
					</label>
				</div>

				<div className="dashmate-layouts-apply-container">
					<button
						type="button"
						className="button button-primary dashmate-layouts-apply-btn"
						onClick={ applyLayout }
						disabled={ selectedLayout === 'current' || applying || loading }
					>
						{ applying
							? settings.strings?.applying || 'Applying...'
							: settings.strings?.applyLayout || 'Apply Layout' }
					</button>
					{ applyButtonMessage.text && (
						<div
							className={ `dashmate-layouts-inline-message dashmate-layouts-inline-message--${ applyButtonMessage.type }` }
						>
							<span
								className={ `dashmate-layouts-inline-message-icon dashmate-layouts-inline-message-icon--${ applyButtonMessage.type }` }
							>
								{ applyButtonMessage.type === 'success' ? '✓' : '✗' }
							</span>
							{ applyButtonMessage.text }
						</div>
					) }
				</div>
			</div>

			{ /* Layout Grid */ }
			<div className="dashmate-layouts-grid">
				{ Object.entries( layouts ).map( ( [ key, layout ] ) => (
					<div
						key={ key }
						className={ `dashmate-layout-grid-item ${
							selectedLayout === key ? 'dashmate-layout-grid-item--selected' : ''
						}` }
						onClick={ () => handleLayoutSelect( key ) }
					>
						<div className="dashmate-layout-grid-item-header">
							<h4 className="dashmate-layout-grid-item-title">{ layout.title }</h4>
						</div>
						<LayoutPreview layoutData={ layoutDataMap[ key ] } selectedLayout={ key } />
					</div>
				) ) }
			</div>

			<div className="dashmate-layouts-content">
				<div className="dashmate-layouts-header">
					<div className="dashmate-layouts-copy-container">
						<button
							type="button"
							className="button button-secondary dashmate-layouts-header-copy-btn"
							onClick={ copyToClipboard }
							disabled={ ! layoutData || loading }
						>
							{ copyButtonText }
						</button>
						{ copyButtonMessage.text && (
							<div
								className={ `dashmate-layouts-inline-message dashmate-layouts-inline-message--${ copyButtonMessage.type }` }
							>
								<span
									className={ `dashmate-layouts-inline-message-icon dashmate-layouts-inline-message-icon--${ copyButtonMessage.type }` }
								>
									{ copyButtonMessage.type === 'success' ? '✓' : '✗' }
								</span>
								{ copyButtonMessage.text }
							</div>
						) }
					</div>
				</div>

				<div className="dashmate-layouts-json">
					{ loading ? (
						<div className="dashmate-layouts-json-loading">
							{ settings.strings?.loadingData || 'Loading layout data...' }
						</div>
					) : layoutData ? (
						<CodeHighlight
							code={ JSON.stringify( layoutData, null, 2 ) }
							language="json"
							showLineNumbers={ true }
						/>
					) : (
						<div className="dashmate-layouts-json-no-data">
							{ settings.strings?.noDataAvailable || 'No layout data available' }
						</div>
					) }
				</div>
			</div>
		</div>
	);
};

// Initialize the React app when DOM is ready.
document.addEventListener( 'DOMContentLoaded', () => {
	const container = document.getElementById( 'dashmate-layouts-app' );
	if ( container ) {
		const root = createRoot( container );
		root.render( <LayoutsApp /> );
	}
} );

export default LayoutsApp;
