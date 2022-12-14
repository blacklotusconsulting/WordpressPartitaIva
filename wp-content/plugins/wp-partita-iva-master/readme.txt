===  Partita Iva per Fattura Elettronica  ===
Contributors: blacklotusconsulting
Tags: fattura-elettronica,fattura,elettronica,woocommerce,woocommerce-fattura-elettronica,codice-fiscale,pec,iva,codice-univoco,codice-cliente,piva,fatturazione-elettronica,fattura-woocommerce,woocommerce-vat,vat-number,vat-number-woocommerce
Requires at least: 5.1
Tested up to: 6.0.2
Requires PHP: 7.1
Stable tag: 1.2.2
Version:     1.2.2
Author:      Alessandro Romani
Author URI:  https://www.blacklotus.eu
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Plugin Name: Partita Iva per Fattura Elettronica
Plugin URI:  https://github.com/blacklotusconsulting/WordpressPartitaIva
Donate Link: https://www.paypal.com/donate/?business=PZXF7XXABDC7C&no_recurring=0&currency_code=EUR

Description:

Partita Iva per Fattura Elettronica adds to the Woocommerce standard checkout form some custom fields(VAT Number, Fiscal Code, NIN Code and PEC email address) required for the italian market.
It is possible to choose the fields that will be displayed in the checkout form and set as mandatory field for each field.
Data are stored as Woocommerce Order Meta.
Requires Woocommerce

Partita Iva per Fattura Elettronica Aggiunge il supporto per l'inserimento nel form di pagamento di Woocommerce dei campi Partita IVA, Codice Fiscale, Codice Cliente e indirizzo PEC, necessari alla fatturazione elettronica in Italia.

Con questa plugin in versione gratuita vengono aggiunte al form di pagamento standard di Woocommerce le seguenti funzionalit√†:
1) Inserimento del campo Codice Fiscale nel form di pagamento di Woocommerce con salvataggio del dato nei metadati dell'ordine
2) Inserimento del campo Partita Iva nel form di pagamento di Woocommerce con salvataggio del dato nei metadati dell'ordine
3) Inserimento del campo Indirizzo PEC nel form di pagamento di Woocommerce con salvataggio del dato nei metadati dell'ordine
4) Inserimento del campo Codice Univoco Iva nel form di pagamento di Woocommerce con salvataggio del dato nei metadati dell'ordine
5) Inserimento del campo Checkbox per la richiesta di fattura elettronica. Se valorizzato in fase di acquisto verr√† inviata una mail all'indirizzo dell'amministratore del sito con i dettagli per la fatturazione elettronica.
6) Pannello di controllo per abilitare/disabilitare la visibilit√† e l'obbligatoriet√† dei campi nel form di pagamento.

Per il corretto funzionamento √® necessaria l'installazione di Woocommerce.

== Frequently Asked Questions ==
Does this plugin require Woocommerce?
Yes, it requires Woocommerce.

Is this plugin FREE?
Yes, it is free. If you want you can buy me a chocolate cake donating at this link https://www.paypal.com/donate/?business=PZXF7XXABDC7C&no_recurring=0&currency_code=EUR

Does this plugin create a PDF of the invoice?
Nope. It just save VAT Number, Fiscal Code, NIN Code and PEC email address as order meta data.

== Changelog ==
1.2.2 Added support for Wordpress 6.0.2. and Woocommerce 6.8.2
1.2.1 Added more information on the readme file, Plugin Banner and Plugin Icon.
1.2 Fix incorrect Stable Tag. Variables and options escaped when echo'd
1.1 Fixed code to Sanitize Escape and Validate data.
1.0 First release.

== Upgrade Notice ==
No upgrade notice so far

== Screenshots ==
1.Form Custom Fields in Order Page
2.Settings to enable/disable and make custom Fields mandatory in Wordpress settings page
3. Admin order page with saved Custom Fields as order meta