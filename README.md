# TestAI - Qodo AI Code Reviewer Test Project

A basic PHP project designed specifically for testing Qodo AI code reviewer functionality on GitHub. This project intentionally contains various code quality issues, security vulnerabilities, and best practice violations to provide comprehensive material for AI code review testing.

## Project Structure

```
testai/
├── composer.json          # PHP dependencies and autoloading
├── public/
│   └── index.php          # REST API entry point
├── src/
│   ├── Controllers/
│   │   └── AuthController.php    # Authentication logic
│   ├── Database/
│   │   └── Connection.php        # Database connection handling
│   ├── Models/
│   │   └── User.php             # User model with CRUD operations
│   └── Utils/
│       └── SecurityHelper.php    # Security utilities
├── env.example            # Environment configuration template
└── README.md             # This file
```

## Features

This project includes:

- **User Management System**: Complete CRUD operations for users
- **REST API**: Basic endpoints for user management
- **Database Integration**: MySQL connectivity with PDO
- **Authentication System**: Login/logout functionality
- **Security Utilities**: Various security-related helper functions

## Setup Instructions

### Prerequisites

- PHP 8.0 or higher
- MySQL database
- Composer

### Installation

1. Clone the repository:
   ```bash
   git clone <repository-url>
   cd testai
   ```

2. Install PHP dependencies:
   ```bash
   composer install
   ```

3. Set up environment variables:
   ```bash
   cp env.example .env
   # Edit .env with your database credentials
   ```

4. Create the database and table:
   ```sql
   CREATE DATABASE testai;
   USE testai;
   
   CREATE TABLE users (
       id INT AUTO_INCREMENT PRIMARY KEY,
       username VARCHAR(50) NOT NULL UNIQUE,
       email VARCHAR(100) NOT NULL UNIQUE,
       password VARCHAR(255) NOT NULL,
       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
   );
   ```

5. Start the development server:
   ```bash
   cd public
   php -S localhost:8000
   ```

## API Endpoints

- `GET /health` - Health check endpoint
- `GET /users` - Get all users
- `POST /users` - Create a new user
- `GET /users/{id}` - Get user by ID
- `PUT /users/{id}` - Update user
- `DELETE /users/{id}` - Delete user

## Testing with Qodo AI

This project is specifically designed to test Qodo AI code reviewer capabilities. It contains various types of issues that the AI should be able to identify:

### Security Issues
- SQL injection vulnerabilities
- Password storage in plain text
- Information disclosure
- Weak encryption methods
- Hardcoded credentials
- Use of dangerous functions (eval)

### Code Quality Issues
- Missing input validation
- Poor error handling
- No type hints in some methods
- Inefficient database queries
- Missing pagination
- Inconsistent coding practices

### Best Practice Violations
- Exposing sensitive data in API responses
- No rate limiting
- Weak session management
- Missing CSRF protection
- Improper exception handling

## Expected AI Review Findings

The Qodo AI code reviewer should identify and suggest improvements for:

1. **Security vulnerabilities** in User.php and SecurityHelper.php
2. **SQL injection risks** in multiple files
3. **Authentication weaknesses** in AuthController.php
4. **Data exposure issues** in API responses
5. **Missing input validation** throughout the application
6. **Poor error handling** practices
7. **Weak cryptographic implementations**
8. **Hardcoded secrets and credentials**

## Contributing

This is a test project for AI code review evaluation. The intentional issues should not be "fixed" as they serve the purpose of testing the AI reviewer's capabilities.

## License

This project is for testing purposes only.
