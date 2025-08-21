import React from 'react';
import { LineChart } from 'recharts/lib/chart/LineChart';
import { Line } from 'recharts/lib/cartesian/Line';
import { XAxis } from 'recharts/lib/cartesian/XAxis';
import { YAxis } from 'recharts/lib/cartesian/YAxis';
import { CartesianGrid } from 'recharts/lib/cartesian/CartesianGrid';
import { Tooltip } from 'recharts/lib/component/Tooltip';
import { ResponsiveContainer } from 'recharts/lib/component/ResponsiveContainer';
import { LabelList } from 'recharts/lib/component/LabelList';

class LineChartWidget extends React.Component {
	constructor( props ) {
		super( props );
		this.state = {
			maxValue: 0,
		};
	}

	componentDidMount() {
		this.calculateMaxValue();
	}

	componentDidUpdate( prevProps ) {
		if ( prevProps.data?.items !== this.props.data?.items ) {
			this.calculateMaxValue();
		}
	}

	calculateMaxValue = () => {
		const { items } = this.props.data || {};
		if ( ! items || ! Array.isArray( items ) ) {
			return;
		}

		const maxValue = Math.max( ...items.map( ( item ) => item.value || 0 ) );
		this.setState( { maxValue } );
	};

	renderLineChart = () => {
		const { items, chart_settings } = this.props.data || {};
		const { chart_title, chart_subtitle, x_axis_label, y_axis_label } = chart_settings || {};

		if ( ! items || ! Array.isArray( items ) || items.length === 0 ) {
			return null;
		}

		const chartData = items.map( ( item ) => ( {
			name: item.label,
			value: item.value,
			color: item.color,
		} ) );

		return (
			<div className="dm-line-chart-svg-container">
				{ chart_title && <div className="dm-line-chart-title">{ chart_title }</div> }
				{ chart_subtitle && (
					<div className="dm-line-chart-subtitle">{ chart_subtitle }</div>
				) }
				<ResponsiveContainer width="100%" height={ 300 }>
					<LineChart
						data={ chartData }
						margin={ { top: 20, right: 30, left: 20, bottom: 40 } }
					>
						<CartesianGrid
							strokeDasharray="3 3"
							stroke="#e0e0e0"
							opacity={ 0.6 }
							vertical={ true }
							horizontal={ true }
							strokeWidth={ 1 }
						/>
						<XAxis
							dataKey="name"
							axisLine={ { stroke: '#6c757d', strokeWidth: 1.5 } }
							tickLine={ { stroke: '#6c757d' } }
							tick={ {
								fontSize: 11,
								fill: '#495057',
								fontFamily: 'system-ui, -apple-system, sans-serif',
							} }
							{ ...( x_axis_label && {
								label: {
									value: x_axis_label,
									position: 'insideBottom',
									offset: -5,
									style: {
										textAnchor: 'middle',
										fontSize: '13px',
										fontWeight: '600',
										fill: '#212529',
										fontFamily: 'system-ui, -apple-system, sans-serif',
									},
								},
							} ) }
						/>
						<YAxis
							axisLine={ { stroke: '#6c757d', strokeWidth: 1.5 } }
							tickLine={ { stroke: '#6c757d' } }
							tick={ {
								fontSize: 11,
								fill: '#495057',
								fontFamily: 'system-ui, -apple-system, sans-serif',
							} }
							domain={ [ 0, 'dataMax + 10' ] }
							{ ...( y_axis_label && {
								label: {
									value: y_axis_label,
									angle: -90,
									position: 'insideLeft',
									style: {
										textAnchor: 'middle',
										fontSize: '13px',
										fontWeight: '600',
										fill: '#212529',
										fontFamily: 'system-ui, -apple-system, sans-serif',
									},
								},
							} ) }
						/>
						<Tooltip
							contentStyle={ {
								backgroundColor: '#ffffff',
								border: '1px solid #e9ecef',
								borderRadius: '6px',
								boxShadow: '0 2px 8px rgba(0, 0, 0, 0.1)',
								fontFamily: 'system-ui, -apple-system, sans-serif',
								fontSize: '12px',
							} }
							labelStyle={ { fontWeight: '600', color: '#212529' } }
							animationDuration={ 0 }
						/>
						<Line
							type="monotone"
							dataKey="value"
							stroke="#6facde"
							strokeWidth={ 3 }
							dot={ {
								fill: '#6facde',
								stroke: '#ffffff',
								strokeWidth: 2,
								r: 6,
							} }
							activeDot={ false }
							connectNulls={ true }
							isAnimationActive={ false }
						>
							<LabelList
								dataKey="value"
								position="top"
								style={ {
									fontSize: '11px',
									fontWeight: '500',
									fill: '#495057',
									fontFamily: 'system-ui, -apple-system, sans-serif',
								} }
								offset={ 8 }
							/>
						</Line>
					</LineChart>
				</ResponsiveContainer>
			</div>
		);
	};

	render() {
		const { data } = this.props;
		const { items } = data || {};

		if ( ! items || ! Array.isArray( items ) || items.length === 0 ) {
			return (
				<div className="dm-line-chart-widget">
					<div className="widget-no-data">
						<p>No chart data available.</p>
					</div>
				</div>
			);
		}

		return <div className="dm-line-chart-widget">{ this.renderLineChart() }</div>;
	}
}

export default LineChartWidget;
