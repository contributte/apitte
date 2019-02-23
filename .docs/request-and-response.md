# Request and response

We use [guzzle](https://github.com/guzzle/psr7) integration of [PSR-7](https://www.php-fig.org/psr/psr-7/)
so read the [PSR-7 docs](https://www.php-fig.org/psr/psr-7/) first please it contains almost everything you need to know.

Instead of `Psr\Http\Message\ServerRequestInterface` and `Psr\Http\Message\ResponseInterface`
use `Apitte\Core\Http\ApiRequest` and `Apitte\Core\Http\ApiResponse` in type hints. They add some additional methods.

## Response adjusters

Simple static helpers which help you with frequently used response modifications

### FileResponseAdjuster

- Sets headers required to recognize file properly
- Disables caching of given response
- See [guzzle/psr7 docs](https://github.com/guzzle/psr7) for available streams

```php
use Apitte\Core\Adjuster\FileResponseAdjuster;
use Contributte\Psr7\Psr7ResponseFactory;
use function GuzzleHttp\Psr7\stream_for;

$response = Psr7ResponseFactory::fromGlobal();
$stream = stream_for(fopen('/tmp/test.json', 'r+'));
$response = FileResponseAdjuster::adjust($response, $stream, 'filename.json', 'application/json');
```

