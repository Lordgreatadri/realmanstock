# AWS Architecture Design - RealMan Livestock Management System

## Overview
This document outlines a cost-effective, resilient AWS architecture with minimal operational overhead for the RealMan Livestock Management System.

## Architecture Principles
- ✅ **Low Operational Overhead** - Fully managed services, minimal server management
- ✅ **Cost Effective** - Pay-per-use pricing, no over-provisioning
- ✅ **Resilient** - Multi-AZ deployment, automatic failover, data redundancy
- ✅ **Scalable** - Auto-scaling capabilities for traffic spikes
- ✅ **Secure** - VPC isolation, encryption at rest and in transit
- ✅ **Low Latency** - Hosted in optimal region for Ghana users

## AWS Region Selection for Ghana

### Option 1: Africa (Cape Town) - af-south-1 ⭐ RECOMMENDED
**Geographic Location:** South Africa (closest to Ghana)

**Pros:**
- ✅ Lowest latency for Ghana users (~80-120ms vs ~180-250ms from US/EU)
- ✅ Data sovereignty - data stays in Africa
- ✅ Best user experience for local customers
- ✅ Reduced network hops

**Cons:**
- ❌ 10-30% higher costs than US regions
- ❌ Limited service availability (no Lightsail as of 2025)
- ❌ Smaller AZ selection (3 AZs available)
- ❌ Must use EC2, cannot use Lightsail

**Service Availability:**
- ✅ EC2, RDS, ElastiCache
- ✅ S3, CloudFront
- ✅ SQS, Lambda
- ✅ SES (limited), Route 53
- ❌ Lightsail (not available)
- ✅ VPC, Auto Scaling

**Cost Impact:** ~$250-280/month (vs $211/month in US)

**Latency from Accra, Ghana:**
- To Cape Town: ~80-120ms
- To US East: ~180-250ms
- To EU Ireland: ~120-180ms

### Option 2: Europe (Ireland) - eu-west-1
**Geographic Location:** Western Europe

**Pros:**
- ✅ Full service availability (including Lightsail)
- ✅ Lower costs than Africa region
- ✅ Good connectivity to Africa
- ✅ More mature region with better support

**Cons:**
- ❌ Higher latency than Cape Town (~120-180ms)
- ❌ Data leaves Africa

**Cost Impact:** ~$211-230/month

**Latency from Accra, Ghana:**
- To Ireland: ~120-180ms
- To Frankfurt: ~140-200ms

### Option 3: US East (N. Virginia) - us-east-1
**Geographic Location:** United States

**Pros:**
- ✅ Lowest costs
- ✅ All services available
- ✅ Most documentation uses this region
- ✅ Largest service selection

**Cons:**
- ❌ Highest latency (~180-250ms)
- ❌ Furthest from users

**Cost Impact:** ~$211/month (baseline)

**Latency from Accra, Ghana:**
- To US East: ~180-250ms

### Recommended Approach: Hybrid Strategy

**Phase 1: Start in EU (Ireland)** - eu-west-1
- Use Lightsail for cost optimization
- Lower costs while building customer base
- Full service availability
- Acceptable latency (~150ms average)

**Phase 2: Migrate to Africa (Cape Town)** - af-south-1
- When customer base grows and can justify extra cost
- Use EC2 instead of Lightsail
- ~50% latency improvement
- Better user experience

**Phase 3: Multi-Region (Future)**
- Primary: af-south-1 (Africa users)
- Secondary: eu-west-1 (European users if any)
- CloudFront CDN handles global distribution

---

## Architecture Diagram

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                              END USERS                                       │
│                    (Customers, Managers, Admins)                            │
└────────────────────────────┬────────────────────────────────────────────────┘
                             │
                             │ HTTPS
                             ▼
                    ┌────────────────┐
                    │  Route 53 DNS  │ ← Custom Domain (realman.com)
                    └────────┬───────┘
                             │
                             ▼
            ┌────────────────────────────────┐
            │   AWS CloudFront (CDN)         │ ← Global edge caching
            │  - Static assets (CSS/JS/IMG)  │   SSL/TLS termination
            │  - Gzip compression            │   DDoS protection
            └────────┬───────────────────────┘
                     │
                     ▼
    ┌────────────────────────────────────────────────┐
    │  Application Load Balancer (ALB)               │ ← Health checks
    │  - HTTPS listeners                             │   SSL certificates
    │  - Multi-AZ distribution                       │   Path-based routing
    └────────┬───────────────────────┬───────────────┘
             │                       │
             ▼                       ▼
    ┌────────────────┐      ┌────────────────┐
    │  EC2 Instance  │      │  EC2 Instance  │ ← Auto Scaling Group
    │  (AZ-1a)       │      │  (AZ-1b)       │   Min: 1, Max: 4
    │  Laravel App   │      │  Laravel App   │   Target: 2 instances
    └────────┬───────┘      └────────┬───────┘
             │                       │
             └───────────┬───────────┘
                         │
         ┌───────────────┼───────────────────┐
         │               │                   │
         ▼               ▼                   ▼
┌─────────────────┐ ┌──────────────┐ ┌─────────────────┐
│   Amazon RDS    │ │ ElastiCache  │ │   Amazon SQS    │
│   (MySQL)       │ │   (Redis)    │ │  - Queue Jobs   │
│ - Multi-AZ      │ │ - Cluster    │ │  - Email Queue  │
│ - Auto Backup   │ │ - Sessions   │ │  - SMS Queue    │
│ - Read Replica  │ │ - Cache      │ │                 │
└─────────────────┘ └──────────────┘ └────────┬────────┘
                                               │
                                               ▼
                                      ┌─────────────────┐
                                      │ Lambda Workers  │ ← Process queued jobs
                                      │ - Email sender  │   Serverless execution
                                      │ - SMS sender    │   Auto-scaling
                                      │ - Processors    │
                                      └────────┬────────┘
                                               │
                         ┌─────────────────────┼────────────────────┐
                         │                     │                    │
                         ▼                     ▼                    ▼
                ┌─────────────────┐   ┌──────────────┐   ┌─────────────────┐
                │  Amazon SES     │   │ FrogSMS API  │   │   Amazon S3     │
                │  - Email Send   │   │  - SMS Send  │   │  - File Storage │
                │  - Bounce Track │   │              │   │  - Images       │
                └─────────────────┘   └──────────────┘   │  - Documents    │
                                                          │  - Backups      │
                                                          └────────┬────────┘
                                                                   │
                                                          ┌────────▼────────┐
                                                          │  CloudFront     │
                                                          │  CDN for S3     │
                                                          └─────────────────┘

┌────────────────────────────────────────────────────────────────────────────┐
│                         MONITORING & SECURITY                              │
├────────────────────────────────────────────────────────────────────────────┤
│  CloudWatch (Logs, Metrics, Alarms)  │  IAM (Access Control)              │
│  AWS Secrets Manager (Credentials)    │  VPC Security Groups               │
│  AWS Backup (Automated Backups)       │  AWS WAF (Web Application Firewall)│
└────────────────────────────────────────────────────────────────────────────┘
```

---

## Cost-Optimized Alternative: AWS Lightsail

For **maximum cost savings** and **minimal overhead**, consider AWS Lightsail:

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                              END USERS                                       │
└────────────────────────────┬────────────────────────────────────────────────┘
                             │
                             ▼
                    ┌────────────────┐
                    │  Route 53 DNS  │
                    └────────┬───────┘
                             │
                             ▼
            ┌────────────────────────────────┐
            │   CloudFront CDN               │ ← Static content
            │   + SSL Certificate            │
            └────────┬───────────────────────┘
                     │
                     ▼
    ┌────────────────────────────────────────────────┐
    │  Lightsail Load Balancer                       │ ← $18/month
    │  - SSL/TLS termination                         │   Health checks
    │  - Automatic traffic distribution              │
    └────────┬───────────────────────┬───────────────┘
             │                       │
             ▼                       ▼
    ┌────────────────┐      ┌────────────────┐
    │  Lightsail     │      │  Lightsail     │ ← $40/month each
    │  Instance 1    │      │  Instance 2    │   4GB RAM, 2 vCPU
    │  - Laravel App │      │  - Laravel App │   80GB SSD
    │  - Nginx       │      │  - Nginx       │
    └────────┬───────┘      └────────┬───────┘
             │                       │
             └───────────┬───────────┘
                         │
         ┌───────────────┼───────────────────┐
         │               │                   │
         ▼               ▼                   ▼
┌─────────────────┐ ┌──────────────┐ ┌─────────────────┐
│  RDS MySQL      │ │ ElastiCache  │ │   Amazon SQS    │
│  db.t3.small    │ │ cache.t3.micro│ │  - Free tier   │
│  $30/month      │ │ $15/month    │ │  first 1M req  │
│  Multi-AZ: +100%│ │              │ │                 │
└─────────────────┘ └──────────────┘ └────────┬────────┘
                                               │
                                               ▼
                                      ┌─────────────────┐
                                      │ Lambda Workers  │ ← $0 (free tier)
                                      └────────┬────────┘
                                               │
                         ┌─────────────────────┼────────────────┐
                         │                     │                │
                         ▼                     ▼                ▼
                ┌─────────────────┐   ┌──────────────┐ ┌──────────────┐
                │  Amazon SES     │   │ FrogSMS API  │ │  Amazon S3   │
                │  $0.10/1000     │   │              │ │  $0.023/GB   │
                └─────────────────┘   └──────────────┘ └──────────────┘

Total Monthly Cost: ~$130-160/month (depending on traffic)
```

---

## Service Breakdown

### 1. Compute Layer

#### Option A: Amazon EC2 with Auto Scaling (Recommended for Growth)
**Purpose:** Run Laravel application with automatic scaling

**Configuration:**
- **Instance Type:** t3.medium (2 vCPU, 4GB RAM)
- **Auto Scaling:** 
  - Min: 1 instance (off-peak)
  - Desired: 2 instances (normal)
  - Max: 4 instances (peak)
- **AMI:** Ubuntu 22.04 LTS
- **Storage:** 30GB GP3 EBS per instance
- **Availability Zones:** Multi-AZ (e.g., af-south-1a, af-south-1b)

**Cost Estimate (by region):**
- **US East (us-east-1):** 2 x t3.medium: ~$60/month
- **EU Ireland (eu-west-1):** 2 x t3.medium: ~$65/month (+8%)
- **Africa Cape Town (af-south-1):** 2 x t3.medium: ~$80/month (+33%)
- Data transfer: ~$10/month
- **Recommended for Ghana: EU Ireland at ~$75/month**

**Benefits:**
- ✅ Auto-scaling for traffic spikes
- ✅ Full control over environment
- ✅ Can optimize PHP/Nginx configuration

#### Option B: AWS Lightsail (Recommended for Cost Optimization)
**Purpose:** Simple, predictable pricing with less management overhead

**Configuration:**
- **Plan:** $40/month per instance (4GB RAM, 2 vCPU, 80GB SSD)
- **Instances:** 2 instances for high availability
- **Load Balancer:** $18/month
- **Static IP:** Free (5 per account)
- **Region:** EU (Ireland) or US East only - **NOT AVAILABLE in Africa (Cape Town)**

**Cost Estimate:**
- 2 x Lightsail instances: $80/month
- Load Balancer: $18/month
- **Total: ~$98/month**

**Benefits:**
- ✅ Predictable monthly cost
- ✅ Simplified management console
- ✅ Includes bandwidth (3TB/instance)
- ✅ Easy snapshots and backups

**Limitation for Ghana:**
- ⚠️ Not available in af-south-1 (Africa region)
- ⚠️ Must deploy in EU (Ireland) or US regions
- ⚠️ Higher latency from Ghana (~150ms vs ~100ms with EC2 in Cape Town)

**Recommendation:** Start with Lightsail in EU (Ireland), migrate to Fargate or EC2 in Cape Town when budget allows

#### Option C: AWS Fargate (Serverless Containers) ⭐ RECOMMENDED FOR LOW OVERHEAD
**Purpose:** Serverless container execution - zero server management

**Configuration:**
- **Service:** Amazon ECS (Elastic Container Service) with Fargate launch type
- **Container:** Laravel app in Docker container
- **Task Definition:**
  - vCPU: 0.5 vCPU per task
  - Memory: 1GB per task
- **Tasks Running:** 2 tasks (for high availability)
- **Load Balancer:** Application Load Balancer
- **Auto Scaling:** Scale 1-4 tasks based on CPU/memory
- **Region:** Available in all regions (af-south-1, eu-west-1, us-east-1)

**Cost Estimate (by region):**
- **US East (us-east-1):**
  - Fargate: 2 tasks × 0.5 vCPU × 1GB × 730hrs = ~$30/month
  - ALB: ~$21/month
  - **Total: ~$51/month**
  
- **EU Ireland (eu-west-1):**
  - Fargate: 2 tasks × 0.5 vCPU × 1GB × 730hrs = ~$33/month
  - ALB: ~$22/month
  - **Total: ~$55/month**
  
- **Africa Cape Town (af-south-1):**
  - Fargate: 2 tasks × 0.5 vCPU × 1GB × 730hrs = ~$40/month
  - ALB: ~$24/month
  - **Total: ~$64/month**

**Benefits:**
- ✅ **Zero server management** - No patching, no OS updates
- ✅ **Pay only for what you use** - Per-second billing
- ✅ **Auto-scaling built-in** - Scales up/down automatically
- ✅ **Fast deployment** - Deploy new versions in seconds
- ✅ **Available in all regions** - Including Africa (Cape Town)
- ✅ **High availability** - Tasks spread across AZs automatically
- ✅ **Container-based** - Modern DevOps practices

**Considerations:**
- ⚠️ Requires Docker containerization of Laravel app
- ⚠️ Cold start time (if scaling from 0, not recommended)
- ⚠️ More expensive than EC2 for 24/7 workloads at scale
- ⚠️ Persistent storage requires EFS or S3 (no local disk)

**Setup Requirements:**
1. Create Dockerfile for Laravel app
2. Build and push to Amazon ECR (Container Registry)
3. Create ECS cluster with Fargate
4. Define task and service
5. Configure ALB with target groups

**When to Choose Fargate:**
- ✅ Want zero operational overhead
- ✅ Team comfortable with Docker/containers
- ✅ Variable traffic patterns
- ✅ Want fastest deployment pipeline
- ✅ Don't want to manage servers at all

**Cost Comparison for Ghana (Running 2 instances/tasks 24/7):**
```
┌─────────────────┬──────────────┬───────────────┬────────────────┐
│ Option          │ EU Ireland   │ Africa (CPT)  │ Overhead       │
├─────────────────┼──────────────┼───────────────┼────────────────┤
│ Fargate ⭐      │ ~$55/month   │ ~$64/month    │ Zero (managed) │
│ Lightsail       │ ~$98/month   │ N/A           │ Minimal        │
│ EC2             │ ~$75/month   │ ~$80/month    │ High           │
└─────────────────┴──────────────┴───────────────┴────────────────┘
```

**Recommendation for Ghana:** 
- **Best Choice: Fargate in EU Ireland at ~$55/month**
- Lowest operational overhead
- Available in Africa region when you migrate
- Most cost-effective serverless option
- Modern, scalable architecture

---

### Compute Options Summary

| Feature | Fargate ⭐ | Lightsail | EC2 |
|---------|-----------|-----------|-----|
| **Operational Overhead** | None | Minimal | High |
| **Cost (EU)** | $55/month | $98/month | $75/month |
| **Africa Region** | ✅ Available | ❌ Not Available | ✅ Available |
| **Auto-scaling** | ✅ Built-in | ❌ Manual | ✅ Requires setup |
| **Container Support** | ✅ Native | ❌ No | ⚠️ DIY |
| **Patching** | ✅ Automatic | ⚠️ Manual | ❌ Manual |
| **Deployment Speed** | ✅ Seconds | ⚠️ Minutes | ❌ Minutes |
| **Learning Curve** | Medium | Easy | Hard |
| **Best For** | Modern apps | Getting started | Full control |



#### Amazon RDS for MySQL
**Purpose:** Managed relational database with automatic backups

**Configuration:**
- **Instance Class:** db.t3.small (2 vCPU, 2GB RAM)
- **Storage:**  (by region):**
- **US East:** Multi-AZ: ~$60/month
- **EU Ireland:** Multi-AZ: ~$65/month (+8%)
- **Africa Cape Town:** Multi-AZ: ~$78/month (+30%)
- Read Replica: +30-40% per region
- **Recommended for Ghana: EU Ireland at ~$65

**Cost Estimate:**
- Single-AZ: ~$30/month
- Multi-AZ: ~$60/month
- Read Replica: +$30/month (optional)
- **Recommended: Multi-AZ at ~$60/month**

**Benefits:**
- ✅ Automatic backups and point-in-time recovery
- ✅ Automatic failover (Multi-AZ)
- ✅ Automated patching and updates
- ✅ No database administration overhead

**Alternative:** 
- **Lightsail Database:** $15/month (1GB RAM, 40GB SSD) - Good for starting out
- **Aurora Serverless v2:** Pay per use, scales automatically - Good for variable workloads

---

### 3. Caching Layer

#### Amazon ElastiCache for Redis
**Purpose:** Session storage, application caching, queue backend

**Configuration:**
- **Node Type:** cache.t3.micro (0.5GB memory)
- **Nodes:** 1 primary + 1 replica
- **Cluster Mode:** Disabled (simpler setup)
- **Engine:** Redis 7.x

**Cost Estimate:**
- cache.t3.micro: ~$15/month per node
- 2 nodes (primary + replica): ~$30/month
- **Total: ~$30/month**

**Benefits:**
- ✅ Sub-millisecond latency
- ✅ Automatic failover
- ✅ Managed service (no Redis maintenance)
- ✅ Scales session storage easily

**Alternative:**
- **Database sessions:** Use MySQL for sessions (saves $30/month but slower)
- **File-based cache:** Use local storage (not recommended for multi-instance)

---

### 4. Storage Layer

#### Amazon S3
**Purpose:** Store user uploads, images, documents, backups

**Configuration:**
- **Storage Class:** S3 Standard for active files
- **Storage Class:** S3 Glacier for backups
- **Versioning:** Enabled
- **Lifecycle Policy:** Move old files to cheaper storage after 30 days
- **Bucket Policy:** Private with signed URLs

**Cost Estimate:**
- Storage: ~$0.023/GB/month
- 100GB active files: ~$2.30/month
- Requests: ~$0.50/month
- **Total: ~$3-5/month** (grows with usage)

**Benefits:**
- ✅ Unlimited scalability
- ✅ 99.999999999% durability
- ✅ Versioning and lifecycle management
- ✅ Pay only for what you use

---

### 5. Content Delivery Network (CDN)

#### Amazon CloudFront
**Purpose:** Deliver static assets globally with low latency

**Configuration:**
- **Origins:** S3 bucket + ALB/Lightsail LB
- **Price Class:** Use only North America and Europe (cheaper)
- **SSL Certificate:** Free via AWS Certificate Manager
- **Caching:** Cache static assets for 24 hours

**Cost Estimate:**
- Data transfer: ~$0.085/GB
- 100GB/month: ~$8.50/month
- Requests: ~$1/month
- **Total: ~$10/month**

**Benefits:**
- ✅ Global edge locations reduce latency
- ✅ Reduces load on origin servers
- ✅ Built-in DDoS protection
- ✅ Free SSL certificates

---

### 6. Queue & Background Jobs

#### Amazon SQS (Simple Queue Service)
**Purpose:** Queue emails, SMS, and background processing tasks

**Configuration:**
- **Queue Type:** Standard Queue
- **Message Retention:** 4 days
- **Visibility Timeout:** 300 seconds
- **Queues:** 
  - `default` - General background jobs
  - `emails` - Email notifications
  - `sms` - SMS notifications

**Cost Estimate:**
- First 1 million requests: FREE
- Beyond: $0.40 per million requests
- **Total: ~$0-2/month** (likely free tier)

**Benefits:**
- ✅ Fully managed, no server to maintain
- ✅ Scales automatically
- ✅ Reliable message delivery
- ✅ Perfect for Laravel queues

---

### 7. Email Service

#### Amazon SES (Simple Email Service)
**Purpose:** Send transactional emails (order confirmations, notifications)

**Configuration:**
- **Region:** us-east-1
- **DKIM:** Enabled for better deliverability
- **Bounce Handling:** Configured with SNS notifications
- **Sending Limit:** Request production access (50,000/day)

**Cost Estimate:**
- $0.10 per 1,000 emails
- 10,000 emails/month: $1/month
- **Total: ~$1-5/month**

**Benefits:**
- ✅ Extremely low cost
- ✅ High deliverability rates
- ✅ Scales to millions of emails
- ✅ Built-in bounce/complaint handling

---

### 8. Background Job Processing

#### AWS Lambda
**Purpose:** Process queued jobs without dedicated servers

**Configuration:**
- **Runtime:** PHP 8.2 using Bref layer
- **Memory:** 512MB
- **Timeout:** 300 seconds (5 minutes)
- **Concurrency:** 10 concurrent executions
- **Triggers:** SQS queue messages

**Functions:**
- `process-email` - Send emails via SES
- `process-sms` - Send SMS via FrogSMS
- `process-notifications` - Handle notifications
- `process-reports` - Generate reports

**Cost Estimate:**
- First 1 million requests: FREE
- Beyond: $0.20 per million requests
- Compute time: $0.0000166667 per GB-second
- **Total: ~$0-5/month** (likely free tier)

**Benefits:**
- ✅ Zero server management
- ✅ Scales automatically
- ✅ Pay only for execution time
- ✅ Perfect for queue workers

**Alternative:**
- Run queue workers on EC2/Lightsail instances (uses existing compute)

---

### 9. Load Balancing

#### Application Load Balancer (ALB) - For EC2
**Purpose:** Distribute traffic across multiple instances

**Configuration:**
- **Scheme:** Internet-facing
- **Availability Zones:** 2 AZs minimum
- **Target Groups:** EC2 instances
- **Health Checks:** Every 30 seconds
- **SSL:** Free certificate via ACM

**Cost Estimate:**
- $16/month (base)
- LCU charges: ~$5/month
- **Total: ~$21/month**

#### Lightsail Load Balancer - For Lightsail
**Purpose:** Simplified load balancer for Lightsail instances

**Cost Estimate:**
- **Fixed: $18/month**

**Benefits:**
- ✅ Simple, predictable pricing
- ✅ Integrated with Lightsail instances
- ✅ Free SSL certificate

---

### 10. Domain & DNS

#### Route 53
**Purpose:** DNS management and health checks

**Configuration:**
- **Hosted Zone:** realman.com
- **Records:** A, AAAA, CNAME for app and CDN
- **Health Checks:** Monitor endpoint availability
- **Routing Policy:** Failover or latency-based

**Cost Estimate:**
- Hosted zone: $0.50/month
- Queries: ~$0.40/month (first billion free)
- **Total: ~$1/month**

---

### 11. Monitoring & Logging

#### Amazon CloudWatch
**Purpose:** Application monitoring, logs, and alarms

**Configuration:**
- **Logs:** All Laravel logs sent to CloudWatch
- **Metrics:** CPU, Memory, Disk, Custom application metrics
- **Alarms:** 
  - High CPU usage (>80%)
  - High error rate (>5%)
  - Queue depth (>1000 messages)
- **Retention:** 7 days for logs

**Cost Estimate:**
- Logs: ~$5/month
- Metrics: ~$3/month
- Alarms: ~$2/month
- **Total: ~$10/month**

**Benefits:**
- ✅ Centralized logging
- ✅ Real-time monitoring
- ✅ Automatic alerting
- ✅ Performance insights

---

### 12. Security & Secrets

#### AWS Secrets Manager
**Purpose:** Store database passwords, API keys securely

**Configuration:**
- Secrets stored:
  - Database credentials
  - Redis password
  - AWS access keys
  - FrogSMS API key
  - Laravel APP_KEY

**Cost Estimate:**
- $0.40/secret/month
- ~5 secrets: ~$2/month
- **Total: ~$2/month**

#### AWS WAF (Web Application Firewall) - Optional
**Purpose:** Protect against common web exploits

**Cost Estimate:**
- $5/month + $1/rule/month
- **Total: ~$10/month** (optional)

---
 by Region

### Option 1: Lightsail in EU (Ireland) - RECOMMENDED START
**Best for:** Starting out, cost-conscious deployment  
**Region:** eu-west-1  
**Latency from Ghana:** ~150ms

| Service | Monthly Cost |
|---------|--------------|
| Lightsail Instances (2x $40) | $80 |
| Lightsail Load Balancer | $18 |
| RDS MySQL (db.t3.small Multi-AZ) | $65 |
| ElastiCache Redis (2 nodes) | $32 |
| S3 Storage | $5 |
| CloudFront CDN | $10 |
| SQS Queues | $0 (free tier) |
| Lambda Workers | $0 (free tier) |
| SES Email | $2 |
| Route 53 | $1 |
| CloudWatch | $10 |
| Secrets Manager | $2 |
| **TOTAL** | **~$225/month** |

### Option 2: EC2 in Africa (Cape Town) - BEST PERFORMANCE
**Best for:** Optimal Ghana user experience  
**Region:** af-south-1  
**Latency from Ghana:** ~100ms

| Service | Monthly Cost |
|---------|--------------|
| EC2 Instances (2x t3.medium) | $80 |
| Application Load Balancer | $24 |
| RDS MySQL (db.t3.small Multi-AZ) | $78 |
| ElastiCache Redis (2 nodes) | $38 |
| S3 Storage | $6 |
| CloudFront CDN | $10 |
| SQS Queues | $0 (free tier) |
| Lambda Workers | $0 (free tier) |
| SES Email (via eu-west-1) | $2 |
| Route 53 | $1 |
| CloudWatch | $12 |
| Secrets Manager | $2 |
| **TOTAL** | **~$253/month** |

**Premium:** +$28/month (+12%) for 50% better latency

### Option 3: EC2 in US East - MOST ECONOMICAL
**Best for:** Budget priority over latency  
**Region:** us-east-1  
**Latency from Ghana:** ~200ms

| Service | Monthly Cost |
|---------|--------------|
| EC2 Instances (2x t3.medium) | $70 |
| Application Load Balancer | $21 |
| RDS MySQL (db.t3.small Multi-AZ) | $60 |
| ElastiCache Redis (2 nodes) | $30 |
| S3 Storage | $5 |
| CloudFront CDN | $10 |
| SQS Queues | $0 (free tier) |
| Lambda Workers | $0 (free tier) |
| SES Email | $2 |
| Route 53 | $1 |
| CloudWatch | $10 |
| Secrets Manager | $2 |
| **TOTAL** | **~$211/month** |

### Regional Cost Comparison Summary

```
┌──────────────────┬─────────────┬──────────────┬────────────────┐
│ Region           │ Monthly Cost│ Latency (ms) │ Best For       │
├──────────────────┼─────────────┼──────────────┼────────────────┤
│ US East          │ ~$211       │ ~200         │ Lowest cost    │
│ EU Ireland ⭐    │ ~$225       │ ~150         │ Best balance   │
│ Africa Cape Town │ ~$253       │ ~100         │ Best latency   │
└──────────────────┴─────────────┴──────────────┴────────────────┘
```

### Recommended Deployment Path for Ghana Business:

**Phase 1 (Months 1-6): Start in EU Ireland**
- Deploy Lightsail-based architecture
- Cost: ~$225/month
- Latency: Acceptable at ~150ms
- Benefit: Lower costs while validating business model

**Phase 2 (Months 7-12): Migrate to Africa (Cape Town)**
- Migrate to EC2 in af-south-1
- Cost: ~$253/month (+$28)
- Latency: Excellent at ~100ms
- Benefit: Superior user experience as customer base grows

**Phase 3 (Year 2+): Optimize Further**
- Add CloudFront edge caching (already included)
- Consider multi-region if expanding beyond Ghana
- Optimize costs based on actual usage patterns

**Note:** All costs exclude:
- Data transfer overage (included: ~3TB/month)
- FrogSMS API charges (variable)
- Domain registration (~$12/year)
- AWS Support plan (optional: $29-$100/month
**Note:** Costs exclude data transfer and FrogSMS API charges (variable based on usage)

---

## Deployment Architecture Flow

### Request Flow
```
1. User → Route 53 (DNS resolution)
2. Route 53 → CloudFront (if static asset)
3. CloudFront → S3 (return static asset) OR
4. CloudFront → Load Balancer (if dynamic request)
5. Load Balancer → EC2/Lightsail instance (Laravel app)
6. Laravel app → RDS (database query)
7. Laravel app → ElastiCache (session/cache lookup)
8. Laravel app → SQS (dispatch job if needed)
9. Laravel app → Response back through CloudFront to user
```

### Background Job Flow
```
1. Laravel app → SQS (push job to queue)
2. SQS → Lambda function OR EC2 queue worker
3. Lambda/Worker → Process job
4. Job → SES (send email) OR FrogSMS (send SMS)
5. Job → RDS (update database)
6. Job → Complete and delete from SQS
```

### File Upload Flow
```
1. User uploads file → Laravel app
2. Laravel app → S3 (store file with presigned URL)
3. S3 → Return file URL
4. Laravel app → RDS (save file metadata)
5. User accesses file → CloudFront → S3 (cached delivery)
```

---

## High Availability & Disaster Recovery

### Multi-AZ Deployment
- **Application:** 2+ instances across different AZs
- **Database:** Multi-AZ RDS with automatic failover
- **Cache:** Redis replication across AZs
- **Load Balancer:** Automatically distributed across AZs

### Backup Strategy
```
┌──────────────────┬──────────────┬───────────────┬─────────────┐
│ Component        │ Backup Type  │ Frequency     │ Retention   │
├──────────────────┼──────────────┼───────────────┼─────────────┤
│ RDS Database     │ Automated    │ Daily         │ 7 days      │
│ RDS Database     │ Manual       │ Weekly        │ 30 days     │
│ S3 Files         │ Versioning   │ Continuous    │ Permanent   │
│ EC2/Lightsail    │ Snapshots    │ Weekly        │ 4 weeks     │
│ Application Code │ Git          │ Every commit  │ Permanent   │
└──────────────────┴──────────────┴───────────────┴─────────────┘
```

### Disaster Recovery Plan
1. **RDS Failure:** Automatic failover to standby instance (1-2 minutes)
2. **EC2 Instance Failure:** Auto Scaling launches new instance (3-5 minutes)
3. **AZ Failure:** Traffic routes to healthy AZ automatically
4. **Region Failure:** Restore from backups to different region (manual, 1-2 hours)

**RTO (Recovery Time Objective):** 5 minutes for most failures
**RPO (Recovery Point Objective):** < 5 minutes of data loss

---

## Security Architecture

### Network Security
```
┌───────────────────────────────────────────────────┐
│                   VPC (10.0.0.0/16)               │
│                                                   │
│  ┌──────────────────────────────────────────┐     │
│  │   Public Subnet (10.0.1.0/24) - AZ-1a    │     │
│  │   - Load Balancer                        │     │
│  │   - NAT Gateway                          │     │
│  └──────────────────────────────────────────┘     │
│                                                   │
│  ┌──────────────────────────────────────────┐     │
│  │   Public Subnet (10.0.2.0/24) - AZ-1b    │     │
│  │   - Load Balancer                        │     │
│  │   - NAT Gateway                          │     │
│  └──────────────────────────────────────────┘     │
│                                                   │
│  ┌──────────────────────────────────────────┐     │
│  │  Private Subnet (10.0.11.0/24) - AZ-1a   │     │
│  │  - EC2 Instances (Laravel app)           │     │
│  │  - Access via NAT Gateway only           │     │
│  └──────────────────────────────────────────┘     │
│                                                   │
│  ┌──────────────────────────────────────────┐     │
│  │  Private Subnet (10.0.12.0/24) - AZ-1b   │     │
│  │  - EC2 Instances (Laravel app)           │     │
│  │  - Access via NAT Gateway only           │     │
│  └──────────────────────────────────────────┘     │
│                                                   │
│  ┌──────────────────────────────────────────┐     │
│  │    Data Subnet (10.0.21.0/24) - AZ-1a    │     │
│  │    - RDS Primary                         │     │
│  │    - ElastiCache Redis                   │     │
│  │    - No internet access                  │     │
│  └──────────────────────────────────────────┘     │
│                                                   │
│  ┌──────────────────────────────────────────┐     │
│  │    Data Subnet (10.0.22.0/24) - AZ-1b    │     │
│  │    - RDS Standby                         │     │
│  │    - ElastiCache Replica                 │     │
│  │    - No internet access                  │     │
│  └──────────────────────────────────────────┘     │
└───────────────────────────────────────────────────┘
```

### Security Groups
```
┌─────────────────────┬────────────┬────────────────────┐
│ Security Group      │ Inbound    │ Source             │
├─────────────────────┼────────────┼────────────────────┤
│ ALB-SG              │ 443        │ 0.0.0.0/0          │
│ ALB-SG              │ 80         │ 0.0.0.0/0          │
│ App-SG              │ 80         │ ALB-SG             │
│ RDS-SG              │ 3306       │ App-SG             │
│ Redis-SG            │ 6379       │ App-SG             │
└─────────────────────┴────────────┴────────────────────┘
```

### Data Encryption
- ✅ **In Transit:** TLS 1.2+ for all connections
- ✅ **At Rest:** 
  - RDS: AES-256 encryption
  - S3: Server-side encryption (SSE-S3)
  - EBS: Encrypted volumes
  - ElastiCache: Encryption at rest enabled

### IAM Roles & Policies
```
┌──────────────────┬─────────────────────────────────┐
│ Role             │ Permissions                     │
├──────────────────┼─────────────────────────────────┤
│ EC2-App-Role     │ - S3 read/write                 │
│                  │ - SQS send/receive              │
│                  │ - SES send email                │
│                  │ - CloudWatch logs               │
│                  │ - Secrets Manager read          │
├──────────────────┼─────────────────────────────────┤
│ Lambda-Role      │ - SQS receive/delete            │
│                  │ - SES send email                │
│                  │ - RDS connect                   │
│                  │ - CloudWatch logs               │
├──────────────────┼─────────────────────────────────┤
│ Developer-Role   │ - EC2 read                      │
│                  │ - RDS read                      │
│                  │ - CloudWatch read               │
│                  │ - S3 read                       │
└──────────────────┴─────────────────────────────────┘
```

---

## Scaling Strategy

### Vertical Scaling (Increase instance size)
```
Low Traffic:  t3.small   → $15/month
Medium:       t3.medium  → $30/month  ← Start here
High:         t3.large   → $60/month
Very High:    t3.xlarge  → $120/month
```
 for Ghana

For **RealMan Livestock** serving Ghana customers, I recommend:

### ⭐ **Recommended: Start in EU (Ireland) - eu-west-1**

```
Region: eu-west-1 (Europe - Ireland)
Latency from Accra, Ghana: ~150ms

✅ AWS Lightsail (2 instances @ $40 each) - $80/month
✅ Lightsail Load Balancer - $18/month
✅ RDS MySQL Multi-AZ (db.t3.small) - $65/month
✅ ElastiCache Redis (2 nodes) - $32/month
✅ S3 + CloudFront - $15/month
✅ SQS + Lambda - $0 (free tier)
✅ SES Email - $2/month
✅ Route 53 + CloudWatch - $11/month
✅ Secrets Manager - $2/month

TOTAL: ~$225/month
```

**Why EU (Ireland) for Ghana?**
- ✅ Good latency (~150ms) - acceptable for web apps
- ✅ Full Lightsail availability (not in Africa region)
- ✅ Lower cost than Africa region (+$28/month more in Cape Town)
- ✅ Excellent connectivity to West Africa
- ✅ All AWS services available
- ✅ Can migrate to Africa later when budget allows

**Why NOT start in Africa (Cape Town)?**
- ❌ No Lightsail support (must use EC2 = more complex)
- ❌ 30% higher costs (~$253/month vs $225/month)
- ⚠️ Only ~50ms latency improvement (100ms vs 150ms)
- ⚠️ Not worth the extra cost/complexity initially

**Growth Path for Ghana Business:**
1. **Months 1-6:** EU Ireland (Lightsail) - $225/month, validate market
2. **Months 7-12:** Migrate to Africa (EC2) - $253/month, optimize UX
3. **Year 2+:** Add CloudFront caching, optimize costs
4. **Future:** Multi-region if expanding to other African countries

### Performance Comparison from Accra, Ghana:

```
┌─────────────────┬──────────────┬───────────────────────────┐
│ AWS Region      │ Latency (RTT)│ User Experience           │
├─────────────────┼──────────────┼───────────────────────────┤
│ af-south-1      │ ~80-120ms    │ Excellent ⭐⭐⭐⭐⭐        │
│ eu-west-1       │ ~120-180ms   │ Very Good ⭐⭐⭐⭐ (START)  │
│ eu-central-1    │ ~140-200ms   │ Good ⭐⭐⭐               │
│ us-east-1       │ ~180-250ms   │ Acceptable ⭐⭐           │
│ ap-south-1      │ ~250-350ms   │ Slow ⭐                   │
└─────────────────┴──────────────┴───────────────────────────┘
```

**Reality Check:**
- 150ms feels instant for most web applications
- CloudFront CDN caches static content at edge (20-50ms for images/CSS/JS)
- Only API calls experience the full latency
- Most users won't notice difference between 100ms and 150m

### Phase 2: Add Resilience (Week 2)
1. ✅ Enable RDS Multi-AZ
2. ✅ Add second application instance
3. ✅ Set up ElastiCache Redis
4. ✅ Configure Auto Scaling
5. ✅ Implement backups

### Phase 3: Optimize & Monitor (Week 3)
1. ✅ Set up SQS queues
2. ✅ Deploy Lambda workers OR queue workers
3. ✅ Configure CloudWatch monitoring
4. ✅ Set up alarms and notifications
5. ✅ Enable AWS WAF (optional)

### Phase 4: Production Hardening (Week 4)
1. ✅ Security audit
2. ✅ Performance testing
3. ✅ Disaster recovery testing
4. ✅ Documentation
5. ✅ Team training

---

## Recommended Starting Architecture

For **RealMan Livestock**, I recommend starting with:

```
✅ AWS Lightsail (2 instances) - $80/month
✅ Lightsail Load Balancer - $18/month
✅ RDS MySQL Multi-AZ (db.t3.small) - $60/month
✅ ElastiCache Redis (single node) - $15/month
✅ S3 + CloudFront - $15/month
✅ SQS + Lambda - $0 (free tier)
✅ SES Email - $2/month
✅ Route 53 + CloudWatch - $11/month

TOTAL: ~$201/month
```

**Why this configuration?**
- ✅ Low operational overhead (fully managed services)
- ✅ Cost-effective (~$200/month for production-grade)
- ✅ Highly resilient (multi-AZ, auto-failover)
- ✅ Scalable (can upgrade incrementally)
- ✅ Simple to manage (Lightsail console is beginner-friendly)

**Growth Path:**
1. Start with this architecture
2. When traffic grows, migrate from Lightsail to EC2 Auto Scaling
3. Add read replicas when database becomes bottleneck
4. Consider Aurora Serverless for unpredictable workloads
5. Add CloudFront for international users

---

## Next Steps

1. **Review Architecture** - Confirm this meets business requirements
2. **Set AWS Budget** - Configure billing alerts at $250/month
3. **Create AWS Account** - Use Organizations for multi-account setup
4. **Follow Deployment Guide** - See PRODUCTION_DEPLOYMENT.md
5. **Run Cost Calculator** - https://calculator.aws/ for exact estimates
6. **Schedule Training** - AWS fundamentals for the team

---

## Questions & Support

- **AWS Support Plan:** Start with Basic (free), upgrade to Developer ($29/month) if needed
- **Architecture Review:** Schedule AWS Well-Architected Review (free)
- **Cost Optimization:** Enable AWS Cost Explorer and Trusted Advisor

This architecture provides a solid foundation for the RealMan Livestock Management System with room to grow as the business scales! 🚀
