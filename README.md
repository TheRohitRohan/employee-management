# Employee Management System

A web-based Employee Management System built with PHP, MySQL, and Bootstrap. This system allows administrators to manage employee records, including adding, editing, and deleting employee information.

## Features

- User Authentication
- Employee Management (CRUD operations)
- Search and Filter functionality
- Pagination
- Responsive Design
- AJAX-powered operations
- CSRF Protection
- Input Validation

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- XAMPP/WAMP/MAMP (for local development)

## Installation

1. Clone the repository:
```bash
git clone https://github.com/yourusername/employee-management.git
```

2. Create a MySQL database named `employee_management`

3. Import the database schema:
```bash
mysql -u root -p employee_management < database.sql
```

4. Configure the database connection in `includes/config.php`:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'employee_management');
define('DB_USER', 'root');
define('DB_PASS', '');
```

5. Place the project in your web server's document root (e.g., `htdocs` for XAMPP)

6. Access the application through your web browser:
```
http://localhost/Task1
```

## Default Login Credentials

- Username: admin
- Password: admin123

## Directory Structure

```
├── ajax/                  # AJAX handlers
├── assets/               # Static assets
│   ├── css/             # Stylesheets
│   └── js/              # JavaScript files
├── includes/            # PHP includes
│   ├── config.php      # Configuration
│   ├── db.php          # Database class
│   ├── functions.php   # Helper functions
│   └── sidebar.php     # Navigation sidebar
├── database.sql        # Database schema
├── index.php          # Entry point
└── README.md          # Documentation
```

## Security Features

- Password Hashing
- CSRF Protection
- Input Sanitization
- Prepared Statements
- Session Management

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the LICENSE file for details. 