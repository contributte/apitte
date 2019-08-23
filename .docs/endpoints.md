# Endpoints

Endpoint is representation of a unique url (like a `/api/v1/users`) and one or multiple operations (HTTP methods)

In our case endpoint is implemented as a [controller](#controllers) method.

## Controllers

Create base controller with root path to your api

- controller must implement `Apitte\Core\UI\Controller\IController`

```php
namespace App\Api\V1\Controllers;

use Apitte\Core\Annotation\Controller\GroupPath;
use Apitte\Core\UI\Controller\IController;

/**
 * @GroupPath("/api/v1")
 */
abstract class BaseV1Controller implements IController
{
}
```

Create an endpoint

- Controller must have annotation `@ControllerPath()` and be registered as service
- Method must have annotations `@Path()` and `@Method()`

```yaml
services:
    - App\Api\V1\Controllers\UsersController
```

```php
namespace App\Api\V1\Controllers;

use Apitte\Core\Annotation\Controller\ControllerPath;
use Apitte\Core\Annotation\Controller\Method;
use Apitte\Core\Annotation\Controller\Path;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use Nette\Utils\Json;

/**
 * @ControllerPath("/users")
 */
class UsersController extends BaseV1Controller
{

    /**
     * @Path("/")
     * @Method("GET")
     */
    public function index(ApiRequest $request, ApiResponse $response): ApiResponse
    {
        // This is an endpoint
        //  - its path is /api/v1/users/
        //  - it should be available on address example.com/api/v1/users/

        $response = $response->writeBody(Json::encode([
            [
                'id' => 1,
                'firstName' => 'John',
                'lastName' => 'Doe',
                'emailAddress' => 'john@doe.com',
            ],
            [
                'id' => 2,
                'firstName' => 'Elon',
                'lastName' => 'Musk',
                'emailAddress' => 'elon.musk@spacex.com',
            ],
        ]));

        return $response;
    }

}
```

**Tip** Combination of `GroupPath("/")`, `@ControllerPath("/")` and `@Path("/")` targets homepage, e.q. `example.com/`.

### List of annotations

ID
  - Must consist only of following characters: `a-z`, `A-Z`, `0-9`, `_`
  - `@GroupId`
    - Abstract classes only
    - Defined on controller
  - `@ControllerId`
    - Defined on controller
  - `@Id`
    - Defined on method

Path
  - See example controllers above
  - Must consist only of following characters: `a-z`, `A-Z`, `0-9`, `-_/`
  - `@GroupPath`
    - Abstract classes only
    - Defined on controller
  - `@ControllerPath`
    - Defined on controller
  - `@Path`
    - Defined on method
  
`@Method`
  - Allowed HTTP method for endpoint
  - GET, POST, PUT, OPTION, DELETE, HEAD
  - `@Method("GET")`
  - `@Method({"POST", "PUT"})`
  - Defined on method
  
`@Tag`
  - Used by [OpenApi](schema.md#openapi)
  - Could by also used by your custom logic
  - `@Tag(name="name")`
  - `@Tag(name="string", value="string|null")`
  - Defined on class and method

Mapping
  - Validate and map data from request and map data to response
  - `@RequestParameter`, `@RequestParameters`
  - `@RequestBody`
  - See [mapping](mapping.md) chapter for more info.

Negotiations
  - Response transformations
  - `@Negotiation`, `@Negotiations`
  - See [apitte/negotiation](https://github.com/apitte/negotiation) docs for more info.

### Automatic controllers registration

It's boring to register each controller one by one as a service, let them register through the `ResourceExtension`.

Install [contributte/di](https://github.com/contributte/di)

Configure resource extension and profit.

```yaml
extensions:
    resource: Contributte\DI\Extension\ResourceExtension

resource:
    resources:
        App\Api\V1\Controllers\:
            # where the classes are located
            paths: [%appDir%/Api/V1/Controllers]
```
