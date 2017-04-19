------------------------------------------------------------------------
Order Entry System v2.3.4
------------------------------------------------------------------------

SUPPORTED OPENCART/VQMOD VERSIONS
---------------------------------
Opencart v1.5.x, vQmod v2.4.1

INSTALLATION
------------
1) Copy all of the files to your Opencart directory.  No core Opencart files are overwritten.

2) Make sure you have vQmod v2.2.1 installed - this is now required

3) Go to your System, Users, User Groups.  Click the Top Administrators edit link and check the boxes in both access/modify for sale/order_entry

4) Open Order Entry to allow any new database fields to be created

5) Go to the System, Localisation, Order Statuses and add a new order status for Quotes

6) Go to the System, Settings, store edit link, Order Entry tab and set any of the settings you may need/wish to set.  You must set at least the
   columns for the order list.  You must also set the Quotes Order Status.  Use the order status you created in step 5.
   
7) Make sure you edit the config.php and admin/config.php files if using Opencart 1.5.5.1 and add the following back to the file:
   
   Under the // HTTP section
   
   define('HTTP_IMAGE', 'http://www.yourdomain.com/path/image/');
   
   Under the // HTTPS section
   
   define('HTTPS_IMAGE', 'http://www.yourdomain.com/path/image/');

   Obviously, change the www.yourdomain.com to your actual domain and the path to your actual opencart path.  You can use the other entries as a guide
   Just make sure you include the image/ at the end as above.
   
8) If your configured payment methods use an image instead of text, you will need to add a new language item to your catalog language file for the
   payment method.  The new item to add is $_['text_backup_title'] = 'Whatever you want to call the payment method'.  This is what will be shown in
   the drop-down box for Payment Method in Order Entry as images cannot be displayed in drop-down boxes


ORDER ENTRY FEATURES
--------------------
1) Create new orders/quotes and edit any existing orders/quotes.  You can either convert a Quote to a sale if the customer pays you directly or you
can have the customer login to their account and pay for the order on the catalog side.  On the customers Order List screen, there will be a $ icon for any order that has not been paid.  They can click this icon to take them to the checkout so they can pay for the order.  There is also a Pay Now button located on the Order History page for any orders marked as a Quote
 
2) Add products to the order from your storefront by doing the following:
	a) Login to your Admin
	b) Login to the storefront as your customer by using the Sales, Customers, Customers and select the store to login to.  This will open a new
	   tab in the same browser.  Do not close this tab until you are done processing the order.  Alternatively, you can open a new tab for your storefront and create the cart as a guest.  Do not add anything to the cart at this point!  Just login and leave the tab open.
	c) Open Order Entry and leave on the order list screen
	d) Go back to your storefront tab and add items to the cart.  Once done adding items, do not close the tab.
	e) After all items needed are added to the cart, go back to the Admin tab
	f) Click Create Order or Create Quote button
	g) Select the customer (if you created cart as a guest).  If you logged in as a customer, the customer information will auto-populate
	h) Click on the Refresh Cart button if the products from the cart do not show up automatically
	i) Complete the order/quote

3) Add products to the order that may not exist in your catalog.  This is useful to add a custom, one time item that you may not want to add to your store catalog
 
4) Include optional fees and discounts.  Add as many as you would like, no limits.  Checkbox included for each added fee/discount for easy remova from the order.  Apply fees and discounts before or after taxes.  Include discount on shipping option

5) Process credit card payments from Order Entry using any of the following payment gateways:
	- Authorize.net AIM
	- Authorize.net AIM Simple
	- Cardsave Hosted
	- eProcessing Network
	- Moneris API
	- MyGate
	- PayPal Payflow Pro
	- PayPal Pro
	- PayPal Pro UK
	- PayPal Standard
	- Payson
	- Perpetual Payments (CashFlows)
	- Sagepay Direct
	- Total Web Secure
	- USAePay
	- WorldPay
 
6) Accept partial payments using the 3rd party Payment System module.  I developed this module for the seller a while ago and decided to use this as the base for the partial payment system in Order Entry.  It does require that you purchase this module, but we have done extensive testing and made numerous modifications to both Order Entry and the Payment System module to make them fully compatible.  The Payment System module allows you to name the payment system anything you want via the payment method settings page and this will change the text anywhere it is displayed on your store
 
7) Change the currency being used on an order by order basis.  Any currency you have installed on your store can be used to create an order.  So if your store uses the USD but you want to send a quote to someone in Euros, you can change the currency for the quote and it will show the quote in Euros
 
8) Create new customers from Order Entry when creating an order if the customer does not exist.  Your customer will receive an email with their account information.  Their account password is auto-generated and included in the email with a link to the "Change Password" page

9) Add a new shipping address or update an existing address in Order Entry.  This will update the currently selected address in the address table or if you add a new address, will create another address for the selected customer
 
10) Email, Print, and Export your orders.  You can select multiple orders to email, print, and export.  Export function can be modified using a vQmod XML file to include any order data you would like.  Depending on complexity of request, an additional fee could be required.  For simple changes to the exported file, no fee will be charged.

11) Apply coupons, vouchers, store credit, reward points to the order
 
12) Uses the storefront shipping files so all shipping methods you have installed should work without any modification.

13) Order Totals are automatically calculated for the order
	 
14) Uses your tax, shipping, and payment rules.  You can override taxes being charged using the Tax Exempt checkbox on the order form.  You can also override the taxes for a specific product on the order
	 
15) Stock is automatically subtracted (if you have Subtract Stock set to Yes) when an order is placed and added back if you delete the order.  The stock is also adjusted if you edit an order and add/subtract/remove products from the order
	 
16) Invoice has been updated to include your store logo
 
17) Removed the code that automatically generated invoice numbers.  This will now work like the standard Opencart where you have to manually generate the invoice number if necessary

18) A lot of configuration options under the System, Settings, store edit link, Order Entry tab
	
19) Multi-store compatible

 
VERSION HISTORY
---------------------------------------
August 13, 2015 - v2.3.4
	- ADDITIONS:
		1) Add images to custom products.  After adding a custom product to the order form, click the eye icon and choose the image you want to use for the custom product and click
		   Save.  Image will be saved with the order and available when editing the order or printing the invoice/packing slip
		
	- FIXES:
		1) Fixed issue with new customer form on multistore sites not using the correct store name, email, url when sending the email to the customer
		2) Fixed NUMEROUS issues with the PDF Invoice Pro module and Order Entry.  Must be using the latest version of PDF Invoice Pro.  Ask for modified files if you use this module
		   and it is not working with Order Entry
		3) Fixed issue with product image options causing an undefined index error
		4) Fixed two issues related to store credits. First issue was with the code being added to the catalog/model/total/credit.php file causing an undefined variable "text"
		   message.  The second issue was applying a store credit on an edited order.  The customer transaction table was not being updated so the applied credit was not being
		   subtracted from the customers store credit balance
		5) Fixed issue with new orders in Order Entry not marking the order as paid/unpaid based on the payment method setting for this.  Edited orders will still use the checkbox
		   for marking orders as paid/unpaid

February 4, 2015 - v2.3.3
	- ADDITIONS:
		1) Added a stock warning message if you try to enter a quantity for a product with not enough stock
		2) Updated Order Entry to work with the latest version of the Package Tracking service module (moduloom).  You must use the lastest version to be compatible with Order Entry
		3) Added ability to choose affiliates for orders.  Affiliate commission will be calculated and affiliate will be emailed with their commission information for the order
		4) Added support for Czech Post shipping method
		5) Added support for Category Product Based shipping method
		6) Added new setting: Show Shipping Charges with tax.  You can now set the shipping charges to be shown including taxes just like you can with the products
		7) Added new setting: Show Sub-Total Charges with tax.  You can now set the sub-total charges to be shown including taxes just like products and shipping
		8) Added support for the Paymentbased_fee order total module
		9) Added support for the Advanced Discounts & Fees order total module
	   10) Added new order list column: Customer ID.  Can now filter by Customer ID as well as show the customer id on the order list screen
	   11) Added some new features for the Admin Notes module including a red exclamation point next to an order with notes, the ability to save the note without having to save the
	       order, and the ability to delete notes from within the order
	   12) Added ability to override certain order totals.  There is a new, red exclamation point next to order totals that you can override.  You can either set to 0 (override) or
	       full amount (no override) at this time.  I may add the ability to change the amount to another value at some point
	   13) 
		
	- FIXES:
		1) Fixed issue with SagePay Direct and MasterCard credit cards not sending the correct card type to SagePay causing an error - The PaymentSystem invalid
		2) When I added the ability to choose order table fields for exporting, I didn't add the shipping cost, taxes, or sub-total order totals to the list.  These have been added
		   back and can now be selected for exporting
		3) Fixed issue with custom products on quotes.  When customer would checkout, the weight was being calculated incorrectly in some cases
		4) Fixed issue with the Moneris API payment method and the order_entry_payments.xml file.  There seem to be different versions of this payment method available so I had to
		   add code to support the other version as well as the original version the code was intended for
		5) Fixed issue with Guest checkout and using the Dropship function.  Was not saving the correct shipping address when processing the order and subsequently was showing the
		   payment address when editing the order
		6) Fixed issue with languages not showing up in the Change Language drop-down box and the correct language not being selected when editing an order
		7) Fixed issue with overriding the product line weights using the cart weight box not showing up on the order list correctly
		8) Fixed issue with product names not showing up in the correct language on the customer emails
		9) Fixed issue with product names with special characters not displaying correctly on order form after updating a value on the product line
	   10) Fixed several issues with product stock and quotes.  Setting the Subtract Stock for Quote to no didn't work as intended and was adding the stock back when set to No when
	       deleting a quote
	   11) Fixed a bug when saving a product with a SKU on the product line, the SKU was not showing up
	   12) Fixed a bug where disabled items were still showing up on the order form.  This is where you are able to disable the Create Order button, Create Quote button, etc.  These
	       were still showing up even if you set the correct user group and selected the items to be disabled
	   13) Fixed a bug with the order export function where some of the order table fields were not being exported properly and causing an error
	   14) Fixed a bug with the Payment Date column filter function as it was not working
	   15) 

September 26, 2014 - v2.3.2
	- ADDITIONS:
		1) Added a Map View button on the order list screen for each order.  Will display a map showing the address location
		2) Added an override function for setting the paid/unpaid status and order status when using Cash, Pending, Offline CC, and PayPal
		   Email Link payment methods.  If you manually set the order status and/or the paid/unpaid status of an order, this value will
		   override the Order Entry settings for those payment methods
	
	- FIXES:
		1) Fixed issue with new order and edit options function causing an undefined index: edit_order message
		2) Fixed the "Delete" function in the Order History section.  It was accidentally removed in the last release
		3) Fixed issue with edited Quotes causing an error on the catalog side when customer logs in to pay.  Only happened with edited quotes

September 19, 2014 - v2.3.1
	- ADDITIONS:
		1) Added support for the Quickcheckout module
		2) Added support for PDF Invoice Pro
		3) Added support for Pretty HTML Email
		4) Added support for Purchase Order System by MarketInSG
		5) Added a new setting to combine the firstname and lastname in one column on the Export to CSV function.  If checked, the firstname and
		   lastname will combined in the same column on the exported CSV file
		6) Added 4 new settings: Cash Payment, Offline CC Payment, PayPal Link Payment, Pending Payment.  The new settings allow you to choose
		   the default paid/unpaid status for each method as well as the order status to use for that payment method.  On a new order, this will
		   override the order status selected during the order creation if you do change the order status.  Edited orders will allow use the
		   order status chosen on the order form regardless of these settings
		7) Added "Create Order" button on the customer form (Sales, Customers, Edit) to start an order for that customer
		8) Added a new order list filter: Invoice Number
		9) Added comments to the packing slip
	   10) Added ability to edit products with options on existing orders
	   11) Added ability to include negative product quantities or decimals
	   12) Added new setting: Allow 0 qty products.  Setting this to No will prevent any products with 0 quantity or less from showing up when
	       adding products to the order using autocomplete.  Setting this to Yes will allow you to add products with 0 quantity or less to the
		   order
	   13) Added new setting: Require telephone.  With this setting, you can make the telephone field required when creating a new customer from
	       Order Entry
	   14) Added two new "save" buttons when adding a customer.  One will start a new order with the created customer and the other will start a
	       new quote with the created customer
	
	- FIXES:
		1) Fixed issue with Global Reward points not auto-assigning reward points to customer accounts when order status is changed in Order Entry
		2) Fixed an issue with Options Boost and the option_price_only setting.  With this set to yes, the products were showing up as 0.00 when
		   editing an order
		3) Fixed text issue when hovering over the Convert to Quote icon on the order list screen
		4) Fixed the subject and greeting text for Quote emails and invoices so it is more clear that they are quotes and not orders
		5) Fixed an issue when setting Optional Fees/Discounts box to not show, was still showing up on order form
		6) Several fixes for product weight and cart weight issues
		7) Fixed issue with "undefined index: location" when printing packing slip

June 9, 2014 - v2.3.0.1
	- ADDITIONS:
		1) Added Moneris API payment method support
		2) Added a new setting: Process/Save Button.  With this setting, you can determine how to handle the order form after processing an edited
		   order.  There are two options: Save and Close, Save and Keep Open.  Save and Close will close the order form after processing is
		   completed.  Stay and Keep Open will keep the order form open after processing the changes
		
	- FIXES:
		1) Fixed a language issue with the action icons on the order list screen.  Made the TITLE and ALT tags language based variables and using the
		   REL tag now to determine which icon is being used.  So you can now successfully translate the TITLE and ALT tags for the icon images using
		   the language file
		2) Major fixes for the tracking number/tracking url code.  Somewhere along the lines I broke this.  I have fixed it.  While fixing my shipper
		   tracking code, I also fixed the 15x_Tracking_System xml file for Order Entry

June 6, 2014 - v2.3.0
	- ADDITIONS:
		1) Added a "refresh" icon next to the pencil icon.  Also changed the way clicking the pencil works.  Now you will be taken to a new window
		   with the customer information where you can add a new address or edit the existing information.  When done, save the customer and go back
		   to the Order Entry window.  You can then click the refresh icon and the new/edited address will be added to the drop-down
		2) Completed the Dropship function - these addresses will not be saved to the customers address table if used but will be used for the current
		   order
		3) Added a new order form field and settings: Purchase Order.  You can choose to show or hide the form field and the orders can be filtered
		   by a partial or complete purchase order number
		4) Added ability to convert a regular sale to a quote
		5) Added support for the WorldPay standard module (redirected to WorldPay website for payment information)
		6) Modified the default Opencart customer form and added an additional emails box which can be used to add commonly used email addresses for
		   sending order confirmations and invoices.  This data will then be used to auto populate the additional email box in Order Entry on the
		   order form
		7) Added an email to be sent to the admin when a quote is paid for by the customer
		8) Added new setting: Order Table Export fields.  You can now choose any of the order table fields to include on the exported order list.  If
		   you do not choose any table fields for this, there is a default set of fields that will be exported
		9) Added support for the Cardsave Hosted payment method.  This is similar to PayPal Standard in that you are directed to the Cardsave site 
		   where you can input the payment details, process the payment, and then be returned to Order Entry
	   10) Added Czech and German language files - thanks to those that translated
	   11) Added a new setting to allow you to set a default store to use when creating a new order.  This is a multi-store setting only and will not
	       show up in single store sites.  This is under the System, Settings, store edit link, Order Entry tab at the top
	   12) Added a new setting: Customer Language.  This allows you to choose the language the emails, invoices, packing slips will be shown in based
	       on your customers language.  The languages shown will be the languages you have installed for your store. The order totals are not being
		   translated as they are saved in the order totals table in the language you enter the order in.  Same with product names on the order
	   13) Added support for the Admin RMA module
	   14) Added a function to check payment method titles for <img> tags.  If found, a backup title will be used so it shows up in the drop-down box
	       for Payment Method in Order Entry.  In order to use this correctly, you will need to add a $_['text_backup_title'] to the catalog language
		   file for the payment method
		
	- FIXES:
		1) The issue with adding more custom products to an order with custom products already was increasing the quantity of an existing custom
		   product instead of adding the new custom product to the order
		2) Fixed an issue with the "Do not apply taxes" checkbox being clicked and still applying taxes to applicable shipping methods.  There was
		   also an issue if adding a discount/optional fee and selecting to apply before or after taxes causing a JSON error.  This has been resolved
		   as well
		3) Fixed layout issue in Google Chrome when printing invoices/packing slips
		4) Fixed the Professional HTML Email template code so you no longer need the special file to use Order Entry with the Professional HTML Email
		   module.  If you currently are using the order_entry_with_email_template_support.xml file, you will need to delete it after uploading the
		   new files
		5) Fixed issue with Bank Transfer and Check/Money Order payment methods where instructions were not being put on the confirmation email when
		   orders were created in Order Entry.  These instructions now show up properly on the confirmation emails and invoices
		6) Fixed issue with Quote icon when using another language other than English in your Admin.  Now if you have multiple languages and set the
		   Quote Order Status setting to your language, the $ will show up
		7) Changed some code for the "options" to be compatible with Mijoshop.  Thanks to Alan for providing these fixes
		8) Fixed several language items that were not in the language files but were hard coded
		9) Numerous fixes for language based Quote order status issues both in the Admin and on the Catalog side
	   10) Made some improvements to the email invoices, print invoices, and packing slips in Order Entry.  This was all CSS related changes to display
	       better in a wider range of email clients and browsers

April 1, 2014 - v2.2.9
	- ADDITIONS:
		1) Put in code to fix an issue where using the same browser and being in an order in Order Entry and trying to checkout on the catalog side
		   using the same browser was causing numerous errors
		2) Added support for Advanced Coupons
		3) Added ability to disable certain parts of the order form based on user group.  This will allow you to create a user group and hide certain
		   parts of the order form from that user group
		4) Added a new order_entry.xml file which needs to be requested by you IF you use the Professional HTML Email template system by Opencart
		   Templates.  This adds support back for the Professional HTML Email template system
		5) Added support for Dependent Options - requires a modified dependent_options.xml file that you can request from me
		6) Added a new setting to allow you to choose how the autocomplete function returns results.  You can do a %search% or search%.  The
		   difference is with the %search%, it will search the entire string for any match.  This match could be at the beginning, in the middle, or
		   at the end.  With the search%, it will search from the beginning and match on the beginning only.  Options are Yes to use %search% or No to
		   use search%
		
	- FIXES:
		1) Fixed issue with product names or model numbers containing an apostrophe not displaying correctly on the order form
		2) Fixed issue with PayPal Standard payment method in Order Entry causing a JSON error
		3) Fixed issue with PayPal Standard, both catalog side and in Order Entry, not logging the PayPal Transaction ID
		4) Fixed issue with database table modifications not working correctly on fresh install
		5) Fixed layout issue with Google Chrome for the order list screen
		6) Fixed issue with comments not being saved if you edit something else on the form after entering order comments
		7) Fixed issue with the red, multi-order buttons at the top not showing "Quote" instead of "Invoice" on the emails and invoices for Quotes
		8) Fixed issue with text, textarea, file, date, datetime, and time option fields not showing up correctly on the product line when product is
		   added to an order.  Also fixes an issue when creating a quote and customer views on the catalog side with the values for these options not
		   showing up
		9) Fixed issue between OpenStock and Order Entry when adding products with options to the cart and then adding a product without options,
		   the product without options was taking the price of the previously added product with options
	   10) Fixed issue with the 15x Shipment Tracking module fix.  When the oe_15_shipment_tracker.xml was installed, it would remove the "delete"
	       action for the order history Action column.  Also fixed issue with the tracking numbers not showing up after being entered
	   11) Fixed several issues with the product options popup when adding a product with options to an order
	   12) Fixed issue with the Check and Purchase Order payment methods not showing the Apply Payment button therefore not allowing those methods
		
December 31, 2013 - v2.2.8
	- ADDITIONS:
		1) Added RFC822 validation routine for email addresses
		2) Added a "view image" icon next to product name on product line.  Clicking the view icon will popup a larger image of the product.  There is
		   a new setting under the Product section of the Order Entry settings to set the width x height of the popup image.  Will default to
		   280 x 280 if nothing is set in the settings		   
		3) Added new setting: Product Name field size.  This will default to size=25 if you do not specify a size in the settings
		4) Added new order list column: Customer Email.  Can filter the orders on email address now as well
		5) Added ability to specify the size of product images for the emails, invoices, and packing slips.  Choices are small (100x100), 
		   medium (200x200), and large (300x300) or None to not display the images
		6) Removed support for the Custom Email Template and HTML Email Template systems.  The newer versions of these modules do not work with
		   Order Entry and I do not have the time right now to make them work with Order Entry
		7) Added a module to allow your customers to request a quote on the catalog side
		8) Added section to display customer comments, if any left, from the order table
	
	- FIXES:
		1) Fixed issue with changing the store on a multi-store site
		2) Fixed issue when editing orders on Opencart 1.5.2.1 and earlier where undefined company_id and tax_id messages were being logged and
		   causing a JSON error
		3) Fixed issue with the checkbox for marking an order as paid or unpaid.  Was not saving when marking the order as "paid"
		4) Fixed the issues with the "undefined selected_currency" messages being logged, usually happening when using Guest on the order
		5) Fixed issue with the currency selector and custom pricing for a product.  The price entered was being converted from the store default
		6) More fixes for currency conversions on edited orders not calculating correctly
		7) Fixed issue with custom invoice number not showing up on default Opencart order view page
		8) Fixed several issues with the OpenStock module and Order Entry
		9) Fixed email issue with editing a quote and re-sending to the customer - the Buy Now button or link was not placed on the invoice.  This has
		   been fixed and the email will contain the link to the catalog side where the customer can log in and finalize the order
	   10) Fixed issue where customer comment in order table was being overwritten with Order Entry comments
	   11) Fixed issue where a product with a special price configured but a custom price was used on the order form, it would not show the custom
	       price when editing the order
	   12) Fixed issue with tracking number not being saved to Order History when editing an order

November 1, 2013 - v2.2.7
	- ADDITIONS:
		1) Added ability to search for customers on postcode and telephone number.  This is in addition to the already existing search by customer
		   name, company name, and address
		2) Added support for multi-lingual invoices/packing slips - the invoice will now be shown in the language of the customer that placed the
		   order.  The language for the customer is saved in the order table with the order and that is the language used on these now
		
	- FIXES:
		1) Fixed issue with the custom shipping method and FilterIt module.  Custom shipping does not require a shipping address be passed to it for
		   a quote and this was causing some issues with the FilterIt module when adding/modifying an order in Order Entry.  The address will now be
		   passed to the custom shipping method even though it is not used so it will not cause these issues
		2) Fixed issue where store has both a config_language_id and config_language configured.  This was causing a problem with the order email
		   and a "cannot find language file !" message
		3) Fixed issue created when I added code to support the Custom Unit module.  The product total was showing as NaN prior to saving the
		   product to the order.  I have fixed this issue and it now shows the correct product price * quantity
		4) Fixed issue with changing the product name when first adding a product to the order.  It wasn't saving the new product name.  You could
		   edit the product name and it would save correctly.  Now it will save correctly when first adding to the order
		5) Fixed issue when order is made on the catalog side, the total product weight was not being calculated and added to the order_product table
		   correctly when more than one of the item exists in the cart
		6) Fixed issues with PayPal Standard payment method on the catalog side not marking an order paid or with a valid order status.  However,
		   there are several things that need to be setup on your PayPal account in regards to Notifications and IPN.  Both of these must be set and
		   point to your website in order for the order to be marked appropriately and for PayPal to return the payment status to your site
		7) Changed the way addresses are shown on edited orders.  If the address on an order does not match an address for the customer, Order Entry
		   will show the address as it is on the order.  You will then have the option of updating this address using one of the customers currently
		   configured addresses or leaving it as is.  I also removed the ability to add/edit addresses within Order Entry. This function was creating
		   a lot of hassle and I need to re-think the best way to allow something like this
		8) Fixed issue with image/logo filenames with spaces not displaying on emails/invoices/packing slips
		9) Fixed issue with invoice dates on older orders where the date would show up as 01/01/1970

October 15, 2013 - v2.2.6
	- ADDITIONS:
		1) Added support for the Multiple Option Quantity module
		2) Added the invoice number and date to the order line (top of the order edit form) if the order has an invoice number.  Otherwise, this does
		   not show
		3) Added an invoice date field below the invoice number field.  You can manually assign an invoice number and date using these two fields.
		   This will override the autogenerated invoice number.  You can also clear the invoice number and date from the order by blanking out the
		   invoice number field and saving the order
		4) Added the following buttons to the Quote edit page: Email Quote, Print Quote, Export Quote
		5) Added the following buttons to the Order edit page: Email Order, Print Order, Print Packing Slip, Export Order
		6) Updated the Packing Slip - added settings to control the items displayed on the packing slip
		7) Added a new column and filter to the order list: Company
		8) Added keypress function on the filter line to allow you to press Enter to filter the order list
		9) Added support for the Custom Unit module
	   10) For orders using a currency other than the store default, added the conversion to the store default currency to the Dashboard view and 
	       default Opencart order list
		
	- FIXES:
		1) MAJOR: Fixes to the currency conversion functions
		2) Fixed issue where location field on the product line was hidden, would get a JSON error when adding a custom product to the order
		3) Fixed display of invoice number/date on invoices.  If not invoice number is set, these lines do not show up
		4) When saving an edited order, the order status line at the top will dynamically update to show any of the following updated values, if
		   changed: order date, order status, invoice number, and invoice date
		5) Fixed the filtering.  Now when editing an order from a filtered list, when you close the order form you will be returned to the filtered
		   list instead of the full list
		6) Fixed issue where choosing Cash Payment or Credit Card (Offline) was not marking the order as paid, which it should by default on newly
		   created orders.  Editing an order will require you to mark the order paid using the checkbox by the Save Changes button
		7) Fixed issue with the default order view function when an order is using an Order Entry custom payment method such as Cash Payment.  When
		   clicking on the View link, you would get a message about not being able to load controller payment/cash/orderAction.  This seems to only
		   affect Opencart v1.5.6.  Previous versions did not have this issue
		8) Several fixes for the HTML Email Templates module.  With the new changes, a modified emailtemplate.xml file is required.  If you use this
		   module, please email me for the modified xml file.  It will not work properly without this file
		9) Fixed issue with display of country on the order list screen if no shipping was required.  Will now check for shipping country first and 
		   if that doesn't exist, will use the payment country
	   10) Fixed several issues with custom shipping both on a new order and editing an order with custom shipping
	   11) Changed the buttons on creating a quote to say "Save Quote" instead of "Save Order"
	   12) Fixed issue with undefined variable: selected_currency when starting an order from the catalog side
	   13) Fixed issues with Total Sales, Total Sales by Year, Total Quotes, and Total Quotes by Year not adding up correctly if using multi-currency
	   14) Fixed issues with Order Total on default Opencart order list not showing the correct order total if using multi-currency
	   15) Aligned the filter by date and filter by total boxes on the filter line

September 20, 2013 - v2.2.5
	- ADDITIONS:
		1) Added a message to the successful order processing message when editing an order stating "You can either continue editing this order or
		   click the Close Order Form button".  The comments section already updates upon successful processing.  The order paid status will now also
		   be updated upon successful order processing
		2) Added the Order Entry manual to the distribution files
		
	- FIXES:
		1) Several more fixes for multi-store config settings
		2) Fixed issue when payment method was a cheque, the cheque details were not being shown after applying or during subsequent editing

September 16, 2013 - v2.2.4
	- ADDITIONS:
		1) Added new setting: Show Product Price + Tax on invoices.  Set to No (default) and the product lines on the invoice will show the product
		   price excluding tax.  Set to Yes and the product lines on the invoice will show the product price including tax
		2) Added new setting: Show Product Price + Tax on product line total.  Set to No (default) and the product total lines on the order form
		   will show price excluding tax.  Set to Yes and the product total lines on the product form will show the product price including tax
		3) Added new setting: Check Notify Customer checkbox by default.  Set to Yes to start order with the Notify customer checkbox already checked
		   and No to start the order with the Notify customer checkbox unchecked
		4) Added new setting: Show/Hide product form columns.  Required columns are Product Name, Model, Qty, Price, and Total.  The rest are optional
		   and can be set to show or hide
		5) Added new setting: Order Status for Quotes.  You can now set your own order status for Quotes.  It is no longer restricted to being set to
		   'Quote'.  Added for multiple language support
		6) Added support for the Global Reward Points module
		7) Added new column and filter to the order list screen for multi-stores: Store
		8) Custom shipping on a quote will now show up on the catalog side at checkout and be the default selected shipping method for your customer.
		   Your customer will be allowed to choose a different shipping method from the available options during checkout if they so desire.  The
		   order will be updated with the shipping method chosen, if different from the custom shipping method
		9) Added ability to delete Order History entries
	   10) When saving an edited order, the order form will remain on the same page as in the past but the form will update all the sections so you 
		   will be able to see any newly added comments, etc
	   11) Added support to save the product cost to the order_product table when processing an order
	   12) Added alternating color coded lines for the product lines
	
	- FIXES:
		1) Fixed an issue that affects Opencart 1.5.2.1 and earlier versions only.  There is an Opencart bug in the
		   catalog/controller/account/order.php file, specifically in the public function info() section where the $order_id variable is not set yet
		   but a redirect is attempting to access that variable.  This was causing problems when customers would click the link from a Quote email
		   to login to their account and pay for the quote.  It would give them an "order could not be found" error.
		2) Fixed issue where changing currency when editing an order would not save the new currency to the order table
		3) Fixed JSON syntax error when adding products from non-default store in a multi-store setup
		4) Fixed language issue in multi-store environment - non-default store order confirmation emails were causing a JSON error because the
		   language id was not being set correctly
		5) Fixed numerous issues with product prices and calculated totals (sub-total, taxes, total) when the default store currency was not being
		   used on the order.  This affected both existing products and custom products.  Previous orders may have incorrect totals when first edited.
		   If you save the order immediately, the totals will update correctly and then you can edit it again.  This should only affect previous
		   orders.  New orders will not have this issue
		6) Fixed issue with PayPal Email Link payment method and the HTML Email Template system.  The link and buttons would not show on these emails
		7) Fixed issues with the pressing of the Enter key causing you to be redirected to the login page
		8) Fixed issue with Openstock module and products with options pricing would not allow you to override when adding or editing an order.  Will
		   require the updated Openstock.xml file
		9) Removed Quotes from the last 10 orders list on the dashboard.  They shouldn't have been there as they aren't really orders!
	   10) Fixed issue with Openstock and products with multiple options.  The quantity wasn't showing up correctly for the multiple options
	   11) Fixed issue with editing an order with a product that did not have special pricing at the time the order was placed but was changed later
	       to have special pricing.  When editing the order, the special price was being used instead of the original sale price of the product for
		   that order
	   12) Fixed issue with order totals not displaying on the dashboard correctly when using a different currency from the default store currency

August 22, 2013 - v2.2.3
	- ADDITIONS:
		1) Added tighter security for the delete buttons in Order Entry.  If the logged in user is not a member of the Top Administrator group, they
		   will not be able to delete orders in Order Entry
		2) Added a PayPal Email Link payment option.  What this will do is include a payment button for PayPal in the order confirmation email to your
		   customer which they can then click and pay for the order via PayPal without visiting your site.  The PayPal payment page will show all of
		   the items being ordered, their cost, and the shipping/taxes total just like the regular PayPal Standard when used on your catalog side
		   checkout process.  The order will be updated upon successful payment as well
		3) Grouped the Order Entry custom payment methods together and separated from the configured payment methods for your site
		4) Organized the settings page and added section labels to group the settings more logically
		5) Added a message to go to the settings page after first installing Order Entry to setup your order list columns
		6) Added a setting to make the link to the order in a Quote a button or text link.  Button will put a Buy Now button in place of the text
		   link in the quote email.  Link uses the default text link in the quote email
		7) Added support for new payment methods: Authorize.Net AIM Simple and Purchase Order
		8) Added more settings: Show Invoice # and Show Order Date
		9) Added a Cash Payment method
		
	- FIXES:
		1) Fixed the filter name on the order list screen to convert the filter input to lowercase when performing the query
		2) Fixed product images not showing up in the emails even though show in emails is set to Yes
		3) Fixed the order confirmation email on 1.5.1.3 and earlier versions of Opencart
		4) Fixed the order status - when selecting an order status and changing some other data on the form, the order status would revert to the
		   previously chosen status
		5) Fixed the product_to_product table issue when adding/deleting an order if you do not have that table
		6) Fix to prevent a session or cookie for the currency from the catalog from being used while adding an order in Order Entry
		7) Fixed the subtract stock for a quote routine - when converting a quote to a sale with "subtract stock for a quote" set to No, it would
		   still add the product stock back before subtracting when it shouldn't have added it back
		8) Fixed the order emails not being sent to the store admin when a customer doesn't have an email address.  Now if the customer does not
		   have an email address and the notify box is checked, the admin will receive the order email and any emails entered in the additional emails
		   box on the order form will also receive the order confirmation/update email.  This will also fix the Error: RCPT TO messages when processing an order
		9) Fix for a search in the order_entry.xml file in the catalog/model/checkout/coupon.php file in Opencart 1.5.5.1+.  This was causing a JSON
		   error when processing an order or quote with a coupon applied.  Updated to work with both the newer and older version of the coupon query
	   10) Fix for a bug with adding a new address or updating an address for a customer in Order Entry
	   11) Fix for issue when using options to ask questions (no stock and not subtracting from stock).  This would cause the available stock status
	       to show 0 in stock which may not be correct.  Now it will skip any options set to a quantity of 0 AND No for subtract stock when doing
		   the available stock calculations
	   12) Fixed the override tax boxes - in case you didn't know, you can click on a tax in the totals and set a custom amount if needed
	   13) Fixed the date display for the order date at the top of the order edit screen.  Also moved the input to change the order date to the
	       Order Options section of the form
	   14) Several layout fixes for Guest checkouts with no shipping address required.  Also a couple of code fixes when no shipping address required 
	       is set to Yes
	   15) Fixed the price displays for products, optional fees/discounts, and custom shipping when using a currency that is not your store default

August 8, 2013 - v2.2.2
	- ADDITIONS:
		1) Added shipping method and cart weight column to available order list columns
		2) Added support for the following modules: Combo/Bundle for Opencart, Product Color Options, OpenStock, Canned Messages
		3) Added support for the following payment gateways: Payson, MyGate
		4) Pressing TAB key on an autocomplete field will select the highlighted autocomplete item
		5) Pressing ENTER key when adding a product will trigger the Save product button click
		6) After adding a product, focus will return to product name autocomplete box
		7) Added ability to change the order paid status on the order list screen.  Clicking on the Y or N in the order paid column will set it to the
		   opposite of what it currently is.  So if an order is marked as Paid (Y) and you click on it, it will be set to Not Paid (N) and vice versa.
		   Also added order filter support for the order paid column
		8) Added a function for the catalog side checkout when a customer is paying for a quote which will check for an optional discount.  If the
		   quote contains an optional discount, the customer will not be allowed to add a coupon to the order as well
	    9) Added a new method for bulk changing order statuses from the order list screen
	   10) Added a Buy Now button to the Quote email sent to the customer (replaces the text link in the email)
	   11) Added new setting to allow you to choose whether to subtract stock for a Quote or not
	   12) Updated to work with Opencart 1.5.6
		
	- FIXES:
		1) Fix for optional discounts not applying to shipping or taxes properly
		2) Fixed the order_status_id for a Quote paid via PayPal Standard on the catalog side where the order_status_id was being set to 0 regardless
		   of the payment status returned by PayPal for the order.  The order_status_id will now update to the correct value based on the payment
		   status returned by PayPal
		3) Fix for a problem where Optional Discounts could be applied more than once for a Quote on the catalog side if the customer clicks the "Pay
		   Now" button more than once or logged out and back in to their account
		4) Fix for stock quantities for products with options - now instead of showing the product stock quantity for all options, it will show the
		   lowest option quantity for the selected options of that product
		5) Fix for option weights not being added to product weight for shipping calculations
		6) Fix for the Total Web Secure payment gateway implementation for Order Entry
		7) Fix for filtering orders on customer firstname AND lastname.  If "test user" was used as the filter, it would not return any orders.  But
		   if "test" or "user" were used, it would return the correct orders.  With the fix, "test user" will also return the correct orders
		8) Fix for pagination issue when using order filters
		9) Fix for the Notify checkbox losing the checkmark if something else on the form is changed prior to processing/saving the order
	   10) Fix for not being able to add/edit an address from the order form when creating a new order

July 24, 2013 - v2.2.1
	- ADDITIONS:
		1) Added support for the Purolator shipping method
		2) Added support for the Total Web Secure payment gateway
		3) Added setting to hide the Cart Weight box
		
	- FIXES:
		1) More fixes for the reward point system
		2) Fix for additional emails not being sent if using the "use_catalog_email.xml" Order Entry modification file
		3) Changed the autocompletes back to the default, require only 1 character before searching
		4) Changed the order list customer filter function to search on both first and last names using the % wildcard on either end of the search
		5) Fix for Customer Ref box not being saved when adding a customer ref number and changing something else on the form before saving
		6) Changed the function to update order statuses from the order list screen.  Select the orders you would like to change the status for,
		   choose the order status you want to set the orders to from the drop-down order status box in the filter section and the selected orders
		   will be updated to this status
		7) Fix for issue where Hide Zones is set to No but doesn't show up when Require Shipping Address is set to No as well
		8) Fix for showing tracking numbers on the order list screen
		9) Fix for customer paying for Quote on catalog side and coupon, voucher and/or optional discount/fees not showing up on catalog side

July 3, 2013 - v2.2.0
	- ADDITIONS:
		1) Added a "Shipped" checkbox to the product line
		2) Added setting to allow you to auto-generate invoice numbers when you print the order invoice - invoice date added to the invoice template.
		   Leave set to No to use the default Opencart invoice generating system
		3) The text in the customer autocomplete box will now disappear when you click in the box to search for a customer as long as a customer is
		   not already selected.  This was requested by numerous people
		4) Added support for ext2store extension.  Will be bypassed if using Order Entry but still active for your store front customers
		5) Added functionality to check for both specials and discounts and apply the best option for the customer if eligible for both a special
		   price and discount price based on quantity ordered
		6) Added an invoice number box on the order form where you can override the default Opencart invoice system and use custom invoice numbers.
		   If invoice number is entered in box on form, the invoice prefix and invoice number default to Opencart will not be used and this number
		   will be
		7) Added function allowing you to change the order date
		8) Added support for the Custom Email Templates module
	    9) Added support for the Multiple Flat Rate Options shipping method
	   10) Added new settings to choose the columns displayed on the Order List screen
	   11) Added a bunch of new filters on the Order List page
	   12) Added new setting and ability to not require an address when adding a new customer.  This will cause errors if your products require
	       shipping, so make sure to set this to Yes in the settings if you require shipping.  If you do not have products that require shipping, you
		   can make the address an optional entry when adding a new customer
		
	- FIXES:
		1) Fixed the location and weight not populating when adding a product to the order
		2) Fixed an issue with the new weight functions causing an undefined "index" notice
		3) Fixed a packing slip issue if payment_company_id and payment_tax_id are not available
		4) Fixed issue with weight name displaying multiple times in the shipping / payment section
		5) Fixed issue when customer has multiple addresses, the selected address was not being used if it was not the default customer address
		6) Fixed issue with the xml file modifying the catalog/model/checkout/voucher.php file.  There is a slight difference in this file between
		   the various Opencart versions.  Updated the XML file to take this into account and update the file correctly based on OC version
		7) Fixed issue when new customer added with no email address, was causing JSON errors and the order entry form would not populate with the
		   added user
		8) Fixed quantity issues when editing an order - was showing incorrect product quantity available
		9) Fixed issue when editing an order with a gift voucher
	   10) Fixed issue with the weights showing times the quantity on the product line (should have been individual weight, not combined weight)
	   11) Fixed issue with editing order allowing you to choose the "No Customer Account" dropdown option causing all kinds of errors and not
	       allowing subsequent edits of the order because of the missing information.  This option was never intended to be selected but was for
		   orders prior to order entry or for customers that no longer have an account on the store
	   12) Fixed issue with the round function on products without a weight set
	   13) Fixed issue on stores with no existing customers not showing the drop-down box to add new customers or use guest checkout
	   14) Fixed issue when adding additional emails, if you change anything on the order prior to processing the order, the additional emails would
	       no longer be available
	   15) Fixed issue when changing payment method from cheque to another method, the check information was not being removed from the order
	   16) Fixed invoice number field on main order list screen to not show anything if no invoice number is set
	   17) Fixed several issues with the function when allowing a customer account without email would cause email errors (obviously) within Order
	       Entry as well as put in some code to allow you to edit the customer account without an email address if not required
	   18) Fixed issue with editing orders where rewards points were used on the order
	   19) Changed the language of the Process Order buttons to Save Changes when editing an order and removed the redirect to the order list screen
	       after saving the order

May 20, 2013 - v2.1.6.1
	- ADDITIONS:
		1) NONE
		
	- FIXES:
		1) Fixed issue where coupon and voucher were not showing even when setting was set to yes

May 19, 2013 - v2.1.6
	- ADDITIONS:
		1) Added checkbox to the Optional Fee section to allow you to add the fee/subtract the discount from Shipping charges as well
		2) Added ability to modify the model and/or product name when adding product to order
		3) Added location and weight fields to product line
		4) Added a second Process Order button at the top of the order form
		5) Added a customer edit function to edit the customer information.  Links to the customer edit page in Opencart
		6) Added line to add check #, check date, and bank name if payment method is Cheque / Money Order
		7) Added a date/timestamp to the orders.csv export file
		8) Added ability to change order status from the Order Entry orders list screen (main screen)
	    9) Added support for Total Based Shipping, Bill Me payment method, 1.5.0.x Shipment Tracking system
	   10) Changed the function for adding / updating products.  Updates are done via AJAX and you can update any of the input box information for the
	       product.  Removed the edit, save, and cancel buttons on the product lines.
	   11) Changed the layout of the Order History / Order Information boxes to take up less space
	   12) Added the cart weight to the shipping / payment section and this value can be changed for shipping calculations (weight and unit)
	   13) Added ability to modify product weight on product line (weight and unit)
	
	- FIXES:
		1) Fixed issue with tax calculations on Opencart 1.5.2.1 and earlier
		2) Fixed permissions for deleting orders.  Users now need modify permissions in order to delete orders
		3) Fixed issue in versions of Opencart without Company ID and Tax ID fields
		4) Fixed issue where editing an order with checkbox options, the options were being added to subsequent products in the order list (only 
		   happens with options using the checkboxes)
		5) Fixed issue with checkout on 1.5.1.3.1 and earlier versions of Opencart

April 30, 2013 - v2.1.5
	- ADDITIONS:
		1) None
		
	- FIXES:
		1) Fixed issue when Customer dropdown box is hidden, the buttons on the New Customer form do not function properly
		2) Fixed issue with Quote and customer account on the catalog side not working with Shoppica2 theme

April 28, 2013 - v2.1.4
	- ADDITIONS:
	    1) Added ability to create a Quote for your customer and allow your customer to pay for the order on the catalog side by logging in to their
	       account, viewing their order list and clicking on the $ icon or viewing the order history for the order and clicking on the Pay Now button
		2) Changed the autocomplete for customer.  Can now search by name, company, or email address in the same autocomplete box
		3) Added support for the OCA Manufacturer Notification module
		4) Added some more config options: include product images on emails and invoices, show/hide the coupon and/or voucher sections on the order
		   form, show/hide customer reference number, show/hide tax exempt, show/hide optional fees, disable customer/company dropdowns, show missing
		   orders (orders with an order_status_id of 0), color code order totals, show/hide the currency selector, require customer email when adding
		   new customer in Order Entry
		5) Added support for Installments module
		6) Added Missing Orders to the Order Status filter (if setting Show Missing Orders is set to Yes, this will show up)
		7) Added support for Admin Notes module (separate module from Order Entry as it is a standalone module).  This allows you to keep internal
		   notes and is separate from the Notify Customer in the order history section.  These notes are not passed on to the customer at any time
		8) Added support for the eProcessing Network gateway and PayPal Payflow Pro
		9) Added Mark Paid, Mark Unpaid checkbox override for marking an order as paid or unpaid.  This will prevent the credit card box from coming
		   up if an order is not marked as paid
	   10) Moved currency selector from top header bar to the order form
	   11) Added order #, date added, and order status to top header bar when editing an order
		
	- FIXES:
		1) MAJOR FIX: Fixed a memory issue when adding a lot of custom products.  Would run out of memory after about 4 or so products on systems with
		   64M of memory.  Tested up to 15 products with the fix on a 64M system with no memory errors
		2) Fixed problem with exporting CSV file for orders and the Euro and Pound symbols displaying incorrectly in the CSV file
		3) Moved Order Status line to the Order History section
		4) Fixed the customer search by name as it was not working properly.  Only company and email were returning results
		5) Fixed some "unserialize offset" errors when editing older orders
		6) Fixed issue with custom products on Opencart versions 1.5.1.3.1 and earlier
		7) Fixed the tracking information in the Order History.  Will show up on order emails and invoices
		8) Fixed the packing slip store url.  Some orders showed admin/ in the url.  Code was added to remove this from the url
		9) Fixed issue with orders not being marked as paid from the catalog side and when edited, was requesting credit card information again (if
		   credit card order)
	   10) Fixed issue where you could clear the customer name field and still save the order without a customer and causing a JSON error
	   11) Fixed issue with Add/Edit address link not showing up after selecting a customer
	   12) Fixed issue when editing an order that a store credit was applied to, the store credit did not show up on the edited order and the order
	       totals were wrong
	   13) Fixed issue adding disabled products in Order Entry

March 24, 2013 - v2.1.3
	- ADDITIONS:
		1) Added support for the Package Tracking Service module - need an updated vqmod/xml/moduloom_package_tracking.xml file.  Email me to get
		   this file.  Only needed if you use the Moduloom Package Tracking module and want to enter tracking details using Order Entry
		2) Changed the layout of the Order History section
		
	- FIXES:
		1) Fix for the product line tax checkbox issue where the box is checked even if a product is not taxable
		2) Added a fix for the HKPost shipping module
		3) Had several requests for the original search results when searching for a product so I added back the original code for this
		4) Fixed issue with editing an order with a custom product and adding another custom product overwrites the original custom product.
		   Additional custom products added worked fine
		5) Fixed the shipment tracking information to make sure it is saved to the order history and is included on the initial order email (if set)
		6) Fixed the displaying of the store logo on emails/invoices for Opencart 1.5.5.1
		7) Fix for the window.open function when trying to print an invoice or packing slip in IE.  Was just opening and closing the window before
		   you could print

March 14, 2013 - v2.1.2
	- ADDITIONS:
		1) Added a new setting under System, Settings, store edit link, Order Entry allowing you to choose whether or not to use disabled products in
		   Order Entry.  Set to Yes to allow disabled products to be added to the order.  Set to No to keep disabled products from being added
		2) Pressing enter on any field when adding a product to the order will cause the product to be added to the order
		3) Added support for the Order Status Color Coding module
		4) Added sales agent support to show which admin user added the order
		5) Added support for the Product Bundle module
		6) Added an additional emails box.  This will allow you to add additional emails to send the order notifications to
		7) Added tracking line where you can enter a tracking number and url
		
	- FIXES:
		1) Fixed bug with the multi-store selector causing JSON error messages when changing stores and an issue causing numerous undefined index:
		   payment_address messages
		2) Fixed bug with turning an existing order into a Payment System (Partial Payment) order - order status will automatically update to your
		   selected order status for Payment System orders
		3) Fixed bug with orders paid by credit card in full allowing the card to be charged again.  Orders paid in full with a credit card or PayPal
		   Standard will not allow you to apply another payment or change the payment method
		4) Fixed bug allowing you to apply a 0 layaway payment
		5) Fixed bug that prevented the showing of the checkbox to switch between drop-downs and autocompletes for customer selection
		6) Fixed bug that would let you select one of the blank spaces in the customer dropdown causing a bunch of undefined variable/index messages
		7) Removed the Store column from the order list for single store sites.  Multi-store sites will still have this column
		8) Fixed the overwriting of two images in the admin/view/image folder.  All images have been renamed with an _oe to prevent Opencart files
		   from being overwritten

March 5, 2013 - v2.1.1
	- ADDITIONS:
		1) Added the USAePay payment gateway and PayPal Standard
		2) Convert an existing, regular sale to a partial payment plan sale.  This is not reversible once converted to a partial payment sale and also
		   requires the 3rd party Payment System module in order to do partial payments
		3) Added the following options/changes to the Optional Fee/Discount line:
			a) Changed tax checkbox "Include taxes on fee:".  This checkbox will only be shown for a fee (+ or +%)
			b) Added a new checkbox "Apply discount before taxes:".  This checkbox will only be shown for a discount (- or -%)
			c) Removed Sort Order:  Fees will automatically sort based on the above checkbox selections.  If you choose to "include taxes" for a fee
			   or "apply before taxes" for a discount, the amount will be shown before the taxes in the totals section.  If you choose to not tax the fee or apply the discount after taxes, the amount will be shown before the total in the totals section
		4) Added drop-down in the Customer Billing section to allow you to change the billing address
		5) Added a setting in the System, Settings, store edit link, Order Entry tab to allow you to disable the zones in Order Entry
		6) Can now be used with the HTML Email Template system by Opencart Templates without giving a JSON error when processing an order
		
	- FIXES:
		1) Fixed the maximum partial payment deposit to not allow the amount entered to be greater than the order total
		2) Changed the minimum partial payment deposit to allow you to override the minimum deposit amount in Order Entry but keep the minimum deposit
		   amount setting for the storefront (will allow a deposit of 0 or greater)
		3) Fixed the comment box so that if you make other changes to the form before processing, it won't remove any entered comments
		4) Fix applied if the Partial Payment system is not installed and the getLayaway function does not exist
		5) Fixed the Optional Fees/Discount sort orders and pre or post tax processing
		6) Fixed the shipping method box.  Made the "shipping titles" optgroups instead of options to keep them from being selected (thanks for the
		   nudge tombenson)
		7) Numerous layout changes/fixes
		8) Fixed the payment button to not be visible if not using the Partial Payment system as this step is not needed when all you need to do is
		   select a payment method.  This only added an extra step for stores that do not use the Partial Payment system

February 26, 2013 - v2.1.0
	- ADDITIONS:
		1) Added an optional XML file to redirect the default Opencart order list Edit link to Order Entry for editing.  If you do not want this
		   feature, do not copy the default_order_edit_changes.xml file
		2) Added ability to change the currency used on the order.  Uses currencies setup for your store
		3) Added a company select box for selecting a customer by company name
		4) Added order comment box and associated order history
		5) Added notify customer checkbox so you can choose whether to notify a customer by email when an order is created or updated
		6) Added a customer reference number field (optional)
		7) Accept partial payments using the following Opencart extension
			http://www.opencart.com/index.php?route=extension/extension/info&extension_id=7904&filter_search=Layaway#.USwH0qL_mSo
		8) Added a Pending Payment option - this will effectively put the order on hold while you wait for payment
		9) Added the following credit card processing gateways: PayPal Pro, PayPal Pro UK, Authorize.net, Perpetual Payments, Sagepay Direct.
		   Additional payment gateways can be added for a small fee
		
	- FIXES:
		1) Fixed issue with Guest Checkout giving a JSON error
		2) Fixed another currency issue, this time with the shipping methods
		3) Fixed the following issues with the Optional Fees/Discounts:
			a) When selecting the - amount option, was not being applied and showing up as 0
			b) Optional Fees/Discounts were not being saved to the Order Totals table and weren't being show in invoices or default OC order info
			c) Shows the correct monetary symbol based on your selected currency
			d) Discount not being applied before taxes when given a sort order that would place it before taxes
		4) Fixed the autocomplete queries for customer, company, and products
		5) Several other small bug fixes were made

February 20, 2013 - v2.0.7

	- ADDITIONS:
		1) None
		
	- FIXES:
		1) Fixed issue with the totals section display two different currencies

February 19, 2013 - v2.0.6

	- ADDITIONS:
		1) Added the Invoice ID column back to the main Order list screen
		2) Added the Store Name to the main Order list screen (for multi-store support)
		3) Added support for image options
		4) Added a packing slip button and template
		5) Added the stock status and quantity when editing a product in the order
		6) Added two new config options under System, Settings, Order Entry tab to allow you to choose whether or not to allow adding products with
		   options where the option has 0 or less stock and to set the Tax Class for the Optional Fee lines if tax is applied to those
		7) Added Optional Fee/Discount function.  You can apply optional fees and/or discounts to the order.  Add as many as you would like.  Also
		   includes a checkbox for each added fee/discount to allow you to remove from the order
		
	- FIXES:
		1) Fixed issue with adding multiple custom products where the last one entered would overwrite the previous one
		2) Fixed issue with printing multiple orders where characters other than UTF8 exist would cause a NULL response
		3) Fixed issue with taxes not being applied if payment or store address are used instead of shipping address
		4) Fixed issue with store filter on order list showing the total orders for all stores regardless of which store was selected.  Now when the
		   store filter is used, it will show the total orders for that store only
		5) Updated the instructions for using the storefront to create the cart used in Order Entry (#3 of the feature list in this document)
		6) Fixed issue when starting an order with a cart created on the storefront, the taxes weren't being applied to products if applicable
		
February 6, 2013 - v2.0.5

	- ADDITIONS:
		1) Added support for checkbox, text, date, and datetime options
		
	- FIXES:
		1) Fixed several issues with the customer order confirmation email including the wrong link sending the customer to your admin and logos
		   displayed on multi-store sites
		2) Fixed issue with adding a product that doesn't exist in your catalog.  If you only entered a sku or entered no sku at all, was resulting
		   in a JSON error, "Undefined offset 1" message
		3) Fixed issue where SKU wasn't being saved to the order_product table for a custom product (product not in your catalog)
		4) Fixed issue with Stocklimiter and Order Entry to allow them to work together
		5) When editing an order, the options were not always showing up.  Fixed it so that options using checkboxes, radio, text, date, and 
		   datetime fields would show up as well as select options
		6) Fixed to work with the Profit and Reporting mod
		7) Several more fixes for earlier versions (1.5.1.3.1 and earlier)
		8) Fixed issue with frontend cart where stock checkout is set to No but customers can still checkout

January 23, 2013 - v2.0.4

	- ADDITIONS:
		1) Updated to work with 1.5.5.1 (will not work with 1.5.5)
	
	- FIXES:
		1) More fixes for earlier versions (before 1.5.1.3) of Opencart
		2) Fix for "Undefined index:" error when adding/removing taxes from a specific product on an order with a coupon
		3) Fix for adding a product with taxes regardless if you checked the box or not
		4) A couple more fixes for "tax" related issues
		5) Fix for the table modifications where "Duplicate field 'shipping_code'" was being logged
	
January 21, 2013 - v2.0.3

	- ADDITIONS:
		1) Added a "Tax" field on the product entry line.  Choose to include or exclude taxes on a product by product basis
		2) Add a product not in your catalog.  Useful if the item is a one time, custom item that you do not want to add to your catalog
		3) Support for multi-store configurations
		4) Add a new address for the selected customer or edit the selected address
		
	- FIXES:
		1) Fix for "Undefined index: customer_group_id" when using Guest as the customer
		2) A bunch of fixes for earlier versions of Opencart (1.5.0.x)
		3) Fix for a coupon that was applied to an order but is no longer active/missing
		4) Fix for missing/disabled products on an order
		5) Fix for "Undefined index: payment_address_id" on the storefront checkout confirm page

January 17, 2013 - v2.0.2

	- ADDITIONS:
		1) Product prices will now include taxes if set to "Display Prices with Tax" in your admin settings
		2) Custom Shipping method added back (will also be used if editing an order prior to installation of this version)
 
	- BUG FIXES:
		1) Fixed "Undefined index: payment_method" notice
		2) Fix if a product on a prior order is now disabled or has been removed from your product catalog
		3) Fix to use Order Entry with the Restrict Totals module
		4) More fixes for editing orders for customers without an account
		5) Fix if an order status was not selected during order creation, was causing an undefined index message.  Will now use default order status
		6) Fix for prices displaying taxes.  Was adding the tax a second time if editing a product already added to the order
	
January 10, 2013 - v2.0.1

 - ADDITIONS:
	1) Add items to the order form from the storefront
	2) Added error handlers for all the AJAX calls
	3) Added a message when waiting on an AJAX request to let you know it is doing something
	
 - BUG FIXES:
	1) Editing existing orders where a customer may not have an account on your store and/or no shipping_address_id exists
	2) Problem loading certain shipping, payment, or order total storefront language/model files
	3) "Use of undefined constant DIR_CATALOG" has been fixed
	4) Incorrect Order Date on the exported CSV file


January 9, 2013 - Initial Release

Report Bugs or Comments/Suggestions
---------------------------------------
develop@acfddev.com

