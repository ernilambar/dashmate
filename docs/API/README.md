# Dashmate API Documentation

## Overview

The Dashmate API provides REST endpoints for managing dashboard layouts, widgets, and columns. All endpoints are prefixed with `/wp-json/dashmate/v1/`.

## Base Information

- **Namespace**: `dashmate/v1`
- **Base URL**: `/wp-json/dashmate/v1/`
- **Authentication**: Currently allows all requests for development
- **Response Format**: JSON

## Documentation Structure

This documentation is split into the following sections:

### Core Documentation
- **[Getting Started](./getting-started.md)** - Basic concepts, response format, and layout structure
- **[Error Handling](./error-handling.md)** - Error codes, response formats, and troubleshooting

### Endpoint Documentation
- **[Dashboard Endpoints](./endpoints/dashboard.md)** - Dashboard management and reordering
- **[Layouts Endpoints](./endpoints/layouts.md)** - Layout management and application
- **[Widgets Endpoints](./endpoints/widgets.md)** - Widget management and content
- **[Columns Endpoints](./endpoints/columns.md)** - Column CRUD operations

### Reference Documentation
- **[Widget Types](./reference/widget-types.md)** - Available widget types and their configurations
- **[Field Types](./reference/field-types.md)** - Supported form field types for widget settings
- **[Data Storage](./reference/data-storage.md)** - How data is stored and structured

### Development
- **[Development Notes](./development.md)** - Development guidelines and best practices

## Quick Start

1. Read the [Getting Started](./getting-started.md) guide for basic concepts
2. Review the [Dashboard Endpoints](./endpoints/dashboard.md) for core functionality
3. Explore [Widget Types](./reference/widget-types.md) to understand available widgets
4. Check [Error Handling](./error-handling.md) for troubleshooting

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
