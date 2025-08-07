import React from 'react';

const LayoutPreview = ( { layoutData, selectedLayout } ) => {
	// Handle both direct structure and nested layout structure
	const columns = layoutData?.columns;
	const widgets = layoutData?.widgets;
	const column_widgets = layoutData?.column_widgets;

	if ( ! layoutData || ! columns || ! widgets || ! column_widgets ) {
		return (
			<div className="layout-preview">
				<div className="layout-preview-empty">
					<p>No layout data available for preview</p>
				</div>
			</div>
		);
	}

	return (
		<div className="layout-preview">
			<div className="layout-preview-content">
				{ columns.map( ( column ) => {
					const columnWidgetIds = column_widgets[ column.id ] || [];
					const columnWidgets = columnWidgetIds
						.map( ( widgetId ) => widgets.find( ( widget ) => widget.id === widgetId ) )
						.filter( Boolean );

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
