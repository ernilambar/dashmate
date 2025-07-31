# Dashmate REST API Endpoints

This document describes the REST API endpoints available in the Dashmate plugin.

## Base URL
All endpoints are prefixed with: `/wp-json/dashmate/v1/`

## Authentication
All endpoints require WordPress authentication with `manage_options` capability.

## Endpoints

### Dashboard

#### GET /dashboard
Get the current dashboard layout.

**Response:**
```json
{
  "success": true,
  "data": {
    "columns": [
      {
        "id": "col-1",
        "title": "Main Column",
        "width": "full",
        "widgets": [
          {
            "id": "widget-1",
            "type": "chart",
            "title": "Sales Overview",
            "settings": {
              "chart_type": "line",
              "data_source": "sales",
              "time_period": "30_days"
            },
            "position": 0
          }
        ]
      }
    ]
  }
}
```

#### POST /dashboard
Save the dashboard layout.

**Request Body:**
```json
{
  "columns": [
    {
      "id": "col-1",
      "title": "Main Column",
      "width": "full",
      "widgets": [
        {
          "id": "widget-1",
          "type": "chart",
          "title": "Sales Overview",
          "settings": {
            "chart_type": "line",
            "data_source": "sales",
            "time_period": "30_days"
          },
          "position": 0
        }
      ]
    }
  ]
}
```

### Widgets

#### GET /widgets
Get available widget types and their settings schemas.

**Response:**
```json
{
  "success": true,
  "data": {
    "chart": {
      "name": "Chart Widget",
      "description": "Display data in various chart formats",
      "icon": "chart-line",
      "settings_schema": {
        "chart_type": {
          "type": "select",
          "label": "Chart Type",
          "options": [
            {"value": "line", "label": "Line Chart"},
            {"value": "bar", "label": "Bar Chart"}
          ],
          "default": "line"
        }
      }
    }
  }
}
```

#### GET /widgets/{id}/data
Get data for a specific widget.

**Response:**
```json
{
  "success": true,
  "data": {
    "chart_data": {
      "labels": ["Jan", "Feb", "Mar"],
      "datasets": [
        {
          "label": "Sales",
          "data": [12000, 19000, 15000],
          "borderColor": "#3b82f6"
        }
      ]
    }
  }
}
```

#### POST /widgets/{id}/settings
Save settings for a specific widget.

**Request Body:**
```json
{
  "settings": {
    "chart_type": "bar",
    "data_source": "sales",
    "time_period": "30_days"
  }
}
```

### Columns

#### GET /columns
Get all columns.

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": "col-1",
      "title": "Main Column",
      "width": "full",
      "widgets": []
    }
  ]
}
```

#### POST /columns
Create a new column.

**Request Body:**
```json
{
  "title": "New Column",
  "width": "sidebar"
}
```

#### GET /columns/{id}
Get a specific column.

#### PUT /columns/{id}
Update a specific column.

**Request Body:**
```json
{
  "title": "Updated Column Title",
  "width": "full"
}
```

#### DELETE /columns/{id}
Delete a specific column.

## Widget Types

### Chart Widget
- **Type:** `chart`
- **Data Sources:** `sales`, `users`, `orders`
- **Settings:** `chart_type`, `data_source`, `time_period`

### Metric Widget
- **Type:** `metric`
- **Data Sources:** `revenue`, `orders`, `users`, `conversion`
- **Settings:** `metric_type`, `data_source`, `format`

### List Widget
- **Type:** `list`
- **List Types:** `orders`, `users`, `products`, `comments`
- **Settings:** `list_type`, `limit`, `show_date`, `show_avatar`

### Table Widget
- **Type:** `table`
- **Table Types:** `orders`, `users`, `products`
- **Settings:** `table_type`, `columns`, `rows_per_page`

## Error Responses

All endpoints return consistent error responses:

```json
{
  "code": "error_code",
  "message": "Error message",
  "data": {
    "status": 400
  }
}
```

Common error codes:
- `file_not_found`: Requested data file not found
- `file_read_error`: Unable to read data file
- `file_write_error`: Unable to write data file
- `json_decode_error`: Invalid JSON in data file
- `json_encode_error`: Unable to encode data to JSON
- `widget_not_found`: Widget with specified ID not found
- `column_not_found`: Column with specified ID not found
- `unknown_widget_type`: Unknown widget type
- `unknown_data_source`: Unknown data source
- `invalid_columns`: Invalid columns data structure
- `invalid_widget`: Invalid widget data structure
