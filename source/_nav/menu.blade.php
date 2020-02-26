<nav class="hidden lg:flex items-center justify-end text-lg">
  <a title="{{ $page->siteName }} Blog" href="/blog"
     class="ml-6 text-gray-700 hover:text-purple-600 {{ $page->isActive('/blog') ? 'active text-purple-600' : '' }}">
    Blog
  </a>

  <div class="relative group flex items-center">
    <span class="ml-6 font-semibold cursor-pointer text-gray-700 hover:text-purple-600 {{ $page->isActive('/secciones') ? 'active text-purple-600' : '' }}">
      <span class="hover-group:active hover-group:text-purple-600">Secciones</span>
    </span>

    <div class="hidden group-hover:block absolute top-0 right-0 font-semibold cursor-pointer text-gray-700">
      <div class="bg-white rounded-lg py-2 mt-10 border border-b flex flex-col items-end w-32 shadow">
        @foreach($page->sections as $section)
          <a href={{"/blog/secciones/{$section}"}}>
            <div class="hover:bg-purple-3 text-black hover:bg-purple-200 hover:text-purple-700 self-grow w-32 px-4 py-1 flex justify-end {{ $page->isActive('/' . $section) ? 'active text-purple-900 bg-purple-300' : '' }}">
              {{ ucfirst($section) }}
            </div>
          </a>
        @endforeach
      </div>
    </div>
  </div>

  <a title="{{ $page->siteName }} About" href="/acerca-de-mi"
     class="ml-6 text-gray-700 hover:text-purple-600 {{ $page->isActive('/acerca-de-mi') ? 'active text-purple-600' : '' }}">
    Acerca de mí
  </a>
</nav>
