# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project overview

IpMatcher (`atk14/ip-matcher`) is a tiny single-class PHP library for checking whether an IP address matches another IP, a CIDR subnet, or an array of IPs/subnets. It supports both IPv4 and IPv6. The entire implementation lives in `src/ip.php` as the `IP` class — there is no namespace, no autoloading beyond Composer's classmap.

## Commands

Install dependencies (requires Composer; dev dependency is `atk14/tester`):
```
composer update --dev
```

Run the full test suite (must `cd` into `test/` first — the runner scans the current directory for `tc_*.php` files):
```
cd test && ../vendor/bin/run_unit_tests
```

Run a single test case file:
```
cd test && ../vendor/bin/run_unit_tests tc_ip.php
```

There is no build or lint step; this is a plain PHP library.

## Architecture

- `src/ip.php` defines the static `IP` class with the public API:
  - `IP::Match($ip, $cidr)` — detects whether `$ip` is IPv4 or IPv6 (via `filter_var`) and dispatches to `Match4` or `Match6`.
  - `IP::Match4($ip, $cidr)` — IPv4-specific matching. Parses `$cidr` as `subnet[/mask]`, where mask may be a CIDR bit count (e.g. `/24`) or a dotted mask (e.g. `/255.255.0.0`, converted via the private `_Mask2cidr`). Uses `ip2long` and bitmasking to compare.
  - `IP::Match6($ip, $cidr)` — IPv6-specific matching. Uses `inet_pton` plus a private `InetToBits` helper that expands the packed address into a 128-character binary string, then compares prefixes bit-by-bit according to the mask length.
  - Both `Match4` and `Match6` accept `$cidr` as either a single string or an array of strings/subnets, recursing over the array and returning true on first match (OR semantics).
  - Mismatched address families (e.g. testing an IPv6 address against an IPv4-only `$cidr` or vice versa) resolve to `false` rather than throwing.

- `test/` contains the test suite, run via the `atk14/tester` framework (a thin wrapper that auto-discovers PHPUnit and runs `tc_*.php` files, each expected to define a `Tc<Name>` class extending `TcBase`).
  - `test/initialize.php` simply `include`s `../src/ip.php` before tests run — there's no Composer autoloading in the test environment.
  - `test/tc_ip.php` is the only test case, covering `Match4`/`Match6`/`Match` with a table-driven list of `{ip, cidr, expected_result}` cases for both address families, including cross-family negatives.

- CI (`.travis.yml`) runs the same `cd test && ../vendor/bin/run_unit_tests` command across PHP 5.6–7.3.
