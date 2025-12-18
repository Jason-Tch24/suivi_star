# STAR System - Administrator Guide

## System Administration

### Initial Setup
1. **Database Configuration**: Ensure MySQL is running and accessible
2. **Environment Setup**: Configure `.env` file with correct database credentials
3. **Database Installation**: Run setup.php to create tables and seed initial data
4. **User Account Creation**: Set up initial administrator accounts
5. **Google Authentication** (Optional): Configure Google OAuth for user registration and login

### Google Authentication Setup

#### Prerequisites
- Google Cloud Console account
- Composer installed on your system

#### Installation Steps

1. **Install Dependencies**
   ```bash
   php setup-google-auth.php
   ```
   Or manually:
   ```bash
   composer install
   ```

2. **Configure Google Cloud Console**
   - Go to https://console.cloud.google.com/
   - Create or select a project
   - Enable Google+ API
   - Create OAuth 2.0 credentials
   - Add authorized origins: `http://localhost` (or your domain)
   - Add redirect URI: `http://localhost/suivie_star/auth/google/callback.php`

3. **Update Environment Configuration**
   Add to your `.env` file:
   ```env
   GOOGLE_CLIENT_ID=your_google_client_id_here
   GOOGLE_CLIENT_SECRET=your_google_client_secret_here
   ```

4. **Test Implementation**
   Visit: `http://localhost/suivie_star/test-google-auth.php`

#### Features Added
- **Quick Registration**: Users can register instantly with Google
- **Secure Login**: OAuth 2.0 authentication via Google
- **Profile Integration**: Google profile pictures and information
- **Unified User Management**: Google and local accounts in same system

### User Management

#### Creating Users
```php
// Example: Creating a new mentor
$userModel = new User();
$userId = $userModel->create([
    'email' => 'mentor@church.org',
    'password' => 'secure_password',
    'first_name' => 'John',
    'last_name' => 'Doe',
    'phone' => '+1234567890',
    'role' => 'mentor',
    'status' => 'active'
]);
```

#### User Roles and Permissions
- **aspirant**: Can view own progress, update profile
- **mentor**: Can manage assigned aspirants, submit reports
- **mds**: Can conduct interviews, approve/reject aspirants
- **administrator**: Full system access, user management
- **pastor**: Dashboard analytics, program oversight

### Step Management

#### Validating Step Progression
1. Navigate to Admin Dashboard
2. Review "Step Progress Overview"
3. Click "Manage" for specific step
4. Review aspirant details
5. Approve or reject progression

#### Manual Step Advancement
```php
$aspirantModel = new Aspirant();
$aspirantModel->advanceToNextStep($aspirantId, $validatorId);
```

### Ministry Management

#### Adding New Ministries
1. Access Admin Dashboard
2. Navigate to "Manage Ministries"
3. Click "Add New Ministry"
4. Fill in ministry details:
   - Name
   - Description
   - Coordinator (optional)
   - Status (active/inactive)

#### Assigning Coordinators
```php
$ministryModel = new Ministry();
$ministryModel->update($ministryId, [
    'coordinator_id' => $userId
]);
```

### Notification System

#### Sending System Announcements
```php
$notificationModel = new Notification();
$notificationModel->sendAnnouncement(
    'System Maintenance',
    'The system will be down for maintenance on Sunday at 2 AM.',
    ['aspirant'] // Exclude aspirants from this notification
);
```

#### Automated Notifications
The system automatically sends notifications for:
- Step progressions
- Mentor assignments
- Deadline reminders
- Interview scheduling

### Reporting and Analytics

#### Available Reports
1. **Aspirant Progress Report**: Track individual and group progress
2. **Ministry Performance Report**: Analyze ministry effectiveness
3. **Step Completion Report**: Monitor bottlenecks in the journey
4. **User Activity Report**: Track system usage

#### Generating Custom Reports
```php
// Example: Get aspirants by step
$journeyStepModel = new JourneyStep();
$aspirantsAtStep3 = $journeyStepModel->getAspirantsAtStep(3);

// Example: Get ministry statistics
$ministryModel = new Ministry();
$stats = $ministryModel->getStatistics();
```

### System Maintenance

#### Database Backup
```bash
# Create backup
mysqldump -u username -p star_volunteer_system > backup_$(date +%Y%m%d).sql

# Restore backup
mysql -u username -p star_volunteer_system < backup_20250902.sql
```

#### Log Management
- Application logs: `storage/logs/star.log`
- Error logs: Check server error logs
- Email logs: `email_logs` table in database

#### Performance Monitoring
- Monitor database query performance
- Check server resource usage
- Review user activity patterns
- Monitor email delivery rates

### Security Configuration

#### Password Policies
- Minimum 8 characters
- Require uppercase, lowercase, numbers
- Password expiration (recommended: 90 days)
- Account lockout after failed attempts

#### Session Management
- Session timeout: 120 minutes (configurable)
- Secure session cookies
- CSRF protection enabled
- XSS protection headers

#### Data Protection
- Encrypt sensitive data
- Regular security audits
- Access logging
- Data retention policies

### Troubleshooting

#### Common Issues

**Database Connection Errors**:
1. Check MySQL service status
2. Verify database credentials in `.env`
3. Ensure database exists
4. Check user permissions

**Email Delivery Issues**:
1. Verify SMTP settings
2. Check email logs table
3. Test with external email service
4. Review spam filters

**Performance Issues**:
1. Check database indexes
2. Monitor server resources
3. Review slow query log
4. Optimize large data operations

**User Access Issues**:
1. Verify user role and status
2. Check session configuration
3. Clear browser cache
4. Review access logs

### System Configuration

#### Environment Variables
```env
# Application
APP_NAME="STAR Volunteer Management System"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# Database
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=star_volunteer_system
DB_USERNAME=star_user
DB_PASSWORD=secure_password

# Email
MAIL_DRIVER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
```

#### System Settings
Access via Admin Dashboard → System Settings:
- PCNC training duration
- Ministry training duration
- Step deadlines
- Email notification preferences
- File upload limits

### Backup and Recovery

#### Regular Backup Schedule
1. **Daily**: Database backup
2. **Weekly**: Full system backup
3. **Monthly**: Archive old backups
4. **Quarterly**: Disaster recovery test

#### Recovery Procedures
1. **Database Recovery**: Restore from latest backup
2. **File Recovery**: Restore uploaded files and documents
3. **Configuration Recovery**: Restore environment settings
4. **User Communication**: Notify users of any data loss

### Monitoring and Alerts

#### Key Metrics to Monitor
- User login frequency
- Step completion rates
- System error rates
- Email delivery success
- Database performance

#### Alert Thresholds
- Failed login attempts > 10/hour
- Database response time > 2 seconds
- Email delivery failure rate > 5%
- Disk space usage > 80%

### Integration Guidelines

#### Adding New Features
1. Follow MVC architecture
2. Use existing database models
3. Implement proper authentication
4. Add appropriate logging
5. Update user documentation

#### API Development
For future API integration:
1. Use RESTful conventions
2. Implement proper authentication
3. Add rate limiting
4. Document endpoints
5. Version API appropriately

### Support and Maintenance

#### Regular Tasks
- **Daily**: Review error logs, check system status
- **Weekly**: User account review, backup verification
- **Monthly**: Performance analysis, security review
- **Quarterly**: System updates, feature planning

#### Emergency Procedures
1. **System Down**: Check server status, review logs
2. **Data Breach**: Secure system, notify users, investigate
3. **Performance Issues**: Scale resources, optimize queries
4. **User Issues**: Provide immediate support, document solutions
