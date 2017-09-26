# Apitte\Core

:wrench: Core API library for [`Nette Framework`](https://github.com/nette/).

-----

[![Build Status](https://img.shields.io/travis/apitte/core.svg?style=flat-square)](https://travis-ci.org/apitte/core)
[![Code coverage](https://img.shields.io/coveralls/apitte/core.svg?style=flat-square)](https://coveralls.io/r/apitte/core)
[![Licence](https://img.shields.io/packagist/l/apitte/core.svg?style=flat-square)](https://packagist.org/packages/apitte/core)

[![Downloads this Month](https://img.shields.io/packagist/dm/apitte/core.svg?style=flat-square)](https://packagist.org/packages/apitte/core)
[![Downloads total](https://img.shields.io/packagist/dt/apitte/core.svg?style=flat-square)](https://packagist.org/packages/apitte/core)
[![Latest stable](https://img.shields.io/packagist/v/apitte/core.svg?style=flat-square)](https://packagist.org/packages/apitte/core)

## Discussion / Help

[![Join the chat](https://img.shields.io/gitter/room/apitte/apitte.svg?style=flat-square)](http://bit.ly/apittegitter)

## Install

```
composer require apitte/core
```

## Version

| State       | Version      | Branch   | PHP      |
|-------------|--------------|----------|----------|
| development | `dev-master` | `master` | `>= 5.6` |

## Example 

Just define some endpoints, I mean controllers. Place some annotations and thats all.

```php
namespace App\Controllers;

use Apitte\Core\Annotation\Controller\Controller;
use Apitte\Core\Annotation\Controller\Method;
use Apitte\Core\Annotation\Controller\Path;
use Apitte\Core\Annotation\Controller\RootPath;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use Apitte\Core\UI\Controller\IController;

/**
 * @Controller
 * @RootPath("/hello")
 */
final class HelloController implements IController
{

    /**
     * @Path("/world")
     * @Method("GET")
     */
    public function index(ApiRequest $request, ApiResponse $response)
    {
        return $response->writeBody('Hello world!');
    }
}
```

And register your controller as service.

```yaml
services:
    - App\Controllers\HelloController
```

As you can see, the architecture is ultra simple. `ApiRequest` & `ApiResponse` wrap PSR-7, so you can re-use these declared methods.

## Overview

- [Installation - how to register an extension](https://github.com/apitte/core/tree/master/.docs#installation)
- [Configuration - all options](https://github.com/apitte/core/tree/master/.docs#configuration)
- [Usage - controller showtime](https://github.com/apitte/core/tree/master/.docs#usage)
- [Plugins - apitte plugins](https://github.com/apitte/core/tree/master/.docs#plugins)
- [Advanced - complex configuration](https://github.com/apitte/core/tree/master/.docs#advanced)
- [Playground - real examples](https://github.com/apitte/core/tree/master/.docs#playground)

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
    </tr>
  <tbody>
</table>

-----

The development is sponsored by [Tlapnet](http://www.tlapnet.cz) and a lot of coffeees. Thank you guys! :+1:

-----

Thank you for testing, reporting and contributing.
