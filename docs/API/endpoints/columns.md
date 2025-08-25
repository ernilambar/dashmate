# Column Endpoints

## Overview

Column endpoints provide functionality for managing dashboard columns, including creating, retrieving, updating, and deleting columns.

## GET /columns

Retrieves a list of all columns in the current dashboard.

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
      },
      {
        "id": "col-2",
        "widgets": []
      }
    ]
  }
}
```

## POST /columns

Creates a new column in the dashboard.

**Request Body:**
```json
{
  "id": "col-3",
  "widgets": []
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "id": "col-3",
    "widgets": []
  }
}
```

## GET /columns/{id}

Retrieves a specific column by ID.

**Parameters:**
- `id` (string, required): Column ID

**Response:**
```json
{
  "success": true,
  "data": {
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
}
```

## PUT /columns/{id}

Updates an existing column.

**Parameters:**
- `id` (string, required): Column ID

**Request Body:**
```json
{
  "widgets": [
    {
      "id": "widget-1",
      "type": "html",
      "settings": {
        "title": "Updated Widget Title",
        "content": "Updated widget content"
      },
      "collapsed": false
    },
    {
      "id": "widget-2",
      "type": "links",
      "settings": {
        "title": "Links Widget",
        "links": []
      },
      "collapsed": false
    }
  ]
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "id": "col-1",
    "widgets": [
      {
        "id": "widget-1",
        "type": "html",
        "settings": {
          "title": "Updated Widget Title",
          "content": "Updated widget content"
        },
        "collapsed": false
      },
      {
        "id": "widget-2",
        "type": "links",
        "settings": {
          "title": "Links Widget",
          "links": []
        },
        "collapsed": false
      }
    ]
  }
}
```

## DELETE /columns/{id}

Deletes a column from the dashboard.

**Parameters:**
- `id` (string, required): Column ID

**Response:**
```json
{
  "success": true,
  "data": {
    "message": "Column deleted successfully"
  }
}
```

## Error Responses

### Column Not Found
```json
{
  "code": "column_not_found",
  "message": "Column not found",
  "data": {
    "status": 404
  }
}
```

### Invalid Request
```json
{
  "code": "invalid_request",
  "message": "Invalid column data",
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

## Column Structure

### Column Object
- **id** (string, required): Unique column identifier (alphanumeric, hyphens allowed)
- **widgets** (array): Array of widget objects

### Widget Object
- **id** (string, required): Unique widget identifier
- **type** (string, required): Widget type (html, links, progress_circles, tabular, line_chart)
- **settings** (object): Widget configuration data
- **collapsed** (boolean): Whether the widget is minimized
