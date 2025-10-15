# DashMate API Reference

## Overview

The DashMate API provides REST endpoints for managing dashboards and widgets. All endpoints are prefixed with `/wp-json/dashmate/v1/`.

## Base Information

- **Namespace**: `dashmate/v1`
- **Base URL**: `/wp-json/dashmate/v1/`
- **Authentication**: WordPress nonce verification
- **Response Format**: JSON

## Response Format

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

## Dashboard Endpoints

### GET /dashboards/{dashboard_id}

Retrieves the complete dashboard data including columns and their widgets.

**Parameters:**
- `dashboard_id` (string, required): Dashboard identifier

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

### POST /dashboards/{dashboard_id}

Updates the dashboard layout with new column configuration.

**Parameters:**
- `dashboard_id` (string, required): Dashboard identifier

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

## Widget Endpoints

### GET /widgets

Retrieves a list of all available widget types and their configurations (global endpoint).

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
      }
    ]
  }
}
```

### GET /dashboards/{dashboard_id}/widgets

Retrieves all widgets for a specific dashboard.

**Parameters:**
- `dashboard_id` (string, required): Dashboard identifier

### GET /dashboards/{dashboard_id}/widgets/{widget_id}/content

Retrieves the content for a specific widget.

**Parameters:**
- `dashboard_id` (string, required): Dashboard identifier
- `widget_id` (string, required): Widget instance identifier

**Response:**
```json
{
  "success": true,
  "data": {
    "id": "widget-1",
    "type": "html",
    "content": "<div>Widget content</div>",
    "metadata": {
      "classes": ["widget", "html-widget"],
      "attributes": {}
    }
  }
}
```

### POST /dashboards/{dashboard_id}/widgets/{widget_id}/content

Retrieves widget content with custom settings (for preview purposes).

**Parameters:**
- `dashboard_id` (string, required): Dashboard identifier
- `widget_id` (string, required): Widget instance identifier

**Request Body:**
```json
{
  "settings": {
    "title": "Preview Title",
    "content": "Preview content"
  }
}
```

### POST /dashboards/{dashboard_id}/widgets/{widget_id}/settings

Updates the settings for a specific widget instance.

**Parameters:**
- `dashboard_id` (string, required): Dashboard identifier
- `widget_id` (string, required): Widget instance identifier

**Request Body:**
```json
{
  "settings": {
    "title": "Updated Widget Title",
    "content": "Updated widget content"
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

## Error Codes

| Code | Description |
|------|-------------|
| `invalid_request` | Invalid request parameters |
| `widget_not_found` | Widget not found |
| `layout_not_found` | Layout not found |
| `column_not_found` | Column not found |
| `permission_denied` | Insufficient permissions |

## Authentication

All API requests require a valid WordPress nonce. Include the nonce in the `X-WP-Nonce` header:

```
X-WP-Nonce: your-nonce-value
```

## PHP API Usage

### Dashboard Management

Create custom dashboard pages by extending the `Abstract_Dashboard_Page` class:

```php
use Nilambar\Dashmate\Admin\Abstract_Dashboard_Page;

class My_Dashboard extends Abstract_Dashboard_Page {
    protected function init_properties() {
        $this->page_slug      = 'my-dashboard';
        $this->page_title     = esc_html__('My Dashboard', 'my-plugin');
        $this->menu_title     = esc_html__('My Dashboard', 'my-plugin');
        $this->capability     = 'manage_options';
        $this->menu_icon      = 'dashicons-dashboard';
        $this->menu_position  = 30;
        $this->dashboard_id   = 'my_dashboard';
    }
}

// Register the dashboard page
add_action('init', function() {
    new My_Dashboard();
});
```

**Required Properties:**
- `page_slug` - Unique page identifier
- `page_title` - Page title displayed in browser
- `menu_title` - Menu item text
- `capability` - Required user capability
- `dashboard_id` - Dashboard identifier for API calls
- `starter_layout` - Default layout configuration

**Optional Properties:**
- `menu_icon` - WordPress dashicon or custom icon
- `menu_position` - Menu position in admin menu
- `parent_page` - Parent page slug for submenu items

### Widget Registration

**Important**: Custom widgets can ONLY be registered using the `dashmate_widgets` filter. All direct registration methods are restricted to internal use only and are not accessible to external plugins.

```php
use Nilambar\Dashmate\Widgets\Abstract_Widget;

class My_Custom_Widget extends Abstract_Widget {
    public function __construct($id) {
        parent::__construct($id, 'my_custom', esc_html__('My Custom Widget', 'my-plugin'));
    }

    protected function define_widget() {
        $this->description = esc_html__('A custom widget.', 'my-plugin');
        $this->icon        = 'admin-tools';

        $this->settings_schema = [
            'title' => [
                'type'        => 'text',
                'label'       => esc_html__('Title', 'my-plugin'),
                'default'     => esc_html__('My Widget', 'my-plugin'),
            ],
        ];
    }

    public function get_content(array $settings = []): array {
        $settings = $this->merge_settings_with_defaults($settings);
        return [
            'title' => $settings['title'],
            'content' => '<p>Widget content here</p>',
        ];
    }
}

// Register widget using the dashmate_widgets filter
add_filter('dashmate_widgets', function($widgets) {
    $widgets['my_custom'] = new My_Custom_Widget('my_custom');
    return $widgets;
});
```

