# XML RPC Bundle

[![Build Status](https://travis-ci.org/bdunogier/xmlrpcbundle.png)](https://travis-ci.org/bdunogier/xmlrpcbundle)

## Principle

This Symfony 2 bundle will let you implement an XML-RPC server without leaving the comfort of your gold old Symfony 2.

It does the following:
- intercept requests to the XML-RPC endpoint
- convert the XML payload to a unique route (based on the methodCall)
- convert the XML payload to a set of primitive parameters (arrays, hashes, integers, strings...)

## Usage example

Let's consider this XML-RPC payload:

```xml
<methodCall>
  <methodName>myAPI.getLatestContent</methodName>
  <params>
    <param>
      <value>Some string</value>
    </param>
  </params>
</methodCall>
```

To process this call, all you need to do is create a route that matches /xmlrpc/API.getLatestContent, and map a
controller to it, the usual way:

```yml
myroute.getLatestContent:
  path: /xmlrpc/myAPI.getLatestContent
  defaults:
    _controller: MyBundle:API:getLatestContent
```

And that's it. In your controller, you will receive a standard HttpFoundation Request, where each parameter is part
of the POST data.

An hello world example can be found inside the bundle itself.

## Parameters providers

By default, the route won't receive any parameter, and parameters from the XML-RPC request will be made available
as $_POST[0], $_POST[1], ...

In order to get more meaningful controllers, `ParametersProcessor` can be used.

A parameter processor must:
- implement `BD\Bundle\XmlRpcBundle\XmlRpc\ParametersProcessorInterface`
- be registered as a service tagged as `bdxmlrpc.parameters_processor`, with a `methodName` attribute matching
  the XML-RPC method

The interface requires two methods: `getRoutePathArguments` and `getParameters`. Both will refine the request
arguments based on the contents of the `$parameters` array, a numerical array of the XML-RPC request parameters.

### `ParametersProcessorInterface::getRoutePathArgument()`

Returns route URI arguments, as a numerically indexed array. Each argument will be added to the route's path, separated
by slashes.

### `ParametersProcessorInterface::getParameters()`

This method must return an array, associative or not, of POST parameters that will be added to the request.