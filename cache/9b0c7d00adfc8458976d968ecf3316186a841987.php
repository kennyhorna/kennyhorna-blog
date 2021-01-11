<!DOCTYPE html>
<html lang="es" xml:lang="es" xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <meta name="description" content="<?php echo e($page->meta_description ?? $page->siteDescription); ?>">

  <meta property="og:title" content="<?php echo e($page->title ?  $page->title . ' | ' : ''); ?><?php echo e($page->siteName); ?>"/>
  <meta property="og:type" content="website"/>
  <meta property="og:url" content="<?php echo e($page->getUrl()); ?>"/>
  <meta property="og:description" content="<?php echo e($page->siteDescription); ?>"/>

  <title><?php echo e($page->siteName); ?><?php echo e($page->title ? ' | ' . $page->title : ''); ?></title>

  <link rel="home" href="<?php echo e($page->baseUrl); ?>">
  <link rel="icon" href="/favicon.png">
  <link href="/blog/feed.atom" type="application/atom+xml" rel="alternate" title="<?php echo e($page->siteName); ?> Atom Feed">

<?php echo $__env->yieldPushContent('meta'); ?>

<?php if($page->production): ?>
  
  <script async src="https://www.googletagmanager.com/gtag/js?id=UA-149941036-1"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'UA-149941036-1');
  </script>
<?php endif; ?>

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/inter-ui@3.11.0/inter.min.css">
  <link href="https://fonts.googleapis.com/css?family=Fira+Code&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?php echo e(mix('css/main.css', 'assets/build')); ?>">
</head>

<body>
<?php (setlocale(LC_ALL, 'es_ES.UTF-8')); ?>
<div id="vue-app" class="flex flex-col justify-between min-h-screen text-gray-800 leading-normal font-sans bg-gray-100">
  <header class="flex items-center shadow bg-white border-b h-20 py-4 sticky top-0 z-50" role="banner">
    <div class="container flex items-center max-w-6xl mx-auto px-4 lg:px-8">
      <div class="flex items-center">
        <a href="/" title="<?php echo e($page->siteName); ?> home" class="inline-flex items-center">
          <h1 class="text-lg md:text-2xl text-purple-800 font-semibold hover:text-purple-600 my-0"><?php echo e($page->siteName); ?></h1>
        </a>
      </div>

      <div class="flex flex-1 justify-end items-center">
        <search></search>

        <?php echo $__env->make('_nav.menu', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <?php echo $__env->make('_nav.menu-toggle', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
      </div>
    </div>
  </header>

  <?php echo $__env->make('_nav.menu-responsive', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

  <main role="main" class="flex-auto w-full container max-w-6xl mx-auto pt-4 pb-8 md:px-6">
    <?php echo $__env->yieldContent('body'); ?>
  </main>

  <footer class="bg-white text-center text-sm mt-12 py-4" role="contentinfo">

      <span class="md:mr-2">
        &copy; Kenny Horna <?php echo e(date('Y')); ?>.
      </span>

  </footer>


</div>
<script src="<?php echo e(mix('js/main.js', 'assets/build')); ?>"></script>
<?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\HCK\Repositories\kennyhorna.com-legacy\source/_layouts/master.blade.php ENDPATH**/ ?>