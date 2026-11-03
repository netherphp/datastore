# NetherPHP: Datastore

[![Packagist](https://img.shields.io/packagist/v/netherphp/datastore.svg?style=for-the-badge)](https://packagist.org/packages/netherphp/datastore)
[![Build Status](https://img.shields.io/github/actions/workflow/status/netherphp/datastore/phpunit.yml?style=for-the-badge)](https://github.com/netherphp/datastore/actions)
[![codecov](https://img.shields.io/codecov/c/gh/netherphp/datastore?style=for-the-badge&token=VQC48XNBS2)](https://codecov.io/gh/netherphp/datastore)

Provides a data collection object that is a redux of the Nether\Common\Datastore rebuilt with the lessons learned from the original. It's primary purpose is to make PHP's API more sane by having consistent callable argument orders, naming, etc, whilst working with arrays of data.

# Documentation

[View on Github Wiki](https://github.com/netherphp/datastore/wiki/API:-Nether%5CDatastore)

# TODO

* Move Nether\Meta namespace to `netherphp/meta` once base annotations are re-established.

* Move Nether\Filters namespace to `netherphp/filter` once base filters are re-implemented.

* Dependencies: Nether\Datastore
	- netherphp/filter
	- netherphp/meta

* Dependencies: Nether\Filters
	- netherphp/meta

* Dependencies: Nether\Meta
	- none
