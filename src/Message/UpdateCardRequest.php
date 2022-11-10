<?php

namespace Omnipay\Cybersource;

class UpdateCardRequest extends AbstractRequest
{
    public function getData()
    {
        $data = array();

        if ($this->getToken()) {
            $data['card'] = $this->getToken();
        } elseif ($this->getCard()) {
            $data['card'] = $this->getCard();
            $data['email'] = $this->getCard()->getEmail();
        }

        $this->validate('cardReference');

        return $data;
    }
}
