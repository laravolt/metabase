# Advanced Usage Guide

This guide covers advanced usage patterns and techniques for the Laravolt Metabase package.

## Table of Contents

- [Custom Service Implementation](#custom-service-implementation)
- [Advanced Parameter Handling](#advanced-parameter-handling)
- [Caching Strategies](#caching-strategies)
- [Custom Views and Layouts](#custom-views-and-layouts)
- [Security Considerations](#security-considerations)
- [Performance Optimization](#performance-optimization)
- [Integration Patterns](#integration-patterns)

## Custom Service Implementation

### Extending the MetabaseService

You can extend the base service to add custom functionality:

```php
<?php

namespace App\Services;

use Laravolt\Metabase\MetabaseService;
use Illuminate\Support\Facades\Cache;

class CustomMetabaseService extends MetabaseService
{
    /**
     * Generate cached embed URL with custom TTL.
     */
    public function getCachedEmbedUrl(?int $dashboard, ?int $question, int $ttl = 3600): string
    {
        $cacheKey = "metabase_embed_{$dashboard}_{$question}_" . md5(serialize($this->params));
        
        return Cache::remember($cacheKey, $ttl, function () use ($dashboard, $question) {
            return $this->generateEmbedUrl($dashboard, $question);
        });
    }
    
    /**
     * Generate embed URL with user context.
     */
    public function generateUserContextEmbedUrl(?int $dashboard, ?int $question, $user): string
    {
        // Add user-specific parameters
        $userParams = [
            'user_id' => $user->id,
            'user_role' => $user->role,
            'tenant_id' => $user->tenant_id ?? null,
        ];
        
        $this->setParams(array_merge($this->params, $userParams));
        
        return $this->generateEmbedUrl($dashboard, $question);
    }
}
```

### Registering Custom Service

In your `AppServiceProvider`:

```php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\CustomMetabaseService;
use Laravolt\Metabase\MetabaseService;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(MetabaseService::class, CustomMetabaseService::class);
    }
}
```

## Advanced Parameter Handling

### Dynamic Parameter Resolution

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravolt\Metabase\MetabaseService;

class DashboardController extends Controller
{
    public function show(Request $request, MetabaseService $metabase)
    {
        $params = $this->resolveParameters($request);
        
        $metabase->setParams($params);
        $embedUrl = $metabase->generateEmbedUrl(1, null);
        
        return view('dashboard.show', compact('embedUrl', 'params'));
    }
    
    private function resolveParameters(Request $request): array
    {
        $user = $request->user();
        $params = [];
        
        // Date range handling
        if ($request->has(['start_date', 'end_date'])) {
            $params['date_range'] = $request->start_date . '~' . $request->end_date;
        } else {
            $params['date_range'] = now()->startOfMonth()->format('Y-m-d') . '~' . now()->endOfMonth()->format('Y-m-d');
        }
        
        // User-based filtering
        if ($user->hasRole('manager')) {
            $params['department'] = $user->department_id;
        } elseif ($user->hasRole('admin')) {
            $params['show_all'] = true;
        } else {
            $params['user_id'] = $user->id;
        }
        
        // Additional filters
        foreach (['region', 'category', 'status'] as $filter) {
            if ($request->filled($filter)) {
                $params[$filter] = $request->input($filter);
            }
        }
        
        return $params;
    }
}
```

### Parameter Validation

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DashboardRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'start_date' => 'nullable|date|before_or_equal:end_date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'region' => 'nullable|string|in:north,south,east,west,all',
            'category' => 'nullable|array',
            'category.*' => 'string|max:50',
            'status' => 'nullable|in:active,inactive,pending',
        ];
    }
    
    public function getMetabaseParams(): array
    {
        $params = [];
        
        if ($this->filled(['start_date', 'end_date'])) {
            $params['date_range'] = $this->start_date . '~' . $this->end_date;
        }
        
        if ($this->filled('region') && $this->region !== 'all') {
            $params['region'] = $this->region;
        }
        
        if ($this->filled('category')) {
            $params['categories'] = implode(',', $this->category);
        }
        
        if ($this->filled('status')) {
            $params['status'] = $this->status;
        }
        
        return $params;
    }
}
```

## Caching Strategies

### URL Caching with Tags

```php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Laravolt\Metabase\MetabaseService;

class CachedMetabaseService extends MetabaseService
{
    private const CACHE_TTL = 3600; // 1 hour
    
    public function generateEmbedUrl(?int $dashboard, ?int $question): string
    {
        $cacheKey = $this->generateCacheKey($dashboard, $question);
        $tags = $this->generateCacheTags($dashboard, $question);
        
        return Cache::tags($tags)->remember($cacheKey, self::CACHE_TTL, function () use ($dashboard, $question) {
            return parent::generateEmbedUrl($dashboard, $question);
        });
    }
    
    private function generateCacheKey(?int $dashboard, ?int $question): string
    {
        $identifier = $dashboard ? "dashboard_{$dashboard}" : "question_{$question}";
        $paramsHash = md5(serialize($this->params) . serialize($this->additionalParams));
        
        return "metabase_embed_{$identifier}_{$paramsHash}";
    }
    
    private function generateCacheTags(?int $dashboard, ?int $question): array
    {
        $tags = ['metabase_embeds'];
        
        if ($dashboard) {
            $tags[] = "dashboard_{$dashboard}";
        }
        
        if ($question) {
            $tags[] = "question_{$question}";
        }
        
        return $tags;
    }
    
    public function clearCache(?int $dashboard = null, ?int $question = null): void
    {
        if ($dashboard) {
            Cache::tags(["dashboard_{$dashboard}"])->flush();
        } elseif ($question) {
            Cache::tags(["question_{$question}"])->flush();
        } else {
            Cache::tags(['metabase_embeds'])->flush();
        }
    }
}
```

### Cache Warming

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\CachedMetabaseService;

class WarmMetabaseCache extends Command
{
    protected $signature = 'metabase:warm-cache {--dashboard=*} {--question=*}';
    protected $description = 'Warm Metabase embed URL cache';
    
    public function handle(CachedMetabaseService $metabase): int
    {
        $dashboards = $this->option('dashboard') ?: config('metabase.warm_cache.dashboards', []);
        $questions = $this->option('question') ?: config('metabase.warm_cache.questions', []);
        
        foreach ($dashboards as $dashboardId) {
            $this->info("Warming cache for dashboard {$dashboardId}");
            $metabase->generateEmbedUrl((int) $dashboardId, null);
        }
        
        foreach ($questions as $questionId) {
            $this->info("Warming cache for question {$questionId}");
            $metabase->generateEmbedUrl(null, (int) $questionId);
        }
        
        $this->info('Cache warming completed');
        return 0;
    }
}
```

## Custom Views and Layouts

### Custom Component View

Create `resources/views/components/custom-metabase.blade.php`:

```blade
<div class="metabase-container" data-dashboard="{{ $dashboard }}" data-question="{{ $question }}">
    @if($title)
        <h3 class="metabase-title">{{ $title }}</h3>
    @endif
    
    <div class="metabase-controls">
        @if($showRefresh)
            <button onclick="refreshMetabase()" class="btn btn-sm btn-outline-primary">
                <i class="fas fa-refresh"></i> Refresh
            </button>
        @endif
        
        @if($showFullscreen)
            <button onclick="fullscreenMetabase()" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-expand"></i> Fullscreen
            </button>
        @endif
    </div>
    
    <div class="metabase-iframe-wrapper position-relative">
        <iframe
            src="{{ $iframeUrl }}"
            {{ $attributes->merge([
                'width' => '100%',
                'height' => '600px',
                'style' => 'border:0',
                'class' => 'metabase-iframe'
            ]) }}
            loading="lazy"
        ></iframe>
        
        @if($showLoading)
            <div class="metabase-loading position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center bg-white">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
function refreshMetabase() {
    const iframe = document.querySelector('.metabase-iframe');
    iframe.src = iframe.src;
}

function fullscreenMetabase() {
    const container = document.querySelector('.metabase-container');
    if (container.requestFullscreen) {
        container.requestFullscreen();
    }
}

// Hide loading spinner when iframe loads
document.querySelector('.metabase-iframe').addEventListener('load', function() {
    const loading = document.querySelector('.metabase-loading');
    if (loading) {
        loading.style.display = 'none';
    }
});
</script>
```

### Custom Component Class

```php
<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Laravolt\Metabase\MetabaseService;

class CustomMetabase extends Component
{
    public ?int $dashboard;
    public ?int $question;
    public array $params;
    public ?string $title;
    public bool $showRefresh;
    public bool $showFullscreen;
    public bool $showLoading;
    
    public function __construct(
        ?int $dashboard = null,
        ?int $question = null,
        array $params = [],
        ?string $title = null,
        bool $showRefresh = false,
        bool $showFullscreen = false,
        bool $showLoading = true
    ) {
        $this->dashboard = $dashboard;
        $this->question = $question;
        $this->params = $params;
        $this->title = $title;
        $this->showRefresh = $showRefresh;
        $this->showFullscreen = $showFullscreen;
        $this->showLoading = $showLoading;
    }
    
    public function render()
    {
        $metabase = app(MetabaseService::class);
        $metabase->setParams($this->params);
        $iframeUrl = $metabase->generateEmbedUrl($this->dashboard, $this->question);
        
        return view('components.custom-metabase', compact('iframeUrl'));
    }
}
```

## Security Considerations

### Parameter Sanitization

```php
<?php

namespace App\Services;

use Laravolt\Metabase\MetabaseService;

class SecureMetabaseService extends MetabaseService
{
    private array $allowedParameters = [
        'date_range',
        'user_id',
        'department_id',
        'region',
        'category',
        'status'
    ];
    
    public function setParams(array $params): void
    {
        $sanitizedParams = $this->sanitizeParameters($params);
        parent::setParams($sanitizedParams);
    }
    
    private function sanitizeParameters(array $params): array
    {
        $sanitized = [];
        
        foreach ($params as $key => $value) {
            if (!in_array($key, $this->allowedParameters)) {
                continue; // Skip disallowed parameters
            }
            
            $sanitized[$key] = $this->sanitizeValue($value);
        }
        
        return $sanitized;
    }
    
    private function sanitizeValue($value)
    {
        if (is_array($value)) {
            return array_map([$this, 'sanitizeValue'], $value);
        }
        
        if (is_string($value)) {
            // Remove potentially dangerous characters
            return preg_replace('/[<>"\']/', '', $value);
        }
        
        return $value;
    }
}
```

### Access Control

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class SecureDashboardController extends Controller
{
    public function show(Request $request, int $dashboardId)
    {
        // Check if user can access this dashboard
        Gate::authorize('view-dashboard', $dashboardId);
        
        // Additional security checks
        if (!$this->isValidDashboard($dashboardId)) {
            abort(404);
        }
        
        $params = $this->getSecureParameters($request);
        
        return view('dashboard.show', compact('dashboardId', 'params'));
    }
    
    private function isValidDashboard(int $dashboardId): bool
    {
        $allowedDashboards = config('metabase.allowed_dashboards', []);
        return in_array($dashboardId, $allowedDashboards);
    }
    
    private function getSecureParameters(Request $request): array
    {
        $user = $request->user();
        $params = [];
        
        // Always include user context for security
        $params['user_id'] = $user->id;
        
        // Row-level security based on user role
        if ($user->hasRole('employee')) {
            $params['department_id'] = $user->department_id;
            $params['user_id'] = $user->id;
        } elseif ($user->hasRole('manager')) {
            $params['department_id'] = $user->department_id;
        }
        // Admins see all data (no additional filters)
        
        return $params;
    }
}
```

## Performance Optimization

### Lazy Loading

```blade
<div class="metabase-container" data-dashboard="{{ $dashboard }}">
    <div class="metabase-placeholder" style="height: 600px; background: #f8f9fa; display: flex; align-items: center; justify-content: center;">
        <button onclick="loadMetabase(this)" class="btn btn-primary">
            Load Dashboard
        </button>
    </div>
</div>

<script>
function loadMetabase(button) {
    const container = button.closest('.metabase-container');
    const dashboard = container.dataset.dashboard;
    
    // Replace placeholder with actual iframe
    container.innerHTML = `
        <iframe src="/metabase/embed/${dashboard}" 
                width="100%" 
                height="600px" 
                style="border:0">
        </iframe>
    `;
}
</script>
```

### Progressive Enhancement

```php
<?php

namespace App\View\Components;

use Illuminate\View\Component;

class ProgressiveMetabase extends Component
{
    public function render()
    {
        return view('components.progressive-metabase');
    }
}
```

```blade
<!-- components/progressive-metabase.blade.php -->
<div class="progressive-metabase" 
     data-dashboard="{{ $dashboard }}"
     data-params="{{ json_encode($params) }}">
     
    <!-- Fallback content -->
    <noscript>
        <p>JavaScript is required to view this dashboard.</p>
        <a href="{{ $fallbackUrl }}" class="btn btn-primary">View in Metabase</a>
    </noscript>
    
    <!-- Loading state -->
    <div class="loading-state">
        <div class="skeleton-loader"></div>
    </div>
</div>

<script>
// Progressive enhancement
document.addEventListener('DOMContentLoaded', function() {
    const containers = document.querySelectorAll('.progressive-metabase');
    
    containers.forEach(container => {
        // Check if element is in viewport
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    loadMetabaseEmbed(entry.target);
                    observer.unobserve(entry.target);
                }
            });
        });
        
        observer.observe(container);
    });
});

function loadMetabaseEmbed(container) {
    const dashboard = container.dataset.dashboard;
    const params = JSON.parse(container.dataset.params || '{}');
    
    // Create iframe dynamically
    const iframe = document.createElement('iframe');
    iframe.src = generateEmbedUrl(dashboard, params);
    iframe.width = '100%';
    iframe.height = '600px';
    iframe.style.border = '0';
    
    // Replace loading state
    container.innerHTML = '';
    container.appendChild(iframe);
}
</script>
```

## Integration Patterns

### API Integration

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravolt\Metabase\MetabaseService;

class MetabaseApiController extends Controller
{
    public function generateEmbedUrl(Request $request, MetabaseService $metabase)
    {
        $request->validate([
            'dashboard' => 'required_without:question|integer',
            'question' => 'required_without:dashboard|integer',
            'params' => 'sometimes|array',
        ]);
        
        $metabase->setParams($request->input('params', []));
        
        $url = $metabase->generateEmbedUrl(
            $request->input('dashboard'),
            $request->input('question')
        );
        
        return response()->json([
            'embed_url' => $url,
            'expires_at' => now()->addHour()->toISOString(),
        ]);
    }
}
```

### Webhook Integration

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class MetabaseWebhookController extends Controller
{
    public function handleDashboardUpdate(Request $request)
    {
        $dashboardId = $request->input('dashboard_id');
        
        // Clear cache for updated dashboard
        Cache::tags(["dashboard_{$dashboardId}"])->flush();
        
        // Optionally warm cache
        $this->warmCache($dashboardId);
        
        return response()->json(['status' => 'success']);
    }
    
    private function warmCache(int $dashboardId): void
    {
        dispatch(function () use ($dashboardId) {
            $metabase = app(MetabaseService::class);
            $metabase->generateEmbedUrl($dashboardId, null);
        })->onQueue('cache-warming');
    }
}
```

This advanced usage guide provides comprehensive examples for extending and customizing the Laravolt Metabase package to meet complex requirements while maintaining security and performance.