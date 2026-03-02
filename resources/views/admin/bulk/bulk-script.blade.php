@push('script')
<script>
    $(document).ready(function () {
        // Add these flags at the top
        let isProcessing = false;
        let lastScannedCode = '';
        let lastScanTime = 0;
        let scanTimeout;

        // Barcode scan handler - use more specific event binding
        $('.barcode').on('keypress change', function (e) {
            // Only process on Enter key (keycode 13) or change event
            if (e.type === 'keypress' && e.which !== 13) {
                return;
            }
            
            e.preventDefault();
            
            // Prevent multiple simultaneous scans
            if (isProcessing) {
                console.log('Already processing a scan, ignoring...');
                return;
            }
            
            var $this = $(this);
            var parcel_no = $this.val().toUpperCase().trim();
            var currentTime = new Date().getTime();
            
            if (!parcel_no || parcel_no.length < 5) return;
            
            // Check for duplicate scan within 2 seconds
            if (parcel_no === lastScannedCode && (currentTime - lastScanTime) < 2000) {
                console.log('Duplicate scan prevented');
                $this.val('');
                return;
            }
            
            // Check if already in list
            if ($("#row_" + parcel_no).length) {
                Swal.fire('Oops...', '{{ __('already_added_to_list') }}', 'error');
                $this.val('');
                return;
            }
            
            // Update scan tracking
            lastScannedCode = parcel_no;
            lastScanTime = currentTime;
            isProcessing = true;
            
            // Clear input immediately to prevent re-triggering
            $this.val('');
            
            var url = $('#url').val() ?? path;
            var add_url = $this.attr('data-url');
            var val = parseInt($('#parcels').attr('data-val') || 0) + 1;
            var formData = { val: val };

            console.log('Processing parcel:', parcel_no);

            $.ajax({
                type: "GET",
                dataType: 'json',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url + '/' + add_url + parcel_no,
                success: function (data) {
                    if (data.error == true) {
                        Swal.fire('Oops...', data.message, 'error');
                        return;
                    }

                    // Double-check if row was added by another request
                    if ($("#row_" + parcel_no).length) {
                        Swal.fire('Oops...', '{{ __('already_added_to_list') }}', 'error');
                        return;
                    }

                    $('#parcels').append(data.view);
                    $('#parcels').attr('data-val', data.val);

                    // Add hidden input with parcel ID
                    if (data.parcel && data.parcel.id) {
                        // Remove any existing hidden input with same ID (just in case)
                        $('input[name="parcels[]"][value="' + data.parcel.id + '"]').remove();
                        
                        $('#parcel-form').append(
                            '<input type="hidden" name="parcels[]" value="' + data.parcel.id + '">'
                        );
                        
                        console.log('Added parcel ID:', data.parcel.id);
                    }
                },
                error: function (xhr) {
                    console.error('AJAX error adding parcel:', xhr);
                    Swal.fire('Error', 'Failed to add parcel', 'error');
                },
                complete: function() {
                    // Release processing lock after a delay
                    setTimeout(function() {
                        isProcessing = false;
                    }, 1000);
                }
            });
        });

        // Clear barcode field on focus
        $('.barcode').on('focus', function() {
            $(this).val('');
        });

        // Remove row handler — also remove hidden input
        $(document).on('click', '.delete-btn-remove', function () {
            var row = $(this).attr('data-row');
            
            // Find the parcel ID from the row's data attribute
            var parcelId = $('#' + row).data('parcel-id');
            
            if (parcelId) {
                $('input[name="parcels[]"][value="' + parcelId + '"]').remove();
            }
            
            $('#' + row).remove();
            
            // Update data-val
            var currentVal = parseInt($('#parcels').attr('data-val') || 0);
            $('#parcels').attr('data-val', currentVal - 1);
            
            console.log('Removed parcels[] with ID:', parcelId);
        });

        // Merchant change handler (if it adds multiple parcels)
        $(document).on('change', '.pickup-merchant', function (e) {
            e.preventDefault();
            var merchant = $(this).val();
            var url = $(this).attr('data-url');

            if (!merchant) return;

            var formData = { merchant: merchant };

            $.ajax({
                type: "GET",
                dataType: 'json',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                success: function (data) {
                    if (data.error) {
                        Swal.fire('Oops...', data.message, 'error');
                        return;
                    }

                    // Clear old parcels and old hidden inputs
                    $('#merchant-parcels tbody').empty();
                    $('input[name="parcels[]"]').remove();

                    // Add the new HTML rows
                    $('#merchant-parcels tbody').append(data.view);

                    // Find IDs from the newly added rows and create hidden inputs
                    $('#merchant-parcels tbody tr').each(function(index) {
                        var parcelId = $(this).data('parcel-id');
                        
                        if (parcelId) {
                            $('#parcel-form').append(
                                '<input type="hidden" name="parcels[]" value="' + parcelId + '">'
                            );
                        }
                    });
                    
                    // Update data-val
                    var rowCount = $('#merchant-parcels tbody tr').length;
                    $('#parcels').attr('data-val', rowCount);
                },
                error: function (xhr) {
                    console.error('AJAX error adding merchant parcels:', xhr);
                }
            });
        });

        // Prevent Enter key submitting form prematurely
        $('#parcel-form').on('keyup keypress', function(e) {
            var keyCode = e.keyCode || e.which;
            if (keyCode === 13) {
                e.preventDefault();
                return false;
            }
        });

        // Debug on submit — prevent empty submit
        $('#parcel-form').on('submit', function (e) {
            const parcelCount = $('input[name="parcels[]"]').length;
            console.log('Submitting form - parcels count:', parcelCount);
            console.log('Parcel IDs being sent:', 
                $('input[name="parcels[]"]').map((i, el) => el.value).get()
            );

            if (parcelCount === 0) {
                Swal.fire('Warning', 'No parcels selected! Scan or add parcels first.', 'warning');
                e.preventDefault();
                return false;
            }
        });

        // Reset scanning on page unload
        $(window).on('beforeunload', function() {
            if (scanTimeout) {
                clearTimeout(scanTimeout);
            }
        });
    });
</script>
@endpush