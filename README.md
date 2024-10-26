# Task Management API

A RESTful API built with Laravel for managing tasks. This API allows users to create, read, update, and delete tasks with filtering capabilities.

## Features

- CRUD operations for tasks
- Filter tasks by status and due date
- Input validation
- Clean architecture following SOLID principles
- Comprehensive test suite
- API documentation using Scribe

## Requirements

- PHP >= 8.1
- Composer
- MySQL/PostgreSQL
- Laravel 10.x

## Installation

1. Clone the repository
```bash
git clone https://github.com/abdelwahab313/task-management-api.git
cd task-management-api
```

2. Configure environment variables
```bash
cp .env.example .env
```

3. Install dependencies and setup docker containers
```bash
make setup
```
the API server should be up and running at http://localhost:8080

4. Run migrations
```bash
php artisan migrate
```


## Running Tests

Run the test suite using PHPUnit:
```bash
php artisan test
```



## API Endpoints

### Create Task
```
POST /api/tasks
```
Parameters:
- `title` (required, string) - Task title
- `description` (string) - Task description
- `status` (required, enum) - One of: pending, in_progress, completed
- `due_date` (required, date) - Future date

### List Tasks
```
GET /api/tasks
```
Query Parameters:
- `title` (optional) - Filter by title
- `status` (optional) - Filter by status
- `due_date` (optional) - Filter by due date
- `page` (optional) - Page number
- `per_page` (optional) - Number of tasks per page


### Update Task
```
PUT /api/tasks
```
Query Parameters:
- `id` (required) - Task ID
- `title` (optional) - New title
- `description` (optional) - New description
- `status` (optional) - New status
- `due_date` (optional) - New due date

### Delete Task
```
DELETE /api/tasks
```
Query Parameters:
- `id` (required) - Task ID

## Error Handling

The API returns appropriate HTTP status codes:
- 200: Success
- 201: Created
- 400: Bad Request
- 404: Not Found
- 422: Validation Error
- 500: Server Error


## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

