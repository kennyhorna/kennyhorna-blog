---
extends: _layouts.post
section: content
title: "Laravel Airlock: autenticación ligera para SPAs Y APIs"
date: 2020-02-19
description: "Junto con el lanzamiento de Laravel 7.X, el equipo de Laravel nos trae también Airlock. Laravel Airlock es una librería que nos permite una autenticación algo más simple y ligera para SPAs, aplicaciones móviles y APIs basadas en tokens. Veamos cómo funciona."  
cover_image: /assets/images/posts/0019/0019-laravel-airlock-autenticacion-ligera-para-spas-y-apis.png
featured: false
categories: [laravel, php, programming, librerias]
---

Junto con el lanzamiento de Laravel 7.X, se lanza Laravel Airlock. Airlock es una librería mantenida por el equipo de Laravel que permite una autenticación algo más simple y ligera para SPAs, aplicaciones móviles y APIs basadas en tokens. Veamos cómo funciona.

------

Este artículo pertenece a la serie [Novedades de Laravel 7](/blog/0021-novedades-de-laravel-7). Puedes leer sobre el resto de sus novedades [aquí](/blog/0021-novedades-de-laravel-7).

-----

## Fundamentos

### ¿Qué es en sí?

Según la propia documentación en su sección en Laravel, nos dice:

> **Laravel Airlock** proporciona un sistema de autenticación ligero para SPA (aplicaciones de una sola página), aplicaciones móviles y APIs simples basadas en tokens. Airlock permite a los usuario de tu aplicación generar múltiples tokens API para su cuenta. A estos tokens <u>se les pueden otorgar habilidades/alcances</u> que especifican qué acciones se les permite realizar.

### ¿Qué ofrece y como se diferencia de Passport?

[Tal como menciona en su documentación](https://laravel.com/docs/master/airlock), Laravel Airlock brinda soluciones para dos escenarios. Por un lado, te facilita la generación y emisión de API tokens y, por otro lado, te ofrece una simple manera de autenticar SPAs que necesitan comunicarse con una API hecha en Laravel. Veamos un poco de detalle de cada uno. Algo importante resaltar es que tranquilamente puedes optar por usar solo uno de estos dos modos, no estás obligado a implementar ambos.

#### API Tokens

Airlock te brinda [un modo](https://laravel.com/docs/master/airlock#api-token-authentication) de generar _API tokens_ que puedes usar sin la complejidad de implementar un servidor Oauth2 completo, como sí lo hace Passport. Si es que no necesitas los modos de concesión de tokens de _Credenciales del Cliente_ para -autenticación máquina-a-máquina- ni el _Código de Autorización_,  entonces Laravel Airlock se convierte en una interesante alternativa.

#### Autenticación para SPAs

Para [este segundo modo](https://laravel.com/docs/master/airlock#spa-authentication) de autenticación Airlock no utiliza ningún tipo de token. En lugar de esto, utiliza los servicios incorporados de Laravel para la autenticación basada en ``cookies``. Esto provee muchos beneficios (CSRF, etc). De este modo, Airlock solo intentará autenticar usando cookies cuando estos vienen de requests originados del propio front-end de tu SPA.

Pero basta de palabrería. Pasemos a utilizarlo.

## Manos a la obra

### Instalación

Puedes instalar Airlock mediante Composer haciendo:

    composer require laravel/airlock
    
Luego necesitas publicar la configuración de Airlock. Este archivo se copiará en tu directorio `/config`:

    php artisan vendor:publish --provider="Laravel\Airlock\AirlockServiceProvider"

No olvides de correr las migraciones. Airlock necesita crear una tabla en la cual almacena los tokens.

    php artisan migrate
    
Por último, si el uso que le vas a dar Airlock es para una SPA, deberás de añadir el middleware de Airlock en el grupo ``api`` que se encuentra dentro ``app/Http/Kernel.php``:

    use Laravel\Airlock\Http\Middleware\EnsureFrontendRequestsAreStateful;
    
    'api' => [
        EnsureFrontendRequestsAreStateful::class, // <--
        'throttle:60,1',
        \Illuminate\Routing\Middleware\SubstituteBindings::class,
    ],
    
## Uso en el día a día

>  Para no replicar por completo la documentación, en este artículo nos vamos a centrar en autenticaciones basadas en tokens. Pero descuida, al final del post enlazaré con la sección de la documentación referente a SPAs.

Como se mencionó al inicio, puedes utilizar Airlock para autenticar mediante tokens (API) así como también mediante
_cookies_ de sesión, útiles para SPAs. Veamos un ejemplo del primer caso.

### Generando tokens para usar en APIs

Para poder generar tokens, vas a necesitar añadir el trait ``HasApiTokens`` en tu modelo "autentificable", que usualmente es el ``User``:

    use Laravel\Airlock\HasApiTokens;
    
    class User extends Authenticatable
    {
        use HasApiTokens, Notifiable;
    }   //  ^^^^^^^^^^^^

Entonces, tan solo tendremos que hacer lo siguiente para generar un _token_ para un usuario en específico:

    $user = User::find(1); // Obteniendo al usuario
    $token = $user->createToken('nombre-del-token');
    //              ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
    // Para finalmente retornar el token en sí:
    return $token->plainTextToken;
    
Dado que un usuario puede generar varios _tokens_, vamos a poder a acceder a todos ellos haciendo:

    foreach ($user->tokens as $token)
    {
        // Haz lo que quieres aquí
    }

### Habilidades/alcances

Airlock te permite poder asignar [ciertas habilidades o limitaciones](https://laravel.com/docs/master/airlock#token-abilities) a los _tokens_ que generes. Por ejemplo, si quisiéramos generar un _token_ para el usuario que solo le permita actualizar productos, podríamos hacer algo así:

    return $user->createToken('nombre-del-token', ['product:update'])->plainTextToken; 

Como puedes apreciar, el alcance puede ser especificado pasando un segundo parámetro al método ``createToken``.

Del mismo modo, si lo que queremos es consultar si un usuario puede realizar una acción en específico, podemos hacerlo mediante el método ``tokenCan()``:

    if ($user->tokenCan('product:update')) {
        //
    }

### Autenticación para aplicaciones Móviles

El [modo de autenticar apps móviles](https://laravel.com/docs/master/airlock#mobile-application-authentication) es similar al de APIs de terceros, sin embargo hay algunas particularidades que puedes emplear en el modo en el que generas _tokens_.

#### Generando tokens

Para esto, podemos crear una ruta que acepte el email, contraseña y nombre del dispositivo, luego de esto intercambiaremos estas credenciales por un _token_ de Airlock. Este _token_ será almacenado por el dispositivo para que pueda utilizarlo y realizar más peticiones autenticadas al servidor. Veamos un ejemplo (tomado de la documentación):

    use App\User;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Hash;
    use Illuminate\Validation\ValidationException;
    
    Route::post('/airlock/token', function (Request $request) {
        // Validamos los valores a recibir
        $request->validate([
            'email'       => 'required|email',
            'password'    => 'required',
            'device_name' => 'required'
        ]);
        
        // Obtenemos al usuario a autenticar
        $user = User::where('email', $request->email)->first();
        
        // Vemos si las credenciales son erróneas para retornar un mensaje de error    
        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales utilizadas son erróneas.'],
            ]);
        }
    
        // Por último, retornamos el token utilizando como nombre el del dispositivo
        return $user->createToken($request->device_name)->plainTextToken;
    });

Como nota adicional, cuando el dispositivo móvil haga solicitudes utilizando este _token_, deberá pasarlo mediante una cabecera de autenticación como un token ``Bearer``:

    Authorization: Bearer {el-token-recibido}

> Como habrás podido notar, todo el código está definido dentro de un _Closure_ en la misma definición de la ruta. Evidentemente, se aconseja extraer esto a un controlador pero para motivos del artículo y facilidad de uso se puso de este modo.

### Protección de rutas

Airlock incluye middlewares para añadir [protecciones a tus rutas](https://laravel.com/docs/master/airlock#protecting-mobile-api-routes). Podemos solicitar peticiones autenticadas simplemente haciendo lo siguiente:

    Route::middleware('auth:airlock')->get('/users', 'UsersController@index');
    //     ^^^^^^^^^^^^^^^^^^^^^^^^^

### Testing

Por último, algo muy útil para los que realizan pruebas en código es que, al igual que muchas herramientas de Laravel, Airlock provee una forma fácil para emular la autenticación en para cuando escribamos pruebas:

    use App\User;
    use Laravel\Airlock\Airlock;
    
    public function test_a_user_can_see_his_purchases()
    {
        Airlock::actingAs(
            factory(User::class)->create(),
            ['list-purchases']
        );
    
        $response = $this->get('/api/purchases');
    
        $response->assertOk();
    }
    
Puedes leer más sobre esto [aquí](https://laravel.com/docs/master/airlock#testing).

--------

# Cierre

Como podemos ver, Airlock nos ofrece una alternativa fácil de implementar que puede ser de utilidad en muchos escenarios. 

Si te interesó esta librería, no olvides leer el resto de novedades que trae Laravel 7 ([aquí](/blog/0021-novedades-de-laravel-7)), ya que tenemos [una serie de artículos -"Novedades de Laravel 7"-](/blog/0021-novedades-de-laravel-7) dedicados a este tema. 
