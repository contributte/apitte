# Schema

Internally all annotations from controllers are used to build api schema.
It is basically metadata describing whole api.
Endpoints, routing, openapi, ...

## OpenApi

Apitte api schema is used to generate [OpenApi](https://github.com/OAI/OpenAPI-Specification) schema.

You could also define OpenApi schema directly if you need define things that are not directly supported by apitte schema.

See [apitte/openapi docs](https://github.com/apitte/openapi) for more info.

## Loaders

Loads api schema

> Known limitation:
> Currently is not possible to define own loaders.

### DoctrineAnnotationsLoader

Default loader which loads schema from annotations.

See [endpoints](endpoints.md) and [mapping](mapping.md) docs for usage examples.

### NeonLoader

Use with caution. This is an experimental loader which may drastically change in future.
(So yes, it is not enough documented for that reason)

```yaml
api:
    plugins:
        Apitte\Core\DI\Plugin\CoreSchemaPlugin:
            enable: true
            files:
                - %appDir%/Api/V1/schema.neon
```

> Known limitation:
> Schema for endpoint could be defined only by annotations or by neon, so you cannot e.g. define endpoint in annotations and modify it in neon.

## Validation

There are already some validations which check if there is not an error in schema
but you could also define your own validations to e.g. ensure that all endpoints have defined an tag or ID.

```php
namespace App\Api\Validations;

use Apitte\Core\Schema\Builder\SchemaBuilder;use Apitte\Core\Schema\Validation\IValidation;

class EnsureTagIsDefinedValidation implements IValidation
{

    public function validate(SchemaBuilder $builder): void
    {
    	foreach ($builder->getControllers() as $controller) {
    		// Get tags from controller and controller methods, check if any available...
    	}
    }

}
```

```yaml
api:
    plugins:
        Apitte\Core\DI\Plugin\CoreSchemaPlugin:
            validations:
              - App\Api\Validations\EnsureTagIsDefinedValidation
```

