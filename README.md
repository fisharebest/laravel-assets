[![Latest Stable Version](https://poser.pugx.org/fisharebest/laravel-assets/v/stable.svg)](https://packagist.org/packages/fisharebest/laravel-assets)
[![Build Status](https://travis-ci.org/fisharebest/laravel-assets.svg?branch=master)](https://travis-ci.org/fisharebest/laravel-assets)
[![Coverage Status](https://coveralls.io/repos/fisharebest/laravel-assets/badge.svg?branch=master&service=github)](https://coveralls.io/github/fisharebest/laravel-assets?branch=master)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/84c48b77-7330-4e2a-b66d-f981b41a76a7/mini.png)](https://insight.sensiolabs.com/projects/84c48b77-7330-4e2a-b66d-f981b41a76a7)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/fisharebest/laravel-assets/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/fisharebest/laravel-assets/?branch=master)

# laravel-assets

Simple, flexible asset management for Laravel 5.  Combine and minify your CSS and JS files to make your website faster.

## Installation

Add the dependency to `composer.json`, and then run `composer update`.

```json
{
    "require": {
        "fisharebest/laravel-assets": "~1.0",
    },
}
```

Add the service provider and facade to `config/app.php`.

```php
return [
    'providers' => [
        Fisharebest\LaravelAssets\AssetsServiceProvider::class,
    ],
    'aliases' => [
        'Assets' => Fisharebest\LaravelAssets\AssetsFacade::class,
    ],
]
```

Create a configuration file, `config/assets.php`, containing default values.  Edit the settings in this file to match your project’s directory structure.

```
$ php artisan vendor:publish --provider="Fisharebest\LaravelAssets\AssetsServiceProvider"
```

## Step 1.  How to add assets

You would usually add assets in each of your templates (layouts, views, partials, etc.) that requires them.

```php
<!-- resources/views/layouts/master.blade.php -->
<?php Assets::add(['jquery', 'bootstrap', 'global.js', 'style.css', 'analytics.js']) ?>
<!-- the rest of your view ... -->
```

```php
<!-- resources/views/pages/list.blade.php -->
<?php Assets::add('list.js') ?>
<!-- the rest of your view ... -->
```

Of course, you could also add assets anywhere you choose; controllers, helpers, etc.

As well as individual files, you can add named collections of files.
These are defined in `config/assets.php`.

Where you have dependencies, you should list the files in the order they should be loaded.
For example, if `list.js` depends on jQuery, you would specify jQuery before `list.js`.

```php
<!-- resources/views/pages/list.blade.php -->
<?php Assets::add(['jquery', 'list.js']) ?>
<!-- the rest of your view ... -->
```

Duplicates are ignored, so you can add jQuery to each view that uses it and it will
only be rendered once.

## Step 2.  How to render links to assets

It is conventional to render CSS assets in the `<head>` element, and JS assets at the
end of the `<body>` element.


```php
<!-- resources/views/layouts/master.blade.php -->
<html>
    <head>
        {!! Assets::css() !!}
    </head>
    <body>
        ...
        {!! Assets::js() !!}
    </body>
</html>
```

## But what if…

### What if my assets don't have a `.js` or `.css` extension?

Specify the type as a parameter when adding the assets.  For example,

```php
Assets::add('http://example.com/script?parameter', 'js')
```

### What if I want to divide my assets into separate groups?

Specify the group as a parameter when adding and rendering assets.

```php
<!-- resources/views/layouts/master.blade.php -->
<?php Assets::add('jquery.js') ?>
<?php Assets::add('ie8.js', null, 'ie8') ?>
<?php Assets::add('analytics.js', null, 'head-script') ?>
<html>
    <head>
        ...
        <!--[if lte IE 8]>{!! Assets::js('ie8') !!}<![endif]-->
        {!! Assets::js('head-script') !!}
    </head>
    <body>
        ...
        {!! Assets::js() !!}
    </body>
</html>
```

### What if I want to add additional attributes to the style/script?

Specify a list of attributes as an argument to the render functions.

```php
{!! Assets::css('print', ['media' => 'print']) !!}
{!! Assets::js('analytics', ['async']) !!}
```

### What if I my asset files are tiny?

There's a configuration option `inline_threshold`.  Any asset file
smaller than this number of bytes will be rendered inline, thus saving
an HTTP request.

### What if I want to change the configuration at runtime?

Configuration can be changed at any time.  It only takes effect when
the assets are rendered.

```php
Assets::setGzipStatic(6);
Assets::css(); // will create compressed assets
```

### What if I want to use my own minifier?

Write your own filter (implement `Fisharebest\LaravelAssets\Filters\FilterInterface`)
and specify it in the configuration file `config/assets.php`.  Use one of the existing
filters as a template.

### What if I want to use a CDN or a cookie-free domain?

Specify a URL in the `destination_url` setting which corresponds to the folder given
in `destination`.

```php
// config/assets.php
return [
    'destination'     => 'min',                   // We create assets here
    'destination_url' => 'http://my-cdn.com/min', // Users read assets from here
]
```

### What if I need to copy my pipelined assets to an external server?

Write your own notifier (implement `Fisharebest\LaravelAssets\Notifiers\NotifierInterface`)
and specify it in the configuration file `config/assets.php`.  Use one of the existing
notifiers as a template.

You would most likely set the `destination_url` to your CDN server, and add a notifier
which copies the file from the `destination` folder to this server.

### What if I cannot use file_get_contents() because of firewall/proxy issues?

Write your own loader (implement `Fisharebest\LaravelAssets\Loaders\LoaderInterface`)
and specify it in the configuration file `config/assets.php`.  Use one of the existing
loaders as a template.

### How do I delete old files after I update my assets or change my filters?

There is an artisan command for that.

```
php artisan assets:purge
```
