@push('script')
<script type="text/javascript">
    var currency = document.getElementById('currency').dataset.defaultCurrency;


    $(document).ready(function(){
        $(document).on('keyup', '.cash-collection', function(e) {
            e.preventDefault();
            if($('#fragile').is(':checked')){
                var fragile = 1;
            }else{
                var fragile = 0;
            }

            var url = path;
            var formData = {
                merchant     : $('.merchant').val(),
                parcel_type  : $('.parcel_type').val(),
                weight       : $('.weight').val(),
                cod          : $(this).val(),
                packaging    : $('.packaging').val(),
                fragile      : fragile
            }
            $.ajax({
                type: "GET",
                dataType: 'json',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url + '/' + 'charge-details',
                success: function (data) {
                    $('#cash-collection-charge').html(data['cod']);
                    $('#current-payable-charge').html(currency+ ' ' +data['payable']);
                    $('#delivery-charge').html(data['charge']);
                    $('#cod-charge').html(data['cod_charge']);
                    $('#vat-charge').html(data['vat']);
                    $('#total-delivery-charge').html(data['total_delivery_charge']);

                    $('#packaging-charge').html(data['packaging_charge']);
                    $('#frazile-charge').html(data['frazile_charge']);
                },
                error: function (data) {
                }
            });

		});

        $(document).on('change', '.merchant, .parcel_type, .weight, .packaging', function(e) {

            if($('#fragile').is(':checked')){
                var fragile = 1;

            }else{
                var fragile = 0;
            }
            var url = path;
            var formData = {
                merchant    : $('.merchant').val(),
                parcel_type : $('.parcel_type').val(),
                weight      : $('.weight').val(),
                cod         : $('.cash-collection').val(),
                packaging   : $('.packaging').val(),
                fragile     : fragile
            }
            $.ajax({
                type: "GET",
                dataType: 'json',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url + '/' + 'charge-details',
                success: function (data) {
                    $('#cash-collection-charge').html(data['cod']);
                    $('#current-payable-charge').html(currency+ ' ' +data['payable']);
                    $('#delivery-charge').html(data['charge']);
                    $('#cod-charge').html(data['cod_charge']);
                    $('#vat-charge').html(data['vat']);
                    $('#total-delivery-charge').html(data['total_delivery_charge']);

                    $('#packaging-charge').html(data['packaging_charge']);
                    $('#fragile-charge').html(data['fragile_charge']);
                },
                error: function (data) {
                }
            });

        });

        $(document).on('click', '#fragile', function(e) {

            if($('#fragile').is(':checked')){
                var fragile = 1;

            }else{
                var fragile = 0;

            }

            var url = path;
            var formData = {
                merchant    : $('.merchant').val(),
                parcel_type : $('.parcel_type').val(),
                weight      : $('.weight').val(),
                cod         : $('.cash-collection').val(),
                packaging   : $('.packaging').val(),
                fragile     : fragile
            }


            $.ajax({
                type: "GET",
                dataType: 'json',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url + '/' + 'charge-details',
                success: function (data) {

                    $('#cash-collection-charge').html(data['cod']);
                    $('#current-payable-charge').html(currency+ ' ' +data['payable']);
                    $('#delivery-charge').html(data['charge']);
                    $('#cod-charge').html(data['cod_charge']);
                    $('#vat-charge').html(data['vat']);
                    $('#total-delivery-charge').html(data['total_delivery_charge']);

                    $('#packaging-charge').html(data['packaging_charge']);
                    $('#fragile-charge').html(data['fragile_charge']);
                },
                error: function (data) {
                }
            });

        });
    });
</script>
@endpush
