# Quickstart

This extension is here to provide [GP WebPay](http://www.gpwebpay.cz) system for Nette Framework.


## Installation

The best way to install Pixidos/GPWebPay is using  [Composer](http://getcomposer.org/):

```sh
$ composer require pixidos/gpwebpay
```

and you can enable the extension using your neon config

```yml
extensions:
    gpwebpay: Pixidos\GPWebPay\DI\GPWebPayExtension
```

and setting

```yml
gpwebpay:
    privateKey: < your private certificate path >
    privateKeyPassword: < private certificate password >
    publicKey: < gateway public certificate path (you will probably get this by email) > //gpe.signing_prod.pem
    url: <url of gpwabpay system gateway > //example: https://test.3dsecure.gpwebpay.com/unicredit/order.do
    merchantNumber: <your merechant number >
```

or if you need more then one gateway
```yml
gpwebpay:
    privateKey:
        czk: < your CZK private certificate path .pem>
        eur: < your EUR private certificate path .pem>
    privateKeyPassword:
        czk: < private CKZ certificate password >
        eur: < private EUR certificate password >
    publicKey: < gateway public certificate path (you will probably get this by email) > //gpe.signing_prod.pem
    url: <url of gpwebpay system gateway > //example: https://test.3dsecure.gpwebpay.com/unicredit/order.do
    merchantNumber:
        czk: <your CZK merechant number >
        eur: <your EUR merechant number >
```

## Usage


```php
use Pixidos\GPWebPay\Exceptions\GPWebPayException;
use Pixidos\GPWebPay\Request;
use Pixidos\GPWebPay\Response;
use Pixidos\GPWebPay\Operation;

class MyPresenter extends Nette\Application\UI\Presenter
{

	/** @var \Pixidos\GPWebPay\Components\GPWebPayControlFactory @inject */
	public $gpWebPayFactory;

	/**
     * @return GPWebPayControl
     * @throws InvalidArgumentException
     */
    public function createComponentWebPayButton()
    {
        $operation = new Operation(
                    new OrderNumber('123456789'),
                    new Amount(1000),
                    new Currency(CurrencyEnum::CZK()),
                    'ckz' // leave empty or null for default key
                );
        // if you use more than one gateway use gatewayKey - same as in config
        // $operation = new Operation(int $orderId, int $totalPrice, int $curencyCode, string $gatewayKey);

        // if you need to switch gateway lang
        // $operation->addParam(new Lang('cs'));
        // or add some next parameter by $operation->addParam(<IParam> $param);     

        /**
         * you can set Response URL. In default will be used handelSuccess() in component
         * https://github.com/Pixidos/GPWebPay/blob/master/src/Pixidos/GPWebPay/Components/GPWebPayControl.php#L93
         * $operation->setResponseUrl($url);
         */

        $control = $this->gpWebPayFactory->create($operation);

        # Run before redirect to webpay gateway
        $control->onCheckout[] = function (GPWebPayControl $control, Request $request){

            //...

        }

        return $control;

    }
}
```

## Templates

```smarty
{var $attrs = array(class => 'btn btn-primary')}
{control webPayButton $attrs, 'text on button'}
```