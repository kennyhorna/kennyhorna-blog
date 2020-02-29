---
extends: _layouts.post
section: content
title: "Novedades de Laravel 7: Mejoras en el enlazado de modelos a rutas"
date: 2020-02-28
description: "Como en cada nueva versión, Laravel 7 nos trae una serie de nuevas funciones y mejoras. En este artículo destacaremos las mejoras hacia el enlazado de modelos a rutas (Route Model Binding)."  
cover_image: /assets/images/posts/0022/0022-novedades-laravel-7-mejoras-en-el-enlazado-de-modelos-a-rutas.png
featured: false
categories: [laravel, php, programming]
---

Como en cada nueva versión, Laravel 7 nos trae una serie de nuevas funciones y mejoras. En este artículo destacaremos las mejoras hacia el **enlazado de modelos a rutas** (_Route Model Binding_).

------

Este artículo pertenece a la serie [Novedades de Laravel 7](/blog/0021-novedades-de-laravel-7). Puedes leer sobre el resto de las características introducidas en esta versión [aquí](/blog/0021-novedades-de-laravel-7).

-----

### Enlazado implícito de Modelos a Rutas

Desde versiones anteriores (v5.2+) Laravel nos da la posibilidad de **enlazar implícitamente modelos a parámetros de rutas** (_Route Model Binding_). Laravel realiza esto deduciendo el nombre del modelo desde la definición de la ruta en sí. 

Antes de la introducción de esta funcionalidad en aquella versión, teníamos que hacer más trabajo. Por ejemplo, para una ruta que muestre el detalle de una **tienda**, hubiéramos hecho:

    # routes/web.php
    
    Route::get('stores/{id}', 'StoreController@show');
    
Por lo que luego, tenías que buscar el objeto en cuestión inyectando el ``$id`` para finalmente hacer la consulta directamente en el controlador:

    # app/Http/Controllers/StoreController.php

    public function show ($id)
    {   //                ^^^^
        $store = Store::find($id);
        
        return $store;
    }  

Con la introducción del **enlazado implícito de modelos en rutas** -en Laravel 5.2-, esto se facilitó. Tan solo hacía falta modificar la ruta de la siguiente manera:

    Route::get("stores/{store}", 'StoreController@show');

De este modo, Laravel intentará encontrar un modelo que corresponda con el parámetro de ruta (``Store``), pudiendo así resolver el modelo e inyectarlo hacia nuestro controlador:

    public function show (Store $store)
    { //                         ^^^^^
        return $store; // <--
    }  

Sin embargo, **¿qué pasa si no queremos buscar por la llave primaria del modelo -típicamente el ``id``- sino por alguna otra columna?**

Tomemos el caso de un **Artículo** (modelo ``Post``). Quizás querríamos buscar por el ``slug`` del título, en lugar del ``id`` del artículo en sí. Previo a Laravel 7, se le tenía que indicar a nuestro modelo cómo es que debía de resolver estos enlazados implícitos:

    # app/Post.php

    public function getRouteKeyName()
    {
        return 'slug'; // <-- nombre del atributo/columna
    }
    
El detalle de esta solución es que este cambio **regirá para todas las rutas** que quieran resolver modelos ``Post``. Entonces, ¿qué sucede si en otro lugar de nuestra aplicación, necesitamos buscar por un campo **distinto** a ``slug``? Quizás en el administrador deseamos ahora sí buscar por ``id`` o por ``uuid``. En fin, nos obligaba a volver a la solución inicial, buscando manualmente en cada método que lo necesite.

Esto quedará en el pasado con las nuevas versiones de Laravel.

### Mejoras en Laravel 7

En Laravel 7, si queremos resolver el modelo por **algún campo en específico distinto al de la llave primaria**, tan solo tenemos que indicarlo en la misma definición de la ruta con la notación ``:columna``. Veamos un ejemplo:

    Route::get("posts/{post:slug}", 'PostController@show');
    //                 ^^^^^^^^^
    
Así de simple. Ahora, lo _cool_ de esto es que nos da la flexibilidad de poder definir distintas rutas con diferentes modo de resolución de manera simple:

    Route::get('posts/{post:slug}', 'PostController@show');
    //                 ^^^^^^^^^
    Route::get('admin/posts/{post:uuid}', 'AdminPostController@show');
    //                       ^^^^^^^^^

Genial.

### Filtros automáticos

Hay escenarios en los cuales implícitamente enlazas múltiples modelos en una misma definición de ruta, **y quieres filtrar los resultados de uno respecto del otro**. Tomemos el caso de un **autor** (``Author``) con sus **artículos** (``Post``).

    Route::get("authors/{author}/posts/{post:slug}", "AuthorPostController@index");
    //                   ^^^^^^         ^^^^^^^^^
    
En Laravel 7, cuando uses una llave personalizada para resolver modelos implícitamente, automáticamente se aplicará un filtro (Query scope) tratando de deducir el nombre de las relaciones entre ambos modelos. 

En el ejemplo anterior, Laravel intentará buscar la relación ``posts()`` en el modelo ``Author``, de este modo al tratar de resolver los artículos/_posts_ de un author en específico, internamente **aplicará un filtro para limitar los resultados del segundo modelo, respecto del primero**. Entonces, solo recibiremos los artículos/_posts_ que **pertenezcan** a ese autor en específico.

Esto es particularmente útil pues, personalmente he tenido situaciones de este tipo y siempre suelo colocar un _check_ adicional al recibir los modelos para garantizar que efectivamente estén vinculados. Siempre puede suceder el caso en el que un usuario pueda querer acceder a realizar operaciones en los recursos a los cuales no debería tener acceso. 

Para leer más detalles sobre esta sección, visita [esta sección](https://laravel.com/docs/master/routing#route-model-binding) de la documentación oficial.

-----

## Cierre

Como puedes notar, este pequeño -pero interesante- cambio nos puede ahorrar unas cuantas líneas de código extra que ahora podremos dedicar a mejorar nuestra lógica de negocio. 

Si te interesó esta nueva funcionalidad, no olvides leer el resto de novedades que trae Laravel 7 ([aquí](/blog/0021-novedades-de-laravel-7)), ya que tenemos [una serie de artículos -"Novedades de Laravel 7"-](/blog/0021-novedades-de-laravel-7) dedicados a este tema. 
