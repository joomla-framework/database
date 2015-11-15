<?php
/**
 * Part of the Joomla Framework Crypt Package
 *
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Crypt;

/**
 * Cipher class for Rijndael 256 encryption, decryption and key generation.
 *
 * @since       1.0
 * @deprecated  2.0  Use \Joomla\Crypt\Cipher_Crypto instead
 */
class Cipher_Rijndael256 extends Cipher_Mcrypt
{
	/**
	 * @var    integer  The mcrypt cipher constant.
	 * @see    http://www.php.net/manual/en/mcrypt.ciphers.php
	 * @since  1.0
	 * @deprecated  2.0  Use \Joomla\Crypt\Cipher_Crypto instead
	 */
	protected $type = MCRYPT_RIJNDAEL_256;

	/**
	 * @var    integer  The mcrypt block cipher mode.
	 * @see    http://www.php.net/manual/en/mcrypt.constants.php
	 * @since  1.0
	 * @deprecated  2.0  Use \Joomla\Crypt\Cipher_Crypto instead
	 */
	protected $mode = MCRYPT_MODE_CBC;

	/**
	 * @var    string  The JCrypt key type for validation.
	 * @since  1.0
	 * @deprecated  2.0  Use \Joomla\Crypt\Cipher_Crypto instead
	 */
	protected $keyType = 'rijndael256';
}
