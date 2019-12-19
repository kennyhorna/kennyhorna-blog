---
extends: _layouts.post
section: content
title: "Más allá de CRUDs: [01] Laravel orientado al Dominio"
date: 2019-12-15
description: También podría llamarlos "grupos", "módulos"; Algunas personas los llaman "servicios". Cualquiera que sea el nombre que prefieras, los dominios describen un conjunto de problemas de negocio que estás tratando de resolver.  
cover_image: /assets/images/posts/0008/mas-alla-de-cruds-01-laravel-orientado-al-dominio.png
featured: false
reference: https://stitcher.io/blog/laravel-beyond-crud-01-domain-oriented-laravel
categories: [laravel, php, programming]
---

Esta es el artículo #01 originalmente publicado por [Brent](https://mobile.twitter.com/brendt_gd) en su 
[blog](https://stitcher.io/blog/laravel-beyond-crud-01-domain-oriented-laravel) (puedes encontrar ahí la serie en 
su idioma original).

El índice de artículos que conforman esta serie lo [puedes encontrar aquí](/blog/laravel-mas-alla-de-cruds).

Dicho esto, comencemos 😉.

-------

> Los seres humanos pensamos en categorías, nuestro código debería ser un reflejo de eso.

El término "Dominio" lo tomo del popular paradigma de programación DDD, o "Diseño Orientado al Dominio (Domain Driven 
Design). Según una de sus definiciones se describe como "Esfera específica de actividad o conocimiento".

Mientras que mi uso del término "dominio" no significa lo mismo que cuando se usa dentro del contexto de DDD,
hay muchas similitudes. Si estás familiarizado con DDD, descubrirás estas similitudes a lo largo de esta serie.
He tratado de especificar en los casos relevantes cualquier coincidencia y diferencia según sea el caso.

Entonces, _dominios_. También podría llamarlos "grupos", "módulos"; Algunas personas los llaman "servicios". 
Cualquiera que sea el nombre que prefieras, los dominios describen un conjunto de problemas de negocio que 
estás tratando de resolver.

Un momento.. me doy cuenta de que acabo de usar mi primer término "empresarial" en esta serie: "el problema 
de negocio". Al leer esta serie, notarás que hice todo lo posible para alejarme del aspecto teórico, de 
alta gerencia y de negocios. Soy desarrollador y prefiero mantener las cosas prácticas. Entonces, otro 
nombre más simple sería "proyecto".

Pongamos un ejemplo: una aplicación para gestionar reservas de hotel. Tiene que gestionar clientes, 
reservas, facturas, inventarios de hoteles, etc.

Los frameworks web modernos te enseñan a tomar un grupo de conceptos relacionados y dividirlo en varios 
lugares a lo largo de su base de código: controladores con controladores, modelos con modelos, etc. Ya 
entiendes la idea.

¿Alguna vez un cliente te dijo que "trabajes en todos los controladores ahora" o que "pases algo más de 
tiempo en el directorio de modelos"? No, te piden que trabajes en funciones de facturación, gestión de 
clientes o reservas.

Estos grupos son lo que yo llamo _dominios_. Apuntan a agrupar -dentro del proyecto- conceptos que se 
relacionan. Si bien esto puede parecer trivial al principio, es más complicado de lo que piensas. 
Es por eso que parte de esta serie se centrará en un conjunto de reglas y prácticas para mantener tu 
código bien ordenado.

Obviamente no hay una fórmula matemática que pueda darte, casi todo depende del proyecto específico 
en el que estés trabajando. Así que no pienses en esta serie como un conjunto de reglas fijas. Más bien 
piensa que te entrega una colección de ideas que puede usar y desarrollar, como quieras.

Es una oportunidad de aprendizaje, mucho más que una solución que puedas arrojar a cualquier problema 
que encuentres.

### Dominios y aplicaciones

Si estamos agrupando ideas, evidentemente surge la pregunta: ¿hasta dónde llegamos? Por ejemplo, podría 
agrupar todo lo relacionado con la factura: modelos, controladores, recursos, reglas de validación, trabajos, etc.

Esto plantea un problema en las aplicaciones HTTP tradicionales: a menudo no existe un mapeo uno a uno entre 
los controladores y los modelos. De acuerdo, en las API REST -y para la mayoría de sus controladores CRUD clásicos- 
puede haber una asignación estricta uno a uno, pero desafortunadamente estas son las excepciones a las reglas 
que nos harán pasar un mal rato. Las facturas, por ejemplo, simplemente no se manejan de forma aislada, 
necesitan que se envíe un cliente, necesitan reservas para facturar, etc.

Es por eso que necesitamos hacer una distinción adicional entre lo que es el código de dominio y lo que no.

Por un lado, está el dominio, que representa toda la lógica empresarial; y, por otro lado, tenemos el 
código que usa/consume ese dominio para integrarlo con el framework y lo expone al usuario final. 
Las aplicaciones proporcionan la infraestructura para que los usuarios finales usen y manipulen el 
dominio de una manera fácil de usar.

### En la práctica

Entonces, ¿cómo se ve esto en la práctica? El dominio tendrá clases como modelos, constructores de 
consultas, eventos de dominio, reglas de validación y más; Veremos en profundidad todos estos conceptos.

La capa de aplicación contendrá una o varias aplicaciones. Cada aplicación se puede ver como una 
aplicación aislada que puede usar todo el dominio. En general, las aplicaciones no se hablan entre sí.

Un ejemplo podría ser un panel de administración HTTP, y otro podría ser una API REST. También me gusta 
pensar en la consola, el _artisan_ de Laravel, como una aplicación propia.

Como una descripción general de alto nivel, así es como se vería la estructura de carpetas de un proyecto 
orientado al dominio:

<div class="files">
    // Un directorio específico de dominio por concepto de negocio
    <div class="folder folder--open">app/Domain/Invoices
        <div class="folder">Actions</div>
        <div class="folder">QueryBuilders</div>
        <div class="folder">Collections</div>
        <div class="folder">DataTransferObjects</div>
        <div class="folder">Events</div>
        <div class="folder">Exceptions</div>
        <div class="folder">Models</div>
        <div class="folder">Listeners</div>
        <div class="folder">Rules</div>
        <div class="folder">States</div>
    </div>
    <br>
    <div class="folder folder--open">app/Domain/Customers
            <div class="folder">...</div>
        </div>
</div>

Y así es como se vería la _capa de aplicación_:

<div class="files">
    // El app panel de administración
    <div class="folder folder--open">app/App/Admin
        <div class="folder">Controllers</div>
        <div class="folder">Middlewares</div>
        <div class="folder">Requests</div>
        <div class="folder">Resources</div>
        <div class="folder">ViewModels</div>
    </div>
    <br>
    // La REST API app
    <div class="folder folder--open">app/App/Api
        <div class="folder">Controllers</div>
        <div class="folder">Middlewares</div>
        <div class="folder">Requests</div>
        <div class="folder">Resources</div>
    </div>
    <br>
    // La app de consola
    <div class="folder folder--open">app/App/Console
        <div class="folder">Commands</div>
    </div>
</div>

### Acerca de los _namespaces_

Es posible que hayas notado que el ejemplo anterior no sigue la convención de Laravel de `\App` como el 
_namespace_ raíz único. Dado que las aplicaciones son solo una parte de nuestro proyecto, y debido a que 
pueden haber varias, no tiene sentido usar `\App` como la raíz de todo.

Ten en cuenta que si prefieres permanecer más cerca de la estructura predeterminada de Laravel, puedes 
hacerlo. Esto significa que terminarás con _namespaces_ como `\App\Domain` y `\App\Api`. Pero eres 
libre de hacer lo que te resulte cómodo.

Sin embargo, si deseas separar los _namespaces_ raíz, deberás realizar algunos cambios en la forma en 
que Laravel se inicia.

En primer lugar, deberás registrar todos los _namespace_ raíz en tu `composer.json`:

```json
{
    // …

    "autoload" : {
        "psr-4" : {
            "App\\" : "app/App/",
            "Domain\\" : "app/Domain/",
            "Support\\" : "app/Support/"
        }
    }
}
```

Ten en cuenta que también tengo un namespace `\Support`, que por ahora puedes considerar como el depósito 
de todos los pequeños _helpers_ que no pertenecen a ningún lado.

A continuación, debemos volver a registrar el namespace `\App`, ya que Laravel lo usará internamente 
para varias cosas.

```php
namespace App;

use Illuminate\Foundation\Application as LaravelApplication;

class BaseApplication extends LaravelApplication
{
    protected $namespace = 'App\\';

    public function path($path = '')
    {
        return $this->basePath.DIRECTORY_SEPARATOR.'app/App'.($path ? DIRECTORY_SEPARATOR.$path : $path);
    }
}
```

Finalmente, necesitamos usar nuestra aplicación base personalizada registrándola en `bootstrap/app.php`:

```php
// bootstrap/app.php

$app = new App\BaseApplication(
    realpath(__DIR__.'/../')
);
```

Desafortunadamente, no hay una forma más limpia de hacer esto, ya que el framework nunca tuvo la intención 
de cambiar la estructura de carpetas predeterminada. Nuevamente, si no te sientes cómodo al hacer estos 
cambios, no dudes en seguir usando la estructura de namespaces raíz predeterminada de Laravel.

-----

Cualquiera sea la estructura de carpetas que termines utilizando, lo más importante es que comiences 
a pensar en grupos de conceptos de negocio relacionados, en lugar de en grupos de código con las 
mismas propiedades técnicas.

Sin embargo, dentro de cada grupo, cada dominio, hay espacio para estructurar el código de manera 
que sea fácil de usar dentro de esos grupos individuales. La primera parte de esta serie analizará 
de cerca cómo se pueden estructurar los dominios internamente y qué patrones se pueden utilizar para 
ayudarlo a mantener su base de código mantenible a medida que crece con el tiempo. Después de eso, 
veremos la capa de aplicación, cómo se puede consumir el dominio exactamente y cómo mejoramos los 
conceptos existentes de Laravel utilizando, por ejemplo, modelos de vista.

Hay mucho terreno por recorrer, y espero que puedas aprender muchas cosas que puedas poner en 
práctica de inmediato.
