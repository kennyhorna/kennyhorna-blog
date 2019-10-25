---
extends: _layouts.post
section: content
title: "Parte III: Instalación de certificados SSL gratuitos y ajustes finales de Laravel"
date: 2019-10-25
description: Esta es la tercera y última parte de la guía sobre como configurar un VPS utilizando LEMP Stack para servir una aplicación Laravel. En esta ocasión nos enfocaremos en la creación e instalación de nuestro certificado SSL gratuito y de ajustar nuestra aplicación par que lo utilice.  
cover_image: /assets/images/posts/desplegando-aplicaciones-laravel-6-en-lemp-stack-ubuntu-18-y-nginx-p03.png
featured: true
categories: [despliegue, tutoriales, laravel, php]
---

Esta es la tercera -y última- parte de la guía sobre como configurar un VPS utilizando LEMP Stack para servir una
aplicación Laravel. En esta ocasión nos enfocaremos en la creación e instalación de nuestro certificado SSL 
gratuito y de ajustar nuestra aplicación par que lo utilice.

Los artículos de esta mini-serie son los siguientes:

- [Parte I: Instalación y configuración de LEMP Stack](/blog/desplegando-aplicaciones-laravel-6-en-lemp-stack-ubuntu-18-y-nginx-git-hooks-certificado-ssl-parte-i/) 
- [Parte II: Instalación y configuración de Laravel + Git Hooks](/blog/desplegando-aplicaciones-laravel-6-en-lemp-stack-ubuntu-18-y-nginx-git-hooks-certificado-ssl-parte-ii/) 
- **Parte III: Instalación de certificados SSL gratuitos y ajustes finales de Laravel** _(Estamos aquí)_

Sin más que añadir, continuemos.

### Prerrequisitos

1. Tener un VPS con una CLI por medio de la cual operar (tal como hemos configurado en los pasos previos de esta mini-serie) ✔️
2. Nginx o Apache instalado. Dado que montamos nuestro sitio sobre LEMP stack, ya tenemos Nginx instalado y configurado ✔️
3. Un nombre de dominio registrado y operativo. Puedes comprar uno en [GoDaddy](http://godaddy.com/)
, [Namecheap](http://namecheap.com/), [Google Domains](https://domains.google/), entre otros. En mi caso utilizaré
un sub-dominio de un dominio que poseo: ``site.kennyhorna.com``.

### ¿Qué es Let's Encrypt?

Para la generación e instalación de nuestros certificados haremos uso de Let's Encrypt. sin embargo, antes usarlo
sería bueno que sepamos qué es y qué es lo que hace. Tal como mencionan en su [website](https://letsencrypt.org/):

> **Let’s Encrypt** es una autoridad de certificación (AC, o CA por sus siglas en inglés) gratuita, automatizada, 
> y abierta, manejada para el beneficio público. Es un servicio provisto por el Internet Security Research Group (ISRG).

> Le damos a las personas certificados digitales que necesitan en orden para habilitar HTTPS (SSL/TLS) para sitios web, 
> gratuitamente, de la forma más fácil para el usuario en la que podemos. Hacemos esto porque queremos crear un web 
> más seguro y respetador de privacidad.

Con todo esto aclarado, comencemos.
  
### 1. Apuntando el dominio (o sub-dominio) hacia nuestro servidor 
  
Este paso variará dependiendo de tu gestor de dominios, pero la idea es la misma: Crear un 
["A record" o Registro A](https://support.google.com/a/answer/2576578?hl=es-419) que apunte hacia nuestro servidor.

Para el caso de dominios de primer nivel, es recomendable que apuntes la versión tanto con "www" como también
 la versión sin "www":

- _A record_ en **mi-dominio.com** apuntando hacia la IP pública de tu servidor.
- _A record_ en **www.mi-dominio.com** apuntando hacia la IP pública de tu servidor.
  
En el caso de un sub-dominio, bastará con que lo apuntes a tu IP pública.

Dado yo lo haré en un sub-dominio y que mi dominio (``kennyhorna.com``) tiene los DNS apuntando los de hacia Netlify, 
es por ahí por donde apuntaré al sub-dominio hacia mi servidor:

![](/assets/images/posts/desplegando-aplicaciones-laravel-6-en-lemp-stack-ubuntu-18-y-nginx-p03-1.png)
 
 De este modo, ya habré mapeado para el sub-dominio ``site.kennyhorna.com`` apunte hacia la IP pública de mi servidor:
  ``67.207.95.95``.
  
> Si recién has adquirido tu dominio, los DNS pueden tomar hasta 72 horas en propagarse. Si tu dominio ya tiene los
> DNS propagados, el ajuste de nuevos Registros A es, usualmente, instantáneo pero puede tomar hasta 45 minutos.

### 2. Instalando Certbot

Para poder obtener certificados SSL por medo de Let's Encrypt, necesitaremos instalar Certbot en nuestro servidor.

Dado que este proyecto está en constante desarrollo, necesitamos agregar el repositorio de Certbot a nuestro listado
de repositorios a escanear antes de instalarlo. Para esto hacemos:

```bash
sudo add-apt-repository ppa:certbot/certbot
```

Tendremos que aceptar, apretamos <kbd>Enter</kbd>. Paso seguido, actualizaremos la lista de paquetes para recolectar 
la información de los componentes del nuevo repositorio:

```bash
sudo apt update
```

Y finalmente, instalaremos Certbot para Nginx:

```bash
sudo apt install python-certbot-nginx
```

Confirmamos con ``Y``, y ahora sí, Certbot está listo para ser usado, pero para configurar el SSL que trabajará con Nginx, 
necesitaremos verificar la configuración de este.

### 3. Configurando Nginx

Accedemos a la configuración de nuestro bloque de Nginx, dado que en la guía utilizamos el que viene por defecto, hacemos:

```bash
sudo nano /etc/nginx/sites-available/default
```

Nos centramos en la línea que especifica el server_name, que si venimos de los pasos anteriores, estará indicando la IP
pública de nuestro servidor, y añadimos el dominio (o sub-dominio) al listado:

![](/assets/images/posts/desplegando-aplicaciones-laravel-6-en-lemp-stack-ubuntu-18-y-nginx-p03-2.png)

Si en tu caso estás configurando un dominio de primer nivel, deberías añadirlo con y sin "www":

```bash
server_name mi-dominio.com www.mi-dominio.com;
```

Guardamos nuestros cambios y listo.

> Para guardar los cambios apretaremos primero <kbd>Ctrl</kbd> + <kbd>X</kbd>, luego tipeamos `Y` y por último apretamos <kbd>Enter</kbd>.

Ahora vamos a probar nuestra configuración para asegurarnos de que todo marcha bien:

```bash
sudo nginx -t
```

> Si en caso saltara algún error, vuelve a abrir el archivo en busca de errores tipográficos, guarda tus cambios
> y prueba reiniciando Nginx: ``sudo systemctl reload nginx``.

### 4. Permitiendo conexiones HTTPS a través del firewall

Vamos a necesitar habilitar el corta-fuegos ``ufw`` y registrar las reglas necesarias para que permita estas conexiones,
pero dado que Nginx registra pocos perfiles, esto es sencillo.

Para verificar si tenemos habilitado o no el ``ufw`` hacemos:

    sudo ufw status

Si te retorna ``inactive``, necesitaremos activarlo. Para activarlo, hacemos:

    sudo ufw enable 

En cambio, si en el paso anterior, te retornó algo como esto significará que está
activo pero que hay que añadirle las reglas de HTTPS:

```bash
Output
Status: active

To                         Action      From
--                         ------      ----
OpenSSH                    ALLOW       Anywhere                  
Nginx HTTP                 ALLOW       Anywhere                  
OpenSSH (v6)               ALLOW       Anywhere (v6)             
Nginx HTTP (v6)            ALLOW       Anywhere (v6)
```

Podremos ver el listado de perfiles que podemos aplicarle ejecutando el siguiente comando:

```bash
sudo ufw app list
```

![](/assets/images/posts/desplegando-aplicaciones-laravel-6-en-lemp-stack-ubuntu-18-y-nginx-p03-3.png)

Vamos a añadirle las reglas "Nginx Full", para esto hacemos:

```bash
sudo ufw allow 'Nginx Full'
```

![](/assets/images/posts/desplegando-aplicaciones-laravel-6-en-lemp-stack-ubuntu-18-y-nginx-p03-4.png)

> Si en caso ya lo tenías activo, podrías eliminar las reglas básicas de HTTP mediante:
>
> ``sudo ufw delete allow 'Nginx HTTP'``

**IMPORTANTE**: En caso hayamos tenido desactivado el firewall desde un inicio, es posible que luego
de hacer estos cambios **perdamos acceso mediante SSH**. Para evitar esto, añadiremos los perfiles 
de OpenSSH a la lista de reglas permitidas, para hacerlo ejecutamos:

```bash
sudo ufw allow 'OpenSSH'
```

Si todo fue bien, al ejecutar ``sudo ufw status`` debería mostrarnos algo como esto: 

```bash
Output
Status: active

To                         Action      From
--                         ------      ----
Nginx Full                 ALLOW       Anywhere                  
OpenSSH                    ALLOW       Anywhere                  
Nginx Full (v6)            ALLOW       Anywhere (v6)
OpenSSH (v6)               ALLOW       Anywhere (v6)             
```

![](/assets/images/posts/desplegando-aplicaciones-laravel-6-en-lemp-stack-ubuntu-18-y-nginx-p03-5.png)

### 5. Obteniendo nuestro certificado SSL

Certbot ofrece distintas maneras para obtener certificados SSL. Al usar el plugin de Nginx que instalamos
inicialmente, éste se encarga de configurarlo, así como también de recargar la configuración en caso de ser
necesario. Entonces procedemos a ejecutar:

```bash
sudo certbot --nginx -d tu-dominio.com -d www.tu-dominio.com
```

> En mi caso, al tratarse de un sub-dominio ejecuté:
>
> ```bash
>  sudo certbot --nginx -d site.kennyhorna.com
> ``` 

Si es primera vez que configuramos Certbot en ese servidor, nos pedirá que ingresemos una dirección email la
cual será utilizada para comunicar cualquier eventualidad. Escribimos nuestro correo y luego apretamos
<kbd>Enter</kbd>.

A continuación nos pedirá que aceptemos (o declinemos) sus términos y condiciones. Escribimos ``A`` y apretamos
<kbd>Enter</kbd>.

Luego nos preguntará si queremos compartir nuestro email con una fundación sin fines de lucro asociada a 
Let's Encrypt. Escogemos nuestra opción (``Y`` o ``N``) y apretamos <kbd>Enter</kbd>.

Por último, nos preguntará que si queremos redirigir el tráfico HTTP hacia HTTPS, removiendo el acceso HTTP.
Escogemos nuestra opción (``1`` o ``2``) y apretamos <kbd>Enter</kbd>.

Si todo marchó bien, nos mostrará un mensaje de felicitaciones:

```bash
Congratulations! You have successfully enabled https://tu-dominio.com
```

![](/assets/images/posts/desplegando-aplicaciones-laravel-6-en-lemp-stack-ubuntu-18-y-nginx-p03-6.png)

Con esto, tus certificados ya habrán sido transferidos, instalados y cargados. Por lo que si intentamos
acceder a nuestro sitio mediante el dominio, veremos que ya podemos acceder a nuestro sitio y que este
mostrará el mensaje de que es un sitio seguro:

![](/assets/images/posts/desplegando-aplicaciones-laravel-6-en-lemp-stack-ubuntu-18-y-nginx-p03-7.png)

### 6. Verificando la renovación automática de nuestro Certificado SSL

Los certificados caducan cada 90 días. Felizmente para nosotros, Certbot tiene una herramienta que 
nos renueva los certificados automáticamente cada vez que sea necesario. Para probar la configuración
de la renovación automática, podemos hacer:

```bash
sudo certbot renew --dry-run
```

Si es que no hay ningún problema, nos mostrará un mensaje de éxito.

![](/assets/images/posts/desplegando-aplicaciones-laravel-6-en-lemp-stack-ubuntu-18-y-nginx-p03-9.png)

Si el proceso automático de renovación alguna vez fallase, Let’s Encrypt enviará un mensaje al email
que especificamos, alertando que nuestro certificado se encuentra próximo a expirar.

Como nota adicional, podemos verificar la validez de nuestro certificado de la mano de la web [SSL Labs](https://www.ssllabs.com).

Para hacerlo, podemos ingresar en nuestro navegador:

```bash
https://www.ssllabs.com/ssltest/analyze.html?d=mi-dominio.com
```

En mi caso, probando con ``site.kennyhorna.com``, el resultado fue el siguiente:

![](/assets/images/posts/desplegando-aplicaciones-laravel-6-en-lemp-stack-ubuntu-18-y-nginx-p03-8.png)

🎉🎊 Felicidades 🎊🎉, ya tienes tu Certificado SSL instalado y totalmente gratuito.

### 7. Actualizando nuestra aplicación Laravel

Ahora vamos a hacer un último ajuste a nuestra aplicación Laravel, para indicarle nuestro nuevo dominio.

Para esto, editaremos nuestro archivo de variables de entorno (``.env``) que se encuentra en nuestro
directorio de proyecto. Para eso accedemos a nuestro directorio:

```bash
cd /var/www/laravel
```

Paso seguido editaremos el ``.env`` con ayuda de Nano:

```bash
nano .env
```
Para finalmente modificar la llave ``APP_URL`` actualizándole con nuestro dominio (o sub-dominio):

![](/assets/images/posts/desplegando-aplicaciones-laravel-6-en-lemp-stack-ubuntu-18-y-nginx-p03-8.png)

> En caso hayamos guardado en caché nuestra configuración, no olvides correr: ``php artisan config:clear``. 

-----

## Cierre

Con estos pasos finales daré por concluida esta mini-serie de artículos enfocados al levantamiento de un servidor
que ponga a disposición al mundo nuestro increíble proyecto Laravel.

> PD: Si intentas acceder a ``site.kennyhorna.com``, puede que ya no esté disponible, pues solo fue un sitio
temporal que monté para fines de realizar esta guía. 


Espero que haya sido de tu agrado 💪😎

Si tienes dudas, comentarios, observaciones y/o correcciones, no dudes en hacermelas llegar, tanto por aquí
o por [Twitter](https://twitter.com/kennyhorna).


