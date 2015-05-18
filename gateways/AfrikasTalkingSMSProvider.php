<?php
/**
 * Provider just implements the send() method that handles (guess what)
 * the mailing process of messages.
 * the first parameter can either be an array containing the Information 
 * or a string containing the recipient, or a object instance of YumUser.
 * In the YumUser case, the email will be sent to the E-Mail field of the
 * profile.
 * @return true if sends sms, false otherwise
 */
Yii::import("application.extensions.africatalking.*");
class AfrikasTalkingSMSProvider  extends BaseSMSProvider{
   
   public $account_username;
   public $apiKey;

   
   public function initialize(){
      if(is_null(self::$gateway) && !is_object(self::$gateway)){
         self::$gateway = new AfricasTalkingGateway($this->account_username,$this->apiKey);
             
      }  
      return $this;
   }
   public function sendSMS($to, $text,$from){
    try {
        $this->from=$from;
        $this->text=$text;
        $this->response=self::$gateway->sendMessage($to, $text,$from);
        if($this->response) $this->isSuccess=true;   
        return $this;
    } catch (Exception $e) {
        echo "Error Sending sms using africa talking <br/> Error: {$e->getMessage()}";
    }
    
    
   }
   public function receiveSMS($text,$from){

   }
   public function deliveryReport($msgId){

   }
   public function sendBulk($to, $text,$from){

   }
   public function getResponseArray($response=null){
      $responses=array();
      if(is_null($this->response)) return array(array("sender"=>"","recipient"=>"","pduid"=>"","type"=>"","status"=>"","message"=>""));
     
      foreach($this->response as $result) {
          // Note that only the Status "Success" means the message was sent
         
         $responses[]=array("message"=>$this->text,"sender"=>$this->from,"cost"=>$result->cost,"recipient"=>$result->number,"pduid"=>$result->messageId,"type"=>$this->type,"status"=>$result->status);
         $this->TotalCost+=PaymentUtil::str_to_amount($result->cost);
         if(preg_match("/Success/i", $result->status)){
            $this->smsSent++;
         }else{
            $this->smsFailed++;
         }
      }

      return $responses;
   }
}