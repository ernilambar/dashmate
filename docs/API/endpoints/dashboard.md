# Dashboard Endpoints

## Overview

Dashboard endpoints provide functionality for managing the overall dashboard layout, including retrieving dashboard data, updating layouts, and reordering widgets across columns.

## Endpoints

### GET `/dashboard`

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

### PUT `/dashboard`

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
          "settings": {},
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
            "settings": {},
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

### PUT `/dashboard/reorder`

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

## Usage Examples

### Retrieving Dashboard Data

```javascript
fetch('/wp-json/dashmate/v1/dashboard')
  .then(response => response.json())
  .then(data => {
    console.log('Dashboard data:', data.data.columns);
  });
```

### Updating Dashboard Layout

```javascript
const newLayout = {
  columns: [
    {
      id: "col-1",
      widgets: [
        {
          id: "widget-1",
          settings: { title: "My Widget" },
          collapsed: false
        }
      ]
    }
  ]
};

fetch('/wp-json/dashmate/v1/dashboard', {
  method: 'PUT',
  headers: {
    'Content-Type': 'application/json',
  },
  body: JSON.stringify(newLayout)
})
.then(response => response.json())
.then(data => {
  console.log('Dashboard updated:', data);
});
```

### Reordering Widgets

```javascript
const reorderData = {
  column_widgets: {
    "col-1": ["widget-1", "widget-3"],
    "col-2": ["widget-2", "widget-4"]
  }
};

fetch('/wp-json/dashmate/v1/dashboard/reorder', {
  method: 'PUT',
  headers: {
    'Content-Type': 'application/json',
  },
  body: JSON.stringify(reorderData)
})
.then(response => response.json())
.then(data => {
  console.log('Widgets reordered:', data);
});
```

## Data Structure

### Column Object
- **id** (string, required): Unique column identifier (alphanumeric, hyphens allowed)
- **widgets** (array): Array of widget objects

### Widget Object
- **id** (string, required): Unique widget identifier (alphanumeric, hyphens, underscores allowed)
- **settings** (object): Widget configuration data
- **collapsed** (boolean): Whether the widget is minimized

## Error Handling

Common errors for dashboard endpoints:

- `invalid_columns`: Columns data is invalid
- `invalid_column_widgets`: Column widgets data is invalid
- `save_error`: Unable to save dashboard data

See [Error Handling](../error-handling.md) for more details.
