---
extends: _layouts.post
section: content
title: "Más allá de CRUDs: [07] Ingresando a la capa de aplicación"
date: 2020-01-30
description: "¿Qué pertenece exactamente a la capa de aplicación? ¿Cómo agrupamos el código en ese lugar? Estas preguntas serán respondidas en este capítulo ;)"  
cover_image: /assets/images/posts/0015/laravel-mas-alla-de-cruds-ingresando-a-la-capa-de-aplicacion.png
featured: true
reference: https://stitcher.io/blog/laravel-beyond-crud-07-entering-the-application-layer
categories: [laravel, php, programming]
---

¿Qué pertenece exactamente a la capa de aplicación? ¿Cómo agrupamos el código en ese lugar? Estas preguntas serán respondidas en este capítulo ;)

-------

Este es el artículo #07 de la serie [Laravel: Más allá de CRUDs](/blog/laravel-mas-alla-de-cruds). Fue originalmente publicado por [Brent](https://mobile.twitter.com/brendt_gd) en su [blog](https://stitcher.io/blog/laravel-beyond-crud-07-entering-the-application-layer) (puedes encontrar ahí la serie en su idioma original).

La tabla de contenido que conforma esta serie la [tienes aquí](/blog/laravel-mas-alla-de-cruds).

Dicho esto, continuemos 😉.

-------

En el [1er capítulo](/blog/laravel-mas-alla-de-cruds-laravel-orientado-al-dominio), dijimos que una de las características de los proyectos de Laravel orientados al dominio es la siguiente:

> […] Lo más importante es que comiences a pensar en grupos de conceptos de negocio relacionados, en lugar de en grupos de código con las mismas propiedades técnicas.

En otras palabras: agrupa tu código en función de lo que parece en el mundo real, en lugar de su propósito en la base del código.

También escribimos que el _dominio_ y el _código de la aplicación_ son dos cosas que van separadas. Además, las aplicaciones pueden usar varios grupos del dominio a la vez si es necesario, para exponer la funcionalidad del dominio al usuario final.

Sin embargo, ¿qué pertenece exactamente a esta capa de aplicación? ¿Cómo agrupamos el código en ese lugar? Estas preguntas serán respondidas en este capítulo.

Estamos entrando en la capa de aplicación.

### Varias aplicaciones

Lo primero que hay que entender es que un proyecto puede tener varias aplicaciones. De hecho, cada proyecto de Laravel ya tiene dos por defecto: las aplicaciones HTTP y las de consola. Aún así, hay varias partes más en tu proyecto que pueden considerarse como una aplicación independiente: cada integración de terceros, una API REST, un front-end para el cliente, una aplicación administrativa y todo lo demás.

Todas estas pueden considerarse como aplicaciones separadas, exponiendo y presentando el dominio para sus propios casos de uso únicos. De hecho, tiendo a pensar en la consola Artisan como otra más en esta lista: es una aplicación utilizada por los desarrolladores para trabajar y manipular el proyecto.

Sin embargo, dado que estamos en desarrollo web, nuestro enfoque principal probablemente estará dentro de las aplicaciones relacionadas con HTTP. Entonces, ¿qué incluyen estos? Echemos un vistazo:

- Controladores
- Peticiones
- Reglas de validación específicas de la aplicación
- Middleware
- Recursos
- ViewModels
- QueryBuilders: los que analizan consultas de URL

Incluso diría que las vistas blade, los archivos JavaScript y CSS pertenecen a una aplicación y no deben dejarse de lado en una carpeta de recursos. Me doy cuenta de que este es un paso demasiado lejos para muchas personas, pero quería mencionarlo, al menos.

Recuerda, el objetivo de una aplicación es obtener la entrada del usuario, pasarla al dominio y representar la salida de manera utilizable para el usuario. Después de varios capítulos en el código de dominio, no debería sorprendernos que la mayor parte del código de la aplicación sea meramente estructural, a menudo aburrido; pasar datos de un punto a otro.

Sin embargo, hay mucho que contar sobre varios de los conceptos mencionados anteriormente: _ViewModels_, integraciones de terceros, ¿qué pasa con los _jobs_?; Abordaremos estos temas en capítulos futuros, pero por ahora queremos centrarnos en las ideas principales detrás de la capa de aplicación y una descripción general de estas.

### Estructurando aplicaciones HTTP

Hay un punto muy importante que debemos discutir antes de continuar: ¿Cómo se estructurará generalmente una aplicación HTTP? ¿Deberíamos seguir las convenciones de Laravel, o debemos pensarlo un poco más?

Como estoy dedicando una sección de un capítulo a esta pregunta, probablemente puedas adivinar la respuesta. Así que echemos un vistazo a lo que Laravel recomendaría que hagas por defecto.

<div class="files">
    <div class="folder folder--open">App/Admin
        <div class="folder folder--open">Http
            <div class="folder">Controllers</div>
            <div class="file">Kernel.php</div>
            <div class="folder">Middleware</div>
        </div>
        <div class="folder">Requests</div>
        <div class="folder">Resources</div>
        <div class="folder">Views</div>
        <div class="folder">ViewModels</div>
    </div>
</div>

Esta estructura está bien en proyectos pequeños, pero sinceramente, no escala bien. Para aclarar lo que quiero decir con esto, te mostraré la estructura de documentos de una aplicación de administración en uno de nuestros proyectos para clientes. Obviamente no puedo revelar demasiada información sobre este proyecto, así que borré la mayoría de los nombres de clase. Sin embargo, como hemos estado utilizando la facturación como ejemplo a lo largo de esta serie, dejé el nombre de algunas clases relacionadas con la factura dentro de la aplicación de administración. Dale un vistazo.

Ah y por cierto, ¡disfruta desplazándote hacia abajo!

<div class="files">
    <div class="folder folder--open">App/Admin
        <div class="folder folder--open">Controllers
            <div class="folder folder--open">█████████
                <div class="file">███████████████████.php</div>
                <div class="file">████████████████████████████████.php</div>
                <div class="file">███████████████████████.php</div>
                <div class="file">██████████████████████████████████.php</div>
                <div class="file">███████████████████████.php</div>
                <div class="file">█████████████████████████.php</div>
                <div class="file">███████████████████████.php</div>
                <div class="file">███████████████████████████████.php</div>
                <div class="file">██████████████████.php</div>
                <div class="file">████████████████.php</div>
            </div>
            <div class="folder folder--open">████████████
                <div class="file">████████████████████████████████.php</div>
                <div class="folder folder--open">████████████████
                    <div class="file">████████████████████████████████.php</div>
                    <div class="file">█████████████████████████████████████████.php</div>
                    <div class="file">██████████████████████████████.php</div>
                    <div class="file">████████████████████████████████.php</div>
                    <div class="file">███████████████████████████████.php</div>
                <div class="file">████████████████████████████████.php</div>
                </div>
                <div class="file">█████████████████████████████████.php</div>
                <div class="file">██████████████████████████████████.php</div>
                <div class="file">█████████████████████████████████.php</div>
                <div class="file">██████████████████████████████.php</div>
                <div class="file">█████████████████████████████.php</div>
                <div class="file">█████████████████████████████████████████.php</div>
                <div class="file">█████████████████████████████████████.php</div>
                <div class="file">██████████████████████████████████.php</div>
                <div class="file">█████████████████████████████████████████.php</div>
                <div class="file">████████████████████████████████████.php</div>
                <div class="folder folder--open">█████████████
                    <div class="file">█████████████████████████████.php</div>
                    <div class="file">███████████████████████████.php</div>
                    <div class="file">█████████████████████████████.php</div>
                    <div class="file">███████████████████████████████████████████.php</div>
                    <div class="file">███████████████████████████████████████.php</div>
                    <div class="file">████████████████████████████.php</div>
                <div class="file">█████████████████████████████.php</div>
                </div>
                <div class="file">██████████████████████████████.php</div>
                <div class="file">█████████████████████████.php</div>
            </div>
            <div class="folder folder--open">███
                <div class="file">████████████████████.php</div>
                <div class="file">██████████████████████.php</div>
                <div class="file">█████████████████████.php</div>
                <div class="file">█████████████.php</div>
                <div class="file">█████████████████████.php</div>
                <div class="file">██████████████████.php</div>
                <div class="file">██████████████████.php</div>
                <div class="file">██████████████████████.php</div>
                <div class="file">██████████████████████.php</div>
                <div class="file">███████████████████.php</div>
                <div class="file">████████████████████.php</div>
                <div class="file">██████████████████.php</div>
                <div class="file">██████████████████████████.php</div>
                <div class="file">████████████████████.php</div>
                <div class="file">██████████████████.php</div>
                <div class="file">████████████████.php</div>
            </div>
            <div class="file">███████████████████.php</div>
            <div class="file">█████████████████████.php</div>
            <div class="folder folder--open">████████████
                <div class="file">███████████████████████.php</div>
                <div class="file">██████████████████.php</div>
                <div class="file">████████████████████.php</div>
                <div class="file">████████████████████.php</div>
                <div class="folder folder--open">████████████████████████
                    <div class="file">█████████████████████████████████████████.php</div>
                    <div class="file">██████████████████████████████████████.php</div>
                    <div class="file">█████████████████████████████████.php</div>
                    <div class="file">██████████████████████████████.php</div>
                    <div class="file">███████████████████████████████.php</div>
                    <div class="file">███████████████████████████████████████.php</div>
                    <div class="file">███████████████████████████████.php</div>
                    <div class="file">████████████████████████████████████████.php</div>
                <div class="file">█████████████████████████████████████.php</div>
                </div>
                <div class="folder folder--open">Invoices
                    <div class="file">████████████████████████████████████.php</div>
                    <div class="file">█████████████████████.php</div>
                    <div class="file">IgnoreMissedInvoicesController.php</div>
                    <div class="file">████████████████.php</div>
                    <div class="file">██████████████████████.php</div>
                    <div class="file">████████████████████.php</div>
                    <div class="file">InvoiceStatusController.php</div>
                    <div class="file">InvoicesController.php</div>
                    <div class="file">MissedInvoicesController.php</div>
                    <div class="file">█████████████.php</div>
                    <div class="file">RefreshMissedInvoicesController</div>
                <div class="file">█████████████.php</div>
                </div>
                <div class="folder folder--open">████████
                <div class="file">█████████████████████.php</div>
                </div>
                <div class="file">██████████████████.php</div>
                <div class="file">██████████████████.php</div>
            </div>
            <div class="folder folder--open">███████████████████
                <div class="file">████████████████████████.php</div>
                <div class="file">████████████████████████████.php</div>
                <div class="file">███████████████████.php</div>
                <div class="file">████████████████████.php</div>
                <div class="file">████████████████████.php</div>
                <div class="file">██████████████████████████.php</div>
                <div class="file">███████████████████████████.php</div>
                <div class="file">██████████████████████████████████.php</div>
                <div class="file">███████████████████████████████████.php</div>
                <div class="file">██████████████████████████.php</div>
                <div class="file">███████████████████████████████.php</div>
                <div class="file">████████████████████████████████.php</div>
                <div class="file">████████████████████████.php</div>
                <div class="file">████████████████████████.php</div>
                <div class="file">█████████████████████.php</div>
                <div class="file">██████████████████████████.php</div>
                <div class="file">██████████████████████████████.php</div>
                <div class="file">██████████████████████████.php</div>
                <div class="file">███████████████████████.php</div>
                <div class="file">██████████████████████.php</div>
                <div class="file">████████████████████████████.php</div>
                <div class="file">███████████████████████.php</div>
                <div class="file">█████████████████████████████.php</div>
                <div class="file">██████████████████████.php</div>
                <div class="file">███████████████████████████████.php</div>
                <div class="file">███████████████████████.php</div>
                <div class="file">███████████████████████.php</div>
                <div class="file">███████████████████████████████.php</div>
                <div class="file">████████████████████████.php</div>
                <div class="file">██████████████████████████████.php</div>
                <div class="file">███████████████████████████████.php</div>
                <div class="file">█████████████████████████.php</div>
                <div class="file">██████████████████████.php</div>
                <div class="file">███████████████████████████.php</div>
                <div class="file">█████████████████████████████████.php</div>
                <div class="file">███████████████████████████.php</div>
                <div class="file">████████████████████████████.php</div>
                <div class="file">████████████████████.php</div>
                <div class="file">███████████████.php</div>
            </div>
            <div class="file">███████████████.php</div>
            <div class="file">███████████████.php</div>
            <div class="folder folder--open">█████████████
                <div class="file">█████████████████████.php</div>
                <div class="file">█████████████████████████████.php</div>
                <div class="file">████████████████████████████.php</div>
                <div class="file">███████████████████████████.php</div>
                <div class="file">██████████████████████████.php</div>
                <div class="file">██████████████████████████.php</div>
                <div class="file">█████████████████████████.php</div>
            </div>
            <div class="file">██████████████████.php</div>
            <div class="file">█████████████████.php</div>
            <div class="file">█████████████████████████.php</div>
            <div class="file">██████████████████████.php</div>
            <div class="folder folder--open">████████
                <div class="file">███████████████████.php</div>
                <div class="file">███████████████████████████.php</div>
                <div class="file">█████████████████████.php</div>
                <div class="file">█████████████████.php</div>
            </div>
            <div class="folder folder--open">███████████████
                <div class="file">█████████████████.php</div>
                <div class="file">███████████████.php</div>
                <div class="file">██████████████.php</div>
                <div class="file">████████████████████████.php</div>
                <div class="file">██████████████████████████.php</div>
                <div class="file">██████████████████████████.php</div>
                <div class="file">███████████████████.php</div>
            </div>
            <div class="file">████████████████████.php</div>
            <div class="folder folder--open">██████████
                <div class="file">███████████████████████████.php</div>
                <div class="file">██████████████████.php</div>
                <div class="file">███████████████████.php</div>
                <div class="file">███████████████.php</div>
                <div class="file">████████████████████████.php</div>
                <div class="file">█████████████████████████████.php</div>
                <div class="file">████████████████████████.php</div>
                <div class="file">█████████████████████.php</div>
                <div class="file">████████████████████.php</div>
                <div class="file">████████████████████████.php</div>
                <div class="file">████████████████████████████.php</div>
                <div class="file">███████████████████████.php</div>
                <div class="file">███████████████████.php</div>
                <div class="file">███████████████████████.php</div>
                <div class="file">████████████████.php</div>
                <div class="file">██████████████████.php</div>
                <div class="file">█████████████████.php</div>
                <div class="file">██████████████████.php</div>
                <div class="file">█████████████████████████████.php</div>
                <div class="file">██████████████████████.php</div>
                <div class="file">████████████████████.php</div>
                <div class="file">████████████████████████.php</div>
                <div class="file">███████████████████.php</div>
                <div class="file">███████████████.php</div>
                <div class="file">██████████████████.php</div>
            </div>
            <div class="folder folder--open">███████
                <div class="file">████████████████.php</div>
            </div>
            <div class="file">███████████████.php</div>
        </div>
        <div class="folder folder--open">Filters
            <div class="file">████████████████████.php</div>
            <div class="file">███████████████████████████.php</div>
            <div class="file">█████████████████████████.php</div>
            <div class="file">██████████████████████████████████.php</div>
            <div class="file">██████████████████████.php</div>
            <div class="file">█████████████████████████████.php</div>
            <div class="file">██████████████████████████.php</div>
            <div class="file">████████████████.php</div>
            <div class="file">███████████.php</div>
            <div class="file">███████████.php</div>
            <div class="file">████████████████.php</div>
            <div class="file">InvoiceMonthFilter.php</div>
            <div class="file">InvoiceOfferFilter.php</div>
            <div class="file">InvoiceStatusFilter.php</div>
            <div class="file">InvoiceYearFilter.php</div>
            <div class="file">█████████████████████.php</div>
            <div class="file">███████████.php</div>
            <div class="file">███████████████████.php</div>
        </div>
        <div class="folder folder--open">Middleware
            <div class="file">██████████████████████████.php</div>
            <div class="file">█████████████████████.php</div>
            <div class="file">█████████████████████████████████.php</div>
            <div class="file">█████████████████████████████████████.php</div>
            <div class="file">EnsureValidHabitantInvoiceCollectionSettingsMiddleware.php</div>
            <div class="file">EnsureValidInvoiceDraftSettingsMiddleware.php</div>
            <div class="file">██████████████████████████████████.php</div>
            <div class="file">EnsureValidOwnerInvoiceCollectionSettingsMiddleware.php</div>
            <div class="file">██████████████████████.php</div>
            <div class="file">█████████████████████.php</div>
            <div class="file">█████████████████████████████.php</div>
            <div class="file">█████████████████████████████████.php</div>
            <div class="file">████████████████████.php</div>
            <div class="file">███████████████████.php</div>
            <div class="file">█████████████████.php</div>
        </div>
        <div class="folder folder--open">Queries
            <div class="file">██████████████████.php</div>
            <div class="file">███████████████████████████.php</div>
            <div class="file">██████████████████.php</div>
            <div class="file">████████████████████████.php</div>
            <div class="file">██████████████████████.php</div>
            <div class="file">██████████████████.php</div>
            <div class="file">██████████████████████.php</div>
            <div class="file">███████████████████.php</div>
            <div class="file">████████████████████████.php</div>
            <div class="file">█████████████████████████████.php</div>
            <div class="file">████████████████████████.php</div>
            <div class="file">████████████████████.php</div>
            <div class="file">█████████████████████.php</div>
            <div class="file">███████████████████.php</div>
            <div class="file">████████████████████.php</div>
            <div class="file">█████████████████████████.php</div>
            <div class="file">██████████████████████.php</div>
            <div class="file">███████████████████████.php</div>
            <div class="file">██████████████████.php</div>
            <div class="file">██████████████████████████████████.php</div>
            <div class="file">███████████████████████████.php</div>
            <div class="file">█████████████████████.php</div>
            <div class="file">InvoiceCollectionIndexQuery.php</div>
            <div class="file">InvoiceIndexQuery.php</div>
            <div class="file">█████████████████████████████.php</div>
            <div class="file">███████████████████████.php</div>
            <div class="file">███████████████.php</div>
            <div class="file">████████████████████████████.php</div>
            <div class="file">████████████████████████.php</div>
            <div class="file">██████████████████.php</div>
            <div class="file">█████████████████████.php</div>
            <div class="file">█████████████████████████████.php</div>
            <div class="file">████████████████████.php</div>
            <div class="file">████████████████.php</div>
            <div class="file">██████████████████.php</div>
            <div class="file">█████████████████████████.php</div>
            <div class="file">████████████████████████.php</div>
            <div class="file">█████████████████████.php</div>
            <div class="file">██████████████████.php</div>
            <div class="file">███████████████████.php</div>
            <div class="file">███████████████.php</div>
            <div class="file">███████████████.php</div>
        </div>
        <div class="folder folder--open">Requests
            <div class="file">█████████████████████████.php</div>
            <div class="file">█████████████████████.php</div>
            <div class="file">██████████████.php</div>
            <div class="file">██████████████.php</div>
            <div class="file">██████████████.php</div>
            <div class="file">████████████████.php</div>
            <div class="file">██████████████████████████████.php</div>
            <div class="file">███████████████████████.php</div>
            <div class="file">███████████████████████████████.php</div>
            <div class="file">█████████████████████████████.php</div>
            <div class="file">InvoiceRequest.php</div>
            <div class="file">██████████████████████.php</div>
            <div class="file">███████████████████.php</div>
            <div class="file">█████████████████████.php</div>
            <div class="file">████████████.php</div>
            <div class="file">████████████████████.php</div>
            <div class="file">████████████████████████████████████.php</div>
            <div class="file">██████████████████████████████████.php</div>
            <div class="file">██████████████████.php</div>
            <div class="file">███████.php</div>
            <div class="file">██████████████████████.php</div>
            <div class="file">████████████.php</div>
            <div class="file">███████████.php</div>
            <div class="file">████████████████████████.php</div>
        </div>
        <div class="folder folder--open">Resources
            <div class="file">████████████████.php</div>
            <div class="file">███████████████.php</div>
            <div class="file">██████████████.php</div>
            <div class="file">███████████████████████.php</div>
            <div class="file">█████████████████████████████.php</div>
            <div class="file">███████████████████████████.php</div>
            <div class="file">███████████████████.php</div>
            <div class="file">███████████████.php</div>
            <div class="file">████████████████████████████.php</div>
            <div class="file">██████████████████████.php</div>
            <div class="file">████████████████████████████████████.php</div>
            <div class="file">████████████████████.php</div>
            <div class="file">█████████████████████████████████.php</div>
            <div class="file">███████████████.php</div>
            <div class="file">█████████████████████████████.php</div>
            <div class="file">████████████████████.php</div>
            <div class="file">████████████████████.php</div>
            <div class="file">█████████████████████████████████████.php</div>
            <div class="file">████████████████████████.php</div>
            <div class="file">████████████████.php</div>
            <div class="file">████████████████████.php</div>
            <div class="file">█████████████████████████████████████.php</div>
            <div class="file">███████████████████████████████.php</div>
            <div class="file">███████████████████████████.php</div>
            <div class="file">████████████████████.php</div>
            <div class="file">█████████████████████.php</div>
            <div class="file">█████████████████████████.php</div>
            <div class="file">█████████████████████.php</div>
            <div class="file">█████████████████.php</div>
            <div class="file">█████████████████████.php</div>
            <div class="file">██████████████████.php</div>
            <div class="file">█████████████████████████.php</div>
            <div class="file">█████████████████.php</div>
            <div class="file">████████████████.php</div>
            <div class="file">████████████████████.php</div>
            <div class="file">███████████████████████████████.php</div>
            <div class="file">████████████████████████████████.php</div>
            <div class="file">████████████████████████████████.php</div>
            <div class="file">█████████████████████████████.php</div>
            <div class="file">███████████████████████████████.php</div>
            <div class="file">████████████████████████.php</div>
            <div class="file">████████████████████████████.php</div>
            <div class="file">████████████████.php</div>
            <div class="file">█████████████████████.php</div>
            <div class="file">███████████████████████.php</div>
            <div class="file">█████████████████.php</div>
            <div class="folder folder--open">Invoices
                <div class="file">InvoiceCollectionDataResource.php</div>
                <div class="file">InvoiceCollectionResource.php</div>
                <div class="file">InvoiceDataResource.php</div>
                <div class="file">InvoiceDraftResource.php</div>
                <div class="file">InvoiceLineDataResource.php</div>
                <div class="file">InvoiceLineResource.php</div>
                <div class="file">InvoiceResource.php</div>
                <div class="file">██████████████████.php</div>
                <div class="file">█████████████████.php</div>
                <div class="file">█████████████.php</div>
            </div>
            <div class="file">InvoiceIndexResource.php</div>
            <div class="file">InvoiceLabelResource.php</div>
            <div class="file">InvoiceMainOverviewResource.php</div>
            <div class="file">InvoiceResource.php</div>
            <div class="file">████████████████████.php</div>
            <div class="file">█████████████.php</div>
            <div class="file">███████████████.php</div>
            <div class="file">██████████████████████████.php</div>
            <div class="file">████████████████████.php</div>
            <div class="file">██████████████████.php</div>
            <div class="file">██████████████.php</div>
            <div class="file">█████████████████████████████.php</div>
            <div class="file">██████████████████████████.php</div>
            <div class="file">█████████████████████.php</div>
            <div class="file">█████████████████████████.php</div>
            <div class="file">█████████████.php</div>
            <div class="file">██████████████████████.php</div>
            <div class="file">███████████████████.php</div>
            <div class="file">███████████████.php</div>
            <div class="file">███████████████.php</div>
            <div class="file">███████████████.php</div>
            <div class="file">█████████████████████.php</div>
            <div class="file">█████████████.php</div>
            <div class="file">█████████████████.php</div>
            <div class="file">███████████████████.php</div>
            <div class="file">███████████████████████.php</div>
            <div class="file">██████████████.php</div>
            <div class="file">██████████████████████████.php</div>
            <div class="file">█████████████████.php</div>
            <div class="file">██████████████████████.php</div>
            <div class="file">█████████████.php</div>
            <div class="file">█████████████████.php</div>
            <div class="file">████████████.php</div>
            <div class="file">███████████████████████.php</div>
            <div class="file">████████████████.php</div>
            <div class="file">████████████████████.php</div>
            <div class="file">████████████████████████████.php</div>
            <div class="file">█████████████████████.php</div>
            <div class="file">██████████████████████████.php</div>
            <div class="file">█████████████████.php</div>
            <div class="file">█████████████████████.php</div>
            <div class="file">███████████████████.php</div>
            <div class="file">████████████.php</div>
            <div class="file">████████████████.php</div>
            <div class="file">████████████.php</div>
            <div class="file">█████████████████████.php</div>
        </div>
        <div class="folder folder--open">ViewModels
            <div class="file">█████████████████.php</div>
            <div class="file">███████████████.php</div>
            <div class="file">████████████████████████.php</div>
            <div class="file">█████████████████.php</div>
            <div class="file">████████████████████.php</div>
            <div class="file">█████████████████████████████████.php</div>
            <div class="file">████████████████████████████.php</div>
            <div class="file">██████████████████████████.php</div>
            <div class="file">██████████████████████████████.php</div>
            <div class="file">████████████████████████.php</div>
            <div class="file">█████████████████.php</div>
            <div class="file">██████████████████████████████.php</div>
            <div class="file">█████████████████████████.php</div>
            <div class="file">█████████████████████.php</div>
            <div class="file">█████████████.php</div>
            <div class="file">████████████████.php</div>
            <div class="file">██████████████████.php</div>
            <div class="file">█████████████████████.php</div>
            <div class="file">██████████████████████.php</div>
            <div class="file">██████████████████████████.php</div>
            <div class="file">██████████████████████.php</div>
            <div class="file">██████████████████.php</div>
            <div class="file">████████████████████.php</div>
            <div class="file">███████████████████.php</div>
            <div class="file">██████████████████.php</div>
            <div class="file">█████████████████████████████.php</div>
            <div class="file">██████████████████████████.php</div>
            <div class="file">█████████████████████.php</div>
            <div class="file">█████████████████.php</div>
            <div class="file">██████████████████████████.php</div>
            <div class="file">███████████████.php</div>
            <div class="file">███████████████████████████.php</div>
            <div class="folder folder--open">████████████████████████
                <div class="file">████████████████.php</div>
                <div class="file">█████████████████.php</div>
                <div class="file">██████████████████.php</div>
                <div class="file">███████████████████████.php</div>
            </div>
            <div class="file">█████████████████████████.php</div>
            <div class="file">███████████████████████████████.php</div>
            <div class="file">███████████████████████████████.php</div>
            <div class="file">███████████████████████.php</div>
            <div class="file">██████████████████.php</div>
            <div class="file">InvoiceCollectionHabitantContractPreviewViewModel.php</div>
            <div class="file">InvoiceCollectionOwnerContractPreviewViewModel.php</div>
            <div class="file">InvoiceCollectionPreviewViewModel.php</div>
            <div class="file">InvoiceDraftViewModel.php</div>
            <div class="file">InvoiceIndexViewModel.php</div>
            <div class="file">InvoiceLabelsViewModel.php</div>
            <div class="file">InvoiceStatusViewModel.php</div>
            <div class="file">█████████████████.php</div>
            <div class="file">█████████████████████.php</div>
            <div class="file">██████████████████████.php</div>
            <div class="file">█████████████.php</div>
            <div class="file">██████████████████.php</div>
            <div class="file">███████████████████.php</div>
            <div class="file">██████████████.php</div>
            <div class="file">██████████████████████.php</div>
            <div class="file">████████████████████████████.php</div>
            <div class="file">██████████████████████████████████████.php</div>
            <div class="file">████████████████.php</div>
            <div class="file">█████████████████.php</div>
            <div class="file">████████████████████████.php</div>
            <div class="file">████████████████████████.php</div>
            <div class="file">█████████████████████.php</div>
            <div class="file">██████████████████.php</div>
            <div class="file">████████████████████.php</div>
            <div class="file">██████████████████████████████.php</div>
            <div class="file">█████████████████████████.php</div>
            <div class="file">███████████████████████████████.php</div>
            <div class="file">██████████████████.php</div>
            <div class="file">███████████████.php</div>
            <div class="file">██████████████.php</div>
            <div class="file">████████████████████.php</div>
            <div class="file">████████████████████████.php</div>
            <div class="file">█████████████████.php</div>
            <div class="file">█████████████████████████.php</div>
            <div class="file">██████████████████.php</div>
            <div class="file">████████████████████████████.php</div>
            <div class="file">█████████████████████████████.php</div>
            <div class="file">█████████████████████.php</div>
            <div class="file">██████████████████████.php</div>
            <div class="file">██████████████████.php</div>
            <div class="file">██████████████████████.php</div>
            <div class="file">█████████████████████████.php</div>
            <div class="file">██████████████████████.php</div>
            <div class="file">█████████████████.php</div>
            <div class="file">█████████████.php</div>
            <div class="file">█████████████.php</div>
            <div class="file">██████████████████████.php</div>
            <div class="file">█████████.php</div>
            <div class="file">█████████████████.php</div>
        </div>
    </div>
</div>

¡Hola de nuevo!

Eso fue mucho por desplazar. Sin embargo, no estoy bromeando: así es como se veía uno de nuestros proyectos después de un año y medio de desarrollo. Y ten en cuenta que este es solo el código de la aplicación de administración, no incluye nada relacionado con el dominio.

Entonces, ¿cuál es el problema central aquí? En realidad, es lo mismo que con nuestro código de dominio en el capítulo 1: estamos agrupando nuestro código en función de las propiedades técnicas, en lugar de su significado en el mundo real; controladores con controladores, solicitudes con solicitudes, ver modelos con ver modelos, etc.

Una vez más, un concepto como las facturas se extiende a través de múltiples directorios y se mezcla con docenas de otras clases. Incluso con el mejor soporte IDE, es muy difícil comprender la aplicación en su conjunto y no hay forma de obtener una visión general de lo que está sucediendo.

¿La solución? No hay sorpresas aquí, espero; es lo mismo que hicimos con los dominios: agrupa el código que se relaciona. En este ejemplo, las facturas:

<div class="files">
    <div class="folder folder--open">Admin
        <div class="folder  folder--open">Invoices
            <div class="folder folder--open">Controllers
                <div class="file">IgnoreMissedInvoicesController.php</div>
                <div class="file">InvoiceStatusController.php</div>
                <div class="file">InvoicesController.php</div>
                <div class="file">MissedInvoicesController.php</div>
                <div class="file">RefreshMissedInvoicesController.php</div>
            </div>
            <div class="folder folder--open">Filters
                <div class="file">InvoiceMonthFilter.php</div>
                <div class="file">InvoiceOfferFilter.php</div>
                <div class="file">InvoiceStatusFilter.php</div>
                <div class="file">InvoiceYearFilter.php</div>
            </div>
            <div class="folder folder--open">Middleware
                <div class="file">EnsureValidHabitantInvoiceCollectionSettingsMiddleware.php</div>
                <div class="file">EnsureValidInvoiceDraftSettingsMiddleware.php</div>
                <div class="file">EnsureValidOwnerInvoiceCollectionSettingsMiddleware.php</div>
            </div>
            <div class="folder folder--open">Queries
                <div class="file">InvoiceCollectionIndexQuery.php</div>
                <div class="file">InvoiceIndexQuery.php</div>
            </div>
            <div class="folder folder--open">Requests
                <div class="file">InvoiceRequest.php</div>
            </div>
            <div class="folder folder--open">Resources
                <div class="file">InvoiceCollectionDataResource.php</div>
                <div class="file">InvoiceCollectionResource.php</div>
                <div class="file">InvoiceDataResource.php</div>
                <div class="file">InvoiceDraftResource.php</div>
                <div class="file">InvoiceIndexResource.php</div>
                <div class="file">InvoiceLabelResource.php</div>
                <div class="file">InvoiceLineDataResource.php</div>
                <div class="file">InvoiceLineResource.php</div>
                <div class="file">InvoiceMainOverviewResource.php</div>
                <div class="file">InvoiceResource.php</div>
                <div class="file">InvoiceResource.php</div>
            </div>
            <div class="folder folder--open">ViewModels
                <div class="file">InvoiceCollectionHabitantContractPreviewViewModel.php</div>
                <div class="file">InvoiceCollectionOwnerContractPreviewViewModel.php</div>
                <div class="file">InvoiceCollectionPreviewViewModel.php</div>
                <div class="file">InvoiceDraftViewModel.php</div>
                <div class="file">InvoiceIndexViewModel.php</div>
                <div class="file">InvoiceLabelsViewModel.php</div>
                <div class="file">InvoiceStatusViewModel.php</div>
            </div>
        </div>
    </div>
</div>

¿Qué tal eso? Cuando trabajes con facturas, tiene un lugar al que ir para saber qué código está disponible para ti. Tiendo a llamar a estos grupos "módulos de aplicación" o "módulos" para abreviar; y puedo decirte por experiencia que hacen la vida mucho más fácil cuando trabajas en proyectos de esta escala.

¿Esto significa que los módulos deben asignarse uno a uno en el dominio? ¡Definitivamente no! Eso sí, podría haber cierta superposición, pero no es obligatorio. Por ejemplo: tenemos un módulo de configuración dentro de la aplicación de administración, que toca varios grupos de dominio a la vez. No tendría sentido tener controladores de configuración separados, ver modelos, etc., distribuidos en múltiples módulos: cuando estamos trabajando en la configuración, es una característica en sí misma; no uno que se extendió por varios módulos solo para estar sincronizado con el dominio.

Otra pregunta que podría surgir al observar esta estructura es.. ¿qué hacer con las clases de propósito general?. Cosas como una clase de solicitud base, middleware que se usa en todas partes, etc... ¿Recuerdas el namespace _Support_ en el capítulo uno? ¡Precisamente existe para esos casos! El soporte contiene todo el código que debería ser accesible a nivel global, pero bien podría haber sido parte del framework.

-----

Ahora que tienes una visión general de cómo podemos estructurar las aplicaciones, es hora de ver algunos de los patrones que usamos ahí para facilitarnos la vida. Comenzaremos con eso la próxima vez, cuando hablemos de **modelos de vista** (_view models_).
