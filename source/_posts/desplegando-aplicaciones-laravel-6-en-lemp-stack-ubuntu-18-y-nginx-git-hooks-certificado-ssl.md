---
extends: _layouts.post
section: content
title: "Desplegando Laravel con LEMP Stack (Linux, Nginx, MySQL, PHP) + Git Hooks + SSL Gratuito"
date: 2019-10-19
description: Siempre es difícil querer desplegar aplicaciones Laravel, sobre todo cuando estamos iniciando. Esta guía pretende orientarte en esta -frecuentemente complicada- tarea.  
cover_image: /assets/images/posts/0002/desplegando-aplicaciones-laravel-6-en-lemp-stack-ubuntu-18-y-nginx.png
featured: false
categories: [despliegue, tutoriales, laravel, php, series]
---

Siempre es un poco difícil querer desplegar aplicaciones Laravel cuando estamos iniciando, es por eso que esta guía pretende orientarte en esta tarea.

Esta guía está basada en la realizada por 
[J.A. Curtis](https://devmarketer.io/learn/deploy-laravel-5-app-lemp-stack-ubuntu-nginx/) hace un par de años,
la cual me ayudó mucho en su momento.

Para facilitar la lectura, he dividido esta guía en tres partes:

- [Parte I: Instalación y configuración de LEMP Stack](/blog/desplegando-aplicaciones-laravel-6-en-lemp-stack-ubuntu-18-y-nginx-git-hooks-certificado-ssl-parte-i/)
- [Parte II: Instalación y configuración de Laravel + Git Hooks](/blog/desplegando-aplicaciones-laravel-6-en-lemp-stack-ubuntu-18-y-nginx-git-hooks-certificado-ssl-parte-ii/)
- [Parte III: Instalación de certificados SSL gratuitos y ajustes finales de Laravel](/blog/desplegando-aplicaciones-laravel-6-en-lemp-stack-ubuntu-18-y-nginx-git-hooks-certificado-ssl-parte-iii/)

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
Está en tus manos investigar sobre sus diferentes bondades/debilidades y escoger el que más te convenga.

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

<img src="/assets/images/posts/0002/desplegando-aplicaciones-laravel-6-en-lemp-stack-ubuntu-18-y-nginx-0-1.png" class="self-center rounded-lg">

## Iniciemos

Con todos estos puntos aclarados, saltemos a la primera parte de esta serie [aquí](/blog/desplegando-aplicaciones-laravel-6-en-lemp-stack-ubuntu-18-y-nginx-git-hooks-certificado-ssl-parte-i/).
