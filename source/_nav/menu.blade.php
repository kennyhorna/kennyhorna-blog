<nav class="hidden lg:flex items-center justify-end text-lg">
    <a title="{{ $page->siteName }} Blog" href="/blog"
        class="ml-6 text-gray-700 hover:text-purple-600 {{ $page->isActive('/blog') ? 'active text-purple-600' : '' }}">
        Blog
    </a>

    <a title="{{ $page->siteName }} About" href="/acerca-de-mi"
        class="ml-6 text-gray-700 hover:text-purple-600 {{ $page->isActive('/acerca-de-mi') ? 'active text-purple-600' : '' }}">
        Acerca de mí
    </a>
</nav>
