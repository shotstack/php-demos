# Shotstack PHP Examples

### Video examples

- **text.php** -
    Create a HELLO WORLD video title against black background with a zoom in motion effect and soundtrack.

- **images.php** -
    Takes an array of image URLs and creates a video with a soundtrack and simple zoom in effect.

- **titles.php** -
    Create a video to demo titles using the available preset font styles, a soundtrack, zoom in motion effect and 
    wipe right transition.
    
- **filters.php** -
    Applies filters to a video clip, including a title with the name of the filter and a soundtrack.

- **captions.php** -
    Parse an SRT transcript file and apply the captions to a video.

- **layers.php** -
    Layer a title over a background video using tracks. The title includes a zoom effect and is semi-transparent.

- **luma.php** -
    Create animated transition effects using a luma matte and the luma matte asset type.

### Image examples

- **border.php** -
    Add a border frame around a background photo.

### Polling example

- **status.php** -
    Shows the status of a render task and the output video URL. Run this after running one of the render examples.

### Asset management examples

- **serve-api/renderId.php** -
    Fetch all assets associated with a render ID. Includes video or image and thumbnail and poster.

- **serve-api/assetId.php** -
    Fetch an individual asset by asset ID.

- **serve-api/destination.php** -
    Shows how to exclude a render from being sent to the Shotstack hosting destination.

---
### Installation

Install the required dependencies including the [Shotstack SDK](https://packagist.org/packages/shotstack/shotstack-sdk-php)

```bash
composer install
```

### Set your API key

The demos use the **staging** endpoint by default so use your provided **staging** key:

```bash
export SHOTSTACK_KEY=your_key_here
```

Windows users (Command Prompt):

```bash
set SHOTSTACK_KEY=your_key_here
```

You can [get an API key](http://shotstack.io/?utm_source=github&utm_medium=demos&utm_campaign=php_sdk) via the Shotstack web site.

### Run an example

The examples directory includes a number of examples demonstrating the capabilities of the 
Shotstack API.

#### Rendering

To run a rendering/editing example run the examples at the root of the examples folder, e.g. to run the images video 
example:

```bash
php examples/images.php
```

#### Polling

To check the status of a render, similar to polling run the `status.php` example with the render ID, e.g.:

```bash
php examples/status.php 8b844085-779c-4c3a-b52f-d79deca2a960
```

#### Asset management

To look up assets hosted by Shotstack run the examples in the [examples/serve-api](./examples/serve-api/) directory.

Find assets by render ID:
```bash
php examples/serve-api/renderId.php 8b844085-779c-4c3a-b52f-d79deca2a960
```

or 

Find an asset by asset ID:
```bash
php examples/serve-api/assetId.php 3f446298-779c-8c8c-f253-900c1627b776
```
