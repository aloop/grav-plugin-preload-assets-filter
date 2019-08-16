# Preload Assets Filter Plugin

The **Preload Assets Filter** Plugin is an extension for [Grav CMS](http://github.com/getgrav/grav) that takes assets or urls and generate http headers for link preloading.

## Installation

Installing the Preload Assets Filter plugin can be done in one of three ways: The GPM (Grav Package Manager) installation method lets you quickly install the plugin with a simple terminal command, the manual method lets you do so via a zip file, and the admin method lets you do so via the Admin Plugin.

### GPM Installation (Preferred)

To install the plugin via the [GPM](http://learn.getgrav.org/advanced/grav-gpm), through your system's terminal (also called the command line), navigate to the root of your Grav-installation, and enter:

    bin/gpm install preload-assets-filter

This will install the Preload Assets Filter plugin into your `/user/plugins`-directory within Grav. Its files can be found under `/your/site/grav/user/plugins/preload-assets-filter`.

### Manual Installation

To install the plugin manually, download the zip-version of this repository and unzip it under `/your/site/grav/user/plugins`. Then rename the folder to `preload-assets-filter`. You can find these files on [GitHub](https://github.com/aloop/grav-plugin-preload-assets-filter) or via [GetGrav.org](http://getgrav.org/downloads/plugins#extras).

You should now have all the plugin files under

    /your/site/grav/user/plugins/preload-assets-filter

### Admin Plugin

If you use the Admin Plugin, you can install the plugin directly by browsing the `Plugins`-menu and clicking on the `Add` button.

## Usage

This plugin creates two twig filters, `preloadUrl` and `preloadAssets`.

Calls to either filter will pass through the string they are given unmodified.

### `preloadUrl`

```php
# Function signature for preloadUrl
preloadUrl(string $url, string $as = "image", bool $crossorigin = false)
```

Example usage:

```twig
{# Sets the http header "Link: </your/theme/dir/images/logo.jpg>; rel=preload; as=image;" #}
{{ url('theme://images/logo.jpg')|preloadUrl }}

{# Sets the http header "Link: <https://cdn.yoursite.com/images/someimage.jpg>; rel=preload; as=image; crossorigin;" #}
{{ url(https://cdn.yoursite.com/js/script.js)|preloadUrl("image", true) }}
```

### `preloadAssets`

```php
# Function signature for preloadAssets
preloadAssets(string $assetsHtml, bool $crossorigin = false)
```

Example usage:

```twig
{# Works automatically with both assets.css() and assets.js() #}
{{ assets.css()|preloadAssets|raw }}
{{ assets.js()|preloadAssets|raw }}
{{ assets.js('bottom')|preloadAssets(true)|raw }}
```
