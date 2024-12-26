# Task Management System

## Overview

This project is a Task Management System designed to challenge the technical skills of candidates in PHP, Laravel, PostgreSQL, Redis, and Docker. The project focuses solely on APIs and backend functionality, evaluating the candidate’s ability to implement secure, well-structured APIs, handle role-based permissions, and work with modern backend technologies.

## Features

1. **User Authentication and Authorization**
   - Secure user registration and authentication using Laravel Passport.
   - Define roles (e.g., admin, regular user) and permissions.
   - Check if users have access to specific tasks or projects based on roles and permissions.

2. **Task Management**
   - Users can create tasks with a title, description, assigned user(s), and due date.
   - Tasks can be marked as completed or pending.
   - Users can update task details, mark tasks as completed, or delete tasks.

3. **RESTful API**
   - Secure API routes for tasks, users, and authentication.
   - Include CRUD operations for tasks and users.
   - Secure endpoints with proper authentication.
   - The task list endpoint includes robust filtering and searching functionalities.

4. **Database Design**
   - Design a PostgreSQL database schema to store user information and task details using Laravel Migrations.
   - Utilize relationships between users and tasks.

5. **Caching**
   - Use Redis to cache frequently accessed data, such as task lists or user information.

6. **Middleware for Permissions and Roles**
   - Implement middleware to check if users have the necessary roles and permissions to access or modify tasks.
   - Ensure only authorized users can perform actions like creating, updating, or deleting their own tasks.

7. **Security Measures**
   - Use Laravel's built-in password hashing for user authentication.
   - Validate and sanitize all user inputs to prevent cross-site scripting (XSS).
   - Implement logging mechanisms to track and audit user actions, especially for sensitive operations.

8. **Dockerization (Optional)**
   - Dockerize the application for easy deployment and scalability.
   - Use Docker Compose to manage application services (Laravel, PostgreSQL, Redis).

## Docker Setup

To build and run the application using Docker and Docker Compose, follow these steps:

### Step 1: Build the Docker Images

Run the following command to build the Docker images:

```sh
docker-compose build
```

### Step 2: Start the Docker Containers
Run the following command to start the Docker containers:
```sh
docker-compose up -d
```

### Step 3: Run Migrations and Seeders
After starting the containers, you need to run the migrations and seeders to set up the database schema and initial data. Run the following commands:
```sh
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed
```


## Authentication Service

### Registration

Endpoint: `POST /api/register`

Request Body:
```json
{
    "name": "John Doe",
    "email": "john.doe@example.com",
    "password": "password",
    "password_confirmation": "password"
}
```

Response:
```json
{
    "token": "access_token"
}
```

### Login

Endpoint: `POST /api/login`

Request Body:
```json
{
    "email": "john.doe@example.com",
    "password": "password"
}
```

Response:
```json
{
    "token": "access_token"
}
```

## Task Service

### List Tasks

Endpoint: `GET /api/tasks`

Response:
```json
{
    "data": [
        {
            "id": 1,
            "title": "Task 1",
            "description": "Description for Task 1",
            "due_date": "2023-12-31",
            "user_id": 1,
            "status": "pending",
            "created_at": "2023-01-01T00:00:00.000000Z",
            "updated_at": "2023-01-01T00:00:00.000000Z"
        },
        // More tasks...
    ],
    "links": {
        "first": "http://example.com/api/tasks?page=1",
        "last": "http://example.com/api/tasks?page=1",
        "prev": null,
        "next": null
    },
    "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 1,
        "path": "http://example.com/api/tasks",
        "per_page": 15,
        "to": 1,
        "total": 1
    }
}
```

### Create Task

Endpoint: `POST /api/tasks`

Request Body:
```json
{
    "title": "New Task",
    "description": "Description for the new task",
    "due_date": "2023-12-31"
}
```

Response:
```json
{
    "message": "Task created successfully",
    "task": {
        "id": 1,
        "title": "New Task",
        "description": "Description for the new task",
        "due_date": "2023-12-31",
        "user_id": 1,
        "status": "pending",
        "created_at": "2023-01-01T00:00:00.000000Z",
        "updated_at": "2023-01-01T00:00:00.000000Z"
    }
}
```

### View Task

Endpoint: `GET /api/tasks/{id}`

Response:
```json
{
    "success": true,
    "data": {
        "id": 1,
        "title": "Task 1",
        "description": "Description for Task 1",
        "due_date": "2023-12-31",
        "user_id": 1,
        "status": "pending",
        "created_at": "2023-01-01T00:00:00.000000Z",
        "updated_at": "2023-01-01T00:00:00.000000Z"
    }
}
```

### Update Task

Endpoint: `PUT /api/tasks/{id}`

Request Body:
```json
{
    "title": "Updated Task",
    "description": "Updated description for the task",
    "due_date": "2023-12-31",
    "status": "completed"
}
```

Response:
```json
{
    "message": "Task updated successfully",
    "task": {
        "id": 1,
        "title": "Updated Task",
        "description": "Updated description for the task",
        "due_date": "2023-12-31",
        "user_id": 1,
        "status": "completed",
        "created_at": "2023-01-01T00:00:00.000000Z",
        "updated_at": "2023-01-01T00:00:00.000000Z"
    }
}
```

### Delete Task

Endpoint: `DELETE /api/tasks/{id}`

Response:
```json
{
    "message": "Task deleted successfully"
}
```

### Mark Task as Completed

Endpoint: `PATCH /api/tasks/{id}/complete`

Response:
```json
{
    "success": true,
    "message": "Task marked as completed"
}
```

### Mark Task as Pending

Endpoint: `PATCH /api/tasks/{id}/pending`

Response:
```json
{
    "success": true,
    "message": "Task marked as pending"
}
```

## Running Tests

To run the tests, use the following command:

```sh
php artisan test
```

This will execute all the feature and unit tests to ensure that the application is working correctly.

## Conclusion

This documentation provides an overview of the authentication and task services, including the endpoints, request and response formats, and how to run the tests. The Task Management System is designed to be secure, well-structured, and easy to extend with additional features.