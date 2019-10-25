---
extends: _layouts.post
section: content
title: "Desplegando Laravel con LEMP Stack - Parte II: Instalación y configuración de Laravel + Git Hooks"
date: 2019-10-23
description: Esta es la segunda parte de la guía sobre como configurar un VPS utilizando LEMP Stack para servir una aplicación Laravel. En esta ocasión nos enfocaremos en la configuración de Git y Laravel.  
cover_image: /assets/images/posts/desplegando-aplicaciones-laravel-6-en-lemp-stack-ubuntu-18-y-nginx-p02.png
featured: false
categories: [despliegue, tutoriales, laravel, php]
---

En esta segunda parte nos enfocaremos en la instalación de Git, la configuración de nuestro Git Hook, la configuración
de Laravel y de nuestro entorno de producción para poder servir correctamente nuestra aplicación Laravel.

Los artículos de esta mini-serie son los siguientes:

- [Parte I: Instalación y configuración de LEMP Stack](/blog/desplegando-aplicaciones-laravel-6-en-lemp-stack-ubuntu-18-y-nginx-git-hooks-certificado-ssl-parte-i/) 
- **Parte II: Instalación y configuración de Laravel + Git Hooks** _(Estamos aquí)_
- [Parte III: Instalación de certificados SSL gratuitos y ajustes finales de Laravel](/blog/desplegando-aplicaciones-laravel-6-en-lemp-stack-ubuntu-18-y-nginx-git-hooks-certificado-ssl-parte-iii/)

Sin más que añadir, continuemos.

### 10. Instalando Composer

Ya has instalado Composer anteriormente (de lo contrario no tendría mucho sentido que estés leyendo esta guía 🤔)
por lo este paso será conocido para ti. Accedemos por la termina a nuestro servidor (mediante SSH) y ya en la consola
ejecutamos: 

```bash
cd ~ && curl -sS https://getcomposer.org/installer | php
```

Con esto tendremos ya el `composer.phar` en nuestro directorio de inicio, así que es hora de moverlo hacia el `bin` para
poder ejecutarlo de manera más sencilla simplemente haciendo`composer`.

```bash
sudo mv composer.phar /usr/local/bin/composer
```

Puedes comprobar si todo salió bien ejecutando `composer` en la consola, debería de mostrarte todos los comandos 
disponibles de composer.

### 11. Instalando Git

Git no es un desconocido para casi nadie hoy en día y es, básicamente, un estándar. Claro que puedes utilizar otros
métodos de gestión de versiones (o incluso subiendo tus archivos vías SFTP), pero nosotros usaremos Git.

Instalaremos Git en nuestro servidor en un directorio llamado `/var/repo/`. Empecemos por ahí:

```bash
cd /var
mkdir repo && cd repo
```
Con esto, notaremos que ahora estamos dentro del directorio `/var/repo/`. Ejecutamos lo siguiente:

```bash
mkdir site.git && cd site.git
git init --bare
```

> El flag `--bare` puede que te resulte nuevo, y esto es porque -por lo general- se utiliza únicamente en servidores.
> Un repositorio `bare` es de un tipo especial cuyo único propósito es recibir _"pushes"_ de desarrolladores.
> Puedes aprender más sobre esto en la [documentación oficial](https://git-scm.com/book/ch4-2.html).

Con esto tendremos un repositorio Git en `/var/repo/site.git`, 👏👏. 

### 12. Creando nuestro Git Hook

#### Conociendo los _Hooks_

Los repositorios Git tienen una función interesante llamada _"hooks"_ ("ganchos") que usaremos para mover nuestros
archivos luego de ejecutar _git push_. Lo que hacen los _hooks_ es ejecutar cierta acción cuando estos son activados.
Hay tres tipos de _hooks_ a través de Git: _pre-receive_, _post-receive_, and _update_ (más info 
[aquí](https://git-scm.com/book/es/v1/Personalizando-Git-Puntos-de-enganche-Git)).

En nuestro caso, haremos uso del _post-receive_ hook, el cual se activará cuando el repositorio ha descargado tus
archivos completamente luego de recibir un _push_.

#### Configurando nuestro _Hook_

Para crear nuestro hook necesitamos entrar al directorio `hook` que se encuentra dentro nuestro directorio `site.git`.
Para hacerlo haremos:

```bash
cd /var/repo/site.git/hooks
```

Una vez dentro, crearemos nuestro `post-receive` script, el cual se ejecutará luego de recibir nuevo código. Esto lo
haremos con ayuda de Nano:

```bash
sudo nano post-receive
```

Como ya sabemos, esto nos abrirá un archivo `post-receive` en blanco donde podremos añadirle contenido. 
Escribimos (o pegamos) lo siguiente:

```bash
#!/bin/sh
git --work-tree=/var/www/laravel --git-dir=/var/repo/site.git checkout -f
```

> Para pegar en el terminal puede que no te funcione el <kbd>Ctrl</kbd> + <kbd>V</kbd>, si es así, prueba <kbd>Shift</kbd> + <kbd>Insert</kbd>.

Ahora guardamos y salimos.

> Para guardar los cambios apretaremos primero <kbd>Ctrl</kbd> + <kbd>X</kbd>, luego tipeamos `Y` y por último apretamos <kbd>Enter</kbd>.

¿Pero qué hemos hecho exactamente? Veamos.

La directiva `--work-tree=` le indica a Git donde es que debe copiar los archivos recibidos luego de haber sido
descargados por completo. Es por eso que le indicamos el directorio que contendrá nuestra app de Laravel. La
directiva `--git-dir=` le dice a Git donde está el directorio git _bare_ que ha recibido el código. 
**Es así de simple**.

Luego de guardar el script, necesitamos ejecutar un comando más antes de dejar todo listo para recibir y copiar
el código. El archivo `post-receive` necesita permisos de ejecución en order de poder copiar los archivos de un
lado a otro. Para hacer eso ejecutamos lo siguiente (asegurate de seguir dentro de `/var/repo/site.git/hooks/`):

```bash
sudo chmod +x post-receive
```

Eso es todo. Ahora estamos listo para enviar nuestro código al servidor, este será copiado a nuestro directorio
de Laravel, el cual a su vez será leído por Nginx para que pueda servirlo hacia nuestros usuarios 👌.

Hemos acabado por el momento con la configuración de nuestro servidor, ahora necesitamos cerrar nuestra conexión
SSH para acceder a nuestra máquina local para el siguiente paso. Para salir, tan solo escribe `exit`.

    exit
    
Tu línea de comandos se debe de haber cerrado (PuTTY) o ahora notarás que en la consola figura tu nombre
del usuario de tu sistema en lugar de `root@localhost#`.

### 13. Subiendo nuestro proyecto al servidor remoto

#### Configurando nuestro entorno local para subir código 

Ahora que nuestro servidor está listo para recibir código, es hora de configurar nuestra computadora local para
que apunte hacia este cuando queramos subir nuestros cambios a nuestro entorno de producción (nuestro servidor).

Del mismo modo que subimos nuestros archivos a Github/Gitlab/Bitbucket, configuramos un git remote llamado
origin que representa a nuestro almacén remoto de código (en este caso, Github). Así que cuando queremos subir
nuestros cambios hacia Github ejecutamos un comando de este estilo `git push origin mi-rama`. Esto le dice a git
que suba la rama (en este caso la rama llamada `mi-rama`) hacia nuestro "origin" remoto.

Haremos lo mismo nosotros. Crearemos un nuevo _remote_, al cual llamaremos `production`, el cual representará
a nuestro servidor remoto. Nuestra meta es que cuando ejecutemos `git push production master`, git suba nuestros 
cambios de la rama `master` a nuestro servidor remoto.

> Esto no interfiere en absoluto con la subida de cambios a Github/etc, pues seguirá estando disponible el resto
> de _remotes_ que tengamos configurado en nuestro proyecto, por ejemplo el origin: `git push origin master`.
> Puedes crear tantos _remotes_ como desees, podrías tener uno para tu servidor `staging`, otro para el de
> `production`, etc.

Para crear un nuevo _remote_ vamos al **directorio donde tenemos nuestro proyecto**, y desde ahí ejecutamos
`git remote add`:

```bash
cd mis-sitios/kennyhorna    # modificalo para tu caso
git remote add production ssh://root@MI-DOMINIO-O-IP/var/repo/site.git
```

Asegúrate de reemplazar `MI-DOMINIO-O-IP` por tu dominio (o IP). En mi caso, lo cambiaré por la IP de mi servidor,
pues aún no le hemos configurado ningún dominio. Como resultado, en mi caso sería algo así:

```bash
git remote add production ssh://root@67.207.95.95/var/repo/site.git
```

#### Subiendo nuestro proyecto

Con este comando ejecutado hemos configurado ya nuestro _remote_ de producción. Así que -asumiendo que nuestro 
código está listo para ser subido- podemos proceder a subir nuestro código.

```bash
git push production master
```

#### Verificando que nuestro Git Hook funcione

Tenemos que comprobar que nuestro Hook funciona, de lo contrario, todo esto habría sido en vano ;). Para esto,
vamos a volver a acceder a nuestro servidor mediante `SSH`:

    ssh root@67.207.95.95  # adaptalo a tus parámetros
    
Ahora listaremos los archivos que contiene nuestro directorio especial de Laravel:

    ls /var/www/laravel
    
> Lo que hace el comando `ls` es listar los archivos y directorios que contiene el directorio desde donde se ejecuta.

Por tanto, si todo salió bien, deberíamos ver todos nuestro archivos de Laravel 🎉🎉.

![](/assets/images/posts/desplegando-aplicaciones-laravel-6-en-lemp-stack-ubuntu-18-y-nginx-p02-1.png)

### 14. Configurando nuestra Base de Datos

En la primera parte de esta guía instalamos MySQL, pero sin embargo, no hicimos mayor configuración. Aún ni
siquiera hemos creado la base de datos que utilizará nuestro proyecto. Resolvamos esto de una vez.

Accederemos a la consola de MySQL con el siguiente comando:

```bash
mysql -u root -p'tu-clave'
```

> Asegúrate de reemplazar `tu-clave` por la clave que estableciste en tu base de datos.
> Observación: nota que no hay espacio entre la `p` y el primer `'`.

Si todo marchó bien, deberías ver la línea de comenzar iniciando con un `mysql>`:

![](/assets/images/posts/desplegando-aplicaciones-laravel-6-en-lemp-stack-ubuntu-18-y-nginx-p02-2.png)

Esto nos indica que accedimos a MySQL, por lo tanto, ya podemos crear nuestra base de datos.
Dado que estoy subiendo un proyecto de prueba, mi base de datos se llamará `my_site`. Ajusta esto
a tus necesidades:

    CREATE DATABASE my_site;

Para validar que nuestra DB ha sido creada, podemos ejecutar:

    SHOW DATABASES;

![](/assets/images/posts/desplegando-aplicaciones-laravel-6-en-lemp-stack-ubuntu-18-y-nginx-p02-3.png)

Con esto, ya podemos salir de MySQL. Ingresamos ``exit`` y le damos a <kbd>Enter</kbd>.

    exit

### 15. Configurando Laravel

#### Instalando nuestras dependencias

Para comenzar, debemos instalar las dependencias de nuestra aplicación pues, de lo contrario, no funcionará como se
debe. Para esto, haremos uso de Composer, herramienta que instalamos al inicio de est artículo.

Nos situaremos dentro de la carpeta que contiene nuestro proyecto (`cd /var/www/laravel`) y desde ahí ejecutaremos:

```bash
composer install --no-dev
```

> El flag `--no-dev` le indicará a Composer que ignore las dependencias de desarrollo. Dado que nuestro entorno remoto
> está pensado para ser usado en producción, tiene todo el sentido del mundo hacer esto.

 #### Estableciendo permisos
 
 Para poder correr como se debe, Nginx necesita ciertos permisos sobre nuestro directorio Laravel. En primer lugar,
 necesitamos cambiar de dueño a nuestro directorio de Laravel hacia nuestro grupo web.
 
 ```bash
 sudo chown -R :www-data /var/www/laravel
 ```

Ahora el grupo web es dueño de los archivos, en lugar del usuario `root`. A continuación, necesitamos darle permisos
de escritura a nuestro grupo web sobre el directorio `storage` de tal modo que pueda escribir sobre este folder. Es aquí
donde almacenamos nuestros _logs_, _cache_, entre otras cosas. Para hacer esto hacemos:

```bash
sudo chmod -R 775 /var/www/laravel/storage
```
 
Del mismo modo, también debemos de darle permiso de escritura sobre el directorio `/boostrap/cache`:

```bash
sudo chmod -R 775 /var/www/laravel/bootstrap/cache
```

Ok, ahora nuestros directorios son "writables". Aún nos quedan cosas por hacer, pero nos estamos acercando
bastante a completar una instalación exitosa ;)

### 16. Configurando nuestro entorno remoto de Laravel

Todo lo que nos falta hacer por ahora es configurar nuestra aplicación Laravel. Esto lo haremos de la misma
manera que lo haríamos en un entorno local. Utilizaremos los archivos que están dentro de ``config``  y 
también configuraremos nuestro archivo ``.env``.

Como ya sabrás, todos los archivos que están listados en nuestro ``.gitignore`` serán ignorados por Git, por
tanto acá es donde incluimos archivos/carpetas que queremos que nuestro VCS gestione, ya sea por motivos
de seguridad o de facilidad. 

![](/assets/images/posts/desplegando-aplicaciones-laravel-6-en-lemp-stack-ubuntu-18-y-nginx-p02-4.png)

Un ejemplo es el directorio ``/vendor``, este directorio tiene todas las dependencias que nuestra aplicación 
necesita. Sin embargo, dado que **sí** incluimos ``composer.json`` y ``composer.lock`` en nuestro VCS, Composer 
podrá saber qué dependencias y qué versión específica de cada una de ellas necesita nuestra aplicación en orden 
de poder instalarlas, por tanto incluir esta carpeta se hace un poco redundante. Es por eso que la ignoramos.
Lo mismo sucede con el directorio ``node_modules``, gestionado de manera análoga por los archivos 
``package.json`` y ``package.lock``.

Entonces, en resumen. Todo lo que consideremos vital para nuestra aplicación no debería de estar listado en
nuestro ``.gitignore``.

#### Entendiendo el archivo ``.env``

El ``.env`` contiene (o debería contener) todas las llaves y detalles de la configuración de nuestra aplicación. 
Esto incluye: Detalles de nuestra aplicación y entorno, La llave de encriptación de nuestra app, la configuración
 de nuestra(s) bases de datos, configuración de nuestro servidor de emails y mucho más.. 

Ahora te preguntarás ¿Por qué aparece el ``.env`` en nuestro ``.gitignore``, si es tan clave para Laravel? La
respuesta es simple: Muchas de estas configuraciones son específicas a cada entorno (de ahí su nombre: ``env``
es diminutivo de ``environment`` que significa _ambiente/entorno_). Veamos un ejemplo. 

En nuestro entorno local puede que utilicemos un motor base de datos más simple como ``SQLITE``, con esta base de 
datos trabajamos y todo bien. Puede que nuestra aplicación, en el entorno real de producción, necesite un motor de 
base de datos distinto -como por ejemplo MySQL o SQL SERVER- entonces, para poder hacer realidad esto de manera simple,
podríamos tener definido un driver y conexión a base de datos distinta en nuestro entorno remoto que difiera del de
nuestro entorno local. ¿Dónde haríamos estas diferencias? Estás en lo correcto: en nuestro ``.env``. 

Si no fuera de este modo, cada vez que hicieras cambios en tu aplicación, tendrías que cambiar tus llaves de `SQLITE` 
por las del otro motor de base de datos para luego recién subir tus cambios al servidor (y viceversa para cuando 
quieras hacer pruebas locales). Por tanto, utilizando el ``.env`` podemos hacer los mismos cambios en dos entornos
distintos y cada uno funcionará dependiendo de la configuración específica que tenga en su correspondiente entorno.
**Magnífico**.

Ahora que ya sabemos el motivo del uso de los ``.env``, vamos a proceder a crear uno.

#### Creando nuestro archivo ``.env``

Si nos fijamos (``ls -A /var/www/laravel``) veremos que sí bien no tenemos el ``.env``, sí que tenemos uno similar: 
``.env.example``. Podemos utilizar este otro fichero como modelo de ejemplo para poder crear nuestro ``.env``.

> Tal vez hayas notado que incluimos el flag ``-A`` al listar los archivos del directorio laravel: ``ls -A /var/www/laravel``
> lo que hace este argumento es incluir en el listado los archivos ocultos.

Entonces, lo que haremos será clonar el ``.env.example``, renombrarlo por ``.env`` y colocar en este último los 
parámetros de nuestra aplicación, como por ejemplo, la conexión a la base de datos que creamos en uno de los pasos
anteriores. Nos situaremos dentro de nuestra carpeta de Laravel:

```bash
cd /var/www/laravel
cp .env.example .env
```

Con esto ya tendremos nuestro ``.env`` clonado. Ahora procederemos a personalizarlo con ayuda de Nano.

```bash
nano .env
```
Al hacer esto nos mostrará algo parecido a lo siguiente:

![](/assets/images/posts/desplegando-aplicaciones-laravel-6-en-lemp-stack-ubuntu-18-y-nginx-p02-7.png)

Dado que estamos en un entorno de producción, podemos hacer unos cambios iniciales como:

```bash
APP_NAME="El nombre de mi App"
APP_ENV=production
APP_DEBUG=false
APP_URL=http://mi-dominio-o-mi-ip
```

``APP_NAME`` y ``APP_URL`` se describen solos. ``APP_ENV`` le indica a Laravel que estamos en un entorno 
de producción. ``APP_DEBUG`` le indica si es que debe mostrar los errores extensos o si, en cambio, queremos 
que se maneje de una manera más humana (que es lo que queremos en un entorno de producción, dado que serán 
nuestros usuarios quienes vean estos mensajes).

> Habrás notado que ignoramos la llave ``APP_KEY``, descuida, esto lo generaremos con un comando más adelante.

Paso seguido, pondremos los detalles de nuestra base de datos, para que Laravel pueda acceder sin problemas.

Para esto modificaremos las siguientes llaves:

```bash
DB_HOST=localhost
DB_DATABASE=my_site
DB_USERNAME=root
DB_PASSWORD=*******
```

Estas llaves también son simples de deducir. ``DB_HOST`` le indica a Laravel el host donde está alojada nuestra 
base de datos. Si es que nuestra base de datos está en otro servidor, podríamos indicarle ahí el dominio (o IP) a
apuntar. ``DB_DATABASE`` es el nombre de la base de datos que creamos más arriba, en mi caso ``my_site``. Dado
que estoy usando el usuario `root` en mi base de datos, colocaremos esto en ``DB_USERNAME``, y por tanto, en 
``DB_PASSWORD`` colocaremos la clave de este usuario.

Tal ves querrás ajustar la configuración de tus drivers de _cache_, _queue_, _session_, etc. Todo esto lo podrás
hacer en este file, por ejemplo la configuración del servidor del driver que manejará los Emails.

Así que nuestro ``.env`` quedaría de la siguiente forma (a groso modo):

![](/assets/images/posts/desplegando-aplicaciones-laravel-6-en-lemp-stack-ubuntu-18-y-nginx-p02-8.png)
 

#### Generando nuestra llave de encriptación

Tal como mencioné anteriormente, vamos a generar nuestra llave de encriptación con ayuda de un comando del propio
Laravel. Para esto ejecutamos:

```bash
php artisan key:generate
```

Con esto, Laravel generará y almacenará automáticamente nuestra llave en nuestro``.env`` bajo ``APP_KEY``.

Hay muchos otros ajustes que podemos configurar en nuestra aplicación, no en vano tenemos un directorio específico
para todos los archivos de configuración de nuestro app: ``config``. Dado que estos archivos sí están considerados
por Git, podemos modificarlos en nuestro entorno local, hacer un commit con estos cambios y subirlos a nuestro 
servidor remoto haciendo un _push_ a _production_.

#### Guardando en caché nuestra configuración

Dado que hay muchos apartados de configuración, es buena idea guardad en caché estos valores. Para esto podemos
hacer:

```bash
php artisan config:cache
```

 >  Ten en cuenta que luego de cada modificación que hagamos a la configuración deberemos de limpiar esta caché, de lo
 contrario Laravel ignorará estos cambios. Para hacer esto podemos volver a ejecutar el mismo comando, el cual limpiará
> cualquier caché existente y lo regenerará: 
>
> ```bash
> php artisan config:cache    # Regenera la caché tomando los cambios
> ```

### 17. Migrando nuestra base de datos

Ahora que tenemos nuestra configuración lista, podemos proceder a migrar nuestra base de datos. Para esto ejecutamos, 
como ya sabemos:
 
```bash
php artisan migrate
```

> Nos preguntará si estamos seguros de correr este comando pues estamos en un entorno de producción, escribimos ``yes``
> y damos <kbd>Enter</kbd>.

![](/assets/images/posts/desplegando-aplicaciones-laravel-6-en-lemp-stack-ubuntu-18-y-nginx-p02-9.png)

Dado que estoy subiendo una instalación fresca de Laravel, en mi caso solo migrará las tablas por defecto que trae: 
``users``, ``password_resets`` y ``failed_jobs``.

#### Poblando nuestra base de datos

Podemos poblar nuestra base de datos mediante [Seeders](https://laravel.com/docs/seeding). Si es que tuvieras 
configurado tus _seeders_ para poblar tus tablas maestras, es buen momento de correrlos. Para esto haríamos:

```bash
php artisan db:seed
```

Si es que -tal como en mi caso- no tienes configurado _Seeders_, podemos hacer uso de 
[Tinker](https://laravel.com/docs/5.8/artisan#tinker) para agregarle un usuario a nuestra app. Para acceder
a Tinker hacemos:

```bash
php artisan tinker
```

Con Tinker podemos ejecutar comandos de Laravel de manera automática y simple. Entonces, ahora crearemos una nueva
instancia del -en mi caso- modelo ``User``, que es el que tengo como tabla principal. Le añadiremos datos y por
último guardaremos en la base de datos:

```bash
$u = new App\User;
$u->name = 'Mesut Özil';
$u->email = 'mesut.ozil@arsenal.com';
$u->password = Hash::make('assist-king');
$u->save();

exit
```
 
 Con esto habremos creado nuestro usuario con éxito.
 
![](/assets/images/posts/desplegando-aplicaciones-laravel-6-en-lemp-stack-ubuntu-18-y-nginx-p02-10.png)
 
> Podrás notar que para el campo ``password`` le apliqué un "hasheo" a la contraseña. Esto es para darle una capa
> adicional de seguridad a nuestro sistema. Por defecto, al hacer un intento de login, Laravel comparará la 
> contraseña que le brindemos (luego de haberla "hasheado") con la que tengamos en nuestra base de datos. Estas 
> deberían de coincidir.
>
> Para hacer [Hashing](https://laravel.com/docs/hashing), Laravel hace uso de nuestra llave de encriptación 
> (la cual generamos y almacenamos en nuestro ``.env``, bajo ``APP_KEY``).
>

------ 

## Cierre

🎉🎉🎉🎊 **¡Felicidades!** 🎊🎉🎉🎉, con esto ya tenemos nuestro VPS configurado con todo en orden para poder utilizar 
nuestra aplicación Laravel y subir cambios hacia esta de manera rápida y simple. Podemos comprobarlo dirigiéndonos 
hacia nuestro dominio (o en mi caso, hacia nuestra IP):

![](/assets/images/posts/desplegando-aplicaciones-laravel-6-en-lemp-stack-ubuntu-18-y-nginx-p02-11.png)

Como hemos podido ver, el proceso es un poco largo, pero he tratado de incluir el máximo detalle posible para que
quede claro qué hacemos en cada paso.
 
> Es importante saber el **QUÉ**, pero más importante aún, saber el **POR QUÉ** de lo que hacemos.

Ya tenemos nuestra aplicación funcionando. Solo nos queda configurar nuestro dominio en nuestra app y agregarle un
certificado SSL a nuestra aplicación. Esto lo veremos en la tercera -y última- parte:

- [Parte III: Instalación de certificados SSL gratuitos y ajustes finales de Laravel](/blog/desplegando-aplicaciones-laravel-6-en-lemp-stack-ubuntu-18-y-nginx-git-hooks-certificado-ssl-parte-iii/) 

Si es que tienes dudas sobre los pasos anteriores, visita la [Parte I (Instalación y configuración de LEMP Stack)](/blog/desplegando-aplicaciones-laravel-6-en-lemp-stack-ubuntu-18-y-nginx-git-hooks-certificado-ssl-parte-i/)
de la guía.

Cualquier comentario, observación, pregunta y/o aclaración es bien recibida así que.. nos vemos 💪😉.
