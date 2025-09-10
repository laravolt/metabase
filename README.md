# Laravolt Metabase

[![Latest Version on Packagist](https://img.shields.io/packagist/v/laravolt/metabase.svg?style=flat-square)](https://packagist.org/packages/laravolt/metabase)
[![Total Downloads](https://img.shields.io/packagist/dt/laravolt/metabase.svg?style=flat-square)](https://packagist.org/packages/laravolt/metabase)
[![License](https://img.shields.io/github/license/laravolt/metabase.svg?style=flat-square)](https://github.com/laravolt/metabase/blob/master/LICENSE)

A Laravel package that provides seamless integration with Metabase, allowing you to embed dashboards and questions directly into your Laravel applications using Blade components.

## Features

- 🚀 **Easy Integration** - Simple Blade component for embedding Metabase content
- 🔐 **Secure Authentication** - JWT-based secure embedding with configurable secrets
- 🎨 **Customizable UI** - Support for themes, borders, titles, and custom styling
- 📊 **Dynamic Parameters** - Pass dynamic parameters to dashboards and questions
- 🛣️ **Route Integration** - Optional web routes for standalone embed pages
- ⚙️ **Configurable** - Flexible configuration options for different environments

## Table of Contents

- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
  - [Basic Usage](#basic-usage)
  - [Advanced Usage](#advanced-usage)
  - [Route Integration](#route-integration)
- [API Reference](#api-reference)
- [Troubleshooting](#troubleshooting)
- [Contributing](#contributing)
- [License](#license)

## Installation

Install the package via Composer:

```bash
composer require laravolt/metabase
```

The package will automatically register its service provider.

### Publishing Assets (Optional)

You can publish the configuration file:

```bash
php artisan vendor:publish --tag=metabase-config
```

You can also publish the views for customization:

```bash
php artisan vendor:publish --tag=metabase-views
```

## Configuration

### 1. Environment Configuration

Add the following entries to your `config/services.php`:

```php
'metabase' => [
    'url' => env('METABASE_URL'),
    'secret' => env('METABASE_SECRET'),
],
```

### 2. Environment Variables

Update your `.env` file:

```dotenv
METABASE_URL=https://metabase.example.com
METABASE_SECRET=your-metabase-secret-key
```

> **Finding your Metabase Secret Key:**
> 1. Log into your Metabase instance as an admin
> 2. Go to Settings → Admin settings → Embedding
> 3. Enable embedding and copy the secret key
> 
> For more information, visit the [Metabase Embedding Documentation](https://www.metabase.com/docs/latest/administration-guide/13-embedding.html).

### 3. Package Configuration

The package configuration file (`config/metabase.php`) includes:

```php
return [
    'route' => [
        'enabled' => true,                    // Enable/disable web routes
        'middleware' => ['web', 'auth'],      // Middleware for routes
        'prefix' => 'metabase',               // Route prefix
    ],
    'view' => [
        'layout' => 'laravolt::layouts.centered', // Layout for embed pages
    ],
];
```

## Usage

### Basic Usage

#### Embed a Dashboard

```blade
<x-metabase dashboard="1"></x-metabase>
```

#### Embed a Question

```blade
<x-metabase question="2"></x-metabase>
```

#### Custom Dimensions

```blade
<x-metabase question="2" width="80%" height="500px"></x-metabase>
```

### Advanced Usage

#### Passing Parameters

```blade
@php($params = ['category' => 'electronics', 'date_range' => '2023-01-01~2023-12-31'])
<x-metabase dashboard="1" :params="$params"></x-metabase>
```

> **Note:** Use `:params` (with colon) when passing array variables to ensure proper binding.

#### Styling Options

```blade
<x-metabase 
    dashboard="1" 
    :params="$params" 
    :bordered="true" 
    :titled="true" 
    theme="night"
    width="100%"
    height="600px"
    style="border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);"
></x-metabase>
```

#### Available Themes

- `transparent` (default)
- `night` - Dark theme

#### Complete Example

```blade
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Sales Dashboard</h1>
    
    @php($filters = [
        'start_date' => request('start_date', now()->startOfMonth()->format('Y-m-d')),
        'end_date' => request('end_date', now()->endOfMonth()->format('Y-m-d')),
        'region' => request('region', 'all')
    ])
    
    <x-metabase 
        dashboard="3"
        :params="$filters"
        :bordered="true"
        :titled="true"
        theme="transparent"
        width="100%"
        height="800px"
        class="shadow-lg rounded-lg"
    ></x-metabase>
</div>
@endsection
```

### Route Integration

When routes are enabled, you can access embedded content via URLs:

```
GET /metabase/embed/{id}
```

This is useful for creating shareable links or iframe sources for external applications.

## API Reference

### MetabaseComponent

The main Blade component for embedding Metabase content.

#### Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `dashboard` | `int\|null` | `null` | Dashboard ID to embed |
| `question` | `int\|null` | `null` | Question ID to embed |
| `params` | `array` | `[]` | Parameters to pass to Metabase |
| `bordered` | `bool` | `false` | Show border around embed |
| `titled` | `bool` | `false` | Show title in embed |
| `theme` | `string\|null` | `null` | Theme to apply (`night` or `transparent`) |

#### HTML Attributes

All standard HTML attributes are supported and will be passed to the iframe:

- `width` (default: `100%`)
- `height` (default: `800px`)
- `style`, `class`, `id`, etc.

### MetabaseService

The core service class for generating embed URLs.

#### Methods

##### `setParams(array $params): void`

Set parameters to pass to the embedded content.

##### `setAdditionalParams(array $params): void`

Set additional UI parameters (bordered, titled, theme).

##### `generateEmbedUrl(?int $dashboard, ?int $question): string`

Generate the secure embed URL with JWT token.

**Throws:**
- `InvalidArgumentException` - When configuration is missing or invalid

### Configuration Options

#### Route Configuration

```php
'route' => [
    'enabled' => true,                    // Enable web routes
    'middleware' => ['web', 'auth'],      // Applied middleware
    'prefix' => 'metabase',               // URL prefix
]
```

#### View Configuration

```php
'view' => [
    'layout' => 'laravolt::layouts.centered', // Layout for embed pages
]
```

## Troubleshooting

### Common Issues

#### "Embedding is not enabled for this object"

**Solution:** Enable embedding in Metabase:
1. Go to the dashboard/question in Metabase
2. Click the sharing icon
3. Enable "Embed this dashboard/question"
4. Configure embedding parameters as needed

**Reference:** [Metabase Embedding Guide](https://www.metabase.com/learn/embedding/embedding-charts-and-dashboards)

#### "Not found" Error

**Causes:**
- Invalid dashboard or question ID
- Dashboard/question has been deleted
- User doesn't have access to the object

**Solution:** Verify the ID exists and is accessible.

#### "Message seems corrupt or manipulated"

**Causes:**
- Invalid or missing secret key
- Secret key doesn't match Metabase configuration
- JWT token generation issues

**Solution:** 
1. Verify `METABASE_SECRET` in your `.env` file
2. Ensure the secret matches your Metabase embedding settings
3. Clear application cache: `php artisan config:clear`

#### Iframe Loading Issues

**Possible causes:**
- CORS restrictions
- X-Frame-Options headers
- Network connectivity issues

**Solutions:**
1. Check browser developer console for errors
2. Verify Metabase server is accessible
3. Configure CORS settings in Metabase if needed

#### Parameters Not Working

**Common mistakes:**
- Using `params` instead of `:params` in Blade
- Parameter names don't match Metabase field names
- Parameter values in wrong format

**Solution:**
```blade
<!-- ✅ Correct -->
<x-metabase dashboard="1" :params="['status' => 'active']"></x-metabase>

<!-- ❌ Incorrect -->
<x-metabase dashboard="1" params="['status' => 'active']"></x-metabase>
```

### Debug Mode

To debug embedding issues, you can temporarily log the generated URLs:

```php
// In a service provider or controller
$metabase = app(\Laravolt\Metabase\MetabaseService::class);
$metabase->setParams($params);
$url = $metabase->generateEmbedUrl($dashboard, null);
\Log::info('Metabase embed URL: ' . $url);
```

### Performance Considerations

- **Caching:** Consider caching embed URLs for frequently accessed content
- **Loading:** Use appropriate iframe dimensions to avoid layout shifts
- **Parameters:** Minimize the number of dynamic parameters for better performance

## Requirements

- PHP 8.2 or higher
- Laravel 8.0 or higher
- Valid Metabase instance with embedding enabled

## Contributing

We welcome contributions! Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

## Security

If you discover any security-related issues, please email the maintainers instead of using the issue tracker.

## License

This package is open-sourced software licensed under the [MIT License](LICENSE).
