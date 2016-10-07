<?php
class tc_ip extends tc_base {

	function test_ipv6() {
		$tested_subnets = array(
			array(
				"ip" => "21DA:00D3:0000:2F3B:02AC:00FF:FE28:9C5A",
				"cidr" => "21DA:00D3:0000:2F3B::/64",
				"expected_result" => true,
			),
			array(
				"ip" => "21DA:00D3:0000:2F3B:04AC:01FF:FE28:9C5A",
				"cidr" => "21DA:00D3:0000:2F3B::/64",
				"expected_result" => true,
			),
			array(
				"ip" => "21DB:00D3:0000:2F3B:02AC:00FF:FE28:9C5A",
				"cidr" => "21DA:00D3:0000:2F3B::/64",
				"expected_result" => false,
			),
			array(
				"ip" => "::1",
				"cidr" => "0::0/64",
				"expected_result" => true,
			),
			array(
				"ip" => "::1",
				"cidr" => "1::0/64",
				"expected_result" => false,
			),
			array(
				"ip" => "::1",
				"cidr" => "a0::2/4",
				"expected_result" => true,
			),
			array(
				"ip" => "::1",
				"cidr" => "a0::2/64",
				"expected_result" => false,
			),
			array(
				"ip" => "::1",
				"cidr" => "0::0/64",
				"expected_result" => true,
			),
			# IPv6 adress against v4 range
			array(
				"ip" => "::1",
				"cidr" => "127.0.0.1",
				"expected_result" => false,
			),
			array(
				"ip" => "::1",
				"cidr" => "192.168.2.0/24",
				"expected_result" => false,
			),
		);

		foreach($tested_subnets as $idx => $subnet) {
			$this->assertEquals($subnet["expected_result"], IP::Match6($subnet["ip"], $subnet["cidr"]), sprintf("Failed with idx: %d, %s", $idx, print_r($subnet,true)));
			$this->assertEquals($subnet["expected_result"], IP::Match($subnet["ip"], $subnet["cidr"]), sprintf("Failed with idx: %d, %s", $idx, print_r($subnet,true)));
		}
	}

	function test_ipv4() {
		$mask_ar = array(
			"195.100.100.0",
			"195.100.100.1",
			"195.100.100.0/24",
			"195.100.100.255/24",
			"195.100.100.255/16",
			"195.200.100.127/25"
		);
		$tested_subnets = array(
			array(
				"ip" => "195.100.100.1",
				"cidr" => "195.100.100.1",
				"expected_result" => true,
			),
			array(
				"ip" => "195.100.100.1",
				"cidr" => "195.100.100.1/32",
				"expected_result" => true,
			),
			array(
				"ip" => "195.100.100.2",
				"cidr" => "195.100.100.1/32",
				"expected_result" => false,
			),
			array(
				"ip" => "195.100.100.1",
				"cidr" => "195.100.100.0/24",
				"expected_result" => true,
			),
			array(
				"ip" => "195.100.100.1",
				"cidr" => "195.100.100.255/24",
				"expected_result" => true,
			),
			array(
				"ip" => "195.100.101.1",
				"cidr" => "195.100.100.255/24",
				"expected_result" => false,
			),
			array(
				"ip" => "195.100.101.1",
				"cidr" => "195.100.100.255/16",
				"expected_result" => true,
			),
			array(
				"ip" => "195.100.101.1",
				"cidr" => "195.100.100.255/255.255.0.0",
				"expected_result" => true,
			),
			array(
				"ip" => "195.100.100.128",
				"cidr" => "195.100.100.127/25",
				"expected_result" => false,
			),
			array(
				"ip" => "195.100.100.127",
				"cidr" => "195.100.100.127/25",
				"expected_result" => true,
			),
			array(
				"ip" => "195.100.100.127",
				"cidr" => "195.100.100.127/255.255.255.128",
				"expected_result" => true,
			),
			array(
				"ip" => "195.100.100.1",
				"cidr" => $mask_ar,
				"expected_result" => true,
			),
			array(
				"ip" => "195.100.101.1",
				"cidr" => $mask_ar,
				"expected_result" => true,
			),
			array(
				"ip" => "195.101.101.1",
				"cidr" => $mask_ar,
				"expected_result" => false,
			),
			array(
				"ip" => "195.101.100.1",
				"cidr" => $mask_ar,
				"expected_result" => false,
			),
			array(
				"ip" => "195.200.100.127",
				"cidr" => $mask_ar,
				"expected_result" => true,
			),
			array(
				"ip" => "195.200.100.128",
				"cidr" => $mask_ar,
				"expected_result" => false,
			),
			# Check Ipv4 address against v6 ranges
			array(
				"ip" => "195.200.100.128",
#				"cidr" => "0::0/64",
				"cidr" => "21DA:00D3:0000:2F3B::/64",
				"expected_result" => false,
			),
			array(
				"ip" => "195.200.100.128",
				"cidr" => "0::0/64",
				"expected_result" => false,
			),
			array(
				"ip" => "195.200.100.128",
				"cidr" => "::1",
				"expected_result" => false,
			),
		);

		foreach($tested_subnets as $idx => $subnet) {
			$this->assertEquals($subnet["expected_result"], IP::Match4($subnet["ip"], $subnet["cidr"]), sprintf("Failed with idx: %d, %s", $idx, print_r($subnet,true)));
			$this->assertEquals($subnet["expected_result"], IP::Match($subnet["ip"], $subnet["cidr"]), sprintf("Failed with idx: %d, %s", $idx, print_r($subnet,true)));
		}
	}
}
