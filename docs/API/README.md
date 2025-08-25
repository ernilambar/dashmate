# Dashmate API Reference

## Overview

The Dashmate API provides REST endpoints for managing dashboard layouts, widgets, and columns. All endpoints are prefixed with `/wp-json/dashmate/v1/`.

## Base Information

- **Namespace**: `dashmate/v1`
- **Base URL**: `/wp-json/dashmate/v1/`
- **Authentication**: WordPress nonce verification
- **Response Format**: JSON

## Response Format

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

## Endpoints

### Dashboard Management
- **[GET /dashboard](./endpoints/dashboard.md#get-dashboard)** - Retrieve dashboard data
- **[PUT /dashboard](./endpoints/dashboard.md#put-dashboard)** - Update dashboard layout
- **[PUT /dashboard/reorder](./endpoints/dashboard.md#put-dashboard-reorder)** - Reorder widgets

### Widget Management
- **[GET /widgets](./endpoints/widgets.md#get-widgets)** - List available widget types
- **[GET /widgets/{id}](./endpoints/widgets.md#get-widgets-id)** - Get widget configuration
- **[PUT /widgets/{id}](./endpoints/widgets.md#put-widgets-id)** - Update widget settings

### Layout Management
- **[GET /layouts](./endpoints/layouts.md#get-layouts)** - List saved layouts
- **[POST /layouts](./endpoints/layouts.md#post-layouts)** - Save new layout
- **[GET /layouts/{id}](./endpoints/layouts.md#get-layouts-id)** - Get specific layout
- **[PUT /layouts/{id}](./endpoints/layouts.md#put-layouts-id)** - Update layout
- **[DELETE /layouts/{id}](./endpoints/layouts.md#delete-layouts-id)** - Delete layout

### Column Management
- **[GET /columns](./endpoints/columns.md#get-columns)** - List columns
- **[POST /columns](./endpoints/columns.md#post-columns)** - Create column
- **[GET /columns/{id}](./endpoints/columns.md#get-columns-id)** - Get specific column
- **[PUT /columns/{id}](./endpoints/columns.md#put-columns-id)** - Update column
- **[DELETE /columns/{id}](./endpoints/columns.md#delete-columns-id)** - Delete column

## Data Structures

### Dashboard Structure
```json
{
  "columns": [
    {
      "id": "col-1",
      "widgets": [
        {
          "id": "widget-1",
          "type": "html",
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

### Widget Structure
```json
{
  "id": "widget-1",
  "type": "html",
  "settings": {
    "title": "Widget Title",
    "content": "Widget content"
  },
  "collapsed": false
}
```

## Error Codes

| Code | Description |
|------|-------------|
| `invalid_request` | Invalid request parameters |
| `widget_not_found` | Widget not found |
| `layout_not_found` | Layout not found |
| `column_not_found` | Column not found |
| `permission_denied` | Insufficient permissions |

## Authentication

All API requests require a valid WordPress nonce. Include the nonce in the `X-WP-Nonce` header:

```
X-WP-Nonce: your-nonce-value
```
