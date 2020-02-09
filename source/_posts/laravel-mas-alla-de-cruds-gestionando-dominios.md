---
extends: _layouts.post
section: content
title: "Más allá de CRUDs: [06] Gestionando dominios"
date: 2019-12-28
description: "En los capítulos anteriores vimos los tres componentes básicos de nuestros dominios: DTO, acciones y modelos. Hoy tomamos un respiro de las cosas técnicas de bajo nivel y nos centramos en el lado filosófico: ¿cómo comienzas a usar dominios, cómo identificarlos y cómo administrarlos a largo plazo?"  
cover_image: /assets/images/posts/0013/mas-alla-de-cruds-06-gestionando-dominios.png
featured: false
reference: https://stitcher.io/blog/laravel-beyond-crud-06-managing-domains
categories: [laravel, php, programming]
---

En los capítulos anteriores vimos los tres componentes básicos de nuestros dominios: DTO, acciones y modelos. 
Hoy tomamos un respiro de las cosas técnicas de bajo nivel y nos centramos en el lado filosófico: ¿cómo comienzas 
a usar dominios, cómo identificarlos y cómo administrarlos a largo plazo?

-----

Este es el artículo #06 originalmente publicado por [Brent](https://mobile.twitter.com/brendt_gd) en su 
[blog](https://stitcher.io/blog/laravel-beyond-crud-06-managing-domains) (puedes encontrar ahí la serie en 
su idioma original).

El índice de artículos que conforman esta serie lo [puedes encontrar aquí](/blog/laravel-mas-alla-de-cruds).

Dicho esto, comencemos 😉.

-------

### Trabajo en equipo

Al inicio de esta serie, afirmé que todos los paradigmas y principios sobre los que escribí servirían para un 
propósito: ayudar a los equipos de desarrolladores a mantener sus aplicaciones de Laravel mantenibles a lo largo 
de los años.

Algunas personas expresaron su preocupación: ¿no sería una nueva estructura de directorios y el uso de principios 
complejos una mayor dificultad para los nuevos desarrolladores que se unen al equipo?

Si eres un desarrollador familiarizado con los proyectos Laravel predeterminados y con la forma en que se les 
enseña a los principiantes, es cierto que necesitarás pasar un tiempo aprendiendo cómo se manejan estos proyectos. 
Sin embargo, esto no es tan importante como algunas personas podrían pensar.

Imagina un proyecto con alrededor de 100 modelos, 300 acciones, casi 500 rutas. Esta es la escala de proyectos en 
los que estoy pensando. La principal dificultad en estos proyectos no es cómo está estructurado técnicamente el 
código, sino más bien la enorme cantidad de conocimiento empresarial que hay que comprender. No puedes esperar que 
los nuevos desarrolladores comprendan todos los problemas que este proyecto está resolviendo, al instante. Lleva 
tiempo conocer el código, pero lo más importante: el negocio. Cuantas menos magia e indirectas haya, menos espacio 
hay para la confusión.

Es importante comprender el objetivo de la arquitectura que estamos desarrollando en esta serie: no se trata de escribir 
la menor cantidad de caracteres, no se trata de la elegancia del código; se trata de hacer que las bases de códigos 
grandes sean más fáciles de navegar, permitir el menor espacio posible para la confusión y mantener el proyecto saludable 
durante largos períodos de tiempo.

En realidad, tenemos experiencia con este proceso en la práctica: después de haber trabajado con un equipo de tres 
desarrolladores en uno de nuestros proyectos, mi colega Ruben se unió como un nuevo desarrollador de back-end.

La arquitectura era nueva para él, incluso teniendo ya experiencia con Laravel. Así que nos tomamos el tiempo para 
guiarlo. Después de solo unas pocas horas de información y programación de pares, ya podía trabajar en este proyecto 
de forma independiente. Definitivamente tomó varias semanas obtener una comprensión profunda del alcance del proyecto, 
pero afortunadamente la arquitectura no se interpuso en su camino, por el contrario: ayudó a Ruben a centrarse en la 
lógica del negocio.

Si llegaste hasta este punto en esta serie de artículos, espero que comprendas que esta arquitectura no está destinada 
a ser la bala de plata para cada proyecto. Hay muchos casos en los que un enfoque más simple podría funcionar mejor, 
y algunos casos en los que se requiere un enfoque más complejo.

### Identificando dominios

Con el conocimiento que tenemos ahora sobre los componentes básicos del dominio, surge la pregunta de cómo exactamente 
comenzamos a escribir código real. Hay muchas metodologías que puede usar para comprender mejor lo que estás a punto 
de construir, aunque creo que hay dos puntos clave:

- Aunque seas un desarrollador, tu objetivo principal es comprender el problema comercial y traducirlo en código. 
El código en sí es simplemente un medio para un fin; siempre mantén tu enfoque en el problema que estás resolviendo.
- Asegúrate de tener tiempo cara-a-cara con tu cliente. Tomará tiempo extraer el conocimiento que necesitas para escribir 
un programa de trabajo. 

Llegué a pensar en mi descripción de trabajo cada vez más como "un traductor entre problemas del mundo real y soluciones 
técnicas", en lugar de "un programador que escribe código". Creo firmemente que esta mentalidad es clave si vas a 
trabajar en un proyecto de larga duración. No solo tienes que escribir el código, debes comprender los problemas del 
mundo real que estás tratando de resolver.

Dependiendo del tamaño de tu equipo, es posible que no necesite interacción cara a cara entre todos los desarrolladores 
y el cliente, pero no obstante, todos los desarrolladores deberán comprender los problemas que están resolviendo con el 
código.

Estas dinámicas de equipo son un tema tan complejo que merecen su propio libro. De hecho, hay mucha literatura específica 
sobre este tema. Por ahora lo mantendré así, porque a partir de ahora podemos hablar sobre cómo traducimos estos problemas 
en dominios.

En el capítulo 1, escribí que uno de los objetivos de esta arquitectura es agrupar el código que pertenece, basado en su 
significado en el mundo real, en lugar de sus propiedades técnicas. Si tiene una comunicación abierta con tu cliente, 
notarás que lleva tiempo, mucho tiempo, tener una buena idea de qué se trata su negocio. A menudo, tu cliente puede no 
saberlo exactamente, y es solo cuando se sientan que comienzan a pensarlo detenidamente.

Es por eso que no debes temer a los grupos de dominio que cambian con el tiempo. Es posible que comiences con un dominio 
de `Invoice`, pero observa medio año después que ha crecido demasiado para que tu equipo y tú lo puedan comprender por 
completo. Tal vez la generación de facturas y los pagos son dos sistemas complejos por sí mismos, por lo que pueden 
dividirse en dos grupos de dominio en el futuro.

Mi punto de vista es que es bueno seguir iterando sobre la estructura de tu dominio, para seguir refactorizándola. Dadas 
las herramientas adecuadas, no es nada difícil cambiar, dividir y refactorizar dominios; ¡Tu IDE es tu amigo! Mi 
colega Freek se tomó el tiempo para grabar un ejemplo práctico en el que refactoriza una aplicación Laravel predeterminada 
a la arquitectura descrita en esta serie. Puedes ver su sesión de refactorización en vivo a continuación.

<div markdown="1">
<iframe width="800" height="450" src="https://www.youtube.com/embed/yPiMzw-lLF8" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
</div>

En resumen: no tengas miedo de comenzar a usar dominios porque siempre puedes refactorizarlos más tarde.

Entonces, ese es el enfoque que tomarías si deseas comenzar a usar esta arquitectura orientada a dominios: intenta 
identificar subsistemas dentro de tu proyecto, dándote cuenta de que pueden cambiar, y lo harán, con el tiempo. Puedes 
sentarte con tu cliente, puedes pedirle que escriba algunas cosas o incluso puedes hacer sesiones de brainstorming de 
eventos con ellos. Juntos forman una imagen de lo que debería ser el proyecto, y esa imagen bien podría ser refinada 
e incluso cambiada en el futuro.

Y debido a que nuestro código de dominio tiene muy pocas dependencias, es muy flexible, no cuesta mucho mover cosas 
o refactorizar el código.
