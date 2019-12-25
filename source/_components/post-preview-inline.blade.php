<div class="flex flex-col mb-4">
  <div class="w-full mb-6 px-3 md:px-0">
    @if ($post->cover_image)
      <a
          href="{{ $post->getUrl() }}"
          title="Leer: {{ $post->title }}"
      >
        <img src="{{ $post->cover_image }}" alt="{{ $post->title }} cover image" class="mb-6 no-draggable">
      </a>
    @endif

    <h2 class="text-3xl my-0">
      <a
          href="{{ $post->getUrl() }}"
          title="Read more - {{ $post->title }}"
          class="text-purple-700 font-extrabold"
      >{{ $post->title }}</a>
    </h2>

    <span
        class="my-2 inline-block bg-gray-300 hover:bg-purple-200 leading-loose tracking-wide
          text-gray-800 uppercase text-xs font-semibold rounded mr-4 px-2 pt-px cursor-default"
    >
      {{ $post->reading_time->abbreviated }}
    </span>

    <p class="mb-4 mt-0">{!! $post->getExcerpt(200) !!}</p>

    <a
        href="{{ $post->getUrl() }}"
        title="Read more - {{ $post->title }}"
        class="uppercase font-semibold tracking-wide mb-2"
    >Leer más</a>
  </div>
</div>
