---
extends: _layouts.post
section: content
title: "Más allá de CRUDs: Trabajando con data"
date: 2019-12-17
description: "En el núcleo de cada proyecto, encontrarás data. Casi todas las tareas de las aplicaciones se pueden resumir de este modo: proveer, interpretar y manipular data de cualquier modo que el negocio lo requiera."  
cover_image: /assets/images/posts/0009/mas-alla-de-cruds-02-trabjando-con-data.png
featured: true
reference: https://stitcher.io/blog/laravel-beyond-crud-02-working-with-data
categories: [laravel, php, programming]
---

Esta es el artículo #02 de la serie [Laravel: Más allá de CRUDs](/blog/laravel-mas-alla-de-cruds). Fue originalmente 
publicado por [Brent](https://mobile.twitter.com/brendt_gd) en su 
[blog](https://stitcher.io/blog/laravel-beyond-crud-02-working-with-data) (puedes encontrar ahí la serie en 
su idioma original).

La tabla de contenido que conforma esta serie la [tienes aquí](/blog/laravel-mas-alla-de-cruds).

Dicho esto, comencemos 😉.

-------

En el núcleo de cada proyecto, encontrarás data. Casi todas las tareas de las aplicaciones se pueden 
resumir de este modo: proveer, interpretar y manipular data de cualquier modo que el negocio lo requiera.

Probablemente también hayas notado que: al comienzo de un proyecto no comienzas a construir controladores 
ni _jobs_, comienzas construyendo, lo que Laravel llama, _modelos_. Los grandes proyectos se benefician 
al hacer DER (Diagrama Entidad-Relación) y otros tipos de diagramas para conceptualizar qué datos serán 
manejados por la aplicación. Solo cuando eso esté claro, puedes comenzar a construir los puntos de entrada 
y los hooks que funcionan con sus datos.

En este capítulo veremos de cerca cómo trabajar con datos de manera estructurada, de modo que todos los 
desarrolladores de tu equipo puedan escribir la aplicación para manejar estos datos de una manera 
predecible y segura.

Puede que estés pensando en _modelos_ en este momento, pero tenemos que retroceder unos pasos más al principio.

### Teoría de tipos

Para comprender el uso de los DTOs (_Data Transfer Objects_) o Objetos de Transferencia de Datos (spoiler: de 
eso se trata este capítulo), necesitarás tener algunos conocimientos básicos sobre los sistemas de tipos.

No todos están de acuerdo con el vocabulario utilizado cuando se habla de sistemas de tipos. Así que aclaremos 
algunos términos en la forma en que los usaré aquí.

La fuerza de un sistema de tipos, tipos fuertes o débiles, define si una variable puede cambiar su tipo después 
de que se define.

Un ejemplo simple: dada una variable de cadena `$a = 'prueba';`; un sistema de tipo débil te permite reasignar 
esa variable a otro tipo, por ejemplo `$a = 1;`, a un entero.

PHP es un lenguaje débilmente tipado. Siento que existe un ejemplo más real:

```php
$id = '1'; // Ejm. un id recibido desde una URL

function find(int $id): Model
{
    // El input '1' será automáticamente casteado a entero
}

find($id);
```

Para ser claros: tiene sentido que PHP tenga un sistema de tipado débil. Al ser un lenguaje que funciona 
principalmente con solicitudes HTTP, todo es básicamente un _string_.

Puedes pensar que en el PHP moderno, puedes evitar este cambio de tipo _detrás-de-cámaras_ (malabarismo de 
tipos) mediante el uso de la función de tipos estrictos, pero eso no es completamente cierto. Declarar 
tipos estrictos evita que otros tipos pasen a una función, pero aún puede cambiar el valor de la variable 
en la función misma.

```php
declare(strict_types=1);

function find(int $id): Model
{
    $id = '' . $id;

    /*
     *  Esto es perfectamente válido en PHP
     * `$id` es un string ahora.
     */

    // …
}

find('1'); // Esto desencadenaría un TypeError.

find(1); // Esto estaría ok.
```

Incluso con tipos estrictos y sugerencias de tipos, el sistema de tipado de PHP es débil. Las sugerencias 
de tipo solo aseguran el tipo de una variable en ese instante en el tiempo, sin garantías sobre cualquier 
valor futuro que pueda tener esa variable.

Como dije antes: tiene sentido que PHP tenga un sistema de tipo débil, ya que todas las entradas con las 
que tiene que lidiar comienzan siendo strings. Sin embargo, hay una propiedad interesante para los tipos 
fuertes: vienen con algunas garantías. Si una variable tiene un tipo que no se puede cambiar, un rango 
completo de comportamiento inesperado simplemente ya no puede suceder.

Verás, es matemáticamente comprobable que si un programa fuertemente tipado se compila, es imposible que 
ese programa tenga una variedad de errores que puedan existir en lenguajes débilmente tipados. En otras 
palabras, los tipos fuertes le dan al programador un mejor seguro de que el código realmente se comporta 
como se supone que debe hacerlo.

Como nota al margen: ¡esto no significa que un lenguaje fuertemente tipado no pueda tener errores! Eres 
perfectamente capaz de escribir una implementación con errores. Pero cuando un programa fuertemente tipado 
se compila exitosamente, está seguro de que cierto conjunto de errores y errores no pueden ocurrir en 
ese programa.

> Los sistemas de tipo fuerte permiten a los desarrolladores tener mucha más información sobre el programa 
> al escribir el código, en lugar de tener que ejecutarlo.

Hay un concepto más que debemos considerar: los tipos estáticos y dinámicos, y aquí es donde las cosas 
comienzan a ponerse interesantes.

Como probablemente sepas, PHP es un lenguaje interpretado. Esto significa que un script PHP se traduce a 
código de máquina en tiempo de ejecución. Cuando envías una solicitud a un servidor que ejecuta PHP, 
este tomará esos archivos `.php` simples y analizará el texto en algo que el procesador pueda ejecutar.

Una vez más, este es uno de los puntos fuertes de PHP: la simplicidad de escribir un script, actualizar 
la página y todo está ahí. Esa es una gran diferencia en comparación con un lenguaje que debe compilarse 
antes de que pueda ejecutarse.

Obviamente, existen mecanismos de almacenamiento en caché que optimizan esto, por lo que la declaración 
anterior es una simplificación excesiva. Sin embargo, es lo suficientemente bueno como para obtener el 
siguiente punto.

Una vez más, hay un inconveniente: dado que PHP solo verifica sus tipos en tiempo de ejecución, las 
verificaciones de tipo del programa pueden fallar cuando se ejecutan. Esto significa que puede tener 
un error más claro para depurar, pero aún así el programa se ha bloqueado.

Esta comprobación de tipos en tiempo de ejecución hace que PHP sea un lenguaje de tipo dinámico. Por otro 
lado, un idioma escrito de forma estática tendrá todas sus verificaciones de tipo realizadas antes de 
que se ejecute el código.

A partir de PHP `7.0`, su sistema de tipos se ha mejorado bastante. Tanto es así que herramientas como 
[PHPStan](https://github.com/phpstan/phpstan), [phan](https://github.com/phan/phan) y 
[psalm](https://github.com/vimeo/psalm) comenzaron a ser muy populares últimamente. Estas herramientas 
toman el lenguaje dinámico que es PHP, pero ejecutan un montón de análisis estáticos en su código.

Estas librerías opcionales pueden ofrecer una gran cantidad de información sobre su código, sin tener 
que ejecutarlo o probarlo, un IDE como PhpStorm también tiene muchas de estas comprobaciones 
estáticas incorporadas.

Con toda esta información de fondo en mente, es hora de volver al núcleo de nuestra aplicación: la data.

### Estructurando data no estructurada

¿Alguna vez has tenido que trabajar con una "variedad de cosas" que en realidad era más que una simple 
lista? ¿Usaste las llaves de los arrays como campos? ¿Sentiste el dolor de no saber exactamente qué había 
en ese array? ¿No estás seguro de si los datos que contiene son realmente lo que esperas que sean o 
qué campos están disponibles?

Visualicemos de lo que estoy hablando: trabajar con los requests de Laravel. Piense en este ejemplo 
como una operación CRUD básica para actualizar un cliente existente:

```php
function store(CustomerRequest $request, Customer $customer) 
{
    $validated = $request->validated();
    
    $customer->name = $validated['name'];
    $customer->email = $validated['email'];
    
    // …
}
```

Es posible que ya veas surgir el problema: no sabemos exactamente qué datos están disponibles en el array 
$validated. Si bien los arreglos en PHP son una estructura de datos versátil y poderosa, tan pronto como 
se usan para representar algo diferente a "una lista de cosas", hay mejores maneras de resolver su problema.

Antes de buscar soluciones, esto es lo que puedes hacer para lidiar con esta situación:

- Leer el código fuente
- Leer la documentación
- "Arrojar" (_dump_) `$validated` para inspeccionarlo
- O usar un depurador para inspeccionarlo

Ahora imagina por un minuto que estás trabajando con un equipo de varios desarrolladores en este proyecto, 
y que tu colega ha escrito este código hace cinco meses: puedo garantizarte que no sabrás con qué datos 
estás trabajando, sin hacer ninguna de las cosas engorrosas enumeradas anteriormente.

Resulta que los sistemas fuertemente tipados en combinación con el análisis estático pueden ser de 
gran ayuda para comprender con qué estamos tratando exactamente. Idiomas como Rust, por ejemplo, resuelven 
este problema limpiamente:

```php
struct CustomerData {
    name: String,
    email: String,
    birth_date: Date,
}
```

¡Una estructura es lo que necesitamos! Lamentablemente, PHP no tiene estructuras. Tiene arreglos y objetos, 
y eso es todo.

Sin embargo... los objetos y las clases pueden ser suficientes:

```php
class CustomerData
{
    public string $name;
    public string $email;
    public Carbon $birth_date;
}
```

Ahora yo sé; las propiedades escritas solo están disponibles a partir de PHP `7.4`. Dependiendo de 
cuándo leas esta serie, es posible que aún no puedas usarlos. Tengo una solución para ti más adelante 
en este capítulo, sigue leyendo 😉.

Para aquellos que puedan usar PHP `7.4` o superior, pueden hacer cosas como esta:

```php
public function store(CustomerRequest $request, Customer $customer) 
{
    $validated = CustomerData::fromRequest($request);
    
    $customer->name = $validated->name;
    $customer->email = $validated->email;
    $customer->birth_date = $validated->birth_date;
    
    // …
}
```

El analizador estático integrado en tu IDE siempre podría decirnos con qué datos estamos tratando.

Este patrón de envolver datos no estructurados en tipos, para que podamos usar nuestros datos de 
manera confiable, se llama "objetos de transferencia de datos" (Data Transfer Objects - DTOs). 
Es el primer patrón concreto que te recomiendo usar en proyectos Laravel más grandes que el promedio.

Cuando discutas esta serie con tus colegas, amigos o dentro de la comunidad de Laravel, puedes 
toparte con personas que no comparten la misma visión sobre los sistemas de tipos fuertes. De hecho, 
hay muchas personas que prefieren adoptar el lado dinámico y débil de PHP. Y definitivamente hay 
algo que decir al respecto.

Sin embargo, según mi experiencia, el enfoque fuertemente tipado ofrece más ventajas cuando se trabaja 
con un equipo de varios desarrolladores en un proyecto durante mucho tiempo. Tienes que aprovechar 
todas las oportunidades que puedas para reducir la carga cognitiva. No deseas que los desarrolladores 
tengan que comenzar a depurar tu código cada vez que quieran saber qué hay exactamente en una variable. 
La información tiene que estar a la mano para que los desarrolladores puedan centrarse en lo que es 
importante: crear la aplicación.

Por supuesto, el uso de DTOs tiene un precio: no solo existe la sobrecarga de definir estas clases; 
también necesita mapear, por ejemplo, solicitar datos en un DTO.

Los beneficios del uso de DTOs definitivamente superan este costo que tiene que pagar. Independientemente 
del tiempo que pierdas al escribir este código, lo compensará a largo plazo.

Sin embargo, la pregunta sobre la construcción de DTOs a partir de datos "externos" es una pregunta 
que aún necesita respuesta.

### Fábricas de DTOs

¿Cómo construimos DTO? Compartiré dos posibilidades contigo y también explicaré cuál tiene mi 
preferencia personal.

El primero es el más correcto: usar una fábrica dedicada.

```php
class CustomerDataFactory
{
    public function fromRequest(
       CustomerRequest $request
    ): CustomerData {
        return new CustomerData([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'birth_date' => Carbon::make(
                $request->get('birth_date')
            ),
        ]);
    }
}
```

Tener una fábrica separada mantiene tu código limpio durante todo el proyecto. Tiene más sentido 
que esta fábrica viva en la capa de aplicación.

Si bien es la solución correcta, probablemente notaste que usé una taquigrafía en un ejemplo 
anterior, en la clase DTO en sí:

```php
CustomerData::fromRequest
```

¿Qué tiene de malo este enfoque? Bueno, para comenzar: agrega lógica específica de la aplicación 
en el dominio. El DTO que vive en el dominio ahora debe saber sobre la clase `CustomerRequest`, 
que vive en la capa de aplicación.

```php
use Spatie\DataTransferObject\DataTransferObject;

class CustomerData extends DataTransferObject
{
    // …
    
    public static function fromRequest(
        CustomerRequest $request
    ): self {
        return new self([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'birth_date' => Carbon::make(
                $request->get('birth_date')
            ),
        ]);
    }
}
```

Obviamente, mezclar código específico de la aplicación dentro del dominio no es la mejor de las ideas. 
Sin embargo, tiene mi preferencia. Hay dos razones para eso.

En primer lugar: ya establecimos que los DTO son el punto de entrada para los datos en la base de código. 
Tan pronto como trabajemos con datos del exterior, queremos convertirlos en un DTO. Necesitamos hacer 
este mapeo en algún lugar, por lo que podríamos hacerlo dentro de la clase para la que está destinado.

En segundo lugar, y esta es la razón más importante; Prefiero este enfoque porque es una de las 
limitaciones de PHP: no admite parámetros con nombre.

Verás, no desea que tus DTO terminen teniendo un constructor con un parámetro individual para cada 
propiedad: esto no escala y es muy confuso cuando se trabaja con propiedades con valores nulos o 
predeterminados. Es por eso que prefiero el enfoque de pasar un arreglo al DTO, y hacer que se 
construya en función de los datos de este arreglo. Como una nota adicional: utilizamos el paquete 
[spatie/data-transfer-object](https://github.com/spatie/data-transfer-object) para hacer exactamente esto.

Debido a que los parámetros con nombre no son compatibles, tampoco hay un análisis estático disponible, 
lo que significa que no sabes qué datos se necesitan cada vez que construye un DTO. Prefiero mantener este 
"estar en la oscuridad" dentro de la clase DTO, para que pueda usarse sin un pensamiento adicional desde 
el exterior.

Sin embargo, si PHP admitiera algo como parámetros con nombre, diría que el patrón de fábrica es 
el camino a seguir:

```php
public function fromRequest(CustomerRequest $request): CustomerData
{
    return new CustomerData(
        'name' => $request->get('name'),
        'email' => $request->get('email'),
        'birth_date' => Carbon::make(
            $request->get('birth_date')
        ),
    );
}
```

Nota la falta del arreglo al construir `CustomerData`.

Hasta que PHP soporte esto, elegiría la solución pragmática sobre la correcta teórica. Sin embargo, 
depende de ti. Siéntase libre de elegir lo que mejor se adapte a su equipo.

### Una alternativa a las propiedades tipificadas

Como mencioné anteriormente, existe una alternativa al uso de propiedades escritas para admitir 
DTO: docblocks. El paquete DTO que vinculé anteriormente también los admite.

```php
use Spatie\DataTransferObject\DataTransferObject;

class CustomerData extends DataTransferObject
{
    /** @var string */
    public $name;
    
    /** @var string */
    public $email;
    
    /** @var \Carbon\Carbon */
    public $birth_date;
}
```

Sin embargo, de forma predeterminada, los docblocks no dan ninguna garantía de que los datos sean del tipo 
que dicen que son. Afortunadamente, PHP tiene su API de reflexión, y con ella, es posible mucho más.

La solución proporcionada por este paquete puede considerarse como una extensión del sistema de tipo PHP. 
Si bien hay mucho que se puede hacer en "terreno del usuario" y en tiempo de ejecución, aún así agrega 
valor. Si no puede usar PHP `7.4` y desea tener un poco más de certeza de que sus tipos de docblock 
son realmente respetados, este paquete lo tiene cubierto.

------

Debido a que los datos se encuentran en el núcleo de casi todos los proyectos, es uno de los bloques 
de construcción más importantes. Los objetos de transferencia de datos le ofrecen una forma de trabajar 
con datos de forma estructurada, segura y predecible.

Notarás a lo largo de este libro que los DTO se usan con mayor frecuencia. Es por eso que era tan 
importante mirarlos en profundidad al principio. Del mismo modo, hay otro elemento fundamental que 
necesita nuestra atención: *las acciones*. Ese es el tema para el próximo capítulo, stay tuned 😉.

