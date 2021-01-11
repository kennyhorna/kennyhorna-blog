<?php $__env->startSection('content'); ?><p>Todos los artículos relacionados con el framework más popular de PHP: <a href="http://laravel.com">Laravel</a>.</p>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('_layouts.category', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>