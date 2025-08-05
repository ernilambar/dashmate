import React from 'react';

const LayoutPreview = ( { layoutData, selectedLayout } ) => {
	// Debug: Log the layout data structure
	console.log( 'LayoutPreview - layoutData:', layoutData );
	console.log( 'LayoutPreview - selectedLayout:', selectedLayout );

	// Handle both direct structure and nested layout structure
	const columns = layoutData?.layout?.columns || layoutData?.columns;
	const widgets = layoutData?.widgets;
	const column_widgets = layoutData?.column_widgets;

	console.log( 'LayoutPreview - extracted data:', { columns, widgets, column_widgets } );

	if ( ! layoutData || ! columns || ! widgets || ! column_widgets ) {
		return (
			<div className="dashmate-layout-preview">
				<div className="dashmate-layout-preview-empty">
					<p>No layout data available for preview</p>
					{ layoutData && (
						<details style={ { marginTop: '10px', fontSize: '12px' } }>
							<summary>Debug: Layout data structure</summary>
							<pre>{ JSON.stringify( layoutData, null, 2 ) }</pre>
						</details>
					) }
					{ ! layoutData && (
						<p style={ { fontSize: '12px', color: '#666' } }>
							Debug: layoutData is null or undefined
						</p>
					) }
				</div>
			</div>
		);
	}

	return (
		<div className="dashmate-layout-preview">
			<div className="dashmate-layout-preview-header">
				<h4>Layout Preview</h4>
				<span className="dashmate-layout-preview-layout-name">
					{ selectedLayout !== 'current' ? selectedLayout : 'Current Layout' }
				</span>
			</div>

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
