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
Metabase secret key can be found in metabase settings page (only accessible by admin).
> Visit https://www.metabase.com/docs/latest/administration-guide/13-embedding.html for more information.

## Usage
```blade
<!-- embed dashboard -->
<x-metabase dashboard="1"></x-metabase>

<!-- embed question -->
<x-metabase question="2"></x-metabase>

<!-- passing html attributes -->
<x-metabase question="2" width="80%" height="500px"></x-metabase>

<!-- passing metabase parameters -->
@php($params = ['category' => 'php'])
<x-metabase dashboard="1" :params="$params"></x-metabase> 
// BEWARE of the colon in ":params" (not "param") because we are passing array variable directly to the component
 ```

## Common Problems

### Embedding is not enabled for this object.
Solution: https://www.metabase.com/learn/embedding/embedding-charts-and-dashboards

### Not found.
Invalid dashboard or question ID.

### Message seems corrupt or manipulated.
Invalid secret key.
