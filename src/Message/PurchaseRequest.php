<?php

namespace Omnipay\Cybersource;

class PurchaseRequest extends AuthorizeRequest
{
    public function getTransactionType()
    {
        return 'sale';
    }
}
