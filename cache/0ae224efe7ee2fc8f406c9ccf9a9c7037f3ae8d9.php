<?php $__env->startPush('meta'); ?>
  <meta property="og:locale" content="es_ES"/>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('body'); ?>
  <?php $__currentLoopData = $posts->where('featured', true); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $featuredPost): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="w-full mb-6 px-3 md:px-0">
      <?php if($featuredPost->cover_image): ?>
        <a href="<?php echo e($featuredPost->getUrl()); ?>" title="Leer: <?php echo e($featuredPost->title); ?>">
          <img src="<?php echo e($featuredPost->cover_image); ?>" alt="<?php echo e($featuredPost->title); ?> imagen de portada"
               class="mb-6 no-draggable">
        </a>
      <?php endif; ?>

      <div class="flex flex-row my-2">
        <span
            class="inline-block bg-gray-300 hover:bg-purple-200 leading-loose tracking-wide
            text-gray-800 uppercase text-xs font-semibold rounded mr-4 px-2 pt-px cursor-default"
        >
        <?php echo e($featuredPost->reading_time->abbreviated); ?>

      </span>

        <p class="text-gray-700 font-medium text-sm my-0">
          <?php echo e(strftime("%d de %B, %Y", $featuredPost->getDate()->getTimestamp())); ?>

        </p>
      </div>

      <h2 class="text-3xl mt-0">
        <a href="<?php echo e($featuredPost->getUrl()); ?>" title="Read <?php echo e($featuredPost->title); ?>"
           class="text-purple-700 font-extrabold">
          <?php echo e($featuredPost->title); ?>

        </a>
      </h2>

      <p class="mt-0 mb-4"><?php echo $featuredPost->description; ?></p>

      <a href="<?php echo e($featuredPost->getUrl()); ?>" title="Leer: - <?php echo e($featuredPost->title); ?>"
         class="uppercase tracking-wide mb-4">
        Leer más
      </a>
    </div>

    <?php if(! $loop->last): ?>
      <hr class="border-b my-6">
    <?php endif; ?>
  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>



  <?php $__currentLoopData = $posts->where('featured', false)->take(6)->chunk(2); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="flex flex-col md:flex-row md:-mx-6">
      <?php $__currentLoopData = $row; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="w-full md:w-1/2 md:mx-6">
          <?php echo $__env->make('_components.post-preview-inline', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </div>

        <?php if(! $loop->last): ?>
          <hr class="block md:hidden w-full border-b mt-2 mb-6">
        <?php endif; ?>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <?php if(! $loop->last): ?>
      <hr class="w-full border-b mt-2 mb-6">
    <?php endif; ?>
  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('_layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\HCK\Repositories\kennyhorna.com-legacy/source\index.blade.php ENDPATH**/ ?>