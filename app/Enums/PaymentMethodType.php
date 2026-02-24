<?php

namespace App\Enums;

enum PaymentMethodType: string
{
    case BANK   = 'bank';
    case MFS    = 'mfs';
    case CASH   = 'cash';

    case MOBILE_BANKING = 'mobile_banking';
}
