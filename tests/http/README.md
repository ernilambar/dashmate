# HTTP API Tests

This directory contains HTTP test files for testing the Dashmate WordPress plugin REST API endpoints.

## Setup

### 1. Environment Variables

Copy the example environment file to the project root and configure your local settings:

```bash
cp .env.example .env
```

Edit the `.env` file in the project root with your actual values:

```env
DASHMATE_BASE_URL=http://your-wordpress-site.com
WP_NONCE=your-actual-wordpress-nonce
```

### 2. Getting WordPress Nonce

You can obtain a WordPress nonce in several ways:

#### Method 1: From WordPress Admin
1. Log into your WordPress admin
2. Open browser developer tools (F12)
3. Go to Network tab
4. Navigate to any admin page
5. Look for requests with `X-WP-Nonce` header
6. Copy the nonce value

#### Method 2: Using WordPress REST API
```bash
curl -X POST http://your-site.com/wp-json/wp/v2/users/me \
  -H "Content-Type: application/json" \
  -d '{"username":"your-username","password":"your-password"}'
```

#### Method 3: From WordPress Admin Page Source
1. View page source of any WordPress admin page
2. Search for `wpApiSettings` or `nonce`
3. Extract the nonce value

### 3. Testing

Use a REST client extension (like REST Client for VS Code) to run the tests:

1. Open `widgets.http` in your editor
2. Install REST Client extension if not already installed
3. Click "Send Request" above any request to test it

## Available Test Files

- `widgets.http` - Tests for Widgets_Controller endpoints

## Security Notes

- **Never commit `.env` files** to version control (keep in project root)
- **Never commit actual nonces** or credentials
- Use different nonces for different environments
- Rotate nonces regularly for security

## Troubleshooting

### Common Issues

1. **401 Unauthorized**: Check your nonce value and ensure it's valid
2. **404 Not Found**: Verify your BASE_URL is correct
3. **403 Forbidden**: Ensure your WordPress user has proper permissions

### Debug Mode

Enable WordPress debug mode to see detailed error messages:

```php
// In wp-config.php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
```

## Contributing

When adding new test files:

1. Use environment variables for sensitive data
2. Include comprehensive test cases
3. Add error handling tests
4. Document any special requirements
5. Follow the existing naming conventions
