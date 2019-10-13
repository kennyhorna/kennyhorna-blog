@extends('_layouts.master')

@push('meta')
    <meta property="og:title" content="{{ $page->title }}" />
    <meta property="og:type" content="article" />
    <meta property="og:url" content="{{ $page->getUrl() }}"/>
    <meta property="og:description" content="{{ $page->description }}" />
@endpush

@section('body')
    <div class="w-full flex md:flex-row scrollbar">
      <div class="w-full lg:w-4/5 bg-white rounded-lg border shadow">
        @if ($page->cover_image)
          <img src="{{ $page->cover_image }}" alt="{{ $page->title }} cover image" class="mb-2 w-full rounded-t-lg">
        @endif

        <div class="w-full p-6 md:p-12 md:pb-6">
          <h1 class="leading-none mb-2 text-4xl text-center md:text-left md:text-5xl">{{ $page->title }}</h1>

          <p class="text-gray-700 text-md md:mt-0">{{ $page->author }}  •  <?php setlocale(LC_ALL, 'es_ES'); echo strftime("%e de %B del %Y", $page->getDate()->getTimestamp()); ?></p>
          @if ($page->categories)
            @foreach ($page->categories as $i => $category)
              <a
                  href="{{ '/blog/secciones/' . $category }}"
                  title="View posts in {{ $category }}"
                  class="inline-block bg-gray-300 hover:bg-purple-200 leading-loose tracking-wide text-gray-800 uppercase text-xs font-semibold rounded mr-4 px-3 pt-px"
              >{{ $category }}</a>
            @endforeach
          @endif

          <div class="border-b border-purple-200 mb-10 pb-4 anchor-tags" v-pre>
            @yield('content')
          </div>

          <div id="commento"></div>
        </div>
      </div>
      <navigation-on-page :headings="pageHeadings"></navigation-on-page>
    </div>
    <nav class="flex justify-between text-sm md:text-base">
        <div>
            @if ($next = $page->getNext())
                <a href="{{ $next->getUrl() }}" title="Older Post: {{ $next->title }}">
                    &LeftArrow; {{ $next->title }}
                </a>
            @endif
        </div>

        <div>
            @if ($previous = $page->getPrevious())
                <a href="{{ $previous->getUrl() }}" title="Newer Post: {{ $previous->title }}">
                    {{ $previous->title }} &RightArrow;
                </a>
            @endif
        </div>
    </nav>
@endsection

@push('scripts')
  <script src="https://cdn.commento.io/js/commento.js"></script>
@endpush
