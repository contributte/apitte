# Dispatcher

Dispatcher is a front controller of whole api.

Its responsibility is matching request via [router](router.md), invoking [endpoint](endpoints.md) through handler and returning response from that endpoint.

Also sends 404 response if request is not matched with an endpoint.

## CoreDispatcher

Basic implementation of dispatcher - do exactly what is described in [the previous section](#dispatcher).

## DecoratedDispatcher

Adds decorators for advanced handling of request and response transformations.

For more information see chapter about [decorators](decorators.md)

## JsonDispatcher

Adds possibility to return scalar or array from endpoint - raw data are transformed into response with status 200 and Content-Type application/json.

Used by default (but could be overriden by decorator plugin)

> Incompatible with [decorators](decorators.md), alternative solution is available in [apitte/negotiation transformers](https://github.com/apitte/negotiation)

## WrappedDispatcher

Wraps whichever dispatcher is in usage
- to handle all errors through [error handler](errors.md#error-handler)
- to ensure that ApiRequest and ApiResponse implementations of PSR7 are passed into deeper layers
