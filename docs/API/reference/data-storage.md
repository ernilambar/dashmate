# Data Storage

## Overview

This document covers how Dashmate stores and manages data, including the database structure, data formats, and storage mechanisms.

## Storage Architecture

### WordPress Options Table

All dashboard data is stored in the WordPress options table using the WordPress Options API. This provides:

- **Automatic serialization**: Complex data structures are automatically serialized
- **Built-in caching**: WordPress handles caching and performance optimization
- **Standard WordPress integration**: Follows WordPress best practices
- **Easy backup**: Standard WordPress backup procedures work seamlessly

### Primary Storage

- **Option Name**: `dashmate_dashboard_data`
- **Data Type**: Serialized JSON
- **Access Method**: WordPress Options API

## Data Structure

### Dashboard Data Format

The main dashboard data follows this structure:

```json
{
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
```

### Column Object

```json
{
  "id": "col-1",
  "widgets": []
}
```

**Properties**:
- `id` (string): Unique column identifier (alphanumeric, hyphens allowed)
- `widgets` (array): Array of widget objects

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

**Properties**:
- `id` (string): Unique widget identifier (alphanumeric, hyphens, underscores allowed)
- `settings` (object): Widget configuration data
- `collapsed` (boolean): Whether the widget is minimized

## Data Management

### Reading Data

```php
// Get dashboard data
$dashboard_data = get_option('dashmate_dashboard_data', array());

// Access columns
$columns = $dashboard_data['columns'] ?? array();

// Access specific column
$column = null;
foreach ($columns as $col) {
    if ($col['id'] === 'col-1') {
        $column = $col;
        break;
    }
}
```

### Writing Data

```php
// Update dashboard data
$dashboard_data = array(
    'columns' => array(
        array(
            'id' => 'col-1',
            'widgets' => array(
                array(
                    'id' => 'widget-1',
                    'settings' => array(
                        'title' => 'My Widget',
                        'content' => 'Widget content'
                    ),
                    'collapsed' => false
                )
            )
        )
    )
);

// Save to WordPress options
update_option('dashmate_dashboard_data', $dashboard_data);
```

### Data Validation

Before saving data, the API validates:

- **Column IDs**: Must be alphanumeric with hyphens allowed
- **Widget IDs**: Must be alphanumeric with hyphens and underscores allowed
- **Data Structure**: Must match expected format
- **Widget Settings**: Must validate against widget-specific schemas

## Layout Storage

### Layout Types

#### Options-based Layouts
- **Storage**: WordPress options table
- **Example**: Current dashboard configuration
- **Access**: Read-only (cannot be applied)
- **Key**: `dashmate_dashboard_data`

#### File-based Layouts
- **Storage**: JSON files in `/layouts/` directory
- **Example**: Default layout template
- **Access**: Can be applied to dashboard
- **Format**: Standard JSON files

### Layout File Structure

Layout files follow the same structure as dashboard data:

```json
{
  "columns": [
    {
      "id": "col-1",
      "widgets": [
        {
          "id": "widget-1",
          "settings": {
            "title": "Default Widget",
            "content": "Default content"
          },
          "collapsed": false
        }
      ]
    }
  ]
}
```

## Widget Settings Storage

### Settings Schema

Each widget type defines its settings schema:

```json
{
  "title": {
    "type": "text",
    "label": "Widget Title",
    "default": "My Widget"
  },
  "content": {
    "type": "text",
    "label": "Widget Content",
    "default": ""
  }
}
```

### Settings Validation

Settings are validated against schemas:

- **Type checking**: Values must match expected types
- **Required fields**: Required fields must be present
- **Default values**: Missing fields get default values
- **Format validation**: URLs, numbers, etc. are validated

## Data Persistence

### WordPress Integration

Dashmate leverages WordPress's built-in data persistence:

- **Automatic serialization**: Complex objects are automatically serialized
- **Database abstraction**: Uses WordPress database abstraction layer
- **Caching**: WordPress handles caching automatically
- **Backup compatibility**: Works with standard WordPress backup tools

### Performance Considerations

- **Lazy loading**: Widget content is generated on-demand
- **Caching**: WordPress options are cached by default
- **Minimal queries**: Single option read/write per operation
- **Efficient updates**: Only changed data is updated

## Data Migration

### Version Management

When data structure changes:

1. **Version detection**: Check current data version
2. **Migration scripts**: Transform old data to new format
3. **Backup creation**: Create backup before migration
4. **Validation**: Verify migrated data integrity

### Migration Example

```php
// Check if migration is needed
$current_version = get_option('dashmate_data_version', '1.0');
if (version_compare($current_version, '2.0', '<')) {
    // Perform migration
    $dashboard_data = get_option('dashmate_dashboard_data', array());
    $migrated_data = migrate_dashboard_data($dashboard_data);
    update_option('dashmate_dashboard_data', $migrated_data);
    update_option('dashmate_data_version', '2.0');
}
```

## Backup and Recovery

### Automatic Backups

- **Pre-migration backups**: Automatic backup before data structure changes
- **Error recovery**: Rollback capability for failed operations
- **Data integrity**: Validation before and after operations

### Manual Backups

```php
// Create backup
$dashboard_data = get_option('dashmate_dashboard_data', array());
$backup_data = array(
    'timestamp' => current_time('timestamp'),
    'data' => $dashboard_data
);
update_option('dashmate_dashboard_data_backup', $backup_data);

// Restore backup
$backup = get_option('dashmate_dashboard_data_backup', array());
if (!empty($backup['data'])) {
    update_option('dashmate_dashboard_data', $backup['data']);
}
```

## Security Considerations

### Data Sanitization

- **Input sanitization**: All user input is sanitized
- **Output escaping**: Data is escaped when displayed
- **Nonce verification**: Form submissions are verified
- **Capability checks**: User permissions are validated

### Access Control

- **WordPress capabilities**: Uses WordPress user capabilities
- **Admin-only access**: Dashboard management requires admin privileges
- **API authentication**: REST API endpoints are properly secured

## Best Practices

### Data Management

1. **Regular backups**: Create regular backups of dashboard data
2. **Validation**: Always validate data before saving
3. **Error handling**: Implement proper error handling for data operations
4. **Performance**: Monitor and optimize data access patterns

### Development

1. **Schema evolution**: Plan for data structure changes
2. **Migration testing**: Test migrations thoroughly
3. **Backup strategy**: Implement comprehensive backup strategy
4. **Documentation**: Document data structures and relationships

## Related Documentation

- [Getting Started](../getting-started.md) - Basic concepts and layout structure
- [Dashboard Endpoints](../endpoints/dashboard.md) - Dashboard data management
- [Layouts Endpoints](../endpoints/layouts.md) - Layout storage and management
- [Error Handling](../error-handling.md) - Data validation and error handling
