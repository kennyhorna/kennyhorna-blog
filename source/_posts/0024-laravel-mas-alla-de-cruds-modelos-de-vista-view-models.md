---
extends: _layouts.post
section: content
title: "Más allá de CRUDs: [09] Fábricas de pruebas"
date: 2020-03-22
description: "Las fábricas de pruebas son un concepto conocido en Laravel, aunque carecen de muchas áreas: no son muy flexibles y también son una especie de caja negra para el usuario. En este capítulo vamos a ver cómo podemos administrar los datos del dominio para las pruebas."  
cover_image: /assets/images/posts/0024/0024-laravel-mas-alla-de-cruds-fabricas-de-pruebas.png
featured: false
reference: https://stitcher.io/blog/laravel-beyond-crud-09-test-factories
categories: [laravel, php, programming]
---

Las fábricas de pruebas son un concepto conocido en Laravel, aunque carecen de muchas áreas: no son muy flexibles y también son una especie de caja negra para el usuario. En este capítulo vamos a ver cómo podemos administrar los datos del dominio para las pruebas.

-------

Este es el artículo #09 de la serie [Laravel: Más allá de CRUDs](/blog/laravel-mas-alla-de-cruds). Fue originalmente publicado por [Brent](https://mobile.twitter.com/brendt_gd) en su [blog](https://stitcher.io/blog/laravel-beyond-crud-09-test-factories) (puedes encontrar ahí la serie en su idioma original).

La tabla de contenido que conforma esta serie la [tienes aquí](/blog/laravel-mas-alla-de-cruds).

Dicho esto, continuemos 😉.

-------

> Nota: este capítulo aborda principalmente código relacionado con la capa de dominio. Volveremos a la capa de aplicación en capítulos futuros.

Tomemos el ejemplo de los [estados de fábrica](https://laravel.com/docs/database-testing#creating-models) (factory
 states), un patrón poderoso, pero mal implementado en Laravel.

    $factory->state(Invoice::class, 'pending', [
        'status' => PaidInvoiceState::class,
    ]);

En primer lugar: Tu IDE no tiene idea de qué tipo de objeto es realmente `$factory`. Existe mágicamente en los archivos
 de fábrica, aunque no hay auto-completado. Una solución rápida es agregar este docblock, aunque es algo engorroso.

    /** @var \Illuminate\Database\Eloquent\Factory $factory */
    $factory->state(/* … */);

En segundo lugar, los estados se definen como cadenas, lo que los convierte en una "caja negra" cuando se usa una fábrica en las pruebas.

    public function test_case()
    {
        $invoice = factory(Invoice::class)
            ->states(/* Qué estados están realmente disponibles aquí? */)
            ->create();
    }

Tercero, no hay ningún tipo de insinuación sobre el resultado del factory, el IDE no sabe que ``$factory`` en
 realidad es una instancia de ``Invoice``; de nuevo: una **caja negra**.

Y finalmente, dado un dominio lo suficientemente grande, es posible que necesites más que unos pocos estados en tu conjunto de pruebas, que se vuelven difíciles de administrar con el tiempo.

En este capítulo veremos **una forma alternativa de implementar este patrón de fábrica**, para permitir mucha más **flexibilidad** y mejorar significativamente tu experiencia de usuario. El objetivo real de estas clases de fábrica es ayudarte a escribir pruebas de integración, sin tener que dedicar demasiado tiempo a configurar el sistema.

Ten en cuenta que digo _"pruebas de integración"_ y no _"pruebas unitarias"_: cuando estamos probando nuestro código de dominio, estamos probando la <u>lógica comercial central</u>. La mayoría de las veces, probar la lógica de negocios significa que no probaremos la parte aislada de una clase, sino más bien una regla de negocios compleja e intrincada que requiere que algunos (o muchos) datos estén presentes en la base de datos.

Como mencioné antes: estamos hablando de sistemas grandes y complejos en esta serie; Es importante tener eso en cuenta. En particular, por eso decidí llamar a estas pruebas _pruebas de integración_ en este capítulo; para evitar entrar en discusiones sobre cuáles son las pruebas unitarias y cuáles no.

### Una fábrica simple

Una fábrica de pruebas no es más que una <u>simple clase</u>. No es necesario un paquete, no hay interfaces para implementar o clases abstractas para extender. El poder de una _fábrica_ no es la complejidad del código, sino más bien uno o dos patrones correctamente aplicados.

Así es como se ve esa clase, simplificada:

    class InvoiceFactory
    {
        public static function new(): self
        {
            return new self();
        }
        
        public function create(array $extra = []): Invoice
        {
            return Invoice::create(array_merge(
                [
                    'number' => 'I-1',
                    'status' => PendingInvoiceState::class,
                    // …
                ],
                $extra
            ));   
        }
    }

Analicemos algunas decisiones de diseño.

En primer lugar, el constructor estático `new`. Puedes estar algo confundido acerca de por qué lo necesitamos, ya que podríamos simplemente hacer que el método de creación sea estático. Contestaré esa pregunta en profundidad más adelante en este capítulo, pero por ahora debes saber que queremos que esta fábrica sea altamente configurable antes de realmente crear una factura. Así que ten la seguridad de que pronto se aclarará.

En segundo lugar, ¿por qué tiene el nombre `new` el constructor estático? La respuesta es práctica: en el contexto de las fábricas, `make` y `create` a menudo se asocian con una fábrica que realmente produce un resultado. `new` nos ayuda a evitar confusiones innecesarias.

Finalmente, el método `create`: Este acepta una matriz opcional de datos adicionales para garantizar que siempre podamos hacer algunos cambios de última hora en nuestras pruebas.

Con nuestro sencillo ejemplo, ahora podemos crear facturas así:

    public function test_case()
    {
        $invoice = InvoiceFactory::new()->create();
    }

Antes de ver la configurabilidad, abordemos una pequeña mejora que podemos hacer de inmediato: los números de factura deben ser únicos, por lo que si creamos dos facturas en un caso de prueba, se romperá. Sin embargo, no queremos preocuparnos por hacer un seguimiento de los números de factura en la mayoría de los casos, así que hagamos que la fábrica se encargue de esto:

    class InvoiceFactory
    {
        private static int $number = 0;
    
        public function create(array $extra = []): Invoice
        {
            self::$number += 1;
    
            return Invoice::create(array_merge(
                [
                    'number' => 'I-' . self::$number,
                    // …
                ],
                $extra
            ));   
        }
    }

### Fábricas dentro de Fábricas

En el ejemplo original, mostré que podríamos querer crear una factura pagada. Anteriormente era un poco ingenuo cuando asumí que esto simplemente significaba cambiar el campo de estado en el modelo de factura (Invoice). ¡También necesitamos un pago real para guardar en la base de datos! Las fábricas predeterminadas de Laravel pueden manejar esto con **devoluciones de llamada** (_callbacks_), que se activan después de que se creó el modelo; aunque imagina lo que sucede si estás manejando varios, tal vez incluso decenas de estados, cada uno con sus propios efectos secundarios. Un simple hook `$factory->afterCreating` realmente no es lo suficientemente robusto como para manejar todo esto de una manera sensata.

Entonces, cambiemos las cosas. Configuremos correctamente nuestra fábrica de facturas, antes de crear la factura real.

    class InvoiceFactory
    {
        private string $status = null;
    
        public function create(array $extra = []): Invoice
        {
            $invoice = Invoice::create(array_merge(
                [
                    'status' => $this->status ?? PendingInvoiceState::class
                ],
                $extra
            ));
            
            if ($invoice->status->isPaid()) {
                PaymentFactory::new()->forInvoice($invoice)->create();
            }
            
            return $invoice;
        }
    
        public function paid(): self
        {
            $clone = clone $this;
            
            $clone->status = PaidInvoiceState::class;
            
            return $clone;
        }
    }

Por cierto, si te estás preguntando sobre ese `clone`, lo veremos más adelante.

Lo que hemos hecho configurable es el **estado** de la factura, tal como lo harían los estados de fábrica en Laravel, pero en nuestro caso existe la ventaja de que nuestro IDE realmente sabe con qué estamos tratando:

    public function test_case()
    {
        $invoice = InvoiceFactory::new()
            ->paid()
            ->create();
    }

Aún así, hay margen de mejora. ¿Has visto la verificación que hacemos después de crear la factura?

    if ($invoice->status->isPaid()) {
        PaymentFactory::new()->forInvoice($invoice)->create();
    }

Esto se puede hacer aún más flexible. Estamos usando un `PaymentFactory` por debajo, pero ¿qué pasa si queremos un control más detallado sobre cómo se realizó ese pago? Puedes imaginar que hay algunas reglas comerciales sobre las facturas pagadas que se comportan de manera diferente según el tipo de pago, por ejemplo.

Además, queremos evitar pasar demasiada configuración directamente al `InvoiceFactory`, ya que se convertirá en un desastre muy rápidamente. Entonces, ¿cómo resolvemos esto?

Aquí está la respuesta: permitimos que el desarrollador pase **opcionalmente** un `PaymentFactory` a `InvoiceFactory` para que esta fábrica se pueda configurar como quiera el desarrollador. Así es como se ve:

    public function paid(PaymentFactory $paymentFactory = null): self
    {
        $clone = clone $this;
        
        $clone->status = PaidInvoiceState::class;
        $clone->paymentFactory = $paymentFactory ?? PaymentFactory::new();
        
        return $clone;
    }

Y así es como se usa en el método `create`:

    if ($this->paymentFactory) {
        $this->paymentFactory->forInvoice($invoice)->create();
    }

Al hacerlo, surgen muchas posibilidades. En este ejemplo, estamos haciendo una factura que se paga, específicamente con un pago de Paypal.

    public function test_case()
    {
        $invoice = InvoiceFactory::new()
            ->paid(
                PaymentFactory::new()->type(PaypalPaymentType::class)
            )
            ->create();
    }

Otro ejemplo: queremos probar cómo se maneja una factura cuando se ha pagado, pero solo _después_ de la fecha de vencimiento de la factura:

    public function test_case()
    {
        $invoice = InvoiceFactory::new()
            ->expiresAt('2020-03-22')
            ->paid(
                PaymentFactory::new()->at('2020-03-30')
            )
            ->create();
    }

Con solo unas pocas líneas de código, obtenemos mucha más flexibilidad.

### Fábricas inmutables

¿Y qué hay de ese ``clone`` anterior? ¿Por qué es importante hacer que las fábricas sean **inmutables**? Mira, a veces necesitas hacer varios modelos con la misma fábrica, pero con pequeñas diferencias. En lugar de crear un nuevo objeto de fábrica para cada modelo, **puedes reutilizar el objeto de fábrica original** y solo cambiar las cosas que necesita.

Sin embargo, si no estás utilizando fábricas inmutables, existe la posibilidad de que termines con datos que realmente no deseabas. Tomemos el ejemplo de los pagos de facturas: digamos que necesitamos dos facturas en la misma fecha, una pagada y otra pendiente.

    $invoiceFactory = InvoiceFactory::new()
        ->expiresAt(Carbon::make('2020-01-01'));
    
    $invoiceA = $invoiceFactory->paid()->create();
    $invoiceB = $invoiceFactory->create();
    
¡Si nuestro método pagado no fuera inmutable, significaría que ``$invoiceB`` también sería una factura pagada! Claro, podríamos micro-gestionar cada creación de modelo, pero eso quita la flexibilidad de este patrón. Es por eso que las funciones inmutables son geniales: **puedes configurar una fábrica base y reutilizarla durante sus pruebas**, ¡sin preocuparte por los efectos secundarios no deseados!

---

### Cierre

Sobre la base de estos dos principios (**configurar fábricas dentro de fábricas** y **hacerlas inmutables**), surgen muchas posibilidades. Claro, lleva tiempo escribir estas fábricas, pero también ahorran mucho tiempo en el transcurso del desarrollo. En mi experiencia, vale la pena el esfuerzo adicional, ya que hay mucho más que ganar de ellos en comparación con su costo.

Desde que usé este patrón, nunca volví a mirar las fábricas que vienen por defecto en Laravel. Hay mucho que ganar con este enfoque.

Una desventaja que se me ocurre es que necesitará un poco más de código adicional para crear varios modelos a la vez. Sin embargo, si lo deseas, puede agregar fácilmente un pequeño fragmento de código en una clase base de fábrica como esta:

    abstract class Factory
    {
        // Las clases concretas deben añadir un tipo de retorno 
        abstract public function create(array $extra = []);
    
        public function times(int $times, array $extra = []): Collection
        {
            return collect()
                ->times($times)
                ->map(fn() => $this->create($extra));
        }
    }
    
Ten en cuenta que también puedes usar estas fábricas para otras cosas, **no solo para modelos**. Los he estado usando ampliamente para configurar DTO y, a veces, incluso clases _Request_.

Sugeriría jugar con ellos la próxima vez que necesites fábricas de prueba. ¡Te puedo asegurar que no te decepcionarán!
