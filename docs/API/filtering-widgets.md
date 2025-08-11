# Filtering Widgets

This document explains how to filter widgets in Dashmate using the filter-based approach.

## Overview

Dashmate uses WordPress filters to allow developers to control which widgets appear in dashboard data. This provides a flexible way to customize widget behavior.

## Available Filters

### `dashmate_dashboard_data`

This filter allows you to modify the dashboard data, including filtering out specific widgets from columns.

**Hook:** `dashmate_dashboard_data`

**Parameters:**
- `$data` (array) - Dashboard data structure

**Returns:** Modified dashboard data

**Example:**
```php
add_filter( 'dashmate_dashboard_data', function( $data ) {
    // Filter out specific widgets from all columns
    if ( isset( $data['columns'] ) && is_array( $data['columns'] ) ) {
        foreach ( $data['columns'] as &$column ) {
            if ( isset( $column['widgets'] ) && is_array( $column['widgets'] ) ) {
                $column['widgets'] = array_values(
                    array_filter(
                        $column['widgets'],
                        function( $widget ) {
                            // Remove widgets with specific IDs
                            $disabled_ids = ['widget-1', 'widget-2'];
                            return ! isset( $widget['id'] ) || ! in_array( $widget['id'], $disabled_ids, true );
                        }
                    )
                );
            }
        }
    }

    return $data;
});
```

## Best Practices

1. **Use `dashmate_dashboard_data`** when you want to filter widgets from dashboard instances
2. **Always return arrays** from filters to maintain data integrity
3. **Check for array existence** before modifying data structures
4. **Use descriptive variable names** in your filter callbacks

## Advanced Usage

### Conditional Filtering

```php
add_filter( 'dashmate_dashboard_data', function( $data ) {
    // Only show certain widgets for admin users
    if ( ! current_user_can( 'manage_options' ) ) {
        if ( isset( $data['columns'] ) && is_array( $data['columns'] ) ) {
            foreach ( $data['columns'] as &$column ) {
                if ( isset( $column['widgets'] ) && is_array( $column['widgets'] ) ) {
                    $column['widgets'] = array_values(
                        array_filter(
                            $column['widgets'],
                            function( $widget ) {
                                // Remove admin-only widgets for non-admin users
                                $admin_only_widgets = ['admin-widget-1', 'admin-widget-2'];
                                return ! isset( $widget['id'] ) || ! in_array( $widget['id'], $admin_only_widgets, true );
                            }
                        )
                    );
                }
            }
        }
    }

    return $data;
});
```

### Widget Data Modification

```php
add_filter( 'dashmate_dashboard_data', function( $data ) {
    // Modify widget settings or add custom data
    if ( isset( $data['columns'] ) && is_array( $data['columns'] ) ) {
        foreach ( $data['columns'] as &$column ) {
            if ( isset( $column['widgets'] ) && is_array( $column['widgets'] ) ) {
                foreach ( $column['widgets'] as &$widget ) {
                    // Add custom data to specific widgets
                    if ( isset( $widget['id'] ) && $widget['id'] === 'sample-html' ) {
                        $widget['custom_data'] = 'Additional information';
                    }
                }
            }
        }
    }

    return $data;
});
```
