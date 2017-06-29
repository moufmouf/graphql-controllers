[![Latest Stable Version](https://poser.pugx.org/thecodingmachine/graphql-controllers/v/stable)](https://packagist.org/packages/thecodingmachine/graphql-controllers)
[![Total Downloads](https://poser.pugx.org/thecodingmachine/graphql-controllers/downloads)](https://packagist.org/packages/thecodingmachine/graphql-controllers)
[![Latest Unstable Version](https://poser.pugx.org/thecodingmachine/graphql-controllers/v/unstable)](https://packagist.org/packages/thecodingmachine/graphql-controllers)
[![License](https://poser.pugx.org/thecodingmachine/graphql-controllers/license)](https://packagist.org/packages/thecodingmachine/graphql-controllers)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/thecodingmachine/graphql-controllers/badges/quality-score.png?b=1.x)](https://scrutinizer-ci.com/g/thecodingmachine/graphql-controllers/?branch=1.x)
[![Build Status](https://travis-ci.org/thecodingmachine/graphql-controllers.svg?branch=1.x)](https://travis-ci.org/thecodingmachine/graphql-controllers)
[![Coverage Status](https://coveralls.io/repos/thecodingmachine/graphql-controllers/badge.svg?branch=1.x&service=github)](https://coveralls.io/github/thecodingmachine/graphql-controllers?branch=1.x)


GraphQL controllers
===================

**Work in progress, no stable release yet**

A utility library on top of `Youshido/graphql` library.

This library allows you to write your GraphQL queries in simple to write controllers:

```php
use TheCodingMachine\GraphQL\Controllers\Annotations\Query;

class UserController
{
    /**
     * @Query()
     * @return User[]
     */
    public function users(int $limit, int $offset): array
    {
        // Some code that returns an array of "users".
        // This completely replaces the "resolve" method.
    }
}
```


Troubleshooting
---------------

### Error: Maximum function nesting level of '100' reached

Youshido's GraphQL library tends to use a very deep stack. This error does not necessarily mean your code is going into an infinite loop.
Simply try to increase the maximum allowed nesting level in your XDebug conf:

```
xdebug.max_nesting_level=500
```