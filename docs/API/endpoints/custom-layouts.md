# Custom Layouts Endpoints

## Overview

Custom Layouts endpoints provide functionality for managing custom dashboard layouts stored in the database. These layouts are saved with the option key format `dashmate_dashboard_custom_<key>` and are separate from the current dashboard configuration.

## Endpoints

### Get All Custom Layouts
**GET** `/wp-json/dashmate/v1/custom-layouts`

Returns a list of all custom layouts stored in the database.

**Response:**
```json
{
  "success": true,
  "data": {
    "my-custom-layout": {
      "columns": [
        {
          "id": "col-1",
          "widgets": [
            {
              "id": "widget-1",
              "settings": {
                "title": "Custom Widget",
                "content": "Widget content"
              },
              "collapsed": false
            }
          ]
        }
      ],
      "_meta": {
        "created": "2024-01-01 12:00:00",
        "updated": "2024-01-01 12:00:00",
        "key": "my-custom-layout"
      }
    }
  }
}
```

### Create Custom Layout
**POST** `/wp-json/dashmate/v1/custom-layouts`

Creates a new custom layout in the database.

**Request Body:**
```json
{
  "key": "my-custom-layout",
  "data": {
    "columns": [
      {
        "id": "col-1",
        "widgets": [
          {
            "id": "widget-1",
            "settings": {
              "title": "Custom Widget",
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

**Response:**
```json
{
  "success": true,
  "data": {
    "message": "Custom layout \"my-custom-layout\" created successfully!",
    "key": "my-custom-layout",
    "data": {
      "columns": [
        {
          "id": "col-1",
          "widgets": [
            {
              "id": "widget-1",
              "settings": {
                "title": "Custom Widget",
                "content": "Widget content"
              },
              "collapsed": false
            }
          ]
        }
      ]
    }
  }
}
```

### Get Specific Custom Layout
**GET** `/wp-json/dashmate/v1/custom-layouts/{layout_key}`

Returns the data for a specific custom layout.

**Parameters:**
- `layout_key` (string, required): The custom layout identifier

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
              "title": "Custom Widget",
              "content": "Widget content"
            },
            "collapsed": false
          }
        ]
      }
    ],
    "_meta": {
      "created": "2024-01-01 12:00:00",
      "updated": "2024-01-01 12:00:00",
      "key": "my-custom-layout"
    }
  }
}
```

### Update Custom Layout
**PUT** `/wp-json/dashmate/v1/custom-layouts/{layout_key}`

Updates an existing custom layout.

**Parameters:**
- `layout_key` (string, required): The custom layout identifier

**Request Body:**
```json
{
  "data": {
    "columns": [
      {
        "id": "col-1",
        "widgets": [
          {
            "id": "widget-1",
            "settings": {
              "title": "Updated Widget",
              "content": "Updated content"
            },
            "collapsed": false
          }
        ]
      }
    ]
  }
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "message": "Custom layout \"my-custom-layout\" updated successfully!",
    "key": "my-custom-layout",
    "data": {
      "columns": [
        {
          "id": "col-1",
          "widgets": [
            {
              "id": "widget-1",
              "settings": {
                "title": "Updated Widget",
                "content": "Updated content"
              },
              "collapsed": false
            }
          ]
        }
      ]
    }
  }
}
```

### Delete Custom Layout
**DELETE** `/wp-json/dashmate/v1/custom-layouts/{layout_key}`

Deletes a custom layout from the database.

**Parameters:**
- `layout_key` (string, required): The custom layout identifier

**Response:**
```json
{
  "success": true,
  "data": {
    "message": "Custom layout \"my-custom-layout\" deleted successfully!",
    "key": "my-custom-layout"
  }
}
```

## Usage Examples

### Creating a Custom Layout

```javascript
fetch('/wp-json/dashmate/v1/custom-layouts', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'X-WP-Nonce': wpApiSettings.nonce
  },
  body: JSON.stringify({
    key: 'my-custom-layout',
    data: {
      columns: [
        {
          id: 'col-1',
          widgets: [
            {
              id: 'widget-1',
              settings: {
                title: 'Custom Widget',
                content: 'Widget content'
              },
              collapsed: false
            }
          ]
        }
      ]
    }
  })
})
.then(response => response.json())
.then(data => {
  console.log('Custom layout created:', data.data.message);
});
```

### Getting All Custom Layouts

```javascript
fetch('/wp-json/dashmate/v1/custom-layouts')
  .then(response => response.json())
  .then(data => {
    console.log('Custom layouts:', data.data);
  });
```

### Updating a Custom Layout

```javascript
fetch('/wp-json/dashmate/v1/custom-layouts/my-custom-layout', {
  method: 'PUT',
  headers: {
    'Content-Type': 'application/json',
    'X-WP-Nonce': wpApiSettings.nonce
  },
  body: JSON.stringify({
    data: {
      columns: [
        {
          id: 'col-1',
          widgets: [
            {
              id: 'widget-1',
              settings: {
                title: 'Updated Widget',
                content: 'Updated content'
              },
              collapsed: false
            }
          ]
        }
      ]
    }
  })
})
.then(response => response.json())
.then(data => {
  console.log('Custom layout updated:', data.data.message);
});
```

### Deleting a Custom Layout

```javascript
fetch('/wp-json/dashmate/v1/custom-layouts/my-custom-layout', {
  method: 'DELETE',
  headers: {
    'X-WP-Nonce': wpApiSettings.nonce
  }
})
.then(response => response.json())
.then(data => {
  console.log('Custom layout deleted:', data.data.message);
});
```

## Data Structure

### Custom Layout Object
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
  ],
  "_meta": {
    "created": "2024-01-01 12:00:00",
    "updated": "2024-01-01 12:00:00",
    "key": "layout-key"
  }
}
```

### Column Object
```json
{
  "id": "col-1",
  "widgets": []
}
```

### Widget Object
```json
{
  "id": "widget-1",
  "settings": {
    "title": "Widget Title",
    "content": "Widget content"
  },
  "collapsed": false
}
```

### Meta Object
```json
{
  "created": "2024-01-01 12:00:00",
  "updated": "2024-01-01 12:00:00",
  "key": "layout-key"
}
```

## Storage Details

- **Option Key Format**: `dashmate_dashboard_custom_<key>`
- **Storage Location**: WordPress options table
- **Data Type**: Serialized JSON
- **Access Method**: WordPress Options API

## Error Handling

Common errors for custom layouts endpoints:

- `layout_already_exists`: Custom layout already exists (409)
- `layout_not_found`: Custom layout not found (404)
- `invalid_data_structure`: Invalid layout data structure (400)
- `create_failed`: Failed to create custom layout (500)
- `update_failed`: Failed to update custom layout (500)
- `delete_failed`: Failed to delete custom layout (500)

See [Error Handling](../error-handling.md) for more details.

## Integration with Layouts System

Custom layouts are automatically integrated into the main layouts system:

1. **Dynamic Discovery**: Custom layouts are automatically discovered and included in the main layouts list
2. **Consistent API**: Custom layouts use the same data structure as other layouts
3. **Apply Functionality**: Custom layouts can be applied to the current dashboard using the main layouts apply endpoint
4. **Type Identification**: Custom layouts are identified by the `type: 'custom'` field

### Example: Getting All Layouts (Including Custom)

```javascript
fetch('/wp-json/dashmate/v1/layouts')
  .then(response => response.json())
  .then(data => {
    // This will include current, default, and all custom layouts
    console.log('All layouts:', data.data);
  });
```
