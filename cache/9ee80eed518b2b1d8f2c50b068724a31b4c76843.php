<div class="flex flex-col mb-4">
  <div class="w-full mb-6 px-3 md:px-0">
    <?php if($post->cover_image): ?>
      <a
          href="<?php echo e($post->getUrl()); ?>"
          title="Leer: <?php echo e($post->title); ?>"
      >
        <img src="<?php echo e($post->cover_image); ?>" alt="<?php echo e($post->title); ?> cover image" class="mb-6 no-draggable">
      </a>
    <?php endif; ?>

    <h2 class="text-3xl my-0">
      <a
          href="<?php echo e($post->getUrl()); ?>"
          title="Read more - <?php echo e($post->title); ?>"
          class="text-purple-700 font-extrabold"
      ><?php echo e($post->title); ?></a>
    </h2>

    <span
        class="my-2 inline-block bg-gray-300 hover:bg-purple-200 leading-loose tracking-wide
          text-gray-800 uppercase text-xs font-semibold rounded mr-4 px-2 pt-px cursor-default"
    >
      <?php echo e($post->reading_time->abbreviated); ?>

    </span>

    <p class="mb-4 mt-0"><?php echo $post->getExcerpt(200); ?></p>

    <a
        href="<?php echo e($post->getUrl()); ?>"
        title="Read more - <?php echo e($post->title); ?>"
        class="uppercase font-semibold tracking-wide mb-2"
    >Leer más</a>
  </div>
</div>
<?php /**PATH C:\HCK\Repositories\kennyhorna.com-legacy\source/_components/post-preview-inline.blade.php ENDPATH**/ ?>