# Mapping

Validate and map data from request and map data to response

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
use Apitte\Core\Annotation\Controller\RequestParameters;
use Apitte\Core\Annotation\Controller\RequestParameter;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;

/**
 * @Path("/users")
 */
class UsersController extends BaseV1Controller
{

    /**
     * @Path("/{id}")
     * @Method("GET")
     * @RequestParameters({
     *      @RequestParameter(name="id", type="int", description="My favourite user ID")
     * })
     */
    public function detail(ApiRequest $request): ApiResponse
    {
        /** @var int $id Perfectly valid integer */
        $id = $request->getParameter('id');

        // Return response with error or user
    }

}
```

### Options

`@RequestParameter()` have few available options.

- `name="nameOfParameter"` - same as name of parameter in path, query...
- `type="string|int|float|bool|datetime"` - data type, see [data types](#data-types)
- `description={string}` - description of parameter, used in [openapi](schema.md#openapi) schema
- `in="path|query|header|cookie"` - whether parameter is located in url path, url query is a http header or cookie, default is path
- `required=true|false` - required by default, but you could make parameter optional
- `deprecated=true|false` - used in [openapi](schema.md#openapi) schema
- `allowEmpty=true|false` - make parameter nullable

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

	public function normalize($value): string
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
use value object, entity, for it. And it leads us to `@RequestBody`.

We have an entity with described fields.

```php
namespace App\Api\Entity\Request;

use Apitte\Core\Mapping\Request\BasicEntity;

final class UserFilter extends BasicEntity
{

    /**  @var int */
    public $userId;

    /**  @var string */
    public $email;

}
```

And some endpoint with `@RequestBody` annotation. There's a method `ApiRequest::getEntity()`, it gets
the entity from request attributes. So simple, right?

```php
/**
 * @Path("/filter")
 * @Method("GET")
 * @RequestBody(entity="App\Api\Entity\Request\UserFilter")
 */
public function filter(ApiRequest $request)
{
    /** @var UserFilter $entity */
    $entity = $request->getEntity();
}
```

### Validators

Validate request entities with validator.

By default no validator is used.

#### BasicValidator

Supports @required annotation - check if attribute is not empty

```yaml
api:
    plugins:
        Apitte\Core\DI\Plugin\CoreMappingPlugin:
          request:
            validator: Apitte\Core\Mapping\Validator\BasicValidator
```

#### SymfonyValidator

Requires a doctrine annotation reader, you may use [nettrine/annotations](https://github.com/contributte/doctrine-annotations)

Also install [symfony/validator](https://symfony.com/doc/master/validation.html)

```yaml
extensions:
  annotations: Nettrine\Annotations\DI\AnnotationsExtension

api:
    plugins:
        Apitte\Core\DI\Plugin\CoreMappingPlugin:
          request:
            validator: Apitte\Core\Mapping\Validator\SymfonyValidator()
```

Using SymfonyValidator your request entity could look like this:

```php
use Apitte\Core\Mapping\Request\BasicEntity;

final class UserFilter extends BasicEntity
{

    /**
     * @var int
     * @Assert\NotNull()
     * @Assert\Type(
     *     type="integer",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    public $userId;

}
```

You can override `ConstraintValidatorFactory` on `SymfonyValidator`. If you want to use [custom validation contstraints](https://symfony.com/doc/current/validation/custom_constraint.html) with support of Nette DI,
you should also install [contributte/validator](https://github.com/contributte/validator). Take a look at example.

```yaml
services:
    symfonyValidator:
        factory: Apitte\Core\Mapping\Validator\SymfonyValidator
        setup:
            - setConstraintValidatorFactory(Contributte\Validator\ContainerConstraintValidatorFactory())
api:
    plugins:
        Apitte\Core\DI\Plugin\CoreMappingPlugin:
            request:
                validator: @symfonyValidator
```
