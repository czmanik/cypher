# Storyous Integration

This document describes the Storyous POS integration in the Cypher93 project.

## Overview

The integration fetches daily sales data (bills) from the Storyous API to display revenue statistics in the admin dashboard.

### Key Features
- **Daily Revenue Widget**: Displays current day's revenue in the dashboard.
- **Detailed Stats Page**: Allows managers to view specific bills and daily totals.
- **Caching**: Data is cached for performance (15 mins for current day, 24 hours for past days).

## Configuration

To enable the integration, you must configure the API credentials in the Admin Panel.

1. Navigate to **Nastavení -> Storyous API**.
2. Enter the following details provided by Storyous support or your POS administration:
   - **Client ID**: Your application ID.
   - **Client Secret**: Your application secret.
   - **Merchant ID**: The unique identifier for your merchant account.
   - **Place ID**: The unique identifier for the specific branch/place.

> **Note**: Access to these settings is restricted to users with the `is_admin` role.

## Testing the Connection

Two methods are available for testing:

### 1. Admin Panel Check (Auth Only)
- Go to **Nastavení -> Storyous API**.
- Click the **Ověřit spojení** button.
- *This only verifies that the Client ID and Secret are valid and can obtain a token.*

### 2. Console Diagnostic Command (Full Test)
For a complete test that verifies both authentication and data loading (bills), use the artisan command:

```bash
php artisan storyous:test
```

This command will:
1. Check if settings are present.
2. Attempt to authenticate and obtain a token.
3. Attempt to fetch bills for the current day using the configured Place ID.
4. Output detailed success/failure messages and API responses.

## Troubleshooting

### Data Not Loading
If authentication succeeds but data shows 0 or is missing:
- **Check Place ID/Merchant ID**: Ensure they match exactly what is in Storyous. A wrong ID will result in 404 or empty data.
- **Check Logs**: Application logs (`storage/logs/laravel.log`) contain detailed error messages, including the exact URL requested.
- **Clear Cache**: If you recently fixed settings but see old data, clear the cache:
  ```bash
  php artisan cache:clear
  ```

### API Errors
- **401 Unauthorized**: Invalid Client ID/Secret.
- **404 Not Found**: Invalid Merchant ID or Place ID in the endpoint URL.
- **400 Bad Request**: Malformed request parameters (should not happen with current code).

## Technical Details

- **Service**: `App\Services\StoryousService`
- **Settings**: `App\Settings\StoryousSettings`
- **Auth Endpoint**: `https://login.storyous.com/api/auth/authorize` (Client Credentials)
- **Data Endpoint**: `https://api.storyous.com/bills/{merchantId}-{placeId}`
- **Parameters**: `from` (ISO8601), `till` (ISO8601)

## Development

When modifying `StoryousService`, ensure you update the `StoryousTestConnection` command if API logic changes.
