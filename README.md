# nochso/benchmark library

[![Latest Stable Version](https://poser.pugx.org/nochso/benchmark/v/stable)](https://packagist.org/packages/nochso/benchmark)
[![License](https://poser.pugx.org/nochso/benchmark/license)](LICENSE)

nochso/benchmark creates pretty HTML benchmarks from any closures you supply:

- Generate HTML reports including Github flavoured Markdown
- Reliably compare different algorithms
- Run tests for a minimum amount of time to ensure stable results

[**View an example report**](http://nochso.github.io/Benchmark/reports/search.html)

[**View the API documentation**](http://nochso.github.io/Benchmark/docs/nochso/Benchmark.html)

* [Installation](#installation)
* [Usage](#usage)
* [Contributing](#contributing)
* [History](#history)
* [Credits](#credits)
* [License](#license)

## Installation

1. Install composer on [Linux / Unix / OSX][composeru] or [Windows][composerw]
2. Require this package via composer:

    ```sh
    composer require nochso/benchmark
    ```

## Usage

Have a look at the [example reports](reports/).

A very simple example without parameters:

```php
$report = new Report('My report name');
$unit = new Unit('Encryption speed');
$unit->addClosure(function ($n) {
    while ($n--) {
        $x = str_rot13('secret');
    }
}, 'rot13');
$unit->addClosure(function ($n) {
    while ($n--) {
        $x = str_rot13(str_rot13('secret'));
    }
}, 'rot26');
$report->unitList->add($unit);
$report->run();
```

This will generate and save a HTML report to folder `build` by default.

### Options

The Report constructor takes an optional third argument `$config`:

```php
public function __construct($title, $description = '', $config = array())
```

If omitted, default options will be used. Otherwise your configuration will be merged with the defaults.

```php
array(
    'template_dir' => 'template',
    'output_dir'   => 'build',
    'twig'         => array(
        'cache'            => 'cache/twig',
        'auto_reload'      => true,
        'strict_variables' => true,
    ),
)
```

You can also change the minimum duration of tests which defaults to 1000 millseconds (1 second):
```php
\nochso\Benchmark\Timer::$defaultMinDuration = 1000;
```

## Contributing

1. [Open an issue](https://github.com/nochso/benchmark/issues/new) if it's worth discussing.
2. Fork this project on Github.
3. Clone your fork: `git clone git@github.com:yourname/benchmark.git`
4. Don't forget to `composer update`
4. Create your feature branch: `git checkout -b my-new-feature`
5. Commit your changes: `git commit -am 'Add some feature'`
6. Push to the branch: `git push origin my-new-feature`
7. Submit a pull request on Github :)

## History

### Unreleased
#### Added
- Click to hide explanation of results.

#### Changed
- Move `nochso\Benchmark\GithubMarkdownExtension` to `nochso\Benchmark\Twig` namespace.
- Center table on whole page.
- Improved explanation of results.

### 0.3.0 - 2015-09-27
#### Added
- Show median operations/second.
- Compress HTML output with [wyrihaximus/html-compress](https://github.com/WyriHaximus/HtmlCompress).
- Add a note explaining the results before the first result in each report.
- Color each parameter result based on its score compared to other methods.
- Add twig extension to calculate text color based on background color.

#### Changed
- Base the score on median average.
- Improve search report.
- Change fonts to Open Sans and Cousine.

### 0.2.0 - 2015-09-26
#### Added
- Add description property to Unit.
- Add RoboFile commands, e.g. `vendor/bin/robo <command>`
  - `docs` generates Sami API docs on master and adds them to gh-pages.
  - `reports` generates an example report and adds it to gh-pages.
- Sort and color methods by average score using UnitResult.

#### Changed
- Improve PHP docs.
- Use nochso/sami-theme for API documentation.
- Move `Path` and `Out` to new namespace `\nochso\Benchmark\Util`.

### 0.1.0 - 2015-09-25
First public release.

## Credits

- [Marcel Voigt](https://github.com/nochso)

## License
This project is licensed under the ISC license which is MIT/GPL compatible and FSF/OSI approved:

```
Copyright (c) 2015, Marcel Voigt <mv@noch.so>

Permission to use, copy, modify, and/or distribute this software for any
purpose with or without fee is hereby granted, provided that the above
copyright notice and this permission notice appear in all copies.

THE SOFTWARE IS PROVIDED "AS IS" AND THE AUTHOR DISCLAIMS ALL WARRANTIES
WITH REGARD TO THIS SOFTWARE INCLUDING ALL IMPLIED WARRANTIES OF
MERCHANTABILITY AND FITNESS. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR
ANY SPECIAL, DIRECT, INDIRECT, OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES
WHATSOEVER RESULTING FROM LOSS OF USE, DATA OR PROFITS, WHETHER IN AN
ACTION OF CONTRACT, NEGLIGENCE OR OTHER TORTIOUS ACTION, ARISING OUT OF
OR IN CONNECTION WITH THE USE OR PERFORMANCE OF THIS SOFTWARE.
```

[composeru]: "https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx"
[composerw]: "https://getcomposer.org/doc/00-intro.md#installation-windows"
