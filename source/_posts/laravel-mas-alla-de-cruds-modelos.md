---
extends: _layouts.post
section: content
title: "Más allá de CRUDs: Modelos"
date: 2019-12-21
description: "En los capítulos anteriores, hemos hablado sobre dos de los tres componentes básicos de cada aplicación: DTO y acciones: datos y funcionalidad. En este capítulo veremos la última pieza que considero parte de este núcleo: exponer los datos que persisten en un almacén de datos; en otras palabras: modelos."  
cover_image: /assets/images/posts/0011/mas-alla-de-cruds-04-modelos.png
featured: true
reference: https://stitcher.io/blog/laravel-beyond-crud-04-models
categories: [laravel, php, programming]
---

Esta es el artículo #04 de la serie [Laravel: Más allá de CRUDs](/blog/laravel-mas-alla-de-cruds). Fue originalmente 
publicado por [Brent](https://mobile.twitter.com/brendt_gd) en su 
[blog](https://stitcher.io/blog/laravel-beyond-crud-04-models) (puedes encontrar ahí la serie en 
su idioma original).

La tabla de contenido que conforma esta serie la [tienes aquí](/blog/laravel-mas-alla-de-cruds).

Dicho esto, continuemos 😉.

-------

En los capítulos anteriores, hemos hablado sobre dos de los tres componentes básicos de cada aplicación: DTO y 
acciones: datos y funcionalidad. En este capítulo veremos la última pieza que considero parte de este núcleo: 
exponer los datos que persisten en un almacén de datos; en otras palabras: modelos.

Ahora, los modelos son un tema complicado. Laravel proporciona mucha funcionalidad a través de sus clases
modelo Eloquent, lo que significa que no solo representan los datos en un almacén de datos, sino que también 
te permiten crear consultas, cargar y guardar datos, tienen un sistema de eventos incorporado y más.

En este capítulo, no te diré que abandones toda la funcionalidad del modelo que proporciona Laravel; de 
hecho, es bastante útil. Sin embargo, mencionaré algunas trampas con las que debes tener cuidado y soluciones 
para ellas; de modo que incluso en proyectos grandes, los modelos no serán la causa del mantenimiento difícil.

Mi punto de vista es que debemos adoptar el framework, en lugar de tratar de luchar contra él; aunque 
deberíamos adoptarlo de tal manera que los proyectos grandes se mantengan mantenibles. Así que sumerjámonos.

### Modelo ≠ lógica de negocio

El primer inconveniente con el que se encuentran muchos desarrolladores es que piensan en los modelos como 
el lugar ideal para la lógica de negocio. Ya mencioné algunas responsabilidades de los modelos que están 
integradas en Laravel, y argumentaría que tengas cuidado de no agregar más.

Suena muy atractivo al principio, poder hacer algo como `$invoiceLine->price_incuding_vat` o 
`$invoice-> total_price;` y seguro que sí. De hecho, creo que las facturas y las líneas de factura deberían 
tener estos métodos. Sin embargo, hay una distinción importante que hacer: *estos métodos no deberían 
calcular nada*. Echemos un vistazo a lo que no hay que hacer:

Aquí hay un descriptor de acceso `total_price` en nuestro modelo `Invoice`, recorriendo todas las líneas de 
factura y haciendo la suma de su precio total.

```php
class Invoice extends Model
{
    public function getTotalPriceAttribute(): int
    {
        return $this->invoiceLines
            ->reduce(function (int $totalPrice, InvoiceLine $invoiceLine) {
                return $totalPrice + $invoiceLine->total_price;
            }, 0);
    }
}
```

Y así es como se calcula el precio total por línea.

```php
class InvoiceLine extends Model
{
    public function getTotalPriceAttribute(): int
    {
        $vatCalculator = app(VatCalculator::class);
    
        $price = $this->item_amount * $this->item_price;

        if ($this->price_excluding_vat) {
            $price = $vatCalculator->totalPrice(
                $price, 
                $this->vat_percentage
            );
        }
    
        return $price;
    }
}
```

Como leíste el capítulo anterior sobre acciones, puede adivinar lo que haría en su lugar: calcular 
el precio total de una factura es una historia de usuario que debe representarse mediante una acción.

Los modelos `Invoice` e `InvoiceLine` podrían tener las propiedades simples `total_price` y 
`price_incuding_vat`, pero primero se calculan mediante acciones y luego se almacenan en la base de datos. 
Al usar `$invoice-> total_price`, simplemente está leyendo datos que ya se han calculado antes.

Hay algunas ventajas en este enfoque. Primero el obvio: rendimiento, solo estás haciendo los cálculos una 
vez, no siempre cuando necesitas los datos. En segundo lugar, puede consultar los datos calculados directamente. 
Y tercero: no tiene que preocuparse por los efectos secundarios.

Ahora, podríamos comenzar un debate purista sobre cómo la responsabilidad individual ayuda a que sus clases 
sean pequeñas, mejor mantenibles y fácilmente comprobables; y cómo la inyección de dependencia es superior a 
la ubicación del servicio; pero prefiero decir lo obvio en lugar de tener largos debates teóricos donde sé 
que simplemente hay dos lados que no estarán de acuerdo.

Entonces, lo obvio: aunque te gustaría poder hacer `$invoice->send()` o `$invoice->toPdf()`, el código del 
modelo está creciendo y creciendo. Esto es algo que sucede con el tiempo, no parece ser un gran problema 
al principio. `$invoice->toPdf()` en realidad solo puede ser una o dos líneas de código.

Sin embargo, por experiencia, una o dos líneas suman. "Una o dos líneas" no son el problema, pero cien veces 
"una o dos líneas" sí lo son. La realidad es que las clases modelo crecen con el tiempo y pueden crecer 
bastante.

Incluso si no estás de acuerdo conmigo en las ventajas que aporta la inyección de responsabilidad individual 
y de dependencia, hay poco en desacuerdo sobre esto: una clase de modelo con cientos de líneas de código no 
se puede mantener.

Todo eso para decir esto: piensa en los modelos y su propósito como proporcionarle datos solamente, deja que 
algo más se preocupe por asegurarse de que los datos se calculen correctamente.

### Reducción de modelos

Si nuestro objetivo es mantener las clases de modelos razonablemente pequeñas, lo suficientemente pequeñas 
como para poder comprenderlas simplemente abriendo su archivo, necesitamos mover algunas cosas más. 
Idealmente, solo queremos mantener getters y setters, simples accesores y mutadores, _casts_ y relaciones.

Otras responsabilidades deben trasladarse a otras clases. Un ejemplo son los Query Scopes: podríamos 
moverlos fácilmente a clases generadoras de consultas dedicadas.

Lo creas o no: las clases generadoras de consultas son en realidad la forma normal de usar Eloquent; 
los _scopes_ son simplemente "versiones endulzadas" encima de estas. Así es como podría verse una 
clase generadora de consultas.

```php
namespace Domain\Invoices\QueryBuilders;

use Domain\Invoices\States\Paid;
use Illuminate\Database\Eloquent\Builder;

class InvoiceQueryBuilder extends Builder
{
    public function wherePaid(): self
    {
        return $this->whereState('status', Paid::class);
    }
}
```

A continuación, anulamos el método `newEloquentBuilder` en nuestro modelo y devolvemos nuestra clase 
personalizada. Laravel lo usará a partir de ahora.

```php
namespace Domain\Invoices\Models;

use Domain\Invoices\QueryBuilders\InvoiceQueryBuilder;

class Invoice extends Model 
{
    public function newEloquentBuilder($query): InvoiceQueryBuilder
    {
        return new InvoiceQueryBuilder($query);
    }
}
```

Esto es lo que quise decir al "adoptar el framework": no es necesario introducir nuevos patrones como 
repositorios _per se_, puedes construir sobre lo que ya proporciona Laravel. Pensándolo bien, logramos 
el equilibrio perfecto entre el uso de los productos proporcionados por el framework y la prevención de 
que nuestro código crezca demasiado en lugares específicos.

Con esta mentalidad, también podemos proporcionar clases _collection_ personalizadas para las relaciones. 
Laravel tiene un gran soporte de colecciones, aunque a menudo terminas con largas cadenas de funciones de 
esta clase, ya sea en el modelo o en la capa de aplicación. Esto -nuevamente- no es ideal, y afortunadamente 
Laravel nos proporciona los ganchos necesarios para agrupar la lógica de las colecciones en una clase dedicada.

Aquí hay un ejemplo de una clase _collection_ personalizada, y ten en cuenta que es completamente posible 
combinar varios métodos en otros nuevos, evitando largas cadenas de funciones en otros lugares.

```php
namespace Domain\Invoices\Collections;

use Domain\Invoices\Models\InvoiceLines;
use Illuminate\Database\Eloquent\Collection;

class InvoiceLineCollection extends Collection
{
    public function creditLines(): self
    {
        return $this->filter(function (InvoiceLine $invoiceLine) {
            return $invoiceLine->isCreditLine();
        });
    }
}
```

Así es como vinculas una clase _collection_ con un modelo; ``InvoiceLine``, en este caso:

```php
namespace Domain\Invoices\Models;

use Domain\Invoices\Collection\InvoiceLineCollection;

class InvoiceLine extends Model 
{
    public function newCollection(array $models = []): InvoiceLineCollection
    {
        return new InvoiceLineCollection($models);
    }

    public function isCreditLine(): bool
    {
        return $this->price < 0.0;
    }
}
```

Cada modelo que tenga una relación `HasMany` con `InvoiceLine`, ahora usará nuestra clase _collection_ 
en su lugar.

```php
$invoice
    ->invoiceLines
    ->creditLines()
    ->map(function (InvoiceLine $invoiceLine) {
        // …
    });
```

Intenta mantener tus modelos limpios y orientados a los datos, en lugar de hacer que proporcionen lógica 
de negocio. Hay mejores lugares para manejarlo.

