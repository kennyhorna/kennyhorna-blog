---
extends: _layouts.post
section: content
title: Creando sitios estáticos con Jigsaw
date: 2019-10-13
description: Hace algún tiempo tenía ganas de probar Jigsaw, un proyecto de Tighten, así que describiré como cree este mismo blog de la mano de esta herramienta.
cover_image: /assets/images/posts/0001/cover-jigsaw.jpg
featured: false
categories: [jigsaw, blade, despliegue, tutoriales, php]
---

Hace algún tiempo tenía ganas de probar Jigsaw, así que describiré como creé este mismo blog de la mano de 
esta herramienta.

### ¿Qué es un Sitio Estático?

Los sitios web estáticos son aquellos sitios enfocados principalmente a mostrar una información permanente, 
donde el usuario se limita a obtener la información, sin poder realizar mayor interacción con la página web
visitada. Entre estos sitios podemos listar por ejemplo al website de una empresa, a un simple blog o a la 
web de la documentación de un proyecto. 

### ¿Qué es Jigsaw?
Jigsaw es un proyecto _open-source_ desarrollado por el genial equipo de [Tighten](http://tighten.co). 
Tal como mencionan en la [web del proyecto](https://jigsaw.tighten.co), Jigsaw es un framework para la 
rápida creación de sitios estáticos utilizando las mismas herramientas que hacen posibles nuestras 
aplicaciones web.

### ¿Cómo funciona?

Jigsaw hace uso de [Laravel Blade](https://laravel.com/docs/blade) para generar las vistas, por lo que podemos
usar todo el poder de este componente tal como lo haríamos en Laravel.

Por otro lado, viene pre-configurado con [TailwindCSS](https://tailwindcss.com), un frameworks de CSS que
facilita bastante la aplicación de estilos (ya ahondaré en Tailwind en un artículo futuro). Tailwind
nos ayudará a aplicar estilos de manera rápida y consistente para personalizar nuestro sitio a placer.

Sin más que añadir, manos a la obra.

### Instalación

> Para el propósito de esta guía asumiré que ya tienes instalado Composer y -a la fecha de publicación de
> este artículo- PHP 7.1 (o superior).


Para instalar Jigsaw, haremos uso de la consola para crearemos un directorio nuevo en donde instalaremos
el framework:

```bash
mkdir mi-sitio
cd mi-sitio
composer require tightenco/jigsaw
```

Esperamos unos segundos -o minutos, dependiendo de la calidad de tu conexión- y ya tendremos el código base 
del proyecto. 

A continuación procederemos a inicializar Jigsaw. 

Aquí podemos optar por una versión limpia 
o utilizar algunas de las plantillas iniciales que tienen a disposición como "Blog", un sitio básico para un
blog o "Docs", que te da la configuración inicial de un sitio de documentación. En mi caso escogeré "Blog",
pues de paso nos sirve también para familiarizarnos con el framework.

```bash
./vendor/bin/jigsaw init blog
```
 
Luego de la instalación, tu directorio `mi-sitio` tendrá la siguiente estructura:

<div class="files">
    <div class="folder folder--open">source
        <div class="folder folder--open">_assets
            <div class="folder folder--open">js
                <div class="folder">Components</div>
                <div class="file">main.js</div>
            </div>
            <div class="folder folder--open">sass
                <div class="file">main.scss</div>
                <div class="file">...</div>
            </div>
        </div>
        <div class="folder">_categories</div>
        <div class="folder">_components</div>
        <div class="folder folder--open">_layouts
            <div class="file">master.blade.php</div>
            <div class="file"> ... </div>
        </div>
        <div class="folder">_nav</div>
        <div class="folder">_posts</div>
        <div class="folder folder--open">assets
            <div class="folder folder--open">build
                <div class="folder folder--open">js
                    <div class="file">main.js</div>
                </div>
                <div class="folder folder--open">sass
                    <div class="file">main.css</div>
                </div>
                <div class="file">mix-manifest.json</div>
            </div>
            <div class="folder folder--open">images
                <div class="file">jigsaw.png</div>
            </div>
            <div class="folder">img</div>
        </div>
        <div class="file">index.blade.php</div>
        <div class="file"> ... </div>
    </div>
    <div class="folder">tasks</div>
    <div class="folder">vendor</div>
    <div class="file">bootstrap.php</div>
    <div class="file">composer.json</div>
    <div class="file">composer.lock</div>
    <div class="file">config.php</div>
    <div class="file">package.json</div>
    <div class="file">webpack.mix.js</div>
</div>

Como puedes notar, todo lo relacionado al blog en sí lo puedes encontrar en el directorio `/source`,
Es ahí donde crearemos nuestros artículos y colecciones rápidamente utilizando 
[markdown](https://es.wikipedia.org/wiki/Markdown).

### Configuración básica

Para configurar los parámetros básicos del proyecto, vamos a editar el `config.php` para indicarle
los parámetros básicos del sitio:

```php
# config.php

return [
    'baseUrl' => 'http://kennyhorna.test',
    'production' => false,
    'siteName' => "Kenny Horna.",
    'siteDescription' => 'Un lugar donde comparto las cosas que me interesan.',
    'siteAuthor' => 'Kenny Horna',
    // ...
];
```

### Personalización

#### Plantillas

Dado que Jigsaw utiliza Blade, podemos modificar nuestros templates tal como lo hacemos en Laravel:

<div class="files">
    <div class="folder folder--open">source
        <div class="folder">_assets
        </div>
        <div class="folder">_categories</div>
        <div class="folder">_components</div>
        <div class="folder folder--open focus">_layouts
            <div class="file">category.blade.php</div>
            <div class="file">master.blade.php</div>
            <div class="file">post.blade.php</div>
            <div class="file">rss.blade.php</div>
        </div>
        <div class="folder folder--open focus">_nav
            <div class="file">menu.blade.php</div>
            <div class="file">menu-responsive.blade.php</div>
            <div class="file">menu-toggle.blade.php</div>
        </div>
        <div class="folder">_posts</div>
        <div class="folder">assets</div>
        <div class="file">404.blade.php</div>
        <div class="file">about.blade.php</div>
        <div class="file">blog.blade.php</div>
        <div class="file">favicon.ico</div>
        <div class="file">index.blade.php</div>
    </div>
</div>

Teniendo a disposición variables tales como `$page` entre otras más:

```html
<meta property="og:title" content="{{ $page->title }}" />
```
```html
@if ($page->cover_image)
  <img src="{{ $page->cover_image }}" alt="{{ $page->title }}" class="mb-2 w-full">
@endif
```
#### Estilos

Como mencioné previamente, Jigsaw utiliza Tailwind. Con Tailwind podemos editar de manera sencilla todo
el aspecto visual de nuestro sitio, como por ejemplo la plantilla principal, que por defecto es 
`source/_layouts/master.blade.php`. A manera de ejemplo, podemos editar la cabecera para que tenga una
posición fija en la parte superior sin importar si nos desplazamos hacia abajo:

```html
# source/_layouts/master.blade.php

<!-- ... -->
<div 
  class="flex flex-col justify-between min-h-screen text-gray-800 leading-normal font-sans bg-gray-100"
>
  <header 
    class="flex items-center shadow bg-white border-b h-20 py-4 sticky top-0 z-50" 
    role="banner"
  >
    <!-- ... -->
  </header>
  <!-- ... -->
</div>
<!-- ... -->
```

Otra de las cosas que hice fue la de personalizar el scrollbar de la sección de código, para esto añadí
lo siguiente en `source/_assets/sass/_blog.scss`:

```css
pre {
  ::-webkit-scrollbar-track {
    -webkit-box-shadow: inset 0 0 2px rgba(0, 0, 0, 0.1);
    @apply bg-purple-900 rounded-full;
  }

  ::-webkit-scrollbar {
    width: 8px;
    height: 8px;
    @apply bg-purple-900 rounded-full;
  }

  ::-webkit-scrollbar-thumb {
    @apply bg-purple-700 rounded-full;
  }
}
```

### Generando assets para pre-visualizar el sitio

Para poder construir los recursos estáticos del sitio, primero instalaremos las dependencias
necesarias:

```bash
npm install 
```

Para luego generar los recursos:
```bash
npm run dev 
```

Al finalizar esto, habremos generado un nuevo directorio: `build_local`. Acá se alojará todo el
código sin optimizar para poder probar localmente mientras modificamos nuestro sitio.

> Si es que deseas ver los cambios en vivo mientras editas tu código, puedes hacer uso de este
> otro comando que recompilará el código cada vez que detecte cambios en tus archivos:
>  ```bash
>  npm run watch 
>  ```

### Creando contenido para nuestro sitio

Como mencioné anteriormente, escribiremos nuestros artículos utilizando
[Markdown](https://es.wikipedia.org/wiki/Markdown). 

Para agregar un nuevo artículo ta solo tenemos que crear un nuevo archivo bajo`sources/_posts`, 
por ejemplo `creando-sitios-con-jigsaw.md`. Jigsaw tomará el nombre del archivo como la url relativa 
con la cual acceder al artículo, no es necesario que le indiques más :). De este modo, para acceder
a nuestro artículo nos dirigirnos hacia `http://misitio.com/blog/creando-sitios-con-jigsaw`. 

> Podemos editar el formato de nuestras URLs desde la configuración de nuestras "colecciones" las
> cuales podemos encontrar en la configuración `config.php`
> ```php
>   'posts' => [
>        'author' => 'Kenny Horna', // Autor por defecto, en caso no se indique
>        'sort' => '-date', // modo de orden
>        'path' => 'blog/{filename}', // formato de rutas
>    ],
> ```

En la cabecera de estos archivos configuraremos los detalles del artículo, para luego continuar
con el cuerpo del artículo en sí utilizando Markdown:

```markdown
---
extends: _layouts.post
section: content
title: Creando sitios estáticos con Jigsaw
date: 2019-10-12
description: Hace algún tiempo tenía ganas de probar Jigsaw, (...)
cover_image: /assets/images/jigsaw.png
featured: true
--- 

Hace algún tiempo tenía ganas de probar Jigsaw, así que describiré como creé este
mismo blog de la mano de esta herramienta.

### ¿Qué es un Sitio Estático?

Los sitios *web estáticos* son aquellos sitios enfocados principalmente a mostrar una
información permanente, donde el **usuario** se limita a obtener la información, sin poder 
realizar mayor interacción con la página web (...)
```
 
### Preparando para exportarlo 

Una vez hayas finalizado tus modificaciones y quieras construir la versión optimizada de producción
para lanzarla al mundo, ejecutamos el siguiente comando que generará otro directorio (`build_production`)
donde se alojarán los recursos estáticos que servirán a nuestro servidor para que muestre el contenido:

```bash
npm run production 
```
 
 Este directorio será el que aloje todo nuestro contenido optimizado, y por tanto, es al que debe
 apuntar nuestro servidor.

### Desplegando nuestro sitio

Puedes desplegar tu sitio en cualquier servidor. Si nos ceñimos a sitios estáticos, hay unas cuantas
alternativas gratuitas donde podemos alojar sin costo alguno nuestro sitio. Entre estos tenemos a
[Github Pages](https://pages.github.com/) y [Netlify](https://www.netlify.com/). Ambos proveen este
servicio de manera gratuita (con ciertas limitaciones, pero suficientes para nuestro caso) proveyendo
también certificados SSL para nuestro sitio.

<img src="/assets/images/posts/0001/netlify-1.png" class="w-full" alt="Netlify service" />

En nuestro caso, utilizaremos este último: Netlify.

Antes de ir a Netlify, crearemos un nuevo archivo (`netlify.toml`) en la raíz del directorio de nuestro 
proyecto donde se alojará la configuración de Netlify para que se automaticen las actualizaciones de 
nuestro sitio.

> Si es que no lo has hecho hasta ahora, también necesitarás subir tu código a un repositorio online, en
> mi caso utilizaré Github (puedes ver el código fuente de este proyecto 
> [aquí](https://github.com/kennyhorna/kennyhorna-blog)). 

```
[build]

command = "npm run production"
publish = "build_production"
environment = { PHP_VERSION = "7.2" }
```

Agregamos este archivo a nuestro repositorio y ahora ya estamos listos para desplegar nuestro site.

Nos dirigimos a [www.netlify.com](www.netlify.com) y accedemos -en caso no tengas una cuenta aún tendrás
que registrarte- y le damos a **"New site from Git"**.

![](/assets/images/posts/0001/netlify-step-1.png)

Luego de esto, para _Despliegue Continuo_ conectaremos con el repositorio online que utilizamos para
alojar nuestro código, en mi caso lo tengo en GitHub (otorgamos permisos, si es que no lo hicimos aún)
y seleccionamos el repositorio que deseamos.

![](/assets/images/posts/0001/netlify-step-2.png)

Dado que ya tenemos nuestro `netlify.toml` configurado, notaremos que se pre-configuraron el resto de campos
por lo que solo queda continuar el proceso. 

Una vez hecho esto, esperamos unos minutos hasta que Netlify construya el sitio. Al finalizar el proceso, nos
otorgará una url del estilo `XXXXXXXXXX.netlify.com` la cual podemos personalizar en caso querramos. También
podemos conectar un dominio registrado anteriormente, o incluso, registrar uno nuevo a través de Netlify.

#### Nota
La url que utilizaremos en producción debemos de incluirla en el archivo de configuración correspondiente
`config.production.php`. Esto es necesario pues todas las rutas se configurarán con esta url base:

```php
 return [
     'baseUrl' => 'https://kennyhorna.com', // <--
     'production' => true,
 ];
```

Para futuras actualizaciones en tu proyecto, como por ejemplo cuando publiques nuevos artículos, solo tendrás
que hacer _push_ a tu repositorio y Netlify se encargará de actualizar tu sitio por ti. 


## Conclusión

Como hemos podido ver, la configuración de un sitio estático se agiliza bastante con Jigsaw, por lo que 
definitivamente es una herramienta a tener en cuenta para proyectos estáticos rápidos. 



