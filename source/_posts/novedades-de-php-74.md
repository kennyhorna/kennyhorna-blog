---
extends: _layouts.post
section: content
title: Novedades de PHP 7.4
date: 2019-11-03
description: PHP 7.4 es la última versión de la serie 7.X antes de la esperada PHP 8, así que indagaremos en las novedades y cambios que nos trae. 
cover_image: /assets/images/posts/0005/novedades-php-74.png
featured: true
categories: [php]
---

PHP 7.4 es la última versión de la serie 7.X antes de la esperada PHP 8, así que indagaremos en las novedades
y cambios que nos trae.

### Sobre PHP 7.4
El lanzamiento de la nueva versión está pensada para el 28 de noviembre del 2019. 

## Principales novedades

### -> Funciones Flecha
Las funciones flechas (Arrow Functions) son muy útiles para resumir la sintaxis de las funciones mono-lineales.

Si antes tenías que escribir algo de este estilo:
````php
array_map(function (User $usuario) { 
    return $usuario->id; 
}, $usuarios)
````
Ahora lo puedes hacer de manera más simple:
````php
array_map(fn (User $usuario) => $usuario->id, $usuarios)
````

Hay algunas observaciones interesantes de esta adición:

- Pueden siempre acceder al contexto padre, por lo que no es necesario pasar elementos mediante el keyword ``use``.
- Solo deben contener una expresión, que a su vez, es el valor retornado.
- ``$this`` está disponible tal como sucede en _closures_ normales.

Como ejemplo podemos tomar la siguiente función que tan solo multiplica los valores de un arreglo por un factor:
 
```php
$numeros = [1, 2, 3, 4, 5];
$factor = 2;

array_map(function ($numero) use ($factor) { 
    return $numero * factor; 
}, $numeros);
```

Si notamos, en PHP 7.3 o inferior, teníamos que indicarle a nuestra función anónima la existencia de ``$factor`` 
mediante el ``use`` pues de otro modo no existía en el contexto de la función cierre. 
En cambio, ahora podríamos cogerlo directamente del contexto padre:

```php
$numeros = [1, 2, 3, 4, 5];
$factor = 2;

array_map(fn ($numero) => $numero * $factor, $numeros);
```

> Puedes leer más sobre esta adición en el [RFC](https://wiki.php.net/rfc/arrow_functions_v2)

### -> Propiedades de tipo clase _insinuadas_
Las variables de tipo clase ahora también pueden ser insinuadas (_type-hinted_):

```php
class ClaseA
{
    public string $nombre;
    public User $usuario;
    public ?Order $orden;
}
```
> Puedes leer más sobre esta adición en el [RFC](https://wiki.php.net/rfc/typed_properties_v2)

### -> Operador de extensión para arreglos
Ahora es posible utilizar el operador de extensión (``...``) para los arreglos. Mira el siguiente ejemplo:

```php
$a = [1, 2, 3];
$b = [4, 5, 6];

$numeros = [0, ...$a, ...$b, 7, 8, 9];

// [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];
```

**Observación**: Esto solo funcionará en arreglos con llaves numéricas.

> Puedes leer más sobre esta adición en el [RFC](https://wiki.php.net/rfc/spread_operator_for_array)

### -> Operador de asignación de fusión nula
Ahora tenemos un _operador de asignación de fusión nula_, una abreviatura para las operaciones de fusión nulas.
Si antes hacíamos lo siguiente:

```php
$data['fecha'] = $data['fecha'] ?? new DateTime();
```
Ahora podemos hacerlo aún más corto: 
```php
$data['fecha'] ??= new DateTime();
```

> Puedes leer más sobre esta adición en el [RFC](https://wiki.php.net/rfc/null_coalesce_equal_operator)

### -> Separador literal numérico
Ahora podemos utilizar el ``_`` para separar visualmente las cifras de valores numéricos. El motor de PHP
simplemente ignorará los ``_``:

```php
$numeroSinFormato = 6120456.90;
$numeroConFormato = 6_120_456.90;
```
> Puedes leer más sobre esta adición en el [RFC](https://wiki.php.net/rfc/numeric_literal_separator)

### -> Precarga
Una de las más interesantes adiciones al core de PHP -a bajo nivel- es la precarga. Esto puede traer
bastante beneficios a nivel de rendimiento.

En resumen: Si utilizas algún framework, sus archivos tienen que ser cargados y enlazados con cada 
request. La _precarga_ le permite al servidor cargar los archivos PHP en memoria al iniciar el servidor,
de este modo, los tendrá permanentemente disponible para todos los futuros requests.

La mejora de _performance_ trae un costo: Si realizas modificaciones en alguno de los archivos precargados,
tendrás que reiniciar el servidor para que estos puedan ser nuevamente cargados. 

> Puedes leer más sobre esta adición en el [RFC](https://wiki.php.net/rfc/preload)

### -> Serialización personalizada de objetos
Dos nuevos métodos mágicos han sido incluídos: ``__serialize`` y ``__unserialize`` . 

La diferencia entre estos métodos y ``__sleep`` y ``__wakeup`` puedes leerlo en detalle en la discusión
del [RFC](https://wiki.php.net/rfc/custom_object_serialization).

### -> Reflexión para referencias
Librerías como el [var dumper de Symfony](https://symfony.com/doc/current/components/var_dumper.html) 
dependen fuertemente de la API de reflexión para arrojar una variable de manera confiable. 

Previamente, no era posible reflejar referencias de manera apropiada, resultando en que estas librerías
dependan de "hacks" para poder detectarlas.

PHP 7.4 añade la clase ``ReflectionReference`` que soluciona este inconveniente. 

> Puedes leer más sobre esta adición en el [RFC](https://wiki.php.net/rfc/reference_reflection)

### -> Referencias débiles
Las "referencias débiles" son referencias a objetos que no las previenen de ser destruidas.

> Puedes leer más sobre esta adición en el [RFC](https://wiki.php.net/rfc/weakrefs)

### -> Función ``mb_str_split`` añadida
Esta función provee la misma funcionalidad que ``str_split`` pero en strings multi-byte.
This function provides the same functionality as str_split, but on multi-byte strings.

> Puedes leer más sobre esta adición en el [RFC](https://wiki.php.net/rfc/mb_str_split)

### -> Registro de Hashing de contraseñas
Se han realizado cambios internos al modo en que las librerías de _hashing_ son utilizadas, de modo
que sea más fácil para el usuario el poder utilizarlas. De hecho, una nueva función ``password_algo`` 
ha sido añadida la cual lista todos los algoritmos de contraseñas registradas. 

> Puedes leer más sobre esta adición en el [RFC](https://wiki.php.net/rfc/password_registry)

## Cambios y deprecaciones
Así como hay nuevas funciones, también hay cosas que han sido modificadas, que dejarán de funcionar 
en PHP 7.4 o se mostrarán con advertencia para luego ser "deprecadas" en PHP 8. 

### - Precedencia de concatenación
Si antes escribías esto:

    echo "sum: " . $a + $b;

PHP lo interpretaba así:

    echo ("sum: " . $a) + $b;

En PHP 8, lo hará del siguiente modo:

    echo "sum :" . ($a + $b);

PHP 7.4 mostrará una advertencia de deprecación al encontrar una expresión sin paréntesis (``(``/``)``)
que contenga un operador (``+`` / ``-``) y que sea precedido por un ``.``.

> Puedes leer más sobre esta modificación en el [RFC](https://wiki.php.net/rfc/concatenation_precedence)

### - Operador ternario de asociación izquierda, deprecado

El operador ternario tiene algunas inconsistencias. Este [RFC](https://wiki.php.net/rfc/ternary_associativity) 
añade una alerta de deprecación que luego generará un error de compilación en PHP 8:

    1 ? 2 : 3 ? 4 : 5;   // Deprecado
    (1 ? 2 : 3) ? 4 : 5; // Ok

> Puedes leer más sobre esta modificación en el [RFC](https://wiki.php.net/rfc/ternary_associativity)

### - Se permitirán excepciones en ``__toString``
Previamente, no se podían arrojar excepciones desde ``__toString``. Esto era por un antiguo artilugio que
se implementó para dar solución a otro problema. El equipo de PHP ha corregido esto y ahora se podrán
arrojar excepciones desde este método.

> Puedes leer más sobre esta modificación en el [RFC](https://wiki.php.net/rfc/tostring_exceptions)

### - Acceso a elementos de arreglos mediante llaves {}
Antes, podías también acceder a elementos de tus arreglos haciendo esto:

    $numeros = [1, 2];
    echo $numeros[1]; // imprime 2
    echo $numeros{1}; // también imprime 2
     
    $cadena = "kenny";
    echo $cadena[0]; // imprime "k"
    echo $cadena{0}; // también imprime "k"
    
A partir de ahora: ``$arreglo{indice}`` ya no sera posible. 

Esto ya no será posible.

> Puedes leer más sobre esta modificación en el [RFC](https://wiki.php.net/rfc/deprecate_curly_braces_array_access)

### Resto de cambios

Hay muchas otras adiciones, mejores, advertencias y deprecaciones, tales como:

- Aviso al acceder a un array inválido ([RFC](https://wiki.php.net/rfc/notice-for-non-valid-array-container))
- Mejoras a ``proc_open`` ([RFC](https://github.com/php/php-src/blob/PHP-7.4/UPGRADING#L319))
- ``strip_tags`` ahora acepta arreglos ([RFC](https://github.com/php/php-src/blob/PHP-7.4/UPGRADING#L259))
- ``ext-hash`` activado por defecto ([RFC](https://wiki.php.net/rfc/permanent_hash_ext))
- Mejoras a ``password_hash`` ([RFC](https://wiki.php.net/rfc/sodium.argon.hash)) 
- Muchas deprecaciones pequeñas/menores ([RFC](https://wiki.php.net/rfc/deprecations_php_7_4))

Entre muchos otros.

## Cierre

Como puedes notar, PHP 7.4 trae consigo interesantes adiciones y modificaciones que, en mi opinión, seguirán
impulsando el resurgimiento de este gran lenguaje de programación.

Como siempre, cualquier comentario, duda, aclaración o corrección es bienvenido.
