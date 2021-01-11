<nav id="js-nav-menu" class="nav-menu hidden fixed top-0 left-0 right-0 mt-20 lg:hidden">
    <ul class="list-reset my-0">
        <li class="pl-4 list-none">
            <a
                title="<?php echo e($page->siteName); ?> Blog"
                href="/blog"
                class="nav-menu__item hover:text-purple-500 <?php echo e($page->isActive('/blog') ? 'active text-purple' : ''); ?>"
            >Todos los artículos</a>

            <span
                class="nav-menu__item font-semibold hover:text-purple-500"
            >Secciones</span>

            <?php $__currentLoopData = $page->sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <a href=<?php echo e("/blog/secciones/{$section}"); ?>>
                <div class="nav-menu__item pl-4 mb-3 flex flex-col w-full <?php echo e($page->isActive('/' . $section) ? 'active text-purple-900 bg-purple-200' : ''); ?>">
                  - <?php echo e(ucfirst($section)); ?>

                </div>
              </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </li>
        <li class="pl-4 list-none">
            <a
                title="<?php echo e($page->siteName); ?> About"
                href="/acerca-de-mi"
                class="nav-menu__item hover:text-purple-500 <?php echo e($page->isActive('/acerca-de-mi') ? 'active text-purple' : ''); ?>"
            >Acerca de Mí</a>
        </li>
    </ul>
</nav>
<?php /**PATH C:\HCK\Repositories\kennyhorna.com-legacy\source/_nav/menu-responsive.blade.php ENDPATH**/ ?>