# Widget Types

## Overview

The Dashmate API supports various widget types, each designed for specific use cases and data display requirements. This reference covers all available widget types and their configurations.

## Available Widget Types

### HTML Widget (`html`)

**Description**: Displays custom HTML content with full styling capabilities.

**Settings**:
- `title` (string): Widget title
- `content` (string): HTML content to display

**Template**: `html`

**Example Settings**:
```json
{
  "title": "Welcome Message",
  "content": "<h1>Welcome to Dashboard</h1><p>This is a custom HTML widget.</p>"
}
```

**Use Cases**:
- Custom welcome messages
- Rich text content
- Embedded HTML components
- Custom styling and formatting

### Quick Links Widget (`links`)

**Description**: Displays a list of quick links for easy navigation.

**Settings**:
- `title` (string): Widget title
- `links` (array): Array of link objects

**Template**: `links`

**Example Settings**:
```json
{
  "title": "Quick Links",
  "links": [
    {
      "title": "Dashboard",
      "url": "/dashboard",
      "icon": "dashboard"
    },
    {
      "title": "Settings",
      "url": "/settings",
      "icon": "settings"
    }
  ]
}
```

**Link Object Structure**:
- `title` (string): Link display text
- `url` (string): Link destination URL
- `icon` (string, optional): Icon identifier

**Use Cases**:
- Navigation shortcuts
- Frequently accessed pages
- External links
- Internal site navigation

### Sales Widget (`tabular`)

**Description**: Displays sales statistics in a tabular format.

**Settings**:
- `title` (string): Widget title
- `data` (object): Tabular data structure

**Template**: `tabular`

**Example Settings**:
```json
{
  "title": "Sales Overview",
  "data": {
    "headers": ["Product", "Sales", "Revenue"],
    "rows": [
      ["Product A", "150", "$1,500"],
      ["Product B", "200", "$2,000"],
      ["Product C", "100", "$1,000"]
    ]
  }
}
```

**Data Structure**:
- `headers` (array): Column headers
- `rows` (array): Data rows as arrays

**Use Cases**:
- Sales reports
- Financial data
- Statistical information
- Data tables

### Weekly Tickets Widget (`progress-circles`)

**Description**: Displays weekly ticket statistics with progress circles.

**Settings**:
- `title` (string): Widget title
- `data` (object): Progress data structure

**Template**: `progress-circles`

**Example Settings**:
```json
{
  "title": "Weekly Tickets",
  "data": {
    "total": 100,
    "completed": 75,
    "pending": 15,
    "overdue": 10
  }
}
```

**Data Structure**:
- `total` (number): Total number of tickets
- `completed` (number): Completed tickets
- `pending` (number): Pending tickets
- `overdue` (number): Overdue tickets

**Use Cases**:
- Ticket tracking
- Progress monitoring
- Task completion
- Performance metrics

### Sample Bar Chart Widget (`bar-chart`)

**Description**: Displays sample bar chart data with configurable bars.

**Settings**:
- `bars_number` (number): Number of bars to display (3-12, default: 6)
- `hide_labels` (boolean): Hide labels below bars (default: false)
- `show_values` (boolean): Show values on top of bars (default: true)

**Template**: `bar-chart`

**Example Settings**:
```json
{
  "bars_number": 6,
  "hide_labels": false,
  "show_values": true
}
```

**Data Structure**:
- `items` (array): Array of bar objects
  - `value` (number): Bar value
  - `label` (string): Bar label
  - `color` (string): Bar color (hex code)

**Use Cases**:
- Monthly statistics
- Performance metrics
- Sales data visualization
- Comparative analysis

## Widget Configuration

### Common Settings

All widget types support these common settings:

- **title** (string): Widget display title
- **collapsed** (boolean): Whether the widget is minimized (managed by dashboard)

### Widget-Specific Settings

Each widget type has its own settings schema that defines:

- Required fields
- Field types and validation
- Default values
- Field descriptions

### Settings Validation

Widget settings are validated against widget-specific schemas:

- Required fields must be provided
- Field types must match expected format
- Data structures must be valid
- Content must meet security requirements

## Widget Rendering

### Template System

Widgets use template-based rendering:

1. **Template Selection**: Based on widget's `template_type`
2. **Data Processing**: Settings are processed for display
3. **Content Generation**: Template renders final HTML
4. **Styling**: CSS classes are applied for consistent appearance

### Content Security

- HTML content is sanitized to prevent XSS attacks
- URLs are validated for security
- User input is properly escaped
- Script execution is prevented

## Creating Custom Widgets

### Widget Development

To create a new widget type:

1. **Define Widget Class**: Extend the base widget class
2. **Implement Template**: Create template file for rendering
3. **Define Settings Schema**: Specify configuration options
4. **Register Widget**: Add to widget registry
5. **Add Styling**: Create CSS for widget appearance

### Widget Registration

Widgets are automatically discovered and registered:

- Widget classes must extend `Abstract_Widget`
- Template files must be in the widgets directory
- Settings schemas must be defined
- Widget IDs must be unique

## Best Practices

### Widget Design

1. **Clear Purpose**: Each widget should have a specific, clear purpose
2. **Consistent Styling**: Follow design system guidelines
3. **Responsive Design**: Ensure widgets work on all screen sizes
4. **Performance**: Optimize for fast loading and rendering

### Content Management

1. **Data Validation**: Validate all user input
2. **Error Handling**: Provide meaningful error messages
3. **Default Values**: Set sensible defaults for all settings
4. **Documentation**: Document widget behavior and settings

### User Experience

1. **Intuitive Interface**: Make settings easy to understand
2. **Visual Feedback**: Provide clear visual indicators
3. **Accessibility**: Ensure widgets are accessible to all users
4. **Mobile Friendly**: Test on mobile devices

## Related Documentation

- [Field Types](./field-types.md) - Supported form field types
- [Widgets Endpoints](../endpoints/widgets.md) - Widget management API
- [Dashboard Endpoints](../endpoints/dashboard.md) - Dashboard layout management
