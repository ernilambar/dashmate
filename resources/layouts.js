/**
 * Layout page functionality.
 *
 * @package Dashmate
 */

import { useState, useEffect } from 'react';
import { createRoot } from 'react-dom/client';
import LayoutPreview from './components/LayoutPreview';
import JsonHighlight from './components/JsonHighlight';
import './css/layouts.css';
import './css/layout-preview.css';

const LayoutsApp = () => {
	const [ layouts, setLayouts ] = useState( [] );
	const [ selectedLayout, setSelectedLayout ] = useState( 'current' );
	const [ layoutData, setLayoutData ] = useState( null );
	const [ layoutDataMap, setLayoutDataMap ] = useState( {} );
	const [ loading, setLoading ] = useState( true );
	const [ applying, setApplying ] = useState( false );
	const [ message, setMessage ] = useState( { type: '', text: '' } );

	// Get settings from WordPress.
	const settings = window.dashmateLayouts || {};

	// Fetch layouts on component mount.
	useEffect( () => {
		fetchLayouts();
	}, [] );

	// Fetch layout data when selected layout changes.
	useEffect( () => {
		if ( selectedLayout ) {
			fetchLayoutData( selectedLayout );
		}
	}, [ selectedLayout ] );

	const fetchLayouts = async () => {
		try {
			// Ensure REST URL ends with slash
			const restUrl = settings.restUrl.endsWith( '/' )
				? settings.restUrl
				: settings.restUrl + '/';
			const response = await fetch( `${ restUrl }layouts` );
			if ( ! response.ok ) {
				throw new Error( settings.strings?.failedToFetch || 'Failed to fetch layouts' );
			}
			const data = await response.json();
			if ( data.success ) {
				setLayouts( data.data );
				// Fetch layout data for each layout
				fetchAllLayoutData( data.data );
			} else {
				throw new Error(
					data.message || settings.strings?.failedToFetch || 'Failed to fetch layouts'
				);
			}
		} catch ( error ) {
			setMessage( { type: 'error', text: error.message } );
		} finally {
			setLoading( false );
		}
	};

	const fetchAllLayoutData = async ( layoutsData ) => {
		const dataMap = {};
		// Ensure REST URL ends with slash
		const restUrl = settings.restUrl.endsWith( '/' )
			? settings.restUrl
			: settings.restUrl + '/';
		const promises = Object.entries( layoutsData ).map( async ( [ key, layout ] ) => {
			try {
				const response = await fetch( `${ restUrl }layouts/${ key }` );
				if ( response.ok ) {
					const data = await response.json();
					if ( data.success ) {
						dataMap[ key ] = data.data;
					}
				}
			} catch ( error ) {
				console.warn( `Failed to fetch layout data for ${ key }:`, error );
			}
		} );

		await Promise.all( promises );
		setLayoutDataMap( dataMap );
	};

	const fetchLayoutData = async ( layoutKey ) => {
		try {
			setLoading( true );
			// Ensure REST URL ends with slash
			const restUrl = settings.restUrl.endsWith( '/' )
				? settings.restUrl
				: settings.restUrl + '/';
			const response = await fetch( `${ restUrl }layouts/${ layoutKey }` );
			if ( ! response.ok ) {
				throw new Error(
					settings.strings?.failedToFetchData || 'Failed to fetch layout data'
				);
			}
			const data = await response.json();
			if ( data.success ) {
				setLayoutData( data.data );
			} else {
				throw new Error(
					data.message ||
						settings.strings?.failedToFetchData ||
						'Failed to fetch layout data'
				);
			}
		} catch ( error ) {
			setMessage( { type: 'error', text: error.message } );
		} finally {
			setLoading( false );
		}
	};

	const applyLayout = async () => {
		if ( selectedLayout === 'current' ) {
			setMessage( {
				type: 'error',
				text:
					settings.strings?.currentReadOnly ||
					'Cannot apply current layout as it is read-only.',
			} );
			return;
		}

		try {
			setApplying( true );
			setMessage( { type: '', text: '' } );

			// Prepare headers - only include nonce in production.
			const headers = {
				'Content-Type': 'application/json',
			};

			// Add nonce header only in production (not in debug mode).
			if ( ! settings.isDebug && settings.nonce ) {
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
				setMessage( { type: 'success', text: decodedMessage } );
			} else {
				throw new Error(
					data.message || settings.strings?.failedToApply || 'Failed to apply layout'
				);
			}
		} catch ( error ) {
			setMessage( { type: 'error', text: error.message } );
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

	const copyToClipboard = async () => {
		if ( ! layoutData ) return;

		try {
			const jsonString = JSON.stringify( layoutData, null, 2 );
			await navigator.clipboard.writeText( jsonString );
			setMessage( {
				type: 'success',
				text: settings.strings?.copiedToClipboard || 'Layout JSON copied to clipboard!',
			} );
		} catch ( error ) {
			setMessage( {
				type: 'error',
				text: settings.strings?.failedToCopy || 'Failed to copy to clipboard',
			} );
		}
	};

	const handleLayoutSelect = ( layoutKey ) => {
		setSelectedLayout( layoutKey );
	};

	const clearMessage = () => {
		setMessage( { type: '', text: '' } );
	};

	// Auto-clear success messages after 3 seconds.
	useEffect( () => {
		if ( message.type === 'success' ) {
			const timer = setTimeout( clearMessage, 3000 );
			return () => clearTimeout( timer );
		}
	}, [ message ] );

	if ( loading && ! layoutData ) {
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
			{ message.text && (
				<div
					className={ `dashmate-layouts-message dashmate-layouts-message--${ message.type }` }
				>
					{ message.text }
					<button
						type="button"
						className="dashmate-layouts-message-close"
						onClick={ clearMessage }
					>
						×
					</button>
				</div>
			) }

			<div className="dashmate-layouts-controls">
				<div className="dashmate-layouts-selector">
					<label className="dashmate-layouts-selector-label">
						{ settings.strings?.selectLayout || 'Select Layout:' }
					</label>
				</div>

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
					<button
						type="button"
						className="button button-secondary dashmate-layouts-header-copy-btn"
						onClick={ copyToClipboard }
						disabled={ ! layoutData || loading }
					>
						{ settings.strings?.copyJson || 'Copy' }
					</button>
				</div>

				<div className="dashmate-layouts-json">
					{ loading ? (
						<div className="dashmate-layouts-json-loading">
							{ settings.strings?.loadingData || 'Loading layout data...' }
						</div>
					) : layoutData ? (
						<JsonHighlight data={ layoutData } />
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
