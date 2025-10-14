# Dashmate API Reference

## Overview

The Dashmate API provides REST endpoints for managing dashboards and widgets. All endpoints are prefixed with `/wp-json/dashmate/v1/`.

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
- **[GET /dashboards/{dashboard_id}](./endpoints/dashboard.md#get-dashboards-dashboard_id)** - Retrieve dashboard data
- **[POST /dashboards/{dashboard_id}](./endpoints/dashboard.md#post-dashboards-dashboard_id)** - Update dashboard layout
- **[POST /dashboards/{dashboard_id}/reorder](./endpoints/dashboard.md#post-dashboards-dashboard_id-reorder)** - Reorder widgets

### Widget Management
- **[GET /widgets](./endpoints/widgets.md#get-widgets)** - List available widget types (global)
- **[GET /dashboards/{dashboard_id}/widgets](./endpoints/widgets.md#get-dashboards-dashboard_id-widgets)** - Get dashboard widgets
- **[GET /dashboards/{dashboard_id}/widgets/{widget_id}/content](./endpoints/widgets.md#get-dashboards-dashboard_id-widgets-widget_id-content)** - Get widget content
- **[POST /dashboards/{dashboard_id}/widgets/{widget_id}/content](./endpoints/widgets.md#post-dashboards-dashboard_id-widgets-widget_id-content)** - Get widget content with custom settings
- **[POST /dashboards/{dashboard_id}/widgets/{widget_id}/settings](./endpoints/widgets.md#post-dashboards-dashboard_id-widgets-widget_id-settings)** - Update widget settings

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
