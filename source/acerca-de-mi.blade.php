@extends('_layouts.master')

@push('meta')
    <meta property="og:title" content="About {{ $page->siteName }}" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ $page->getUrl() }}"/>
    <meta property="og:description" content="A little bit about {{ $page->siteName }}" />
@endpush

@section('body')
    <div class="container w-full flex items-center justify-center">
      <div class="max-w-4xl pt-10">
        <h1>Acerca de mí</h1>

        <img src="/assets/images/main/about-me.jpg"
             alt="Kenny Horna"
             class="flex rounded-full h-64 w-64 bg-contain mx-auto md:float-right my-6 md:ml-10 no-draggable">

        <p class="mb-6">¡Hola! Mi nombre es Kenny Horna, Ingeniero de Aplicaciones y Diseñador de Experiencia de Usuario.</p>

        <p class="mb-6">He trabajado en muchos proyectos de software desde la concepción, estrategia, organización y validación, hasta la implementación de las soluciones software para productos y servicios digitales en rubros tales como E-Commerce, Energía, Internet of Things, entre otros.</p>

        <p class="mb-6">Aquí escribiré sobre lo interesante que me tenga atrapado de momento, principalmente desarrollo de software, así como también del día a día y muchos otros temas más.</p>

      </div>
    </div>
@endsection
