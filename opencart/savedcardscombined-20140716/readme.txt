DISCLAIMER: 

This software is provided 'as is' by Robert Mullaney (hereinafter referred to as 'RM') without warranty of any kind, either express or implied, including, but not limited to; the implied warranties of fitness for a purpose, or the warranty of non-infringement. Without limiting the foregoing, RM makes no warranty that:

 - The software will meet your requirements.
 - The software will be uninterrupted, timely, secure or error-free.
 - The quality of the software will meet your expectations.

Software and documentation made available herein could include mistakes, inaccuracies, technical or typographical errors. RM may make changes at any time to the software or documentation made available on the OpenCart web site.

RM assumes no responsibility for errors or omissions in the software or documentation available from its web site.

This software includes a Single Domain License. Use on multiple domains prohibited without written authorization from the author.

========================================================

INTRODUCTION:

Allows customers to save credit cards in your database.

========================================================

SUPPORTED PAYMENT GATEWAYS:

* Authorize.net (AIM)
* PayPal Payments Pro (UK)
* PayPal Payments Pro Payflow Edition
* PayPal Website Payment Pro
* Perpetual Payments
* Sage Payment Solutions (US)
* SagePay Direct
* VirtualMerchant
* Web Payment Software

Additional gateways may be developed upon request.

========================================================

INSTALLATION:

Requires vQmod - https://code.google.com/p/vqmod/

1. Upload the contents of "upload" to your OpenCart folder.
2. See: Extensions > Modules > Saved Credit Cards

========================================================

NOTES:

* Developed on 1.5.6 but should work for all 1.5.x versions
* Once a card is saved, only masked version is displayed
* Should work with most themes

========================================================

SUPPORT:

http://forum.opencart.com/ucp.php?i=pm&mode=compose&u=16030

========================================================

UPDATES:

Jul-16-2014
  1. Rewritten as an installable module.
  2. Now includes adapters for all applicable core payment gateways.
  3. Also includes adapter for my VirtualMerchant payment module
     http://www.opencart.com/index.php?route=extension/extension/info&extension_id=602
  4. Added configuration value for standalone encryption key (no longer relies on store key).
  5. Changing encryption key purges all saved cards from the database.
  6. Uninstalling the module will remove the database table.
Mar-28-2014
  1. BUGFIX: "Save Card" option was only displayed if customer manually added cards via My Account
  2. DOCS: Updated documentation to cover how to modify the extension for renamed "admin" folders
Nov-15-2013
  1. Initial release.