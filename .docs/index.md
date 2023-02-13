# Setup

Register `Apitte` using `ApiExtension` to your Nette-based application.

```neon
# config.neon

extensions:
    api: Apitte\Core\DI\ApiExtension

api:
    debug: %debugMode%
    catchException: true # Sets if exception should be catched and transformed into response or rethrown to output (debug only)
```

After that, create entrypoint to your Nette-based application. For example `www/index.php` looks like that.

```php
// www/index.php

use Apitte\Core\Application\IApplication;
use App\Bootstrap;

require __DIR__ . '/../vendor/autoload.php';

Bootstrap::boot()
    ->createContainer()
    ->getByType(IApplication::class)
    ->run();
```

If you wanna combine Nette application and Apitte application together, `www/index.php` looks like that.

```php
// www/index.php

use Apitte\Core\Application\IApplication as ApiApplication;
use App\Bootstrap;
use Nette\Application\Application as UIApplication;

require __DIR__ . '/../vendor/autoload.php';

$isApi = substr($_SERVER['REQUEST_URI'], 0, 4) === '/api';
$container = Bootstrap::boot()->createContainer();

if ($isApi) {
    // Apitte application
    $container->getByType(ApiApplication::class)->run();
} else {
    // Nette application
    $container->getByType(UIApplication::class)->run();
}
```

## Plugins

### Prepared plugins

- Schema plugin
  - Core plugin (**enabled by default**) which manage building and validation of whole api schema.
  - See [schema](schema.md) chapter for more info.
- OpenApi plugin
  - [OpenApi](https://github.com/OAI/OpenAPI-Specification) integration with [Swagger UI](https://petstore.swagger.io) support.
  - See [openapi](openapi.md) chapter for more info.
- Mapping plugin
  - Validate request parameters, map request body to entity and entity to response body.
  - See [mapping](mapping.md) chapter for more info.
- Middleware plugin
  - PSR-7 request/response integration, a.k.a. middlewares. Based on [contributte/middlewares](https://github.com/contributte/middlewares).
  - See [middlewares](middlewares.md) chapter for more info.
- Decorator plugin
  - Decorate request and response objects (e.q. authentication/authorization).
  - See [decorators](decorators.md) chapter for more info.
- Negotiation plugin
  - Transforms data into format requested in `Accept` header and in url suffix (`/api/v1/users.xml`)
  - See [negotiation](negotiation.md) chapter for more info.
- Debug plugin
  - Debug api easily and display [Tracy debug bar](https://github.com/nette/tracy) along with dumped response data.
  - See [debug](debug.md) chapter for more info.
- Console plugin
  - Console commands for your api.
  - Based on [symfony/console](https://github.com/symfony/console)
  - See [console](console.md) chpater for more info.
- Presenter plugin
  - Route into your api through a single nette route and presenter.
  - See [presenter](presenter.md) chapter for more info.

### Custom plugin

```yaml
api:
    plugins:
        App\Api\Plugin\YourAmazingPlugin:
```

```php
namespace App\Api\Plugin;

use Apitte\Core\DI\Plugin\Plugin;

class YourAmazingPlugin extends Plugin
{

    public static function getName() : string
    {
        return 'pluginName';
    }

    // Add new services, override existing or whatever you want
    // Take a look at existing plugins for inspiration

}
```
