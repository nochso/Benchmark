# Change log

## 0.5.2 - 2016-04-17
### Added
- Clicking on parameter result shows original measurement.

### Changed
- Display normalized operations/unit instead of operations/sec.

### Removed
- Removed median average.

## 0.5.1 - 2016-04-16
### Changed
- Explicitly require symfony/finder

## 0.5.0 - 2016-04-16
### Changed
- Update composer dependencies.

## 0.4.0 - 2015-10-10
### Added
- Click to hide explanation of results.
- Display report progress.

### Changed
- Minify and inline both Javascript and CSS.
- Center table on whole page.
- Improved explanation of results.
- Move `nochso\Benchmark\GithubMarkdownExtension` to `nochso\Benchmark\Twig` namespace.

### Fixed
- Fixed wrong layout when using units without parameters.
- Fixed default path to template.

## 0.3.0 - 2015-09-27
### Added
- Show median operations/second.
- Compress HTML output with [wyrihaximus/html-compress](https://github.com/WyriHaximus/HtmlCompress).
- Add a note explaining the results before the first result in each report.
- Color each parameter result based on its score compared to other methods.
- Add twig extension to calculate text color based on background color.

### Changed
- Base the score on median average.
- Improve search report.
- Change fonts to Open Sans and Cousine.

## 0.2.0 - 2015-09-26
### Added
- Add description property to Unit.
- Add RoboFile commands, e.g. `vendor/bin/robo <command>`
  - `docs` generates Sami API docs on master and adds them to gh-pages.
  - `reports` generates an example report and adds it to gh-pages.
- Sort and color methods by average score using UnitResult.

### Changed
- Improve PHP docs.
- Use nochso/sami-theme for API documentation.
- Move `Path` and `Out` to new namespace `\nochso\Benchmark\Util`.

## 0.1.0 - 2015-09-25
First public release.
