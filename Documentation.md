# Gemvc Framework Documentation
**Version:** 5.9.14  
**Author:** Ali Khorsandfard (ali.khorsandfard@gmail.com)  
**License:** MIT

## Table of Contents
1. [Installation](#installation)
2. [Getting Started](#getting-started)
3. [Core Components](#core-components)
4. [Database Operations](#database-operations)
5. [Services](#services)
6. [Authentication](#authentication)
7. [Traits](#traits)
8. [Error Handling](#error-handling)
9. [Best Practices](#best-practices)
10. [Environment Configuration](#environment-configuration)
11. [Support & Contributing](#support-&-contributing)
12. [Library Integration](#library-integration)

## Installation

### Requirements
- PHP >= 7.4
- MySQL/MariaDB
- Composer

### Quick Start
```bash
composer create-project gemvc/installer [your_project_name]
cd [your_project_name]
php -S localhost:8000 -t public
```

### Application Directory Structure
your_project_name/
├── app/
│   ├── api/              # API Services (extends ApiService/AuthService)
│   ├── controller/       # Business Logic
│   ├── model/           # Data Logic
│   ├── table/           # Database Operations
│   └── .env             # Environment config
├── vendor/
│   ├── gemvc/framework/
│   │   └── src/
│   │       ├── core/    # Framework core
│   │       └── traits/  # Framework traits
│   └── gemvc/library/   # GEMVC Library
├── composer.json
└── index.php

### Framework Directory Structure

1. **Application Structure**

## License
The Gemvc Framework is open-sourced software licensed under the MIT license.

# GEMVC Framework Documentation

## Table of Contents
1. [Architecture Overview](#architecture-overview)
2. [Core Components](#core-components)
   - [Bootstrap](#bootstrap)
   - [Request Class](#request-class)
   - [ApiService](#apiservice)
   - [AuthService](#authservice)
3. [Framework Layers](#framework-layers)
   - [Service Layer](#service-layer)
   - [Controller Layer](#controller-layer)
   - [Model Layer](#model-layer)
   - [Table Layer](#table-layer)
   - [Database Layer](#database-layer)
4. [Request Lifecycle](#request-lifecycle)
5. [Authentication & Authorization](#authentication--authorization)
6. [Request Handling](#request-handling)
7. [Documentation System](#documentation-system)
8. [Error Handling](#error-handling)
9. [Best Practices](#best-practices)
10. [Additional Core Features](#additional-core-features)
11. [Environment Configuration](#environment-configuration)
12. [Support & Contributing](#support-&-contributing)

## Architecture Overview

GEMVC follows a clean architecture pattern with bidirectional flow:

Incoming Request Flow:
```
Frontend Request → index.php → Bootstrap + Request → Service → Controller → Model → Table → Database
```

Response Flow:
```
Database → Table → Model → Controller → Service → Response
```

The Model layer can return different types based on the scenario:
- JsonResponse (direct API response)
- Objects (for further processing)
- Simple types (bool, int, string)
- Static method returns

## Core Components

### Bootstrap
The entry point and request lifecycle manager:
```php
// index.php
$request = new ApacheRequest();  // or SwooleRequest
$bootstrap = new Bootstrap($request);
```

Bootstrap handles:
- URL parsing
- Service resolution
- Method execution
- Response validation

### Request Class
Manages all incoming HTTP requests:
- Input validation
- Request sanitization
- JWT handling
- Filtering & sorting
- Pagination support

### ApiService
Base class for all services:
```php
class ApiService {
    protected Request $request;
    
    protected function validatePosts(array $schema): void {
        // Validation logic
    }
}
```

### AuthService
Extended base class for authenticated services:
```php
class AuthService extends ApiService {
    protected Auth $auth;
    protected int $company_id;
    protected int $user_id;
}
```

## Request Lifecycle

1. **Request Initialization**
   - Server-specific request creation (Apache/Swoole)
   - Request normalization

2. **Bootstrap Process**
   - URL parsing
   - Service resolution
   - Authentication verification
   - Method execution

3. **Service Processing**
   - Authorization checks
   - Input validation
   - Business logic execution
   - Response generation

## Service Layer

### Public Services
For unauthenticated endpoints:
```php
class PublicService extends ApiService {
    public function login(): JsonResponse {
        $this->validatePosts(['email' => 'email', 'password' => 'string']);
        // Login logic
    }
}
```

### Protected Services
For authenticated endpoints:
```php
class SecureService extends ApiService {
    public function create(): JsonResponse {
        $this->auth->authorize(['specific-role']);
        $this->validatePosts(['field' => 'type']);
        return (new Controller($this->request))->create();
    }
}
```

## Controller Layer

### Architectural Principle
In GEMVC, Controllers should ONLY be called by the Service (API) Layer. This strict architectural rule ensures:

1. **Single Entry Point**
```php
// ✅ CORRECT: Controller called from Service
class UserService extends ApiService {
    public function create(): JsonResponse {
        return (new UserController($this->request))->create();
    }
}

// ❌ WRONG: Never call controllers directly or from other layers
$controller = new UserController($request);  // Don't do this!
```

2. **Clean Architecture Benefits**
- **Single Responsibility**: Services handle API concerns, Controllers handle business logic
- **Separation of Concerns**: Clear boundaries between layers
- **Maintainable Code**: Predictable flow of data and control
- **Security**: All requests go through proper authorization and validation
- **Consistent Error Handling**: Standardized error responses
- **Documentation**: Clear API endpoints through services

3. **Flow of Control**
```
Request → Service (API Layer) → Controller → Response
           ↓
    - Authentication
    - Authorization
    - Input Validation
    - Request Sanitization
```

### Decoupled Architecture
GEMVC implements a decoupled controller pattern where any Service can use any Controller. This design provides several benefits:

1. **Clean URL Patterns**
```php
// Different URLs can use same controller
/api/classroom/create    →  ClassroomService → ClassroomController
/api/teacher/classrooms  →  TeacherService  → ClassroomController
```

2. **Controller Reusability**
```php
// ClassroomService
class ClassroomService extends AuthService {
    public function create(): JsonResponse {
        $this->auth->authorize(['admin']);
        return (new ClassroomController($this->request))->create();
    }
}

// TeacherService
class TeacherService extends AuthService {
    public function classrooms(): JsonResponse {
        $this->auth->authorize(['teacher']);
        // Reuse ClassroomController for teacher's classrooms
        return (new ClassroomController($this->request))->list();
    }
}
```

### Key Benefits
- **Clean URLs**: Consistent `/api/service-name/method` pattern
- **Code Reuse**: Same controller can serve multiple services
- **Separation of Concerns**: 
  - Services: Handle auth/validation
  - Controllers: Handle business logic
- **Flexibility**: Any service can use any controller method
- **DRY Principle**: No need to duplicate controller logic

### Type-Safe Construction
```php
class ClassLessonController extends Controller {
    // Type-safe constructor enables PHPStan level 9!
    public function __construct(Request $request) {
        parent::__construct($request);
    }
}
```

### Magical mapPost Method
The `mapPost` method is a crucial feature that safely maps request data to objects:

```php
// In Core Controller
class Controller {
    protected Request $request;

    /**
     * @param object $object The object to map the POST data to
     * @info: automatically use $this->request->post to map to Model instance
     */
    public function mapPost(object $object): void {
        $name = get_class($object);
        
        // Validate post data exists
        if (!count($this->request->post)) {
            Response::badRequest("no post data for mapping to $name")->show();
            die();
        }

        // Safe property mapping with type checking
        foreach ($this->request->post as $postName => $value) {
            try {
                if (property_exists($object, $postName)) {
                    $object->$postName = $value;
                }
            } catch (\Exception $e) {
                Response::unprocessableEntity(
                    "post $postName cannot be set to $name: " . $e->getMessage()
                )->show();
                die();
            }
        }
    }
}
```

### Usage in Controllers
```php
class UserController extends Controller {
    public function create(): JsonResponse {
        $model = new UserModel();
        $this->mapPost($model);  // Type-safe mapping
        return $model->createWithJsonResponse();
    }
}
```

### Key Benefits
1. **Type Safety**
   - Enables PHPStan level 9
   - Strict type checking
   - Property existence validation
   - Exception handling

2. **Automatic Mapping**
   - No manual property assignment
   - Matches POST keys to object properties
   - Safe type conversion

3. **Error Handling**
   - Clear error messages
   - Type mismatch detection
   - Missing property handling
   - Proper response codes

4. **Code Quality**
   - Reduces boilerplate
   - Prevents type errors
   - Improves maintainability
   - Static analysis friendly

## Authentication & Authorization

### Core Authentication
- JWT token validation through Auth class
- Role-based authorization
- Request validation

### Best Practice: Custom AuthService
Many applications implement a custom base service for authenticated endpoints. For example:

```php
/**
 * Custom base authentication service
 * @hidden
 */
class AuthService extends ApiService {
    protected Auth $auth;
    protected int $company_id;
    protected int $user_id;

    public function __construct(Request $request) {
        parent::__construct($request);
        $this->auth = new Auth($request);
        
        // Custom company context validation
        if (!$this->auth->token || !$this->auth->token->company_id) {
            Response::forbidden('token not belong to company')->show();
            die();
        }
        $this->company_id = $this->auth->token->company_id;

        // Custom user context validation
        if (!$this->auth->token->user_id) {
            Response::forbidden('unknown user')->show();
            die();
        }
        $this->user_id = $this->auth->token->user_id;
    }
}
```

This pattern demonstrates how to extend GEMVC's core authentication to include:
- Company context validation
- User context validation
- Reusable authentication logic
- Consistent error handling

Note: The `AuthService` implementation shown above is a custom best practice example, not part of the core GEMVC Framework.

## Request Handling

### Input Validation
```php
// Core validation
$this->validatePosts([
    'name' => 'string',
    'email' => 'email',
    '?optional' => 'string'
]);

// String length validation
$this->validateStringPosts([
    'username' => '3|15',  // Min 3, Max 15 chars
    'bio' => '|500'       // Max 500 chars
]);
```

### Filtering & Sorting
```php
// URL: api/users?filter_by=country_id=3,status=active
$this->request->filterable([
    'country_id' => 'int',
    'status' => 'string'
]);

// URL: api/users?sort_by=created_at
$this->request->sortable(['created_at', 'name']);
```

### Pagination
```php
// URL: api/users?page_number=1&per_page=20
$this->request->setPageNumber();
$this->request->setPerPage();
```

### API Forwarding
```php
// Forward with current authorization
$response = $this->request->forwardToRemoteApi('http://api.example.com/endpoint');

// Forward with custom authorization
$response = $this->request->forwardPost('http://api.example.com/endpoint', $authHeader);
```

## Documentation System

### Auto-Generated Documentation
GEMVC automatically generates API documentation from your code:
- Endpoint listings
- Request/response schemas
- Authentication requirements
- Input validation rules
- Example responses

### Documentation Directives
GEMVC provides documentation directives to control API documentation:

1. **@hidden Directive**
   ```php
   /**
    * @hidden
    * This service won't appear in API documentation
    */
   class InternalService extends ApiService {
       /**
        * @hidden
        * This method will also be hidden
        */
       public function internalMethod(): JsonResponse {
       }
   }
   ```

Note: Additional documentation directives are available for enhanced API documentation. These are covered in the advanced documentation.

### Mock Responses
```php
public static function mockResponse(string $method): array {
    return match($method) {
        'create' => [
            'success' => true,
            'data' => [
                'id' => 1,
                'name' => 'Example'
            ]
        ],
        default => ['success' => false]
    };
}
```

## Error Handling

### Standard Error Responses
```php
// Validation error
return $this->error('Validation failed', 422, $errors);

// Not found error
return $this->error('Resource not found', 404);

// Server error
return $this->error('Internal server error', 500);

// Success response
return $this->success(['data' => $result]);
```

### Exception Handling Pattern
```php
try {
    $result = $this->process();
    return $this->success($result);
} catch (ValidationException $e) {
    return $this->error($e->getMessage(), 422);
} catch (ModelNotFoundException $e) {
    return $this->error('Resource not found', 404);
} catch (Exception $e) {
    return $this->error('An error occurred', 500);
}
```

## Best Practices

### Service Layer
```php
class UserService extends AuthService {
    public function create(): JsonResponse {
        // 1. Authorization first
        $this->auth->authorize(['admin']);
        
        // 2. Validation second
        $this->validatePosts([
            'name' => 'required|string',
            'email' => 'required|email'
        ]);
        
        // 3. Controller call last
        return (new UserController($this->request))->create();
    }
}
```

class UserModel extends UserTable {
    public int $id;
    public string $name;
    
    /** @var array<RoleModel> */
    public array $_roles;  // Non-DB property starts with _
}
```

## Library Integration

The GEMVC Framework is built on top of the GEMVC Library. For detailed library documentation, refer to:
- `/vendor/gemvc/library/Documentation.md`
- `/vendor/gemvc/library/AIAssist.jsonc`

### Library Components
- `http/`: HTTP request/response handling
- `helper/`: Utility functions and helpers
- `database/`: Database operations and query building
- `email/`: Email handling and templating

For complete library functionality, always refer to the library documentation.