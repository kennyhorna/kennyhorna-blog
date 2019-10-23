[![Netlify Status](https://api.netlify.com/api/v1/badges/82e4fdd4-851b-4aee-b625-c3cbb53aa981/deploy-status)](https://app.netlify.com/sites/kennyhorna/deploys)

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

To generate the production files, first, change your specific details in the production file ``config.production.php``:

```php
<?php

return [
    'baseUrl' => 'https://kennyhorna.com',
    'production' => true,
];

```
 
 Then, just run:

```bash
npm run production
```

I wrote an article about this, you can find it on my site: 
[kennyhorna.com](https://kennyhorna.com/blog/creando-sitios-estaticos-con-jigsaw/).
