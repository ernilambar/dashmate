# Widgets Endpoints

## Overview

Widgets endpoints provide functionality for managing individual widgets, including retrieving widget types, getting widget data and content, and updating widget settings.

## Endpoints

### GET `/widgets`

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

### GET `/widgets/{id}/data`

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

### PUT `/widgets/{id}/settings`

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

### GET `/widgets/content/{widget_id}`

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

### POST `/widgets/content/{widget_id}`

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

## Usage Examples

### Getting All Widget Types

```javascript
fetch('/wp-json/dashmate/v1/widgets')
  .then(response => response.json())
  .then(data => {
    console.log('Available widgets:', data.data);
    data.data.forEach(widget => {
      console.log(`${widget.title}: ${widget.description}`);
    });
  });
```

### Getting Widget Data

```javascript
fetch('/wp-json/dashmate/v1/widgets/widget-1/data')
  .then(response => response.json())
  .then(data => {
    console.log('Widget data:', data.data);
    console.log('Widget type:', data.data.type);
    console.log('Widget settings:', data.data.settings);
  });
```

### Updating Widget Settings

```javascript
const newSettings = {
  settings: {
    title: "Updated Widget Title",
    content: "<h1>Updated Content</h1>"
  }
};

fetch('/wp-json/dashmate/v1/widgets/widget-1/settings', {
  method: 'PUT',
  headers: {
    'Content-Type': 'application/json',
  },
  body: JSON.stringify(newSettings)
})
.then(response => response.json())
.then(data => {
  console.log('Settings updated:', data.data.message);
});
```

### Getting Widget Content

```javascript
fetch('/wp-json/dashmate/v1/widgets/content/widget-1')
  .then(response => response.json())
  .then(data => {
    console.log('Widget content:', data.data.content);
    console.log('Widget title:', data.data.title);
  });
```

### Getting Widget Content with Custom Settings

```javascript
const customSettings = {
  settings: {
    title: "Custom Title",
    content: "<h1>Custom Content</h1>"
  }
};

fetch('/wp-json/dashmate/v1/widgets/content/widget-1', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
  },
  body: JSON.stringify(customSettings)
})
.then(response => response.json())
.then(data => {
  console.log('Custom widget content:', data.data.content);
});
```

## Data Structure

### Widget Type Object
- **id** (string): Unique widget type identifier
- **title** (string): Human-readable widget name
- **description** (string): Widget description
- **template_type** (string): Template type for rendering
- **settings_schema** (object): Schema for widget settings

### Widget Data Object
- **type** (string): Widget template type
- **content** (string): Rendered widget content
- **settings** (object): Widget configuration data

### Widget Content Object
- **type** (string): Widget template type
- **content** (string): Rendered widget content
- **title** (string): Widget title

## Error Handling

Common errors for widgets endpoints:

- `widget_not_found`: Widget not found
- `invalid_widget_id`: Invalid widget ID format
- `invalid_settings`: Invalid widget settings

See [Error Handling](../error-handling.md) for more details.

## Widget Types

For detailed information about available widget types, see [Widget Types](../reference/widget-types.md).
