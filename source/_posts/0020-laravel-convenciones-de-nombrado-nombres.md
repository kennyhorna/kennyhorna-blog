---
extends: _layouts.post
section: content
title: "Buenas prácticas en Laravel: Convenciones de nombrado"
date: 2020-02-22
description: "Casi todo aspecto en Laravel es configurable para que puedas adaptarlo a tus costumbres. Sin embargo, Laravel de por sí tiene un modo de nombrar distintos elementos que te pueden ahorrar estos ajustes adicionales en caso decidas mantener sus prácticas. Hoy veremos cuales son."  
cover_image: /assets/images/posts/0020/0020-laravel-convenciones-de-nombrado-nombres.png
featured: true
categories: [laravel, php, tips]
---

Casi todo aspecto en Laravel es configurable para que puedas adaptarlo a tus costumbres. Sin embargo, Laravel de por sí tiene un modo de nombrar distintos elementos que te pueden ahorrar estos ajustes adicionales en caso decidas mantener sus prácticas. Estas convenciones siguen el estilo definido por las [PSR-2](https://www.php-fig.org/psr/psr-2/).
 
 Espero que este artículo sirva de guía recurrente ante cualquier duda.

## Base

Antes de comenzar, vamos a refrescar algunos estilos para nombrar ciertos elementos.

### Estilos de nombrado

En el mundo de la programación hay muchos estilos que se emplean para **reemplazar _los espacios_ (`` ``)** de las palabras a la hora de definir elementos tales como nombres de variables, funciones, clases, codificar urls, y un largo etc. Algunas de las más utilizadas son las siguientes:

#### camelCase

 Este estilo elimina los espacios aplicando una mayúscula para juntar la palabra siguiente. Notar que **la primera letra va siempre en minúsculas**. El nombre viene por la forma de la joroba de un **camello** `/\/\` (_camel_). Ejemplos:
 
<div class="mb-4">
  <div class="bg-green-200 px-4"> estoEstaBien</div>
  <div class="bg-red-200 px-4"> EstoNoEsCorrecto</div>
  <div class="bg-red-200 px-4"> esto_tampoco</div>
</div>

#### PascalCase

 Muy similar al anterior, solo que en este caso **la primera letra va en mayúsculas**. El nombre proviene del lenguaje de programación. Ejemplos:
 
 <div class="mb-4">
    <div class="bg-green-200 px-4"> EstoEstaBien</div>
    <div class="bg-red-200 px-4"> estoYaNo</div>
    <div class="bg-red-200 px-4"> esto_menos</div>
 </div>


#### snake_case

 En este estilo, se reemplazan los _espacios_ por **sub-guiones** (`_`), todo el texto se escribe en minúsculas. Tal como indica su nombre, se le llama así por la similitud con el movimiento de las **serpientes**. Ejemplos:
  
<div class="mb-4">
    <div class="bg-green-200 px-4"> esto_esta_bien</div>
    <div class="bg-red-200 px-4"> estoNo</div>
    <div class="bg-red-200 px-4"> EstoMuchoMenos</div>
    <div class="bg-red-200 px-4"> ni-hablar-de-este-otro</div>
</div>

#### kebab-case

Este estilo es similar al anterior (_snake case_) teniendo como única diferencia la utilización de **guiones** (`-`) en lugar de los sub-guiones. De este modo el texto toma la forma de una **brocheta**, de ahí el origen del nombre (_kebab_). Ejemplos:

<div class="mb-4">
    <div class="bg-green-200 px-4"> ahora-si-me-toco-a-mi</div>
    <div class="bg-red-200 px-4"> EstoNoEstaBien</div>
    <div class="bg-red-200 px-4"> estoMuchoMenos</div>
    <div class="bg-red-200 px-4"> este_se_parece_pero_noup</div>
</div>

Entonces, a modo de resumen:

- PascalCase
- camelCase
- snake_case
- kebab-case

Estos no son los únicos estilos, de seguro que hay varios más, sin embargo son los que ocuparemos el día de hoy. Ahora sí, pasemos a los que nos interesa.

## Convenciones Laravel

Vamos a agrupar a las reglas de nombrado según el tipo de elemento. Como nota global mencionar de que, dado que todo el framework está escrito en inglés, este es el lenguaje que debes emplear para nombrar tus elementos.

### Controladores

Los nombres de los controladores se derivan del nombre del modelo añadiendo **el sufijo** _Controller_, aplicando el estilo **PascalCase**. Veamos algunos ejemplos:

<div class="mb-10">
    <div class="bg-green-200 px-4">UserController</div>
    <div class="bg-green-200 px-4">OrderDetailController</div>
    <div class="bg-red-200 px-4">customerController</div>
    <div class="bg-red-200 px-4">DetalleDeFacturaController</div>
    <div class="bg-red-200 px-4">borrowed-book-controller</div>
</div>

### Funciones

Las funciones deben ser nombradas aplicando **camelCase**. Veamos algunos ejemplos:

<div class="mb-10">
    <div class="bg-green-200 px-4">getUser()</div>
    <div class="bg-green-200 px-4">setIsAdminAttribute()</div>
    <div class="bg-green-200 px-4">orderDetails()</div>
    <div class="bg-red-200 px-4">ThisIsABadExample()</div>
    <div class="bg-red-200 px-4">this_is_also_incorrect()</div>
</div>

### Modelos

Para nombrar a los modelos tomaremos el nombre de la entidad en **singular** y siempre aplicando **PascalCase**. Veamos algunos ejemplos:

<div class="mb-4">
    <div class="bg-green-200 px-4">User</div>
    <div class="bg-green-200 px-4">OrderDetail</div>
    <div class="bg-red-200 px-4">customer</div>
    <div class="bg-red-200 px-4">DetalleDeFactura</div>
    <div class="bg-red-200 px-4">borrowed-book</div>
</div>

#### Propiedades de modelos

Los nombres de los atributos tanto los recibidos de la base de datos como los computados, deben ser nombrados siguiendo **snake_case**:

<div class="mb-4">
    <div class="bg-green-200 px-4">$user->name</div>
    <div class="bg-green-200 px-4">$order->created_at</div>
    <div class="bg-red-200 px-4">$invoice->createdAt</div>
    <div class="bg-red-200 px-4">$book->LaunchDate</div>
</div>

#### Relaciones

Las relaciones deben seguir el modo de nombrado de [las funciones](#funciones). Además, los nombres de estos deben de ir en singular o plural dependiendo de la naturaleza de la relación. 

Las relaciones de tipo ``hasMany``, ``belongsToMany``, ``morphMany`` deben de indicarse en **plural**, pues es lógico que estas tratarán a una colección de elementos:

<div class="mb-4">
    <div class="bg-green-200 px-4">$continent->countries()</div>
    <div class="bg-green-200 px-4">$book->authors()</div>
    <div class="bg-red-200 px-4">$spider->leg()</div>
    <div class="bg-red-200 px-4">$continent->country()</div>
</div>

Las relaciones de tipo ``hasOne``, ``belongsTo``, ``morphTo`` deben de indicarse en **singular**, pues estas tratan a una única instancia del modelo relacionado:

<div class="mb-4">
    <div class="bg-green-200 px-4">$phone->owner()</div>
    <div class="bg-green-200 px-4">$room->house()</div>
    <div class="bg-red-200 px-4">$house->districts()</div>
    <div class="bg-red-200 px-4">$file->line()</div>
</div>

#### Métodos de los modelos

El resto de métodos del modelo siguen las mismas reglas que el nombrado de una [función](#funciones) común: **camelCase**. Esto se aplica para los [accesores](https://laravel.com/docs/6.x/eloquent-mutators#accessors-and-mutators), [mutadores](https://laravel.com/docs/6.x/eloquent-mutators#accessors-and-mutators), [query scopes](https://laravel.com/docs/6.x/eloquent#query-scopes), etc.

### Pruebas

Para nombras los métodos de prueba antepondremos `test`, el resto del nombre debe ser una descripción del tipo de prueba que se realice, siguiendo lo ya mencionado en la sección [funciones](#funciones). Para el estilo de nombrado se emplea **camelCase**. Ejemplos:

<div class="mb-10">
    <div class="bg-green-200 px-4">testGetUserOrderHistory()</div>
    <div class="bg-green-200 px-4">testCreateAndAssignRolesToAUser()</div>
    <div class="bg-red-200 px-4">getUserOderHistory()</div>
    <div class="bg-red-200 px-4">test_create_and_assign_roles_to_a_user()</div>
</div>

### Rutas

Los sustantivos en las rutas deben ser indicadas en plural, aplicando durante todo esto **kebab-case**. Ejemplos:

<div class="mb-10">
    <div class="bg-green-200 px-4">/customers/23</div>
    <div class="bg-green-200 px-4">/orders</div>
    <div class="bg-red-200 px-4">/user/15</div>
</div>

### Tablas

#### Tablas entidad

Las tablas toman el nombre inglés en **plural** de la entidad, aplicando en este caso **snake_case**. Veamos algunos ejemplos:

<div class="mb-4">
    <div class="bg-green-200 px-4">users</div>
    <div class="bg-green-200 px-4">order_details</div>
    <div class="bg-red-200 px-4">Payment</div>
    <div class="bg-red-200 px-4">invoice</div>
    <div class="bg-red-200 px-4">libros</div>
</div>

#### Tablas pivot/pivote

El nombre de las **tablas pivote** -empleadas en relaciones de _muchos a muchos_- se deriva del de los nombres en **singular** en **orden alfabético** de las entidades relacionadas, empleando **snake_case**. Veamos algunos ejemplos:

<div class="mb-4">
    <div class="bg-green-200 px-4">user_permission</div>
    <div class="bg-green-200 px-4">category_post</div>
    <div class="bg-red-200 px-4">UserPermission</div>
    <div class="bg-red-200 px-4">post_category</div>
</div>

#### Columnas

Las columnas deben ser nombradas aplicando **snake_case**. Ejemplos:

<div class="mb-4">
    <div class="bg-green-200 px-4">id</div>
    <div class="bg-green-200 px-4">created_at</div>
    <div class="bg-green-200 px-4">phone_number</div>
    <div class="bg-red-200 px-4">createdAt</div>
    <div class="bg-red-200 px-4">PhoneNumber</div>
</div>

#### Llaves primarias y foráneas

Si no se especifica ninguna, Laravel asume por defecto que la **llave primaria** de la tabla es ``id``.

Las **llaves foráneas** se nombran como la entidad en singular pero añadiendo el sufijo ``_id``. Ejemplos:

<div class="mb-10">
    <div class="bg-green-200 px-4">post_id</div>
    <div class="bg-green-200 px-4">user_id</div>
    <div class="bg-green-200 px-4">mobile_phone_id</div>
    <div class="bg-red-200 px-4">userId</div>
    <div class="bg-red-200 px-4">PostId</div>
</div>

### Variables

Las variables deben de ser descriptivas. Estas deben aplicar el estilo **camelCase**. Veamos algunos ejemplos:

<div class="mb-10">
    <div class="bg-green-200 px-4">$admins = User::isAdmin()->get();</div>
    <div class="bg-green-200 px-4">$activeUser = User::active()->first();</div>
    <div class="bg-red-200 px-4">$room = Room::all();</div>
    <div class="bg-red-200 px-4">$invoices = Order::find(1)->invoice;</div>
</div>

### Vistas

Para nombrar a las vistas en blade se aplica **kebab-case**. Estas vistas deben terminar en ``.blade.php``. Ejemplo:

<div class="mb-10">
    <div class="bg-green-200 px-4">footer.blade.php</div>
    <div class="bg-green-200 px-4">active-user.blade.php</div>
    <div class="bg-green-200 px-4">create-admin.blade.php</div>
    <div class="bg-red-200 px-4">active_user.blade.php</div>
    <div class="bg-red-200 px-4">createAdmin.blade.php</div>
</div>

-------

## Cierre

Estas son las recomendaciones y los estilos que emplea Laravel y el resto de su comunidad para nombrar los elementos. Puedes estar de acuerdo como no, es normal pues al final de cuenta son _recomendaciones_. Por ejemplo, personalmente prefiero utilizar **snake_case** para nombrar las funciones de mis [pruebas](#pruebas) pues siento que facilita la lectura, pero hey! todos tienen sus manías.

En fin, de todos modos siempre es bueno tener esto presente para saber como es que se estila. Espero te sirva.

PD: Iré añadiendo más elementos a medida que los note en mi código. Si es que conoces algún elemento que no aparece en la lista, por favor házmelo saber dejando un comentario o abriendo un [issue en el repo](https://github.com/kennyhorna/kennyhorna-blog/issues).
