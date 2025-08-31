import { Component } from 'react';
import { __ } from '@wordpress/i18n';
import Icon from './Icon';

class LayoutSaver extends Component {
	constructor( props ) {
		super( props );
		this.state = {
			isOpen: false,
			saveButtonState: 'idle', // 'idle', 'saving', 'success', 'error'
			saveButtonMessage: '',
			layouts: null,
			selectedLayout: null,
		};
	}

	componentDidMount() {
		// Close dropdown when clicking outside
		document.addEventListener( 'click', this.handleClickOutside );
		this.loadLayouts();
	}

	componentWillUnmount() {
		document.removeEventListener( 'click', this.handleClickOutside );
	}

	handleClickOutside = ( event ) => {
		if ( this.dropdownRef && ! this.dropdownRef.contains( event.target ) ) {
			this.setState( { isOpen: false } );
		}
	};

	async loadLayouts() {
		try {
			const response = await fetch( `${ dashmateApiSettings.restUrl }layouts`, {
				headers: {
					'X-WP-Nonce': dashmateApiSettings?.nonce || '',
				},
			} );
			const data = await response.json();

			if ( data.success ) {
				// Filter only custom layouts
				const customLayouts = {};
				Object.entries( data.data ).forEach( ( [ key, layout ] ) => {
					if ( layout.type === 'custom' ) {
						customLayouts[ key ] = layout;
					}
				} );
				this.setState( { layouts: customLayouts } );
			}
		} catch ( error ) {
			// Handle error silently
		}
	}

	toggleDropdown = () => {
		this.setState( ( prevState ) => ( { isOpen: ! prevState.isOpen } ) );
	};

	handleLayoutSelect = ( layoutKey ) => {
		this.setState( { selectedLayout: layoutKey } );
	};

	handleSaveLayout = async () => {
		const { dashboard, onLayoutSaved } = this.props;
		const { selectedLayout } = this.state;

		if ( ! dashboard || ! selectedLayout ) {
			return;
		}

		this.setState( {
			saveButtonState: 'saving',
			saveButtonMessage: '',
		} );

		try {
			// Try to create the layout first (POST request)
			let response = await fetch( `${ dashmateApiSettings.restUrl }custom-layouts`, {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					'X-WP-Nonce': dashmateApiSettings?.nonce || '',
				},
				body: JSON.stringify( {
					key: selectedLayout,
					data: dashboard,
				} ),
			} );

			let data = await response.json();

			// If creation fails with "already exists" error, try to update instead
			if ( ! data.success && data.code === 'layout_already_exists' ) {
				response = await fetch(
					`${ dashmateApiSettings.restUrl }custom-layouts/${ selectedLayout }`,
					{
						method: 'PUT',
						headers: {
							'Content-Type': 'application/json',
							'X-WP-Nonce': dashmateApiSettings?.nonce || '',
						},
						body: JSON.stringify( {
							data: dashboard,
						} ),
					}
				);

				data = await response.json();
			}

			if ( data.success ) {
				this.setState( {
					saveButtonState: 'success',
					saveButtonMessage: 'Saved successfully!',
					isOpen: false,
					selectedLayout: null,
				} );

				// Call the callback to notify parent component
				if ( onLayoutSaved ) {
					onLayoutSaved();
				}

				// Reset button state after 3 seconds
				setTimeout( () => {
					this.setState( {
						saveButtonState: 'idle',
						saveButtonMessage: '',
					} );
				}, 3000 );
			} else {
				this.setState( {
					saveButtonState: 'error',
					saveButtonMessage: data.message || 'Failed to save layout',
				} );

				// Reset error message after 5 seconds
				setTimeout( () => {
					this.setState( {
						saveButtonMessage: '',
					} );
				}, 5000 );
			}
		} catch ( error ) {
			this.setState( {
				saveButtonState: 'error',
				saveButtonMessage: 'Error saving layout',
			} );

			// Reset error message after 5 seconds
			setTimeout( () => {
				this.setState( {
					saveButtonMessage: '',
				} );
			}, 5000 );
		}
	};

	render() {
		const { isOpen, saveButtonState, saveButtonMessage, layouts, selectedLayout } = this.state;

		// Get custom layout list
		const customLayouts = [];
		if ( layouts && typeof layouts === 'object' ) {
			Object.entries( layouts ).forEach( ( [ key, layout ] ) => {
				customLayouts.push( {
					id: key,
					title: layout.title || key,
				} );
			} );
		}

		return (
			<div className="layout-saver" ref={ ( ref ) => ( this.dropdownRef = ref ) }>
				<button
					className={ `layout-saver-toggle ${ saveButtonState }` }
					onClick={ this.toggleDropdown }
					title="Save Layout"
					disabled={ saveButtonState === 'saving' }
				>
					<Icon name="save-3-line" size="large" />
				</button>
				{ isOpen && (
					<div className="layout-saver-dropdown">
						<div className="layout-saver-header">
							<h4>Save Layout</h4>
						</div>
						<div className="layout-saver-content">
							{ customLayouts.length > 0 ? (
								<>
									<div className="layout-saver-list">
										{ customLayouts.map( ( layout ) => (
											<label key={ layout.id } className="layout-saver-item">
												<input
													type="radio"
													name="layout-selection"
													value={ layout.id }
													checked={ selectedLayout === layout.id }
													onChange={ () =>
														this.handleLayoutSelect( layout.id )
													}
												/>
												<span className="layout-saver-item-title">
													{ layout.title }
												</span>
											</label>
										) ) }
									</div>
									<div className="layout-saver-actions">
										<button
											className="layout-saver-btn"
											onClick={ this.handleSaveLayout }
											disabled={
												saveButtonState === 'saving' || ! selectedLayout
											}
										>
											{ saveButtonState === 'saving'
												? 'Saving...'
												: 'Save Layout' }
										</button>
										{ saveButtonState === 'error' && saveButtonMessage && (
											<div className="layout-saver-error">
												{ saveButtonMessage }
											</div>
										) }
									</div>
								</>
							) : (
								<div className="layout-saver-empty">
									<p>No custom layouts available</p>
									<p className="layout-saver-empty-hint">
										Create custom layouts in the Layouts page first
									</p>
								</div>
							) }
						</div>
					</div>
				) }
			</div>
		);
	}
}

export default LayoutSaver;
