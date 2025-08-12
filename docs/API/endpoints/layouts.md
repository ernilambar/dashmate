# Layouts Endpoints

## Overview

Layouts endpoints provide functionality for managing dashboard layouts, including retrieving available layouts, getting specific layout data, and applying layouts to the current dashboard.

## Endpoints

### Get All Layouts
**GET** `/wp-json/dashmate/v1/layouts`

Returns a list of all available layouts including the current layout from options, with layout data included for immediate use.

**Response:**
```json
{
  "success": true,
  "data": {
    "current": {
      "id": "current",
      "title": "Current Layout",
      "type": "options",
      "layoutData": {
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
          }
        ]
      }
    },
    "default": {
      "id": "default",
      "title": "Default",
      "type": "file",
      "path": "/path/to/layouts/default.json",
      "layoutData": {
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
          }
        ]
      }
    }
  }
}
```

**Note:** The `layoutData` field contains the complete layout structure with columns and their nested widgets. This eliminates the need for separate API calls to fetch individual layout data.

**Custom Layouts:** Custom layouts are automatically discovered and included in this response. They are identified by `type: 'custom'` and can be managed through the separate `/custom-layouts` endpoints.

### Get Specific Layout
**GET** `/wp-json/dashmate/v1/layouts/{layout_key}`

Returns the layout data for a specific layout key.

**Parameters:**
- `layout_key` (string, required): The layout identifier (e.g., "current", "default")

**Examples:**

#### Get Current Layout from Options
**GET** `/wp-json/dashmate/v1/layouts/current`

Returns the current layout data stored in WordPress options.

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
      }
    ]
  }
}
```

#### Get Default Layout from File
**GET** `/wp-json/dashmate/v1/layouts/default`

Returns the default layout data from the JSON file.

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
      }
    ]
  }
}
```

### Apply Layout
**POST** `/wp-json/dashmate/v1/layouts/{layout_key}/apply`

Applies a specific layout to the current dashboard configuration.

**Parameters:**
- `layout_key` (string, required): The layout identifier to apply (e.g., "default", "minimal-layout")

**Examples:**

#### Apply Default Layout
**POST** `/wp-json/dashmate/v1/layouts/default/apply`

Applies the default layout to the current dashboard.

**Response:**
```json
{
  "success": true,
  "data": {
    "message": "Layout \"Default\" applied successfully!",
    "layout_key": "default"
  }
}
```

#### Apply Custom Layout
**POST** `/wp-json/dashmate/v1/layouts/custom-layout/apply`

Applies a custom layout to the current dashboard.

**Response:**
```json
{
  "success": true,
  "data": {
    "message": "Layout \"Custom Layout\" applied successfully!",
    "layout_key": "custom-layout"
  }
}
```

## Layout Types

### Options-based Layouts
- **Type**: `options`
- **Source**: WordPress options table
- **Example**: `current` layout
- **Use Case**: Current dashboard configuration
- **Access**: Read-only (cannot be applied)

### File-based Layouts
- **Type**: `file`
- **Source**: JSON files in `/layouts/` directory
- **Example**: `default` layout
- **Use Case**: Predefined layout templates
- **Access**: Can be applied to dashboard

### Custom Layouts
- **Type**: `custom`
- **Source**: WordPress options table with `dashmate_dashboard_custom_<key>` format
- **Example**: `favourite`, `my-custom-layout`
- **Use Case**: User-created custom layouts
- **Access**: Can be applied to dashboard, full CRUD operations available
- **Management**: Separate API endpoints at `/custom-layouts`

## Usage Examples

### Getting All Layouts

```javascript
fetch('/wp-json/dashmate/v1/layouts')
  .then(response => response.json())
  .then(data => {
    console.log('Available layouts:', Object.keys(data.data));
    console.log('Current layout:', data.data.current);
  });
```

### Getting Specific Layout

```javascript
fetch('/wp-json/dashmate/v1/layouts/default')
  .then(response => response.json())
  .then(data => {
    console.log('Default layout:', data.data);
  });
```

### Applying a Layout

```javascript
fetch('/wp-json/dashmate/v1/layouts/default/apply', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
  }
})
.then(response => response.json())
.then(data => {
  console.log('Layout applied:', data.data.message);
});
```

## Data Structure

### Layout Object
- **id** (string): Layout identifier
- **title** (string): Human-readable layout name
- **type** (string): Layout type (`options` or `file`)
- **path** (string, optional): File path for file-based layouts
- **layoutData** (object): Complete layout structure with columns and widgets

### Layout Data Structure
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
    }
  ]
}
```

## Error Handling

Common errors for layouts endpoints:

- `layout_not_found`: Layout not found
- `current_layout_readonly`: Cannot apply current layout as it is read-only
- `current_layout_not_found`: No layout data found
- `layout_apply_failed`: Failed to update dashboard data
- `layout_json_conversion_failed`: Failed to convert layout data to JSON
- `invalid_layout_data`: Invalid layout data structure

See [Error Handling](../error-handling.md) for more details.
