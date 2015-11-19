<?php
/**
 * Simple class for checking IP address suits IP mask.
 *
 * @package Atk14\Ip
 * @filesource
 */

/**
 * Simple class for checking IP address suits IP mask.
 *
 * Compare two IPs
 * ```
 * IP::Match("127.0.0.1", "127.0.0.1");
 * ```
 *
 * Check an IP belongs to a 127.0.0.1-127.0.0.255 subnet
 * ```
 * IP::Match("127.0.0.1", "127.0.0.1/24");
 * ```
 * Check an IP matches at least one of addresses/subnets specified in an array
 * ```
 * IP::Match("127.0.0.1", array("127.0.0.2", "127.0.0.1/24"));
 * ```
 *
 * @package Atk14\Ip
 * @filesource
 */
class IP {

	/**
	 * Checks if IP address matches another IP or subnet.
	 *
	 * @param string $ip
	 * @param string|array $adr_mask
	 * @return boolean
	 */
	static function Match($ip, $adr_mask) {
		if (is_array($adr_mask)) {
			foreach($adr_mask as $mask) {
				if (self::Match($ip, $mask)) {
					return true;
				}
			}
			return false;
		}
		list($subnet,$mask) = preg_split('/\//', $adr_mask."/");

		# default mask
		$bits = 32;
		if (preg_match("/^(\d{1,2})$/", $mask, $matches)) {
			$bits = $matches[1];
		}
		// TODO: lepsi regexp?
		if (preg_match("/^(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})$/", $mask, $matches)) {
			$bits = IP::_Mask2cidr($mask);
			# pri nejake divne masce, ze ktere nevzejde cele cislo vracime false
			if ((float)(int)$bits != $bits) {
				return false;
			}
		}
		$mask = -1 << 32 - (int)$bits;

		$ip = ip2long($ip);
		$subnet = ip2long($subnet);
		return (($ip&$mask) == ($subnet&$mask));
	}

	/**
	 * Prevede teckovou masku na cidr masku
	 * Pokud je maska nevalidni (napr. 255.240.255.0), vracena hodnota bude mit nenulovou desetinnou cast
	 *
	 * @return float
	 */
	private static function _Mask2cidr($mask) {
		$long = ip2long($mask);
		$base = ip2long('255.255.255.255');
		return 32-log(($long ^ $base)+1,2);
	}
			  
}
