# Widget Endpoints

## Overview

Widget endpoints provide functionality for managing individual widgets, including retrieving widget configurations and updating widget settings.

## GET /widgets

Retrieves a list of all available widget types and their configurations.

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

## GET /widgets/{id}

Retrieves the configuration for a specific widget type.

**Parameters:**
- `id` (string, required): Widget type identifier

**Response:**
```json
{
  "success": true,
  "data": {
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
}
```

## PUT /widgets/{id}

Updates the settings for a specific widget instance.

**Parameters:**
- `id` (string, required): Widget instance identifier

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
    "id": "widget-1",
    "type": "html",
    "settings": {
      "title": "Updated Widget Title",
      "content": "Updated widget content"
    },
    "collapsed": false
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
