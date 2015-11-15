<?php
/**
 * Part of the Joomla Framework Crypt Package
 *
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Crypt;

/**
 * Joomla cipher for encryption, decryption and key generation via the php-encryption library.
 *
 * @since  __DEPLOY_VERSION__
 */
class Cipher_Crypto implements CipherInterface
{
	/**
	 * Method to decrypt a data string.
	 *
	 * @param   string  $data  The encrypted string to decrypt.
	 * @param   Key     $key   The key object to use for decryption.
	 *
	 * @return  string  The decrypted data string.
	 *
	 * @since   __DEPLOY_VERSION__
	 * @throws  \InvalidArgumentException
	 * @throws  \RuntimeException
	 */
	public function decrypt($data, Key $key)
	{
		// Validate key.
		if ($key->type != 'crypto')
		{
			throw new \InvalidArgumentException('Invalid key of type: ' . $key->type . '.  Expected crypto.');
		}

		// Decrypt the data.
		try
		{
			return \Crypto::Decrypt($data, $key->public);
		}
		catch (\InvalidCiphertextException $ex)
		{
			throw new \RuntimeException('DANGER! DANGER! The ciphertext has been tampered with!', $ex->getCode(), $ex);
		}
		catch (\CryptoTestFailedException $ex)
		{
			throw new \RuntimeException('Cannot safely perform decryption', $ex->getCode(), $ex);
		}
		catch (\CannotPerformOperationException $ex)
		{
			throw new \RuntimeException('Cannot safely perform decryption', $ex->getCode(), $ex);
		}
	}

	/**
	 * Method to encrypt a data string.
	 *
	 * @param   string  $data  The data string to encrypt.
	 * @param   Key     $key   The key object to use for encryption.
	 *
	 * @return  string  The encrypted data string.
	 *
	 * @since   __DEPLOY_VERSION__
	 * @throws  \InvalidArgumentException
	 * @throws  \RuntimeException
	 */
	public function encrypt($data, Key $key)
	{
		// Validate key.
		if ($key->type != 'crypto')
		{
			throw new \InvalidArgumentException('Invalid key of type: ' . $key->type . '.  Expected crypto.');
		}

		// Encrypt the data.
		try
		{
			return \Crypto::Encrypt($data, $key->public);
		}
		catch (\CryptoTestFailedException $ex)
		{
			throw new \RuntimeException('Cannot safely perform encryption', $ex->getCode(), $ex);
		}
		catch (\CannotPerformOperationException $ex)
		{
			throw new \RuntimeException('Cannot safely perform encryption', $ex->getCode(), $ex);
		}
	}

	/**
	 * Method to generate a new encryption key object.
	 *
	 * @param   array  $options  Key generation options.
	 *
	 * @return  Key
	 *
	 * @since   __DEPLOY_VERSION__
	 * @throws  \RuntimeException
	 */
	public function generateKey(array $options = array())
	{
		// Create the new encryption key object.
		$key = new Key('crypto');

		// Generate the encryption key.
		try
		{
			$key->public = \Crypto::CreateNewRandomKey();
		}
		catch (\CryptoTestFailedException $ex)
		{
			throw new \RuntimeException('Cannot safely create a key', $ex->getCode(), $ex);
		}
		catch (\CannotPerformOperationException $ex)
		{
			throw new \RuntimeException('Cannot safely create a key', $ex->getCode(), $ex);
		}

		// Explicitly flag the private as unused in this cipher.
		$key->private = 'unused';

		return $key;
	}
}
