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

### Directory Structure
your_project_name/
├── app/
│ ├── Controllers/
│ ├── Models/
│ ├── Services/
│ └── Traits/
├── config/
│ ├── app.php
│ ├── database.php
│ └── auth.php
├── public/
│ └── index.php
├── resources/
│ └── views/
└── storage/
├── logs/
└── cache/
```

## Getting Started

### Basic Configuration
1. Configure your database in `config/database.php`:
```php
return [
    'default' => 'mysql',
    'connections' => [
        'mysql' => [
            'host' => 'localhost',
            'database' => 'your_database',
            'username' => 'your_username',
            'password' => 'your_password'
        ]
    ]
];
```

### Creating Your First Service
```php
namespace App\Services;

use Gemvc\Core\ApiService;

class UserService extends ApiService
{
    public function get()
    {
        return $this->success([
            'message' => 'Hello from Gemvc!'
        ]);
    }
}
```

## Core Components

### Aggregator
The routing and request handling system.

```php
// Example URL pattern
// /api/service_name/method_name
// /api/user/create
```

#### Features:
- Automatic service loading
- Method routing
- Request validation
- Response formatting

### Table System
Database interaction layer with fluent query building.

```php
use App\Models\UserModel;

class UserModel extends Table
{
    protected $table = 'users';
    
    public function active()
    {
        return $this->where('status', 1)->get();
    }
}
```

#### Common Operations:
```php
// Select
$users = $this->table->select(['id', 'name'])->where('active', 1)->get();

// Insert
$id = $this->table->insert(['name' => 'John', 'email' => 'john@example.com']);

// Update
$this->table->where('id', 1)->update(['status' => 'active']);

// Delete
$this->table->where('id', 1)->delete();
```

### Authentication
JWT-based authentication system.

```php
// Login example
public function login()
{
    $this->validate([
        'email' => 'required|email',
        'password' => 'required'
    ]);
    
    if ($token = Auth::attempt($this->request->all())) {
        return $this->success(['token' => $token]);
    }
    
    return $this->error('Invalid credentials', 401);
}
```

## Traits
### Controller Traits
```php
use App\Traits\Controller\IdTrait;
use App\Traits\Controller\CreateTrait;

class UserController
{
    use IdTrait, CreateTrait;
    
    protected $table = 'users';
}
```

Available Controller Traits:
- `ActivateTrait`: Record activation
- `DeactivateTrait`: Record deactivation
- `DeleteTrait`: Record deletion
- `IdTrait`: ID-based operations
- `RemoveTrait`: Soft deletion
- `RestoreTrait`: Record restoration
- `TrashTrait`: Deleted records management

### Model Traits
```php
use App\Traits\Model\ListObjectTrait;
use App\Traits\Model\CreateTrait;

class UserModel extends Table
{
    use ListObjectTrait, CreateTrait;
}
```

Available Model Traits:
- `ActivateTrait`: Status management
- `CreateTrait`: Record creation
- `DeleteTrait`: Record deletion
- `IdTrait`: ID-based queries
- `ListObjectTrait`: List operations
- `ListObjectTrashTrait`: Deleted records listing

## Error Handling

### Standard Error Responses
```php
// Validation error
return $this->error('Validation failed', 422, $errors);

// Not found error
return $this->error('Resource not found', 404);

// Server error
return $this->error('Internal server error', 500);
```

### Try-Catch Pattern
```php
try {
    $result = $this->process();
    return $this->success($result);
} catch (ValidationException $e) {
    return $this->error($e->getMessage(), 422);
} catch (Exception $e) {
    return $this->error('An error occurred', 500);
}
```

## Best Practices

### Service Development
1. Always validate input:
```php
public function create()
{
    $this->validatePosts([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users',
        'password' => 'required|min:8'
    ]);
}
```

2. Use transactions for multiple operations:
```php
$this->table->transaction(function() {
    $user = $this->table->create($userData);
    $this->profileTable->create(['user_id' => $user->id]);
});
```

3. Implement proper error handling:
```php
public function update($id)
{
    try {
        $user = $this->table->findOrFail($id);
        $user->update($this->request->all());
        return $this->success($user);
    } catch (ModelNotFoundException $e) {
        return $this->error('User not found', 404);
    }
}
```

### Security
1. Always validate JWT tokens for protected routes
2. Implement role-based access control
3. Sanitize user input
4. Use prepared statements for queries
5. Implement rate limiting for APIs

## API Documentation
The framework includes automatic API documentation generation. Access your API documentation at:
```
http://your-app/api/docs
```

### Documentation Example
```php
/**
 * @api {post} /api/users Create User
 * @apiName CreateUser
 * @apiGroup Users
 * 
 * @apiParam {String} name User's name
 * @apiParam {String} email User's email
 * @apiParam {String} password User's password
 * 
 * @apiSuccess {Object} user Created user object
 */
public function create()
{
    // Implementation
}
```

## Support and Contributing
For support, please email ali.khorsandfard@gmail.com or create an issue on the GitHub repository.

To contribute:
1. Fork the repository
2. Create your feature branch
3. Commit your changes
4. Push to the branch
5. Create a Pull Request

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
8. [Best Practices](#best-practices)
9. [Additional Core Features](#additional-core-features)
10. [Environment Configuration](#environment-configuration)

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
class ClassLessonController extends Controller {
    public function create(): JsonResponse {
        // 1. Create model instance
        $model = new ClassLessonModel();
        
        // 2. Safely map POST data to model
        $this->mapPost($model);  // Type-safe mapping!
        
        // 3. Use mapped model
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

## Best Practices

### 1. Always Use Bootstrap
```php
// ✅ Correct
$bootstrap = new Bootstrap($request);

// ❌ Wrong
$service = new Service($request);
```

### 2. Proper Service Extension
```php
// ✅ Public endpoints
class PublicService extends ApiService {}

// ✅ Protected endpoints
class SecureService extends AuthService {}
```

### 3. Layered Authorization
```php
class Service extends AuthService {
    // Service-level auth
    public function __construct(Request $request) {
        parent::__construct($request);
        $this->auth->authorize(['admin','employee']);
    }

    // Method-level auth
    public function create(): JsonResponse {
        $this->auth->authorize(['admin']);
        // Method logic
    }
}
```

### 4. Input Validation
```php
// Core GEMVC validation
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

Note: Some examples in this documentation might include custom implementations that are project-specific and not part of the core GEMVC Framework. For instance, `validatePostWithCompany` is a custom implementation built on top of GEMVC's core validation system.

### 5. Controller Decoupling
Services can use any controller:
```php
public function create(): JsonResponse {
    // Auth and validation
    $this->auth->authorize(['role']);
    $this->validatePosts(['field' => 'type']);

    // Can use any controller
    return (new SomeController($this->request))->method();
}
```

### Best Practice Rule
**Never bypass the Service Layer**: All interactions with Controllers must go through the API (Service) Layer. This ensures:
- Proper authentication
- Input validation
- Request sanitization
- Consistent error handling
- Documented API endpoints

## Additional Core Features

### Request Mapping
The Request class provides built-in object mapping capabilities:
```php
// Map POST data directly to object properties
$this->request->setPostToObject($someObject);

// Static mapping helper
Request::mapPost($request, $object);

// Example:
class User {
    public string $name;
    public string $email;
}
$user = new User();
$this->request->setPostToObject($user);
```

### Complete Schema Validation
GEMVC provides schema validation for all HTTP methods:
```php
// GET schema validation
$this->request->defineGetSchema([
    'id' => 'int',
    'filter' => 'string'
]);

// PUT schema validation
$this->request->definePutSchema([
    'name' => 'string',
    'email' => 'email'
]);

// PATCH schema validation
$this->request->definePatchSchema([
    'status' => 'string',
    '?description' => 'string'
]);
```

### Type Validation Methods
Comprehensive type validation and conversion:
```php
// Integer validation
$id = $this->request->intValuePost('id');
$age = $this->request->intValueGet('age');

// Float validation
$price = $this->request->floatValuePost('price');
$rate = $this->request->floatValueGet('rate');

// Available types for validation:
// - string
// - int/integer
// - float/number
// - bool/boolean
// - email
// - date
// - datetime
// - array
// - json
// - url
// - ip
// - ipv4
// - ipv6
```

### JWT Token Handling
Complete JWT token management:
```php
// Token extraction and verification
$token = $this->request->getJwtToken();
if ($token && $token->verify()) {
    // Token is valid
    $userId = $token->user_id;
    $roles = $token->role;
}

// Token properties
$token->isTokenValid;  // Token validation status
$token->user_id;       // Authenticated user ID
$token->role;          // User roles (comma-separated)
$token->company_id;    // Company context
$token->error;         // Any validation errors

// Manual token setting
$this->request->setJwtToken($jwtToken);
```

### Error Handling
Built-in error management:
```php
// Check for errors
if ($this->request->getError()) {
    // Handle error
}

// Access error messages
$errorMessage = $this->request->error;

// Built-in response types
Response::badRequest($message)->show();
Response::unauthorized($message)->show();
Response::forbidden($message)->show();
Response::notFound($message)->show();
Response::success($data)->show();
```

### Request Information
Access to request metadata:
```php
// Request identifiers
$requestId = $this->request->getId();
$timestamp = $this->request->getTime();
$executionStart = $this->request->getStartExecutionTime();

// Request details
$url = $this->request->requestedUrl;
$method = $this->request->requestMethod;
$remoteAddr = $this->request->remoteAddress;
$queryString = $this->request->queryString;
```

## Model Layer

### Overview
Models in GEMVC:
- Extend the Table base class
- Handle data logic
- Provide flexible response types
- Support type-safe operations

### Response Flexibility
Models can return multiple types based on the scenario:

1. **Direct JsonResponse**
```php
public function createWithJsonResponse(): JsonResponse {
    // Handle creation and return API response
    return Response::success(['id' => $this->id])->json();
}
```

2. **Object Return**
```php
public function getDetails(): object {
    return (object)[
        'id' => $this->id,
        'name' => $this->name
    ];
}
```

3. **Simple Types**
```php
public function isActive(): bool {
    return $this->status === 'active';
}
```


### Multi-Model Operations
Example of handling multiple models:
```php
class UserDataController extends Controller {
    public function getData(): JsonResponse {
        // First Model: Company validation
        $companyModel = new CompanyModel();
        if (!$companyModel->hasUser($this->request->user_id)) {
            return Response::forbidden('User not in company');
        }

        // Second Model: Data retrieval
        $userDataModel = new UserDataModel();
        return $userDataModel->data();
    }
}
```

### Model Layer Best Practices
1. Models should extend Table class
2. Choose appropriate return types based on use case
3. Keep data logic in models
4. Use type-safe properties
5. Leverage model flexibility for DRY code

### Special Property Naming Convention
GEMVC uses a special naming convention for model properties that starts with underscore (`_`). This is a powerful feature that:

1. **Ignores in CRUD Operations**
   - Properties starting with `_` are ignored by Table layer
   - Not included in database operations
   - Perfect for related data

2. **Complex Model Relationships**
```php
class ClassroomModel extends ClassroomTable {
    /**
     * @var array<mixed>
     */	
    public array $_classroom_teachers;  // Note the underscore prefix
    
    /**
     * @var array<mixed>
     */
    public array $_classroom_lessons;   // Note the underscore prefix

    public function __construct() {
        parent::__construct();
        $this->_classroom_teachers = [];
        $this->_classroom_lessons = [];
    }
}
```

3. **Benefits**
   - Create complex models without table interference
   - No need for stdClass
   - Maintains PHPStan level 9 compatibility
   - Clean separation of concerns
   - Type-safe relationships
   - No database schema changes needed

### Example Usage

1. **Defining Related Data**
```php
class ClassroomModel extends ClassroomTable {
    /** @var array<mixed> */
    public array $_classroom_teachers;  // Not in database table
    
    public string $name;               // Actual database column
    public int $company_id;           // Actual database column
}
```

2. **Loading Related Data**
```php
public function getWithTeachers(): self {
    $classroom = $this->findOrFail($this->id);
    // Load related data into _ property
    $classroom->_classroom_teachers = (new PlannedTeacherModel())
        ->byClassroom($classroom->id);
    return $classroom;
}
```

3. **Multiple Relations**
```php
public function getMax(): self {
    $classroom = $this->findOrFail($this->id);
    // Load multiple related models
    $classroom->_classroom_teachers = (new PlannedTeacherModel())
        ->byClassroom($classroom->id);
    $classroom->_classroom_lessons = (new ClassLessonModel())
        ->byClassroom($classroom->id);
    return $classroom;
}
```

### Key Benefits

1. **Type Safety**
   - Full PHPStan level 9 support
   - No deprecated warnings in PHP 8+
   - Clear property types
   - Documented arrays

2. **Clean Architecture**
   - Clear separation between table and related data
   - No database schema pollution
   - Flexible model relationships
   - Maintainable code structure

3. **Developer Freedom**
   - Create complex models easily
   - Add related data without table changes
   - Type-safe relationship handling
   - No need for stdClass objects

4. **Performance**
   - No unnecessary database columns
   - Efficient data loading
   - Clear data separation
   - Optimized CRUD operations

### Type Definitions for Related Data

1. **Generic Array Approach**
```php
class ClassroomModel extends ClassroomTable {
    /** @var array<mixed> */
    public array $_classroom_teachers;
}
```

2. **Specific Model Type Approach**
```php
class ClassroomModel extends ClassroomTable {
    /** @var array<PlannedTeacherModel> */
    public array $_classroom_teachers;
    
    /** @var array<ClassLessonModel> */
    public array $_classroom_lessons;
}
```

### Benefits of Each Approach

1. **Generic Array (`array<mixed>`)**
   - More flexible
   - Easier to modify
   - Less coupling between models
   - Good for rapid development

2. **Specific Type (`array<ModelName>`)**
   - Better type hinting in IDE
   - Stronger static analysis
   - Clear documentation
   - Better code completion
   - More explicit dependencies

### Example Usage

```php
class ClassroomModel extends ClassroomTable {
    /** @var array<PlannedTeacherModel> */
    public array $_classroom_teachers;

    public function getWithTeachers(): self {
        $classroom = $this->findOrFail($this->id);
        // IDE will now understand the exact type
        $classroom->_classroom_teachers = (new PlannedTeacherModel())
            ->byClassroom($classroom->id);
        return $classroom;
    }
    
    // IDE can now provide better type hints
    public function getFirstTeacher(): ?PlannedTeacherModel {
        return $this->_classroom_teachers[0] ?? null;
    }
}
```

### Developer Choice
- Choose based on project needs
- Consider team preferences
- Balance flexibility vs type safety
- Can mix approaches in same project

## Model Traits

GEMVC provides a set of powerful traits in `vendor/gemvc/framework/src/traits/model/` that enhance model functionality:

### Available Model Traits

1. **Core CRUD Operations**
   - `CreateTrait` - Create records with JsonResponse
   - `UpdateTrait` - Update records with JsonResponse
   - `RemoveTrait` - Soft delete with timestamp management
   - `DeleteTrait` - Hard delete operations

2. **Listing Operations**
   - `ListTrait` - Basic listing with pagination/filtering
   - `ListObjectTrait` - Object-based listing
   - `ListTrashTrait` - Access soft-deleted records
   - `ListObjectTrashTrait` - Object-based trash listing

3. **Record Management**
   - `IdTrait` - ID-based operations (find, findOrFail)
   - `ActivateTrait` - Record activation
   - `DeactivateTrait` - Record deactivation
   - `RestoreTrait` - Restore soft-deleted records

### Usage Example
```php
namespace App\Model;

use App\Table\ClassroomTable;
use Gemvc\Traits\Model\CreateTrait;
use Gemvc\Traits\Model\ListTrait;
use Gemvc\Traits\Model\RemoveTrait;
use Gemvc\Traits\Model\UpdateTrait;
use Gemvc\Traits\Model\IdTrait;

class ClassroomModel extends ClassroomTable
{
    use CreateTrait;
    use UpdateTrait;
    use RemoveTrait;
    use ListTrait;
    use IdTrait;
}
```

### Common Trait Combinations

1. **Basic Model (CRUD)**
```php
class BasicModel extends Table
{
    use CreateTrait;
    use UpdateTrait;
    use RemoveTrait;
    use ListTrait;
}
```

2. **Soft-Delete Model**
```php
class SoftDeleteModel extends Table
{
    use CreateTrait;
    use UpdateTrait;
    use RemoveTrait;
    use ListTrashTrait;
    use RestoreTrait;
}
```

3. **Activatable Model**
```php
class ActivatableModel extends Table
{
    use CreateTrait;
    use UpdateTrait;
    use ActivateTrait;
    use DeactivateTrait;
}
```

### Best Practices

1. **Import Only Required Traits**
   - Use only traits needed for model functionality
   - Avoid unnecessary trait imports
   - Keep models focused and lean

2. **Trait Order**
   - CRUD traits first
   - Listing traits second
   - Utility traits last
   - Maintain consistent ordering

3. **Type Safety**
   - All traits support PHPStan level 9
   - Use proper return type hints
   - Follow trait method signatures

## Table Layer

The Table layer is the lowest level of GEMVC's architecture, providing direct database interaction. GEMVC offers two base table classes:

1. **Table** (`Gemvc\Core\Table`)
2. **CRUDTable** (`Gemvc\Core\CRUDTable`)

Both extend from `PdoQuery`, which handles the core database operations.

### Base Classes

#### 1. Simple Table
```php
use Gemvc\Core\Table;
use Gemvc\Traits\Table\InsertSingleQuery;
use Gemvc\Traits\Table\UpdateQuery;
use Gemvc\Traits\Table\RemoveQuery;

class CourseTable extends Table 
{
    use InsertSingleQuery;
    use UpdateQuery;
    use RemoveQuery;

    public int $id;
    public int $company_id;
    public string $name;

    public function getTable(): string 
    {
        return 'courses';
    }
}
```

#### 2. CRUD Table
```php
use Gemvc\Core\CRUDTable;

class UserTable extends CRUDTable 
{
    public int $id;
    public string $email;
    public string $name;

    public function getTable(): string 
    {
        return 'users';
    }
}
```

### Key Differences

1. **Simple Table**
   - Minimal base functionality
   - Add CRUD operations via traits
   - More flexible/customizable
   - Choose only needed operations

2. **CRUDTable**
   - Built-in CRUD operations
   - Standard implementation
   - Less flexible but faster to implement
   - All CRUD operations included

### Available Table Traits

Located in `vendor/gemvc/framework/src/traits/table/`:

```php
use Gemvc\Traits\Table\{
    ActivateQuery,
    DeactivateQuery,
    InsertSingleQuery,
    RemoveQuery,
    SafeDeleteQuery,
    SelectByIdQuery,
    UpdateQuery
};
```

### Query Building Methods

Both Table types include fluent query building:

```php
$results = $this
    ->select()
    ->where('status', 'active')
    ->where('company_id', $companyId)
    ->orderBy('created_at', false)  // DESC
    ->limit(10)
    ->run();
```

### Core Features

1. **Type Safety**
```php
class ClassroomTable extends Table {
    public int $id;
    public string $name;
    public ?string $deleted_at;
}
```

2. **Query Building**
```php
// Basic queries
$this->select();
$this->where('column', $value);
$this->whereNull('deleted_at');
$this->whereBetween('date', $start, $end);
$this->orderBy('id', false);
$this->limit(10);

// Joins
$this->join('other_table', 'this.id = other.foreign_id');
```

3. **Pagination**
```php
$table->setPage(2);        // Set current page
$table->getLimit();        // Get items per page
$table->getTotalCounts();  // Get total records
```

### Best Practices

1. **Choosing Base Class**
```php
// ✅ Use Table with specific traits
class CustomTable extends Table 
{
    use InsertSingleQuery;  // Only what you need
}

// ✅ Use CRUDTable for standard operations
class StandardTable extends CRUDTable 
{
    // All CRUD included
}
```

2. **Property Definitions**
```php
// ✅ Match database columns exactly
public int $id;
public string $name;
public ?string $deleted_at;

// ❌ Avoid mismatched types
public string $id;  // Should be int
public array $data; // Should match DB type
```

3. **Table Methods**
```php
// ✅ Clear table name
public function getTable(): string 
{
    return 'courses';
}

// ✅ Custom queries with type safety
public function findActive(): array 
{
    return $this
        ->select()
        ->where('status', 'active')
        ->run();
}
```

### Inheritance Hierarchy

```

## Database Layer: PdoQuery and QueryExecuter Relationship

### Inheritance Structure

```
QueryExecuter (Base Database Operations)
    ↓
PdoQuery (Query Execution Layer)
    ↓
Table/CRUDTable (GEMVC Framework Layer)
```

### QueryExecuter Class
The base class that handles fundamental database operations:

1. **Core Responsibilities**
   - Database connection management
   - Query execution tracking
   - Result fetching
   - Error handling

2. **Key Features**
```php
class QueryExecuter {
    // Execution tracking
    private float $startTime;    // Set in constructor
    private float $endTime;      // Set after execute() or on error
    
    // Result management
    public function execute(): bool
    public function affectedRows(): int
    public function lastInsertId(): string|false
    
    // Result fetching
    public function fetchAll(): array
    public function fetchAllObjects(): array
    public function fetchAllClass(): array
    public function fetchColumn(): mixed
}
```

### PdoQuery Extension
PdoQuery extends QueryExecuter to provide higher-level database operations:

1. **Enhanced Features**
```php
class PdoQuery extends QueryExecuter {
    // Specialized query methods
    public function insertQuery(string $query, array $bindings): int|false
    public function updateQuery(string $query, array $bindings): ?int
    public function selectQuery(string $query, array $bindings): array|false
    public function deleteQuery(string $query, array $bindings): int|null|false
    
    // Private helper for all query types
    private function executeQuery(string $query, array $bindings): bool
}
```

2. **Value Addition**
   - Type-safe parameter binding
   - Standardized query execution
   - Consistent error handling
   - Result type management

### Interaction Flow

1. **Query Execution**
```php
// In PdoQuery
public function selectQuery(string $query, array $bindings): array|false {
    if (!$this->isConnected()) {
        return false;
    }
    
    // Uses QueryExecuter's methods
    if ($this->executeQuery($query, $bindings)) {
        return $this->fetchAll();  // From QueryExecuter
    }
    return false;
}
```

2. **Error Handling**
```php
// QueryExecuter provides base error tracking
parent::setError('Database error');

// PdoQuery enhances with specific errors
$this->setError('Query execution failed: ' . $e->getMessage());
```

3. **Result Processing**
```php
// QueryExecuter provides base fetching
$result = parent::fetchAll();

// PdoQuery adds type safety and validation
return is_array($result) ? $result : false;
```

### Key Benefits of This Structure

1. **Separation of Concerns**
   - QueryExecuter: Core database operations
   - PdoQuery: Query execution and type safety
   - Table/CRUDTable: High-level database interactions

2. **Maintainable Code**
   - Clear responsibility layers
   - Consistent error handling
   - Type-safe operations

3. **Performance Optimization**
   - Connection pooling (QueryExecuter)
   - Prepared statements (PdoQuery)
   - Result caching (both layers)

4. **Type Safety**
   - Strong parameter typing
   - Return type declarations
   - Error state tracking
```

### Class Naming Conventions

1. **API Services**
```php
// ✅ Correct
class Classroom extends AuthService
class User extends ApiService
class PublicAuth extends ApiService
// Service suffix is optional
class ClassroomService extends AuthService  // Also valid

// ❌ Incorrect
class classroom extends AuthService         // Not PascalCase
class ClassroomAPI extends AuthService      // Wrong suffix
class classroom_service extends AuthService // Wrong style
```

### File Naming for Services

```
// ✅ Correct
app/api/
    ├── Classroom.php
    ├── User.php
    ├── PublicAuth.php
    └── ClassroomService.php  // Also valid with Service suffix

// ❌ Incorrect
app/api/
    ├── classroom.php         // Not PascalCase
    ├── ClassroomAPI.php      // Wrong suffix
    └── classroom_service.php // Wrong style
```

The key point is that in the Service layer:
- PascalCase is required
- 'Service' suffix is optional (developer's choice)
- Class name should clearly indicate its purpose
- Must extend either ApiService or AuthService

Each layer has clear responsibilities
Service → Authentication & Validation
Controller → Business Logic
Model → Data Logic & Response Flexibility
Table → Database Operations

## Environment Configuration

GEMVC uses a `.env` file for configuration management. This file contains essential settings for database connections, token management, and URL routing.

### Database Configuration
```php
DB_HOST=localhost
DB_PORT=3306
DB_NAME=your_database
DB_CHARSET=utf8
DB_USER=your_user
DB_PASSWORD='your_password'
QUERY_LIMIT=500  # Default query limit for pagination
```

### JWT Token Settings
```php
TOKEN_SECRET='your_secret_key'
TOKEN_ISSUER='MyCompany'
LOGIN_TOKEN_VALIDATION_IN_SECONDS=789000
REFRESH_TOKEN_VALIDATION_IN_SECONDS=43200    # 12 hours
ACCESS_TOKEN_VALIDATION_IN_SECONDS=1200      # 20 minutes
```

### URL Routing Configuration
GEMVC supports different URL routing modes:

1. **Standard Mode**
```php
SERVICE_IN_URL_SECTION=1    # /api/service/method
METHOD_IN_URL_SECTION=2
```

2. **Aggregation Mode**
```php
SERVICE_IN_URL_SECTION=1    # /api/service/controller/method
CONTROLLER_IN_URL_SECTION=2
METHOD_IN_URL_SECTION=3
AUTH_SERVICE_ADDRESS="http://auth/"
```

3. **Local Development (e.g., XAMPP)**
```php
SERVICE_IN_URL_SECTION=2    # Adjusts for local development paths
METHOD_IN_URL_SECTION=3
```

### Best Practices
1. **Security**
   - Never commit `.env` to version control
   - Use different values for development/production
   - Keep TOKEN_SECRET secure and unique

2. **Token Management**
   - Set appropriate token validation periods
   - Use shorter periods for access tokens
   - Use longer periods for refresh tokens

3. **URL Configuration**
   - Choose routing mode based on deployment
   - Adjust sections based on URL structure
   - Document URL patterns for team