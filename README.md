# STAR - Volunteer Management Intranet System

## Overview
STAR is a comprehensive volunteer management intranet system designed for church organizations to track volunteers through a structured 6-step journey from application to active service.

## System Architecture

### Technology Stack
- **Backend**: PHP 8.4+ with custom MVC framework
- **Database**: MySQL/MariaDB
- **Frontend**: HTML5, CSS3, JavaScript (Vanilla JS with modern features)
- **Authentication**: Session-based with role-based access control
- **Email**: PHPMailer for notifications

### STAR Journey - 6 Steps
1. **Application**: Online registration → "Aspirant STAR" status
2. **Initial Training (PCNC)**: 6-month training program
3. **MDS Interview**: Ministry validation/rejection
4. **Ministry Training**: 1-month training with mentor
5. **Mentor Report**: Favorable/unfavorable assessment
6. **Confirmation & Assignment**: Active STAR member assignment

### User Roles
- **Aspirant STAR**: Personal timeline, materials, progress tracking
- **Administrator**: Application management, step validation, system oversight
- **Mentor**: Aspirant tracking, report submission
- **MDS**: Interview management, approval/rejection
- **Pastor**: Global dashboard, statistics, metrics

## Project Structure
```
suivie_star/
├── config/           # Configuration files
├── src/             # Source code
│   ├── controllers/ # MVC Controllers
│   ├── models/      # Data models
│   ├── views/       # View templates
│   └── middleware/  # Authentication & middleware
├── public/          # Public assets (CSS, JS, images)
├── database/        # Database migrations and seeds
├── storage/         # File uploads and logs
└── vendor/          # Dependencies
```

## Features
- Interactive timeline visualization
- Role-based dashboards
- Digital forms and validations
- Step management system
- Email notification system
- Analytics and reporting
- Document management
- Responsive design

## Installation
1. Clone/setup project in MAMP htdocs
2. Configure database connection
3. Run database migrations
4. Set up email configuration
5. Access via localhost

## Quick Start

### 1. Setup Database
1. Ensure MAMP is running with MySQL
2. Visit `http://localhost:8888/suivie_star/setup.php`
3. Click "Setup Database" to create tables and seed data

### 2. Login with Demo Accounts
- **Administrator**: admin@star-church.org / password123
- **Pastor**: pastor@star-church.org / password123
- **MDS**: mds@star-church.org / password123
- **Mentor**: mentor1@star-church.org / password123
- **Aspirant**: aspirant1@example.com / password123

### 3. Test the System
- Apply as new volunteer: `http://localhost:8888/suivie_star/register`
- Login and explore role-based dashboards
- Test the 6-step STAR journey workflow

## System Features

### ✅ Completed Features
- **Database Schema**: Complete with all tables and relationships
- **User Authentication**: Role-based access control system
- **STAR Journey Tracking**: 6-step workflow with progress monitoring
- **Role-Based Dashboards**: Specialized interfaces for each user type
- **Application System**: Online registration for new aspirants
- **Ministry Management**: Track preferences and assignments
- **Responsive Design**: Mobile-friendly interface
- **Notification System**: In-app and email notifications
- **Progress Timeline**: Visual journey tracking for aspirants

### 🚧 In Development
- Email notification delivery
- Document management system
- Advanced reporting and analytics
- Mentor report submission forms
- Interview scheduling system
- File upload functionality

## User Roles & Capabilities

### Aspirant STAR
- View personal journey timeline
- Track progress through 6 steps
- Update profile information
- Access training materials
- Receive notifications

### Mentor
- View assigned aspirants
- Track mentee progress
- Submit evaluation reports
- Access training resources

### MDS (Ministry of STAR)
- Conduct interviews
- Approve/reject aspirants
- View aspirant progress
- Schedule interviews

### Administrator
- Manage all aspirants
- Validate step progressions
- Manage users and ministries
- System configuration
- Generate reports

### Pastor
- View global dashboard
- Access analytics and metrics
- Oversee entire program
- Final assignment approvals

## Development Status
✅ **Core System Complete** - Ready for testing and deployment
