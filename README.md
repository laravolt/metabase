# Laravolt Metabase
Blade component to embed metabase dashboard or question in your website

## Installation
```bash
composer require laravolt/metabase
```

Add following entries to `config/services.php`:
```php
    'metabase' => [
        'url' => env('METABASE_URL'),
        'secret' => env('METABASE_SECRET'),
    ],
```

And finally, update your `.env` file:
```dotenv
METABASE_URL=https://metabase.example.com
METABASE_SECRET=secret
```

## Usage
```php
<x-metabase dashboard="1"></x-metabase>
<x-metabase question="2"></x-metabase>
<x-metabase question="2" width="80%" height="500px"></x-metabase>
 ```

## Common Problems
### Embedding is not enabled for this object.
TODO
### Not found.

