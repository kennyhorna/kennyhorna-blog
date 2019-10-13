<nav id="js-nav-menu" class="nav-menu hidden lg:hidden">
    <ul class="list-reset my-0">
        <li class="pl-4">
            <a
                title="{{ $page->siteName }} Blog"
                href="/blog"
                class="nav-menu__item hover:text-purple-500 {{ $page->isActive('/blog') ? 'active text-purple' : '' }}"
            >Blog</a>
        </li>
        <li class="pl-4">
            <a
                title="{{ $page->siteName }} About"
                href="/acerca-de-mi"
                class="nav-menu__item hover:text-purple-500 {{ $page->isActive('/acerca-de-mi') ? 'active text-purple' : '' }}"
            >Acerca de Mí</a>
        </li>
    </ul>
</nav>
