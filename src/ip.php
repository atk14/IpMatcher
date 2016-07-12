<?php
/**
 * Simple class for checking IP address suits IP mask.
 *
 * It can check both IPv4 and IPv6 addresses.
 *
 * @package Atk14\Ip
 * @filesource
 */

/**
 * Simple class for checking IP address suits IP mask.
 *
 * Compare two IPs. If the parameter $cidr is passed in the CIDR form, it takes it as a subnet or range of ip addresses
 * and tests if the $ip belongs to given subnet
 *
 * There are two basic methods for matching IPv4 and IPv6 addresses (Match4() and Match6()).
 * Also method Match() can be used which should recognize if the IP is v4 or v6 type.
 *
 * Basic use
 * ```
 * IP::Match("127.0.0.1", "127.0.0.1");
 * ```
 *
 * Check an IP belongs to a 127.0.0.1-127.0.0.255 subnet
 * Subnet mask can be given as integer or a dot mask.
 * ```
 * IP::Match("127.0.0.1", "127.0.0.1/24");
 * IP::Match("127.0.0.1", "127.0.0.1/255.255.255.0");
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

	static function Match($ip, $cidr) {
		if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
			return self::Match4($ip, $cidr);
		} elseif (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
			return self::Match6($ip, $cidr);
		}
	}

	/**
	 * Checks if IP address matches another IP or subnet.
	 *
	 * @param string $ip tested ip address
	 * @param string|array $cidr
	 * @return boolean true - $ip is the same as $cidr or belongs to the subnet
	 */
	static function Match4($ip, $cidr) {
		if (is_array($cidr)) {
			foreach($cidr as $mask) {
				if (self::Match($ip, $mask)) {
					return true;
				}
			}
			return false;
		}
		list($subnet,$mask) = preg_split('/\//', $cidr."/");

		# default mask
		$bits = 32;
		if (preg_match("/^(\d{1,2})$/", $mask, $matches)) {
			$bits = $matches[1];
		}
		// mask can be given in dot form (ie 255.255.0.0)
		// TODO: lepsi regexp?
		if (preg_match("/^(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})$/", $mask, $matches)) {
			$bits = self::_Mask2cidr($mask);
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
	 * Checks if na IPv6 address suits a subnet.
	 *
	 * If $cidr is an IP address, it just compares the $ip and $cidr
	 *
	 * @param string $ip tested ip address
	 * @param string|array $cidr
	 * @return boolean true - $ip is the same as $cidr or belongs to the subnet
	 */
	static function Match6($ip, $cidr) {
		if (is_array($cidr)) {
			foreach($cidr as $mask) {
				if (self::Match6($ip, $mask)) {
					return true;
				}
			}
			return false;
		}
		$ip = inet_pton($ip);
		if ($ip===false) { return false; }

		list($net,$maskbits) = explode('/',"$cidr");
		$net = inet_pton($net);
		if ($net===false) { return false; }

		$binaryip = self::InetToBits($ip);
		$binarynet = self::InetToBits($net);

		$ip_net_bits = substr($binaryip,0,$maskbits);
		$net_bits = substr($binarynet,0,$maskbits);

		return ($ip_net_bits==$net_bits);
	}

	// converts inet_pton output to string with bits
	private static function InetToBits($inet)
	{
		$unpacked = unpack('A16', $inet);
		$unpacked = str_split($unpacked[1]);
		$binaryip = '';
		foreach ($unpacked as $char) {
			$binaryip .= str_pad(decbin(ord($char)), 8, '0', STR_PAD_LEFT);
		}
		return $binaryip;
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
