# Apitte Core

Core library of Apitte API framework

[![Build Status](https://img.shields.io/travis/apitte/core.svg?style=flat-square)](https://travis-ci.org/apitte/core)
[![Code coverage](https://img.shields.io/coveralls/apitte/core.svg?style=flat-square)](https://coveralls.io/r/apitte/core)
[![Licence](https://img.shields.io/packagist/l/apitte/core.svg?style=flat-square)](https://packagist.org/packages/apitte/core)
[![Downloads this Month](https://img.shields.io/packagist/dm/apitte/core.svg?style=flat-square)](https://packagist.org/packages/apitte/core)
[![Downloads total](https://img.shields.io/packagist/dt/apitte/core.svg?style=flat-square)](https://packagist.org/packages/apitte/core)
[![Latest stable](https://img.shields.io/packagist/v/apitte/core.svg?style=flat-square)](https://packagist.org/packages/apitte/core)
[![PHPStan](https://img.shields.io/badge/PHPStan-enabled-brightgreen.svg?style=flat-square)](https://github.com/phpstan/phpstan)

```php
/**
 * @ControllerPath("/hello")
 */
final class HelloController implements IController
{

    /**
     * @Path("/world")
     * @Method("GET")
     */
    public function index(ApiRequest $request, ApiResponse $response): ApiResponse
    {
        return $response->writeBody('Hello world!');
    }

}
```

## Discussion / Help

[![Join the chat](https://img.shields.io/gitter/room/apitte/apitte.svg?style=flat-square)](http://bit.ly/apittegitter)

## Version

| State       | Version      | Branch   | PHP      | Composer                                        |
|-------------|--------------|----------|----------|-------------------------------------------------|
| development | `^0.5.0`     | `master` | `>= 7.1` | `minimum-stability: dev`, `prefer-stable: true` |
| stable      | `^0.4.0`     | `master` | `>= 7.1` |                                                 |
| stable      | `^0.3.0`     | `master` | `>= 5.6` |                                                 |

## Documentation

Need to start with Apitte
- [Setup](.docs/setup.md)
- [Endpoints](.docs/endpoints.md)
- [Mapping](.docs/mapping.md)

This knowledge could make your life easier
- [Architecture](.docs/architecture.md)
- [Decorators](.docs/decorators.md)
- [Dispatcher](.docs/dispatcher.md)
- [Errors](.docs/errors.md)
- [Request and response](.docs/request-and-response.md)
- [Router](.docs/router.md)
- [Schema](.docs/schema.md)

Playground with example applications
- [Examples](https://github.com/apitte/playground)

## Maintainers

<table>
  <tbody>
    <tr>
      <td align="center">
        <a href="https://github.com/f3l1x">
            <img width="150" height="150" src="https://avatars2.githubusercontent.com/u/538058?v=3&s=150">
        </a>
        </br>
        <a href="https://github.com/f3l1x">Milan Felix Šulc</a>
      </td>
      <td align="center">
        <a href="https://github.com/mabar">
            <img width="150" height="150" src="https://avatars0.githubusercontent.com/u/20974277?s=150&v=4">
        </a>
        </br>
        <a href="https://github.com/mabar">Marek Bartoš</a>
      </td>
    </tr>
  </tbody>
</table>

## Sponsoring

<a href="https://github.com/tlapnet"><img  width="200" src="https://cdn.rawgit.com/f3l1x/xsource/2463efb7/assets/tlapdev.png"></a>

The development is sponsored by [Tlapnet](https://www.tlapnet.cz)
