# Error Handling

## Overview

This guide covers error handling in the Dashmate API, including error codes, response formats, and troubleshooting tips.

## Error Response Format

All error responses follow a consistent format:

```json
{
  "code": "error_code",
  "message": "Error message",
  "data": {
    "status": 400
  }
}
```

## Error Codes

| Code | Description | HTTP Status |
|------|-------------|-------------|
| `invalid_columns` | Columns data is invalid | 400 |
| `invalid_column_widgets` | Column widgets data is invalid | 400 |
| `save_error` | Unable to save dashboard data | 500 |
| `widget_not_found` | Widget not found | 404 |
| `column_not_found` | Column not found | 404 |
| `invalid_column` | Column data is invalid | 400 |
| `layout_not_found` | Layout not found | 404 |
| `layouts_retrieval_error` | Failed to retrieve layouts list | 500 |
| `layout_retrieval_error` | Failed to retrieve layout data | 500 |
| `internal_error` | Internal server error | 500 |

## Layout-Specific Errors

### Layout Not Found
```json
{
  "success": false,
  "message": "Layout not found: invalid_key",
  "code": "layout_not_found"
}
```

### Current Layout Read-only
```json
{
  "success": false,
  "message": "Cannot apply current layout as it is read-only.",
  "code": "current_layout_readonly"
}
```

### Current Layout Not Found
```json
{
  "success": false,
  "message": "No layout data found",
  "code": "current_layout_not_found"
}
```

### Layout Apply Failed
```json
{
  "success": false,
  "message": "Failed to update dashboard data",
  "code": "layout_apply_failed"
}
```

### JSON Conversion Failed
```json
{
  "success": false,
  "message": "Failed to convert layout data to JSON",
  "code": "layout_json_conversion_failed"
}
```

### Invalid Layout Data
```json
{
  "success": false,
  "message": "Invalid layout data structure.",
  "code": "invalid_layout_data"
}
```

## Troubleshooting

### Common Issues

1. **Invalid Column/Widget IDs**: Ensure IDs are alphanumeric with only hyphens and underscores allowed
2. **Missing Required Fields**: Check that all required fields are provided in requests
3. **Invalid JSON**: Verify request body is valid JSON format
4. **Permission Issues**: Ensure proper authentication (currently allows all requests for development)

### Debugging Tips

- Check the HTTP status code in the response
- Review the error message for specific details
- Verify request format matches API documentation
- Ensure all required parameters are provided

## Development Notes

- All endpoints currently allow all requests for development purposes
- Error responses include detailed messages for debugging
- Widget settings are validated against widget-specific schemas
- Layout files use JSON format with widgets directly nested within columns
