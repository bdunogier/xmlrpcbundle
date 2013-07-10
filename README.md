# XML RPC Bundle

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

An hello world example can be found in the bundle itself.
