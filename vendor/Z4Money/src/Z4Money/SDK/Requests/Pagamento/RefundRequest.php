<?php

namespace Z4Money\SDK\Requests\Pagamento;

use Z4Money\SDK\Requests\AbstractRequest;

class RefundRequest extends AbstractRequest
{
    public function run()
    {
        $url = parent::getEnvironment()->getApiURL();
        $payment_id = parent::getData()->getPaymentId();
		
		parent::setResourcePath('vendas/' . $payment_id . '/estornar');
        parent::getClient()->getConfig()->setHost($url);

        return parent::sendRequest();
    }
}
