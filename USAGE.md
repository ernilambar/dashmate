# Dashmate Usage Guide

Dashmate is a WordPress dashboard widget system that can be used as a Composer package in your WordPress plugins.

## Installation

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

## Basic Usage

### 1. Initialize Dashmate

In your plugin's main file, initialize Dashmate:

```php
<?php
/**
 * Plugin Name: My Plugin
 */

// Include Composer autoloader
require_once plugin_dir_path(__FILE__) . 'vendor/autoload.php';

use Nilambar\Dashmate\Dashmate;

// Initialize Dashmate
Dashmate::init();

// Load assets (set the paths where Dashmate is installed)
Dashmate::load_assets(
    plugin_dir_path(__FILE__) . 'vendor/ernilambar/dashmate',
    plugin_dir_url(__FILE__) . 'vendor/ernilambar/dashmate/'
);
```

### 2. Create Custom Dashboard Page

Create your own dashboard page by extending the abstract class:

```php
<?php

use Nilambar\Dashmate\Admin\Abstract_Dashboard_Page;

class My_Plugin_Dashboard extends Abstract_Dashboard_Page {

    /**
     * Initialize dashboard page properties.
     */
    protected function init_properties() {
        $this->page_slug      = 'my-plugin-dashboard';
        $this->page_title     = esc_html__( 'My Plugin Dashboard', 'my-plugin' );
        $this->menu_title     = esc_html__( 'My Plugin', 'my-plugin' );
        $this->capability     = 'manage_options';
        $this->menu_icon      = 'dashicons-admin-home';
        $this->menu_position  = 0;
        $this->template_name  = 'pages/app';
        $this->dashboard_id   = 'my_plugin_main';
        $this->starter_layout = 'default';
    }
}

// Register the dashboard page
add_action('init', function() {
    new My_Plugin_Dashboard();
});
```

### 3. Create Custom Widgets

Create custom widgets by extending the abstract widget class:

```php
<?php

use Nilambar\Dashmate\Abstract_Widget;

class My_Custom_Widget extends Abstract_Widget {

    /**
     * Constructor.
     */
    public function __construct($id) {
        parent::__construct($id, 'my_custom', esc_html__('My Custom Widget', 'my-plugin'));
    }

    /**
     * Define widget configuration.
     */
    protected function define_widget() {
        $this->description = esc_html__('A custom widget for my plugin.', 'my-plugin');
        $this->icon        = 'admin-tools';

        $this->settings_schema = [
            'title' => [
                'type'        => 'text',
                'label'       => esc_html__('Title', 'my-plugin'),
                'description' => esc_html__('Widget title.', 'my-plugin'),
                'default'     => esc_html__('My Widget', 'my-plugin'),
            ],
            'content' => [
                'type'        => 'textarea',
                'label'       => esc_html__('Content', 'my-plugin'),
                'description' => esc_html__('Widget content.', 'my-plugin'),
                'default'     => '',
            ],
        ];
    }

    /**
     * Get widget content.
     */
    public function get_content(array $settings = []): array {
        $settings = $this->merge_settings_with_defaults($settings);

        return [
            'title'   => $settings['title'],
            'content' => $settings['content'],
        ];
    }
}
```

### 4. Register Custom Widgets

Register your custom widgets:

```php
<?php

use Nilambar\Dashmate\Widget_Registry;

// Register custom widget
add_action('init', function() {
    Widget_Registry::register_widget('my_custom', My_Custom_Widget::class);
});
```

## API Usage

### Dashboard Management

```php
use Nilambar\Dashmate\Dashboard_Manager;

// Get dashboard data
$dashboard_data = Dashboard_Manager::get_dashboard_data('my_dashboard');

// Save dashboard data
$result = Dashboard_Manager::save_dashboard_data($data, 'my_dashboard');

// Check if dashboard exists
$exists = Dashboard_Manager::dashboard_data_exists('my_dashboard');
```

### Widget Management

```php
use Nilambar\Dashmate\Widget_Manager;

// Create a widget
$widget = Widget_Manager::create_widget('my_custom', [
    'title' => 'My Widget Title',
    'content' => 'Widget content here'
], 'column_1');

// Get widget content
$content = Widget_Manager::get_widget_content('widget_id');

// Update widget settings
$result = Widget_Manager::update_widget_settings('widget_id', [
    'title' => 'Updated Title'
]);

// Delete widget
$result = Widget_Manager::delete_widget('widget_id');
```

## Available Widget Types

Dashmate comes with several built-in widget types:

- **HTML Widget** (`html`) - Display custom HTML content
- **Line Chart Widget** (`line_chart`) - Display line charts using Recharts
- **Links Widget** (`links`) - Display a list of links
- **Progress Circles Widget** (`progress_circles`) - Display progress circles
- **Tabular Widget** (`tabular`) - Display tabular data

## REST API Endpoints

Dashmate automatically registers REST API endpoints:

- `GET /wp-json/dashmate/v1/dashboard/{dashboard_id}` - Get dashboard data
- `POST /wp-json/dashmate/v1/dashboard/{dashboard_id}` - Save dashboard data
- `DELETE /wp-json/dashmate/v1/dashboard/{dashboard_id}` - Delete dashboard data
- `GET /wp-json/dashmate/v1/widgets` - Get available widgets
- `GET /wp-json/dashmate/v1/widgets/{widget_id}/content` - Get widget content
- `GET /wp-json/dashmate/v1/columns` - Get column management endpoints
- `GET /wp-json/dashmate/v1/layouts` - Get available layouts

## Hooks and Filters

### Actions

- `dashmate_init` - Fired when Dashmate is initialized
- `dashmate_widgets_loaded` - Fired when widgets are loaded

### Filters

- `dashmate_dashboard_data` - Filter dashboard data
- `dashmate_widget_types` - Filter available widget types
- `dashmate_widget_custom_classes` - Add custom CSS classes to widgets
- `dashmate_widget_metadata` - Add custom metadata to widgets

## Example: Complete Plugin Integration

```php
<?php
/**
 * Plugin Name: My Dashboard Plugin
 * Description: A plugin that uses Dashmate for dashboard management
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Include Composer autoloader
require_once plugin_dir_path(__FILE__) . 'vendor/autoload.php';

use Nilambar\Dashmate\Dashmate;
use Nilambar\Dashmate\Admin\Abstract_Dashboard_Page;
use Nilambar\Dashmate\Abstract_Widget;
use Nilambar\Dashmate\Widget_Registry;

// Initialize Dashmate
Dashmate::init();

// Load assets (set the paths where Dashmate is installed)
Dashmate::load_assets(
    plugin_dir_path(__FILE__) . 'vendor/ernilambar/dashmate',
    plugin_dir_url(__FILE__) . 'vendor/ernilambar/dashmate/'
);

// Custom dashboard page
class My_Dashboard_Page extends Abstract_Dashboard_Page {
    protected function init_properties() {
        $this->page_slug      = 'my-dashboard';
        $this->page_title     = esc_html__('My Dashboard', 'my-plugin');
        $this->menu_title     = esc_html__('My Dashboard', 'my-plugin');
        $this->capability     = 'manage_options';
        $this->menu_icon      = 'dashicons-dashboard';
        $this->menu_position  = 30;
        $this->template_name  = 'pages/app';
        $this->dashboard_id   = 'my_dashboard';
        $this->starter_layout = 'default';
    }
}

// Custom widget
class My_Stats_Widget extends Abstract_Widget {
    public function __construct($id) {
        parent::__construct($id, 'my_stats', esc_html__('My Stats', 'my-plugin'));
    }

    protected function define_widget() {
        $this->description = esc_html__('Display plugin statistics.', 'my-plugin');
        $this->icon        = 'chart-bar';

        $this->settings_schema = [
            'show_users' => [
                'type'        => 'checkbox',
                'label'       => esc_html__('Show User Count', 'my-plugin'),
                'description' => esc_html__('Display total user count.', 'my-plugin'),
                'default'     => true,
            ],
        ];
    }

    public function get_content(array $settings = []): array {
        $settings = $this->merge_settings_with_defaults($settings);

        $content = '<h3>' . esc_html__('Plugin Statistics', 'my-plugin') . '</h3>';

        if ($settings['show_users']) {
            $user_count = count_users()['total_users'];
            $content .= '<p>' . sprintf(
                esc_html__('Total Users: %d', 'my-plugin'),
                $user_count
            ) . '</p>';
        }

        return [
            'html_content' => $content,
        ];
    }
}

// Initialize everything
add_action('init', function() {
    // Register dashboard page
    new My_Dashboard_Page();

    // Register custom widget
    Widget_Registry::register_widget('my_stats', My_Stats_Widget::class);
});
```

## Support

For more information and examples, visit the [Dashmate GitHub repository](https://github.com/ernilambar/dashmate).
