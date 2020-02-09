---
extends: _layouts.post
section: content
title: "Laravel: Más allá de CRUDs"
date: 2019-12-15
description: Una serie de artículos para desarrolladores PHP que trabajan en proyectos Laravel más grandes que el promedio.  
cover_image: /assets/images/posts/0007/mas-alla-de-cruds-00.png
featured: false
reference: https://stitcher.io/blog/laravel-beyond-crud
categories: [laravel, php, programming, series]
---

Esta es una adaptación al español de lo publicado por [Brent](https://mobile.twitter.com/brendt_gd) -de Spatie- en su 
[blog](https://stitcher.io/blog/laravel-beyond-crud) (puedes encontrar ahí la serie en su idioma original).

### Tabla de contenido

Los artículos que conforman esta serie son:

- [[00] Prefacio](#prefacio)
- [[01] Laravel orientado al Dominio](/blog/laravel-mas-alla-de-cruds-laravel-orientado-al-dominio) 
- [[02] Trabajando con data](/blog/laravel-mas-alla-de-cruds-trabajando-con-data)
- [[03] Acciones](/blog/laravel-mas-alla-de-cruds-acciones)
- [[04] Modelos](/blog/laravel-mas-alla-de-cruds-modelos)
- [[05] Modelos con patrón de estado](/blog/laravel-mas-alla-de-cruds-estados)
- [[06] Gestionando Dominios](/blog/laravel-mas-alla-de-cruds-gestionando-dominios)
- [[07] Ingresando a la capa de aplicación](/blog/laravel-mas-alla-de-cruds-ingresando-a-la-capa-de-aplicacion)
- [[08] "Modelos vista" (view models)](/blog/0017-laravel-mas-alla-de-cruds-modelos-de-vista-view-models)
- [09] Fábricas de pruebas
- Se están preparando más capítulos..

La dinámica que sugiere el autor original es la adaptación casi literal de sus publicaciones para luego añadir
al final comentarios, observaciones y/o críticas en caso existieran.

### Prefacio

> Una serie de artículos para desarrolladores PHP que trabajan en proyectos Laravel más grandes que el promedio

He venido escribiendo y manteniendo durante años varias aplicaciones web que por lo general son más grandes que 
las promedio. Estos son proyectos que requieren un equipo de desarrolladores para trabajar en él durante al 
menos un año, a menudo más tiempo. Son proyectos que requieren más que el conocido enfoque "Laravel CRUD" para 
mantenerse mantenibles.

En este tiempo, he analizado varias arquitecturas que nos ayudarían a mí y a nuestro equipo a mejorar la 
capacidad de mantenimiento de estos proyectos, así como a facilitar el desarrollo, tanto para nosotros como 
para nuestros clientes: [DDD](https://github.com/jatubio/5minutos_laravel/wiki/Resumen-sobre-DDD.-Domain-Driven-Design), 
[Arquitectura Hexagonal](https://apiumhub.com/es/tech-blog-barcelona/arquitectura-hexagonal/), 
[Event Sourcing](https://www.adictosaltrabajo.com/2018/06/20/event-sourcing-para-aplicaciones-escalables/).

Debido a que la mayoría de estos proyectos eran grandes, pero no descomunales, estos paradigmas en general 
casi siempre eran excesivos. Además de eso, todavía estábamos lidiando con plazos fijos, lo que significa 
que no podíamos pasar años ajustando la arquitectura.

En general, se trataba de proyectos con una vida útil de desarrollo de seis meses a un año, con un equipo 
de tres a seis desarrolladores trabajando simultáneamente en ellos. Después de su puesta en marcha, la mayoría 
de estos proyectos todavía seguirían siendo mantenidos en años venideros.

En esta serie, escribiré sobre el conocimiento que adquirimos a lo largo de los años al diseñar estos proyectos. 
Examinaré de cerca el camino de Laravel y lo que funcionó y no funcionó para nosotros. Esta serie es para ti si 
estás lidiando con estos grandes proyectos de Laravel y quieres soluciones prácticas y pragmáticas para administrarlos.

Hablaré sobre teoría, patrones y principios, aunque todo estará en el contexto de una aplicación web que funcione 
en la vida real.

El objetivo de esta serie es brindarte soluciones concretas a problemas de la vida real, cosas que puede comenzar a 
hacer de manera diferente en sus proyectos hoy mismo. ¡Espero lo disfrutes!

-----

### Nota personal

He venido siguiendo el trabajo que realizan Brent y el resto del equipo de Spatie, por lo cual me interesa bastante
el modo en el que ellos tratan proyectos medianos/grandes para poder adoptar las buenas prácticas que encuentre.
Estoy seguro que -al igual que yo- le sacarás mucho provecho a esta serie.
