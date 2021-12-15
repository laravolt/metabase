# Laravolt Metabase
Blade component to embed metabase dashboard or question in your website.
For more information about Metabase, please visit [Metabase official website](https://metabase.com/).

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
Solution: https://www.metabase.com/learn/embedding/embedding-charts-and-dashboards

### Not found.
Solution: make sure your dashboard or question ID was correct

### Message seems corrupt or manipulated.
Solution: check your secret key.
