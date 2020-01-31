---
extends: _layouts.post
section: content
title: "Compartiendo apps Laravel utilizando tu servidor local con Ngrok"
date: 2020-01-21
description: "¿Alguna vez has necesitado compartir rápidamente tu aplicación con algún compañero, desarrollador o incluso con el cliente? Pues Ngrok te permite hacerlo sin necesidad de desplegar nuestra aplicación a un entorno de pruebas/producción."  
cover_image: /assets/images/posts/0014/compartiendo-tu-proyecto-laravel-utilizando-tu-servidor-local-con-ngrok.png
featured: false
categories: [despliegue, laravel, php, herramientas]
---

¿Alguna vez has necesitado compartir rápidamente tu aplicación con algún compañero, desarrollador o incluso con el cliente?
Pues Ngrok te permite hacerlo sin necesidad de desplegar nuestra aplicación a un entorno de pruebas/producción.

PD: En realidad esto no es exclusivo de Laravel, puedes utilizarlo con cualquier tipo de proyecto.

Bueno, antes de comenzar debemos entender qué es lo que Ngrok nos provee.

### ¿Qué hace Ngrok?

Según su propio sitio web:

> Ngrok expone servidores locales detrás de NATs y cortafuegos hacia el internet público a través de túneles seguros.

### ¿Cómo funciona?

Mediante el programa que descargas a tu pc, lo que hace Ngrok cuando le indicas el puerto (o host) en donde está
disponible tu servicio web es conectar con el servicio en la nube de Ngrok que acepta el tráfico en una dirección
pública y mapea ese tráfico hacia el proceso corriendo en tu pc, para luego llevarlo hacia la dirección local
que especificaste. 

<img src="/assets/images/posts/0014/ngrok-flow.png" alt="Flujo Ngrok: fuente Ngrok." />

### Configurando Ngrok en nuestra computadora

Este proceso es rápido y solo lo realizarás una vez. Consiste en los siguientes pasos:

1. Registrarnos en Ngrok para obtener nuestro _authtoken_.
2. Descargar el ejecutable de Ngrok.
3. Autenticarnos mediante la consola (con el _authtoken_ que obtuvimos en el `paso 1`)

#### Obteniendo nuestro _authtoken_

Para esto, nos dirigimos hacia la [web de Ngrok](https://dashboard.ngrok.com/signup). Una vez hecho esto y ya habiendo
iniciado sesión, vamos al apartado "auth" y copiamos nuestro token pues lo utilizaremos en el paso #3.

<img src="/assets/images/posts/0014/compartiendo-tu-proyecto-laravel-utilizando-tu-servidor-local-con-ngrok-ss1.png" alt="Paso 1" />

#### Descargando el ejecutable

Descargaremos el programa que nos ayuda a hacer todo esto el cual lo podemos conseguir 
[en su sitio web](https://ngrok.com/download). Está disponible para multiples sistemas operativos, en mi caso, 
la versión para Windows de 64-bits.

Una vez descargado, lo descomprimimos en un directorio de fácil acceso. En mi caso, lo coloqué en la carpeta `ngrok`
que creé en la raíz de mi disco C. Solo contiene un archivo, el ejecutable: `ngrok.exe`

<img src="/assets/images/posts/0014/compartiendo-tu-proyecto-laravel-utilizando-tu-servidor-local-con-ngrok-ss2.png" alt="Paso 2" />

#### Identificándonos mediante el token

Ahora, abriremos la consola, navegamos hacia la carpeta donde tenemos alojado nuestro ejecutable y escribimos:

    ngrok authtoken <el-token-que-copiaste-anteriormente>

<img src="/assets/images/posts/0014/compartiendo-tu-proyecto-laravel-utilizando-tu-servidor-local-con-ngrok-ss3.png" alt="Paso 3" />

Listo.

### Uso en el día a día

Ahora ya podemos crear túneles para exponer nuestros proyectos y compartirlos con las personas que deseemos sin necesidad
de desplegar un servidor en la nube. Para esto, utilizaremos el comando `ngrok http <puerto/virtual-host>`.

Por ejemplo, si levantamos nuestro proyecto Laravel local con el comando `php artisan serve`, ya sabemos que por lo general
lo levanta en el puerto `8000` por lo que haríamos:

    ngrok http 8000

Una vez hecho esto, notarás que Ngrok te brinda unas direcciones las cuales podemos compartir con quienes queramos.

En mi caso, dado que estoy probando mis cambios en caliente tra haber hecho `npm run watch` (o mejor aún: `yarn run watch`)
lo quiero mapear hacia el puerto `3000`, por tanto:

    ngrok http 3000

Y puedes ver que las direcciones que me brinda Ngrok son:

<img src="/assets/images/posts/0014/compartiendo-tu-proyecto-laravel-utilizando-tu-servidor-local-con-ngrok-ss4.png" alt="Ngrok - direcciones brindadas" />

Por lo que si voy al navegador, copio y pego la dirección brindada. Esto nos retornará nuestro, esta vez siendo
accedido públicamente:

> **Tip**: Si quieres copiar desde la consola de Windows puedes hacerlo haciendo clic derecho. Si es una consola distinta,
> prueba <kbd>CTRL</kbd>+<kbd>V</kbd>.

<img src="/assets/images/posts/0014/compartiendo-tu-proyecto-laravel-utilizando-tu-servidor-local-con-ngrok-ss5.png" alt="Ngrok - Resultado final" />

**Como nota adicional**: puedes ver todas las opciones de configuración ejecutando `ngrok --help` o `ngrok` a secas.
Para más información puedes revisar la documentación de [Ngrok](https://ngrok.com/docs).

------

# Cierre

Como ves, es muy fácil de utilizar y nos puede salvar en más de un apuro. Además, habrás notado que nos brinda dos 
direcciones: una HTTP y otra HTTPS. Esta segunda puede ser de utilidad cuando queramos utilizar algún servicio externo 
que nos exija utilizar una conexión segura. 
