# Deployment Guide for Employee Management System

## Prerequisites
1. AWS Account
2. AWS CLI installed
3. SSH client
4. Basic knowledge of Linux commands

## Step 1: Launch EC2 Instance
1. Log in to AWS Console
2. Go to EC2 Dashboard
3. Click "Launch Instance"
4. Choose Amazon Linux 2023 AMI
5. Select t2.micro (free tier eligible)
6. Configure Security Group:
   - SSH (Port 22)
   - HTTP (Port 80)
   - HTTPS (Port 443)
7. Create or select an existing key pair
8. Launch instance

## Step 2: Connect to EC2 Instance
```bash
ssh -i your-key.pem ec2-user@your-instance-ip
```

## Step 3: Install LAMP Stack
```bash
# Update system
sudo yum update -y

# Install Apache
sudo yum install httpd -y
sudo systemctl start httpd
sudo systemctl enable httpd

# Install PHP and required extensions
sudo yum install php php-mysqlnd php-json php-xml php-mbstring php-curl php-gd -y

# Install MySQL
sudo yum install mysql-server -y
sudo systemctl start mysqld
sudo systemctl enable mysqld

# Secure MySQL installation
sudo mysql_secure_installation
```

## Step 4: Configure Apache
```bash
# Create project directory
sudo mkdir -p /var/www/html/employee-management

# Set permissions
sudo chown -R ec2-user:ec2-user /var/www/html/employee-management
```

## Step 5: Deploy Application
1. Clone repository:
```bash
cd /var/www/html/employee-management
git clone https://github.com/TheRohitRohan/employee-management.git .
```

2. Set up environment variables:
```bash
sudo nano /etc/environment
```
Add the following:
```
DB_HOST=localhost
DB_NAME=employee_management
DB_USER=your_db_user
DB_PASS=your_db_password
BASE_URL=http://your-domain-or-ip
```

3. Import database:
```bash
mysql -u root -p
CREATE DATABASE employee_management;
USE employee_management;
source database/database.sql;
```

## Step 6: Configure Apache Virtual Host
```bash
sudo nano /etc/httpd/conf.d/employee-management.conf
```
Add:
```apache
<VirtualHost *:80>
    ServerName your-domain-or-ip
    DocumentRoot /var/www/html/employee-management
    
    <Directory /var/www/html/employee-management>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
```

## Step 7: Set Up SSL (Optional but Recommended)
1. Install Certbot:
```bash
sudo yum install certbot python3-certbot-apache -y
```

2. Obtain SSL certificate:
```bash
sudo certbot --apache -d your-domain.com
```

## Step 8: Final Configuration
1. Set proper permissions:
```bash
sudo chown -R apache:apache /var/www/html/employee-management
sudo chmod -R 755 /var/www/html/employee-management
```

2. Restart Apache:
```bash
sudo systemctl restart httpd
```

## Step 9: Security Considerations
1. Configure firewall:
```bash
sudo yum install firewalld -y
sudo systemctl start firewalld
sudo systemctl enable firewalld
sudo firewall-cmd --permanent --add-service=http
sudo firewall-cmd --permanent --add-service=https
sudo firewall-cmd --reload
```

2. Set up regular backups:
```bash
# Create backup script
sudo nano /usr/local/bin/backup.sh
```
Add:
```bash
#!/bin/bash
BACKUP_DIR="/var/backups/employee-management"
mkdir -p $BACKUP_DIR
mysqldump -u root -p employee_management > $BACKUP_DIR/db_backup_$(date +%Y%m%d).sql
tar -czf $BACKUP_DIR/files_backup_$(date +%Y%m%d).tar.gz /var/www/html/employee-management
```

3. Set up cron job for backups:
```bash
sudo crontab -e
```
Add:
```
0 2 * * * /usr/local/bin/backup.sh
```

## Monitoring and Maintenance
1. Set up CloudWatch for monitoring
2. Configure automatic updates
3. Set up log rotation
4. Monitor disk space usage

## Troubleshooting
1. Check Apache error logs:
```bash
sudo tail -f /var/log/httpd/error_log
```

2. Check PHP error logs:
```bash
sudo tail -f /var/log/php_errors.log
```

3. Check MySQL error logs:
```bash
sudo tail -f /var/log/mysqld.log
```

## Backup and Restore
1. Database backup:
```bash
mysqldump -u root -p employee_management > backup.sql
```

2. Files backup:
```bash
tar -czf employee-management-backup.tar.gz /var/www/html/employee-management
```

3. Restore database:
```bash
mysql -u root -p employee_management < backup.sql
```

4. Restore files:
```bash
tar -xzf employee-management-backup.tar.gz -C /var/www/html/
``` 