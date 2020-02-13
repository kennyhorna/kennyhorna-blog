---
extends: _layouts.post
section: content
title: "Comprendiendo al helper tap()"
date: 2020-02-13
description: "El método `tap()` es muy útil pero puede que lo hayas pasado por encima o no comprendas del todo sobre cómo funciona. Descúbrelo hoy mediante algunos ejemplos prácticos."  
cover_image: /assets/images/posts/0018/0018-comprendiendo-al-helper-tap.png
featured: true
categories: [laravel, php, programming, tips]
---

El método `tap()` es muy útil pero puede que lo hayas pasado por encima o no comprendas del todo sobre cómo funciona. Descúbrelo hoy mediante algunos ejemplos prácticos.

-----

### El helper `tap()`

Laravel 5.3 introdujo este útil método que básicamente sirve para ejecutar una acción sobre un valor y devolver este valor evitando la necesidad de emplear variables temporales. Puede que no tenga mucho sentido descrito de esa manera.. pero vayamos con un ejemplo. Imaginemos que queremos actualizar el título de un post en específico desde el controlador. Normalmente haríamos lo siguiente:

    $post = Post::find(25);
    $post->update(['title' => 'Mi nuevo título']);
    
    return $post->fresh();

¿Cómo podríamos hacer utilizando `tap()`?, pues:

    return tap(Post::find(25), function ($post)
        $post->update(['title' => 'Mi nuevo título']);
    );

Entonces, **¿Cuál es realmente el beneficio?** Ahorrarnos el crear una variable temporal para almacenar `$post`. Si es que nos fijamos, en el primer argumento colocamos `Post::find(25)` y este se pasará al callback que estamos pasando como segundo argumento. Lo interesante es que en este segundo método podemos hacer cualquier operación pues siempre se retornará el valor del primer argumento.

### ¿Cómo funciona?

Analicemos como funciona esto, ignoremos las líneas comentadas por el momento, luego explicaremos estas:

     function tap($value, $callback = null)
     {         
         // if (is_null($callback)) {
         //    return new HigherOrderTapProxy($value);
         // }
 
         $callback($value);
 
         return $value;
     }
     
Como puedes ver, simplemente toma la función `$callback` que pasamos como segundo parámetro y lo aplica sobre la variable que pasamos como primer parámetro, retornando finalmente esta variable en cuestión.

### Mensaje de orden superior

Ahora, ¿qué son esas líneas comentadas? Lo que hacen es verificar si es que el segundo parámetro es `null`:

    function tap($value, $callback = null)
     {         
         if (is_null($callback)) {                  // <--
            return new HigherOrderTapProxy($value); // <--
         }                                          // <--
 
         $callback($value);
 
         return $value;
     }
 
 Si esto es así, se retornará uan instancia de la clase `HigherOrderTapProxy` (Laravel 5.4+) que no es más que una implementación de los **mensajes de orden superior** que ya habíamos visto en las colecciones en [el artículo anterior](/blog/0016-colecciones-mensajes-de-orden-superior). Entonces, aplicando esto a nuestro ejemplo inicial veremos que podemos simplificarlo a lo siguiente:

    return tap(Post::find(25))->update(['title' => 'Mi nuevo título']);
    
¿Interesante cierto? Principalmente porque la función `update()` retorna un booleano, sin embargo, dado que estamos usando `tap()` se retornará el valor inicial, por ende, una instancia de `Post` con `id=25`.

### Ejemplos de uso

A lo largo de su código fuente, Laravel lo implementa en muchos lugares. Lo podemos encontrar, por ejemplo, en el método `create()` de Eloquent: 

    public function create(array $attributes = [])
    {
        return tap($this->newModelInstance($attributes), function ($instance) {
            $instance->save();
        });
    }

Así es como lucía antes de implementar `tap()`:

    public function create(array $attributes = [])
    {
        $instance = $this->newModelInstance($attributes);
    
        $instance->save();
    
        return $instance;
    }

Otra aplicación de este útil helper es en el middleware `AuthenticationSession`:

    public function handle($request, Closure $next)
    {
        return tap($next($request), function () use ($request) {
            $this->storePasswordHashInSession($request);
        });
    }
    
Así es como lucía antes de `tap()`:

    public function handle($request, Closure $next)
    {
        $response = $next($request);
    
        $this->storePasswordHashInSession($request);
    
        return $response;
    }

Este caso es interesante pues aquí no se está efectuando ninguna operación sobre el valor pasado a la función `tap`. Esto nos dice que podemos hacer lo que deseemos dentro del `$callback` que pasamos a `tap`, aún con el beneficio de ahorrarnos el utilizar variables temporales (en este caso la de `$response`).

### tap() en colecciones

Este método también está disponible para las [colecciones](https://laravel.com/docs/collections#method-tap). Es particularmente útil aquí para efectuar operaciones en la colección sin necesidad de interrumpir el flujo de este. Veamos un ejemplo tomado de la documentación:

    collect([2, 4, 3, 1, 5])
        ->sort()
        ->tap(function ($collection) {
            Log::debug('Valores después de ordenar', $collection->values()->toArray());
        })
        ->shift();
    
    // 1
    
Como podemos apreciar, estamos incluyendo [`tap()`](https://laravel.com/docs/collections#method-tap) para _loggear_ los valores de la colección luego de haber sido ordenados. Esto no afecta para nada el resto del proceso que hemos detallado, en este caso la aplicación del método [`shift()`](https://laravel.com/docs/collections#method-shift).

------

## Cierre

Personalmente, considero que el empleo de métodos/formas/técnicas para la escritura de un código más eficiente y/o legible vale la pena implementar. Hay algunas personas que consideran a la implementación de `tap` como un paso atrás, pues aparentemente dificulta la legibilidad y hace la tarea más difícil para los IDEs; sin embargo, este no ha sido mi caso y espero poder aplicarlo cada vez que pueda.
