<?php

namespace Z4Money;

use Z4Money\SDK\Model\Pagamento;
use Z4Money\SDK\Model\Endereco;
use Z4Money\SDK\Model\Cliente;
use Z4Money\SDK\Model\Cartao;
use Z4Money\SDK\Requests\Pagamento\PagamentoRequest;

require_once('../vendor/autoload.php');

$config = new Configuration();
$config->setBearerAuth('04875a0dd4558d96554bea9f0e1f2d4d428d52bb');


$cliente   = new Cliente();
$endereco  = new Endereco();
$cartao    = new Cartao();
$pagamento = new Pagamento();

$cliente->setNome('Willian Santana');
$cliente->setCPF('44135836801');
$cliente->setDataNascimento('1995-08-24');
$cliente->setEmail('fulano@gmail.com');
$cliente->setCelular('11995213201');

$endereco->setNumero(123);
$endereco->setLogradouro('Rua Bartolomeu Sabino de Melo');
$endereco->setCEP('06550-000');
$endereco->setCidade('Osasco');
$endereco->setEstado('SP');
$endereco->setComplemento('APARTAMENTO');

$cartao->SetTitular('Willian Santana');
$cartao->setStatusNumero('4716058539241955');
$cartao->setCodigoSeguranca(900);
$cartao->setValidade('11/2020');

$pagamento->setTipoPagamentoId(Pagamento::CARTAO_DE_CREDITO);
$pagamento->setValor(400);
$pagamento->setCliente($cliente);
$pagamento->setCartao($cartao);
$pagamento->setEndereco($endereco);
$pagamento->setParcelas(1);

$requestPagamento = new PagamentoRequest($pagamento->container, $config, true);

try {
    $request = $requestPagamento->run();
    var_dump($request);
} catch (Exception $e) {
    var_dump($e);
    echo 'Exception when calling Order request->run: ', $e->getMessage(), PHP_EOL;
}


##### CREATE A PAYMENT BY DEBIT #####

/*
$customer = new Customer();
$order    = new Order();
$items    = new Items();

$customer->setCode('123456');
$customer->setName('Fulano de Tal');
$customer->setDocument('44135836801');
$customer->setEmail('fulano@gmail.com');
$customer->setPhone('11995213200');
$customer->setBirth('1995-08-24');

$order->setCode('12345678');
$order->setNotificationUrl('https://my-store.com/postback/');
$order->setPurchaseDate('2019-11-22 14:35:10');
$order->setValue('199.00');
$order->setBankSlug(Bank::ITAU);
$order->setPaymentMethod(Transaction::DEPOSIT);

for ($i = 1; $i <= 10; $i++) {
    $items->setId('123');
    $items->setDescription('Product number: ' . $i);
    $items->setValue('19.90');
    $items->setQuantity('1');

    $order->setItems($items);
}

$deposit = new Transaction();
$deposit->setCustomer($customer);
$deposit->setOrder($order);

$requestOrder = new OrderRequest($deposit->container, $config, false);

try {
    $request = $requestOrder->run();
    var_dump($request);
} catch (Exception $e) {
    var_dump($e);
    echo 'Exception when calling Order request->run: ', $e->getMessage(), PHP_EOL;
}

*/

##### CREATE A PAYMENT BY CREDTCARD #####

/*
$customer = new Customer();
$order    = new Order();
$items    = new Items();

$customer->setCode('123456');
$customer->setPhone('11995213200');

$order->setCode('2');
$order->setNotificationUrl('https://my-store.com/postback/');
$order->setRedirectUrl('https://my-store.com/backurl/');
$order->setPurchaseDate('2019-11-22 14:35:10');
$order->setValue('199.00');
$order->setPaymentMethod(Transaction::CREDTCARD);

for ($i = 1; $i <= 10; $i++) {
    $items->setId('123');
    $items->setDescription('Product number: ' . $i);
    $items->setValue('19.90');
    $items->setQuantity('1');

    $order->setItems($items);
}

$credtcard = new Transaction();
$credtcard->setCustomer($customer);
$credtcard->setOrder($order);

$requestOrder = new OrderRequest($credtcard->container, $config, false);

try {
    $request = $requestOrder->run();
    var_dump($request);
} catch (Exception $e) {
    var_dump($e);
    echo 'Exception when calling Order request->run: ', $e->getMessage(), PHP_EOL;
}

*/
