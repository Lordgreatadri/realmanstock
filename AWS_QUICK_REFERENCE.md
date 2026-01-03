# AWS Services Quick Reference

Quick commands and snippets for managing AWS services in the RealMan application.

## 📦 Installed Packages

✅ **Already installed:**
```bash
composer require aws/aws-sdk-php
composer require league/flysystem-aws-s3-v3
```

✅ **Optional (for Redis):**
```bash
composer require predis/predis
```

---

## 🗂️ S3 File Storage

### Upload File
```php
use Illuminate\Support\Facades\Storage;

// Upload
Storage::disk('s3')->put('path/file.pdf', $contents);

// Upload from request
$path = $request->file('document')->store('uploads', 's3');

// Get URL
$url = Storage::disk('s3')->url('path/file.pdf');

// Delete
Storage::disk('s3')->delete('path/file.pdf');

// Check exists
Storage::disk('s3')->exists('path/file.pdf');
```

### Required ENV
```env
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=your-key
AWS_SECRET_ACCESS_KEY=your-secret
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=realman-production-storage
AWS_URL=https://d111111abcdef8.cloudfront.net  # Optional CloudFront
```

---

## 📧 SES Email Service

### Send Email
```php
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderConfirmation;

// Send immediately
Mail::to($customer->email)->send(new OrderConfirmation($order));

// Queue email (recommended)
Mail::to($customer->email)->queue(new OrderConfirmation($order));
```

### Required ENV
```env
MAIL_MAILER=ses
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="RealMan"
AWS_ACCESS_KEY_ID=your-key
AWS_SECRET_ACCESS_KEY=your-secret
AWS_DEFAULT_REGION=us-east-1
```

### Test Command
```bash
php artisan tinker
Mail::raw('Test email', fn($m) => $m->to('test@example.com')->subject('Test'));
```

---

## 🔄 SQS Queue Service

### Dispatch Job
```php
use App\Jobs\ProcessOrder;

// Dispatch to queue
ProcessOrder::dispatch($order);

// Dispatch with delay
ProcessOrder::dispatch($order)->delay(now()->addMinutes(10));

// Dispatch to specific queue
ProcessOrder::dispatch($order)->onQueue('high-priority');
```

### Required ENV
```env
QUEUE_CONNECTION=sqs
AWS_ACCESS_KEY_ID=your-key
AWS_SECRET_ACCESS_KEY=your-secret
AWS_DEFAULT_REGION=us-east-1
SQS_PREFIX=https://sqs.us-east-1.amazonaws.com/123456789012
SQS_QUEUE=realman-production-queue
```

### Worker Commands
```bash
# Start worker (development)
php artisan queue:work sqs

# Production (via Supervisor)
php artisan queue:work sqs --sleep=3 --tries=3 --max-time=3600

# Process one job
php artisan queue:work sqs --once

# Restart all workers
php artisan queue:restart
```

---

## 🔴 Redis Cache/Sessions

### Cache Usage
```php
use Illuminate\Support\Facades\Cache;

// Store
Cache::put('key', 'value', 3600); // 1 hour

// Get
$value = Cache::get('key');

// Remember (get or store)
$users = Cache::remember('users', 3600, function () {
    return User::all();
});

// Forget
Cache::forget('key');

// Flush all
Cache::flush();
```

### Required ENV
```env
CACHE_DRIVER=redis
SESSION_DRIVER=redis
REDIS_HOST=realman-redis.abc123.0001.use1.cache.amazonaws.com
REDIS_PASSWORD=null
REDIS_PORT=6379
REDIS_CLIENT=predis
```

---

## 🌐 CloudFront CDN

### Usage
CloudFront works automatically with S3 URLs when configured.

```php
// Files uploaded to S3 automatically use CloudFront
$url = Storage::disk('s3')->url('uploads/file.pdf');
// Returns: https://d111111abcdef8.cloudfront.net/uploads/file.pdf

// For public assets
<link href="{{ asset('css/app.css') }}" rel="stylesheet">
// Returns: https://d111111abcdef8.cloudfront.net/css/app.css
```

### Required ENV
```env
AWS_URL=https://d111111abcdef8.cloudfront.net
ASSET_URL=https://d111111abcdef8.cloudfront.net  # Optional for public assets
```

---

## 🔧 Common AWS CLI Commands

### S3 Operations
```bash
# List buckets
aws s3 ls

# List bucket contents
aws s3 ls s3://realman-production-storage/

# Upload file
aws s3 cp file.pdf s3://realman-production-storage/uploads/

# Download file
aws s3 cp s3://realman-production-storage/uploads/file.pdf ./

# Sync directory
aws s3 sync storage/app/public s3://realman-production-storage/public/
```

### SQS Operations
```bash
# List queues
aws sqs list-queues

# Get queue URL
aws sqs get-queue-url --queue-name realman-production-queue

# Send message
aws sqs send-message \
    --queue-url https://sqs.us-east-1.amazonaws.com/xxx/realman-production-queue \
    --message-body "Test message"

# Get queue attributes
aws sqs get-queue-attributes \
    --queue-url https://sqs.us-east-1.amazonaws.com/xxx/realman-production-queue \
    --attribute-names All
```

### SES Operations
```bash
# List verified emails
aws ses list-identities

# Verify email
aws ses verify-email-identity --email-address noreply@yourdomain.com

# Get sending quota
aws ses get-send-quota

# Get send statistics
aws ses get-send-statistics
```

### RDS Operations
```bash
# List DB instances
aws rds describe-db-instances

# Create snapshot
aws rds create-db-snapshot \
    --db-instance-identifier realman-db \
    --db-snapshot-identifier manual-backup-$(date +%Y%m%d)

# List snapshots
aws rds describe-db-snapshots --db-instance-identifier realman-db
```

---

## 🧪 Testing AWS Services Locally

### Test S3
```bash
php artisan tinker

Storage::disk('s3')->put('test.txt', 'Hello S3!');
Storage::disk('s3')->url('test.txt');
Storage::disk('s3')->get('test.txt');
Storage::disk('s3')->delete('test.txt');
```

### Test SES
```bash
php artisan tinker

Mail::raw('Test from SES', function ($message) {
    $message->to('your-email@example.com')->subject('Test');
});
```

### Test SQS
```bash
# Terminal 1
php artisan queue:work sqs --once

# Terminal 2
php artisan tinker
dispatch(function () { logger('SQS test job!'); });
```

### Test Redis
```bash
php artisan tinker

Cache::put('test', 'value', 60);
Cache::get('test');
Redis::ping(); // Should return 'PONG'
```

---

## 💰 Cost Optimization Tips

1. **Use Free Tiers:**
   - RDS: 750 hours/month (db.t3.micro)
   - S3: 5GB storage, 20k GET, 2k PUT requests
   - CloudFront: 50GB transfer, 2M requests
   - SES: 62,000 emails/month
   - SQS: 1M requests/month

2. **S3 Lifecycle Policies:**
   ```bash
   # Archive old files to Glacier after 90 days
   aws s3api put-bucket-lifecycle-configuration \
       --bucket realman-production-storage \
       --lifecycle-configuration file://lifecycle.json
   ```

3. **CloudWatch Alarms:**
   - Set billing alerts at $10, $50, $100
   - Monitor S3 storage growth
   - Track SES bounce rate

4. **Database Optimization:**
   - Use db.t3.micro during development
   - Scale to db.t3.small only when needed
   - Disable Multi-AZ for dev/staging

5. **Enable Compression:**
   - Gzip for text files in S3
   - Enable CloudFront compression
   - Compress API responses

---

## 🔒 Security Checklist

- [ ] Store AWS credentials in `.env` (never in code)
- [ ] Use separate IAM users for each service
- [ ] Apply least-privilege IAM policies
- [ ] Enable MFA for AWS console access
- [ ] Rotate access keys every 90 days
- [ ] Enable S3 bucket encryption
- [ ] Enable RDS encryption at rest
- [ ] Use HTTPS everywhere (CloudFront, app)
- [ ] Configure security groups properly
- [ ] Enable CloudTrail for audit logs
- [ ] Set up VPC for RDS and ElastiCache
- [ ] Review and remove unused resources monthly

---

## 🚨 Troubleshooting

### S3 Upload Fails
```bash
# Check credentials
aws s3 ls s3://realman-production-storage

# Check Laravel config
php artisan config:clear
php artisan config:cache

# Enable debug
AWS_DEBUG=true
```

### SES Emails Not Sending
```bash
# Check email verified
aws ses get-identity-verification-attributes --identities noreply@yourdomain.com

# Check sandbox status
aws ses get-account-sending-enabled

# Request production access
# Go to: AWS Console > SES > Account dashboard > Request production access
```

### SQS Jobs Stuck
```bash
# Check queue messages
aws sqs get-queue-attributes \
    --queue-url https://sqs.us-east-1.amazonaws.com/xxx/realman-production-queue \
    --attribute-names ApproximateNumberOfMessages

# Restart workers
php artisan queue:restart

# Check worker logs
tail -f storage/logs/laravel.log
```

### Redis Connection Failed
```bash
# Check endpoint
echo $REDIS_HOST

# Test connection from server
telnet your-redis-endpoint.cache.amazonaws.com 6379

# Check security group allows port 6379
# Check Redis config
php artisan tinker
Redis::ping();
```

---

## 📚 Additional Resources

- **Full AWS Configuration Guide:** [AWS_CONFIGURATION_GUIDE.md](AWS_CONFIGURATION_GUIDE.md)
- **Production Deployment Guide:** [PRODUCTION_DEPLOYMENT.md](PRODUCTION_DEPLOYMENT.md)
- **Laravel Documentation:** https://laravel.com/docs/10.x
- **AWS Free Tier:** https://aws.amazon.com/free/
- **Bref (Laravel on Lambda):** https://bref.sh/docs/frameworks/laravel.html

---

*Last Updated: December 27, 2025*
