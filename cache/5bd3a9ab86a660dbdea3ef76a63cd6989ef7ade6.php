<?php echo "<?xml version=\"1.0\"?>\n"; ?>
<feed xmlns="http://www.w3.org/2005/Atom">
    <title><?php echo e($page->siteName); ?></title>
    <link href="<?php echo e($page->baseUrl); ?><?php echo e($page->site_path); ?>" />
    <link type="application/atom+xml" rel="self" href="<?php echo e($page->getUrl()); ?>" />
    <updated><?php echo e(date(DATE_ATOM)); ?></updated>
    <id><?php echo e($page->getUrl()); ?></id>
    <author>
        <name><?php echo e($page->siteAuthor); ?></name>
    </author>
    <?php echo $__env->yieldContent('entries'); ?>
</feed>
<?php /**PATH C:\HCK\Repositories\kennyhorna.com-legacy\source/_layouts/rss.blade.php ENDPATH**/ ?>