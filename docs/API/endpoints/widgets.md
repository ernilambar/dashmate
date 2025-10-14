# Widget Endpoints

## Overview

Widget endpoints provide functionality for managing individual widgets, including retrieving widget configurations and updating widget settings.

## GET /widgets

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
      },
      {
        "id": "links",
        "title": "Links Widget",
        "description": "Display a list of links",
        "fields": [
          {
            "id": "title",
            "type": "text",
            "label": "Title",
            "required": true
          },
          {
            "id": "links",
            "type": "sortable",
            "label": "Links",
            "required": true
          }
        ]
      }
    ]
  }
}
```

## GET /dashboards/{dashboard_id}/widgets

Retrieves all widgets for a specific dashboard.

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

## GET /dashboards/{dashboard_id}/widgets/{widget_id}/content

Retrieves the content for a specific widget in a specific dashboard.

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

## POST /dashboards/{dashboard_id}/widgets/{widget_id}/content

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

**Response:**
```json
{
  "success": true,
  "data": {
    "id": "widget-1",
    "type": "html",
    "content": "<div>Preview content</div>"
  }
}
```

## POST /dashboards/{dashboard_id}/widgets/{widget_id}/settings

Updates the settings for a specific widget instance in a specific dashboard.

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

**Response:**
```json
{
  "success": true,
  "data": {
    "message": "Settings saved successfully"
  }
}
```

## Error Responses

### Widget Not Found
```json
{
  "code": "widget_not_found",
  "message": "Widget not found",
  "data": {
    "status": 404
  }
}
```

### Invalid Settings
```json
{
  "code": "invalid_request",
  "message": "Invalid widget settings",
  "data": {
    "status": 400
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
