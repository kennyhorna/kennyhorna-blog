---
extends: _layouts.post
section: content
title: "Novedades de Laravel 7: Cliente HTTP"
date: 2020-02-28
description: "Como en cada nueva versión, Laravel 7 nos trae una serie de nuevas funciones y mejoras. En este artículo destacaremos la integración de Laravel con Zttp bajo el nuevo facade HTTP."  
cover_image: /assets/images/posts/0023/0023-novedades-de-laravel-7-el-nuevo-facade-http-consultas-nativas-a-servicios-externos.png
featured: false
categories: [laravel, php, programming]
---

Como en cada nueva versión, Laravel 7 nos trae una serie de nuevas funciones y mejoras. En este artículo destacaremos la integración de Laravel con [Zttp](https://github.com/kitetail/zttp) -que a su vez es un wrapper de [Guzzle](https://github.com/guzzle/guzzle)- bajo el nuevo _facade_ **HTTP**.

------

Este artículo pertenece a la serie [Novedades de Laravel 7](/blog/0021-novedades-de-laravel-7). Puedes leer sobre el resto de las características introducidas en esta versión [aquí](/blog/0021-novedades-de-laravel-7).

-----

Esta nueva fachada **HTTP** no es nada más que un wrapper de la popular librería **Guzzle**, con una API más familiar e intuitiva. Ofrece también, una manera fácil de aplicarla en nuestras pruebas unitarias y de integración.

### Haciendo requests

Escribir una llamada hacia un endpoint exterior es tan simple como:

    use Illuminate\Support\Facades\Http;

    $response = Http::get('http://la-url-del-servicio.com'); 

Este método retornará una instancia de ``Illuminate\Http\Client\Response`` la cual proveerá una serie de métodos que te permitirán inspeccionar la respuesta.

    $response->body() : string;
    $response->json() : array;
    $response->status() : int;
    $response->ok() : bool;
    $response->successful() : bool;
    $response->serverError() : bool;
    $response->clientError() : bool;
    $response->header($header) : string;
    $response->headers() : array;

Esta clase también implementa la interfaz ``ArrayAccess``, por lo que puedes acceder a la data de la respuesta JSON directamente desde la respuesta:

    return Http::get('http://la-url-del-servicio.com/users/1')['phone_number'];
    //                                                         ^^^^^^^^^^^^^^
    
Cuando requieras enviar cierta información adicional al momento de realizar consulta de tipo ``POST``, ``PUT``, ``PATCH``, puedes realizarlo fácilmente pasando data como segundo argumento:

    $response = Http::post('http://la-url-del-servicio.com/users', [
        'name' => 'Kenny',
        'role' => 'Titán',
    ]);

> Con la misma facilidad, puedes explorar las opciones para hacer [Requests con data codificada en la Url](https://laravel.com/docs/master/http-client#request-data) y [Requests Multi-part](https://laravel.com/docs/master/http-client#request-data).

### Autenticación

El **_facade_ HTTP** también nos provee una manera cómoda de manejar la **autenticación de nuestras peticiones** cuando hagamos consultas a servicios externos. Para esto,  tenemos disponible tres formas de autenticación: Básica, _Digest_ y mediante un _Bearer_ token.

    // Basic auth
    $response = Http::withBasicAuth('kennyhorna@gmail.com', 'mi-clave')->post(...);
    
    // Digest auth
    $response = Http::withDigestAuth('kennyhorna@gmail.com', 'mi-clave')->post(...);
    
    // Mediante un token
    $response = Http::withToken('mi-token')->post(...);

### Cabeceras

Del mismo modo, podemos añadir cabeceras de esta forma:

    $response = Http::withHeaders([
        'X-una-cabecera' => 'valor-1',
        'X-otra-cabecera' => 'valor-2'
    ])->post('http://la-url-del-servicio.com/users', [
        'name' => 'Lilly Collins',
    ]);

### Manejo de errores

Una de las principales diferencias del uso del **_facade_ HTTP** y de Guzzle es que este último arroja una excepción si es que algo anda mal (errores ``4XX`` y ``5XX``). HTTP no lo hará por defecto, sino que te ofrece distintas formas de tratar los errores dependiendo de cómo quieras manejarlos. De este modo, podemos **consultar** a la respuesta de nuestra petición si es que todo marchó bien o si es que hubo algún error:

    // Determina si el código de respuesta es >= 200 y < 300...
    $response->successful();
    
    // Determina si el código de respuesta es de nivel 400...
    $response->clientError();
    
    // Determina si el código de respuesta es de nivel 500...
    $response->serverError();  

Si lo que prefieres es que se arroje una **excepción** (instancia de ``Illuminate\Http\Client\RequestException``), puedes realizar lo siguiente:

    $response = Http::post(...);
    
    // Arrojar una excepción si es que hubo algún error..
    $response->throw();
    
    return $response['data'];
    
> Puedes leer más sobre el manejo de errores en [esta](https://laravel.com/docs/master/http-client#error-handling) sección de la documentación.

 ### Pruebas
 
 Al igual que muchas funcionalidades de Laravel, esta no es la excepción. El **facade HTTP** nos permite **simular** estas peticiones externas y retornar **fake data**, para que podamos correr nuestras pruebas sin necesidad de realizar peticiones externas.
 
Para falsear una petición y respuesta, solo necesitamos invocar al método ``fake()`` antes de la lógica de nuestra prueba:

    use Illuminate\Support\Facades\Http;
    
    Http::fake();
    
    $response = Http::post(...);

Si es que necesitamos simular peticione a distintos servicios -los cuales deberían retornar distinta data- podemos indicarlo pasando un arreglo de elementos a este método ``fake``:

    Http::fake([
        // Falseamos peticiones hacia endpoints de Facebook...
        'facebook.com/*' => Http::response(['foo' => 'bar'], 200, ['Headers']),
    
        // Falseamos peticiones hacia endpoints de Twitter...
        'twitter.com/*' => Http::response('Hello World', 200, ['Headers']),
    ]);

> Puedes leer más sobre la **simulación de peticiones** [en esta sección](https://laravel.com/docs/master/http-client#faking-responses) de la documentación.

#### Inspeccionando respuestas

Si lo que necesitas es **asegurarte** de que estás enviando toda la información necesaria y adecuada en tus peticiones, puedes realizar estas validaciones mediante el método ``assetSent(...)`` (luego de haber invocado ``fake()``). Veamos un ejemplo.

    // Activando la simulación de peticiones..
    Http::fake();
    
    // Realizando nuestra consulta..
    Http::withHeaders([
        'X-First' => 'foo',
    ])->post('http://la-url-del-servicio.com/users', [
        'name' => 'Kenny',
        'role' => 'author',
    ]);
    
    // Verificando que se envió la data correcta..
    Http::assertSent(function ($request) {
        return $request->hasHeader('X-First', 'foo') &&
               $request->url() == 'http://la-url-del-servicio.com/users' &&
               $request['name'] == 'Kenny' &&
               $request['role'] == 'author';
    });


-----

## Cierre

La introducción del soporte de **peticiones HTTP** de manera **nativa** (si bien sea mediante un _wrapper_ de una de las librerías más utilizadas para estas tareas) hace más robusto aún a Laravel. Definitivamente la implementaré en mis proyectos, pues me atrae sobretodo la manera fácil de poder **integrarla a mis pruebas de software**. Ya ahondaremos en esto en una **futura serie dedicada a esta interesante práctica** ;).

Si te interesó esta nueva funcionalidad, no olvides leer **el resto de novedades que trae Laravel 7** ([aquí](/blog/0021-novedades-de-laravel-7)), ya que tenemos [una serie de artículos -"Novedades de Laravel 7"-](/blog/0021-novedades-de-laravel-7) dedicados a este tema. 
