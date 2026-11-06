# NetherPHP: Datastore #########################################################

[![Packagist](https://img.shields.io/packagist/v/netherphp/datastore.svg?style=for-the-badge)](https://packagist.org/packages/netherphp/datastore)
[![Build Status](https://img.shields.io/github/actions/workflow/status/netherphp/datastore/phpunit.yml?style=for-the-badge)](https://github.com/netherphp/datastore/actions)
[![codecov](https://img.shields.io/codecov/c/gh/netherphp/datastore?style=for-the-badge&token=VQC48XNBS2)](https://codecov.io/gh/netherphp/datastore)

Provide a wrapper to work on collections of data instead of dealing with arrays directly by hand. Sane and consistent function naming, without having to guess
the order of arguments for callable arugments. Also implements common SPL
interfaces so the Datastore can be passed onto other things that accept generic
iterables.

## Documentation ###############################################################

See `docs/API.md` for full API documentation.

#### Example: creating a configuration and then using it later.

```php
$Config = new Datastore([
	'APIKEY' => 'qwer-ty-asdf-gh-zxcv-bn',
	'Theme'  => 'dark'
]);
```

```php
echo SomeCaptchaWidgetProbably(
	$Config['APIKEY'],
	$Config['Theme']
);
```

#### Example: processing a list of users into a mailing-list.json file.

```php
(new Datastore($Users))
->Filter(fn(User $Who)=> $Who->HasOptedIn('emails'))
->SortByValue(fn(User $A, User $B)=> $A->Username <=> $B->Username)
->Remap(fn(User $Who)=> sprintf('%s <%s>', $Who->Username, $Who->Email))
->Reindex()
->Write('mailing-list.json');
```

#### Example: loading a mailing-list.json file from disk.

```php
$Emails = Datastore::FromFile('mailing-list.json');
$Emails->Each(fn(string $To)=> print('* %s%s', $To, PHP_EOL));
```