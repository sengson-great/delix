<div>
    <a
        href="{{ Sentinel::getUser()->user_type == 'merchant' ? route('merchant.parcel.detail', $parcel->id) : route('merchant.staff.parcel.detail', $parcel->id) }}">
        <span>{{ __('id') }}:#{{ $parcel->parcel_no }}</span><br>
        <!-- <span>{{ __('invno') }}:{{ $parcel->customer_invoice_no }} 
        </span><br>-->
        <span>{{ $parcel->created_at != '' ? date('M d, Y h:i a', strtotime($parcel->created_at)) : '' }}
        </span><br>
        <!-- @if ($parcel->parcel_type == 'frozen')
            <span>{{ __('pickup') }}:
                {{ $parcel->pickup_date != '' ? date('M d, Y', strtotime($parcel->pickup_date)) : '' }}
                {{ $parcel->pickup_time != '' ? date('h:i a', strtotime($parcel->pickup_time)) : '' }}
            </span><br>
            <span>{{ __('delivery') }}:
                {{ $parcel->delivery_date != '' ? date('M d, Y', strtotime($parcel->delivery_date)) : '' }}
                {{ $parcel->pickup_time != '' ? date('h:i a', strtotime($parcel->pickup_time)) : '' }}
            </span><br>
        @else
            <span>{{ __('pickup') }}:
                {{ $parcel->pickup_date != '' ? date('M d, Y', strtotime($parcel->pickup_date)) : '' }}
            </span><br>
            <span>{{ __('delivery') }}:
                {{ $parcel->delivery_date != '' ? date('M d, Y', strtotime($parcel->delivery_date)) : '' }}
            </span><br>
        @endif
        @if ($parcel->status == 'delivered' || $parcel->status == 'delivered-and-verified')
            <span>{{ __('delivered_at') }}:
                {{ $parcel->event != '' ? date('M d, Y g:i A', strtotime($parcel->event->created_at)) : '' }}
            </span><br>
            <span>{{ __('delivered_by') }}:
                {{ @$parcel->deliveryMan->user->first_name . ' ' . @$parcel->deliveryMan->user->last_name }}
            </span><br>
        @endif
        @if ($parcel->status == 'received')
            <span>{{ __('pickup_by') }}:
                {{ @$parcel->pickupMan->user->first_name . ' ' . @$parcel->pickupMan->user->last_name }}
            </span><br>
        @endif
        @if ($parcel->status == 'pickup-assigned' || $parcel->status == 're-schedule-pickup')
            <span>{{ __('pickup_man') }}:
                {{ @$parcel->pickupMan != '' ? @$parcel->pickupMan->user->first_name . ' ' . @$parcel->pickupMan->user->last_name : '' }}
            </span><br>
        @endif
        @if ($parcel->status == 'return-assigned-to-merchant')
            <span>{{ __('returned_at') }}:
                {{ $parcel->event != '' ? date('M d, Y h:i a', strtotime($parcel->event->created_at)) : '' }}
            </span><br>
            <span>
                {{ __('return_delivery_man') }}:
                {{ @$parcel->returnDeliveryMan != '' ? @$parcel->returnDeliveryMan->user->first_name . ' ' . @$parcel->returnDeliveryMan->user->last_name : '' }}
            </span><br>
        @endif
        @if ($parcel->status == 'returned-to-merchant')
            <span>{{ __('returned_at') }}:
                {{ $parcel->returnEvent != '' ? date('M d, Y h:i a', strtotime($parcel->returnEvent->created_at)) : '' }}
            </span><br>
            <span>{{ __('returned_by') }}:
                {{ @$parcel->returnDeliveryMan != '' ? @$parcel->returnDeliveryMan->user->first_name . ' ' . @$parcel->returnDeliveryMan->user->last_name : '' }}
            </span><br>
        @endif
        @if ($parcel->status == 'delivery-assigned' || $parcel->status == 're-schedule-delivery')
            <span>{{ __('delivery_man') }}:
                {{ @$parcel->deliveryMan != '' ? @$parcel->deliveryMan->user->first_name . ' ' . @$parcel->deliveryMan->user->last_name : '' }}
            </span><br>
        @endif -->
    </a>
</div>