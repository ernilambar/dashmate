# DashMate

A WordPress dashboard customization plugin that allows users to create and manage custom dashboard layouts with custom widgets.

## Features

- **Custom Dashboard Layouts**: Create and save multiple dashboard configurations
- **Widget System**: Extensible widget framework with sample widgets included
- **REST API**: Full API for dashboard and widget management
- **Responsive Design**: Works across all device sizes

## Installation

### As a Composer Package

Add Dashmate to your plugin's `composer.json`:

```json
{
    "require": {
        "ernilambar/dashmate": "^1.0"
    }
}
```

Then run:
```bash
composer install
```

### Development Setup

1. **Install PHP Dependencies**
   ```bash
   composer install
   ```

2. **Install Node Dependencies**
   ```bash
   pnpm install
   ```

3. **Build Assets**
   ```bash
   pnpm run build
   ```

4. **Development**
   ```bash
   pnpm run dev
   ```

## Quick Start

### 1. Initialize Dashmate

```php
<?php
// Include Composer autoloader
require_once plugin_dir_path(__FILE__) . 'vendor/autoload.php';

use Nilambar\Dashmate\Core\Dashmate;

// Initialize Dashmate
Dashmate::init();

// Load assets
Dashmate::load_assets(
    plugin_dir_path(__FILE__) . 'vendor/ernilambar/dashmate',
    plugin_dir_url(__FILE__) . 'vendor/ernilambar/dashmate/'
);
```

### 2. Create Dashboard Page

```php
<?php
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

### 3. Create Custom Widget

```php
<?php
use Nilambar\Dashmate\Widgets\Abstract_Widget;

class My_Widget extends Abstract_Widget {
    public function __construct($id) {
        parent::__construct($id, 'my_widget', esc_html__('My Widget', 'my-plugin'));
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
    $widgets['my_widget'] = new My_Widget('my_widget');
    return $widgets;
});
```

## Available Widget Types

- **HTML Widget** (`html`) - Display custom HTML content
- **Line Chart Widget** (`line_chart`) - Display line charts using Recharts
- **Links Widget** (`links`) - Display a list of links
- **Progress Circles Widget** (`progress_circles`) - Display progress circles
- **Tabular Widget** (`tabular`) - Display tabular data

## Requirements

- WordPress 6.6+
- PHP 7.4+
- Node.js 20+

## API Documentation

See [docs/API.md](docs/API.md) for complete REST API reference.

## Hooks and Filters

### Actions
- `dashmate_init` - Fired when Dashmate is initialized
- `dashmate_widgets_loaded` - Fired when widgets are loaded

### Filters
- `dashmate_widgets` - **Primary filter for widget registration** - Add custom widgets to the system
- `dashmate_dashboard_data` - Filter dashboard data
- `dashmate_widget_types` - Filter available widget types
- `dashmate_widget_custom_classes` - Add custom CSS classes to widgets
- `dashmate_widget_metadata` - Add custom metadata to widgets
