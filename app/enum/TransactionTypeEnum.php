<?php

namespace App\enum;

enum TransactionTypeEnum:string
{
    case SEND = 'SEND';
    case RECEIVE = 'RECEIVE';
    case CHARGE = 'CHARGE';

}
