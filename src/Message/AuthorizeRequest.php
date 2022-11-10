<?php

namespace Omnipay\Cybersource;

class AuthorizeRequest extends AbstractRequest
{
    public function getData()
    {
        $this->validate('currency', 'amount');

        $data = $this->getBaseData() + $this->getTransactionData();
        $data['signed_date_time'] = gmdate("Y-m-d\TH:i:s\Z");
        $data['unsigned_field_names'] = 'card_type,card_number,card_expiry_date';
        $data['signed_field_names'] = implode(',', array_keys($data)) . ',signed_field_names';
        $data['signature'] = $this->signData($data);

        // this is in progress - at this stage let's just pass the
        // cc fields through but really we need to return a form for them to enter it
        $data['card_type'] = $this->getCardType();
        $data['card_number'] = $this->getCard()->getNumber();
        $data['card_expiry_date'] = $this->getCard()->getExpiryDate('m-Y');
        return $data;
    }

    public function signData($data)
    {
        return base64_encode(hash_hmac('sha256', $this->buildDataToSign($data), $this->getSecretKey(), true));
    }

    public function buildDataToSign($data)
    {
        $signedFieldNames = explode(",", $data["signed_field_names"]);
        foreach ($signedFieldNames as $field) {
            $dataToSign[] = $field . "=" . $data[$field];
        }
        return implode(",", $dataToSign);
    }

    public function getRequiredFields()
    {
        return array_merge(array(
            'amount',
            'city',
            'country',
            'address1',
            'email',
            'firstName',
            'lastName',
            'currency',
        ));
    }

    public function getTransactionData()
    {
        return array(
            'totalAmount' => $this->getAmount(),
            'currency' => $this->getCurrency(),
            'description' => $this->getDescription(),
            'payment_method' => $this->getPaymentMethod(),
            'firstName' => $this->getCard()->getFirstName(),
            'lastName' => $this->getCard()->getLastName(),
            'email' => $this->getCard()->getEmail(),
            'address1' => $this->getCard()->getAddress1(),
            'locality' => $this->getLocality(),
            'country' => strtoupper($this->getCard()->getCountry()),
            'expirationYear' => $this->getCard()->getExpiryYear(),
            'number' => $this->getCard()->getNumber(),
            'securityCode' => $this->getCard()->getCvv(),
            'expirationMonth' => $this->getCard()->getExpiryMonth(),
            'code' => $this->getUniqueID(),
        );
    }

    /**
     * @return array
     */
    public function getBaseData()
    {
        return array(
            'access_key' => $this->getAccessKey(),
            'profile_id' => $this->getProfileId(),
            'transaction_type' => $this->getTransactionType(),
        );
    }

    /**
     * @return string
     */
    public function getUniqueID()
    {
        return uniqid();
    }

    public function getEndpoint()
    {
        return parent::getEndpoint() . '/pts/v2/payments';
    }

    public function getPaymentMethod()
    {
        return 'card';
    }

    public function getTransactionType()
    {
        return 'authorization';
    }
}
