# Prometheus & Grafana Setup Guide

This guide helps you set up Prometheus and Grafana to monitor your Laravel e-commerce application.

## Prerequisites

- Docker and Docker Compose installed
- The Laravel backend running and exporting metrics to `/api/metrics`

## Quick Start

### 1. Install Dependencies

```bash
cd is-web-project
composer install
composer dump-autoload
```

### 2. Start All Services

From the project root:

```bash
docker-compose up -d
```

This will start:

- **Backend**: http://localhost:8000
- **Frontend**: http://localhost:5173
- **Prometheus**: http://localhost:9090
- **Grafana**: http://localhost:3000

### 3. Verify Metrics Collection

Visit: http://localhost:9090/targets

You should see the Laravel backend target with status "UP".

### 4. Access Grafana

1. Open http://localhost:3000
2. Login with default credentials:
   - Username: `admin`
   - Password: `admin`
3. Change the default password (recommended)
4. The Prometheus datasource should already be configured
5. Import the Laravel Metrics dashboard

## Accessing Metrics

### Raw Prometheus Format

Visit your backend metrics endpoint:

```
http://localhost:8000/api/metrics
```

### Prometheus Web UI

Access the Prometheus queries interface:

```
http://localhost:9090/graph
```

Example queries:

- `rate(app_http_requests_total[5m])` - Requests per second
- `app_http_requests_errors_total` - Total errors
- `histogram_quantile(0.95, app_http_request_duration_seconds_bucket)` - 95th percentile latency

### Grafana Dashboards

Visit http://localhost:3000 and check the "Dashboards" section.

Pre-configured dashboard: **Laravel E-Commerce Metrics**

## Key Metrics to Monitor

### Application Health

- **Request Rate**: `rate(app_http_requests_total[5m])`
- **Error Rate**: `rate(app_http_requests_errors_total[5m])`
- **Response Time (p95)**: `histogram_quantile(0.95, rate(app_http_request_duration_seconds_bucket[5m]))`

### Database Performance

- **Query Rate**: `rate(app_database_queries_total[5m])`
- **Slow Queries (p99)**: `histogram_quantile(0.99, rate(app_database_query_duration_seconds_bucket[5m]))`
- **Active Connections**: `app_database_active_connections`

### Business Metrics (Custom)

Track your specific endpoints and operations by checking the Grafana dashboards.

## Recording Custom Metrics

To add custom metrics in your controllers:

```php
use App\Facades\Metrics;

// In your controller method
$startTime = microtime(true);
// Your business logic here
$duration = microtime(true) - $startTime;

Metrics::recordDatabaseQuery('mysql', 'custom_operation', $duration);
```

See [METRICS.md](../is-web-project/METRICS.md) for detailed documentation.

## Stopping Services

```bash
docker-compose down
```

To stop and remove volumes:

```bash
docker-compose down -v
```

## Troubleshooting

### Prometheus shows no targets

- Check that the backend container is running: `docker-compose ps`
- Verify the metrics endpoint works: `curl http://localhost:8000/api/metrics`
- Check Prometheus logs: `docker-compose logs prometheus`

### Grafana datasource connection failed

- Ensure Prometheus is running and healthy: `docker-compose logs prometheus`
- Verify the Prometheus URL in Grafana: http://prometheus:9090

### No data in Grafana dashboards

- Wait a few minutes for Prometheus to scrape the metrics
- Generate some traffic to the API: `curl http://localhost:8000/api/health`
- Check Prometheus: http://localhost:9090/targets (all targets should be UP)

### Metrics endpoint returns empty response

- Restart the backend container: `docker-compose restart backend`
- Ensure `composer dump-autoload` was run
- Check the Laravel logs for errors

## Performance Considerations

- **Scrape Interval**: Set to 15s by default (in `prometheus.yml`)
- **Data Retention**: Set to 30 days by default (in `docker-compose.yml`)
- **Memory Usage**: Prometheus stores metrics in memory, adjust retention as needed
- **CPU Usage**: Increase query optimization if CPU usage is high

## Advanced Configuration

### Adjust Prometheus Retention

Edit `docker-compose.yml`:

```yaml
command:
  - "--storage.tsdb.retention.time=7d" # Change from 30d to desired duration
```

### Add Alerts

Edit `prometheus.yml` to add alert rules:

```yaml
rule_files:
  - "/etc/prometheus/alerts.yml"
```

### Customize Scrape Interval

Edit `prometheus.yml`:

```yaml
global:
  scrape_interval: 30s # Increase from 15s
```

## Accessing Historical Data

Prometheus stores data in `/prometheus/` volume. To access raw data files:

```bash
docker-compose exec prometheus ls -la /prometheus/
```

## Documentation References

- [Prometheus Documentation](https://prometheus.io/docs/)
- [Grafana Documentation](https://grafana.com/docs/grafana/latest/)
- [Laravel Metrics Implementation](../is-web-project/METRICS.md)

## Next Steps

1. ✅ Services are running
2. ✅ Metrics are being collected
3. 📊 Create custom dashboards in Grafana
4. 🚨 Set up alerts for important metrics
5. 📈 Monitor business metrics
