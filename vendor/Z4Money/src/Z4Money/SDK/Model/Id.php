<?php

namespace Z4Money\SDK\Model;

class Id
{
    public $container = [];

    public function __construct(array $data = null)
    {
        $this->container['payment_id'] = $data['payment_id'] ?? null;
    }
    
	public function getPaymentId()
    {
        return $this->container['payment_id'];
    }

    public function setPaymentId($payment_id)
    {
        $this->container['payment_id'] = $payment_id;

        return $this;
    }
	
}
