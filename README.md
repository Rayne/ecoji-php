# Ecoji for PHP 🏣🔉🦐🔼🍉🔹🦒📲🐒🍍🀄☕

Ecoji encodes data as 1024 emojis.
It's like `base1024` with an emoji character set.

> [`rayne/ecoji`](https://packagist.org/packages/rayne/ecoji) is a PHP port of [Ecoji](https://github.com/keith-turner/ecoji) with 100% test coverage.

[![Latest Stable Version](https://poser.pugx.org/rayne/ecoji/v/stable)](https://packagist.org/packages/rayne/ecoji)
[![Code Coverage](https://scrutinizer-ci.com/g/rayne/ecoji-php/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/rayne/ecoji-php/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/rayne/ecoji-php/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/rayne/ecoji-php/?branch=master)
[![License](https://poser.pugx.org/rayne/ecoji/license)](https://packagist.org/packages/rayne/ecoji)

## Installation

```bash
composer require rayne/ecoji
```

## Example

### Encoding

```php
use Rayne\Ecoji\Ecoji;

$ecoji = new Ecoji;
$ecoji->encode("Base64 is so 1999, isn\'t there something better?\n");
```

```
🏗📩🎦🐇🎛📘🔯🚜💞😽🆖🐊🎱🥁🚄🌱💞😭💮🇵💢🕥🐭🔸🍉🚲🦑🐶💢🕥🔮🔺🍉📸🐮🌼👦🚟🥴📑
```

### Decoding

```php
use Rayne\Ecoji\Ecoji;

$ecoji = new Ecoji;
$ecoji->decode('🏗📩🎦🐇🎛📘🔯🚜💞😽🆖🐊🎱🥁🚄🌱💞😭💮🇵💢🕥🐭🔸🍉🚲🦑🐶💢🕥🔮🔺🍉📸🐮🌼👦🚟🥴📑');
```

```
Base64 is so 1999, isn't there something better?
```

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

### Build Image

```bash
docker build -t rayne/ecoji:"$(cat assets/version.txt)" -t rayne/ecoji:latest .
```
