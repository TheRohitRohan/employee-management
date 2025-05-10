# Employee Management System

A modern, secure, and user-friendly Employee Management System built with PHP, MySQL, and Bootstrap 5.

## Features

- 🔐 Secure Authentication System
- 👥 Complete Employee Management
  - Add new employees
  - Edit employee details
  - Delete employees
  - View employee list
- 🔍 Advanced Search & Filtering
  - Search by name, email, or phone
  - Filter by department
  - Filter by status
- 📱 Responsive Design
  - Works on desktop and mobile devices
  - Clean and modern UI
- 🔒 Security Features
  - CSRF Protection
  - SQL Injection Prevention
  - XSS Protection
  - Secure Password Handling
- 📊 Dashboard with Statistics
- 📄 Pagination for Large Datasets

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web Server (Apache/Nginx)
- Modern Web Browser

## Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/TheRohitRohan/employee-management.git
   ```

2. Set up your web server to point to the project directory

3. Create a MySQL database and import the database schema:
   ```sql
   CREATE DATABASE employee_management;
   USE employee_management;
   ```

4. Configure the database connection:
   - Open `includes/config.php`
   - Update the database credentials:
     ```php
     define('DB_HOST', 'localhost');
     define('DB_NAME', 'employee_management');
     define('DB_USER', 'your_username');
     define('DB_PASS', 'your_password');
     ```

5. Set up the admin account:
   - Default credentials:
     - Username: admin
     - Password: admin123
   - Change these credentials after first login

## Directory Structure

```
├── ajax/                  # AJAX handlers
├── assets/               # Static assets
│   ├── css/             # Stylesheets
│   └── js/              # JavaScript files
├── includes/            # PHP includes
│   ├── config.php      # Configuration
│   ├── db.php          # Database connection
│   └── functions.php   # Helper functions
├── add_employee.php     # Add new employee
├── edit_employee.php    # Edit employee
├── employees.php        # Employee listing
├── login.php           # Login page
└── README.md           # This file
```

## Security Features

### CSRF Protection
The system implements CSRF (Cross-Site Request Forgery) protection:
- Each form includes a unique CSRF token
- Tokens are validated on the server side
- Prevents unauthorized form submissions

### SQL Injection Prevention
- Uses PDO with prepared statements
- Input sanitization
- Parameterized queries

### XSS Protection
- Output escaping
- Input validation
- Content Security Policy

## Usage

1. **Login**
   - Access the system through `login.php`
   - Use the default admin credentials

2. **Employee Management**
   - View all employees on the main page
   - Use the search and filter options
   - Add new employees using the "Add Employee" button
   - Edit or delete employees using the action buttons

3. **Search & Filter**
   - Search by name, email, or phone
   - Filter by department
   - Filter by status (active/inactive)

## Contributing

1. Fork the repository
2. Create your feature branch
3. Commit your changes
4. Push to the branch
5. Create a new Pull Request


## Acknowledgments

- Bootstrap 5 for the UI framework
- jQuery for AJAX functionality
- SweetAlert2 for beautiful alerts
- Font Awesome for icons 
