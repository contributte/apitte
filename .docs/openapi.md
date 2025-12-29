# OpenApi

Convert Apitte schema to [OpenApi Schema](https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md)
and add [Swagger UI](https://petstore.swagger.io) as [Tracy](https://github.com/nette/tracy) panel

## Setup

Install and register OpenApi plugin.

```neon
api:
    plugins:
        Apitte\OpenApi\DI\OpenApiPlugin:
```

## Usage

### SchemaBuilder

You can get whole schema from SchemaBuilder service.

```php
use Apitte\OpenApi\ISchemaBuilder;

/** @var ISchemaBuilder $schemaBuilder */
$openApi = $schemaBuilder->build();
```

### Definitions

There are many ways how to define open api schema.

You can write **raw** OpenApi.

- [Config](#config)
- [External Files](#external-files)
- [OpenApi Attributes](#openapi-attributes)

Or you can let the plugin do for you in **dynamic** way.

- [Core Attributes](#core-attributes)
- [Entity Attributes](#entity-attributes)

Also you can write your **own** definition.

- [Custom](#custom-definition)

#### Config

You can easily define whole schema (or part) directly in extension config.

```neon
api:
    plugins:
        Apitte\OpenApi\DI\OpenApiPlugin:
            definition:
                openapi: "3.0.2"
                info:
                    title: My awesome OpenApi specification
                    version: "1.0.0"
                ...
```

#### External Files

Define whole OpenApi schema (or part) in external files.

```neon
api:
    plugins:
        Apitte\OpenApi\DI\OpenApiPlugin:
            files:
                - %appDir%/openApi/petstore.neon
                - %appDir%/openApi/petstoreExtended.yaml
                - %appDir%/openApi/petstoreAdmin.json
```

Supported types are `neon`, `yaml` and `json`.

#### OpenApi-Attributes

This definition comes from core, but use only `OpenApi` attribute.

```php
use Apitte\Core\Annotation\Controller\Method;
use Apitte\Core\Annotation\Controller\OpenApi;
use Apitte\Core\Annotation\Controller\Path;
use Apitte\Core\Annotation\Controller\RequestParameter;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use Apitte\Negotiation\Http\ArrayEntity;

#[Path("/")]
#[OpenApi(<<<'EOT'
    openapi: '4.0.3'
    info:
       title: Defined by controller attribute
       version: '1.0.0'
EOT)]
final class HomeController extends BaseV1Controller
{

    #[Path("/")]
    #[Method("GET")]
    #[OpenApi(<<<'EOT'
        summary: Defined specific endpoint
        operationId: listPets
        tags:
           - pets
        parameters:
            -
                name: limit
                in: query
                description: 'How many items to return at one time (max 100)'
                required: false
                schema:
                    type: integer
                    format: int32
        responses:
            '200':
                description: A paged array of pets
                headers:
                    x-next:
                        description: A link to the next page of responses
                        schema:
                            type: string
                content:
                    application/json:
                        schema:
                            $ref: '#/components/schemas/Pets'
            default:
                description: unexpected error
                content:
                    application/json:
                        schema:
                            $ref: '#/components/schemas/Error'
    EOT)]

    public function index(ApiRequest $request, ApiResponse $response): ApiResponse
    {
        return $response->withEntity(ArrayEntity::from(['data' => ['Welcome']]));
    }

}
```

#### Core-Attributes

This definition is based on PHP attributes, which are parts of core.

```php
use Apitte\Core\Annotation\Controller\Method;
use Apitte\Core\Annotation\Controller\Path;
use Apitte\Core\Annotation\Controller\RequestBody;
use Apitte\Core\Annotation\Controller\Response;
use Apitte\Core\Annotation\Controller\Tag;
use Apitte\Core\Http\ApiRequest;

#[Path("/users")]
#[Tag("User")]
final class UserDetailController extends BaseV1Controller
{

    #[Path("/create")]
    #[Method("POST")]
    #[RequestBody(required: true, description: "Sample request")]
    #[Response(code: "200", description: "Success")]
    #[Response(code: "404", description: "Not found")]
    public function detail(ApiRequest $request): array
    {
        return [];
    }

}
```

#### Entity-Attributes

Same as CoreDefinition but it use `entity` in `RequestBody` & `Response` attribute.

```php
use Apitte\Core\Annotation\Controller\Method;
use Apitte\Core\Annotation\Controller\Path;
use Apitte\Core\Annotation\Controller\RequestBody;
use Apitte\Core\Annotation\Controller\Responses;
use Apitte\Core\Annotation\Controller\Response;
use Apitte\Core\Http\ApiRequest;

#[Path("/users")]
final class UserDetailController extends BaseV1Controller
{

    #[Path("/create")]
    #[Method("POST")]
    #[RequestBody(required: true, description: "Sample request", entity: App\Controllers\Entity\User::class)]
    #[Response(code: "200", description: "Success", entity: App\Controllers\Entity\User::class)]
    #[Response(code: "404", description: "Not found")]
    public function detail(ApiRequest $request): array
    {
        return [];
    }

}
```

Entity is loaded by reflection, it loads all public properties using `EntityAdapter`.

You can redefine entity adapter by interface.


#### Custom Definition

If you need, you can add your definition using `IDefinition` interface.

### Tracy SwaggerUI Panel

You can configure Swagger UI with a few optional parameters.

```yaml
api:
    plugins:
        Apitte\OpenApi\DI\OpenApiPlugin:
            swaggerUi:
                panel: %debugMode% #activate Tracy panel in debug mode
                url: null # default url
                expansion: list # list|full|none
                filter: true # true|false|string
                title: My API v2
```

### OpenApi Controller Endpoint

You will probably need to provide your open api schema outside from your app.

Create controller for this case.

```php
use Apitte\Core\Annotation\Controller\Method;
use Apitte\Core\Annotation\Controller\Path;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use Apitte\Core\UI\Controller\IController;
use Apitte\OpenApi\ISchemaBuilder;

#[Path("/openapi")]
final class OpenApiController implements IController
{

    public function __construct(
        private readonly ISchemaBuilder $schemaBuilder,
    )
    {
    }

    #[Path("/")]
    #[Method("GET")]
    public function index(ApiRequest $request, ApiResponse $response): ApiResponse
    {
        $openApi = $this->schemaBuilder->build();
        return $response->writeJsonBody($openApi->toArray());
    }

}
```
