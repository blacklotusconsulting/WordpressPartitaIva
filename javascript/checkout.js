jQuery(document).ready(function($) {
    function toggleFields() {
        var customerType = $('input[name="billing_customer_type"]:checked').val();
    //    var campo_cf = $('#billing_cf_field');
        var campo_piva = $('#billing_vat_field');
   //      var campo_nin = $('#billing_nin_field');
   //     var campo_pec = $('#billing_pec_field');

    //    var cf = $('input[name="billing_cf"]');
        var piva = $('input[name="billing_vat"]');
     //   var nin = $('input[name="billing_nin"]');
      //  var pec = $('input[name="billing_pec"]');

        if (customerType == 'business') {
          //  campo_cf.removeClass('validate-required');
            campo_piva.addClass('validate-required');
          // campo_nin.addClass('validate-required');
          //  campo_pec.addClass('validate-required');

            //cf.attr('aria-required', 'true');
            piva.attr('aria-required', 'true');
          //  nin.attr('aria-required', 'true');
          //  pec.attr('aria-required', 'true');
        } else {
          //  campo_cf.addClass('validate-required');
            campo_piva.removeClass('validate-required');
           // campo_nin.removeClass('validate-required');
          //  campo_pec.removeClass('validate-required');

           // cf.removeAttr('aria-required');
            piva.removeAttr('aria-required');
          //  nin.removeAttr('aria-required');
          //  pec.removeAttr('aria-required');
        }
        // Modifica le label dei campi obbligatori
        toggleRequiredLabels();
    }
    function toggleRequiredLabels() {
        //var fieldsToToggle = ['#billing_cf_field', '#billing_vat_field', '#billing_nin_field', '#billing_pec_field'];
        var fieldsToToggle = ['#billing_vat_field'];

        $.each(fieldsToToggle, function(index, field) {
            var label = $(field).find('label');

            if ($(field).hasClass('validate-required')) {
                label.html(label.html().replace('(opzionale)', '*'));
            } else {
                label.html(label.html().replace('*', '(opzionale)'));
            }
        });
    }


    toggleFields();

    $('input[name="billing_customer_type"]').change(function() {
        toggleFields();
    });
});