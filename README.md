# Fortigate Enable and Disable Static Route Application

A secure, containerized PHP application for institutional management of Fortigate firewall static routes. Enable and disable internet connectivity for lab devices through an intuitive web interface.

## Overview

This application provides a centralized control interface for managing static routes on Fortigate firewalls, enabling or disabling internet connectivity for laboratory environments. Designed for institutional deployment with role-based access control and secure session management.

## Features

✅ **Secure Authentication** - Static credential-based login with forced password change on first use  
✅ **Mobile Responsive GUI** - Works seamlessly on desktop, tablet, and mobile devices  
✅ **Multi-Device Support** - Manage multiple lab devices (Trading Lab, Data Science Lab, etc.)  
✅ **Environment Configuration** - All settings stored in .env file (no database needed)  
✅ **Real-time Status** - Live internet connectivity status monitoring for each device  
✅ **Enable/Disable Control** - One-click management to enable or disable internet for each device  
✅ **Docker Support** - Production-ready containerization for institutional deployment  
✅ **Session Management** - Secure session handling with automatic logout  
✅ **API Integration** - Direct Fortigate REST API integration for static route management  

## Project Structure

```
fortigate-enable-disable-static-route/
├── index.php                 # Entry point (redirects to dashboard or login)
├── login.php                 # Authentication page with static credentials
├── dashboard.php             # Main control dashboard with device cards
├── change-password.php       # Initial password change (first login)
├── settings.php              # User settings and password management
├── logout.php                # Session termination
├── config.php                # Configuration loader for .env file
├── session.php               # Session management and authentication
├── api.php                   # Fortigate API client for device control
├── api-device-status.php     # AJAX endpoint for real-time status
├── .env.example              # Configuration template (DO NOT COMMIT .env)
├── Dockerfile                # Docker image configuration (PHP 8.2 + Apache)
├── docker-compose.yml        # Docker Compose orchestration
├── docker-entrypoint.sh      # Container startup script
└── README.md                 # This file
```

## Configuration

### 1. Create Configuration File

```bash
cp .env.example .env
```

### 2. Edit .env with Your Settings

```env
# Application Configuration
APP_NAME="Fortigate Enable and Disable Static Route Application"
APP_ENV=production

# Authentication (Set strong password in production)
ADMIN_USERNAME=admin
ADMIN_PASSWORD=your_secure_password

# Fortigate API Configuration
API_BASE_URL=https://your_firewall_ip:7799
API_BEARER_TOKEN=your_api_bearer_token
API_TIMEOUT=30
SSL_VERIFY=false  # Use true with valid certificates in production

# Device Definitions (add more as needed)
DEVICE_7=Trading Lab:Trading Lab Internet Control
DEVICE_8=Data Science Lab:Data Science Lab Internet Control
```

### 3. Adding More Devices

Add new device lines to the `.env` file in the format: `DEVICE_[ID]=[NAME]:[DESCRIPTION]`

```env
DEVICE_9=Network Lab:Network Testing Lab
DEVICE_10=Security Lab:Cybersecurity Lab
DEVICE_11=Research Lab:Research Lab Internet Control
```

## Installation

### Option 1: Docker (Recommended for Production)

1. **Clone and prepare repository:**

```bash
git clone <your-repository-url>
cd fortigate-enable-disable-static-route
cp .env.example .env
```

2. **Edit .env with institutional settings:**

```bash
# Update with your Fortigate IP, API token, and credentials
nano .env
```

3. **Build and run with Docker Compose:**

```bash
docker-compose up -d --build
```

4. **Access the application:**
   - Open your browser to: `http://localhost:8089`
   - Login with configured credentials from .env
   - Change password when prompted (required on first login)

5. **Verify deployment:**

```bash
docker-compose ps
docker-compose logs -f
```

6. **Stop the container:**

```bash
docker-compose down
```

### Option 2: Local PHP Development Server

1. **Requirements:**
   - PHP 7.4 or higher (PHP 8.2 recommended)
   - PHP cURL extension
   - Network connectivity to Fortigate API

2. **Setup:**

```bash
cp .env.example .env
nano .env  # Configure settings
mkdir sessions
chmod 755 sessions
```

3. **Run development server:**

```bash
php -S localhost:8000
```

4. **Access:**
   - Open `http://localhost:8000`
   - Login with configured credentials

### Option 3: Apache/Nginx Server

1. **Copy files to web server directory:**

```bash
cp -r . /var/www/fortigate-app/
cd /var/www/fortigate-app
cp .env.example .env
```

2. **Configure permissions:**

```bash
chmod 755 sessions
chown www-data:www-data .
chown www-data:www-data .env
```

3. **Configure web server (Apache example):**

```apache
<VirtualHost *:80>
    DocumentRoot /var/www/fortigate-app
    <Directory /var/www/fortigate-app>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

4. **Enable mod_rewrite (Apache):**

```bash
a2enmod rewrite
systemctl restart apache2
```

## Usage

### First Login
1. Navigate to the application URL
2. Enter credentials from .env: `admin` / your_password
3. Change password when prompted (required on first login)
4. Access device control dashboard

### Device Management
- **View Status**: Each device displays internet status (✓ ENABLED / ✗ DISABLED)
- **Enable Internet**: Click "Enable Internet" button for device
- **Disable Internet**: Click "Disable Internet" button for device
- **Status Updates**: Status refreshes automatically every 5 seconds

### Account Management
- **Change Password**: Use Settings page to update password anytime
- **Logout**: Click logout to end session

## Docker Configuration

### Image Details
- **Base**: PHP 8.2 with Apache
- **Extensions**: cURL, OpenSSL
- **Ports**: 8089 (HTTP)
- **Session Storage**: `/var/www/html/sessions`
- **Health Check**: Enabled with 30-second intervals

### Environment Variables
All configuration through `.env` file (see Configuration section)

### Volume Management
```bash
# View volumes
docker-compose ps

# Clean up volumes (removes all data)
docker-compose down -v
```

## Security

### For Institutional Deployment:
- ✓ Enable HTTPS with valid SSL certificates
- ✓ Use strong, complex passwords
- ✓ Implement network access controls
- ✓ Regularly rotate API bearer tokens
- ✓ Monitor application logs for suspicious activity
- ✓ Run security audits on Fortigate permissions
- ✓ Use environment-specific configurations (dev/staging/prod)

### Best Practices:
- Never commit `.env` file to version control
- Use `.env.example` as template only
- Restrict application access via firewall
- Implement application-level rate limiting
- Enable audit logging for compliance
- Use service accounts with minimal permissions
- Rotate credentials regularly
```bash
chmod -R 755 firewall_enable_disable_internet
```

## Usage

### Login Page

- Simple form with username and password
- Default credentials: `admin` / `admin123`
- Change credentials in `.env` file as needed

### Dashboard

- **Device Cards**: Each device shows in a responsive card layout
- **Status Display**: Shows current enable/disable status
- **One-Click Control**: 
  - **Enable Button**: Turn on internet for the device
  - **Disable Button**: Turn off internet for the device
- **Real-time Feedback**: Success/error messages for each action

### Mobile Responsive

The application automatically adapts to:
- 📱 Mobile phones (320px and up)
- 📱 Tablets (768px and up)
- 💻 Desktops (1200px and up)

## API Integration

The application communicates with your firewall API using:

- **Authentication**: Bearer token (configure in .env)
- **Base URL**: Your firewall management endpoint
- **Endpoints**: 
  - GET `/api/v2/cmdb/router/static/{device_id}` - Get device status
  - PUT `/api/v2/cmdb/router/static/{device_id}` - Set device status

### Example cURL commands for manual testing:

**Get Device Status:**
```bash
curl --location 'https://10.10.10.1:7799/api/v2/cmdb/router/static/7' \
  --header 'Authorization: Bearer r3kr50qtjww0n4k8Ny6wbNzs5bkghH'
```

**Enable Internet (Device 7):**
```bash
curl --location --request PUT 'https://10.10.10.1:7799/api/v2/cmdb/router/static/7' \
  --header 'Content-Type: application/json' \
  --header 'Authorization: Bearer r3kr50qtjww0n4k8Ny6wbNzs5bkghH' \
  --data '{"seq-num":"7","status":"enable"}'
```

**Disable Internet (Device 7):**
```bash
curl --location --request PUT 'https://10.10.10.1:7799/api/v2/cmdb/router/static/7' \
  --header 'Content-Type: application/json' \
  --header 'Authorization: Bearer r3kr50qtjww0n4k8Ny6wbNzs5bkghH' \
  --data '{"seq-num":"7","status":"disable"}'
```

## Security Considerations

⚠️ **Important for Production:**

1. **Change default credentials** in `.env` file
2. **Use strong passwords** for admin account
3. **Keep API Bearer token secure** - don't commit to git
4. **Enable HTTPS** for production deployment
5. **Use environment variables** instead of hardcoding secrets
6. **Implement rate limiting** to prevent abuse
7. **Add logging** for audit trails

## Troubleshooting

### Docker container won't start
- Check if port 8080 is already in use
- View logs: `docker-compose logs -f`

### Can't connect to API
- Verify API_BASE_URL in .env file
- Check Bearer token is correct
- Ensure firewall API is accessible from the container
- For self-signed certificates, SSL_VERIFY is set to false

### Sessions not working
- Ensure /var/lib/php/sessions directory exists and is writable
- Check PHP session settings

### Mobile view not responsive
- Clear browser cache (Ctrl+Shift+Delete)
- Check viewport meta tag in HTML

## File Permissions

After deployment, ensure proper permissions:

```bash
# Linux/Mac
chmod 755 firewall_enable_disable_internet
chmod 644 firewall_enable_disable_internet/*.php
chmod 644 firewall_enable_disable_internet/.env

# In Docker (automatically set)
chown -R www-data:www-data /var/www/html
```

## Environment Variables Reference

| Variable | Default | Description |
|----------|---------|-------------|
| APP_NAME | Firewall Internet Control | Application name |
| APP_ENV | production | Environment (production/development) |
| ADMIN_USERNAME | admin | Login username |
| ADMIN_PASSWORD | admin123 | Login password |
| API_BASE_URL | https://10.10.10.1:7799 | Firewall API base URL |
| API_BEARER_TOKEN | ... | Bearer token for API authentication |
| API_TIMEOUT | 30 | Request timeout in seconds |
| SSL_VERIFY | false | SSL certificate verification |
| DEVICE_* | ... | Device configurations (ID:Name:Description) |
| API_ENDPOINT | /api/v2/cmdb/router/static/ | Base API endpoint path |

## Support & Development

For issues or feature requests:
1. Check the troubleshooting section
2. Review .env configuration
3. Check Docker/PHP logs
4. Verify API connectivity

## License

MIT License - Feel free to use and modify as needed.
