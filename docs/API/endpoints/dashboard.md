# Dashboard Endpoints

## Overview

Dashboard endpoints provide functionality for managing the overall dashboard layout, including retrieving dashboard data, updating layouts, and reordering widgets.

## GET /dashboard

Retrieves the complete dashboard data including columns and their widgets.

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

## PUT /dashboard

Updates the dashboard layout with new column configuration.

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

## PUT /dashboard/reorder

Reorders widgets across columns using a column_widgets structure.

**Request Body:**
```json
{
  "column_widgets": {
    "col-1": ["widget-1", "widget-3"],
    "col-2": ["widget-2", "widget-4"]
  }
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "message": "Widgets reordered successfully"
  }
}
```

## Error Responses

### Invalid Request
```json
{
  "code": "invalid_request",
  "message": "Invalid request parameters",
  "data": {
    "status": 400
  }
}
```

### Permission Denied
```json
{
  "code": "permission_denied",
  "message": "Insufficient permissions",
  "data": {
    "status": 403
  }
}
```
