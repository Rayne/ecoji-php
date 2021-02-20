# Ecoji for PHP 🏣🔉🦐🔼🍉🔹🦒📲🐒🍍🀄☕

Ecoji encodes data as 1024 emojis.
It's like `base1024` with an emoji character set. Visit [ecoji.io](https://ecoji.io) to try Ecoji in your browser.

> [`rayne/ecoji`](https://packagist.org/packages/rayne/ecoji) is a PHP port of [Ecoji](https://github.com/keith-turner/ecoji) with 100% test coverage.

[![Latest Stable Version](https://poser.pugx.org/rayne/ecoji/v/stable)](https://packagist.org/packages/rayne/ecoji)
[![Code Coverage](https://scrutinizer-ci.com/g/rayne/ecoji-php/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/rayne/ecoji-php/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/rayne/ecoji-php/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/rayne/ecoji-php/?branch=master)
[![License](https://poser.pugx.org/rayne/ecoji/license)](https://packagist.org/packages/rayne/ecoji)

## Contents

* [Installation](#installation)
* [Encoding](#encoding)
* [Decoding](#decoding)
* [Streams](#streams)
* [CLI](#cli)
* [Docker](#docker)

## Installation

```bash
composer require rayne/ecoji
```

## Encoding

```php
use Rayne\Ecoji\Ecoji;

$ecoji = new Ecoji;
$ecoji->encode("Base64 is so 1999, isn\'t there something better?\n");
```

```
🏗📩🎦🐇🎛📘🔯🚜💞😽🆖🐊🎱🥁🚄🌱💞😭💮🇵💢🕥🐭🔸🍉🚲🦑🐶💢🕥🔮🔺🍉📸🐮🌼👦🚟🥴📑
```

## Decoding

```php
use Rayne\Ecoji\Ecoji;

$ecoji = new Ecoji;
$ecoji->decode('🏗📩🎦🐇🎛📘🔯🚜💞😽🆖🐊🎱🥁🚄🌱💞😭💮🇵💢🕥🐭🔸🍉🚲🦑🐶💢🕥🔮🔺🍉📸🐮🌼👦🚟🥴📑');
```

```
Base64 is so 1999, isn't there something better?
```

## Streams

```php
use Rayne\Ecoji\Ecoji;

$ecoji = new EcojiStream;
$ecoji->encode($sourceStream, $destinationStream);
$ecoji->decode($sourceStream, $destinationStream);
```

`EcojiStream` doesn't wrap the encoded stream without configuring the wrap length first.
A value of `0` disables wrapping.

```php
use Rayne\Ecoji\Ecoji;

$ecoji = new EcojiStream;
$ecoji->setWrap(80);
```

## CLI

The CLI encodes and decodes files and streams.

```bash
./bin/ecoji --help
```

```
Usage: ecoji [OPTIONS]... [FILE]

Encode or decode data as Unicode emojis. 😁

Options:
    -d, --decode          Decode data.
    -w, --wrap COLS       Wrap encoded lines after COLS characters (default 76).
                          Use 0 to disable line wrapping.
    -h, --help            Print this message.
    -v, --version         Print version information.
```

Installing the Composer package `rayne/ecoji` will create a symlink, e.g. `vendor/bin/ecoji`.

## Docker

Launch a temporary [Ecoji Docker container](https://hub.docker.com/r/rayne/ecoji) to utilize the CLI:

```bash
docker run -it --rm rayne/ecoji --help
```

Pipe data through a container:

```bash
echo -n "Ecoji for Docker" | docker run -i --rm rayne/ecoji
🏣🔉🦐🔼🍉🔹🦒📲🏟🙁🎧🤒💙☕☕☕
```

Encode or decode a file by mounting it as volume
or piping its content through a container:

```bash
docker run -it --rm -v /my/message:/file rayne/ecoji /file
```

```bash
cat /my/message | docker run -i --rm rayne/ecoji
```

### Docker Images

The [`docker/README.md`](docker/README.md) explains how to build the application and all optional development images for all supported PHP versions.
Additional convenience scripts run the unit tests with all supported PHP versions.

## Tests

The library registers the test runner as composer script.

```bash
composer test
```

All units tests can also be run in the development containers specified in the `docker` directory.
