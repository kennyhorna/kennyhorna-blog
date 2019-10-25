---
extends: _layouts.post
section: content
title: "Desplegando Laravel con LEMP Stack - Parte I: Instalación y configuración de LEMP Stack"
date: 2019-10-19
description: Siempre es difícil querer desplegar aplicaciones Laravel, sobre todo cuando estamos iniciando. Esta guía pretende orientarte en esta -frecuentemente complicada- tarea.  
cover_image: /assets/images/posts/desplegando-aplicaciones-laravel-6-en-lemp-stack-ubuntu-18-y-nginx-p01.png
featured: false
categories: [despliegue, tutoriales, laravel, php]
---

Siempre es un poco difícil querer desplegar aplicaciones Laravel cuando estamos iniciando, es por eso que esta guía pretende orientarte en esta tarea.

Esta guía está basada en la realizada por 
[J.A. Curtis](https://devmarketer.io/learn/deploy-laravel-5-app-lemp-stack-ubuntu-nginx/) hace un par de años,
la cual me ayudó mucho en su momento.

Para facilitar la lectura, he dividido esta guía en tres partes:

- **Parte I: Instalación y configuración de LEMP Stack** _(Estamos aquí)_ 
- [Parte II: Instalación y configuración de Laravel + Git Hooks](/blog/desplegando-aplicaciones-laravel-6-en-lemp-stack-ubuntu-18-y-nginx-git-hooks-certificado-ssl-parte-ii/)
- [Parte III: Instalación de certificados SSL gratuitos y ajustes finales de Laravel](/blog/desplegando-aplicaciones-laravel-6-en-lemp-stack-ubuntu-18-y-nginx-git-hooks-certificado-ssl-parte-iii/)

En esta primera parte nos enfocaremos en la creación y configuración de nuestro VPS, así como también la
instalación y configuración de LEMP Stack.

### Aclaraciones previas

#### LEMP

"LEMP" son las iniciales de **L**inux, **E**ngine-X (Nginx), **M**ySQL y **P**HP. Es un término popular pues
esta pila de tecnologías nos ayudan a hacer disponible al mundo nuestra aplicación.

1. **Linux**: El sistema operativo donde estará montado nuestro servidor. Emplearemos la distribución Ubuntu (18.04)
2. **Nginx**: Es el servidor web.
3. **MySQL**: El gestor de base de datos que utilizaremos.
4. **PHP**: El lenguaje principal en el que está escrita nuestra aplicación (Laravel).

#### ¿LEMP o LAMP?

La diferencia entre uno y otro es el servidor web: Nginx (L**E**MP) vs Apache (L**A**MP). Personalmente siempre 
utilizo Nginx, pero es más por temas de preferencia/familiaridad que porque uno sea mejor o peor que el otro. 
Está en tus investigar sus deferencias bondades/debilidades y escoger el que más te convenga.

Para fines de esta guía, emplearemos Nginx.

#### ¿Dónde debería alojar mi aplicación?

La respuesta correcta es: donde prefieras. Está guía está basada en la instalación de una aplicación Laravel 
en un [VPS](https://es.wikipedia.org/wiki/Servidor_virtual_privado) (_Virtual Private Server_). El modo por 
el cual interactuaremos con nuestro servidor será vía SSH, por tanto, da igual donde lo tengas alojado pues 
el proceso será el mismo.

En este tutorial instalaremos nuestra aplicación en [Digital Ocean](https://m.do.co/c/94f893c66eb5) 
(pueden obtener $50 de regalo usando [este enlace](https://m.do.co/c/94f893c66eb5)). 
Otros sitios recomendados en donde pueden alojar sus aplicaciones son: 
[Vultr](https://www.vultr.com/), [Linode](https://www.linode.com/), [Heroku](https://www.heroku.com/),
entre muchos otros.

<img src="/assets/images/posts/desplegando-aplicaciones-laravel-6-en-lemp-stack-ubuntu-18-y-nginx-0-1.png" class="self-center rounded-lg">

## Comencemos

### 1. Configurando nuestro VPS

> Esto va a depender de donde decidas alojar tu servidor. Dado que en esta guía emplearemos 
> [Digital Ocean](https://m.do.co/c/94f893c66eb5), las imágenes serán las de este servicio. Sin embargo,
> los pasos son similares en los otros servicios listados.

Creamos una cuenta en el servicio -o accedemos si es que ya contamos con una cuenta-  y accedemos a la 
creación del nuestra instancia, en DigitalOcean, las instancias reciben el nombre de _"Droplets"_.

Una vez en el panel de creación, vamos a seleccionar las características de nuestro _droplet_. Para 
comenzar seleccionaremos el sistema operativo, en nuestro caso la versión estable más reciente de Ubuntu:
18.04.3

![](/assets/images/posts/desplegando-aplicaciones-laravel-6-en-lemp-stack-ubuntu-18-y-nginx-1-3.png)

Paso seguido seleccionaremos el tamaño/potencia de nuestro droplet. Esto se hace a través de la elección
del plan en el que esté nuestro droplet. Dado que es para una demo, escogeremos la opción más modesta
que cuesta USD $5/mes. Este tamaño es suficiente para realizar pruebas o para sitios simples que no reciban
mucho tráfico.

> Ten en cuenta que puedes aumentar la capacidad de tu droplet en cualquier momento desde el panel de
> configuración de tu servicio.

![](/assets/images/posts/desplegando-aplicaciones-laravel-6-en-lemp-stack-ubuntu-18-y-nginx-1-4.png)

A continuación, nos preguntará si le queremos asignar un _bloque de almacenamiento_ y la región en donde se
encontrará nuestro droplet. Los **bloques de almacenamiento** son una suerte de "discos locales" que 
pueden ser accedidos por varios de tus droplets (que se encuentren en la misma región). Dado que no tenemos 
configurado ninguno, omitiremos este paso.

Para la región seleccionaré **New York 1** pues es la más "cercana" a mi ubicación y por ende, menor 
ping/latencia. En tu caso selecciona la que más te convenga.

![](/assets/images/posts/desplegando-aplicaciones-laravel-6-en-lemp-stack-ubuntu-18-y-nginx-1-5.png)

#### Llaves SSH

Entraremos ahora a la sección de **Autenticación**. 

> Te recomiendo fuertemente que configures tus llaves SSH en esta sección. Para esto, puedes añadir una llave
> nueva (tu llave pública), o seleccionar una existente en caso no hayas añadido alguna previamente.

![](/assets/images/posts/desplegando-aplicaciones-laravel-6-en-lemp-stack-ubuntu-18-y-nginx-1-6.png)

Como ajustes finales, le daremos un nombre y también algunas etiquetas (opcional).

![](/assets/images/posts/desplegando-aplicaciones-laravel-6-en-lemp-stack-ubuntu-18-y-nginx-1-7.png)

Por último, le daremos en **"Crear Droplet"**.

> Opcionalmente, podemos activar las copias de respaldo automáticas (semanales), útiles si no gestionas esto
> en tu app. Tener en cuenta que esto añade en $1/mes el costo.

Esperamos unos segundos hasta que se cree nuestro droplet. Una vez terminado, lo veremos listado como en
nuestros recursos desde donde visualizaremos la IP de nuestro servidor.

![](/assets/images/posts/desplegando-aplicaciones-laravel-6-en-lemp-stack-ubuntu-18-y-nginx-1-8.png)

### 2. Accediendo mediante SSH

Accederemos ahora a nuestro servidor mediante nuestro cliente SSH. 

> Para Linux y MacOS no es necesario añadir nada, tienen clientes SSH nativos en el terminal. Para Windows 
> en cambio, podemos hacer uso de PuTTY. Puedes descargar PuTTY 
> [desde aquí](https://www.chiark.greenend.org.uk/~sgtatham/putty/latest.html).

    ssh root@XXX.XXX.XXX.XXX

En donde `XXX.XXX.XXX.XXX` es la IP de nuestro servidor. En mi caso utilizaré la IP del servidor que
acabo de crear (67.207.95.95), resultando: `ssh root@67.207.95.95`.

> Al ser primera vez que nos conectamos a este servidor, nos preguntará si confiamos en esta dirección IP
> o si queremos guardar en caché el registro. Aceptamos y continuamos.

![](/assets/images/posts/desplegando-aplicaciones-laravel-6-en-lemp-stack-ubuntu-18-y-nginx-2-1.png)

Como podemos ver, ya hemos accedido remotamente al servidor a través de una interfaz de línea de comandos.
Ya tenemos control total de nuestro VPS. 

### 3. Actualizando el instalador de paquetes

Utilizaremos [APT](https://es.wikipedia.org/wiki/Advanced_Packaging_Tool) para la gestión de nuestro paquetes,
por lo que es importante mantenerlo actualizado. Para esto ejecutaremos:

    sudo apt-get update
    
Una vez ejecutado este comando, te irá detallando el proceso que está realizando a medida que va actualizando
el listado de paquetes.

> Puedes ver más detalle de este comando, y en como se diferencia de este otro: ,`apt-get upgrade`, en 
> [este artículo](http://www.linuxhispano.net/2013/05/03/diferencia-entre-apt-get-update-y-apt-get-upgrade/).

 ### 4. Instalando Nginx
 
 Procederemos a instalar Nginx, para esto ejecutaremos ahora:
 
     sudo apt-get install nginx
     
Nos preguntará si queremos continuar, presionamos <kbd>Enter</kbd>  para que procesa a instalarse. 
Tan simple como esto. De hecho, ahora que tenemos nuestro servidor web instalado, si accedemos a la IP
de nuestro servidor desde nuestro navegador, veremos que Nginx nos da la bienvenida :). 

Deberías ver algo similar a lo siguiente:

![](/assets/images/posts/desplegando-aplicaciones-laravel-6-en-lemp-stack-ubuntu-18-y-nginx-3-1.png)

### 5. Instalando y asegurando MySQL

Las bases de datos son piezas clave en aplicaciones web. Nos permiten almacenar la información y acceder
a ella. Una aplicación sin base de datos, salvo excepciones, prácticamente no tiene mucho sentido. Debido
a esto instalaremos [MySQL](https://es.wikipedia.org/wiki/MySQL).

> De más está decir que puedes instalar el gestor de base de datos que prefieras (Oracle, MongoDB, 
> MS SQL Server, Neo4j, PostgreSQL, etc). En mi caso escogí MySQL.

#### Instalación

Para instalarlo, volveremos a utilizar APT. A la fecha de este artículo, la versión es la v5.7.27.
Escribimos en consola (y apretamos <kbd>Enter</kbd> para aceptar):

    sudo apt-get install mysql-server

#### Asegurando nuestra instalación

MySQL tiene la reputación de ser inseguro. Esto no es porque realmente lo sea, sino que las personas que
lo configuran dejan la configuración por defecto y estas no son seguras para nada. Tomando el caso de mi
entorno local, mi configuración de MySQL ni siquiera tiene clave y el único usuario es **root**. Esto
está bien para un entorno local, pero es imperdonable en un entorno de producción o siquiera en uno 
_stagging_.

Felizmente, MySQL tiene un _helper_ que nos ayudará a librarnos de estos detalles. Nos obligará, por ejemplo,
a que nuestro usuario _root_ tenga una contraseña. Desactiva también el acceso remoto en modo _root_. Para
aplicar estos ajustes solo ejecuta:

    sudo mysql_secure_installation

Nos preguntará si queremos activar el plugin que valida la seguridad/fortaleza de nuestras contraseñas. Dado
que nosotros gestionaremos esto conscientemente, podemos omitir esto. Escribimos `n` y le damos <kbd>Enter</kbd>.

Acto seguido nos pedirá que establezcamos una contraseña (y que la verifiquemos) para el usuario _root_.

> No te preocupes si no aparece nada en pantalla mientra la escribas por seguridad el terminal 
> oculta la contraseña. 

![](/assets/images/posts/desplegando-aplicaciones-laravel-6-en-lemp-stack-ubuntu-18-y-nginx-4-1.png)

Acto seguido preguntará si queremos remover "usuarios anónimos". Escribimos `y` (+ <kbd>Enter</kbd>).

Luego, nos preguntará si queremos "desactivar el acceso remoto en modo root". Escribimos `y` (+ <kbd>Enter</kbd>).

A continuación nos preguntará si queremos remover "las bases de datos de prueba". Escribimos `y` (+ <kbd>Enter</kbd>).

Por último, nos consulta si queremos "recargar la table de privilegios". Nuevamente, escribimos `y` (+ <kbd>Enter</kbd>).

Si todo marchó correctamente, la terminal te mostrará el mensaje **"All Done!"**

![](/assets/images/posts/desplegando-aplicaciones-laravel-6-en-lemp-stack-ubuntu-18-y-nginx-4-2.png)

### 6. Instalando y configurando PHP

#### Instalación

En realidad, Ubuntu ya trae una versión de PHP. Lo que necesitamos, sin embargo, es PHP para procesamiento.
Esto es posible gracias al plugin llamado [PHP-FPM](https://php-fpm.org/).

> PHP-FPM (FastCGI Process Manager) nos ayuda a interconectar programas interactivos con un servidor web.
> Es la implementación alternativa de PHP FastCGI más popular con características adicionales que son 
> realmente útiles para sitios web de alto tráfico. Puedes aprender más sobre PHP-FPM en 
> [este artículo](https://www.stackscale.es/php-fpm-php-webs-alto-trafico/).

Para instalarlo ejecutamos:

    sudo apt-get install php-fpm php-mysql php-mbstring
    
Podemos verificar la versión de PHP que estamos corriendo con el siguiente comando:

    php -v
    
| Al momento de publicado este artículo, la versión por defecto que nos trae es la `7.2.19`.

Con esto ya tendremos todo el **LEMP stack** instalado y listo para poder servir nuestra aplicación :)

![](/assets/images/posts/desplegando-aplicaciones-laravel-6-en-lemp-stack-ubuntu-18-y-nginx-6-1.png)

#### Configurar PHP

Procederemos a configurar todo lo necesario para hacer funcionar nuestra aplicación. Del lado de PHP, no
hay mucho que configurar mas allá de un ajuste de configuración que debemos hacer.

Nuevamente volvemos a la terminal para editar el archivo `php.ini` en el editor de tu preferencia. En 
nuestro caso emplearemos Nano en esta guía.

    sudo nano /etc/php/7.2/fpm/php.ini
    
> En caso utilices una versión distinta de PHP, solo cambia la versión en la ruta, por ejemplo para PHP
> `7.3` haríamos: `sudo nano /etc/php/7.3/fpm/php.ini`

La línea que necesitamos editar es la siguiente:

    ;cgi.fix_pathinfo=1

Puedes buscarla manualmente, o si es que utilizamos Nano, podemos apretar <kbd>Ctrl</kbd> + <kbd>W</kbd> e ingresamos 
`cgi.fix_pathinfo=` para finalmente apretar <kbd>Enter</kbd>.

> Como consejo: Si es que buscas pegar texto en la consola, podrás notar que <kbd>Ctrl</kbd> + <kbd>V</kbd> no
> funciona, en cambio podemos utilizar <kbd>Shift</kbd> + <kbd>Insert</kbd>.

Esto nos llevará hasta la línea correcta. Si nos fijamos, está comentada (al inicio de la línea hay un
`;`) y tiene como valor `1`. Así que vamos a modificarla para que quede de este modo:

    cgi.fix_pathinfo=0

![](/assets/images/posts/desplegando-aplicaciones-laravel-6-en-lemp-stack-ubuntu-18-y-nginx-6-2.png)

Para guardar los cambios apretaremos primero <kbd>Ctrl</kbd> + <kbd>X</kbd>, luego tipeamos `Y` y por último apretamos <kbd>Enter</kbd>.

Para que los cambios surtan efecto, reiniciaremos `php-fpm` de la siguiente manera:

```bash
sudo systemctl restart php7.2-fpm
``` 
    
### 7. Configurando Nginx

Siempre es delicado configurar Nginx, así que mucha atención a partir de ahora.

vamos a proceder a modificar el archivo de configuración por defecto de Nginx del siguiente modo:

    sudo nano /etc/nginx/sites-available/default
    
Verás muchas líneas que inician con `#`, todos estos son comentarios. Por motivos de simplicidad vamos
a ignorar estos de momento. tendrá la siguiente forma:

```bash
server {
    listen 80 default_server;
    listen [::]:80 default_server;
    
    root /var/www/html;
    index index.html index.htm index.nginx-debian.html;

    server_name _;

    location / {
        try_files $uri $uri/ =404;
    }
}
```

Añadiremos `index.php` adelante de `index.html` en el listado de archivos índice de modo que 
quedaría así: 

    index index.php index.html index.htm index.nginx-debian.html;
    //    ^^^^^^^^^
    
![](/assets/images/posts/desplegando-aplicaciones-laravel-6-en-lemp-stack-ubuntu-18-y-nginx-7-1.png)

A continuación cambiaremos el `server_name` y colocaremos acá la IP de nuestro VPS. Esto le dice
a Nginx a qué dominio debe de responder. Dado que de momento accedemos a nuestro servidor mediante
la dirección IP, será lo que colocaremos. Sin embargo, en caso tengamos un dominio que queramos
utilizar para nuestra aplicación, podemos añadirlo acá también.

    server_name 67.207.95.95;
 
![](/assets/images/posts/desplegando-aplicaciones-laravel-6-en-lemp-stack-ubuntu-18-y-nginx-7-2.png)

Ahora le indicaremos a Nginx que utilice la extensión que instalamos anteriormente: `php-fpm`. 

Si nos fijamos en el formato de configuración del archivo que estamos editando, notaremos que cada 
servidor está estructurado por distintas directivas. Una de estas son las `location`. si observamos,
tenemos tres bloques que inician con `location`: el inicial y dos comentados (inician con `#`):

![](/assets/images/posts/desplegando-aplicaciones-laravel-6-en-lemp-stack-ubuntu-18-y-nginx-7-3.png)

Entonces, para activar `php-fpm`, vamos a quitarle los comentarios al primer bloque comentado (el
segundo en el archivo) y a modificar la versión de php a utilizar.

Así mismo, procederemos a quitarle los comentarios al segundo bloque comentado (el tercero en el archivo),
esto le dirá a Nginx que ignore los archivos `.htaccess`. Esto es porque estos archivos son para Apache,
 y dado que nosotros estamos utilizando Nginx no tiene mucho sentido utilizarlos.
 
 Aplicando estos cambios resulta lo siguiente:
 
     # ...
 
     location ~ \.php$ {
         include snippets/fastcgi-php.conf;
         fastcgi_pass unix:/run/php/php7.2-fpm.sock;
     }
 
     location ~ /\.ht {
         deny all;
     }

> Nota: Tener en cuenta mantener comentada esta línea: `fastcgi_pass 127.0.0.1:9000;`

![](/assets/images/posts/desplegando-aplicaciones-laravel-6-en-lemp-stack-ubuntu-18-y-nginx-7-4.png)

Ahora guardaremos nuestros cambios (<kbd>Ctrl</kbd> + <kbd>X</kbd>, luego tipeamos `Y` y por último apretamos <kbd>Enter</kbd>)
para poder validar si nuestra configuración es correcta. Para hacer esto ingresamos:

    sudo nginx -t
    
Si es que no cometimos algún error, el resultado del comando anterior debería devolvernos algo así:

![](/assets/images/posts/desplegando-aplicaciones-laravel-6-en-lemp-stack-ubuntu-18-y-nginx-7-5.png)

Con esto configurado, ya estamos listos para desplegar nuestra aplicación PHP.

### 8. Creando un directorio para nuestra Laravel

Ahora que nuestro servidor está listo para "servir" archivos, le cargaremos nuestro proyecto de Laravel
para que pueda cumplir su tarea. Por defecto Nginx va a buscar en el directorio `/var/www/` como la raíz
desde donde leer nuestros archivos. Crearemos un directorio dentro de este y le llamaremos `laravel` (o
lo que prefieras).

    sudo mkdir -p /var/www/laravel

Lo siguiente es decirle a Nginx que busque en esta carpeta, y no solo eso, sino especificarle donde se
encuentra el archivo por defecto de nuestro sitio.En el caso de Laravel, este archivo es el `index.php`
que encontramos dentro del directorio `/public`. Este archivo es el de entrada y hacia donde se dirigen
todos los requests que llegan a nuestra aplicación, ya sea mediante API, etc. Por tanto, este archivo
recibe también todos los parámetros adicionales que llegan con cada request.

Para hacer esto, nuevamente editaremos nuestra configuración de Nginx:

    sudo nano /etc/nginx/sites-available/default
    
En esta ocasión, nuestro foco estará en la directiva `root`, que es la que especifica el directorio raíz
de este servidor. Ahí la actualizaremos por `/var/www/laravel/public`. Es la única línea a editar:

    server {
        listen 80 default_server;
        listen [::]:80 default_server ipv6only=on;
    
        root /var/www/laravel/public;  # <--
        index index.php index.html index.htm;
        
        # ...

![](/assets/images/posts/desplegando-aplicaciones-laravel-6-en-lemp-stack-ubuntu-18-y-nginx-8-1.png)

Más arriba, comenté que el archivo `index.php` recoge también los parámetros adicionales que llegan con cada
request (el payload), por tanto debemos decirle a nuestro servidor que también envíe esta data a Laravel.
Para esto volveremos a modificar Nginx actualizando lo siguiente:

        server_name 67.207.95.95;

        location / {
                try_files $uri $uri/ /index.php?$query_string;
        }                          # ^^^^^^^^^^^^^^^^^^^^^^^^^
        
![](/assets/images/posts/desplegando-aplicaciones-laravel-6-en-lemp-stack-ubuntu-18-y-nginx-8-2.png)

Guardaremos nuestros cambios (<kbd>Ctrl</kbd> + <kbd>X</kbd>, luego tipeamos `Y` y por último apretamos <kbd>Enter</kbd>). Luego de esto,
procederemos a reiniciar Nginx para que los cambios surtan efecto.

    sudo service nginx restart
    
Si es que ahora nos dirigimos hacia la url de nuestro dominio (o en nuestro caso, a nuestra IP), veremos
este error `404` en lugar de la página de bienvenida que retornaba previamente:

![](/assets/images/posts/desplegando-aplicaciones-laravel-6-en-lemp-stack-ubuntu-18-y-nginx-8-3.png)

### 9. Creando un archivo Swap (Opcional)

Antes de instalar composer o Laravel, necesitamos tener en cuenta la memoria de nuestro VPS. Instalar
estas aplicaciones requieren una descarga alta de archivos (en comparación de instalación de simple paquetes)
y podría terminar consumiendo toda la memoria RAM de nuestro servidor, sobretodo si estamos en una instancia
pequeña <1GB (como en nuestro caso). Si cuentas con una instancia más poderosa, puedes omitir este paso.

Un archivo Swap le permite al sistema operativo liberar data de la memoria RAM y almacenarla en el SSD
para cuando necesita más espacio del disponible.

Por tanto, crearemos un archivo Swap de 1GB de capacidad en nuestro SSD:

    sudo fallocate -l 1G /swapfile
    
A continuación le diremos a Ubuntu que formatee este espacio:

    sudo mkswap /swapfile

Por último, le diremos que lo comience a utilizar haciendo:

    sudo swapon /swapfile

-----

## Cierre

Con todo esto realizado ya tenemos todo listo para subir, instalar y configurar nuestra aplicación Laravel 
en nuestro recientemente configurado VPS. Estos pasos lo realizaremos en la siguiente parte de esta mini-serie &#128515;:

- [Parte II: Instalación y configuración de Laravel + Git Hooks](/blog/desplegando-aplicaciones-laravel-6-en-lemp-stack-ubuntu-18-y-nginx-git-hooks-certificado-ssl-parte-ii/)
