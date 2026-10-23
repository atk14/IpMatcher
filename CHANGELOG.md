# Change Log

All notable changes to IpMatcher will be documented in this file.

## [1.0.2] 2026-10-23

* cbccaa0 - Fix IPv6 match without explicit mask defaulting to /0 instead of /128

## [1.0.1] 2026-10-23

* be65574 - Fix IPv6 subnet matching: trailing zero bytes stripped by unpack, IPv4 cidr accepted by Match6
