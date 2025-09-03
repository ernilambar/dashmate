import React from 'react';

const LayoutPreview = ( { layoutData, selectedLayout } ) => {
	// Handle column-based layout where widgets are nested in columns
	const columns = layoutData?.columns;

	if ( ! layoutData || ! columns ) {
		return (
			<div className="layout-preview">
				<div className="layout-preview-empty">
					<p>No layout data available for preview</p>
				</div>
			</div>
		);
	}

	const columnCount = columns.length;
	const columnClass = `layout-preview-columns-${ columnCount }`;

	return (
		<div className="layout-preview">
			<div className={ `layout-preview-content ${ columnClass }` }>
				{ columns.map( ( column ) => {
					const columnWidgets = column.widgets || [];

					return (
						<div key={ column.id } className="layout-preview-column">
							<div className="layout-preview-widgets">
								{ columnWidgets.length > 0 ? (
									columnWidgets.map( ( widget, index ) => (
										<div
											key={ `${ column.id }-${ widget.id }` }
											className="layout-preview-widget"
										>
											<div className="layout-preview-widget-title">
												{ widget.id }
											</div>
										</div>
									) )
								) : (
									<div className="layout-preview-empty-widget">
										<span>No widgets</span>
									</div>
								) }
							</div>
						</div>
					);
				} ) }
			</div>
		</div>
	);
};

export default LayoutPreview;
