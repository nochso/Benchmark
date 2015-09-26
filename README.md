# nochso/benchmark library

[![Latest Stable Version](https://poser.pugx.org/nochso/benchmark/v/stable)](https://packagist.org/packages/nochso/benchmark)
[![License](https://poser.pugx.org/nochso/benchmark/license)](LICENSE)

nochso/benchmark creates pretty HTML benchmarks from any closures you supply:

- Generate HTML reports including Github flavoured Markdown
- Reliably compare different algorithms
- Run tests for a minimum amount of time to ensure stable results

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
- Improve PHP docs
- Use nochso/sami-theme for API documentation
- Move `Path` and `Out` to new namespace `\nochso\Benchmark\Util`

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
