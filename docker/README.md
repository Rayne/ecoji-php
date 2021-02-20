# Docker Images

## Files

- `app`:
  Contains the `Dockerfile` for the CLI application

- `bin`:
  This directory contains convenience scripts

    - `bin/build-all`:
      Builds the application image and all development images

    - `bin/run`:
      Forwards the given arguments to a container running the specified development container,
      e.g. `run 8.0 composer test` runs the unit tests inside a development container with PHP 8.0

    - `bin/test-all`:
      Runs unit tests in all supported development containers.
      Reports and reusable files for later runs are written to `/builds/$version-dev-php-$php_version`.
      As a consequence, bandwidth and time is saved.
      Since every application and PHP version has a dedicated directory,
      reports are not overwritten by runs with different versions

- `dev-php-*`:
  These directories contain `Dockerfile` files that build development environments for all supported PHP version.

## Docker Example

If one does not want to use the previously mentioned convenience scripts,
this is (almost) the second simplest solution to build and execute a development image.

```bash
cd ..

docker build \
    -f docker/dev-php-8.0/Dockerfile \
    -t rayne/ecoji:dev-php-8.0 \
    .

docker run --rm -ti \
    --user "$UID:$UID" \
    --workdir /app \
    -v "$(pwd):/app:rw" \
    rayne/ecoji:dev-php-8.0 composer update

docker run --rm -ti \
    --user "$UID:$UID" \
    --workdir /app \
    -v "$(pwd):/app:rw" \
    rayne/ecoji:dev-php-8.0 composer test
```

The same can be accomplished by building all development images first
and then executing the same `composer` commands.

```bash
./bin/build-all
./bin/run 8.0 composer update
./bin/run 8.0 composer test
```
