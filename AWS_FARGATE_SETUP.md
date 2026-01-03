# AWS Fargate Architecture - Quick Reference

## ⭐ RECOMMENDED: AWS Fargate for RealMan Livestock (Ghana)

### Why Fargate?
- ✅ **$183/month** - Cheapest option
- ✅ **Zero server management** - No EC2, no SSH, no patching
- ✅ **Auto-scaling** - Built-in, handles traffic automatically
- ✅ **Fast deployments** - Update app in seconds
- ✅ **Available in Africa** - Easy migration to Cape Town region

---

## Architecture Overview

```
User Request → CloudFront CDN → ALB → Fargate Tasks (Laravel) → RDS MySQL
                                                                → ElastiCache Redis
                                                                → S3
                                                                → SQS → Lambda
```

---

## Cost Breakdown (EU Ireland - eu-west-1)

| Service | Configuration | Monthly Cost |
|---------|--------------|--------------|
| **AWS Fargate** | 2 tasks × 0.5 vCPU × 1GB × 730hrs | **$33** |
| **Application Load Balancer** | Internet-facing, 2 AZs | **$22** |
| **ECR (Container Registry)** | Docker image storage | **$1** |
| **RDS MySQL Multi-AZ** | db.t3.small | $65 |
| **ElastiCache Redis** | 2 nodes (cache.t3.micro) | $32 |
| **S3 + CloudFront** | Storage + CDN | $15 |
| **SQS + Lambda** | Queue + Workers | $0 (free) |
| **SES Email** | Transactional emails | $2 |
| **Route 53 + CloudWatch** | DNS + Monitoring | $11 |
| **Secrets Manager** | Credentials storage | $2 |
| **TOTAL** | | **~$183/month** |

---

## Fargate vs Alternatives

| Feature | Fargate ⭐ | Lightsail | EC2 |
|---------|-----------|-----------|-----|
| **Monthly Cost (EU)** | **$183** | $225 | $211 |
| **Operational Overhead** | **Zero** | Minimal | High |
| **Server Management** | **None** | SSH, updates | Full control |
| **Auto-scaling** | **Built-in** | Manual | Requires setup |
| **Deployment Speed** | **Seconds** | Minutes | Minutes |
| **Available in Africa** | **Yes** | No | Yes |
| **Container Native** | **Yes** | No | DIY |
| **Rollback Speed** | **Instant** | Slow | Slow |

---

## Setup Steps

### 1. Dockerize Laravel App

**Dockerfile:**
```dockerfile
FROM php:8.2-fpm-alpine

# Install dependencies
RUN apk add --no-cache \
    nginx \
    mysql-client \
    redis

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql

# Copy Laravel app
COPY . /var/www/html
WORKDIR /var/www/html

# Install Composer dependencies
RUN composer install --optimize-autoloader --no-dev

# Configure Nginx
COPY docker/nginx.conf /etc/nginx/nginx.conf

# Start script
COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 80

CMD ["/start.sh"]
```

**docker/start.sh:**
```bash
#!/bin/sh
php artisan config:cache
php artisan route:cache
php artisan view:cache
php-fpm -D
nginx -g 'daemon off;'
```

### 2. Build and Push to ECR

```bash
# Authenticate to ECR
aws ecr get-login-password --region eu-west-1 | \
  docker login --username AWS --password-stdin <account-id>.dkr.ecr.eu-west-1.amazonaws.com

# Create repository
aws ecr create-repository --repository-name realman-app --region eu-west-1

# Build image
docker build -t realman-app:latest .

# Tag for ECR
docker tag realman-app:latest <account-id>.dkr.ecr.eu-west-1.amazonaws.com/realman-app:latest

# Push to ECR
docker push <account-id>.dkr.ecr.eu-west-1.amazonaws.com/realman-app:latest
```

### 3. Create ECS Cluster

```bash
# Create cluster
aws ecs create-cluster --cluster-name realman-production --region eu-west-1
```

### 4. Create Task Definition

**task-definition.json:**
```json
{
  "family": "realman-app",
  "networkMode": "awsvpc",
  "requiresCompatibilities": ["FARGATE"],
  "cpu": "512",
  "memory": "1024",
  "containerDefinitions": [
    {
      "name": "laravel-app",
      "image": "<account-id>.dkr.ecr.eu-west-1.amazonaws.com/realman-app:latest",
      "portMappings": [
        {
          "containerPort": 80,
          "protocol": "tcp"
        }
      ],
      "environment": [
        {"name": "APP_ENV", "value": "production"},
        {"name": "DB_HOST", "value": "realman-db.xxxxx.eu-west-1.rds.amazonaws.com"}
      ],
      "secrets": [
        {
          "name": "APP_KEY",
          "valueFrom": "arn:aws:secretsmanager:eu-west-1:xxxx:secret:realman/app-key"
        },
        {
          "name": "DB_PASSWORD",
          "valueFrom": "arn:aws:secretsmanager:eu-west-1:xxxx:secret:realman/db-password"
        }
      ],
      "logConfiguration": {
        "logDriver": "awslogs",
        "options": {
          "awslogs-group": "/ecs/realman-app",
          "awslogs-region": "eu-west-1",
          "awslogs-stream-prefix": "ecs"
        }
      }
    }
  ]
}
```

```bash
# Register task definition
aws ecs register-task-definition --cli-input-json file://task-definition.json
```

### 5. Create ALB

```bash
# Create load balancer
aws elbv2 create-load-balancer \
  --name realman-alb \
  --subnets subnet-xxxxx subnet-yyyyy \
  --security-groups sg-xxxxx \
  --region eu-west-1

# Create target group
aws elbv2 create-target-group \
  --name realman-targets \
  --protocol HTTP \
  --port 80 \
  --vpc-id vpc-xxxxx \
  --target-type ip \
  --health-check-path /health \
  --region eu-west-1

# Create listener
aws elbv2 create-listener \
  --load-balancer-arn <alb-arn> \
  --protocol HTTP \
  --port 80 \
  --default-actions Type=forward,TargetGroupArn=<target-group-arn>
```

### 6. Create ECS Service

```bash
aws ecs create-service \
  --cluster realman-production \
  --service-name realman-web \
  --task-definition realman-app \
  --desired-count 2 \
  --launch-type FARGATE \
  --network-configuration "awsvpcConfiguration={subnets=[subnet-xxxxx,subnet-yyyyy],securityGroups=[sg-xxxxx],assignPublicIp=DISABLED}" \
  --load-balancers targetGroupArn=<target-group-arn>,containerName=laravel-app,containerPort=80 \
  --region eu-west-1
```

### 7. Configure Auto-scaling

```bash
# Register scalable target
aws application-autoscaling register-scalable-target \
  --service-namespace ecs \
  --resource-id service/realman-production/realman-web \
  --scalable-dimension ecs:service:DesiredCount \
  --min-capacity 1 \
  --max-capacity 4

# Create scaling policy
aws application-autoscaling put-scaling-policy \
  --service-namespace ecs \
  --resource-id service/realman-production/realman-web \
  --scalable-dimension ecs:service:DesiredCount \
  --policy-name cpu-scaling \
  --policy-type TargetTrackingScaling \
  --target-tracking-scaling-policy-configuration file://scaling-policy.json
```

**scaling-policy.json:**
```json
{
  "TargetValue": 70.0,
  "PredefinedMetricSpecification": {
    "PredefinedMetricType": "ECSServiceAverageCPUUtilization"
  },
  "ScaleInCooldown": 300,
  "ScaleOutCooldown": 60
}
```

---

## Deployment Workflow

### Initial Deployment
```bash
# 1. Build and push image
docker build -t realman-app:v1.0.0 .
docker push <ecr-url>/realman-app:v1.0.0

# 2. ECS automatically pulls and deploys
# (Service already configured above)
```

### Update Deployment (Zero Downtime)
```bash
# 1. Build new version
docker build -t realman-app:v1.0.1 .
docker tag realman-app:v1.0.1 <ecr-url>/realman-app:latest
docker push <ecr-url>/realman-app:latest

# 2. Force new deployment
aws ecs update-service \
  --cluster realman-production \
  --service realman-web \
  --force-new-deployment \
  --region eu-west-1

# ECS performs rolling update:
# - Starts new tasks with new image
# - Waits for health checks to pass
# - Drains connections from old tasks
# - Stops old tasks
# Total time: ~2-3 minutes, zero downtime
```

### Rollback
```bash
# Tag previous version as latest
docker tag realman-app:v1.0.0 <ecr-url>/realman-app:latest
docker push <ecr-url>/realman-app:latest

# Force new deployment
aws ecs update-service --cluster realman-production --service realman-web --force-new-deployment
```

---

## Environment Variables

Store secrets in AWS Secrets Manager:

```bash
# Create secrets
aws secretsmanager create-secret \
  --name realman/app-key \
  --secret-string "base64:xxxxx" \
  --region eu-west-1

aws secretsmanager create-secret \
  --name realman/db-password \
  --secret-string "secure-password" \
  --region eu-west-1

# Task definition automatically fetches these
```

---

## Monitoring

### CloudWatch Logs
```bash
# View logs
aws logs tail /ecs/realman-app --follow --region eu-west-1
```

### Metrics to Monitor
- **CPU Utilization** - Should stay below 70%
- **Memory Utilization** - Should stay below 80%
- **Request Count** - Track traffic patterns
- **Response Time** - Should be < 500ms
- **Error Rate** - Should be < 1%

### Alarms
```bash
# High CPU alarm
aws cloudwatch put-metric-alarm \
  --alarm-name realman-high-cpu \
  --alarm-description "Alert when CPU exceeds 80%" \
  --metric-name CPUUtilization \
  --namespace AWS/ECS \
  --statistic Average \
  --period 300 \
  --threshold 80 \
  --comparison-operator GreaterThanThreshold \
  --evaluation-periods 2
```

---

## Migration to Africa Region

When ready to move to Cape Town (af-south-1):

```bash
# 1. Create ECR repository in af-south-1
aws ecr create-repository --repository-name realman-app --region af-south-1

# 2. Copy image
docker pull <eu-account>.dkr.ecr.eu-west-1.amazonaws.com/realman-app:latest
docker tag <eu-account>.dkr.ecr.eu-west-1.amazonaws.com/realman-app:latest \
  <af-account>.dkr.ecr.af-south-1.amazonaws.com/realman-app:latest
docker push <af-account>.dkr.ecr.af-south-1.amazonaws.com/realman-app:latest

# 3. Repeat steps 3-7 above in af-south-1 region

# 4. Update Route 53 to point to new ALB

# Total migration time: < 1 hour
```

---

## Cost Optimization Tips

1. **Right-size tasks** - Start with 0.5 vCPU, 1GB; monitor and adjust
2. **Use Spot for non-prod** - 70% cheaper for dev/staging environments
3. **Scale down off-hours** - Reduce to 1 task at night if applicable
4. **Use S3 Intelligent-Tiering** - Automatically moves old files to cheaper storage
5. **Enable Container Insights** - Only when troubleshooting (adds cost)

---

## Comparison: Ghana Deployment Costs (24/7 operation)

| Region | Fargate Cost | Total Monthly | Latency from Accra |
|--------|-------------|---------------|-------------------|
| **EU Ireland (eu-west-1)** | **$55** | **$183** | **~150ms** ⭐ |
| **Africa Cape Town (af-south-1)** | $64 | $214 | ~100ms |
| **US East (us-east-1)** | $51 | $179 | ~200ms |

**Recommendation:** Start in EU Ireland, migrate to Africa when revenue justifies the extra $31/month.

---

## Next Steps

1. ✅ Dockerize Laravel application
2. ✅ Set up ECR repository
3. ✅ Create ECS cluster and task definition
4. ✅ Configure ALB and target groups
5. ✅ Deploy service with 2 tasks
6. ✅ Set up auto-scaling
7. ✅ Configure CloudWatch monitoring
8. ✅ Test deployment and rollback procedures

**Total setup time:** 1-2 days for experienced team, 3-5 days for learning

---

This Fargate architecture gives you the best balance of low cost ($183/month), zero operational overhead, and scalability for the RealMan Livestock application! 🚀
