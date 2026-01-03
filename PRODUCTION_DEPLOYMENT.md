# Production Deployment Guide

> **Note:** For AWS-specific deployment with S3, SES, SQS, and other AWS services, see the comprehensive **[AWS Configuration Guide](AWS_CONFIGURATION_GUIDE.md)**.

## System Requirements

- **PHP**: ^8.1
- **Composer**: Latest stable version
- **Node.js**: 18.x or higher
- **NPM**: 9.x or higher
- **Database**: MySQL 8.0+ or PostgreSQL 13+
- **Web Server**: Nginx or Apache with mod_rewrite

## PHP Dependencies (Production)

Install production dependencies using Composer:

```bash
composer install --optimize-autoloader --no-dev
```

### Required PHP Packages:
- laravel/framework: ^10.10
- laravel/sanctum: ^3.3
- guzzlehttp/guzzle: ^7.2
- barryvdh/laravel-dompdf: ^3.1
- dyrynda/laravel-efficient-uuid: ^5.0
- dyrynda/laravel-model-uuid: ^7.1
- maatwebsite/excel: ^3.1
- spatie/laravel-permission: ^6.23
- laravel/tinker: ^2.8
- **aws/aws-sdk-php**: ^3.369 (for AWS services)
- **league/flysystem-aws-s3-v3**: ^3.30 (for S3 storage)

### Development-Only Packages (DO NOT install in production):
- barryvdh/laravel-debugbar
- barryvdh/laravel-ide-helper
- fakerphp/faker
- laravel/breeze
- laravel/pint
- laravel/sail
- mockery/mockery
- nunomaduro/collision
- phpunit/phpunit
- spatie/laravel-ignition

## JavaScript/Node Dependencies

Build frontend assets for production:

```bash
npm install
npm run build
```

### Frontend Packages:
- @tailwindcss/forms: ^0.5.2
- alpinejs: ^3.4.2
- autoprefixer: ^10.4.2
- axios: ^1.6.4
- laravel-vite-plugin: ^1.0.0
- postcss: ^8.4.31
- tailwindcss: ^3.1.0
- vite: ^5.0.0

## Production Deployment Steps

### 1. Clone Repository
```bash
git clone <repository-url>
cd realman
```

### 2. Install Dependencies
```bash
# Install PHP dependencies (production only)
composer install --optimize-autoloader --no-dev

# Install Node dependencies and build assets
npm install
npm run build
```

### 3. Environment Configuration
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure your database and other settings in .env
```

### 4. Database Setup
```bash
# Run migrations
php artisan migrate --force

# Seed database (if needed)
php artisan db:seed --force
```

### 5. Optimize Application
```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize autoloader
composer dump-autoload --optimize
```

### 6. Set Permissions
```bash
# Storage and cache directories need write permissions
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### 7. Queue and Scheduler (if applicable)
```bash
# Set up supervisor for queue workers
# Set up cron job for scheduler:
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

## Security Checklist

- [ ] Set `APP_ENV=production` in `.env`
- [ ] Set `APP_DEBUG=false` in `.env`
- [ ] Use strong `APP_KEY`
- [ ] Configure proper database credentials
- [ ] Set up HTTPS/SSL certificate
- [ ] Configure CORS properly (set `CORS_ALLOWED_ORIGINS` to your frontend domain(s))
- [ ] Configure Sanctum stateful domains (set `SANCTUM_STATEFUL_DOMAINS` if using SPA)
- [ ] Set secure session and cookie settings
- [ ] Enable CSRF protection
- [ ] Configure trusted proxies if behind load balancer
- [ ] Set up proper file permissions
- [ ] Configure rate limiting
- [ ] Enable authentication and authorization

## Environment Variables (Production)

```env
APP_NAME="RealMan"
APP_ENV=production
APP_KEY=base64:your-generated-key-here
APP_DEBUG=false
APP_URL=https://yourdomain.com

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=realman_prod
DB_USERNAME=your_db_user
DB_PASSWORD=your_secure_password

BROADCAST_DRIVER=log
CACHE_DRIVER=redis
FILESYSTEM_DISK=local
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
SESSION_LIFETIME=120

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=your-mail-host
MAIL_PORT=587
MAIL_USERNAME=your-mail-username
MAIL_PASSWORD=your-mail-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"

# CORS Settings (comma-separated list of allowed origins)
CORS_ALLOWED_ORIGINS=https://yourdomain.com,https://www.yourdomain.com
SANCTUM_STATEFUL_DOMAINS=yourdomain.com,www.yourdomain.com

# SMS/OTP Settings
SMS_DRIVER=frog
OTP_EXPIRY_MINUTES=10
SMS_PROVIDER=frog
FROGSMS_BASE_URL=https://frog.wigal.com.gh/ismsweb/sendmsg
FROGSMS_PASSWORD=your-frogsms-password
FROGSMS_USERNAME=your-frogsms-username
FROGSMS_SENDER_ID=your-sender-id
```

## Performance Optimization

1. **Enable OPcache** in php.ini
2. **Use Redis** for cache and sessions
3. **Enable HTTP/2** on web server
4. **Implement CDN** for static assets
5. **Enable Gzip compression**
6. **Configure database query caching**
7. **Use queue workers** for heavy tasks

## Monitoring & Maintenance

- Set up error logging and monitoring
- Configure log rotation
- Set up database backups
- Monitor disk space
- Monitor application performance
- Keep Laravel and dependencies updated

## Update Production

```bash
# Pull latest changes
git pull origin main

# Update dependencies
composer install --optimize-autoloader --no-dev
npm install
npm run build

# Run migrations
php artisan migrate --force

# Clear and rebuild caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Restart queue workers (if applicable)
php artisan queue:restart
```

---

## AWS Serverless Deployment (Cost-Optimized)

This section provides recommendations for deploying the RealMan application to AWS using serverless and free-tier services to minimize costs.

### Recommended Architecture

**Option A: Serverless (Lambda-based) - Most Cost-Effective for Variable Traffic**

Best for applications with unpredictable or low traffic. Pay only for what you use.

**Option B: Hybrid (Lightsail + Serverless Components) - Simplest Setup**

Best for predictable traffic and easier Laravel deployment. Fixed monthly cost starting at $3.50/month.

### AWS Services Breakdown

#### 1. **Compute Layer**

**Option A: AWS Lambda with Bref** (Serverless)
- **Service**: AWS Lambda + API Gateway + Bref PHP runtime
- **Free Tier**: 1M requests/month + 400,000 GB-seconds compute time
- **Cost**: ~$0 for low traffic, scales automatically
- **Setup**: Use [Bref](https://bref.sh/) framework for Laravel on Lambda
- **Pros**: True serverless, auto-scaling, pay-per-request
- **Cons**: Cold starts, complex setup, 15-minute max execution time

**Option B: AWS Lightsail** (Recommended for Simplicity)
- **Service**: Lightsail VPS (Virtual Private Server)
- **Cost**: $3.50-$5/month for 512MB-1GB RAM
- **Free Tier**: First month free (sometimes)
- **Pros**: Simple setup, predictable pricing, full Laravel compatibility
- **Cons**: Fixed capacity, need to manage server

#### 2. **Database**

**Recommended: Amazon RDS MySQL** (Free Tier Available)
- **Free Tier**: 750 hours/month of db.t3.micro or db.t4g.micro (20GB storage)
- **Cost After Free Tier**: ~$15-30/month
- **Features**: Automated backups, automated patching, easy scaling
- **Configuration**: 
  - Instance: db.t3.micro (1 vCPU, 1GB RAM)
  - Storage: 20GB GP3 SSD
  - Multi-AZ: Disable for cost savings (enable for production HA)

**Alternative: Aurora Serverless v2**
- **Cost**: ~$0.12/hour when active (scales to zero when idle)
- **Min**: $43/month (0.5 ACU minimum)
- **Pros**: Auto-scaling, better performance
- **Cons**: More expensive than RDS MySQL free tier

**Budget Option: Lightsail Managed Database**
- **Cost**: $15/month (1GB RAM, 40GB SSD)
- **Pros**: Predictable pricing, simple setup
- **Cons**: Less features than RDS

#### 3. **File Storage**

**Amazon S3** (Free Tier)
- **Free Tier**: 5GB storage +, 20,000 GET requests, 2,000 PUT requests/month
- **Cost After**: ~$0.023/GB/month + requests
- **Usage**: Store uploads, PDFs, exports, backups
- **Configuration**:
  - Create bucket: `realman-production-storage`
  - Enable versioning for critical files
  - Configure lifecycle policies to archive old files to S3 Glacier

**Laravel Integration**:
```bash
composer require league/flysystem-aws-s3-v3
```

Update `.env`:
```env
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=your-access-key
AWS_SECRET_ACCESS_KEY=your-secret-key
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=realman-production-storage
AWS_USE_PATH_STYLE_ENDPOINT=false
```

#### 4. **Content Delivery Network (CDN)**

**Amazon CloudFront** (Free Tier)
- **Free Tier**: 50GB data transfer out, 2M HTTP/HTTPS requests/month
- **Cost After**: ~$0.085/GB
- **Usage**: Serve static assets (CSS, JS, images) globally
- **Benefits**: Faster load times, reduced server load, HTTPS included
- **Setup**: Point to S3 bucket or application domain

#### 5. **Email Service**

**Amazon SES (Simple Email Service)** (Free Tier)
- **Free Tier**: 62,000 emails/month when sending from EC2
- **Cost After**: $0.10 per 1,000 emails
- **Usage**: Transactional emails, notifications, reports
- **Regions**: Use us-east-1, us-west-2, or eu-west-1 for best rates

**Configuration** in `.env`:
```env
MAIL_MAILER=ses
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="RealMan"
AWS_ACCESS_KEY_ID=your-ses-access-key
AWS_SECRET_ACCESS_KEY=your-ses-secret-key
AWS_DEFAULT_REGION=us-east-1
```

**Alternative: Keep FrogSMS for critical notifications**

#### 6. **Caching Layer**

**Option A: Amazon ElastiCache (Redis)**
- **Cost**: ~$13/month for cache.t3.micro
- **No Free Tier** for ElastiCache
- **Pros**: Managed Redis, high performance
- **Usage**: Session storage, application cache, queue backend

**Option B: DynamoDB** (Free Tier)
- **Free Tier**: 25GB storage, 25 read/write capacity units
- **Cost**: Free for low usage
- **Pros**: Serverless, auto-scaling
- **Usage**: Cache storage, session storage

**Budget Option: File/Database Cache**
- **Cost**: Free
- **Pros**: No additional service needed
- **Cons**: Slower performance
- **Use for**: Development or very low traffic

#### 7. **Queue Service**

**Amazon SQS** (Free Tier)
- **Free Tier**: 1M requests/month
- **Cost After**: $0.40 per 1M requests
- **Usage**: Background jobs, processing requests, email queue
- **Configuration**:

```bash
composer require aws/aws-sdk-php
```

Update `.env`:
```env
QUEUE_CONNECTION=sqs
SQS_KEY=your-access-key
SQS_SECRET=your-secret-key
SQS_PREFIX=https://sqs.us-east-1.amazonaws.com/your-account-id
SQS_QUEUE=realman-queue
SQS_REGION=us-east-1
```

#### 8. **Domain & DNS**

**Amazon Route 53**
- **Cost**: $0.50/month per hosted zone + $0.40 per 1M queries
- **Features**: Health checks, traffic routing, domain registration

**Budget Alternative: Cloudflare DNS**
- **Cost**: Free for DNS
- **Pros**: Free SSL, CDN, DDoS protection
- **Cons**: Not integrated with AWS

#### 9. **SSL Certificates**

**AWS Certificate Manager (ACM)** (Free)
- **Cost**: FREE for public SSL certificates
- **Features**: Auto-renewal, wildcard certificates
- **Usage**: Use with CloudFront, ALB, API Gateway

#### 10. **Monitoring & Logging**

**Amazon CloudWatch** (Free Tier)
- **Free Tier**: 5GB logs, 10 custom metrics, 3 dashboards
- **Cost After**: ~$0.50/GB for logs
- **Usage**: Application logs, error tracking, performance metrics

**Budget Alternative: CloudWatch Logs + External Monitoring**
- Use CloudWatch for basic logs
- Add free tier of Sentry.io or LogRocket for error tracking

#### 11. **Backup & Disaster Recovery**

**AWS Backup** (Pay per use)
- **Cost**: ~$0.05/GB/month for backups
- **Features**: Automated RDS snapshots, S3 versioning
- **Configuration**:
  - Daily RDS snapshots (retain 7 days)
  - Weekly full backups (retain 4 weeks)
  - S3 versioning for critical files

### Recommended Deployment Architecture

#### **Architecture A: Full Serverless (Lambda + API Gateway)**

```
User Request
    ↓
Route 53 (DNS)
    ↓
CloudFront (CDN) ← S3 (Static Assets)
    ↓
API Gateway
    ↓
Lambda (Bref PHP) ← RDS MySQL
    ↓                  ↓
SQS Queue          S3 Storage
    ↓
Lambda Workers
```

**Estimated Monthly Cost**: $15-40
- RDS MySQL (db.t3.micro): $15-25/month
- Lambda: Free tier covers most traffic
- S3: $1-5/month
- SQS: Free tier sufficient
- CloudFront: Free tier for low traffic
- SES: Free tier (62k emails)

**Best For**: Variable traffic, low to medium usage, cost optimization

#### **Architecture B: Hybrid (Lightsail + Managed Services)**

```
User Request
    ↓
Route 53 (DNS)
    ↓
CloudFront (CDN) ← S3 (Static Assets)
    ↓
Application Load Balancer (Optional)
    ↓
Lightsail Instance ← RDS MySQL or Lightsail DB
    ↓                    ↓
SQS Queue           S3 Storage
```

**Estimated Monthly Cost**: $25-50
- Lightsail VPS (1GB): $5/month
- Lightsail Database (1GB): $15/month OR RDS free tier
- S3: $1-5/month
- CloudFront: Free tier for low traffic
- SES: Free tier
- SQS: Free tier

**Best For**: Predictable traffic, simpler setup, full Laravel compatibility

### Cost Optimization Strategies

#### 1. **Use Free Tier Maximally**
- RDS: Stay within 750 hours/month (run 24/7 on single instance)
- S3: Keep under 5GB, use lifecycle policies
- CloudFront: Monitor data transfer
- SES: Stay under 62,000 emails/month
- Lambda: Optimize function duration

#### 2. **Reserved Instances** (After Free Tier)
- RDS Reserved Instances: Save up to 60%
- Purchase 1-year commitments for 30-40% savings

#### 3. **Auto-Scaling & Scheduling**
- Scale down RDS instance during off-hours (if possible)
- Use Aurora Serverless v2 for variable workloads
- Schedule Lambda functions instead of continuous polling

#### 4. **Data Transfer Optimization**
- Use CloudFront to reduce origin requests
- Enable compression for API responses
- Store large files in S3 Glacier for archival

#### 5. **Database Optimization**
- Use read replicas only when necessary
- Disable Multi-AZ for development/staging
- Regular database cleanup (old logs, sessions)

#### 6. **Monitoring Costs**
- Set up AWS Budget alerts (free)
- Monitor cost anomalies with Cost Explorer
- Tag resources for cost tracking

### Step-by-Step: AWS Lightsail Deployment (Recommended for Beginners)

#### 1. **Create Lightsail Instance**
```bash
# From AWS Console:
# - Choose Lightsail
# - Create Instance
# - Select OS: Ubuntu 22.04 LTS
# - Select Plan: $5/month (1GB RAM)
# - Create Instance
```

#### 2. **Configure Server**
```bash
# SSH into instance
ssh ubuntu@your-instance-ip

# Update system
sudo apt update && sudo apt upgrade -y

# Install PHP 8.1 and extensions
sudo apt install -y php8.1-fpm php8.1-mysql php8.1-mbstring \
    php8.1-xml php8.1-bcmath php8.1-curl php8.1-zip \
    php8.1-gd php8.1-intl php8.1-redis

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Nginx
sudo apt install -y nginx

# Install Node.js
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs

# Install Redis (for caching)
sudo apt install -y redis-server
```

#### 3. **Create RDS Database**
```bash
# From AWS Console:
# - Go to RDS
# - Create Database
# - Engine: MySQL 8.0
# - Template: Free tier
# - Instance: db.t3.micro
# - Storage: 20GB GP3
# - Set master username and password
# - VPC: Same as Lightsail (or enable public access), attention here... o
# - Create database
```

#### 4. **Create S3 Bucket**
```bash
# From AWS Console or AWS CLI:
aws s3 mb s3://realman-production-storage
aws s3api put-bucket-versioning \
    --bucket realman-production-storage \
    --versioning-configuration Status=Enabled
```

#### 5. **Deploy Application**
```bash
# Clone repository
cd /var/www
sudo git clone https://github.com/your-repo/realman.git
cd realman

# Install dependencies
composer install --optimize-autoloader --no-dev
npm install && npm run build

# Set permissions
sudo chown -R www-data:www-data /var/www/realman
sudo chmod -R 775 storage bootstrap/cache

# Configure environment
cp .env.example .env
nano .env
# Update: APP_ENV=production, APP_DEBUG=false, database, S3, etc.

php artisan key:generate
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

#### 6. **Configure Nginx**
```nginx
# /etc/nginx/sites-available/realman
server {
    listen 80;
    server_name yourdomain.com;
    root /var/www/realman/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

```bash
# Enable site
sudo ln -s /etc/nginx/sites-available/realman /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

#### 7. **Setup SSL with Let's Encrypt** (Free)
```bash
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com
```

#### 8. **Configure Supervisor for Queues**
```bash
sudo apt install -y supervisor

# Create config: /etc/supervisor/conf.d/realman-worker.conf
[program:realman-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/realman/artisan queue:work sqs --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/realman/storage/logs/worker.log
stopwaitsecs=3600

sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start realman-worker:*
```

#### 9. **Setup Cron for Scheduler**
```bash
sudo crontab -e -u www-data
# Add:
* * * * * cd /var/www/realman && php artisan schedule:run >> /dev/null 2>&1
```

### Monthly Cost Breakdown (Estimated)

#### **Minimum Setup (Free Tier Optimized)**
| Service | Configuration | Monthly Cost |
|---------|--------------|--------------|
| Lightsail VPS | 1GB RAM, 40GB SSD | $5.00 |
| RDS MySQL | db.t3.micro (Free Tier) | $0.00 |
| S3 Storage | <5GB (Free Tier) | $0.00 |
| CloudFront | <50GB transfer (Free Tier) | $0.00 |
| SES Email | <62k emails (Free Tier) | $0.00 |
| Route 53 | Hosted Zone | $0.50 |
| **Total** | | **$5.50/month** |

#### **Production Setup (Post Free Tier)**
| Service | Configuration | Monthly Cost |
|---------|--------------|--------------|
| Lightsail VPS | 2GB RAM, 60GB SSD | $10.00 |
| RDS MySQL | db.t3.small (2GB RAM) | $30.00 |
| ElastiCache Redis | cache.t3.micro | $13.00 |
| S3 Storage | 20GB + requests | $2.00 |
| CloudFront | 100GB transfer | $8.50 |
| SES Email | 100k emails | $10.00 |
| Route 53 | Hosted Zone + queries | $1.00 |
| Backups | RDS snapshots, S3 | $5.00 |
| **Total** | | **$79.50/month** |

### Security Best Practices on AWS

1. **IAM Users & Policies**
   - Create separate IAM users for different services
   - Use least-privilege access policies
   - Enable MFA for console access
   - Rotate access keys regularly

2. **Security Groups**
   - Restrict RDS to only Lightsail instance IP
   - Only open ports 80, 443 on web server
   - Use VPC for internal communication

3. **Encryption**
   - Enable RDS encryption at rest
   - Use S3 bucket encryption (SSE-S3 or SSE-KMS)
   - Force HTTPS with CloudFront

4. **Monitoring & Alerts**
   - Enable CloudWatch alarms for CPU, memory, disk
   - Set up SNS notifications for critical alerts
   - Monitor AWS CloudTrail for security events

5. **Backup Strategy**
   - Daily RDS automated backups (retain 7 days)
   - Weekly manual snapshots (retain 30 days)
   - S3 versioning for critical files
   - Cross-region backup for disaster recovery

### Useful AWS CLI Commands

```bash
# Install AWS CLI
curl "https://awscli.amazonaws.com/awscli-exe-linux-x86_64.zip" -o "awscliv2.zip"
unzip awscliv2.zip
sudo ./aws/install

# Configure
aws configure

# S3 operations
aws s3 cp file.pdf s3://realman-production-storage/uploads/
aws s3 sync storage/app/public s3://realman-production-storage/public

# SQS operations
aws sqs send-message --queue-url https://sqs.us-east-1.amazonaws.com/xxx/realman-queue --message-body "test"
aws sqs receive-message --queue-url https://sqs.us-east-1.amazonaws.com/xxx/realman-queue

# RDS operations
aws rds describe-db-instances
aws rds create-db-snapshot --db-instance-identifier realman-db --db-snapshot-identifier manual-backup-$(date +%Y%m%d)

# CloudWatch logs
aws logs tail /aws/lambda/realman-function --follow
```

### Additional Resources

- **Bref for Laravel on Lambda**: https://bref.sh/docs/frameworks/laravel.html
- **AWS Free Tier**: https://aws.amazon.com/free/
- **Laravel on AWS**: https://aws.amazon.com/getting-started/hands-on/deploy-php-application/
- **AWS Cost Calculator**: https://calculator.aws/
- **Laravel Vapor** (Managed Laravel Serverless): https://vapor.laravel.com/ ($$)

### Support & Troubleshooting

Common issues and solutions:

1. **502 Bad Gateway**: Check PHP-FPM is running, verify Nginx config
2. **Storage permissions**: `sudo chown -R www-data:www-data storage bootstrap/cache`
3. **Database connection**: Check RDS security group allows Lightsail IP
4. **Queue not processing**: Check supervisor status, SQS credentials
5. **High costs**: Review CloudWatch metrics, enable cost alerts

---

*Last Updated: December 27, 2025*

