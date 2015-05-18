<?php

namespace common\extentions;

/**
 * This is just an example.
 */
class SMS extends \yii\base\Widget
{
	private $gateways=[];
	public $gatewaysList=[];
	public $defaultGateway=null;   
    public $afterSend=null;


    public function run()
    {
        foreach ($this->gatewaysList as $name => $config) {
		      $config['class']="{$name}";
		      $this->gateways[$name]=\Yii::createComponent($config);

	    }
    }
    public function send($to, $text,$from, $extra=array()){
       $gateway=$this->getSMSgatway();
       $sent=$Provider->sendSMS($recepients, $text, $from);
       if(!is_null($afterSend)){       	 
       	 call_user_func_array($this->afterSend,$gateway->getResponseArray());
       }
    }

	  /**
	   * Returns the SMS provider
	   * @return BaseSMSProvider
	   */
	private function getSMSgatway() {
	    return $this->_providers[$this->defaultProvider]->initialize();
	}

}
