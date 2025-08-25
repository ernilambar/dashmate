# Layout Endpoints

## Overview

Layout endpoints provide functionality for managing saved dashboard layouts, including creating, retrieving, updating, and deleting layouts.

## GET /layouts

Retrieves a list of all saved layouts.

**Response:**
```json
{
  "success": true,
  "data": {
    "layouts": [
      {
        "id": 1,
        "name": "Default Layout",
        "description": "Default dashboard layout",
        "created_at": "2024-01-01T00:00:00Z",
        "updated_at": "2024-01-01T00:00:00Z"
      },
      {
        "id": 2,
        "name": "Analytics Layout",
        "description": "Layout focused on analytics widgets",
        "created_at": "2024-01-02T00:00:00Z",
        "updated_at": "2024-01-02T00:00:00Z"
      }
    ]
  }
}
```

## POST /layouts

Creates a new saved layout.

**Request Body:**
```json
{
  "name": "New Layout",
  "description": "A new dashboard layout",
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
    "id": 3,
    "name": "New Layout",
    "description": "A new dashboard layout",
    "created_at": "2024-01-03T00:00:00Z",
    "updated_at": "2024-01-03T00:00:00Z"
  }
}
```

## GET /layouts/{id}

Retrieves a specific saved layout by ID.

**Parameters:**
- `id` (integer, required): Layout ID

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Default Layout",
    "description": "Default dashboard layout",
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
    ],
    "created_at": "2024-01-01T00:00:00Z",
    "updated_at": "2024-01-01T00:00:00Z"
  }
}
```

## PUT /layouts/{id}

Updates an existing saved layout.

**Parameters:**
- `id` (integer, required): Layout ID

**Request Body:**
```json
{
  "name": "Updated Layout Name",
  "description": "Updated layout description",
  "columns": [
    {
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
    "id": 1,
    "name": "Updated Layout Name",
    "description": "Updated layout description",
    "updated_at": "2024-01-03T00:00:00Z"
  }
}
```

## DELETE /layouts/{id}

Deletes a saved layout.

**Parameters:**
- `id` (integer, required): Layout ID

**Response:**
```json
{
  "success": true,
  "data": {
    "message": "Layout deleted successfully"
  }
}
```

## Error Responses

### Layout Not Found
```json
{
  "code": "layout_not_found",
  "message": "Layout not found",
  "data": {
    "status": 404
  }
}
```

### Invalid Request
```json
{
  "code": "invalid_request",
  "message": "Invalid layout data",
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
