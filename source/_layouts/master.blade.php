<!DOCTYPE html>
<html lang="es" xml:lang="es" xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <meta name="description" content="{{ $page->meta_description ?? $page->siteDescription }}">

  <meta property="og:title" content="{{ $page->title ?  $page->title . ' | ' : '' }}{{ $page->siteName }}"/>
  <meta property="og:type" content="website"/>
  <meta property="og:url" content="{{ $page->getUrl() }}"/>
  <meta property="og:description" content="{{ $page->siteDescription }}"/>

  <title>{{ $page->siteName }}{{ $page->title ? ' | ' . $page->title : '' }}</title>

  <link rel="home" href="{{ $page->baseUrl }}">
  <link rel="icon" href="/favicon.png">
  <link href="/blog/feed.atom" type="application/atom+xml" rel="alternate" title="{{ $page->siteName }} Atom Feed">

@stack('meta')

@if ($page->production)
  <!-- Insert analytics code here -->
  @endif

  <link href="https://fonts.googleapis.com/css?family=Nunito+Sans:300,300i,400,400i,700,700i,800,800i" rel="stylesheet">
  <link rel="stylesheet" href="{{ mix('css/main.css', 'assets/build') }}">
</head>

<body>
<div id="vue-app" class="flex flex-col justify-between min-h-screen text-gray-800 leading-normal font-sans bg-gray-100">
  <header class="flex items-center shadow bg-white border-b h-20 py-4 sticky top-0 z-50" role="banner">
    <div class="container flex items-center max-w-8xl mx-auto px-4 lg:px-8">
      <div class="flex items-center">
        <a href="/" title="{{ $page->siteName }} home" class="inline-flex items-center">
          <h1 class="text-lg md:text-2xl text-purple-800 font-semibold hover:text-purple-600 my-0">{{ $page->siteName }}</h1>
        </a>
      </div>

      <div class="flex flex-1 justify-end items-center">
        <search></search>

        @include('_nav.menu')

        @include('_nav.menu-toggle')
      </div>
    </div>
  </header>

  @include('_nav.menu-responsive')

  <main role="main" class="flex-auto w-full container max-w-6xl mx-auto pt-4 pb-8 px-6">
    @yield('body')
  </main>

  <footer class="bg-white text-center text-sm mt-12 py-4" role="contentinfo">
    <ul class="flex flex-col md:flex-row justify-center list-reset">
      <li class="md:mr-2">
        &copy; Kenny Horna {{ date('Y') }}. Construído con <a href="http://jigsaw.tighten.co/">Jigsaw</a> y <a
            href="https://tailwindcss.com/">TailwindCSS</a>.
      </li>
    </ul>
  </footer>


</div>
<script src="{{ mix('js/main.js', 'assets/build') }}"></script>
@stack('scripts')
</body>
</html>
