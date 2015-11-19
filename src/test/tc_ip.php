<?php
class tc_ip extends tc_base {
	function test() {
		$this->assertTrue(IP::Match("195.100.100.1", "195.100.100.1"));

		$this->assertTrue(IP::Match("195.100.100.1", "195.100.100.1/32"));
		$this->assertFalse(IP::Match("195.100.100.2", "195.100.100.1/32"));
		
		$this->assertTrue(IP::Match("195.100.100.1", "195.100.100.0/24"));
		$this->assertTrue(IP::Match("195.100.100.1", "195.100.100.255/24"));
		$this->assertFalse(IP::Match("195.100.101.1", "195.100.100.255/24"));

		$this->assertTrue(IP::Match("195.100.101.1", "195.100.100.255/16"));
		$this->assertFalse(IP::Match("195.100.100.128", "195.100.100.127/25"));
		$this->assertTrue(IP::Match("195.100.100.127", "195.100.100.127/25"));

		$mask_ar = array(
			"195.100.100.0",
			"195.100.100.1",
			"195.100.100.0/24",
			"195.100.100.255/24",
			"195.100.100.255/16",
			"195.200.100.127/25"
		);
		$this->assertTrue(IP::Match("195.100.100.1", $mask_ar));
		$this->assertTrue(IP::Match("195.100.101.1", $mask_ar));
		$this->assertFalse(IP::Match("195.101.101.1", $mask_ar));
		$this->assertFalse(IP::Match("195.101.100.1", $mask_ar));
		$this->assertTrue(IP::Match("195.200.100.127", $mask_ar));
		$this->assertFalse(IP::Match("195.200.100.128", $mask_ar));



	}
}
