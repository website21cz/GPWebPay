<?php
/**
 * Created by PhpStorm.
 * User: Ondra Votava
 * Date: 21.10.2015
 * Time: 15:09
 */

namespace Pixidos\GPWebPay\Components;

use Nette\Application\UI;
use Nette\ComponentModel\IContainer;

use Pixidos\GPWebPay\Exceptions\GPWebPayException;
use Pixidos\GPWebPay\Operation;
use Pixidos\GPWebPay\GPWebPayProvider;
use Pixidos\GPWebPay\GPWebPayResponse;
use Pixidos\GPWebPay\GPWebPayRequest;


/**
 * Class GPWebPayControl
 * @package Pixidos\GPWebPay\Components
 * @author Ondra Votava <ondra.votava@pixidos.com>
 *
 * @method onCheckout(GPWebPayControl $control, GPWebPayRequest $request)
 * @method onSuccess(GPWebPayControl $control, GPWebPayResponse $response)
 * @method onError(GPWebPayControl $control, GPWebPayException $exception)
 */
class GPWebPayControl extends UI\Control
{
    /**
     * @var array of callbacks, signature: function(GPWebPayControl $control)
     */
    public $onCheckout = array();
    /**
     * @var array of callbacks, signature: function(GPWebPayControl $control, GPWebPayResponse $response)
     */
    public $onSuccess = array();
    /**
     * @var array of callbacks, signature: function(GPWebPayControl $control, \Exception $exception)
     */
    public $onError = array();
    /**
     * @var Operation $operation
     */
    private $operation;
    /**
     * @var  GPWebPayProvider $provider
     */
    private $provider;
    /**
     * @var  string $templateFile
     */
    private $templateFile;

    /**
     * @param Operation $operation
     * @param GPWebPayProvider $provider
     * @param IContainer|null $control
     * @param string $name
     */
    public function __construct(Operation $operation, GPWebPayProvider $provider, IContainer $control = null, $name = null)
    {
        parent::__construct($control, $name);

        $this->operation = $operation;
        $this->provider = $provider;
    }

    /**
     * @throws GPWebPayException
     * @throws \Exception
     */
    public function handleCheckout()
    {
        try {
            if (!$this->operation->getResponseUrl())
                $this->operation->setResponseUrl($this->link('//success!'));

            $url = $this->provider->createRequest($this->operation)->getRequestUrl();
            $this->onCheckout($this, $this->provider->getRequest());
            $this->getPresenter()->redirectUrl($url);
        } catch (GPWebPayException $e) {
            throw $e;
        }
    }


    /**
     * @throws GPWebPayException
     */
    public function handleSuccess()
    {
        $params = $this->getPresenter()->getParameters();

        try {
            /** @var GPWebPayResponse $response */
            $response = $this->provider->createResponse($params);
            $this->provider->verifyPaymentResponse($response);
        } catch (GPWebPayException $e) {
            $this->errorHandler($e);
            return;
        }

        $this->onSuccess($this, $response);
    }


    /**
     * @param GPWebPayException $exception
     * @throws GPWebPayException
     */
    protected function errorHandler(GPWebPayException $exception)
    {
        if (! $this->onError) {
            throw $exception;
        }

        $this->onError($this, $exception);
    }

    /**
     * @param string $templateFile
     */
    public function setTemplateFile($templateFile)
    {
        $this->templateFile = $templateFile;
    }

    /**
     * @param array $attrs
     * @param string $text
     */
    public function render($attrs = array(), $text = "Pay")
    {
        $template = $this->template;
        $template->setFile($this->getTemplateFilePath());
        $template->checkoutLink = $this->link('//checkout!');
        $template->text = $text;
        $template->attrs = $attrs;
        $this->template->render();
    }

    /**
     * @return string
     */
    public function getTemplateFilePath()
    {
        return ($this->templateFile)
            ? $this->templateFile
            : $this->getDefaultTemplateFilePath();
    }

    /**
     * @return string
     */
    public function getDefaultTemplateFilePath()
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'templates/gpWebPayControl.latte';
    }
}