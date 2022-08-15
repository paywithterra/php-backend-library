## Development

~~~~ bash
git clone 
~~~~

~~~~ bash
composer i
~~~~

### Running tests:
~~~~ bash
./vendor/bin/phpunit .
~~~~

### Preparing release

Because of wide support of PHP versions and environments, we need to generate optional autoloader for projects without Composer.
For this purpose we use [PHP Autoload Builder](https://github.com/theseer/Autoload).

Install it:

```console
curl https://github.com/theseer/Autoload/releases/download/1.27.1/phpab-1.27.1.phar --location --output phpab.phar
chmod +x phpab.phar
```

Generate autoloader:

```console
./phpab.phar -s -n -o src/autoload-legacy.php -b src composer.json
```
