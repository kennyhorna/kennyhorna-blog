@extends('_layouts.master')

@push('meta')
    <meta property="og:title" content="{{ $page->title }}" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ $page->getUrl() }}"/>
    <meta property="og:description" content="{{ $page->description }}" />
@endpush

@section('body')
    <div class="px-4">
      <h1 class="text-3xl md:5xl">{{ $page->title }}</h1>

      <div class="text-xl md:text-2xl border-b border-purple-200 mb-4 pb-2 md:mb-6 md:pb-10">
        @yield('content')
      </div>
    </div>

    @foreach ($page->posts($posts) as $post)
        @include('_components.post-preview-inline')

        @if (! $loop->last)
            <hr class="w-full border-b mt-2 mb-6">
        @endif
    @endforeach
@stop
