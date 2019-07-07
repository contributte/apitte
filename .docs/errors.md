# Errors

How to handle user and server errors

## Throwing errors

To display errors to user use our prepared `ApiException`, specifically:

- `ClientErrorException` for user errors (400-499)
- `ServerErrorException` for server errors (500-599)

Other errors are transformed into a generic exception (see [SimpleErrorHandler docs](#simpleerrorhandler) for more info)

> Known limitation:
> Currently could be returned this way only single error not mixed with data. This limitation will be solved in near future.
> As a workaround you can set exact response content you want, without any special handling inside apitte.

## Error handler

Error handler is responsible for catching all errors
and transforming them into response through [error converter](#error-converter)

> Known limitation: 
> Handler is currently contained in [WrappedDispatcher](dispatcher.md#wrappeddispatcher) so any errors thrown in [middlewares](https://github.com/apitte/middlewares) could not by handled by it.

### SimpleErrorHandler

Default error handler

- Transforms error into json response
  - ApiException (and inherited errors like ClientErrorException) message, context and code are used directly in response
  - For other (non-api) errors is used generic message
  - Context is send only if is not empty
- Allows rethrow error in debug mode (see catchException option in [setup](setup.md))

### PsrLogErrorHandler

Extends SimpleErrorHandler for logging of errors.

It is activated automatically if you have an autowired Psr\Log\LoggerInterface implementation in DI container (like [contributte/monolog](https://github.com/contributte/monolog) or PSR adapter of [tracy](https://github.com/nette/tracy/))

## Error converter

Error converter converts all errors into response.

### JsonErrorConverter

Default error converter which transforms all errors into json response

```json
{
  "status": "error",
  "code": 500,
  "message": "Application encountered an internal error. Please try again later.",
  "context": []
}
```

### Negotiation

[negotiation](https://github.com/apitte/negotiation) package uses own error converter to support multiple content types
