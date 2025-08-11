# Getting Started

## Overview

The Dashmate API provides REST endpoints for managing dashboard layouts, widgets, and columns. This guide covers the basic concepts you need to understand before using the API.

## Base Information

- **Namespace**: `dashmate/v1`
- **Base URL**: `/wp-json/dashmate/v1/`
- **Authentication**: Currently allows all requests for development
- **Response Format**: JSON

## Response Format

All API responses follow a consistent format:

### Success Response
```json
{
  "success": true,
  "data": {
    // Response data
  }
}
```

### Error Response
```json
{
  "code": "error_code",
  "message": "Error message",
  "data": {
    "status": 400
  }
}
```

## Layout Structure

Dashmate uses a column-based layout system where widgets are directly nested within columns:

```json
{
  "columns": [
    {
      "id": "col-1",
      "widgets": [
        {
          "id": "widget-1",
          "settings": {
            "title": "Widget Title",
            "content": "Widget content"
          },
          "collapsed": false
        }
      ]
    }
  ]
}
```

### Key Concepts

- **Columns**: Containers that hold widgets in a vertical layout
- **Widgets**: Individual components that display content or functionality
- **Settings**: Configuration data for each widget
- **Collapsed State**: Whether a widget is minimized or expanded

### Data Structure

- **Column ID**: Must be alphanumeric with hyphens allowed
- **Widget ID**: Must be alphanumeric with hyphens and underscores allowed
- **Widget Order**: Determined by array position within each column's widgets array
- **Settings**: Validated against widget-specific schemas

## Next Steps

- Review [Dashboard Endpoints](../endpoints/dashboard.md) for core functionality
- Explore [Widget Types](../reference/widget-types.md) to understand available widgets
- Check [Error Handling](../error-handling.md) for troubleshooting
