<div class="flex flex-col mb-4">
  <div class="w-full mb-6">
    @if ($post->cover_image)
      <a
          href="{{ $post->getUrl() }}"
          title="Leer: {{ $post->title }}"
      >
        <img src="{{ $post->cover_image }}" alt="{{ $post->title }} cover image" class="mb-6 no-draggable">
      </a>
    @endif

    <h2 class="text-3xl mt-0">
      <a
          href="{{ $post->getUrl() }}"
          title="Read more - {{ $post->title }}"
          class="text-purple-700 font-extrabold"
      >{{ $post->title }}</a>
    </h2>

    <p class="mb-4 mt-0">{!! $post->getExcerpt(200) !!}</p>

    <a
        href="{{ $post->getUrl() }}"
        title="Read more - {{ $post->title }}"
        class="uppercase font-semibold tracking-wide mb-2"
    >Leer más</a>
  </div>
</div>
