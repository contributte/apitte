# Negotiation

Content negotiation for Apitte.

Transform response entity into response with unified format in dependence on `Accept` header and uri path suffix `/api/v1/users(.json|.xml)`

## Setup

Install and register negotiation plugin.

```neon
api:
    plugins:
        Apitte\Negotiation\DI\NegotiationPlugin:
```

## Response

Instead of writing data into response body use `$response->withEntity($entity)` so transformers could handle transformation for you.

```php
namespace App\Api\V1\Controllers;

use Apitte\Core\Annotation\Controller\Method;
use Apitte\Core\Annotation\Controller\Path;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use Apitte\Negotiation\Http\ArrayEntity;

#[Path("/users")]
class UsersController extends BaseV1Controller
{

    #[Path("/")]
    #[Method("GET")]
    public function index(ApiRequest $request, ApiResponse $response): ApiResponse
    {
        $entity = ArrayEntity::from([
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
        ]);

        return $response
            ->withStatus(ApiResponse::S200_OK)
            ->withEntity($entity);
    }

}
```

## Entities

Value objects which are used to create response

- `ArrayEntity` - create from array
- `ObjectEntity` - create from stdClass
- `ScalarEntity` - create from raw data

## Error handling

Negotiations are implemented through an `IErrorDecorator`, which have higher priority than internal `ErrorHandler`
so response is created from exception in an `ITransformer` and `ErrorHandler` only log that exception (if you use `PsrLogErrorHandler`)

### Negotiators

Handle request and based on path suffix or request headers call appropriate transformer.

`SuffixNegotiator`

- used for request with path suffix like `/api/v1/users.json` -> transformer for `json` suffix is used

`DefaultNegotiator`

- called when none other transform
- require attribute `#[Negotiation(default = true, suffix = "json")]` defined on endpoint - transformer for given suffix is looked for

`FallbackNegotiator`

- used last if no other negotiator transformed response
- uses json transformer by default

### Transformers

Transformers convert entities and exceptions into response.

`JsonTransformer`

  - transform into json

`JsonUnifyTransformer`

  - transform into json with unified format

```neon
api:
    plugins:
        Apitte\Negotiation\DI\NegotiationPlugin:
            unification: true
```

`CsvTransformer`

  - transform into csv
  - known limitation: data need to be a flat structure

#### Implementing transformer

```neon
services:
    - factory: App\Api\Transformer\XmlTransformer
      tags: [apitte.negotiator.transformer: [suffix: xml, fallback: true]]
```

- register transformer for suffix `xml`, used for uris like `/api/v1/users.xml`
- if `fallback: true` is defined and none of transformers matched then use that transformer

```php
namespace App\Api\Transformer;

use Apitte\Core\Exception\ApiException;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use Apitte\Core\Http\ResponseAttributes;
use Apitte\Negotiation\Http\ArrayEntity;
use Apitte\Negotiation\Transformer\AbstractTransformer;
use Throwable;

class XmlTransformer extends AbstractTransformer
{

    /**
     * Encode given data for response
     *
     * @param mixed[] $context
     */
    public function transform(ApiRequest $request, ApiResponse $response, array $context = []) : ApiResponse
    {
        if (isset($context['exception'])) {
            return $this->transformError($context['exception'], $request, $response);
        }

        return $this->transformResponse($request, $response);
    }

    protected function transformResponse(ApiRequest $request, ApiResponse $response): ApiResponse
    {
        $data = $this->getEntity($response)->getData();
        $content = $this->dataToXmlString($data);
        $response->getBody()->write($content);

        return $response
            ->withHeader('Content-Type', 'application/xml');
    }

    protected function transformError(Throwable $error, ApiRequest $request, ApiResponse $response): ApiResponse
    {
    	if ($error instanceof ApiException) {
    		$code = $error->getCode();
    		$message = $error->getMessage();
    	} else {
    		$code = 500;
    		$message = 'Application encountered an internal error. Please try again later.';
    	}

        return $response
            ->withStatus($code)
            ->withAttribute(ResponseAttributes::ATTR_ENTITY, ArrayEntity::from([
                'status' => 'error',
                'message' => $message,
            ]));
    }

}
```
