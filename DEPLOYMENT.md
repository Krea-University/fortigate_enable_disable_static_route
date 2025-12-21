# Build and deploy instructions

## Quick Start with Docker

### Prerequisites
- Docker Desktop installed
- Docker Compose available

### Build and Run

```bash
# Navigate to project directory
cd firewall_enable_disable_internet

# Build the Docker image
docker-compose build

# Start the container
docker-compose up -d

# Check if it's running
docker-compose ps

# View logs
docker-compose logs -f
```

### Access Application
- Open browser: http://localhost:8080
- Login: admin / admin123

### Stop Container
```bash
docker-compose down
```

## Local Development Setup

### Prerequisites
- PHP 7.4 or higher
- PHP with cURL support enabled

### Steps

1. Navigate to project:
```bash
cd firewall_enable_disable_internet
```

2. Start PHP built-in server:
```bash
php -S localhost:8000
```

3. Open browser:
- http://localhost:8000
- Login: admin / admin123

## Production Deployment

### Important Security Steps

1. **Update .env file:**
   - Change ADMIN_PASSWORD to a strong password
   - Update API_BEARER_TOKEN to your actual token
   - Set APP_ENV=production
   - Set SSL_VERIFY=true if using trusted certificates

2. **Deploy with Docker:**
```bash
docker-compose up -d --build
```

3. **Use Nginx/Apache reverse proxy** with SSL/TLS

4. **Backup .env file** - it contains sensitive information

## Scaling Multiple Devices

Add devices to .env:
```env
DEVICE_7=Trading Lab:Trading Lab Internet Control
DEVICE_8=Data Science Lab:Data Science Lab Internet Control
DEVICE_9=Research Lab:Research Lab Internet Control
DEVICE_10=Backup Lab:Backup Lab Internet Control
```

Each device automatically appears as a new card on the dashboard.

## Environment Configuration

All configuration is in `.env` file - no database needed:
- Static credentials
- API endpoints
- Device definitions
- API authentication

## Performance Notes

- Lightweight PHP application
- No database overhead
- Can handle many devices simultaneously
- Direct API calls to firewall
- Session-based authentication

## Maintenance

### Logs
```bash
# Docker logs
docker-compose logs firewall-control

# Access logs (in container)
docker exec firewall-internet-control cat /var/log/apache2/access.log
```

### Updates
1. Update .env configuration
2. Rebuild container: `docker-compose up -d --build`
3. Changes apply immediately
