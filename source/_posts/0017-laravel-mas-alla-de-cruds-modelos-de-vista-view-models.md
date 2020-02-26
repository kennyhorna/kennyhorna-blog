---
extends: _layouts.post
section: content
title: "Más allá de CRUDs: [08] Modelos de vista (View models)"
date: 2020-02-09
description: "En este capítulo, profundizaremos en la capa de aplicación. Una tendencia importante en toda la serie es mantener el código limpio, conciso y manejable. Este capítulo no será diferente, ya que veremos cómo mantener los controladores limpios y al punto."  
cover_image: /assets/images/posts/0017/0017-laravel-mas-alla-de-cruds-modelos-de-vista-view-models.png
featured: false
reference: https://stitcher.io/blog/laravel-beyond-crud-08-view-models
categories: [laravel, php, programming]
---

En este capítulo, profundizaremos en la capa de aplicación. Una tendencia importante en toda la serie es mantener el código limpio, conciso y manejable. Este capítulo no será diferente, ya que veremos cómo mantener los controladores limpios y al punto.

-------

Este es el artículo #08 de la serie [Laravel: Más allá de CRUDs](/blog/laravel-mas-alla-de-cruds). Fue originalmente publicado por [Brent](https://mobile.twitter.com/brendt_gd) en su [blog](https://stitcher.io/blog/laravel-beyond-crud-08-view-models) (puedes encontrar ahí la serie en su idioma original).

La tabla de contenido que conforma esta serie la [tienes aquí](/blog/laravel-mas-alla-de-cruds).

Dicho esto, continuemos 😉.

-------

### Modelos vista

El patrón que usaremos para ayudarnos se llama patrón de **modelo de vista**. Como su nombre lo indica, estas clases son modelos para tus archivos de vista: son responsables de proporcionar datos a una vista, que de otro modo vendrían directamente del controlador o del modelo de dominio. Permiten una mejor separación de preocupaciones y proporcionan más flexibilidad para el desarrollador.

En esencia, los modelos de vista _son clases simples que toman algunos datos y los transforman en algo utilizable para la vista_. En este capítulo, te mostraré los principios básicos del patrón, veremos cómo se integran en los proyectos de Laravel y, finalmente, te mostraré cómo usamos el patrón en uno de nuestros proyectos.

Empecemos.

Digamos que tienes un formulario para crear un artículo para un blog que debe tener una categoría. Necesitarás una forma de llenar el cuadro de selección con las opciones para la categoría en la vista. El controlador tiene que proporcionar esos.

    public function create()
    {
        return view('blog.form', [
            'categories' => Category::all(), // <-
        ]);
    }

El ejemplo anterior funciona para el método de creación, pero no olvidemos que también deberíamos poder editar las publicaciones existentes.

    public function edit(Post $post)
    {
        return view('blog.form', [
            'post' => $post,
            'categories' => Category::all(),
        ]);
    }

A continuación, hay un nuevo requisito comercial: las categorías en las que los usuarios pueden publicar deben estar restringidas. En otras palabras: la selección de categoría debe restringirse en función del usuario.

    return view('blog.form', [
        'categories' => Category::allowedForUser(
            current_user()
        )->get(),
    ]);

Este enfoque no escala. Tendrás que cambiar el código tanto en el método de creación como en el de edición. ¿Te imaginas lo que sucede cuando necesitas agregar etiquetas a una publicación? ¿O si hay existiera otro formulario especial para administradores para crear y editar publicaciones?

La siguiente solución es hacer que el modelo de publicación proporcione las categorías, de esta manera:

    class Post extends Model
    {
        public static function allowedCategories(): Collection 
        {
            return Category::query()
                ->allowedForUser(current_user())
                ->get();
        }
    }
    
Existen numerosas razones por las cuales esta es una mala idea, aunque sucede a menudo en los proyectos de Laravel. Centrémonos en el problema más relevante para nuestro caso: **todavía permite la duplicación**.

Digamos que hay un nuevo modelo de Noticias (`New`s) que también necesita la misma selección de categorías. Esto nuevamente causa duplicación, pero en el nivel del modelo en lugar de en los controladores.

Otra opción es poner el método en el modelo de Usuario. Esto tiene más sentido, pero también dificulta el mantenimiento. Imagine que estamos usando etiquetas como mencioné anteriormente. Estos no dependen del usuario. Ahora necesitamos obtener las categorías del modelo de usuario y las etiquetas de otro lugar.

Espero que tengas claro que el usar modelos como proveedores de datos para las vistas tampoco es la solución mágica.

En resumen, donde sea que intentes obtener las categorías, siempre parece haber alguna duplicación de código. Esto hace que sea más difícil de mantener y razonar sobre el código.

Aquí es donde entran en juego los **modelos de vista**. Encapsulan toda esta lógica para que pueda reutilizarse en diferentes lugares. Tienen una responsabilidad y una sola responsabilidad: **proporcionar a la vista los datos correctos**.

    class PostFormViewModel
    {
        public function __construct(User $user, Post $post = null) 
        {
            $this->user = $user;
            $this->post = $post;
        }
        
        public function post(): Post
        {
            return $this->post ?? new Post();
        }
        
        public function categories(): Collection
        {
            return Category::allowedForUser($this->user)->get();
        }
    }
    
Vamos a nombrar algunas características importantes de esta clase:

- Se inyectan todas las dependencias: esto le da la mayor flexibilidad al contexto externo.
- El modelo de vista expone algunos métodos que puede utilizar la vista.
- Habrá una publicación nueva o existente proporcionada por el método de publicación, dependiendo de si está creando o editando una publicación.

Así es como se ve el controlador:

    class PostsController
    {
        public function create()
        {
            $viewModel = new PostFormViewModel(
                current_user()
            );
            
            return view('blog.form', compact('viewModel'));
        }
        
        public function edit(Post $post)
        {
            $viewModel = new PostFormViewModel(
                current_user(), 
                $post
            );
        
            return view('blog.form', compact('viewModel'));
        }
    }

Y finalmente, se puede usar en la vista así:

```html
<input value="{{ $viewModel->post()->title }}" />
<input value="{{ $viewModel->post()->body }}" />

<select>
    @foreach ($viewModel->categories() as $category)
        <option value="{{ $category->id }}">
            {{ $category->name }}
        </option>
    @endforeach
</select>
```

### Modelos de vista en Laravel

El ejemplo anterior mostró una clase simple con algunos métodos como nuestro modelo de vista. Esto es suficiente para usar el patrón, pero dentro de los proyectos de Laravel hay algunas sutilezas más que podemos agregar.

Por ejemplo, puede pasar un modelo de vista directamente a la función de vista si el modelo de vista implementa `Arrayable`.

    public function create()
    {
        $viewModel = new PostFormViewModel(
            current_user()
        );
        
        return view('blog.form', $viewModel);
    }
    
La vista ahora puede usar directamente las propiedades del modelo de vista como `$post` y `$categories`. El ejemplo anterior ahora se vería así:

```html
<input value="{{ $post->title }}" />
<input value="{{ $post->body }}" />

<select>
    @foreach ($categories as $category)
        <option value="{{ $category->id }}">
            {{ $category->name }}
        </option>
    @endforeach
</select>
```

También puede devolver el modelo de vista en sí como datos JSON, implementando `Responsable`. Esto puede ser útil cuando está guardando el formulario a través de una llamada AJAX y desea repoblarlo con datos actualizados una vez que se realiza la llamada.

    public function update(Request $request, Post $post)
    {
        // Actualiza el artículo..
    
        return new PostFormViewModel(
            current_user(),
            $post
        );
    }
    
Es posible que veas una similitud entre los modelos de vista y los recursos de Laravel ([API Resources](https://laravel.com/docs/eloquent-resources)). Recuerde que los recursos se asignan uno a uno en un modelo, mientras que los modelos de vista pueden proporcionar los datos que desees.

En nuestros proyectos, en realidad estamos utilizando Recursos y Modelos de vista combinados:

    class PostViewModel
    {
        // …
        
        public function values(): array
        {
            return PostResource::make(
                $this->post ?? new Post()
            )->resolve();
        }
    }

Finalmente, en este proyecto estamos trabajando con componentes de formularios Vue, que requieren datos JSON. Hemos realizado una abstracción que proporciona estos datos JSON en lugar de objetos o matrices, al llamar al mágico getter:

    abstract class ViewModel
    {
        // …
        
        public function __get($name): ?string
        {
            $name = Str::camel($name);
        
            // Algunas validaciones…
        
            $values = $this->{$name}();
        
            if (! is_string($values)) {
                return json_encode($values);
            }
        
            return $values;
        }
    }

En lugar de llamar a los métodos del modelo de vista, podemos llamar a sus propiedades y recuperar JSON.

```html
<select-field
    label="{{ __('Post category') }}"
    name="post_category_id"
    :options="{{ $postViewModel->post_categories }}"
></select-field>
```

### Un momento, ¿Qué hay acerca de los compositores de vistas?

Quizás pienses que hay cierta superposición con los **compositores de vistas** de Laravel ([View Composers](https://laravel.com/docs/views#view-composers)), pero no te confundas. [La documentación](https://laravel.com/docs/views#view-composers) de Laravel explica los compositores de vistas de esta manera:

> Los compositores de vistas son devoluciones de llamada o métodos de clase que se invocan cuando se representa una vista. Si tienes datos que deseas vincular a una vista cada vez que se representa esa vista, un compositor de vistas puede ayudarte a organizar esa lógica en una única ubicación.

Los compositores de vista se registran así (este ejemplo está en la documentación de Laravel):

    class ViewComposerServiceProvider extends ServiceProvider
    {
        public function boot()
        {
            View::composer(
                'profile', ProfileComposer::class
            );
    
            View::composer('dashboard', function ($view) {
                // …
            });
        }
        
        // …
    }

Como puedes ver, puedes usar una clase y un ``closure`` que puedes usar para agregar variables a una vista.

Así es como se usan los compositores de vistas en los controladores.

    class ProfileController
    {
        public function index()
        {
            return view('profile');
        }
    }
    
¿Puedes verlos? <u>No, por supuesto que no</u>: los compositores de vistas están registrados en algún lugar del estado global, y tú no sabe qué variables están disponibles para la vista, sin ese conocimiento implícito.

Ahora sé que esto no es un problema en proyectos pequeños. Cuando eres el único desarrollador y tienes 20 controladores y quizás 20 compositores de vista, todo te quedará en la cabeza.

Pero, ¿qué pasa con el tipo de proyectos sobre los que estamos escribiendo en esta serie? Cuando trabajas con varios desarrolladores, en una base de código que cuenta miles y miles de líneas de código. Ya no cabe en tu cabeza, no en esa escala; y mucho menos tus colegas también tienen el mismo conocimiento. Es por eso que el patrón de modelo de vista es el enfoque preferido. El controlador deja en claro qué variables están disponibles para la vista. Además de eso, puede reutilizar el mismo modelo de vista para múltiples contextos.

Un último beneficio -uno en el que quizás no haya pensado- es que podemos pasar datos al modelo de vista explícitamente. Si deseas utilizar un argumento de ruta o un modelo vinculado para determinar los datos pasados ​​a la vista, se hace explícitamente.


### Conclusión

**Administrar el estado global es un problema en aplicaciones grandes**, especialmente cuando trabajas con múltiples desarrolladores en el mismo proyecto. Recuerda también que solo porque dos medios tienen el mismo resultado final, ¡no significa que sean iguales!
