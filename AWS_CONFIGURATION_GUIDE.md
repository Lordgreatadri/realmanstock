# AWS Services Configuration Guide

Complete setup guide for integrating AWS services with the RealMan Laravel application.

## Prerequisites

✅ Already installed packages:
```bash
composer require league/flysystem-aws-s3-v3  # S3 file storage
composer require aws/aws-sdk-php            # AWS SDK for SES, SQS, etc.
```

## Additional Packages (Optional)

### For Redis Cache (ElastiCache)
```bash
# Option 1: Predis (Pure PHP - Recommended for Laravel)
composer require predis/predis

# Option 2: PhpRedis (PHP Extension - Faster)
# Install via system package manager
sudo pecl install redis
# Enable in php.ini: extension=redis.so
```

### For Serverless Deployment (Lambda + Bref)
Only if deploying to AWS Lambda instead of Lightsail:
```bash
composer require bref/bref --update-with-dependencies
composer require bref/laravel-bridge --update-with-dependencies
npm install -g serverless
```

### For CloudWatch Logging (Optional)
```bash
composer require maxbanton/cwh  # CloudWatch Handler for Monolog
```

---

## AWS Services Configuration

### 1. S3 File Storage Configuration

#### Step 1: Create S3 Bucket

**Via AWS Console:**
1. Go to S3 → Create bucket
2. Bucket name: `realman-production-storage` (must be globally unique)
3. Region: `us-east-1` (or your preferred region)
4. Block all public access: Uncheck if you need public file access
5. Enable versioning: Yes (recommended)
6. Create bucket

**Via AWS CLI:**
```bash
# Create bucket
aws s3 mb s3://realman-production-storage --region us-east-1

# Enable versioning
aws s3api put-bucket-versioning \
    --bucket realman-production-storage \
    --versioning-configuration Status=Enabled

# Set CORS policy (if accessing from browser)
aws s3api put-bucket-cors \
    --bucket realman-production-storage \
    --cors-configuration file://s3-cors.json
```

**s3-cors.json:**
```json
{
    "CORSRules": [
        {
            "AllowedOrigins": ["https://yourdomain.com"],
            "AllowedMethods": ["GET", "PUT", "POST", "DELETE"],
            "AllowedHeaders": ["*"],
            "MaxAgeSeconds": 3000
        }
    ]
}
```

#### Step 2: Create IAM User for S3 Access

**Via AWS Console:**
1. Go to IAM → Users → Create user
2. Username: `realman-s3-user`
3. Access type: Access key - Programmatic access
4. Attach policy: `AmazonS3FullAccess` (or create custom policy below)
5. Save Access Key ID and Secret Access Key

**Custom S3 Policy (Recommended - Least Privilege):**
```json
{
    "Version": "2012-10-17",
    "Statement": [
        {
            "Effect": "Allow",
            "Action": [
                "s3:PutObject",
                "s3:GetObject",
                "s3:DeleteObject",
                "s3:ListBucket"
            ],
            "Resource": [
                "arn:aws:s3:::realman-production-storage",
                "arn:aws:s3:::realman-production-storage/*"
            ]
        }
    ]
}
```

#### Step 3: Configure Laravel

**Update `.env`:**
```env
FILESYSTEM_DISK=s3

AWS_ACCESS_KEY_ID=AKIA...your-access-key
AWS_SECRET_ACCESS_KEY=your-secret-access-key
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=realman-production-storage
AWS_USE_PATH_STYLE_ENDPOINT=false

# Optional: Custom S3 URL (if using CloudFront)
AWS_URL=https://d111111abcdef8.cloudfront.net
```

**Usage in Code:**
```php
use Illuminate\Support\Facades\Storage;

// Store file to S3
Storage::disk('s3')->put('uploads/file.pdf', $fileContents);

// Get file URL
$url = Storage::disk('s3')->url('uploads/file.pdf');

// Download file
$contents = Storage::disk('s3')->get('uploads/file.pdf');

// Delete file
Storage::disk('s3')->delete('uploads/file.pdf');

// Check if file exists
$exists = Storage::disk('s3')->exists('uploads/file.pdf');
```

**File Upload Example:**
```php
// In your controller
public function uploadDocument(Request $request)
{
    $request->validate([
        'document' => 'required|file|max:10240', // 10MB max
    ]);

    // Store file with unique name
    $path = $request->file('document')->store('documents', 's3');
    
    // Get public URL
    $url = Storage::disk('s3')->url($path);
    
    // Save to database
    Document::create([
        'file_path' => $path,
        'file_url' => $url,
    ]);
    
    return response()->json(['url' => $url]);
}
```

---

### 2. Amazon SES (Email Service) Configuration

#### Step 1: Verify Email Addresses/Domain

**Via AWS Console:**
1. Go to SES → Verified identities → Create identity
2. Choose: Email address or Domain
3. For email: Enter email and verify via link sent to inbox
4. For domain: Add DNS TXT records to verify ownership

**Via AWS CLI:**
```bash
# Verify email
aws ses verify-email-identity --email-address noreply@yourdomain.com

# Verify domain
aws ses verify-domain-identity --domain yourdomain.com
```

#### Step 2: Request Production Access

**Important:** SES starts in sandbox mode (can only send to verified emails)

1. Go to SES → Account dashboard → Request production access
2. Fill out form explaining use case
3. Wait for approval (usually 24 hours)

#### Step 3: Create IAM User for SES

**Via AWS Console:**
1. IAM → Users → Create user: `realman-ses-user`
2. Attach policy: `AmazonSESFullAccess` or custom policy

**Custom SES Policy:**
```json
{
    "Version": "2012-10-17",
    "Statement": [
        {
            "Effect": "Allow",
            "Action": [
                "ses:SendEmail",
                "ses:SendRawEmail"
            ],
            "Resource": "*"
        }
    ]
}
```

#### Step 4: Configure Laravel

**Update `.env`:**
```env
MAIL_MAILER=ses
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="RealMan"

AWS_ACCESS_KEY_ID=AKIA...your-ses-access-key
AWS_SECRET_ACCESS_KEY=your-ses-secret-key
AWS_DEFAULT_REGION=us-east-1
```

**config/services.php** - Verify this exists:
```php
'ses' => [
    'key' => env('AWS_ACCESS_KEY_ID'),
    'secret' => env('AWS_SECRET_ACCESS_KEY'),
    'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
],
```

**Usage in Code:**
```php
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderConfirmation;

// Send email
Mail::to($customer->email)->send(new OrderConfirmation($order));

// Queue email (recommended)
Mail::to($customer->email)->queue(new OrderConfirmation($order));
```

**Test SES:**
```bash
php artisan tinker

# Send test email
Mail::raw('Test email from SES', function ($message) {
    $message->to('test@example.com')
            ->subject('Test Email');
});
```

---

### 3. Amazon SQS (Queue Service) Configuration

#### Step 1: Create SQS Queue

**Via AWS Console:**
1. Go to SQS → Create queue
2. Type: Standard queue (cheaper) or FIFO queue (ordered)
3. Name: `realman-production-queue`
4. Configuration:
   - Visibility timeout: 90 seconds
   - Message retention: 4 days
   - Receive message wait time: 20 seconds (long polling)
5. Create queue

**Via AWS CLI:**
```bash
aws sqs create-queue \
    --queue-name realman-production-queue \
    --region us-east-1 \
    --attributes VisibilityTimeout=90,MessageRetentionPeriod=345600
```

#### Step 2: Get Queue URL

**Via Console:** Copy the queue URL from queue details

**Via CLI:**
```bash
aws sqs get-queue-url --queue-name realman-production-queue
# Output: https://sqs.us-east-1.amazonaws.com/123456789012/realman-production-queue
```

#### Step 3: Create IAM User for SQS

**Custom SQS Policy:**
```json
{
    "Version": "2012-10-17",
    "Statement": [
        {
            "Effect": "Allow",
            "Action": [
                "sqs:SendMessage",
                "sqs:ReceiveMessage",
                "sqs:DeleteMessage",
                "sqs:GetQueueAttributes",
                "sqs:GetQueueUrl"
            ],
            "Resource": "arn:aws:sqs:us-east-1:123456789012:realman-production-queue"
        }
    ]
}
```

#### Step 4: Configure Laravel

**Update `.env`:**
```env
QUEUE_CONNECTION=sqs

AWS_ACCESS_KEY_ID=AKIA...your-sqs-access-key
AWS_SECRET_ACCESS_KEY=your-sqs-secret-key
AWS_DEFAULT_REGION=us-east-1

SQS_PREFIX=https://sqs.us-east-1.amazonaws.com/123456789012
SQS_QUEUE=realman-production-queue
```

**config/queue.php** - Already configured (verified above)

**Usage in Code:**
```php
use App\Jobs\ProcessAnimalProcessing;

// Dispatch job to SQS
ProcessAnimalProcessing::dispatch($processingRequest);

// Dispatch with delay
ProcessAnimalProcessing::dispatch($processingRequest)->delay(now()->addMinutes(10));

// Dispatch to specific queue
ProcessAnimalProcessing::dispatch($processingRequest)->onQueue('high-priority');
```

**Start Queue Worker:**
```bash
# On your server (via Supervisor)
php artisan queue:work sqs --sleep=3 --tries=3 --max-time=3600
```

**Test SQS:**
```bash
php artisan queue:work sqs --once

# In another terminal, dispatch a job
php artisan tinker
dispatch(new \App\Jobs\TestJob());
```

---

### 4. Amazon ElastiCache (Redis) Configuration

#### Step 1: Create ElastiCache Redis Cluster

**Via AWS Console:**
1. Go to ElastiCache → Redis clusters → Create
2. Cluster mode: Disabled (simpler)
3. Name: `realman-redis`
4. Node type: `cache.t3.micro` ($13/month)
5. Number of replicas: 0 (or 1 for HA)
6. Subnet group: Create new or use existing VPC
7. Security group: Allow port 6379 from application server
8. Create

**Via AWS CLI:**
```bash
aws elasticache create-cache-cluster \
    --cache-cluster-id realman-redis \
    --cache-node-type cache.t3.micro \
    --engine redis \
    --num-cache-nodes 1 \
    --region us-east-1
```

#### Step 2: Get Redis Endpoint

**Via Console:** Copy primary endpoint from cluster details

**Via CLI:**
```bash
aws elasticache describe-cache-clusters \
    --cache-cluster-id realman-redis \
    --show-cache-node-info
```

#### Step 3: Configure Laravel

**Install Predis:**
```bash
composer require predis/predis
```

**Update `.env`:**
```env
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis  # Or keep as SQS

REDIS_HOST=realman-redis.abc123.0001.use1.cache.amazonaws.com
REDIS_PASSWORD=null  # ElastiCache doesn't use password by default
REDIS_PORT=6379
REDIS_CLIENT=predis
```

**config/database.php** - Verify Redis configuration:
```php
'redis' => [
    'client' => env('REDIS_CLIENT', 'predis'),
    
    'default' => [
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'password' => env('REDIS_PASSWORD'),
        'port' => env('REDIS_PORT', 6379),
        'database' => 0,
    ],
    
    'cache' => [
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'password' => env('REDIS_PASSWORD'),
        'port' => env('REDIS_PORT', 6379),
        'database' => 1,
    ],
],
```

**Usage in Code:**
```php
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

// Cache usage
Cache::put('key', 'value', 3600); // 1 hour
$value = Cache::get('key');

// Redis direct usage
Redis::set('user:1:name', 'John Doe');
$name = Redis::get('user:1:name');
```

**Test Redis:**
```bash
php artisan tinker
Cache::put('test', 'value', 60);
Cache::get('test'); // Should return 'value'
```

---

### 5. CloudFront CDN Configuration

#### Step 1: Create CloudFront Distribution

**Via AWS Console:**
1. Go to CloudFront → Create distribution
2. Origin domain: Select your S3 bucket or application domain
3. Origin access: Public or Origin Access Control (OAC)
4. Default cache behavior: 
   - Viewer protocol: Redirect HTTP to HTTPS
   - Allowed methods: GET, HEAD (or all if API)
   - Cache policy: CachingOptimized
5. Settings:
   - Alternate domain names: www.yourdomain.com, yourdomain.com
   - SSL certificate: Request from ACM or use default
6. Create distribution

**For S3 Static Assets:**
```bash
# Create distribution pointing to S3
aws cloudfront create-distribution --distribution-config file://cloudfront-config.json
```

#### Step 2: Configure Laravel to Use CloudFront

**Update `.env`:**
```env
# Use CloudFront URL for S3 assets
AWS_URL=https://d111111abcdef8.cloudfront.net

# Or set asset URL
ASSET_URL=https://d111111abcdef8.cloudfront.net
```

**Usage:**
```php
// Files uploaded to S3 will automatically use CloudFront URL
$url = Storage::disk('s3')->url('uploads/file.pdf');
// Returns: https://d111111abcdef8.cloudfront.net/uploads/file.pdf

// For assets in public folder
<link rel="stylesheet" href="{{ asset('css/app.css') }}">
// Returns: https://d111111abcdef8.cloudfront.net/css/app.css
```

---

### 6. AWS Certificate Manager (SSL) Configuration

#### Create Free SSL Certificate

**Via AWS Console:**
1. Go to Certificate Manager → Request certificate
2. Certificate type: Public certificate
3. Domain names:
   - `yourdomain.com`
   - `*.yourdomain.com` (wildcard)
4. Validation: DNS validation (recommended)
5. Add CNAME records to your DNS (Route 53 or external)
6. Wait for validation (usually 5-30 minutes)

**Via AWS CLI:**
```bash
aws acm request-certificate \
    --domain-name yourdomain.com \
    --subject-alternative-names *.yourdomain.com \
    --validation-method DNS \
    --region us-east-1
```

**Use with CloudFront:**
- Select the validated certificate in CloudFront settings
- CloudFront will automatically serve content over HTTPS

---

### 7. Route 53 (DNS) Configuration

#### Step 1: Create Hosted Zone

**Via AWS Console:**
1. Go to Route 53 → Hosted zones → Create
2. Domain name: `yourdomain.com`
3. Type: Public hosted zone
4. Create

**Copy nameservers** and update at your domain registrar

#### Step 2: Create DNS Records

**For application (Lightsail):**
```bash
# A record pointing to Lightsail static IP
Name: yourdomain.com
Type: A
Value: 54.123.45.67 (your Lightsail IP)

# CNAME for www
Name: www.yourdomain.com
Type: CNAME
Value: yourdomain.com
```

**For CloudFront:**
```bash
# Alias record for CloudFront
Name: cdn.yourdomain.com
Type: A - Alias
Value: Select your CloudFront distribution
```

---

### 8. CloudWatch Logging (Optional)

#### Configure CloudWatch for Laravel Logs

**Install Package:**
```bash
composer require maxbanton/cwh
```

**Update `config/logging.php`:**
```php
'channels' => [
    // ... existing channels
    
    'cloudwatch' => [
        'driver' => 'custom',
        'via' => \App\Logging\CloudWatchLoggerFactory::class,
        'sdk' => [
            'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
            'version' => 'latest',
            'credentials' => [
                'key' => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ],
        ],
        'retention' => 7, // days
        'level' => 'debug',
        'name' => 'realman-production',
        'group' => env('CLOUDWATCH_LOG_GROUP', '/aws/laravel/realman'),
        'stream' => env('CLOUDWATCH_LOG_STREAM', 'production'),
    ],
    
    'stack' => [
        'driver' => 'stack',
        'channels' => ['single', 'cloudwatch'],
        'ignore_exceptions' => false,
    ],
],
```

**Create Factory:**
```php
// app/Logging/CloudWatchLoggerFactory.php
<?php

namespace App\Logging;

use Aws\CloudWatchLogs\CloudWatchLogsClient;
use Maxbanton\Cwh\Handler\CloudWatch;
use Monolog\Logger;

class CloudWatchLoggerFactory
{
    public function __invoke(array $config)
    {
        $client = new CloudWatchLogsClient($config['sdk']);
        
        $handler = new CloudWatch(
            $client,
            $config['group'],
            $config['stream'],
            $config['retention'] ?? 14,
            10000
        );
        
        return new Logger($config['name'], [$handler]);
    }
}
```

---

## Complete Production `.env` Configuration

```env
APP_NAME="RealMan"
APP_ENV=production
APP_KEY=base64:your-generated-key-here
APP_DEBUG=false
APP_URL=https://yourdomain.com

LOG_CHANNEL=stack
LOG_LEVEL=error

# Database (RDS MySQL)
DB_CONNECTION=mysql
DB_HOST=realman-db.abc123.us-east-1.rds.amazonaws.com
DB_PORT=3306
DB_DATABASE=realman_production
DB_USERNAME=admin
DB_PASSWORD=your-secure-password

# Cache & Session (ElastiCache Redis)
CACHE_DRIVER=redis
SESSION_DRIVER=redis
SESSION_LIFETIME=120

# Queue (SQS)
QUEUE_CONNECTION=sqs

# File Storage (S3)
FILESYSTEM_DISK=s3

# AWS Credentials
AWS_ACCESS_KEY_ID=AKIA...your-access-key
AWS_SECRET_ACCESS_KEY=your-secret-access-key
AWS_DEFAULT_REGION=us-east-1

# S3 Configuration
AWS_BUCKET=realman-production-storage
AWS_URL=https://d111111abcdef8.cloudfront.net
AWS_USE_PATH_STYLE_ENDPOINT=false

# SQS Configuration
SQS_PREFIX=https://sqs.us-east-1.amazonaws.com/123456789012
SQS_QUEUE=realman-production-queue

# Redis (ElastiCache)
REDIS_HOST=realman-redis.abc123.0001.use1.cache.amazonaws.com
REDIS_PASSWORD=null
REDIS_PORT=6379
REDIS_CLIENT=predis

# Email (SES)
MAIL_MAILER=ses
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"

# CORS Settings
CORS_ALLOWED_ORIGINS=https://yourdomain.com,https://www.yourdomain.com
SANCTUM_STATEFUL_DOMAINS=yourdomain.com,www.yourdomain.com

# SMS/OTP Settings
SMS_DRIVER=frog
OTP_EXPIRY_MINUTES=10
SMS_PROVIDER=frog
FROGSMS_BASE_URL=https://frog.wigal.com.gh/ismsweb/sendmsg
FROGSMS_PASSWORD=your-password
FROGSMS_USERNAME=your-username
FROGSMS_SENDER_ID=your-sender-id

# CloudWatch Logging (Optional)
CLOUDWATCH_LOG_GROUP=/aws/laravel/realman
CLOUDWATCH_LOG_STREAM=production
```

---

## Testing AWS Services

### Test S3
```bash
php artisan tinker

use Illuminate\Support\Facades\Storage;

# Upload test file
Storage::disk('s3')->put('test.txt', 'Hello S3!');

# Get URL
Storage::disk('s3')->url('test.txt');

# Download
Storage::disk('s3')->get('test.txt');

# Delete
Storage::disk('s3')->delete('test.txt');
```

### Test SES
```bash
php artisan tinker

use Illuminate\Support\Facades\Mail;

Mail::raw('Test from SES', function ($message) {
    $message->to('your-email@example.com')
            ->subject('Test Email');
});
```

### Test SQS
```bash
# Terminal 1: Start worker
php artisan queue:work sqs --once

# Terminal 2: Dispatch job
php artisan tinker
dispatch(function () {
    logger('Job executed via SQS!');
});
```

### Test Redis
```bash
php artisan tinker

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

# Cache test
Cache::put('test-key', 'test-value', 60);
Cache::get('test-key'); // Should return 'test-value'

# Redis direct test
Redis::ping(); // Should return 'PONG'
```

---

## Security Best Practices

### 1. IAM User Management
- ✅ Create separate IAM users for each service
- ✅ Use least-privilege policies
- ✅ Rotate access keys every 90 days
- ✅ Enable MFA for console access
- ✅ Never commit credentials to Git

### 2. Network Security
- ✅ Use VPC for RDS and ElastiCache
- ✅ Configure security groups to allow only necessary IPs
- ✅ Enable encryption at rest for RDS
- ✅ Enable encryption in transit (SSL/TLS)

### 3. S3 Bucket Security
- ✅ Block public access unless specifically needed
- ✅ Enable versioning for important files
- ✅ Use bucket policies for fine-grained access
- ✅ Enable server-side encryption (SSE-S3 or SSE-KMS)

### 4. Application Security
- ✅ Store AWS credentials in `.env` (never in code)
- ✅ Use IAM roles on EC2/Lightsail instead of keys when possible
- ✅ Enable CloudTrail for audit logging
- ✅ Set up billing alerts

---

## Troubleshooting

### S3 Upload Fails
```bash
# Check credentials
aws s3 ls s3://realman-production-storage --profile your-profile

# Check bucket policy
aws s3api get-bucket-policy --bucket realman-production-storage

# Enable debug mode temporarily
AWS_DEBUG=true php artisan tinker
```

### SES Email Not Sending
```bash
# Verify email is confirmed
aws ses get-identity-verification-attributes --identities noreply@yourdomain.com

# Check sending quota
aws ses get-send-quota

# Check sandbox status
aws ses get-account-sending-enabled
```

### SQS Jobs Not Processing
```bash
# Check queue exists
aws sqs get-queue-url --queue-name realman-production-queue

# Check messages in queue
aws sqs get-queue-attributes \
    --queue-url https://sqs.us-east-1.amazonaws.com/xxx/realman-production-queue \
    --attribute-names ApproximateNumberOfMessages

# Check worker logs
tail -f storage/logs/laravel.log
```

### Redis Connection Failed
```bash
# Check ElastiCache security group allows port 6379
# Check Redis endpoint is correct
# Test connection from server
telnet realman-redis.abc123.0001.use1.cache.amazonaws.com 6379
```

---

## Cost Monitoring

### Set Up Billing Alerts

1. Go to AWS Billing → Budgets → Create budget
2. Budget type: Cost budget
3. Amount: $50/month (adjust as needed)
4. Alert threshold: 80% and 100%
5. Email notification to your email

### Monitor Costs by Service

```bash
# Install AWS CLI cost explorer
aws ce get-cost-and-usage \
    --time-period Start=2025-12-01,End=2025-12-31 \
    --granularity MONTHLY \
    --metrics "BlendedCost" \
    --group-by Type=DIMENSION,Key=SERVICE
```

---

**Last Updated:** December 27, 2025
