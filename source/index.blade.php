@extends('_layouts.master')

@push('meta')
  <meta property="og:locale" content="es_ES"/>
@endpush

@section('body')
  @foreach ($posts->where('featured', true) as $featuredPost)
    <div class="w-full mb-6 px-3 md:px-0">
      @if ($featuredPost->cover_image)
        <a href="{{ $featuredPost->getUrl() }}" title="Leer: {{ $featuredPost->title }}">
          <img src="{{ $featuredPost->cover_image }}" alt="{{ $featuredPost->title }} imagen de portada"
               class="mb-6 no-draggable">
        </a>
      @endif

      <div class="flex flex-row my-2">
        <span
            class="inline-block bg-gray-300 hover:bg-purple-200 leading-loose tracking-wide
            text-gray-800 uppercase text-xs font-semibold rounded mr-4 px-2 pt-px cursor-default"
        >
        {{ $featuredPost->reading_time->abbreviated }}
      </span>

        <p class="text-gray-700 font-medium text-sm my-0">
          {{ strftime("%d de %B, %Y", $featuredPost->getDate()->getTimestamp()) }}
        </p>
      </div>

      <h2 class="text-3xl mt-0">
        <a href="{{ $featuredPost->getUrl() }}" title="Read {{ $featuredPost->title }}"
           class="text-purple-700 font-extrabold">
          {{ $featuredPost->title }}
        </a>
      </h2>

      <p class="mt-0 mb-4">{!! $featuredPost->description !!}</p>

      <a href="{{ $featuredPost->getUrl() }}" title="Leer: - {{ $featuredPost->title }}"
         class="uppercase tracking-wide mb-4">
        Leer más
      </a>
    </div>

    @if (! $loop->last)
      <hr class="border-b my-6">
    @endif
  @endforeach

{{--  @include('_components.newsletter-signup')--}}

  @foreach ($posts->where('featured', false)->take(6)->chunk(2) as $row)
    <div class="flex flex-col md:flex-row md:-mx-6">
      @foreach ($row as $post)
        <div class="w-full md:w-1/2 md:mx-6">
          @include('_components.post-preview-inline')
        </div>

        @if (! $loop->last)
          <hr class="block md:hidden w-full border-b mt-2 mb-6">
        @endif
      @endforeach
    </div>

    @if (! $loop->last)
      <hr class="w-full border-b mt-2 mb-6">
    @endif
  @endforeach
@stop
