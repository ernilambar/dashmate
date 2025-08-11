# Columns Endpoints

## Overview

Columns endpoints provide functionality for managing dashboard columns, including creating, reading, updating, and deleting columns.

## Endpoints

### GET `/columns`

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

### POST `/columns`

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

### GET `/columns/{id}`

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

### PUT `/columns/{id}`

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

### DELETE `/columns/{id}`

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

## Usage Examples

### Getting All Columns

```javascript
fetch('/wp-json/dashmate/v1/columns')
  .then(response => response.json())
  .then(data => {
    console.log('All columns:', data.data);
    data.data.forEach(column => {
      console.log(`Column ${column.id}: ${column.title} (${column.widgets.length} widgets)`);
    });
  });
```

### Creating a New Column

```javascript
const newColumn = {
  title: "New Column"
};

fetch('/wp-json/dashmate/v1/columns', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
  },
  body: JSON.stringify(newColumn)
})
.then(response => response.json())
.then(data => {
  console.log('Column created:', data.data);
  console.log('New column ID:', data.data.id);
});
```

### Getting a Specific Column

```javascript
fetch('/wp-json/dashmate/v1/columns/col-1')
  .then(response => response.json())
  .then(data => {
    console.log('Column details:', data.data);
    console.log('Widgets in column:', data.data.widgets);
  });
```

### Updating a Column

```javascript
const updatedColumn = {
  title: "Updated Column Title"
};

fetch('/wp-json/dashmate/v1/columns/col-1', {
  method: 'PUT',
  headers: {
    'Content-Type': 'application/json',
  },
  body: JSON.stringify(updatedColumn)
})
.then(response => response.json())
.then(data => {
  console.log('Column updated:', data.data);
});
```

### Deleting a Column

```javascript
fetch('/wp-json/dashmate/v1/columns/col-1', {
  method: 'DELETE',
  headers: {
    'Content-Type': 'application/json',
  }
})
.then(response => response.json())
.then(data => {
  console.log('Column deleted:', data.data.message);
});
```

## Data Structure

### Column Object
- **id** (string): Unique column identifier (alphanumeric, hyphens allowed)
- **title** (string): Human-readable column name
- **widgets** (array): Array of widget objects in the column

### Widget Object (within Column)
- **id** (string): Unique widget identifier
- **settings** (object): Widget configuration data
- **collapsed** (boolean): Whether the widget is minimized

## Column Management

### Creating Columns
- Column IDs are automatically generated
- Column titles should be descriptive and user-friendly
- New columns start with an empty widgets array

### Updating Columns
- Only the title can be updated
- Column ID remains unchanged
- Widgets array is preserved during updates

### Deleting Columns
- All widgets in the column are also deleted
- This action cannot be undone
- Ensure widgets are moved to other columns before deletion if needed

## Error Handling

Common errors for columns endpoints:

- `column_not_found`: Column not found
- `invalid_column`: Column data is invalid
- `invalid_column_id`: Invalid column ID format

See [Error Handling](../error-handling.md) for more details.

## Best Practices

1. **Column Naming**: Use descriptive titles that indicate the column's purpose
2. **Widget Organization**: Group related widgets in the same column
3. **Column Limits**: Consider the number of columns for optimal layout
4. **Backup**: Save important layouts before making structural changes
