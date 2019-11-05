@extends('_layouts.master')

@section('body')
  @foreach ($posts->where('featured', true) as $featuredPost)
    <div class="w-full mb-6">
      @if ($featuredPost->cover_image)
        <a href="{{ $featuredPost->getUrl() }}" title="Leer: {{ $featuredPost->title }}">
          <img src="{{ $featuredPost->cover_image }}" alt="{{ $featuredPost->title }} cover image"
               class="mb-6 no-draggable">
        </a>
      @endif

      <p class="text-gray-700 font-medium my-2 text-sm">
        {{ strftime("%d de %B, %Y", $featuredPost->getDate()->getTimestamp()) }}
      </p>

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

  @include('_components.newsletter-signup')

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
