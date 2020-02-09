---
extends: _layouts.post
section: content
title: "Colecciones: Mensajes de orden superior"
date: 2020-02-08
description: "Muchos de los métodos de las colecciones de Laravel ofrecen atajos para realizar acciones comunes sobre los elementos de estos. Descubre cómo usarlos."  
cover_image: /assets/images/posts/0016/colecciones-mensajes-de-orden-superior.png
featured: false
categories: [laravel, php, programming, tips]
---

Muchos de los métodos de las colecciones de Laravel ofrecen atajos para realizar acciones comunes sobre los elementos de estos. Descubre cómo usarlos.

-----

Acá un pequeño tip que probablemente hayas pasado por encima. Desde [Laravel 5.4](https://laravel.com/docs/5.4/collections#higher-order-messages), Laravel ofrece la posibilidad de emplear "atajos" en varios de los métodos de la clase Collection para poder aplicarles acciones comunes. Veamos unos cuántos ejemplos.

Imaginemos que tenemos el modelo `User`, y queremos ejecutar una operación sobre todos los resultados de una consulta. Por ejemplo, enviar una notificación a cada uno acerca de las normas de la empresa.. pero solo a los que no han sido notificados aún. Para ejecutar una acción sobre todos los usuarios, podríamos emplear [`each()`](https://laravel.com/docs/collections#method-filter): 

Tradicionalmente, harías hacer esto:

    $users = User::where('notified', false)->get();
    
    $users->each(function ($user) {
        $user->sendTermsNotifications();
    });
    
Pero utilizando [Mensajes de Orden Superior](https://laravel.com/docs/collections#higher-order-messages), podrías reducir el código anterior a lo siguiente:

    $users = User::where('notified', false)->get();
        
    $users->each->sendTermsNotifications(); // <--

Una sintaxis más corta que hace exactamente lo mismo. Veamos otro ejemplo.

Los trabajadores retirados de nuestra empresa reciben un beneficio de 1000 dólares. Entonces, imaginando que ya tenemos en memoria una colección con todos los trabajadores (`$employees`), probablemente lo que haríamos (para evitar una consulta adicional a la base de datos), sería filtrar esta colección con [`filter()`](https://laravel.com/docs/collections#method-filter) -para obtener solo a los trabajadores retirados- y luego otorgarle este beneficio a cada uno de ellos. Para hacer esto podríamos hacer lo siguiente:

    $employees->filter(function($employee) {
        return $employee->is_retired;
    })->each(function($retired) {
        $retired->getBenefit(1000);
    });

Sin embargo, podríamos reducirlo a lo siguiente:

    $employees->filter->is_retired->each->getBenefit(1000);
    
Cool, ¿cierto? 👌. Un último ejemplo.

Imaginemos que queremos obtener dos colecciones, una que contenga a los administradores de nuestro foro y otra a los que no. ¿Cómo haríamos esto con nuestra colección de usuarios? Pues, una solución es utilizar el método [`partition()`](https://laravel.com/docs/collections#method-partition):

    list($moderators, $nonModerators) = $users->partition(function($user) {
        return $user->is_moderator;
    });

Utilizando lo aprendido hoy, podrías reducirlo con lo siguiente:

    list($moderators, $nonModerators) = $users->partition->is_moderator;

Este tipo de atajos puede ser empleado con los métodos `average`, `avg`, `contains`, `each`, `every`, `filter`, `first`, `flatMap`, `groupBy`, `keyBy`, `map`, `max`, `min`, `partition`, `reject`, `some`, `sortBy`, `sortByDesc`, `sum`, y `unique`.

---

## Cierre

Si bien en estos ejemplos he utilizado los métodos en resultados de consultas vía Eloquent, puedes aplicarlos en cualquier colección que desees, por ejemplo `collect(['este', 'es', 'mi', 'cool', 'arreglo'])`. Adicionalmente, algunos de los ejemplos que usé pueden ser resueltos de manera más fácil/eficiente con otro método, sin embargo, sirven para ilustrar lo que intento compartir. Espero te sirva en el futuro 🤘😉.
