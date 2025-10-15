# DashMate API Reference

## Overview

The DashMate API provides REST endpoints for managing dashboards and widgets. All endpoints are prefixed with `/wp-json/dashmate/v1/`.

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

## Dashboard Endpoints

### GET /dashboards/{dashboard_id}

Retrieves the complete dashboard data including columns and their widgets.

**Parameters:**
- `dashboard_id` (string, required): Dashboard identifier

**Response:**
```json
{
  "success": true,
  "data": {
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
}
```

### POST /dashboards/{dashboard_id}

Updates the dashboard layout with new column configuration.

**Parameters:**
- `dashboard_id` (string, required): Dashboard identifier

**Request Body:**
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

## Widget Endpoints

### GET /widgets

Retrieves a list of all available widget types and their configurations (global endpoint).

**Response:**
```json
{
  "success": true,
  "data": {
    "widgets": [
      {
        "id": "html",
        "title": "HTML Widget",
        "description": "Display custom HTML content",
        "fields": [
          {
            "id": "title",
            "type": "text",
            "label": "Title",
            "required": true
          },
          {
            "id": "content",
            "type": "textarea",
            "label": "Content",
            "required": true
          }
        ]
      }
    ]
  }
}
```

### GET /dashboards/{dashboard_id}/widgets

Retrieves all widgets for a specific dashboard.

**Parameters:**
- `dashboard_id` (string, required): Dashboard identifier

### GET /dashboards/{dashboard_id}/widgets/{widget_id}/content

Retrieves the content for a specific widget.

**Parameters:**
- `dashboard_id` (string, required): Dashboard identifier
- `widget_id` (string, required): Widget instance identifier

**Response:**
```json
{
  "success": true,
  "data": {
    "id": "widget-1",
    "type": "html",
    "content": "<div>Widget content</div>",
    "metadata": {
      "classes": ["widget", "html-widget"],
      "attributes": {}
    }
  }
}
```

### POST /dashboards/{dashboard_id}/widgets/{widget_id}/content

Retrieves widget content with custom settings (for preview purposes).

**Parameters:**
- `dashboard_id` (string, required): Dashboard identifier
- `widget_id` (string, required): Widget instance identifier

**Request Body:**
```json
{
  "settings": {
    "title": "Preview Title",
    "content": "Preview content"
  }
}
```

### POST /dashboards/{dashboard_id}/widgets/{widget_id}/settings

Updates the settings for a specific widget instance.

**Parameters:**
- `dashboard_id` (string, required): Dashboard identifier
- `widget_id` (string, required): Widget instance identifier

**Request Body:**
```json
{
  "settings": {
    "title": "Updated Widget Title",
    "content": "Updated widget content"
  }
}
```

## Available Widget Types

### HTML Widget
- **ID**: `html`
- **Description**: Display custom HTML content
- **Fields**: `title` (text), `content` (textarea)

### Links Widget
- **ID**: `links`
- **Description**: Display a list of links
- **Fields**: `title` (text), `links` (sortable)

### Progress Circles Widget
- **ID**: `progress_circles`
- **Description**: Display progress indicators
- **Fields**: `title` (text), `circles` (sortable)

### Tabular Widget
- **ID**: `tabular`
- **Description**: Display data in table format
- **Fields**: `title` (text), `headers` (array), `rows` (array)

### Line Chart Widget
- **ID**: `line_chart`
- **Description**: Display line charts
- **Fields**: `title` (text), `data` (object)

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


