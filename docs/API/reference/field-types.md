# Field Types

## Overview

The Dashmate API supports various field types for widget settings forms. This reference covers all supported field types, their configurations, and usage examples.

## Supported Field Types

### Text Field

Basic text input field for single-line text.

```json
{
  "type": "text",
  "label": "Field Label",
  "description": "Optional description",
  "default": "default value"
}
```

**Properties**:
- `type` (string): Must be "text"
- `label` (string): Field display label
- `description` (string, optional): Help text
- `default` (string, optional): Default value

**Use Cases**:
- Widget titles
- Short text inputs
- Single-line content

### Email Field

Text input field specifically for email addresses with email validation.

```json
{
  "type": "email",
  "label": "Email Address",
  "description": "Enter your email address",
  "default": "user@example.com"
}
```

**Properties**:
- `type` (string): Must be "email"
- `label` (string): Field display label
- `description` (string, optional): Help text
- `default` (string, optional): Default email value

**Use Cases**:
- User email addresses
- Contact forms
- Notification settings

### Password Field

Secure text input field for password entry with masked input.

```json
{
  "type": "password",
  "label": "Password",
  "description": "Enter your password",
  "default": ""
}
```

**Properties**:
- `type` (string): Must be "password"
- `label` (string): Field display label
- `description` (string, optional): Help text
- `default` (string, optional): Default password value (usually empty for security)

**Use Cases**:
- User authentication
- API credentials
- Secure data entry

### URL Field

Text input field specifically for URLs with validation.

```json
{
  "type": "url",
  "label": "URL Field",
  "description": "Enter a valid URL",
  "default": "https://example.com"
}
```

**Properties**:
- `type` (string): Must be "url"
- `label` (string): Field display label
- `description` (string, optional): Help text
- `default` (string, optional): Default URL value

**Use Cases**:
- External links
- API endpoints
- Resource URLs

### Checkbox Field

Boolean input field for true/false values.

```json
{
  "type": "checkbox",
  "label": "Enable Feature",
  "description": "Check to enable this feature",
  "default": false
}
```

**Properties**:
- `type` (string): Must be "checkbox"
- `label` (string): Field display label
- `description` (string, optional): Help text
- `default` (boolean, optional): Default checked state

**Use Cases**:
- Feature toggles
- Boolean settings
- Enable/disable options

### Select Field

Dropdown selection field with predefined options.

```json
{
  "type": "select",
  "label": "Choose Option",
  "description": "Select from available options",
  "default": "option1",
  "choices": [
    {
      "value": "option1",
      "label": "Option 1"
    },
    {
      "value": "option2",
      "label": "Option 2"
    }
  ]
}
```

**Properties**:
- `type` (string): Must be "select"
- `label` (string): Field display label
- `description` (string, optional): Help text
- `default` (string, optional): Default selected value
- `choices` (array): Array of choice objects

**Choice Object**:
- `value` (string): Option value
- `label` (string): Option display text

**Use Cases**:
- Theme selection
- Display options
- Configuration choices

### Number Field

Numeric input field with optional constraints.

```json
{
  "type": "number",
  "label": "Number Input",
  "description": "Enter a number",
  "default": 5,
  "min": 1,
  "max": 10,
  "choices": [
    {
      "value": 1,
      "label": "One"
    },
    {
      "value": 5,
      "label": "Five"
    }
  ]
}
```

**Properties**:
- `type` (string): Must be "number"
- `label` (string): Field display label
- `description` (string, optional): Help text
- `default` (number, optional): Default numeric value
- `min` (number, optional): Minimum allowed value
- `max` (number, optional): Maximum allowed value
- `choices` (array, optional): Predefined numeric choices

**Use Cases**:
- Quantity settings
- Numeric configurations
- Range inputs

### Radio Field

Single selection from multiple options using radio buttons.

```json
{
  "type": "radio",
  "label": "Choose One",
  "description": "Select one option",
  "default": "option1",
  "choices": [
    {
      "value": "option1",
      "label": "Option 1"
    },
    {
      "value": "option2",
      "label": "Option 2"
    }
  ]
}
```

**Properties**:
- `type` (string): Must be "radio"
- `label` (string): Field display label
- `description` (string, optional): Help text
- `default` (string, optional): Default selected value
- `choices` (array): Array of choice objects

**Use Cases**:
- Single choice selections
- Display preferences
- Configuration options

### Buttonset Field

Visual button group for single selection.

```json
{
  "type": "buttonset",
  "label": "Choose Style",
  "description": "Select display style",
  "default": "list",
  "choices": [
    {
      "value": "list",
      "label": "List"
    },
    {
      "value": "grid",
      "label": "Grid"
    }
  ]
}
```

**Properties**:
- `type` (string): Must be "buttonset"
- `label` (string): Field display label
- `description` (string, optional): Help text
- `default` (string, optional): Default selected value
- `choices` (array): Array of choice objects

**Use Cases**:
- Display style selection
- Layout options
- Visual preference settings

### Multicheckbox Field

Multiple selection using checkboxes.

```json
{
  "type": "multicheckbox",
  "label": "Select Items",
  "description": "Choose multiple items",
  "default": ["item1", "item2"],
  "choices": [
    {
      "value": "item1",
      "label": "Item 1"
    },
    {
      "value": "item2",
      "label": "Item 2"
    }
  ]
}
```

**Properties**:
- `type` (string): Must be "multicheckbox"
- `label` (string): Field display label
- `description` (string, optional): Help text
- `default` (array, optional): Default selected values
- `choices` (array): Array of choice objects

**Use Cases**:
- Multiple feature selection
- Category selection
- Filter options

### Sortable Field

Advanced field for reorderable and toggleable items.

```json
{
  "type": "sortable",
  "label": "Sortable Items",
  "description": "Drag to reorder and toggle to enable/disable items",
  "default": ["item1", "item2"],
  "choices": [
    {
      "value": "item1",
      "label": "Item 1"
    },
    {
      "value": "item2",
      "label": "Item 2"
    },
    {
      "value": "item3",
      "label": "Item 3"
    }
  ]
}
```

**Properties**:
- `type` (string): Must be "sortable"
- `label` (string): Field display label
- `description` (string, optional): Help text
- `default` (array, optional): Default enabled items in order
- `choices` (array): Array of choice objects

**Features**:
- **Drag and drop reordering**: Users can drag items to change their order
- **Toggle functionality**: Each item has a checkbox to enable/disable it
- **Vertical-only dragging**: Items can only be dragged vertically
- **Array output**: The saved value is an array of enabled item values in the specified order
- **Visual feedback**: Disabled items are visually dimmed, and dragging provides visual feedback

**Use Cases**:
- Menu item ordering
- Widget arrangement
- Feature prioritization
- Custom ordering preferences

## Field Validation

### Common Validation Rules

- **Required Fields**: Marked with `required: true`
- **Type Validation**: Values must match expected field type
- **Range Validation**: Numbers must be within min/max bounds
- **URL Validation**: URLs must be properly formatted
- **Choice Validation**: Values must be from predefined choices

### Custom Validation

Widget developers can implement custom validation:

- Field-specific validation rules
- Cross-field validation
- Business logic validation
- Format requirements

## Field Rendering

### Frontend Implementation

Fields are rendered using React components:

- Each field type has a corresponding React component
- Components handle user interaction and validation
- Real-time validation feedback
- Consistent styling across all field types

### Styling

Fields follow consistent design patterns:

- Standard form styling
- Responsive design
- Accessibility features
- Visual feedback for interactions

## Best Practices

### Field Design

1. **Clear Labels**: Use descriptive, user-friendly labels
2. **Helpful Descriptions**: Provide context and guidance
3. **Sensible Defaults**: Set appropriate default values
4. **Logical Choices**: Order choices logically

### User Experience

1. **Validation Feedback**: Provide clear error messages
2. **Progressive Disclosure**: Show advanced options when needed
3. **Consistent Behavior**: Maintain consistent interaction patterns
4. **Accessibility**: Ensure all fields are accessible

### Data Management

1. **Type Safety**: Use appropriate field types for data
2. **Validation**: Implement proper validation rules
3. **Sanitization**: Clean and sanitize user input
4. **Persistence**: Handle field state properly

## Related Documentation

- [Widget Types](./widget-types.md) - Available widget types
- [Widgets Endpoints](../endpoints/widgets.md) - Widget management API
- [Error Handling](../error-handling.md) - Validation and error handling
