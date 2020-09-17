## The Crypt Package [![Build Status](https://ci.joomla.org/api/badges/joomla-framework/crypt/status.svg?ref=refs/heads/1.x-dev)](https://ci.joomla.org/joomla-framework/crypt)

The Crypt package provides a set of classes that can be used for encryption and decryption of data.

### Interfaces

#### `CipherInterface`

`CipherInterface` is an interface representing an encryption and decryption API.

### Classes

#### `Cipher\CipherCrypto`

## Changes From 1.x

The package has been refactored from 1.x to 2.0 to be PSR-4 compliant, and in doing so required renaming of several classes.  Below is a table of renamed classes.

| Old Name                      | New Name                            |
| ---------                     | -----                               |
| `\Joomla\Crypt\Cipher_Crypto` | `\Joomla\Crypt\Cipher\CipherCrypto` |

## Installation via Composer

You can simply run the following from the command line:

```sh
composer require joomla/crypt "2.0.*@dev"
```
