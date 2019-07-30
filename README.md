# Shotstack PHP Examples

- **text.php** -
    Create a HELLO WORLD video title against black background with a zoom in motion effect and soundtrack.

- **images.php** -
    Takes an array of image URLs and creates a video with a soundtrack and simple zoom in effect.

- **titles.php** -
    Create a video to demo titles using the available preset font styles, a soundtrack, zoom in motion effect and 
    wipe right transition.
    
- **filters.php** -
    Applies filters to a video clip, including a title with the name of the filter and a soundtrack.
    
- **status.php** -
    Shows the status of a render task and the output video URL. Run this after running one of the render examples.
    
### Installation

Install the required dependencies including the [Shotstack SDK](https://packagist.org/packages/shotstack/shotstack-sdk-php)

```bash
composer install
```

### Set your API key

```bash
export SHOTSTACK_KEY=your_key_here
```
You can [get an API key](http://shotstack.io/?utm_source=github&utm_medium=demos&utm_campaign=php_sdk) via the Shotstack web site.

### Run an example

The examples directory includes a number of examples demonstrating the capabilities of the 
Shotstack API.

To run the images example:

```bash
php examples/images.php
```
