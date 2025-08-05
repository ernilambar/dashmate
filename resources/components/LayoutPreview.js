import React from 'react';

const LayoutPreview = ( { layoutData, selectedLayout } ) => {
	// Handle both direct structure and nested layout structure
	const columns = layoutData?.layout?.columns || layoutData?.columns;
	const widgets = layoutData?.widgets;
	const column_widgets = layoutData?.column_widgets;

	if ( ! layoutData || ! columns || ! widgets || ! column_widgets ) {
		return (
			<div className="dashmate-layout-preview">
				<div className="dashmate-layout-preview-empty">
					<p>No layout data available for preview</p>
				</div>
			</div>
		);
	}

	return (
		<div className="dashmate-layout-preview">
			<div className="dashmate-layout-preview-content">
				{ columns.map( ( column ) => {
					const columnWidgetIds = column_widgets[ column.id ] || [];
					const columnWidgets = columnWidgetIds
						.map( ( widgetId ) => widgets.find( ( widget ) => widget.id === widgetId ) )
						.filter( Boolean );

					return (
						<div key={ column.id } className="dashmate-layout-preview-column">
							<div className="dashmate-layout-preview-widgets">
								{ columnWidgets.length > 0 ? (
									columnWidgets.map( ( widget, index ) => (
										<div
											key={ `${ column.id }-${ widget.id }` }
											className="dashmate-layout-preview-widget"
										>
											<div className="dashmate-layout-preview-widget-title">
												{ widget.id }
											</div>
										</div>
									) )
								) : (
									<div className="dashmate-layout-preview-empty-widget">
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
