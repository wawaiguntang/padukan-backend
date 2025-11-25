# Hybrid Policy Engine (RBAC + PBAC)

A production-grade authorization system that combines Role-Based Access Control (RBAC) with Policy-Based Access Control (PBAC) using Clean Architecture principles.

## Overview

This implementation provides a comprehensive authorization system with:

- **RBAC**: Traditional role-based permissions
- **PBAC**: Advanced policy evaluation with nested JSON conditions
- **Hybrid Approach**: Combines both models for maximum flexibility
- **High Performance**: Cached policy evaluation with Redis/memory support
- **Clean Architecture**: Repository + Service pattern with strict separation of concerns

## Architecture

### Core Components

```
Modules/Authorization/
├── app/
│   ├── Models/                 # Database models (Role, Permission, Policy, RolePermission)
│   ├── Repositories/           # Data access layer with caching
│   │   ├── Policy/            # PolicyRepository with PolicyEvaluator integration
│   │   └── Role/              # Role and permission repositories
│   ├── Services/
│   │   ├── Policy/            # PolicyEvaluator (core PBAC logic)
│   │   ├── Role/              # Role management services
│   │   └── AuthorizationService.php  # Hybrid RBAC + PBAC service
│   ├── Http/
│   │   ├── Controllers/       # REST API endpoints
│   │   └── Middleware/        # AuthorizationMiddleware
│   └── Providers/             # Service registration
├── database/
│   ├── migrations/            # Database schema
│   └── seeders/               # Sample data
└── tests/                     # Comprehensive unit tests
```

### Database Schema

#### Roles Table
```sql
- id: Primary key
- name: Role name (unique)
- slug: URL-friendly identifier
- description: Optional description
- timestamps
```

#### Permissions Table
```sql
- id: Primary key
- name: Permission name (unique)
- slug: URL-friendly identifier
- description: Optional description
- timestamps
```

#### Role Permissions Table (RBAC)
```sql
- id: Primary key
- role_id: Foreign key to roles
- permission_id: Foreign key to permissions
```

#### Policies Table (PBAC)
```sql
- id: Primary key
- name: Policy name (unique)
- resource: Target resource/entity
- actions: JSON array of allowed actions
- scope: Authentication scope/context
- group: Policy group for ordering
- is_active: Active status
- priority: Evaluation priority within group
- conditions: JSON conditions tree
- module: Module this policy belongs to
- description: Policy description
- timestamps
```

## Policy Conditions Format

Policies use nested JSON conditions with logical and comparison operators:

### Basic Structure
```json
{
  "operator": "AND|OR|NOT",
  "conditions": [
    {
      "field": "user.id",
      "operator": "=",
      "value": "resource.owner_id"
    },
    {
      "operator": "OR",
      "conditions": [
        {
          "field": "user.role",
          "operator": "IN",
          "value": ["admin", "editor"]
        },
        {
          "field": "resource.status",
          "operator": "!=",
          "value": "archived"
        }
      ]
    }
  ]
}
```

### Supported Operators

#### Logical Operators
- `AND`: All conditions must be true
- `OR`: At least one condition must be true
- `NOT`: Negates the condition

#### Comparison Operators
- `=`, `!=`: Equality/inequality
- `<`, `>`, `<=`, `>=`: Numeric comparisons
- `IN`, `NOT_IN`: Array membership
- `CONTAINS`, `NOT_CONTAINS`: String/array contains

### Field Resolution

Fields support dot notation for nested object access:
- `user.id` → `context['user']['id']`
- `resource.owner_id` → `context['resource']['owner_id']`

Values can also reference fields:
- `"value": "resource.owner_id"` compares against the resolved field value

## Usage Examples

### 1. Authorization Service

```php
use Modules\Authorization\Services\AuthorizationService;

$authService = app(AuthorizationService::class);

// Check authorization
$result = $authService->authorize($user, 'documents', 'read', [
    'document_id' => 123,
    'owner_id' => 456
]);

if ($result['authorized']) {
    // Access granted
} else {
    // Access denied: $result['reason']
}

// Simple boolean check
if ($authService->can($user, 'documents', 'write')) {
    // User can write documents
}
```

### 2. Middleware

```php
// In routes/api.php
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/documents/{id}', [DocumentController::class, 'show'])
        ->middleware(\Modules\Authorization\Http\Middleware\AuthorizationMiddleware::class . ':documents,read');
});
```

### 3. Controller Integration

```php
<?php

namespace App\Http\Controllers;

use Modules\Authorization\Services\AuthorizationService;

class DocumentController extends Controller
{
    protected AuthorizationService $authService;

    public function __construct(AuthorizationService $authService)
    {
        $this->authService = $authService;
    }

    public function show(Request $request, $documentId)
    {
        $result = $this->authService->authorize(
            $request->user(),
            'documents',
            'read',
            ['document_id' => $documentId]
        );

        if (!$result['authorized']) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        // Return document data
    }
}
```

## API Endpoints

### Check Authorization
```
POST /api/v1/authorization/check
Content-Type: application/json

{
  "resource": "documents",
  "action": "read",
  "context": {"document_id": 123},
  "scope": "optional_scope"
}
```

### Get User Permissions
```
GET /api/v1/authorization/permissions
Authorization: Bearer {token}
```

### Get Applicable Policies
```
GET /api/v1/authorization/policies?resource=documents&action=read
Authorization: Bearer {token}
```

### Access Protected Resource
```
GET /api/v1/authorization/documents/{id}
Authorization: Bearer {token}
// Protected by middleware
```

## Sample Policies

### Document Access Policy
```json
{
  "name": "Document Access Policy",
  "resource": "documents",
  "actions": ["read", "write", "delete"],
  "conditions": {
    "operator": "OR",
    "conditions": [
      {
        "field": "user.id",
        "operator": "=",
        "value": "resource.owner_id"
      },
      {
        "field": "user.roles",
        "operator": "CONTAINS",
        "value": "admin"
      }
    ]
  }
}
```

### Financial Data Policy
```json
{
  "name": "Financial Data Access Policy",
  "resource": "financial_records",
  "actions": ["read"],
  "conditions": {
    "operator": "AND",
    "conditions": [
      {
        "field": "user.department",
        "operator": "IN",
        "value": ["finance", "accounting"]
      },
      {
        "field": "user.clearance_level",
        "operator": ">==",
        "value": 3
      }
    ]
  }
}
```

## Performance Features

### Caching Strategy
- **Policy Cache**: 15-minute TTL for policy data
- **Evaluation Cache**: Context-aware caching for repeated evaluations
- **Redis Support**: Configurable cache driver

### Optimizations
- Lazy loading of user roles and permissions
- Indexed database queries
- Minimal memory footprint for condition evaluation
- Recursion depth protection (50 levels max)

## Testing

Run the comprehensive test suite:

```bash
# Run all authorization tests
php artisan test Modules/Authorization/tests/

# Run specific test
php artisan test Modules/Authorization/tests/Unit/Services/PolicyEvaluatorTest.php
```

### Test Coverage
- ✅ All comparison operators
- ✅ Logical operators (AND, OR, NOT)
- ✅ Nested conditions
- ✅ Dot notation field resolution
- ✅ Field references in values
- ✅ Debug mode functionality
- ✅ Error handling and validation
- ✅ Performance limits (recursion depth)

## Configuration

### Cache Configuration
```php
// config/authorization.php
return [
    'cache' => [
        'ttl' => env('AUTH_CACHE_TTL', 900), // 15 minutes
        'prefix' => 'auth:',
    ],
    'evaluation' => [
        'max_depth' => env('AUTH_MAX_DEPTH', 50),
        'debug_mode' => env('APP_DEBUG', false),
    ],
];
```

### Service Registration
The services are automatically registered in `AuthorizationServiceProvider`:

```php
// Singleton services
$this->app->singleton(PolicyEvaluator::class);
$this->app->singleton(AuthorizationService::class);
```

## Enterprise Scaling Recommendations

### 1. Database Optimization
- Add composite indexes on `(resource, action, scope)`
- Partition policies table by scope/group
- Use read replicas for policy evaluation

### 2. Caching Strategy
- Implement policy versioning for cache invalidation
- Use Redis cluster for horizontal scaling
- Add circuit breaker for cache failures

### 3. Performance Monitoring
- Add metrics for evaluation time
- Monitor cache hit rates
- Track authorization failure patterns

### 4. Security Considerations
- Implement rate limiting on authorization checks
- Add audit logging for sensitive operations
- Use encrypted cache for sensitive policy data

### 5. High Availability
- Policy replication across regions
- Graceful degradation when cache is unavailable
- Fallback to database-only evaluation

## Migration Guide

### From Basic RBAC
1. Existing roles and permissions remain functional
2. Add policies for fine-grained control
3. Gradually migrate authorization checks to use the hybrid service

### From Other PBAC Systems
1. Convert existing policies to the JSON condition format
2. Update field references to use dot notation
3. Test evaluation logic thoroughly

## Troubleshooting

### Common Issues

1. **Policy Not Evaluating**
   - Check condition syntax
   - Verify field paths exist in context
   - Enable debug mode for detailed evaluation steps

2. **Performance Issues**
   - Review cache configuration
   - Check database indexes
   - Monitor evaluation depth

3. **Authorization Failures**
   - Verify user roles and permissions
   - Check policy priorities
   - Review context data structure

### Debug Mode

Enable debug mode to get detailed evaluation information:

```php
$result = $authService->authorize($user, 'resource', 'action', $context);
if (!$result['authorized']) {
    // In debug mode, result contains detailed evaluation steps
    Log::info('Authorization failed', $result['debug_info']);
}
```

## Contributing

1. Follow PSR-12 coding standards
2. Add comprehensive tests for new features
3. Update documentation for API changes
4. Ensure backward compatibility

## License

This implementation is part of the application codebase and follows the project's licensing terms.