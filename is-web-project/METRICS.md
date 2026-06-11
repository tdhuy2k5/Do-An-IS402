# Prometheus Metrics Implementation

This document describes how to use the Prometheus metrics system in the Laravel application.

## Overview

The application now includes built-in Prometheus metrics collection for:

- HTTP requests (total, duration, errors)
- Database queries (total, duration)
- Application information
- Database connections

## Quick Start

### 1. Install Dependencies

After pulling the latest changes, run:

```bash
composer install
composer dump-autoload
```

### 2. Access Metrics Endpoint

Once the application is running, visit:

```
http://localhost:8000/api/metrics
```

This will return metrics in Prometheus text format.

## Available Metrics

### HTTP Metrics

#### `app_http_requests_total`

Total number of HTTP requests.

**Labels:** `method`, `endpoint`, `status`

Example:

```
app_http_requests_total{method="GET",endpoint="products.index",status="200"} 150
```

#### `app_http_request_duration_seconds`

HTTP request duration in seconds (histogram with percentiles).

**Labels:** `method`, `endpoint`, `status`

**Buckets:** 1ms, 5ms, 10ms, 50ms, 100ms, 500ms, 1s, 2s, 5s

Example:

```
app_http_request_duration_seconds_bucket{method="GET",endpoint="products.index",status="200",le="0.1"} 145
app_http_request_duration_seconds_bucket{method="GET",endpoint="products.index",status="200",le="0.5"} 149
```

#### `app_http_requests_errors_total`

Total number of HTTP errors (4xx and 5xx responses).

**Labels:** `method`, `endpoint`, `status`, `error_type` (client_error, server_error)

Example:

```
app_http_requests_errors_total{method="GET",endpoint="products.show",status="404",error_type="client_error"} 5
```

#### `app_info`

Application information.

**Labels:** `app_name`, `app_version`, `environment`

Example:

```
app_info{app_name="esapp",app_version="1.0.0",environment="local"} 1
```

### Database Metrics

#### `app_database_queries_total`

Total number of database queries.

**Labels:** `connection`, `operation` (select, insert, update, delete, etc.)

Example:

```
app_database_queries_total{connection="mysql",operation="select"} 1234
```

#### `app_database_query_duration_seconds`

Database query duration in seconds (histogram).

**Labels:** `connection`, `operation`

**Buckets:** 1ms, 5ms, 10ms, 50ms, 100ms, 500ms, 1s

Example:

```
app_database_query_duration_seconds_bucket{connection="mysql",operation="select",le="0.01"} 1200
```

#### `app_database_active_connections`

Number of active database connections.

**Labels:** `connection`

Example:

```
app_database_active_connections{connection="mysql"} 5
```

## Recording Custom Metrics

### Using the Metrics Facade

```php
use App\Facades\Metrics;

// Record an HTTP request (normally automatic via middleware)
Metrics::recordHttpRequest('GET', 'products.index', 200, 0.125);

// Record a database query
Metrics::recordDatabaseQuery('mysql', 'select', 0.045);

// Set active connections
Metrics::setActiveConnections('mysql', 3);
```

### Using Helper Functions

```php
// Record database query
record_db_query('mysql', 'insert', 0.032);

// Record HTTP request
record_http_request('POST', 'orders.store', 201, 0.267);

// Get metrics service
$metrics = metrics();
```

### Injecting MetricsService

```php
namespace App\Http\Controllers;

use App\Services\MetricsService;

class MyController extends Controller
{
    public function __construct(
        private MetricsService $metrics
    ) {}

    public function store()
    {
        $startTime = microtime(true);

        // Your business logic

        $duration = microtime(true) - $startTime;
        $this->metrics->recordHttpRequest('POST', 'my.endpoint', 201, $duration);
    }
}
```

## Example: Custom Database Query Monitoring

To monitor custom database operations:

```php
use Illuminate\Support\Facades\DB;
use App\Facades\Metrics;

class ProductRepository
{
    public function searchProducts($query)
    {
        $startTime = microtime(true);

        $results = DB::table('products')
            ->where('name', 'like', "%{$query}%")
            ->get();

        $duration = microtime(true) - $startTime;
        Metrics::recordDatabaseQuery('mysql', 'select', $duration);

        return $results;
    }
}
```

## Prometheus Configuration

### Scrape Configuration

Add the following to your `prometheus.yml`:

```yaml
scrape_configs:
    - job_name: "laravel-app"
      static_configs:
          - targets: ["localhost:8000"]
      metrics_path: "/api/metrics"
      scrape_interval: 15s
      scrape_timeout: 10s
```

### Docker Compose Example

```yaml
prometheus:
    image: prom/prometheus:latest
    ports:
        - "9090:9090"
    volumes:
        - ./prometheus.yml:/etc/prometheus/prometheus.yml
        - ./prometheus-data:/prometheus
    command:
        - "--config.file=/etc/prometheus/prometheus.yml"
        - "--storage.tsdb.path=/prometheus"
    networks:
        - ecommerce-network
```

## Grafana Setup

### 1. Add Prometheus Data Source

- URL: `http://prometheus:9090`
- Access: Browser

### 2. Create Dashboards

Use these PromQL queries:

#### Request Rate (requests per second)

```promql
rate(app_http_requests_total[5m])
```

#### Request Duration (p95)

```promql
histogram_quantile(0.95, rate(app_http_request_duration_seconds_bucket[5m]))
```

#### Error Rate

```promql
rate(app_http_requests_errors_total[5m])
```

#### Database Query Rate

```promql
rate(app_database_queries_total[5m])
```

#### Database Query Duration (p99)

```promql
histogram_quantile(0.99, rate(app_database_query_duration_seconds_bucket[5m]))
```

## Middleware Configuration

HTTP request metrics are automatically recorded by the `RecordMetrics` middleware, which is registered in `bootstrap/app.php`.

The middleware:

- Automatically skips the `/metrics` endpoint
- Records request method, route name/path, status code, and duration
- Handles all HTTP responses

## Performance Considerations

- Metrics are stored in-memory and reset on application restart
- For persistent metrics storage, configure Prometheus to scrape and store the metrics
- The metrics endpoint is public and does not require authentication (configurable)
- Consider rate-limiting the metrics endpoint in production

## Best Practices

1. **Use Route Names**: The middleware records the route name if available, which provides better grouping:

    ```php
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    ```

2. **Custom Operation Labels**: When recording database queries, use descriptive operation names:

    ```php
    // Good
    record_db_query('mysql', 'search_products', $duration);

    // Avoid
    record_db_query('mysql', 'query', $duration);
    ```

3. **Monitor Critical Paths**: Focus on recording metrics for critical business operations:

    ```php
    // In your OrderController
    public function store(CreateOrderRequest $request)
    {
        $startTime = microtime(true);
        $order = $this->orderService->create($request->validated());
        $duration = microtime(true) - $startTime;

        Metrics::recordDatabaseQuery('mysql', 'create_order', $duration);

        return response()->json($order, 201);
    }
    ```

4. **Persistent Storage**: In production, use Prometheus with persistent storage (not in-memory):
    ```yaml
    prometheus:
        volumes:
            - prometheus-data:/prometheus # Use named volumes for persistence
    ```

## Troubleshooting

### Metrics endpoint returns empty

- Ensure the application has received at least one request since startup
- Check that middleware is properly registered in `bootstrap/app.php`
- Verify Prometheus client library is installed: `composer show promphp/prometheus_client_php`

### Metrics not updating

- Check that requests are being made to the application
- Verify middleware is not being bypassed
- Check application logs for errors

### High memory usage

- In-memory metrics grow with application uptime
- Consider implementing periodic flushing to Prometheus
- Monitor metrics endpoint response size in Prometheus config

## Additional Resources

- [Prometheus Client PHP Documentation](https://github.com/Prometheus/client_php)
- [Prometheus Best Practices](https://prometheus.io/docs/practices/naming/)
- [Grafana Documentation](https://grafana.com/docs/grafana/latest/)
