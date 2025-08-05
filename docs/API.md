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
          "order": 1
        }
      ]
    },
    "widgets": [
      {
        "id": "widget-1",
        "type": "html",
        "column_id": "col-1",

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
      "id": "col-1"
    },
    {
      "id": "col-2"
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
          "order": 1
        },
        {
          "id": "col-2",
          "order": 2
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

### Layouts Endpoints

#### GET `/layouts`
Retrieves all available layouts with their metadata and URLs to individual layout endpoints.

**Response:**
```json
{
  "success": true,
  "data": {
    "default": {
      "title": "Default",
      "path": "/path/to/layouts/default.json",
      "url": "https://griha.local/wp-json/dashmate/v1/layouts/default"
    }
  }
}
```

#### GET `/layouts/{layout_key}`
Retrieves complete layout data for a specific layout.

**Parameters:**
- `layout_key` (string, required): Layout key (alphanumeric, hyphens, underscores)

**Response:**
```json
{
  "success": true,
  "data": {
    "$schema": "../data/schemas/dashmate.json",
    "layout": {
      "columns": [
        {
          "id": "col-1",
          "order": 1
        },
        {
          "id": "col-2",
          "order": 2
        },
        {
          "id": "col-3",
          "order": 3
        }
      ]
    },
    "widgets": [
      {
        "id": "sample-html",
        "column_id": "col-1",
        "settings": {
          "allow_scripts": false
        }
      },
      {
        "id": "sample-links",
        "column_id": "col-2",
        "settings": {
          "hide_icon": false,
          "display_style": "list"
        }
      },
      {
        "id": "sample-progress-circles",
        "column_id": "col-3",
        "settings": {
          "circles_number": 4,
          "hide_caption": false
        }
      },
      {
        "id": "sample-tabular",
        "column_id": "col-1",
        "settings": {
          "max_items": 5
        }
      }
    ],
    "column_widgets": {
      "col-1": ["sample-tabular"],
      "col-2": ["sample-progress-circles"],
      "col-3": ["sample-links", "sample-html"]
    }
  }
}
```

**Error Responses:**

Layout not found (404):
```json
{
  "success": false,
  "message": "Layout not found: non-existent",
  "code": "layout_not_found"
}
```

Layout retrieval error (500):
```json
{
  "success": false,
  "message": "Layout file does not exist: /path/to/missing/layout.json",
  "code": "layouts_retrieval_error"
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
  "title": "New Column"
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "id": "col-abc123",
    "title": "New Column",
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
  "title": "Updated Column Title"
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "id": "col-1",
    "title": "Updated Column Title",
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
| `layout_not_found` | Layout not found | 404 |
| `layouts_retrieval_error` | Failed to retrieve layouts list | 500 |
| `layout_retrieval_error` | Failed to retrieve layout data | 500 |
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

## Field Types

The following field types are supported in widget settings schemas:

### Text Field
```json
{
  "type": "text",
  "label": "Field Label",
  "description": "Optional description",
  "default": "default value"
}
```

### URL Field
```json
{
  "type": "url",
  "label": "URL Field",
  "description": "Enter a valid URL",
  "default": "https://example.com"
}
```

### Checkbox Field
```json
{
  "type": "checkbox",
  "label": "Enable Feature",
  "description": "Check to enable this feature",
  "default": false
}
```

### Select Field
```json
{
  "type": "select",
  "label": "Choose Option",
  "description": "Select from available options",
  "default": "option1",
  "choices": [
    {
      "value": "option1",
      "label": "Option 1"
    },
    {
      "value": "option2",
      "label": "Option 2"
    }
  ]
}
```

### Number Field
```json
{
  "type": "number",
  "label": "Number Input",
  "description": "Enter a number",
  "default": 5,
  "min": 1,
  "max": 10,
  "choices": [
    {
      "value": 1,
      "label": "One"
    },
    {
      "value": 5,
      "label": "Five"
    }
  ]
}
```

### Radio Field
```json
{
  "type": "radio",
  "label": "Choose One",
  "description": "Select one option",
  "default": "option1",
  "choices": [
    {
      "value": "option1",
      "label": "Option 1"
    },
    {
      "value": "option2",
      "label": "Option 2"
    }
  ]
}
```

### Buttonset Field
```json
{
  "type": "buttonset",
  "label": "Choose Style",
  "description": "Select display style",
  "default": "list",
  "choices": [
    {
      "value": "list",
      "label": "List"
    },
    {
      "value": "grid",
      "label": "Grid"
    }
  ]
}
```

### Multi-Check Field
```json
{
  "type": "multi-check",
  "label": "Select Items",
  "description": "Choose multiple items",
  "default": ["item1", "item2"],
  "choices": [
    {
      "value": "item1",
      "label": "Item 1"
    },
    {
      "value": "item2",
      "label": "Item 2"
    }
  ]
}
```

### Sortable Field
```json
{
  "type": "sortable",
  "label": "Sortable Items",
  "description": "Drag to reorder and toggle to enable/disable items",
  "default": ["item1", "item2"],
  "choices": [
    {
      "value": "item1",
      "label": "Item 1"
    },
    {
      "value": "item2",
      "label": "Item 2"
    },
    {
      "value": "item3",
      "label": "Item 3"
    }
  ]
}
```

The sortable field provides:
- **Drag and drop reordering**: Users can drag items to change their order
- **Toggle functionality**: Each item has a checkbox to enable/disable it
- **Vertical-only dragging**: Items can only be dragged vertically
- **Array output**: The saved value is an array of enabled item values in the specified order
- **Visual feedback**: Disabled items are visually dimmed, and dragging provides visual feedback

## Development Notes

- All endpoints currently allow all requests for development purposes
- Widget content is generated dynamically based on widget type and settings
- Column and widget IDs must be alphanumeric with hyphens and underscores allowed
- Widget order is determined by array position in column_widgets structure
- Widget settings are validated against widget-specific schemas
