{literal}
<script type="text/javascript">
    window.addEventListener('load', onloadEvent, false);
    var data_submitted = false;

    function onloadEvent(){
        document.asyncReady(function() {
                $( ".register--form" ).submit( function(formval) {

                  if(data_submitted == false &&  $('#register_personal_customer_type').val() == 'private'){

                      formval.preventDefault();
                      var abortSubmit = false;
                      var params = {
                          billing : {
                              firstname : $('#firstname').val(),
                              lastname : $('#lastname').val(),
                              street : $('#street').val(),
                              zipcode : $('#zipcode').val(),
                              city : $('#city').val(),
                              country : $('#country').val(),
                          },
                          shipping : {
                              useShippingAdress : $('#register_billing_shippingAddress').is(':checked') ? true : false,
                              firstname : $('#firstname2').val(),
                              lastname : $('#lastname2').val(),
                              street : $('#street2').val(),
                              zipcode : $('#zipcode2').val(),
                              city : $('#city2').val(),
                              country : $('#country_shipping').val(),
                          },
                      };
                      $.ajax({
                          dataType : "json",
                          type : 'post',
                          data : jQuery.param({ eap_adresscheck : params }),
                          contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
                          url: "/../Adressvalidation",
                          beforeSend: function( xhr ) {
                              xhr.overrideMimeType( "text/plain; charset=x-user-defined" );
                          }
                      })
                          .done(function( data ) {
                              if(data.actionRequired == true) {
                                  $.modal.open(data.htmlModal, {
                                      title: 'Adressverifizierung',
                                  });
                                  return false;
                              }else{
                                  data_submitted = true;
                                  $("#register--form").submit();
                              }
                          });
                  }
                });

            });

        }

</script>
{/literal}