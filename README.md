#PHP-TinyPNG

A quick and easy to use library for interacting with the TinyPNG API.

To retrieve an API key please click [here](https://tinypng.com/developers)

For more information about TinyPNG or the TinyPNG API Documentation, please visit the links below:

[Official TinyPNG Website](https://tinypng.com/) - 
[TinyPNG API Documentation](https://tinypng.com/developers/reference)

## Contents

- [Usage](#usage)
- [Example Usage](#example-usage)
- [Example Responses](#example-responses)
- [Troubleshooting](#troubleshooting)
- [Issues / Pull Requests](#issues-pull-requests)

## Usage

1. Download the required files (listed below)
  - tinypng.php
  - cacert.pem (optional) - *This file is only required if you are having trouble connecting to the API's endpoint.*
2. Ensure that both files are within the same directory
3. Require tinypng.php
4. Create a new instance of TinyPNG, making sure that you input your API key as the first parameter
5. Finally, using the instance of TinyPNG that you just created, call the `compress` function to compress either a single or multiple images

## Example Usage

    <?php
    // Require tinypng.php
    require_once("path/to/tinypng.php");
    
    // Create a new instance of TinyPNG
    $tinypng = new TinyPNG("API_KEY_GOES_HERE");
    
    // Compress a single image
    $singleImage = $tinypng -> compress("input.png", "output.png");

    // Compress multiple images
    $images = array(
      "input.png"   => "output.png",
      "input-2.png" => "output.png"
    );
    $multipleImages = $tinypng -> compress($images);
    ?>

## Example Responses

**Single Image:**

    Array
    (
      [input] => Array
        (
          [size] => 420194
        )
    
      [output] => Array
        (
          [size] => 111442
          [ratio] => 0.2652
          [url] => https://api.tinypng.com/output/7rdrvctrffc0u4t5.png
        )
    )

**Multiple Images:**

    Array
    (
      [input.png] => Array
        (
          [input] => Array
            (
              [size] => 420194
            )

          [output] => Array
            (
              [size] => 111442
              [ratio] => 0.2652
              [url] => https://api.tinypng.com/output/aaba648k6hydvpck.png
            )
        )

      [input-2.png] => Array
        (
          [input] => Array
            (
              [size] => 420194
            )

          [output] => Array
            (
              [size] => 111442
              [ratio] => 0.2652
              [url] => https://api.tinypng.com/output/9bk8dksi5gw6kavg.png
            )
        )
    )

## Troubleshooting

**I am having issues connecting to the API endpoint**  
Some users may experience issues when attempting to connect to the TinyPNG API endpoint, and for this reason TinyPNG have been kind enough to provide a pem file to enable us to get around this issue.

The first thing you need to do is make sure you have downloaded the `cacert.pem` file that was mentioned in the first step of the usage instructions at the top of this readme. Once you have downloaded that, make sure it is placed i the same directory as tinypng.php.

After this, it is simply a case of passing `true` as the second parameter of the TinyPNG class instantiation like so:

    // Instead of this
    $tinypng = new TinyPNG("API_KEY_GOES_HERE");

    // Do this (notice 'true' being passed to the second parameter)
    $tinypng = new TinyPNG("API_KEY_GOES_HERE", true);

## Issues / Pull Requests

If you have any issues or feature suggestions, please feel free to submit an issue and/or pull request and I will be happy to take a look.