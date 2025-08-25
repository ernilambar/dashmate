# DashMate

A WordPress dashboard customization plugin that allows users to create and manage custom dashboard layouts with drag-and-drop widgets.

## Features

- **Custom Dashboard Layouts**: Create and save multiple dashboard configurations
- **Widget System**: Extensible widget framework with sample widgets included
- **Drag & Drop Interface**: Intuitive layout management
- **REST API**: Full API for dashboard and widget management
- **Responsive Design**: Works across all device sizes

## Quick Start

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

## Project Structure

- `app/` - PHP backend classes
- `resources/` - Frontend React components and assets
- `layouts/` - Default layout configurations
- `docs/` - API documentation and guides

## Requirements

- WordPress 6.6+
- PHP 7.4+
- Node.js 20+

## Documentation

See `docs/` directory for detailed API documentation and development guides.
