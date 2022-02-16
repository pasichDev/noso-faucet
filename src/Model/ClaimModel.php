<?php

namespace NosoProject\Model;
use NosoProject\Methods\GenCode;
use NosoProject\Lib\ReCaptcha;

final class ClaimModel {
    private $container;
    private $Settings;
    private $UserArray;
    private $DB;
    private $RecaptchaClass;
    private $RecaptchaResponse = null;

    public function __construct($container){
          $this->container = $container;
          $this->Settings = $container->get('settings')['recaptcha'];
          $this->UserArray = $container->get('UserAuthInfo');
          $this->DB = $container->get('db');
          $this->RecaptchaClass = new ReCaptcha($this->Settings['PrivateKey']);
    }



    /**
     * Напиши мини руководство, по поводу ключец генерации
     * 1. Открываем страницу фаусет
     * 2. На этой странице проверяем можно ли подать пост кнопки на второй степ
     * 2.1 Если можно то мы генерируем в этом условии код, который передает в бд и по пост
     * 3. Переходим сверяем код, если вс ок то переведим на дркгкю стрпнцу если нет очисчаем код в бд и возвращаем на фаусет
     * 4. На заключном етапе переводим код, в процессс и после всех дел очищаем код из бд и даем награду!
     * 
     * 
     */


      /**
	 * Array of settings for the view
	 */
	public function OptionArray(){
		return [
			'title' => 'Claim',
			'PublicKey' => $this->Settings['PublicKey'],
                  'TOKEN_HIDEEN' =>  GenCode::GenTokenClaim($this->UserArray['wallet'],$this->DB ),
                  'ViewPayments' => false
		];}

            

      public function Run(){
       if($this->ChecReCaptcha())  $this->ClaimOkay();
      }
       

      protected function ClaimOkay(){
      //Здес мы впишем претензию  в архив
      $Insert = $this->DB->prepare("INSERT INTO `claim` SET `wallet` = :wallet, `date` = :date, `noso` = :noso");
      $Insert->execute(array('wallet' =>  $this->UserArray['wallet'], 'date' => time(), 'noso' => $_ENV['NOSO_PAY']));

      //Здесь мы зачисляем полученно вознаграждения
      $sth = $this->DB->prepare("UPDATE `users` SET `balance` = :balance, `lastclaim` = :lastclaim, `keyClaimVer` = '' WHERE `id` = :id");
      $sth->execute(array('balance' => $this->UserArray['balance'] +  $_ENV['NOSO_PAY'],'lastclaim' => time(), 'id' =>  $this->UserArray['id']));
       
      if($this->UserArray['ref']){
      //Здесь мы получем процент для пользователя
      $ref =$this->DB->prepare("UPDATE `users` SET `refBalance` = `refBalance` + :refBalance  WHERE `wallet` = :wallet");
      $ref->execute(array('refBalance' =>  $this->ClaimNosoRefPercent(),  'wallet' => $this->UserArray['ref']));
      }

      }


      /**
       * The percentage that the referee will receive
       */
      private function ClaimNosoRefPercent(){
            return  $_ENV['NOSO_PAY'] *  $_ENV['PERCENT_REF'];
      }



      /**
       * Check ReCaptcha Code 
       */
       protected function ChecReCaptcha(){
                  if(isset($_POST["g-recaptcha"])) {
                    $this->RecaptchaResponse = $recaptcha_class->verifyResponse(
                          $_SERVER["REMOTE_ADDR"],
                          $_POST["g-recaptcha"]
                      );
                  }  
               return $recaptcha_response != null && $recaptcha_response->success;                   
       }


}