let mix = require('laravel-mix');
let build = require('./tasks/build.js');
let tailwindcss = require('tailwindcss');
require('laravel-mix-purgecss');

mix.disableSuccessNotifications();
mix.setPublicPath('source/assets/build/');
mix.webpackConfig({
  plugins: [
    build.jigsaw,
    build.browserSync('kennyhorna.test'),
    build.watch([
      'config.php',
      'source/**/*.md',
      'source/**/*.php',
      'source/**/*.scss',
    ]),
  ],
});

mix.js('source/_assets/js/main.js', 'js')
  .sourceMaps()
  .sass('source/_assets/sass/main.scss', 'css/main.css')
  .copy('node_modules/share-buttons/dist/share-buttons.js', 'js/share-buttons.js')
  .sourceMaps()
  .options({
    processCssUrls: false,
    postCss: [tailwindcss()],
  })
  .purgeCss({
    extensions: ['html', 'md', 'js', 'php', 'vue'],
    folders: ['source'],
    whitelistPatterns: [/language/, /hljs/],
  })
  .version();
