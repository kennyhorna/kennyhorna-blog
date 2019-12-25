---
extends: _layouts.post
section: content
title: "Más allá de CRUDs: [05] Estados"
date: 2019-12-22
description: "El patrón de diseño Estado es una de las mejores formas de agregar comportamientos específicos de estado al modelo, mientras los mantiene limpios."  
cover_image: /assets/images/posts/0012/mas-alla-de-cruds-05-estados.png
featured: true
reference: https://stitcher.io/blog/laravel-beyond-crud-05-states
categories: [laravel, php, programming]
---

El patrón de diseño "Estado" es una de las mejores formas de agregar comportamientos específicos de estado al modelo, 
mientras los mantiene limpios.

-----

Este es el artículo #05 de la serie [Laravel: Más allá de CRUDs](/blog/laravel-mas-alla-de-cruds). Fue originalmente 
publicado por [Brent](https://mobile.twitter.com/brendt_gd) en su 
[blog](https://stitcher.io/blog/laravel-beyond-crud-05-states) (puedes encontrar ahí la serie en 
su idioma original).

La tabla de contenido que conforma esta serie la [tienes aquí](/blog/laravel-mas-alla-de-cruds).

Dicho esto, comencemos 😉.

-------

Este capítulo hablará sobre el patrón Estado y específicamente cómo aplicarlo a modelos. Puedes pensar en 
este capítulo como una extensión [del anterior (modelos)](/blog/laravel-mas-alla-de-cruds-modelos), donde escribí 
sobre cómo pretendemos mantener nuestros modelos manejables al evitar que manejen lógica de negocio.

Sin embargo, alejar la lógica del negocio de los modelos plantea un problema con un caso de uso muy común: **¿qué 
hacer con los estados del modelo?**

Una factura puede estar pendiente o pagada, un pago puede fallar o tener éxito. Dependiendo del estado, un modelo 
debe comportarse de manera diferente; ¿Cómo podemos cerrar esta brecha entre los modelos y la lógica empresarial?

Los estados -y las transiciones entre ellos- son un caso de uso frecuente en grandes proyectos; tan frecuentes que 
merecen un capítulo propio.

### El patrón Estado

En esencia, el [patrón Estado](https://es.wikipedia.org/wiki/State_(patr%C3%B3n_de_dise%C3%B1o)) es un patrón simple, 
pero permite una funcionalidad muy poderosa. Tomemos nuevamente el ejemplo de las facturas: una factura puede estar 
pendiente o pagada. Para empezar, daré un ejemplo muy simple, porque quiero que entiendas cómo el patrón de estado 
nos permite mucha flexibilidad.

Digamos que el resumen de la factura debe mostrar una insignia que represente el estado de esa factura, es de color 
**naranja** cuando está _pendiente_ y **verde** cuando se _paga_.

Un enfoque de "modelo gordo" ingenuo haría algo como esto:

```php
class Invoice extends Model
{
    // …
    
    public function getStateColour(): string
    {
        if ($this->state->equals(InvoiceState::PENDING())) {
            return 'orange';
        }
    
        if ($this->state->equals(InvoiceState::PAID())) {
            return 'green';
        }

        return 'gray';
    }
}
```

Como estamos utilizando algún tipo de 
[clase enum](http://arco.inf-cr.uclm.es/~david.villa/pensar_en_C++/vol1/ch03s08s03.html) para representar el 
valor del estado, podríamos mejorar esto de la siguiente manera:

```php
class Invoice extends Model
{
    // …
    
    public function getStateColour(): string
    {
        return $this->state->getColour();
    }
}
```

```php
/**
 * @method static self PENDING()
 * @method static self PAID()
 */
class InvoiceState extends Enum
{
    private const PENDING = 'pending';
    private const PAID = 'paid';

    public function getColour(): string
    {
        if ($this->value === self::PENDING) {
            return 'orange';
        }
    
        if ($this->value === self::PAID) {
            return 'green'
        }

        return 'gray';
    }
}
```

 > Como nota aparte, estoy asumiendo el empleo del paquete [myclabs/php-enum](https://github.com/myclabs/php-enum). 
> Como mejora adicional, para una mejor medición, podríamos escribir lo anterior de modo más corto utilizando
> arreglos.

```php
class InvoiceState extends Enum
{
    public function getColour(): string
    {
        return [
            self::PENDING => 'orange',
            self::PAID => 'green',
        ][$this->value] ?? 'gray';
    }
}
```

Cualquiera sea el enfoque que prefieras, en esencia estás enumerando todas las opciones disponibles, verificando 
si una de ellas coincide con la actual y haciendo algo en función del resultado. Es una gran declaración `if`/`else`, 
con cualquier "azúcar sintáctico" que prefieras.

Con este enfoque, agregamos una responsabilidad, ya sea al modelo o la clase enum: tiene que saber qué debe hacer un 
estado específico, tiene que saber cómo funciona un estado. El patrón Estado cambia esto al revés: trata a "un estado" 
como un ciudadano-de-primera-clase de nuestra base de código. Cada estado está representado por una clase separada, 
y cada una de estas clases actúa sobre un tema.

¿Es difícil de entender? Vamos a hacerlo paso a paso.

Comenzaremos con una clase abstracta `InvoiceState`, esta clase describirá toda la funcionalidad que los estados de 
factura concretos pueden proporcionar. En nuestro caso, queremos que un estado proporcione un _color_.

```php
abstract class InvoiceState
{
    abstract public function colour(): string;
}
```

A continuación, crearemos dos clases, cada una de estas representará un estado concreto.

```php
class PendingInvoiceState extends InvoiceState
{
    public function colour(): string
    {
        return 'orange';
    }
}
```

```php
class PaidInvoiceState extends InvoiceState
{
    public function colour(): string
    {
        return 'green';
    }
}
```

Lo primero que debes notar es que cada una de estas clases se puede probar fácilmente por su cuenta.

```php
class InvoiceStateTest extends TestCase
{
    /** @test */
    public function the_colour_of_pending_is_orange
    {   
        $state = new PendingInvoiceState();
        
        $this->assertEquals('orange', $state->colour());
    }
}
```

En segundo lugar, debes tener en cuenta que los colores son un ejemplo ingenuo que estamos utilizando para explicar 
el patrón. También podrías tener lógica de negocio más compleja encapsulada por un estado. Toma este ejemplo: 
_¿se debe pagar una factura?._ Esto, por supuesto, depende del estado, si ya fue pagado o no, pero también podría 
depender del tipo de factura con la que estamos tratando. Digamos que nuestro sistema admite notas de crédito que 
no tienen que pagarse, o permite facturas con un precio de `0`. Esta lógica de negocio puede ser encapsulada por 
las clases de estado.

Sin embargo, hay una cosa que falta para que esta funcionalidad funcione: necesitamos poder ver el modelo desde 
nuestra clase de estado, si vamos a decidir si esa factura debe pagarse o no. Es por eso que tenemos nuestra 
clase padre abstracta `InvoiceState`; agreguemos los métodos requeridos allí.

```php
abstract class InvoiceState
{
    /** @var Invoice */
    protected $invoice;

    public function __construct(Invoice $invoice) { /* … */ }

    abstract public function mustBePaid(): bool;
    
    // …
}
```

Por lo que ahora deberemos implementar en las clases abstractas:

```php
class PendingInvoiceState extends InvoiceState
{
    public function mustBePaid(): bool
    {
        return $this->invoice->total_price > 0
            && $this->invoice->type->equals(InvoiceType::DEBIT());
    }
    
    // …
}
```

```php
class PaidInvoiceState extends InvoiceState
{
    public function mustBePaid(): bool
    {
        return false;
    }
    
    // …
}
```

Nuevamente, podemos escribir pruebas unitarias simples para cada estado, y nuestro modelo de factura simplemente 
puede hacer esto:

```php
class Invoice extends Model
{
    public function getStateAttribute(): InvoiceState
    {
        return new $this->state_class($this);
    }
    
    public function mustBePaid(): bool
    {
        return $this->state->mustBePaid();
    } 
}
```

Finalmente, en la base de datos podemos guardar la clase de estado del modelo concreto en el campo `state_class` y 
listo. Obviamente, hacer este mapeo manualmente (guardar y cargar desde y hacia la base de datos) se vuelve tedioso 
muy rápidamente. Es por eso que [creé un paquete](https://github.com/spatie/laravel-model-states) que se encarga 
de todo el trabajo duro por ti.

Sin embargo, el comportamiento específico del estado, en otras palabras "el patrón Estado", es solo la mitad de 
la solución; todavía tenemos que manejar la transición del estado de la factura de uno a otro, y asegurarnos de que 
solo estados específicos puedan pasar a otros. Así que echemos un vistazo a las transiciones de estado.

### Transiciones

¿Recuerdas cómo hablé sobre alejar la lógica de negocio de los modelos y solo permitirles proporcionar datos de la 
base de datos de una manera viable? El mismo pensamiento puede aplicarse a estados y transiciones. Deberíamos evitar 
los efectos secundarios al usar estados, cosas como hacer cambios en la base de datos, enviar correos, etc. Los 
estados deben usarse para leer o proporcionar datos. Las transiciones, por otro lado, no proporcionan nada. Por el 
contrario, se aseguran de que nuestro estado del modelo se transite correctamente de uno a otro, lo que lleva a 
efectos secundarios aceptables.

Dividir estas dos preocupaciones en clases separadas nos da las mismas ventajas sobre las que escribí una y otra vez: 
mejor capacidad de prueba y reducción de la carga cognitiva. Permitir que una clase solo tenga una responsabilidad 
hace que sea más fácil dividir un problema complejo en varias partes fáciles de entender.

Entonces, _transiciones_: una clase que tomará un modelo, una factura en nuestro caso, y cambiará el estado de esa 
factura, si está permitido, a otra. En algunos casos, puede haber pequeños efectos secundarios como escribir un 
mensaje de registro o enviar una notificación sobre la transición de estado. Una implementación ingenua podría verse 
más o menos así.

```php
class PendingToPaidTransition
{
    public function __invoke(Invoice $invoice): Invoice
    {
        if (! $invoice->mustBePaid()) {
            throw new InvalidTransitionException(self::class, $invoice);
        }

        $invoice->status_class = PaidInvoiceState::class;
        $invoice->save();
    
        History::log($invoice, "Pending to Paid");
    }
}
```

Nuevamente, hay muchas cosas que puedes hacer con este patrón básico:

- Definir todas las transiciones permitidas en el modelo.
- Transición de un estado directamente a otro, mediante el uso de una clase de transición bajo el capó
- Determine automáticamente a qué estado pasar en función de un conjunto de parámetros

Nuevamente, el paquete que mencioné antes agrega soporte para las transiciones, así como la gestión básica de 
la transición. Sin embargo, si deseas máquinas de estado complejas, es posible que desee ver otras soluciones. 

### Estados sin transiciones

Cuando pensamos en "estado", a menudo pensamos que no pueden existir sin transiciones. Sin embargo, eso no es 
cierto: un objeto puede tener un estado que nunca cambia y no se requieren transiciones para aplicar el patrón 
Estado. ¿Porque es esto importante? Bueno, eche un vistazo nuevamente a nuestra implementación 
`PendingInvoiceState::mustBePaid`:

```php
class PendingInvoiceState extends InvoiceState
{
    public function mustBePaid(): bool
    {
        return $this->invoice->total_price > 0
            && $this->invoice->type->equals(InvoiceType::DEBIT());
    }
}
```

Dado que queremos usar el patrón Estado para reducir los bloques frágiles `if`/`else` en nuestro código, ¿puedes 
adivinar a dónde voy con esto? ¿Has considerado que `$this->invoice->type->equals(InvoiceType::DEBIT())` es de 
hecho una declaración `if` disfrazada?

¡De hecho,`InvoiceType` también podría aplicar el patrón Estado! Es simplemente un estado que probablemente nunca 
cambiará para un objeto dado. Mira esto:

```php
abstract class InvoiceType
{
    /** @var Invoice */
    protected $invoice;
    
    // …

    abstract public function mustBePaid(): bool;
}
```

```php
class CreditInvoiceType extends InvoiceType
{
    public function mustBePaid(): bool
    {
        return false
    }
}
```

```php
class DebitInvoiceType extends InvoiceType
{
    public function mustBePaid(): bool
    {
        return true;
    }
}
```

Ahora podemos refactorizar nuestro `PendingInvoiceState::mustBePaid` de esta manera.

```php
class PendingInvoiceState extends InvoiceState
{
    public function mustBePaid(): bool
    {
        return $this->invoice->total_price > 0
            && $this->invoice->type->mustBePaid();
    }
}
```

La reducción de las declaraciones `if`/`else` en nuestro código permite que el código sea más lineal, lo que 
a su vez es más fácil de razonar. Recomiendo echar un vistazo a 
[la charla de Sandi Metz](https://www.youtube.com/watch?v=29MAL8pJImQ) sobre este tema en específico.

----

El patrón Estado es, en mi opinión, impresionante. Nunca volverás a atascarte escribiendo enormes declaraciones 
`if`/`else`, en la vida real a menudo hay más de dos estados de factura, y permite un código limpio y comprobable.

Es un patrón que puedes introducir gradualmente en tus bases de código existentes, y estoy seguro de que será de 
gran ayuda para mantener el proyecto mantenible a largo plazo.
