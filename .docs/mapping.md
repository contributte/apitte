# Mapping

Validate and map data from request and map data to response

- [RequestParameters](#requestparameters) - validate and convert path, query, header and cookie parameters
- [RequestBody](#requestbody) - map request body (or query parameters) to an entity and validate it
- [Response entities](#response-entities) - describe responses by entities

## Setup

```yaml
api:
    plugins:
        Apitte\Core\DI\Plugin\CoreMappingPlugin:
```

Ensure you have also [decorator plugin](decorators.md#setup) registered, mapping is implemented by decorators.

## RequestParameters

Validate request parameters and convert them to correct php datatype.

Do you remember [UsersController](endpoints.md#controllers) example?
Imagine that you need add new endpoint to get an user by its numeric ID.
You will probably want validate that ID and cast it to integer.
That is what request parameters are used for.

```php
namespace App\Api\V1\Controllers;

use Apitte\Core\Annotation\Controller\Method;
use Apitte\Core\Annotation\Controller\Path;
use Apitte\Core\Annotation\Controller\RequestParameter;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;

#[Path("/users")]
class UsersController extends BaseV1Controller
{

    #[Path("/{id}")]
    #[Method("GET")]
    #[RequestParameter(name: "id", type: "int", description: "My favourite user ID")]
    public function detail(ApiRequest $request): ApiResponse
    {
        /** @var int $id Perfectly valid integer */
        $id = $request->getParameter('id');

        // Return response with error or user
    }

}
```

### Options

`#[RequestParameter()]` have few available options.

- `name="nameOfParameter"` - same as name of parameter in path, query...
- `type="string|int|float|bool|datetime"` - data type, see [data types](#data-types)
- `description={string}` - description of parameter, used in [openapi](schema.md#openapi) schema
- `in="path|query|header|cookie"` - whether parameter is located in url path, url query is a http header or cookie, default is path
- `required=true|false` - required by default, but you could make parameter optional
- `deprecated=true|false` - used in [openapi](schema.md#openapi) schema
- `allowEmpty=true|false` - make parameter nullable
- `enum={"only", "these", "values"}` - allowable values

### Data types

- `string`
    - Simply returns given value.
- `int`
    - Converts value to int.
    - Could overflow to float if value is bigger than PHP could handle.
- `float`
    - Converts value to float.
    - Accepts values which have decimals divided by comma `,` or dot `.`
- `bool`
    - Converts `'true'` to `true`
    - and `'false'` to `false`
- `datetime`
    - Converts value to DateTimeImmutable.
- `enum`
    - Checks that value is one of the values listed in `enum` option.


- You can define [custom data types](#custom-datatypes)


- Each of the data types could return null if request parameter is allowed to be empty
- If conversion is not possible (because data type is invalid) then API returns HTTP 400

### Overriding datatypes

You could override each datatype with your own implementation
(e.g. if you need handle bigger numbers then PHP could handle in native integer and float)

```yaml
api:
    plugins:
        Apitte\Core\DI\Plugin\CoreMappingPlugin:
            types:
                string: Apitte\Core\Mapping\Parameter\StringTypeMapper
                int: Apitte\Core\Mapping\Parameter\IntegerTypeMapper
                float: Apitte\Core\Mapping\Parameter\FloatTypeMapper
                bool: Apitte\Core\Mapping\Parameter\BooleanTypeMapper
                datetime: Apitte\Core\Mapping\Parameter\DateTimeTypeMapper
                enum: Apitte\Core\Mapping\Parameter\EnumTypeMapper
```

### Custom datatypes

You can also add your custom data types.

```yaml
api:
    plugins:
        Apitte\Core\DI\Plugin\CoreMappingPlugin:
            types:
                email: MyEmailTypeMapper
```

```php
use Apitte\Core\Mapping\Parameter\ITypeMapper;
use Apitte\Core\Exception\Runtime\InvalidArgumentTypeException;

class MyEmailTypeMapper implements ITypeMapper
{

	/**
	 * @param array<string, mixed> $options
	 */
	public function normalize(mixed $value, array $options = []): string
	{
		if (is_string($value) && filter_var($value, FILTER_VALIDATE_EMAIL)) {
			return $value;
		}

		throw new InvalidArgumentTypeException('email', 'Pass valid email address.');
	}

}

```

## RequestBody

Imagine you have a data grid with many filter options. You can describe all options manually or
use value object, entity, for it. And it leads us to `#[RequestBody]`.

We have an entity with described fields.

```php
namespace App\Api\Entity\Request;

use Apitte\Core\Mapping\Request\BasicEntity;

final class UserFilter extends BasicEntity
{

    public int $userId;

    public ?string $email = null;

}
```

Request body is mapped for `POST`, `PUT` and `PATCH` requests (JSON body), query parameters are mapped for `GET`, `DELETE` and `HEAD` requests.
Properties are filled by their names, values which cannot be assigned to typed properties are skipped, so use a [validator](#validators) to reject invalid requests.

You can also implement `Apitte\Core\Mapping\Request\IRequestEntity` to map the request in your own way (e.g. strictly, with your own error format).

And some endpoint with `#[RequestBody]` attribute. There's a method `ApiRequest::getEntity()`, it gets
the entity from request attributes. So simple, right?

```php
use Apitte\Core\Annotation\Controller\Method;
use Apitte\Core\Annotation\Controller\Path;
use Apitte\Core\Annotation\Controller\RequestBody;

#[Path("/filter")]
#[Method("GET")]
#[RequestBody(entity: App\Api\Entity\Request\UserFilter::class)]
public function filter(ApiRequest $request): ApiResponse
{
    /** @var UserFilter $entity */
    $entity = $request->getEntity();
}
```

The entity is also used to generate the request body schema in [OpenAPI](openapi.md#entity-attributes).

### Validators

Validate request entities with validator.

By default no validator is used.

#### BasicValidator

Supports `@required` phpdoc tag - checks that the property of `BasicEntity` is not empty

```yaml
api:
    plugins:
        Apitte\Core\DI\Plugin\CoreMappingPlugin:
          request:
            validator: Apitte\Core\Mapping\Validator\BasicValidator
```

#### SymfonyValidator

Install [contributte/validator](https://github.com/contributte/validator), it integrates [symfony/validator](https://symfony.com/doc/current/validation.html) into Nette DI.
It is an optional dependency, constraints are read from PHP attributes.

```bash
composer require contributte/validator
```

```yaml
api:
    plugins:
        Apitte\Core\DI\Plugin\CoreMappingPlugin:
          request:
            validator: Apitte\Core\Mapping\Validator\SymfonyValidator()
```

Using SymfonyValidator your request entity could look like this:

```php
use Apitte\Core\Mapping\Request\BasicEntity;
use Symfony\Component\Validator\Constraints as Assert;

final class UserFilter extends BasicEntity
{

    #[Assert\NotNull]
    #[Assert\Positive]
    public int $userId;

    #[Assert\Email]
    #[Assert\Length(max: 255)]
    public ?string $email = null;

}
```

Violations are thrown as `Apitte\Core\Exception\Api\ValidationException` (HTTP 422) with violation messages grouped by property paths in the context.
Implement `Apitte\Core\Mapping\Validator\IEntityValidator` if you need a different error format.

Constraints are also described in generated [OpenAPI](openapi.md#entity-attributes) schemas (e.g. `maxLength`, `pattern`, `format`).

You can override `ConstraintValidatorFactory` on `SymfonyValidator`. Use `ContainerConstraintValidatorFactory` from contributte/validator
if your [custom validation constraints](https://symfony.com/doc/current/validation/custom_constraint.html) need services from Nette DI.
If you want to use translated constraint messages, you can use [contributte/translation](https://github.com/contributte/translation).
Take a look at example.

```yaml
services:
    symfonyValidator:
        factory: Apitte\Core\Mapping\Validator\SymfonyValidator
        setup:
            - setConstraintValidatorFactory(Contributte\Validator\ContainerConstraintValidatorFactory())
            - setTranslator(@Contributte\Translation\Translator)
            - setTranslationDomain('validators')
            - setGroups(['input'])
api:
    plugins:
        Apitte\Core\DI\Plugin\CoreMappingPlugin:
            request:
                validator: @symfonyValidator
```

## Response entities

Responses could be described by entities too. Use `entity` option of `#[Response]` attribute,
the entity is used to generate the response schema in [OpenAPI](openapi.md#entity-attributes).
Append `[]` to describe a list of entities.

```php
use Apitte\Core\Annotation\Controller\Method;
use Apitte\Core\Annotation\Controller\Path;
use Apitte\Core\Annotation\Controller\Response;

#[Path("/users")]
#[Method("GET")]
#[Response(description: "List of users", code: "200", entity: App\Api\Entity\Response\User::class . '[]')]
public function list(ApiRequest $request, ApiResponse $response): ApiResponse
{
    return $response->writeJsonBody(array_map(User::fromEntity(...), $this->users->findAll()));
}
```

Returned `Apitte\Core\Mapping\Response\IResponseEntity` objects are converted by [negotiation](negotiation.md).
