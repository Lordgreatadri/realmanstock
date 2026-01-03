# Docker Deployment Guide

## Local Development with Docker

### Prerequisites
- Docker Desktop installed
- Docker Compose installed
- Git

### Quick Start

1. **Copy environment file:**
   ```bash
   cp .env.docker .env
   ```

2. **Generate application key:**
   ```bash
   php artisan key:generate
   ```

3. **Start containers:**
   ```bash
   docker-compose up -d
   ```

4. **Run migrations:**
   ```bash
   docker-compose exec app php artisan migrate
   ```

5. **Access application:**
   - App: http://localhost:8080
   - Mailhog: http://localhost:8025

### Docker Commands

```bash
# Start all services
docker-compose up -d

# Stop all services
docker-compose down

# View logs
docker-compose logs -f app

# Execute artisan commands
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed

# Access container shell
docker-compose exec app sh

# Rebuild containers
docker-compose build --no-cache

# Restart specific service
docker-compose restart app
```

---

## Production Deployment to AWS ECS Fargate

### 1. Build Production Image

```bash
# Build image
docker build -t realman-app:latest .

# Test locally
docker run -p 8080:80 \
  -e APP_ENV=production \
  -e DB_HOST=your-rds-endpoint \
  -e REDIS_HOST=your-redis-endpoint \
  realman-app:latest
```

### 2. Push to Amazon ECR

```bash
# Authenticate Docker to ECR
aws ecr get-login-password --region eu-west-1 | \
  docker login --username AWS --password-stdin <account-id>.dkr.ecr.eu-west-1.amazonaws.com

# Create ECR repository (first time only)
aws ecr create-repository \
  --repository-name realman-app \
  --region eu-west-1

# Tag image
docker tag realman-app:latest \
  <account-id>.dkr.ecr.eu-west-1.amazonaws.com/realman-app:latest

# Push image
docker push <account-id>.dkr.ecr.eu-west-1.amazonaws.com/realman-app:latest
```

### 3. Create ECS Task Definition

Create `ecs-task-definition.json`:

```json
{
  "family": "realman-app",
  "networkMode": "awsvpc",
  "requiresCompatibilities": ["FARGATE"],
  "cpu": "512",
  "memory": "1024",
  "executionRoleArn": "arn:aws:iam::<account-id>:role/ecsTaskExecutionRole",
  "taskRoleArn": "arn:aws:iam::<account-id>:role/realman-app-task-role",
  "containerDefinitions": [
    {
      "name": "realman-app",
      "image": "<account-id>.dkr.ecr.eu-west-1.amazonaws.com/realman-app:latest",
      "essential": true,
      "portMappings": [
        {
          "containerPort": 80,
          "protocol": "tcp"
        }
      ],
      "environment": [
        {"name": "APP_ENV", "value": "production"},
        {"name": "APP_DEBUG", "value": "false"},
        {"name": "LOG_CHANNEL", "value": "stderr"},
        {"name": "DB_CONNECTION", "value": "mysql"},
        {"name": "DB_HOST", "value": "realman-db.xxxxx.eu-west-1.rds.amazonaws.com"},
        {"name": "DB_PORT", "value": "3306"},
        {"name": "DB_DATABASE", "value": "realman"},
        {"name": "REDIS_HOST", "value": "realman-cache.xxxxx.cache.amazonaws.com"},
        {"name": "REDIS_PORT", "value": "6379"},
        {"name": "CACHE_DRIVER", "value": "redis"},
        {"name": "SESSION_DRIVER", "value": "redis"},
        {"name": "QUEUE_CONNECTION", "value": "sqs"},
        {"name": "AWS_DEFAULT_REGION", "value": "eu-west-1"},
        {"name": "MAIL_MAILER", "value": "ses"}
      ],
      "secrets": [
        {
          "name": "APP_KEY",
          "valueFrom": "arn:aws:secretsmanager:eu-west-1:<account-id>:secret:realman/app-key"
        },
        {
          "name": "DB_USERNAME",
          "valueFrom": "arn:aws:secretsmanager:eu-west-1:<account-id>:secret:realman/db-username"
        },
        {
          "name": "DB_PASSWORD",
          "valueFrom": "arn:aws:secretsmanager:eu-west-1:<account-id>:secret:realman/db-password"
        },
        {
          "name": "AWS_ACCESS_KEY_ID",
          "valueFrom": "arn:aws:secretsmanager:eu-west-1:<account-id>:secret:realman/aws-access-key"
        },
        {
          "name": "AWS_SECRET_ACCESS_KEY",
          "valueFrom": "arn:aws:secretsmanager:eu-west-1:<account-id>:secret:realman/aws-secret-key"
        }
      ],
      "logConfiguration": {
        "logDriver": "awslogs",
        "options": {
          "awslogs-group": "/ecs/realman-app",
          "awslogs-region": "eu-west-1",
          "awslogs-stream-prefix": "ecs"
        }
      },
      "healthCheck": {
        "command": ["CMD-SHELL", "curl -f http://localhost/health || exit 1"],
        "interval": 30,
        "timeout": 5,
        "retries": 3,
        "startPeriod": 60
      }
    }
  ]
}
```

Register task:
```bash
aws ecs register-task-definition \
  --cli-input-json file://ecs-task-definition.json \
  --region eu-west-1
```

### 4. Deploy to ECS

```bash
# Create or update service
aws ecs update-service \
  --cluster realman-production \
  --service realman-web \
  --task-definition realman-app \
  --desired-count 2 \
  --force-new-deployment \
  --region eu-west-1
```

### 5. Update Deployment (Zero Downtime)

```bash
# Build new version
docker build -t realman-app:v1.0.1 .

# Tag and push
docker tag realman-app:v1.0.1 <account-id>.dkr.ecr.eu-west-1.amazonaws.com/realman-app:latest
docker push <account-id>.dkr.ecr.eu-west-1.amazonaws.com/realman-app:latest

# Force new deployment (ECS pulls latest image)
aws ecs update-service \
  --cluster realman-production \
  --service realman-web \
  --force-new-deployment \
  --region eu-west-1

# Monitor deployment
aws ecs describe-services \
  --cluster realman-production \
  --services realman-web \
  --region eu-west-1
```

---

## CI/CD with GitHub Actions

Create `.github/workflows/deploy.yml`:

```yaml
name: Deploy to AWS ECS

on:
  push:
    branches: [main]

env:
  AWS_REGION: eu-west-1
  ECR_REPOSITORY: realman-app
  ECS_CLUSTER: realman-production
  ECS_SERVICE: realman-web

jobs:
  deploy:
    runs-on: ubuntu-latest
    
    steps:
      - name: Checkout code
        uses: actions/checkout@v3

      - name: Configure AWS credentials
        uses: aws-actions/configure-aws-credentials@v2
        with:
          aws-access-key-id: ${{ secrets.AWS_ACCESS_KEY_ID }}
          aws-secret-access-key: ${{ secrets.AWS_SECRET_ACCESS_KEY }}
          aws-region: ${{ env.AWS_REGION }}

      - name: Login to Amazon ECR
        id: login-ecr
        uses: aws-actions/amazon-ecr-login@v1

      - name: Build and push Docker image
        env:
          ECR_REGISTRY: ${{ steps.login-ecr.outputs.registry }}
          IMAGE_TAG: ${{ github.sha }}
        run: |
          docker build -t $ECR_REGISTRY/$ECR_REPOSITORY:$IMAGE_TAG .
          docker tag $ECR_REGISTRY/$ECR_REPOSITORY:$IMAGE_TAG $ECR_REGISTRY/$ECR_REPOSITORY:latest
          docker push $ECR_REGISTRY/$ECR_REPOSITORY:$IMAGE_TAG
          docker push $ECR_REGISTRY/$ECR_REPOSITORY:latest

      - name: Deploy to ECS
        run: |
          aws ecs update-service \
            --cluster ${{ env.ECS_CLUSTER }} \
            --service ${{ env.ECS_SERVICE }} \
            --force-new-deployment \
            --region ${{ env.AWS_REGION }}

      - name: Wait for deployment
        run: |
          aws ecs wait services-stable \
            --cluster ${{ env.ECS_CLUSTER }} \
            --services ${{ env.ECS_SERVICE }} \
            --region ${{ env.AWS_REGION }}
```

---

## Troubleshooting

### Container won't start

```bash
# Check container logs
docker logs <container-id>

# Check Fargate task logs
aws logs tail /ecs/realman-app --follow --region eu-west-1

# Exec into running container
docker exec -it <container-id> sh
```

### Database connection issues

```bash
# Test from container
docker-compose exec app php artisan db:show

# Test MySQL connection
docker-compose exec app mysql -h mysql -u realman -psecret realman
```

### Redis connection issues

```bash
# Test Redis
docker-compose exec app redis-cli -h redis ping

# Check Redis keys
docker-compose exec redis redis-cli keys '*'
```

### Permission issues

```bash
# Fix storage permissions
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
docker-compose exec app chmod -R 775 storage bootstrap/cache
```

### Clear caches

```bash
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan view:clear
docker-compose exec app php artisan cache:clear
```

---

## Performance Optimization

### 1. Multi-stage build (already implemented)
The Dockerfile uses multi-stage builds to minimize image size.

### 2. Layer caching
Dependencies are copied before application code for better caching.

### 3. OPcache configuration
PHP OPcache is enabled in `docker/php.ini`.

### 4. Nginx optimization
Gzip compression and caching configured in `docker/nginx.conf`.

### 5. Image size
Current image size: ~150MB (Alpine-based)

---

## Security Best Practices

1. ✅ Use official base images (php:8.2-fpm-alpine)
2. ✅ Run as non-root user (www-data)
3. ✅ Scan images for vulnerabilities
4. ✅ Use multi-stage builds
5. ✅ Store secrets in AWS Secrets Manager
6. ✅ Disable PHP expose_php
7. ✅ Hide Nginx version
8. ✅ Implement health checks

### Scan for vulnerabilities

```bash
# Using Trivy
docker run --rm -v /var/run/docker.sock:/var/run/docker.sock \
  aquasec/trivy image realman-app:latest

# Using AWS ECR scanning
aws ecr start-image-scan \
  --repository-name realman-app \
  --image-id imageTag=latest \
  --region eu-west-1
```

---

## Additional Resources

- [AWS ECS Fargate Guide](AWS_FARGATE_SETUP.md)
- [AWS Architecture Design](AWS_ARCHITECTURE_DESIGN.md)
- [Production Deployment Checklist](PRODUCTION_DEPLOYMENT.md)
