# Dashmate API Documentation

## Overview

The Dashmate API provides REST endpoints for managing dashboard layouts, widgets, and columns. All endpoints are prefixed with `/wp-json/dashmate/v1/`.

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

## Endpoints

### Dashboard Endpoints

#### GET `/dashboard`
Retrieves the complete dashboard data including layout, widgets, and column widgets.

**Response:**
```json
{
  "success": true,
  "data": {
    "layout": {
      "columns": [
        {
          "id": "col-1",
          "order": 1,
          "width": "50%"
        }
      ]
    },
    "widgets": [
      {
        "id": "widget-1",
        "type": "html",
        "column_id": "col-1",
        "position": 1,
        "settings": {}
      }
    ],
    "column_widgets": {
      "col-1": ["widget-1", "widget-2"]
    }
  }
}
```

#### PUT `/dashboard`
Updates the dashboard layout with new column configuration.

**Request Body:**
```json
{
  "columns": [
    {
      "id": "col-1",
      "width": "50%"
    },
    {
      "id": "col-2",
      "width": "50%"
    }
  ]
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "layout": {
      "columns": [
        {
          "id": "col-1",
          "order": 1,
          "width": "50%"
        },
        {
          "id": "col-2",
          "order": 2,
          "width": "50%"
        }
      ]
    },
    "widgets": []
  }
}
```

#### PUT `/dashboard/reorder`
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

### Widgets Endpoints

#### GET `/widgets`
Retrieves all available widget types for the frontend.

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": "welcome-html",
      "title": "Welcome HTML",
      "description": "HTML content widget",
      "template_type": "html",
      "settings_schema": {}
    },
    {
      "id": "quick-links",
      "title": "Quick Links",
      "description": "Quick links widget",
      "template_type": "links",
      "settings_schema": {}
    },
    {
      "id": "sales",
      "title": "Sales",
      "description": "Sales statistics widget",
      "template_type": "tabular",
      "settings_schema": {}
    },
    {
      "id": "weekly-tickets",
      "title": "Weekly Tickets",
      "description": "Weekly tickets widget",
      "template_type": "progress-circles",
      "settings_schema": {}
    }
  ]
}
```

#### GET `/widgets/{id}/data`
Retrieves widget data including content and settings for a specific widget.

**Parameters:**
- `id` (string, required): Widget ID (alphanumeric, hyphens, underscores)

**Response:**
```json
{
  "success": true,
  "data": {
    "type": "html",
    "content": "<h1>Welcome to Dashboard</h1>",
    "settings": {
      "title": "Welcome Widget",
      "content": "<h1>Welcome to Dashboard</h1>"
    }
  }
}
```

#### PUT `/widgets/{id}/settings`
Updates widget settings for a specific widget.

**Parameters:**
- `id` (string, required): Widget ID (alphanumeric, hyphens, underscores)

**Request Body:**
```json
{
  "settings": {
    "title": "Updated Widget Title",
    "content": "<h1>Updated Content</h1>"
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

#### PUT `/widgets/{id}/move`
Moves a widget to a new column and position.

**Parameters:**
- `id` (string, required): Widget ID (alphanumeric, hyphens, underscores)

**Request Body:**
```json
{
  "column_id": "col-2",
  "position": 1
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "message": "Widget moved successfully"
  }
}
```

#### GET `/widgets/content/{widget_id}`
Retrieves widget content for a specific widget.

**Parameters:**
- `widget_id` (string, required): Widget ID (alphanumeric, hyphens, underscores)

**Response:**
```json
{
  "success": true,
  "data": {
    "type": "html",
    "content": "<h1>Widget Content</h1>",
    "title": "Widget Title"
  }
}
```

#### POST `/widgets/content/{widget_id}`
Retrieves widget content with custom settings.

**Parameters:**
- `widget_id` (string, required): Widget ID (alphanumeric, hyphens, underscores)

**Request Body:**
```json
{
  "settings": {
    "title": "Custom Title",
    "content": "<h1>Custom Content</h1>"
  }
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "type": "html",
    "content": "<h1>Custom Content</h1>",
    "title": "Custom Title"
  }
}
```

### Columns Endpoints

#### GET `/columns`
Retrieves all columns from the dashboard.

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": "col-1",
      "title": "Main Column",
      "width": "50%",
      "widgets": []
    }
  ]
}
```

#### POST `/columns`
Creates a new column.

**Request Body:**
```json
{
  "title": "New Column",
  "width": "full"
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "id": "col-abc123",
    "title": "New Column",
    "width": "full",
    "widgets": []
  }
}
```

#### GET `/columns/{id}`
Retrieves a specific column by ID.

**Parameters:**
- `id` (string, required): Column ID (alphanumeric, hyphens)

**Response:**
```json
{
  "success": true,
  "data": {
    "id": "col-1",
    "title": "Main Column",
    "width": "50%",
    "widgets": []
  }
}
```

#### PUT `/columns/{id}`
Updates a specific column.

**Parameters:**
- `id` (string, required): Column ID (alphanumeric, hyphens)

**Request Body:**
```json
{
  "title": "Updated Column Title",
  "width": "75%"
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "id": "col-1",
    "title": "Updated Column Title",
    "width": "75%",
    "widgets": []
  }
}
```

#### DELETE `/columns/{id}`
Deletes a specific column.

**Parameters:**
- `id` (string, required): Column ID (alphanumeric, hyphens)

**Response:**
```json
{
  "success": true,
  "data": {
    "message": "Column deleted successfully"
  }
}
```

## Widget Types

The API supports the following widget types:

### HTML Widget (`html`)
- **Description**: Displays custom HTML content
- **Settings**: `title`, `content`
- **Template**: `html`

### Quick Links Widget (`links`)
- **Description**: Displays a list of quick links
- **Settings**: `title`, `links` (array of link objects)
- **Template**: `links`

### Sales Widget (`tabular`)
- **Description**: Displays sales statistics in tabular format
- **Settings**: `title`, `data` (tabular data)
- **Template**: `tabular`

### Weekly Tickets Widget (`progress-circles`)
- **Description**: Displays weekly ticket statistics with progress circles
- **Settings**: `title`, `data` (progress data)
- **Template**: `progress-circles`

## Error Codes

| Code | Description | HTTP Status |
|------|-------------|-------------|
| `invalid_columns` | Columns data is invalid | 400 |
| `invalid_column_widgets` | Column widgets data is invalid | 400 |
| `save_error` | Unable to save dashboard data | 500 |
| `widget_not_found` | Widget not found | 404 |
| `column_not_found` | Column not found | 404 |
| `invalid_column` | Column data is invalid | 400 |
| `invalid_width` | Column width is invalid | 400 |
| `internal_error` | Internal server error | 500 |

## Data Storage

All dashboard data is stored in WordPress options:
- **Option Name**: `dashmate_dashboard_data`
- **Structure**:
  ```json
  {
    "layout": {
      "columns": []
    },
    "widgets": [],
    "column_widgets": {}
  }
  ```

## Development Notes

- All endpoints currently allow all requests for development purposes
- Widget content is generated dynamically based on widget type and settings
- Column and widget IDs must be alphanumeric with hyphens and underscores allowed
- Position values are 1-based (not 0-based)
- Widget settings are validated against widget-specific schemas
