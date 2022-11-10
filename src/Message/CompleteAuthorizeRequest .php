<?php

namespace Omnipay\Cybersource;

class CompleteAuthorizeRequest extends AbstractRequest
{

    public function getData()
    {
        $data = $this->httpRequest->request->all();

        return $data;
    }

    public function sendData($data)
    {
        return $this->response = new CompleteAuthorizeResponse($this, $data);
    }
}
