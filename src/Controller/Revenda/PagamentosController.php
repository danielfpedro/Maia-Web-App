<?php

namespace App\Controller\Controle;

use App\Controller\Controle\AppController;

use Cake\Network\Exception\BadRequestException;
use PagSeguro\Library;
use PagSeguro\Domains\Requests\Payment;

class PagamentosController extends AppController
{
	
	public function teste()
	{
		Library::initialize();
		// \PagSeguro\Library::cmsVersion()->setName("Nome")->setRelease("1.0.0");
		// \PagSeguro\Library::moduleVersion()->setName("Nome")->setRelease("1.0.0");

		$payment = new Payment();

		$payment->addItems()->withParameters(
		    '0001',
		    'Licença para Loja',
		    10,
		    39.90
		);

		// $payment->addItems()->withParameters(
		//     '0002',
		//     'Notebook preto',
		//     2,
		//     430.00
		// );

		$payment->setCurrency("BRL");

		//$payment->setExtraAmount(11.5);

		$payment->setReference("LIBPHP000001");

		$payment->setRedirectUrl("http://www.lojamodelo.com.br");

		// Set your customer information.
		$payment->setSender()->setName('João Comprador');
		// $payment->setSender()->setEmail('c13450790258078075412@sandbox.pagseguro.com.br');
		$payment->setSender()->setDocument()->withParameters(
		    'CNPJ',
		    '73.103.757/0001-84'
		);
		$payment->setSender()->setDocument()->withParameters(
		    'CPF',
		    '963.824.695-21'
		);

		// Frete
		$payment->addParameter('shippingAddressRequired', false);

		// $payment->setSender()->setPhone()->withParameters(
		//     11,
		//     56273440
		// );


		// $payment->setShipping()->setAddress()->withParameters(
		//     'Av. Brig. Faria Lima',
		//     '1384',
		//     'Jardim Paulistano',
		//     '01452002',
		//     'São Paulo',
		//     'SP',
		//     'BRA',
		//     'apto. 114'
		// );
		// $payment->setShipping()->setCost()->withParameters(20.00);
		// $payment->setShipping()->setType()->withParameters(\PagSeguro\Enum\Shipping\Type::SEDEX);

		//Add metadata items
		// $payment->addMetadata()->withParameters('PASSENGER_CPF', 'insira um numero de CPF valido');
		// $payment->addMetadata()->withParameters('GAME_NAME', 'DOTA');
		// $payment->addMetadata()->withParameters('PASSENGER_PASSPORT', '23456', 1);

		//Add items by parameter
		//On index, you have to pass in parameter: total items plus one.
		// $payment->addParameter()->withParameters('itemId', '0003')->index(3);
		// $payment->addParameter()->withParameters('itemDescription', 'Notebook Amarelo')->index(3);
		// $payment->addParameter()->withParameters('itemQuantity', '1')->index(3);
		// $payment->addParameter()->withParameters('itemAmount', '200.00')->index(3);

		//Add items by parameter using an array
		$payment->addParameter()->withArray(['notificationURL', 'http://www.lojamodelo.com.br/nofitication']);

		$payment->setRedirectUrl("http://www.lojamodelo.com.br");
		$payment->setNotificationUrl("http://www.lojamodelo.com.br/nofitication");

		//Add discount
		// $payment->addPaymentMethod()->withParameters(
		//     \PagSeguro\Enum\PaymentMethod\Group::CREDIT_CARD,
		//     \PagSeguro\Enum\PaymentMethod\Config\Keys::DISCOUNT_PERCENT,
		//     10.00 // (float) Percent
		// );

		//Add installments with no interest
		// $payment->addPaymentMethod()->withParameters(
		//     \PagSeguro\Enum\PaymentMethod\Group::CREDIT_CARD,
		//     \PagSeguro\Enum\PaymentMethod\Config\Keys::MAX_INSTALLMENTS_NO_INTEREST,
		//     2 // (int) qty of installment
		// );

		//Add a limit for installment
		$payment->addPaymentMethod()->withParameters(
		    \PagSeguro\Enum\PaymentMethod\Group::CREDIT_CARD,
		    \PagSeguro\Enum\PaymentMethod\Config\Keys::MAX_INSTALLMENTS_LIMIT,
		    1 // (int) qty of installment
		);

		// Add a group and/or payment methods name
		$payment->acceptPaymentMethod()->groups(
			\PagSeguro\Enum\PaymentMethod\Group::CREDIT_CARD,
			\PagSeguro\Enum\PaymentMethod\Group::BOLETO,
		    // \PagSeguro\Enum\PaymentMethod\Group::DEPOSIT,
		    \PagSeguro\Enum\PaymentMethod\Group::EFT
		    // \PagSeguro\Enum\PaymentMethod\Group::BALANCE
		);
		// $payment->acceptPaymentMethod()->name(\PagSeguro\Enum\PaymentMethod\Name::DEBITO_ITAU);
		// $payment->acceptPaymentMethod()->name(\PagSeguro\Enum\PaymentMethod\Name::BOLETO);
		// Remove a group and/or payment methods name
		// $payment->excludePaymentMethod()->group(\PagSeguro\Enum\PaymentMethod\Group::CREDIT_CARD);

		$response = [];

		try {

		    /**
		     * @todo For checkout with application use:
		     * \PagSeguro\Configuration\Configure::getApplicationCredentials()
		     *  ->setAuthorizationCode("FD3AF1B214EC40F0B0A6745D041BF50D")
		     */
		    $result = $payment->register(
		        \PagSeguro\Configuration\Configure::getAccountCredentials()
		    );

		    $response['url_gerada'] = $result;
		    $response['urlHtml'] = __("<a href=\"{0}\">CHECKOUT</a>", $result);
		    $response['message'] = 'A URL foi gerada';
		} catch (\Exception $e) {
		    throw new BadRequestException($e->getMessage());
		    // dd($e->getMessage());
		}

		$this->set(compact('response'));
		$this->set('_serialize', 'response');
	}
}