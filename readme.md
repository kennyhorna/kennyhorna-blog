# Welcome to my blog

This is the source code of my blog, made with Jigsaw and TailwindCSS. You can read
more about this site 
[here in my blog](https://kennyhorna.com/blog/creando-sitios-estaticos-con-jigsaw/).


## Installation

After cloning or downloading this repo, install the dependencies:

```bash
composer install
npm run dev
```

Don't forget to modify the config of the site, specially the base url:

```php
return [
    'baseUrl' => 'http://kennyhorna.test', // <--
    'production' => false,
    'siteName' => "Kenny Horna.",
    'siteDescription' => 'Un lugar donde comparto las cosas que me interesan.',
    'siteAuthor' => 'Kenny Horna',

    // ...
]
```

After this, you should be able to preview the site.

To generate the production files, just run:

```bash
npm run production
```

I wrote an article about this, you can find it 
[here](https://kennyhorna.com/blog/creando-sitios-estaticos-con-jigsaw/).
