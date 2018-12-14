<?php
/**
 * @author Harry Tang <harry@powerkernel.com>
 * @link https://powerkernel.com
 * @copyright Copyright (c) 2018 Power Kernel
 */

namespace powerkernel\yiibilling\v1\controllers;
use powerkernel\yiilaundry\models\Order;
use powerkernel\yiicommon\controllers\RestController;
use yii\helpers\Url;
/**
 * Class DefaultController
 * @package powerkernel\yiipage\v1\controllers
 */
class DefaultController extends RestController
{
    
    public function actionIndex()
    {
        return [
            'success' => true,
            'module' => 'Billing API',
            'version' => '1.0.0'
        ];
    }
    public function actionPaypalCancel(){
        return;
    }
    public function actionPaypalSuccess(){
        return;
    }
    public function actionPaypalFailed(){
        return;
    }
    public function actionPaypalReturn($uniqueId,$paymentId,$token,$PayerID){   
        $input = '{
            "payer_id":"'.$PayerID.'"
        }';
        $res =  \Yii::$app->paypal->curl("v1/payments/payment/".$paymentId.'/execute',$input,'POST');         
        if(isset($res['state']) && $res['state'] == "approved"){           
            $links       = $res['transactions'][0]['related_resources'][0]['authorization']['links'];
            $capture_url = "";
            foreach($links as $link){
                if($link['rel']=="capture"){
                    $capture_url = $link['href'];
                }
            }
            if(!empty($capture_url)){
                header("Location: ".Url::toRoute(['default/paypal-success','url'=>$capture_url],true));die;
            }else{
                header("Location: ".Url::toRoute(['default/paypal-failed','errors'=>'Capture url is invalid'],true));die;                 
            }
        }else{
            header("Location: ".Url::toRoute(['default/paypal-failed','errors'=>'Unable to execute payment with paypal.'],true));die;          
        }
    } 
}