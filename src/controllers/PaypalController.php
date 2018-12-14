<?php
namespace powerkernel\yiibilling\controllers;
use powerkernel\yiibilling\models\CreditCardForm;
use powerkernel\yiilaundry\models\Order;
use powerkernel\yiilaundry\models\Cart;
use yii\filters\AccessControl;
use Yii;
use yii\helpers\Url;
/**
 * Class PaypalController
 */
class PaypalController extends \powerkernel\yiicommon\controllers\ActiveController
{

    public  $modelClass = '';

    private $PaypalApiUrl;
    private $client_id;
    private $secret;
    
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['access'] = [
            '__class' => AccessControl::class,
            'only' => ['add-cc','view-cc','list-cc', 'delete-cc','update-cc','auth','make-payment'],
            'rules' => [
                [
                    'verbs' => ['OPTIONS'],
                    'allow' => true,
                ],
                [
                    'actions' => ['add-cc','view-cc','list-cc', 'delete-cc','update-cc','auth','assign-credit-card-to-order','assign-paypal-url-to-order'],
                    'roles' => ['@'],
                    'allow' => true,
                ],
                [
                    'actions' => ['make-payment'],
                    'roles' => ['@'],
                    'allow' => true,
                ],
            ],
        ];
        return $behaviors;
    }

    protected function verbs()
    {
        
        $parents = parent::verbs();
        return array_merge(
            $parents,
            [
                'list-cc' => ['GET'],
                'add-cc' => ['POST'],
                'view-cc' => ['POST'],
                'update-cc' => ['POST'],
                'delete-cc' => ['POST'],
            ]
        );
    }
    public function actionAddCc(){
        $model = new CreditCardForm(); ;  
        if($model->load(\Yii::$app->getRequest()->getParsedBody(),'') && $model->validate()){
            $inputJson['number'] = $model->number;
            $inputJson['type'] = $model->type;
            $inputJson['expire_month'] = $model->expire_month;
            $inputJson['expire_year'] = $model->expire_year;
            $inputJson['cvv2'] = $model->cvv2;
            $inputJson['first_name'] = $model->first_name;
            $inputJson['billing_address']['line1'] = $model->line1;
            $inputJson['billing_address']['city'] = $model->city;
            $inputJson['billing_address']['country_code'] = 'US';
            $inputJson['billing_address']['postal_code'] = $model->postal_code;
            $inputJson['external_customer_id'] = (string)Yii::$app->user->id;
            if(!empty($cid)){
                $Data = \Yii::$app->paypal->curl("v1/vault/credit-cards/".$cid,json_encode($inputJson),'PATCH');
            }else{
                $Data = \Yii::$app->paypal->curl("v1/vault/credit-cards",json_encode($inputJson),'POST');
            }
            if(isset($res['errors']) && res['success']== false){
                return $res;
            }else{
                if(!empty($Data['state']) && $Data['state']=="ok"){
                    return [
                        'success'=>true,
                        'data'=>$Data
                    ];
                }else{
                    return [
                        'success'=>false,
                        'errors'=>$Data
                    ];
                }
            }
        }else{
            $model->validate();
            return [
                'success'=>false,
                'errors'=>$model->errors
            ];
        }
    }   
    public function actionListCc(){       
        $res =  \Yii::$app->paypal->curl("v1/vault/credit-cards?external_customer_id=".(string)Yii::$app->user->id,'','GET');
        if(isset($res['errors']) && res['success']== false){
            return $res;
        }else{
            $card = array();
            if(!empty($res['items'])){
                foreach($res['items'] as $ele){
                    $c['number']      = $ele['number'];
                    $c['type']        = $ele['type'];
                    $c['source_id']   = $ele['id'];
                    array_push($card,$c);
                }                
            }   
            return[
                'success'=>true,
                'data'=>$card,
            ];
        }
    }
    public function actionDeleteCc(){        
        $Data = Yii::$app->getRequest()->getParsedBody();
        $cardID = !empty($Data['cardId'])?$Data['cardId']:"";
        if($cardID==""){
            return [
                'success'=>false,
                'errors'=>"Please select card to remove"
            ];
        }
        $res =  \Yii::$app->paypal->curl("v1/vault/credit-cards/".$cardID,'','DELETE');
        if(isset($res['errors']) && res['success']== false){
            return $res;
        }else{
            return[
                'success'=>true,
                'data'=>""
            ];
        }
    }
    public function actionViewCc(){
        $Data = Yii::$app->getRequest()->getParsedBody();
        $cardID = !empty($Data['cardId'])?$Data['cardId']:"";
        if($cardID==""){
            return [
                'success'=>false,
                'errors'=>"Please select card "
            ];
        }
        $res =  \Yii::$app->paypal->curl("v1/vault/credit-cards/".$cardID,'','GET');
        if(isset($res['errors']) && res['success']== false){
            return $res;
        }else{
            return[
                'success'=>true,
                'data'=>$res
            ];
        }
    }
    public function actionAssignPaypalUrlToOrder($Url,$OrderId){

        if(!empty($Url) && !empty($OrderId)){

            $d    = parse_url(urldecode($Url));
            $Url  = str_replace("url=", '', $d['query']);

            $O = Order::find()->where(['_id'=>$OrderId])->one();
            if($O){
                $O->payment_method      =   "PayPal";
                $O->payment_capture_url =    $Url;
                if($O->save()){
                    return $O;
                }else{
                    $O->validate();
                    return[
                        'success'=>false,
                        'errors'=>$O->errors
                    ];
                }
            }else{
                return[
                    'success'=>false,
                    'errors'=>"Invalid Order Id"
                ];
            }
        }else{
            return[
                'success'=>false,
                'errors'=>"OrderId is missing or Paypal Payment is not Authorized."
            ];
        }
    }
    public function actionAssignCreditCardToOrder($CcId,$OrderId){
        if(!empty($CcId) && !empty($OrderId)){            
            $O = Order::find()->where(['_id'=>$OrderId])->one();
            if($O){
                $O->payment_method  =   "CreditCard";
                $O->payment_card_id =    $CcId;
                if($O->save()){
                    return $O;
                }else{
                    $O->validate();
                    return[
                        'success'=>false,
                        'errors'=>$O->errors
                    ];
                }
            }else{
                return[
                    'success'=>false,
                    'errors'=>"Invalid Order Id"
                ];
            }
        }else{
            return[
                'success'=>false,
                'errors'=>"Order Id and Card can not be blank."
            ];
        }
    }
    public function actionAuth($OrderId=""){
        $total  =  10000;
        $input  =   '{
                "intent": "authorize",
                "payer":
                {
                "payment_method": "paypal"
                },
                "transactions": [
                {
                "amount":
                {
                    "total": "'.number_format((float)$total, 2, '.', '').'",
                    "currency": "USD"
                },
                "description": "Here we authorized maximum amount due to order price may be vary at '.Yii::$app->name.'. Do not worry we will charge only your order amount."
                }],
                "redirect_urls":
                {
                "return_url": "'.Url::toRoute(['default/paypal-return','uniqueId'=>(string)\Yii::$app->user->id],true).'",
                "cancel_url": "'.Url::toRoute(['default/paypal-cancel','uniqueId'=>(string)\Yii::$app->user->id],true).'"
                }
        }';
        $res =  \Yii::$app->paypal->curl("v1/payments/payment",$input,'POST'); 
        if(isset($res['errors']) && $res['success']== false){            
            return $res;
        }else{                    
            if(isset($res['state']) && $res['state']=="created"){                      
                $approval_link = "";
                foreach($res['links'] as $link){
                    if($link['rel']=="approval_url"){
                        $approval_link = $link['href'];                            
                    }
                }
                if($approval_link == ""){
                    return[
                        'success'=>false,
                        'errors'=>"approval_link is blank"
                    ];
                }else{
                    //############## inCase when payment failed and pay again by user #############
                    return[
                        'success'=> true,
                        'data'   => $approval_link
                    ];
                }
            }else{
                return[
                    'success'=>false,
                    'errors'=>'Payment method paypal has some error.'
                ];
            }
        }
    }

    public function actionMakePayment($OrderNo){ 
        $Order = Order::find()->where(['order_number'=>$OrderNo])
        ->andWhere(['!=','payment_status','Success'])
        ->andWhere(['OR',['owner_id'=>(string)\Yii::$app->user->id],['user_id'=>(string)\Yii::$app->user->id]])
        ->one();     
       
        if(empty($Order)){
            return [
                'success'=>false,
                'errors'=>'There is no order with this order number'
            ];
        }; 

        if($Order->payment_method == "CreditCard"){ 
            if(empty($Order->payment_card_id)){
                return [
                    'success'=>false,
                    'errors'=>'Payment card is not available.'
                ];
            }      
            $input ='{            
                "intent": "sale",
                "payer": {
                "payment_method": "credit_card",
                "funding_instruments": [
                {
                    "credit_card_token": {
                        "credit_card_id": "'.$Order->payment_card_id.'",
                        "external_customer_id":"'.(string)$Order->user_id.'"
                    }
                }]
                },
                "transactions": [
                {
                "amount": {
                    "total": "'.number_format((float)$Order->final_amount, 2, '.', '').'",
                    "currency": "USD"
                },
                "description": "Payment by vaulted credit card for Order No #'.$OrderNo.' at '.Yii::$app->name.'"
                }]
            }';            
            $res =  \Yii::$app->paypal->curl("v1/payments/payment",$input,'POST'); 
        }else if($Order->payment_method  == "PayPal"){            
            if(empty($Order->payment_capture_url)){
                return [
                    'success'=>false,
                    'errors'=>'Payment url is not available.'
                ];
            }    
            $input = '{
                "amount": {
                  "currency": "USD",
                  "total": "'.number_format((float)$Order->final_amount, 2, '.', '').'"
                },
                "is_final_capture": true
            }'; 
            $res =  \Yii::$app->paypal->curl($Order->payment_capture_url,$input,'POST',true);  
        }
        $Order->payment_response  =  json_encode($res);
        if(isset($res['success']) && $res['success']==false){           
            $Order->payment_status    =  "Failed";           
        }else{
            $Order->payment_response  =  json_encode($res);
            if(!empty($res['id'])){
                $Order->payment_status   =  "Success";
            }else{
                $Order->payment_status   =  "Failed";
            }                
        }
        if($Order->save()){
            if( $Order->payment_status   !=  "Success"){
              
                $input = array(
                    'user_id'=> (string)$Order->user_id,
                    'title'  =>'Payment attempt for order no #'.(string)$Order->order_number.' has been failed.',
                    'message'=>'We were unable to charge your credit card for order no # '.(string)$Order->order_number.' and for the amount of $ '.$Order->final_amount.' for your use of '.Yii::$app->name.' services. We will attempt to collect amount again unless we are successful in collecting the balance.',
                    'type'=>'Notification'
                );
                Yii::$app->push->send($input,array('order_number'=>(string)$Order->order_number,'order_id'=>(string)$Order->_id));
                return [
                    'success'=>false,
                    'errors'=>$Order->payment_response
                ];
            }else{
                return [
                    'success'=>true,
                    'data'=>$Order
                ];
            }
        }else{            
            return [
                'success'=>false,
                'errors'=>'Unable to update the order payment status.'
            ];
        }
    }
   


}
