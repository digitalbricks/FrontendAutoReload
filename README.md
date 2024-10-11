# FrontendAutoReload Module for ProcessWire CMF/CMS

This module watches the `/templates` folder for file changes and triggers a reload in the frontend if changes are detected.


## Requirements
* ProcessWire >= 3.0.173
* PHP >= 8.2 (not tested lower versions but may work)


## Installation

1. Copy the `FrontendAutoReload` directory into your `site/modules/` directory.
2. In the ProcessWire admin, go to Modules > Refresh.
3. Click "Install" next to the FrontendAutoReload module.


## Usage

In order to load the needed javascript you have to add this in your template, somewhere before the closing `</body>` tag:

```php
$far = $modules->get('FrontendAutoReload');
echo $far->renderScript();
```

Depending on your templates [output strategy](https://processwire.com/docs/front-end/output/) usually the `_foot.php` or `_main.php` should be the right place.


## Configuration

There are three options you may configure:

* polling interval in seconds (default: `5`) - via `(int) setInterval()`
* excluded direcories (default: `'/images'`) – via `(array) setExcludedDirectories()`
* excluded file extensions (default: `'jpeg', 'jpg', 'png', 'svg', 'gif'`) – via `(array) setExcludedExtensions()` 

Here is an example using all of the mentioned methods:

```php
// get a module instance
$far = $modules->get('FrontendAutoReload');

// set polling interval to 2 seconds
$far->setInterval(2);

// exclude site/templates/assets and site/templates/vendor
$far->setExcludedDirectories(['/assets', '/vendor']); // note the leading slash!

// exclude markdown and bitmap files
$far->setExcludedExtensions(['md', 'bmp']); // without dot

// output the javascript
echo $far->renderScript();
```

It's important that the configuration is assigned **before** calling `renderScript()` as that output reflects the configuration.

In the default configuration, images (and the folder that can potentially contain images) are intentionally excluded. This is because the module is intended mainly to react to code changes – but you may change this behavior by using the above-mentioned methods.

## Superuser only
Please be aware that the module is designed to be only active for logged in superusers (be default the initial user who installed ProcessWire, creating the admin user account) and only if `$config->debug = true` is set in the ProcessWire config file (`/site/config.php`). In any other case the URL endpoint won't be registered and the script won't be rendered.


## Technical Background
This autoload module registers a [URL hocks](https://processwire.com/docs/modules/hooks/#url-path-hooks), which were introduced in ProcessWire 3.0.173, as an HTTP endpoint. This endpoint returns the timestamp of the latest modified file as JSON. A script in the frontend (rendered via `renderScript()`) polls that endpoint in the configured interval and triggers a reload if the timestamp changes (is higher than the previous one).

Initially I wanted to use Server Sent Events instead of Polling but as it turns out, you have to jump through some burning rings in order to get this working with URL hooks – such as sending unnecessary bytes (e.g. space characters) to the client to get real time updates. Also, when using URL hooks instead of a "real" separate PHP file, ProcessWire had problems to handle further requests in time while the file watcher does the continuous loop through the files. So I decided to go the Polling route, which also fits the needs.


