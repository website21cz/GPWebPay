<?php
/**
 * Created by PhpStorm.
 * User: Ondra Votava
 * Date: 21.10.2015
 * Time: 11:48
 */

namespace Pixidos\GPWebPay;

/**
 * Class GPWebPayResponse
 * @package Pixidos\GPWebPay
 * @author Ondra Votava <ondra.votava@pixidos.com>
 */

class GPWebPayResponse
{

    /**
     * @var array $params
     */
    private $params;
    /** @var  string */
    private $digest;
    /** @var  string */
    private $digest1;

    /**
     * @param string $operation
     * @param string $ordernumber
     * @param string $merordernum
     * @param string $md
     * @param int $prcode
     * @param int $srcode
     * @param string $resulttext
     * @param string $digest
     * @param string $digest1
     */
    public function __construct($operation, $ordernumber, $merordernum, $md, $prcode, $srcode, $resulttext, $digest, $digest1) {
        $this->params['OPERATION'] = $operation;
        $this->params['ORDERNUMBER'] = $ordernumber;
        if ($merordernum !== NULL) {
            $this->params['MERORDERNUM'] = $merordernum;
        }
        if($md !== NULL)
            $this->params['MD'] = $md;
        $this->params['PRCODE'] = (int)$prcode;
        $this->params['SRCODE'] = (int)$srcode;
        $this->params['RESULTTEXT'] = $resulttext;
        $this->digest = $digest;
        $this->digest1 = $digest1;
    }

    /**
     * @return array
     */
    public function getParams() {
        return $this->params;
    }
    /**
     * @return mixed
     */
    public function getDigest() {
        return $this->digest;
    }
    /**
     * @return bool
     */
    public function hasError() {
        return (bool)$this->params['PRCODE'] || (bool)$this->params['SRCODE'];
    }
    /**
     * @return string
     */
    public function getDigest1() {
        return $this->digest1;
    }

    /**
     * @return string | null
     */
    public function getMerOrderNumber()
    {
        return $this->params['MERORDERNUM'];
    }

    /**
     * @return int
     */
    public function getSrcode()
    {
        return $this->params['SRCODE'];
    }

    /**
     * @return int
     */
    public function getPrcode()
    {
        return $this->params['PRCODE'];
    }

    public function getResultText()
    {
        return $this->params['RESULTTEXT'];
    }
}