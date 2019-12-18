---
extends: _layouts.post
section: content
title: "Más allá de CRUDs: Trabajando con data"
date: 2019-12-19
description: "Ahora que podemos trabajar con data de forma segura y transparente, debemos comenzar a hacer algo con ella."  
cover_image: /assets/images/posts/0010/mas-alla-de-cruds-03-acciones.png
featured: true
reference: https://stitcher.io/blog/laravel-beyond-crud-03-actions
categories: [laravel, php, programming]
---

Esta es el artículo #03 de la serie [Laravel: Más allá de CRUDs](/blog/laravel-mas-alla-de-cruds). Fue originalmente 
publicado por [Brent](https://mobile.twitter.com/brendt_gd) en su 
[blog](https://stitcher.io/blog/laravel-beyond-crud-03-actions) (puedes encontrar ahí la serie en 
su idioma original).

La tabla de contenido que conforma esta serie la [tienes aquí](/blog/laravel-mas-alla-de-cruds).

Dicho esto, continuemos 😉.

-------

> Ahora que podemos trabajar con data de forma segura y transparente, debemos comenzar a hacer algo con ella.

Al igual que no queremos trabajar con arreglos aleatorios llenos de data, tampoco queremos que la parte más 
crítica de nuestro proyecto, la lógica del negocio, se extienda a través de funciones y clases aleatorias.

Aquí tienes un ejemplo: una de las historias de usuario en tu proyecto podría ser "un administrador para crear 
una factura". Esto significa guardar la factura en la base de datos, pero también mucho más:

- Calcular el precio de cada línea de factura individual y el precio total
- Guardar la factura en la base de datos
- Crear un pago a través del proveedor de pagos
- Crear un PDF con toda la información relevante
- Enviar este PDF al cliente

Una práctica común en Laravel es crear "modelos gordos" que manejarán toda esta funcionalidad. 
En este capítulo veremos otro enfoque para agregar este comportamiento a nuestra base de código.

En lugar de mezclar la funcionalidad en modelos o controladores, trataremos estas historias de usuario 
como _ciudadanos de primera clase_ del proyecto. Tiendo a llamar a estas: "acciones".

### Terminología

Antes de analizar su uso, debemos analizar cómo se estructuran las acciones. Para empezar, viven en el dominio.

En segundo lugar, son clases simples sin abstracciones ni interfaces. Una acción es una clase que toma entrada, 
hace algo y tiene una salida. Es por eso que una acción generalmente solo tiene un método público y, en ciertos 
casos, un constructor.

Como convención en nuestros proyectos, decidimos añadir un sufijo a todas nuestras clases. Seguro que 
`CreateInvoice` suena bien, pero tan pronto como estés lidiando con varios cientos o miles de clases, querrás 
asegurarte de que no se produzcan conflictos de nombres. Verás, `CreateInvoice`, también podría ser el nombre 
de un controlador _invocable_, de un comando, de un job o de un request. Preferimos eliminar la mayor confusión 
posible, por lo tanto, `CreateInvoiceAction` será el nombre correcto.

Evidentemente, esto significa que los nombres de nuestras clases se alargarán. La realidad es que si estás 
trabajando en proyectos más grandes, no vas a poder evitar elegir nombres largos para asegurarte de que no 
exista lugar a confusión. Aquí hay un ejemplo extremo de uno de nuestros proyectos, no estoy bromeando: 
 `CreateOrUpdateHabitantContractUnitPackageAction`.

Odiamos este nombre al principio. Intentamos desesperadamente encontrar uno más corto. Al final, tuvimos que 
admitir que la claridad de lo que se trata una clase es lo más importante. De todos modos, el auto-completado 
de nuestro IDE se encargará de los inconvenientes de los nombres largos.

Cuando nos decidimos por un nombre de clase, el siguiente obstáculo a superar es nombrar el método público 
para usar nuestra acción. Una opción es hacerlo invocable, de este modo:

```php
class CreateInvoiceAction
{
    public function __invoke(InvoiceData $invoiceData): Invoice
    {
        // …
    }
}
```

Sin embargo, hay un problema práctico con este enfoque. Más adelante en este capítulo hablaremos sobre cómo 
componer acciones a partir de otras acciones y cómo es un patrón poderoso. Se vería algo así:

```php
class CreateInvoiceAction
{
    private $createInvoiceLineAction;

    public function __construct(
        CreateInvoiceLineAction $createInvoiceLineAction
    ) { /* … */ }

    public function __invoke(InvoiceData $invoiceData): Invoice
    {
        // ...

        foreach ($invoiceData->lines as $lineData) {
            $invoice->addLine(
                ($this->createInvoiceLineAction)($lineData)
            );
        }
    }
}
```

¿Puedes detectar el problema? PHP no permite invocar directamente un invocable cuando es una propiedad 
de clase, ya que PHP está buscando un método de clase. Es por eso que tendrás que ajustar la acción 
entre paréntesis antes de llamarla.

Si bien esto es solo un inconveniente menor, hay un problema adicional con PhpStorm: no puede proporcionar 
el auto-completado de parámetros al llamar a la acción de esta manera. Personalmente, creo que el uso 
adecuado del IDE es una parte integral del desarrollo de un proyecto y no debe ser ignorado. Es por eso 
que hoy en día, nuestro equipo decide no hacer _invocables_ a las acciones.

Otra opción es usar `handle`, que a menudo es usado por Laravel como nombre predeterminado en este tipo 
de casos. Una vez más, hay un problema con esta, específicamente porque Laravel lo usa.

Siempre que Laravel te permita usar el `handle`, por ejemplo en jobs o comandos, también proporcionará 
la inyección de métodos desde el contenedor de dependencias. En nuestras acciones solo queremos que el 
constructor tenga capacidades de DI (inyección de dependencias). Nuevamente, analizaremos detenidamente 
las razones detrás de esto más adelante en este capítulo.

Entonces `handle` también está descartado. Cuando comenzamos a usar acciones, pensamos mucho en este enigma 
de nombres. Al final nos decidimos por `execute` (ejecutar). Sin embargo, ten en cuenta que eres libre de 
crear tus propias convenciones de nomenclatura: el punto aquí es más sobre el patrón de usar acciones que 
sobre sus nombres.

### En la práctica

Con toda la terminología fuera del camino, hablemos sobre por qué las acciones son útiles y cómo usarlas 
realmente.

Primero hablemos sobre la reutilización. El truco cuando se usan acciones es dividirlas en piezas lo suficientemente 
pequeñas para que algunas cosas sean reutilizables, mientras se mantienen lo suficientemente grandes como para 
no terminar con una sobrecarga de estas. Toma nuestro ejemplo de factura: generar un PDF a partir de una factura 
es algo que probablemente suceda desde varios contextos en nuestra aplicación. Claro que está el PDF que se genera 
cuando se crea una factura, pero un administrador también puede querer ver una vista previa o un borrador antes 
de enviarlo.

Estas dos historias de usuario: "crear una factura" y "previsualizar una factura" obviamente requieren dos 
puntos de entrada, dos controladores. Por otro lado, sin embargo, generar el PDF basado en la factura es algo 
que se hace en ambos casos.

Cuando empieces a pasar tiempo pensando en lo que realmente hará la aplicación, notarás que hay muchas acciones 
que pueden reutilizarse. Por supuesto, también debemos tener cuidado de no abstraer demasiado nuestro código. 
A menudo es mejor copiar y pegar un pequeño código que hacer abstracciones prematuras.

Una buena regla general es pensar en la funcionalidad al hacer abstracciones, en lugar de las propiedades técnicas 
del código. Cuando dos acciones pueden hacer cosas similares, aunque lo hacen en contextos completamente diferentes, 
debes tener cuidado de no comenzar a abstraerlas demasiado pronto.

Por otro lado, hay casos en que las abstracciones pueden ser útiles. Tome nuevamente nuestro ejemplo de PDF de 
facturas: es probable que necesite generar más PDFs que solo para facturas, al menos este es el caso en 
nuestros proyectos. Puede tener sentido tener una `GeneratePdfAction` general, que puede funcionar con una 
interfaz, que `Invoice` luego implemente.

Pero, seamos honestos, es probable que la mayoría de nuestras acciones sean bastante específicas pas sus 
historias de usuario y que no sean reutilizables. Puedes pensar que las acciones, en estos casos, son gastos 
generales innecesarios. Sin embargo, espera, porque la reutilización no es la única razón para usarlos. 
En realidad, la razón más importante no tiene nada que ver con los beneficios técnicos: las acciones permiten 
al programador pensar de manera más cercana al mundo real, en lugar del código.

Supongamos que necesitas hacer cambios en la forma en que se crean las facturas. Una aplicación típica de 
Laravel probablemente tendrá esta lógica de creación de facturas distribuida en un controlador y un modelo, 
tal vez un _job_ que genere el PDF, y finalmente un _listener_ de eventos para enviar el correo de la factura. 
Esos son muchos lugares que debes conocer. Una vez más, nuestro código se extiende a través de la base de 
código, agrupada por sus propiedades técnicas, en lugar de su significado.

Las acciones reducen la carga cognitiva que introduce dicho sistema. Si necesitas trabajar en cómo se crean 
las facturas, simplemente puedes ir a la clase de la acción y comenzar desde allí.

No te equivoques: las acciones pueden funcionar bien, por ejemplo, con _jobs asíncronos_ y _listeners_ de 
eventos; sin embargo, estos _jobs_ y _listeners_ simplemente proporcionan la infraestructura para que las 
acciones funcionen, y no la lógica de negocio en sí misma. Este es un buen ejemplo de por qué necesitamos 
dividir las capas de dominio y aplicación: cada una tiene su propio propósito.

Así que obtuvimos reutilización y una reducción de la carga cognitiva, ¡pero aún hay más!

Debido a que las acciones son pequeñas piezas de software que viven prácticamente solas, es muy fácil 
testearlas de forma individual (pruebas unitarias). En tus pruebas, no tienes que preocuparse por enviar 
requests HTTP falsas, configurar mocks de Facades, etc. Simplemente puede realizar una nueva acción, 
tal vez proporcionar algunas dependencias simuladas, pasarle los datos de entrada requeridos y hacer 
afirmaciones en su salida.

Por ejemplo, `CreateInvoiceLineAction`: tomará datos sobre qué artículo facturará, así como una cantidad 
y un período; y calculará el precio total y los precios con y sin impuestos. Estas son cosas para las que puedes 
escribir pruebas unitarias robustas pero simples.

Si todas tus acciones se prueban correctamente de manera unitaria, puedes estar seguro de que la mayor parte 
de la funcionalidad que debe proporcionar la aplicación realmente funcionará según lo previsto. Ahora solo es 
cuestión de usar estas acciones de manera que tengan sentido para el usuario final, y escribir algunas pruebas 
de integración para esas piezas.

### Componiendo acciones

Una característica importante de las acciones que ya mencioné antes brevemente, es cómo usan la inyección 
de dependencias. Como estamos usando el constructor para pasar datos del contenedor y el método `execute` 
para pasar datos relacionados con el contexto; somos libres de componer acciones a partir de acciones a partir 
de acciones a partir de ...

Ya entiendes la idea. Sin embargo, seamos claros que una cadena de dependencia profunda es algo que vas a 
desear evitar (hace que el código sea complejo y altamente dependiente el uno del otro), sin embargo, hay 
varios casos en los que tener DI es muy beneficioso.

Toma nuevamente el ejemplo de `CreateInvoiceLineAction` que tiene que calcular los precios del impuesto. 
Ahora, según el contexto, una línea de factura puede tener un precio que incluya o excluya impuestos. 
Calcular los precios del impuesto es algo trivial, sin embargo, no queremos que nuestra `CreateInvoiceLineAction` 
se preocupe por los detalles de la misma.

Imaginemos que tenemos una clase `VatCalculator` simple, que es algo que podría vivir en el namespace `\Support`, 
podría inyectarse así:

```php
class CreateInvoiceLineAction
{
    private $vatCalculator;

    public function __construct(VatCalculator $vatCalculator)
    { 
        $this->vatCalculator = $vatCalculator;
    }
    
    public function execute(
        InvoiceLineData $invoiceLineData
    ): InvoiceLine {
        // …
    }
}
```

Y la usarías de este modo:

```php
public function execute(
    InvoiceLineData $invoiceLineData
): InvoiceLine {
    $item = $invoiceLineData->item;

    if ($item->vatIncluded()) {
        [$priceIncVat, $priceExclVat] = 
            $this->vatCalculator->vatIncluded(
                $item->getPrice(),
                $item->getVatPercentage()
            );
    } else {
        [$priceIncVat, $priceExclVat] = 
            $this->vatCalculator->vatExcluded(
                $item->getPrice(),
                $item->getVatPercentage()
            );
    }

    $amount = $invoiceLineData->item_amount;
    
    $invoiceLine = new InvoiceLine([
        'item_price' => $item->getPrice(),
        'total_price' => $amount * $priceIncVat,
        'total_price_excluding_vat' => $amount * $priceExclVat,
    ]);
}
```

`CreateInvoiceLineAction` a su vez se inyectaría en `CreateInvoiceAction`. Y este nuevamente tiene otras 
dependencias, `CreatePdfAction` y `SendMailAction`, por ejemplo.

Puedes ver cómo la composición puede ayudarte a mantener pequeñas las acciones individuales y, al mismo 
tiempo, permitir que la lógica de negocio compleja se codifique de una manera clara y fácil de mantener.

### Alternativas a las acciones

Hay dos paradigmas que debo mencionar en este punto, dos formas en que no necesitarías un concepto como 
acciones.

El primero será conocido por personas que estén familiarizadas con DDD: comandos y controladores. Las 
acciones son una versión simplificada de ellas. Cuando los comandos y los controladores hacen una distinción 
entre lo que debe suceder y cómo debe suceder, las acciones combinan estas dos responsabilidades en una sola. 
Es cierto que el bus de comandos ofrece más flexibilidad que las acciones. Por otro lado, también requiere 
que escribas más código.

Para el alcance de nuestros proyectos, dividir las acciones en comandos y controladores estaba yendo demasiado 
lejos. Casi nunca necesitaríamos la flexibilidad adicional, sin embargo, tomaría mucho más tiempo escribir el 
código.

La segunda alternativa que vale la pena mencionar son los sistemas controlados por eventos. Si alguna vez 
trabajaste en un sistema impulsado por eventos, podría pensar que las acciones están demasiado directamente 
acopladas a los lugares donde realmente se utilizan. Una vez más se aplica el mismo argumento: los sistemas 
basados ​​en eventos ofrecen más flexibilidad, pero para nuestros proyectos hubiera sido excesivo usarlos. 
Además, los sistemas controlados por eventos agregan una capa de indirecta que hace que el código sea más 
complejo para razonar. Si bien esta indirecta ofrece beneficios, no superarían el costo de mantenimiento 
para nosotros.


------

Espero que esté claro que no estoy sugiriendo que lo tenemos todo resuelto y que tengamos la solución perfecta 
para todos los proyectos de Laravel. No lo estamos haciendo. Cuando continúes leyendo esta serie, es importante 
que vigiles las necesidades específicas de tu proyecto. Si bien es posible que pueda usar algunos conceptos 
propuestos aquí, también puede necesitar algunas otras soluciones para resolver ciertos aspectos.

Para nosotros, las acciones son la elección correcta porque ofrecen la cantidad correcta de flexibilidad, 
reutilización y reducen significativamente la carga cognitiva. Encapsulan la esencia de la aplicación. De 
hecho, pueden considerarse, junto con los DTO y los modelos, como el verdadero núcleo del proyecto.

Eso nos lleva al siguiente capítulo, la última pieza del núcleo: los modelos.
