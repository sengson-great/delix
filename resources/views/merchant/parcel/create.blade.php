@extends('backend.layouts.master')

@section('title')
    {{ (@$parcel ? __('duplicate') : __('add')) . ' ' . __('parcel') }}
@endsection

@section('mainContent')
    <div class="container-fluid">
        <div class="row gx-20">
            <div class="col-lg-12">
                <div class="header-top d-flex justify-content-between align-items-center mb-12">
                    <h3 class="section-title">{{ @$parcel ? __('duplicate') : __('add') }}
                        {{ __('parcel') }}
                    </h3>
                    <div class="oftions-content-right">
                        <a href="{{ url()->previous() }}" class="d-flex align-items-center btn sg-btn-primary gap-2">
                            <i class="las la-arrow-left"></i>
                            <span>{{ __('back') }}</span>
                        </a>
                    </div>
                </div>
                <form action="{{ route('parcel.store') }}" class="form-validate" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="merchant" value="{{ Sentinel::getUser()->merchant_id ?? Sentinel::getUser()->merchant->id ?? '' }}" class="merchant">
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="card bg-white redious-border p-20 p-sm-30 pt-sm-30">
                                <div class="row g-gs">
                                    <!-- Invoice Number -->
                                    <div class="col-6 mb-3">
                                        <label class="form-label" for="customer_invoice_no">{{ __('invoice') }}#</label>
                                        <input type="text"
                                            class="form-control @error('customer_invoice_no') is-invalid @enderror"
                                            id="customer_invoice_no"
                                            value="{{ old('customer_invoice_no', @$parcel->customer_invoice_no) }}"
                                            name="customer_invoice_no" 
                                            placeholder="{{ __('invoice_or_memo_no') }}">
                                        @error('customer_invoice_no')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Shop Dropdown -->
                                    <div class="col-6 mb-3">
                                        <label class="form-label" for="shop">{{ __('shop') }} <span class="text-danger">*</span></label>
                                        <select class="form-select form-control select-shop @error('shop') is-invalid @enderror"
                                                name="shop" id="shop_select" required>
                                            <option value="">{{ __('select_shop') }}</option>
                                            @if(isset($shops) && $shops->count() > 0)
                                                @foreach ($shops as $shop)
                                                    <option value="{{ $shop->id }}" 
                                                        data-branch="{{ $shop->branch->name ?? '' }}"
                                                        data-phone="{{ $shop->shop_phone_number ?? '' }}"
                                                        data-address="{{ $shop->address ?? '' }}"
                                                        {{ old('shop') == $shop->id ? 'selected' : '' }}
                                                        {{ isset($parcel) && $parcel->shop_id == $shop->id ? 'selected' : '' }}
                                                        {{ (!isset($parcel) && isset($default_shop) && $default_shop->id == $shop->id) ? 'selected' : '' }}>
                                                        {{ $shop->shop_name }}
                                                        @if($shop->default)
                                                            ({{ __('default') }})
                                                        @endif
                                                    </option>
                                                @endforeach
                                            @else
                                                <option value="" disabled>{{ __('no_shops_available') }}</option>
                                            @endif
                                        </select>
                                        @error('shop')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Customer Name -->
                                    <div class="col-6 mb-3">
                                        <label class="form-label" for="customer_name">{{ __('customer') . ' ' . __('name') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('customer_name') is-invalid @enderror"
                                            id="customer_name"
                                            value="{{ old('customer_name', @$parcel->customer_name) }}"
                                            name="customer_name" 
                                            placeholder="{{ __('recipient_name') }}" required>
                                        @error('customer_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Customer Phone -->
                                    <div class="col-6 mb-3">
                                        <label class="form-label" for="customer_phone_number">{{ __('customer') . ' ' . __('phone') }} <span class="text-danger">*</span></label>
                                        <input type="number"
                                            class="form-control @error('customer_phone_number') is-invalid @enderror"
                                            id="customer_phone_number"
                                            value="{{ old('customer_phone_number', @$parcel->customer_phone_number) }}"
                                            name="customer_phone_number"
                                            placeholder="{{ __('recipient') . ' ' . __('phone') }}" required>
                                        @error('customer_phone_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Delivery Area Dropdown -->
                                    <div class="col-6 mb-3">
                                        <label class="form-label" for="parcel_type">{{ __('delivery_area') }} <span class="text-danger">*</span></label>
                                        <select class="form-select form-control @error('parcel_type') is-invalid @enderror" 
                                                name="parcel_type" id="parcel_type" required>
                                            <option value="" selected disabled>{{ __('select_type') }}</option>
                                            <option value="same_day" {{ old('parcel_type', @$parcel->parcel_type) == 'same_day' ? 'selected' : '' }}>{{ __('same_day') }}</option>
                                            <option value="sub_city" {{ old('parcel_type', @$parcel->parcel_type) == 'sub_city' ? 'selected' : '' }}>{{ __('sub_city') }}</option>
                                            <option value="outside_city" {{ old('parcel_type', @$parcel->parcel_type) == 'outside_city' ? 'selected' : '' }}>{{ __('sub_urban_area') }}</option>
                                        </select>
                                        @error('parcel_type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Weight Dropdown -->
                                    <div class="col-6 mb-3">
                                        <label class="form-label" for="weight">{{ __('weight') }} <span class="text-danger">*</span></label>
                                        <select class="form-select form-control weight @error('weight') is-invalid @enderror" 
                                                name="weight" id="weight" required>
                                            <option value="">{{ __('select_weight') }}</option>
                                            @if(isset($charges) && $charges->count() > 0)
                                                @foreach ($charges as $charge)
                                                    <option value="{{ $charge->weight }}" 
                                                        data-charge="{{ $charge->charge ?? 0 }}"
                                                        {{ old('weight', @$parcel->weight) == $charge->weight ? 'selected' : '' }}>
                                                        {{ $charge->weight }} {{ setting('default_weight') ?? 'kg' }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                        @error('weight')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Cash Collection -->
                                    <div class="col-6 mb-3">
                                        <label class="form-label" for="price">{{ __('cash_collection') }} ({{ setting('default_currency') }}) <span class="text-danger">*</span></label>
                                        <input type="number" step="0.01"
                                            class="form-control cash-collection @error('price') is-invalid @enderror"
                                            id="price"
                                            value="{{ old('price', @$parcel->price ?? 0) }}"
                                            name="price"
                                            placeholder="{{ __('cash_amount_including_delivery_charge') }}" required>
                                        @error('price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Selling Price -->
                                    <div class="col-6 mb-3">
                                        <label class="form-label" for="selling_price">{{ __('selling_price') }} ({{ setting('default_currency') }})</label>
                                        <input type="number" step="0.01" class="form-control" id="selling_price"
                                            value="{{ old('selling_price', @$parcel->selling_price) }}"
                                            name="selling_price" 
                                            placeholder="{{ __('selling_price_of_parcel') }}">
                                    </div>

                                    <!-- Customer Address -->
                                    <div class="col-6 mb-3">
                                        <label class="form-label" for="customer_address">{{ __('customer') . ' ' . __('address') }} <span class="text-danger">*</span></label>
                                        <textarea class="form-control @error('customer_address') is-invalid @enderror"
                                            id="customer_address" 
                                            placeholder="{{ __('recipient') . ' ' . __('address') }}"
                                            name="customer_address" required>{{ old('customer_address', @$parcel->customer_address) }}</textarea>
                                        @error('customer_address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Note -->
                                    <div class="col-6 mb-3">
                                        <label class="form-label" for="note">{{ __('note') }}</label>
                                        <textarea class="form-control" id="note"
                                            placeholder="{{ __('note') . ' (' . __('parcel_note_from_merchant') . ')' }}"
                                            name="note">{{ old('note', @$parcel->note) }}</textarea>
                                    </div>
                                </div>

                                <!-- Checkboxes -->
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="fragile" name="fragile" value="1"
                                                {{ isset($parcel) && $parcel->fragile == 1 ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="fragile">
                                                <span class="text-capitalize">{{ __('liquid') }}/{{ __('fragile') }}</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="row">
                                    <div class="col-md-12 text-right mt-4">
                                        <button type="submit" class="btn sg-btn-primary">{{ __('submit') }}</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Sidebar - Charge Details & Pickup Info -->
                        <div class="col-md-4">
                            <!-- Charge Details Card -->
                            <div class="card bg-white redious-border p-20 p-sm-30 pt-sm-30">
                                <div class="card-title-group mb-2">
                                    <div class="card-title">
                                        <h6 class="title">{{ __('charge_details') }}</h6>
                                    </div>
                                </div>
                                <div class="card-inner p-0">
                                    <table class="table">
                                        <tr>
                                            <th>{{ __('title') }}</th>
                                            <th>{{ __('amount') }}</th>
                                        </tr>
                                        <tr>
                                            <td>{{ __('cash_collection') }}</td>
                                            <td><span id="cash-collection-charge">0.00</span></td>
                                        </tr>
                                        <tr>
                                            <td>{{ __('delivery_charge') }}</td>
                                            <td><span id="delivery-charge">0.00</span></td>
                                        </tr>
                                        <tr>
                                            <td>{{ __('total_delivery_charge') }}</td>
                                            <td><span id="total-delivery-charge">0.00</span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ __('current_payable') }}</strong></td>
                                            <td><strong id="current-payable-charge">{{ setting('default_currency') }} 0.00</strong></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <!-- Pickup Information Card -->
                            <div class="card bg-white redious-border p-20 p-sm-30 pt-sm-30 mt-4">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">{{ __('pickup_branch') }}</label>
                                    <input type="text" class="form-control" id="shop_pickup_branch" readonly>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">{{ __('pickup_number') }}</label>
                                    <input type="text" class="form-control" id="shop_phone_number" readonly>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">{{ __('pickup_address') }}</label>
                                    <input type="text" class="form-control" id="shop_address" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    console.log('Page loaded - Dropdowns initializing...');
    
    // Log available data
    console.log('Shops available:', $('.select-shop option').length - 1);
    console.log('Weights available:', $('#weight option').length - 1);
    
    // Initialize any select2 if available
    if ($.fn.select2) {
        $('.form-select').select2({
            width: '100%',
            placeholder: '{{ __('select_option') }}'
        });
    }
    
    // Handle shop change
    $('.select-shop').on('change', function() {
        var selectedOption = $(this).find(':selected');
        var shopId = $(this).val();
        
        console.log('Shop selected:', shopId);
        
        if (shopId) {
            // Get data from option attributes
            var branchName = selectedOption.data('branch') || '';
            var phoneNumber = selectedOption.data('phone') || '';
            var address = selectedOption.data('address') || '';
            
            console.log('Shop data:', {branch: branchName, phone: phoneNumber, address: address});
            
            // Update fields
            $('#shop_pickup_branch').val(branchName);
            $('#shop_phone_number').val(phoneNumber);
            $('#shop_address').val(address);
        } else {
            // Clear fields
            $('#shop_pickup_branch').val('');
            $('#shop_phone_number').val('');
            $('#shop_address').val('');
        }
    });
    
    // Trigger change on page load if shop is pre-selected
    if ($('.select-shop').val()) {
        console.log('Triggering initial shop load for ID:', $('.select-shop').val());
        $('.select-shop').trigger('change');
    }
    
    // Handle weight change
    $('#weight').on('change', function() {
        var selectedOption = $(this).find(':selected');
        var deliveryCharge = parseFloat(selectedOption.data('charge')) || 0;
        
        console.log('Weight selected - Delivery charge:', deliveryCharge);
        
        $('#delivery-charge').text(deliveryCharge.toFixed(2));
        calculateTotal();
    });
    
    // Handle cash collection change
    $('.cash-collection').on('keyup change', function() {
        calculateTotal();
    });
    
    // Calculate total function
    function calculateTotal() {
        var cashCollection = parseFloat($('.cash-collection').val()) || 0;
        var deliveryCharge = parseFloat($('#delivery-charge').text()) || 0;
        
        $('#cash-collection-charge').text(cashCollection.toFixed(2));
        $('#total-delivery-charge').text(deliveryCharge.toFixed(2));
        
        var payable = cashCollection - deliveryCharge;
        $('#current-payable-charge').text('{{ setting("default_currency") }} ' + (payable > 0 ? payable.toFixed(2) : '0.00'));
        
        console.log('Total calculated:', {cash: cashCollection, delivery: deliveryCharge, payable: payable});
    }
    
    // Trigger initial calculation if weight is pre-selected
    if ($('#weight').val()) {
        $('#weight').trigger('change');
    }
});
</script>
@endpush

@include('admin.parcel.charge-script')