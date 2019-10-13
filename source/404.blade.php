@extends('_layouts.master')

@section('body')
  <div class="flex flex-col items-center text-gray-700 mt-4">
    <img src="/assets/images/main/404.png"
         alt="Kenny Horna"
         class="flex w-auto md:w-1/2 bg-contain mx-auto mb-2"
    >

    <h2 class="text-2xl md:text-3xl">Página no encontrada :(</h2>

    <hr class="block w-full max-w-sm mx-auto border my-4">

    <p class="text-xl">
      Volver al <a title="Inicio" href="/">Inicio</a>.
    </p>
  </div>
@endsection
