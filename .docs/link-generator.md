# Link Generator

Generates URLs to [endpoints](endpoints.md) from [schema](schema.md).

## Usage

LinkGenerator is registered as a service `api.core.linkGenerator` and can be autowired.

```php
use Apitte\Core\LinkGenerator\LinkGenerator;

class MyService
{
    public function __construct(
        private LinkGenerator $linkGenerator,
    ) {}

    public function generateLinks(): void
    {
        // Generate link by Controller::method
        $url = $this->linkGenerator->link(UsersController::class . '::detail', ['id' => 123]);
        // Result: /api/v1/users/123

        // Generate link by endpoint ID
        $url = $this->linkGenerator->link('api.users.detail', ['id' => 123]);
        // Result: /api/v1/users/123
    }
}
```

## Lookup methods

### By Controller::method

```php
$linkGenerator->link(UsersController::class . '::list');
$linkGenerator->link(UsersController::class . '::detail', ['id' => 123]);
```

The controller class name and method name are used to find the endpoint.

### By endpoint ID

```php
$linkGenerator->link('api.users.list');
$linkGenerator->link('api.users.detail', ['id' => 123]);
```

The endpoint ID is defined using the `#[Id]` attribute on controllers and methods. IDs are hierarchical, joined by dots.

## Parameters

### Path parameters

Path parameters (defined in the endpoint path like `/{id}`) are substituted from the params array:

```php
// Endpoint: /users/{id}
$linkGenerator->link(UsersController::class . '::detail', ['id' => 123]);
// Result: /users/123
```

### Query parameters

Extra parameters that are not path parameters are added as query string:

```php
// Endpoint: /users
$linkGenerator->link(UsersController::class . '::list', ['page' => 2, 'limit' => 10]);
// Result: /users?page=2&limit=10
```

### Mixed parameters

Path and query parameters can be combined:

```php
// Endpoint: /users/{id}
$linkGenerator->link(UsersController::class . '::detail', ['id' => 123, 'include' => 'posts']);
// Result: /users/123?include=posts
```

## Exceptions

`LinkGeneratorException` is thrown when:

- Endpoint is not found
- Required path parameter is missing

```php
use Apitte\Core\LinkGenerator\LinkGeneratorException;

try {
    $linkGenerator->link('nonexistent');
} catch (LinkGeneratorException $e) {
    // Handle error
}
```
