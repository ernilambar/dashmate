# Development Notes

## Overview

This document provides development guidelines, best practices, and technical notes for working with the Dashmate API.

## Development Environment

### Current Development State

- **Authentication**: Currently allows all requests for development purposes
- **Error Handling**: Comprehensive error handling with detailed messages
- **Validation**: Widget settings are validated against widget-specific schemas
- **Layout Files**: Use JSON format with widgets directly nested within columns

### Development Guidelines

#### API Development

1. **WordPress Standards**: Follow WordPress coding standards and best practices
2. **REST API Patterns**: Use WordPress REST API patterns and conventions
3. **Error Handling**: Use `WP_Error` objects for error conditions
4. **Data Validation**: Implement proper input validation and sanitization

#### Widget Development

1. **Widget Classes**: Extend `Abstract_Widget` for new widget types
2. **Template System**: Use template-based rendering for consistency
3. **Settings Schema**: Define comprehensive settings schemas
4. **Security**: Implement proper content sanitization and validation

## Technical Architecture

### Widget System

Widgets are organized in a modular system:

```
Widgets/
├── Sample_HTML.php
├── Sample_Links.php
├── Sample_Progress_Circles.php
└── Sample_Tabular.php
```

### API Structure

The API follows a controller-based architecture:

```
API/
├── API_Main.php
├── Base_Controller.php
├── Columns_Controller.php
├── Dashboard_Controller.php
├── Layouts_Controller.php
└── Widgets_Controller.php
```

### Data Flow

1. **Request Handling**: REST API endpoints receive requests
2. **Validation**: Input data is validated and sanitized
3. **Processing**: Business logic is executed
4. **Response**: Structured JSON response is returned

## Development Best Practices

### Code Organization

1. **Modular Design**: Keep components modular and focused
2. **Separation of Concerns**: Separate data, logic, and presentation
3. **Consistent Naming**: Use consistent naming conventions
4. **Documentation**: Document all public methods and classes

### Error Handling

1. **WP_Error Usage**: Use WordPress `WP_Error` objects for errors
2. **Validation**: Validate all input data before processing
3. **Logging**: Implement proper error logging for debugging
4. **User Feedback**: Provide meaningful error messages to users

### Security

1. **Input Sanitization**: Sanitize all user input
2. **Output Escaping**: Escape output to prevent XSS
3. **Nonce Verification**: Verify nonces for form submissions
4. **Capability Checks**: Check user capabilities before operations

### Performance

1. **Caching**: Use WordPress caching mechanisms
2. **Database Optimization**: Minimize database queries
3. **Lazy Loading**: Load data only when needed
4. **Asset Optimization**: Optimize CSS and JavaScript assets

## Widget Development

### Creating New Widgets

1. **Extend Base Class**: Extend `Abstract_Widget`
2. **Define Settings**: Create settings schema
3. **Implement Template**: Create template file
4. **Add Styling**: Create CSS for widget appearance
5. **Register Widget**: Widget is auto-discovered

### Widget Template System

Templates are located in the resources directory:

```
resources/
├── components/
│   └── widgets/
│       ├── HtmlWidget.js
│       ├── Links.js
│       ├── ProgressCirclesWidget.js
│       └── TabularWidget.js
└── css/
    └── widgets/
        ├── html.css
        ├── links.css
        ├── progress-circles.css
        ├── line-chart.css
        └── tabular.css
```

### Settings Schema

Define comprehensive settings schemas:

```json
{
  "title": {
    "type": "text",
    "label": "Widget Title",
    "description": "Enter the widget title",
    "default": "My Widget"
  },
  "content": {
    "type": "text",
    "label": "Widget Content",
    "description": "Enter the widget content",
    "default": ""
  }
}
```

## API Development

### Endpoint Structure

Follow WordPress REST API conventions:

- **Namespace**: `dashmate/v1`
- **Base URL**: `/wp-json/dashmate/v1/`
- **HTTP Methods**: Use appropriate HTTP methods (GET, POST, PUT, DELETE)
- **Response Format**: Consistent JSON response structure

### Controller Pattern

Controllers handle specific resource types:

- **Dashboard_Controller**: Dashboard management
- **Layouts_Controller**: Layout operations
- **Widgets_Controller**: Widget management
- **Columns_Controller**: Column CRUD operations

### Response Format

All API responses follow a consistent format:

```json
{
  "success": true,
  "data": {
    // Response data
  }
}
```

## Testing

### Unit Testing

1. **Widget Testing**: Test individual widget functionality
2. **API Testing**: Test API endpoints and responses
3. **Integration Testing**: Test complete workflows
4. **Error Testing**: Test error conditions and edge cases

### Testing Tools

- **WordPress Testing**: Use WordPress testing framework
- **API Testing**: Use tools like Postman or curl
- **Frontend Testing**: Test React components
- **Browser Testing**: Test in multiple browsers

## Deployment

### Production Considerations

1. **Security**: Enable proper authentication and authorization
2. **Performance**: Optimize for production performance
3. **Monitoring**: Implement proper logging and monitoring
4. **Backup**: Ensure proper backup procedures

### Environment Configuration

1. **Development**: Allow all requests for development
2. **Staging**: Test with production-like settings
3. **Production**: Enable all security measures

## Troubleshooting

### Common Issues

1. **Widget Not Loading**: Check widget registration and template files
2. **API Errors**: Check error logs and response codes
3. **Layout Issues**: Verify layout file format and structure
4. **Performance Problems**: Monitor database queries and caching

### Debugging

1. **WordPress Debug**: Enable WordPress debug mode
2. **Error Logging**: Check WordPress error logs
3. **API Testing**: Use API testing tools
4. **Browser DevTools**: Use browser developer tools

## Future Development

### Planned Features

1. **Enhanced Widget Types**: Additional widget types
2. **Advanced Layouts**: More layout options
3. **User Permissions**: Granular user permissions
4. **API Extensions**: Additional API endpoints

### Architecture Evolution

1. **Modular Architecture**: Continue modular development
2. **Performance Optimization**: Ongoing performance improvements
3. **Security Enhancements**: Regular security updates
4. **User Experience**: Continuous UX improvements

## Related Documentation

- [Getting Started](./getting-started.md) - Basic concepts and setup
- [API Endpoints](./endpoints/) - Detailed endpoint documentation
- [Reference](./reference/) - Technical reference materials
- [Error Handling](./error-handling.md) - Error handling and debugging
