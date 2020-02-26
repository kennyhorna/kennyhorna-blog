<nav id="js-nav-menu" class="nav-menu hidden fixed top-0 left-0 right-0 mt-20 lg:hidden">
    <ul class="list-reset my-0">
        <li class="pl-4 list-none">
            <a
                title="{{ $page->siteName }} Blog"
                href="/blog"
                class="nav-menu__item hover:text-purple-500 {{ $page->isActive('/blog') ? 'active text-purple' : '' }}"
            >Todos los artículos</a>

            <span
                class="nav-menu__item font-semibold hover:text-purple-500"
            >Secciones</span>

            @foreach($page->sections as $section)
              <a href={{"/blog/secciones/{$section}"}}>
                <div class="nav-menu__item pl-4 mb-3 flex flex-col w-full {{ $page->isActive('/' . $section) ? 'active text-purple-900 bg-purple-200' : '' }}">
                  - {{ ucfirst($section) }}
                </div>
              </a>
            @endforeach
        </li>
        <li class="pl-4 list-none">
            <a
                title="{{ $page->siteName }} About"
                href="/acerca-de-mi"
                class="nav-menu__item hover:text-purple-500 {{ $page->isActive('/acerca-de-mi') ? 'active text-purple' : '' }}"
            >Acerca de Mí</a>
        </li>
    </ul>
</nav>
