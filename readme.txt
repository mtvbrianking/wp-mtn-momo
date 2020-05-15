=== MTN MOMO ===
Contributors: bmatovu
Donate link:
Tags: mtn, momo, mobile, money, mobile-money, payments
Requires at least: 5.3
Tested up to: 5.4
Requires PHP: 5.6.20
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

MTN MOMO is a plugin to help you make transactions via MTN mobile money.

== Description ==

This plugin helps you make transactions via [MTN Mobile Money](https://momodeveloper.mtn.com).

The [products](https://momodeveloper.mtn.com/products) via available are:

* [Collections](https://momodeveloper.mtn.com/docs/services/collection); enable remote collection of bills, fees or taxes,
* [Disbursements](https://momodeveloper.mtn.com/docs/services/disbursement); automatically deposit funds to multiple users, and
* [Remittances](https://momodeveloper.mtn.com/docs/services/remittance); remit funds to local recipients from the diaspora with ease.

The plugin is available for both **sandbox** & **live** environment.

**Database tables**:

* `wp_mtn_momo_configurations`:

For saving environment viables used in for connecting and transacting on the MTN MOMO API.

These configurations can be managed through the configuration panel in the plugin admin portal.

* `wp_mtn_momo_tokens`:

For storing access tokens used for authenticating your client application on the MTN MOMO API.

Contents of this table are managed automatically by the plugin, so you may not edit anything here manually.

* `wp_mtn_momo_transactions`:

For keep track of transactions between your client application and MTN MOMO API.

The plugin has a transactions page in the admin portal for managing and viewing this data.
It also includes an option to export these to MS Excel.

== Frequently Asked Questions ==

= How do I debug my failed transactions =

You can enable [WordPress debugging](https://wordpress.org/support/article/debugging-in-wordpress) to debug all requests
and responses to and from the plugin.

= Go Live =

To go-live; you just need to update the configurations with from sandbox to live parameters.
But first, submit you KYC forms [here](https://momodeveloper.mtn.com/go-live)

== Screenshots ==

1. Admin Toolbar Menu
2. Configurations
3. Sandbox Configurations
4. Transactions

== Changelog ==

= 0.0.1 =
* Inital beta release.

== Arbitrary section ==

**Prerequisites**:

You will need the following to get started with you integration...

1. Create a [**developer account**](https://momodeveloper.mtn.com/signup) with MTN MOMO.
2. Subscribe to a [**product/service**](https://momodeveloper.mtn.com/products) that you wish to consume.

If you already subscribed to a product, the subscription key can be found in your [**profile**](https://momodeveloper.mtn.com/developer).
