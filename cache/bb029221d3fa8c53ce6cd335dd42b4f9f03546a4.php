<?php $__env->startPush('meta'); ?>
    <meta property="og:title" content="<?php echo e($page->title); ?>" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="<?php echo e($page->getUrl()); ?>"/>
    <meta property="og:description" content="<?php echo e($page->description); ?>" />
<?php $__env->stopPush(); ?>

<?php $__env->startSection('body'); ?>
    <div class="px-4">
      <h1 class="text-3xl md:5xl"><?php echo e($page->title); ?></h1>

      <div class="text-xl md:text-2xl border-b border-purple-200 mb-4 pb-2 md:mb-6 md:pb-10">
        <?php echo $__env->yieldContent('content'); ?>
      </div>
    </div>

    <?php $__currentLoopData = $page->posts($posts); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php echo $__env->make('_components.post-preview-inline', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <?php if(! $loop->last): ?>
            <hr class="w-full border-b mt-2 mb-6">
        <?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('_layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\HCK\Repositories\kennyhorna.com-legacy\source/_layouts/category.blade.php ENDPATH**/ ?>