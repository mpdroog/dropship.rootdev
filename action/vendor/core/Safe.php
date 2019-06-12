<?php
namespace core;
require __DIR__ . "/defuse-crypto.phar";
use Defuse\Crypto\Key;
use Defuse\Crypto\Crypto;

/**
 * Safe
 */
class Safe {
	/**
	 * Encode a string.
	 */
	public static function encode($value, $privKey) {
		$key = Key::loadFromAsciiSafeString($privKey);
                return base64_encode(Crypto::encrypt($value, $key, true));
	}

	/**
	 * Decode a string.
	 */
	public static function decode($value, $privKey) {
		$key = Key::loadFromAsciiSafeString($privKey);
		return Crypto::decrypt(base64_decode($value), $key, true);
	}
}

