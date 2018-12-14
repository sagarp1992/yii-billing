<?php
namespace powerkernel\yiibilling;
use yii\base\Component;
use yii\helpers\Url;
use powerkernel\yiipush\models\Notification;
use powerkernel\yiipush\models\UserToken;
use Yii;
/**
 * Class PaypalComponent
 * @package powerkernel\yiibilling
 */
class PaypalComponent extends Component
{
    public  $credential;
    private $PaypalApiUrl;
    private $client_id;
    private $secret;
    
    public function init()
    {
        parent::init();
        if(isset($this->credential['mode']) && $this->credential['mode']=="Production"){
            $this->client_id    =  $this->credential['production']['client_id'];
            $this->secret       =  $this->credential['production']['secret'];
            $this->PaypalApiUrl =  $this->credential['production']['url'];
        }else{
            $this->client_id     = $this->credential['sandbox']['client_id'];
            $this->secret        = $this->credential['sandbox']['secret'];
            $this->PaypalApiUrl  = $this->credential['sandbox']['url'];
        }
        
    }
    public function getPaypalToken(){
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => $this->PaypalApiUrl."v1/oauth2/token",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => "grant_type=client_credentials",
        CURLOPT_HTTPHEADER => array(
            'Content-Type:application/x-www-form-urlencoded',
            'Authorization: Basic '.base64_encode($this->client_id.":".$this->secret)
        ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);
       
        if ($err) {
            return [
                'success'=>false,
                'errors'=>$err
            ];
        } else {
            $Data = json_decode($response,true);
            if(!empty($Data['access_token'])){
                return [
                    'success'=>true,
                    'data'=>$Data
                ];
            }else{
                return [
                    'success'=>false,
                    'errors'=>"Failed to authenticate"
                ];
            }
           
        }
    } 
    public function curl($action,$inputJson,$method="POST",$IsFullUrl=false){
        $res = $this->getPaypalToken();
        if($res['success']== false){
            return $res;
        }
        $URL = $IsFullUrl==true?$action:$this->PaypalApiUrl.$action;       
        $Token =  $res['data']['access_token'];     
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL =>  $URL,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_POSTFIELDS =>$inputJson,
        CURLOPT_HTTPHEADER => array(
            "authorization: Bearer ".$Token,
            "cache-control: no-cache",
            "content-type: application/json",
        ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {         
            return [
                'success'=>false,
                'errors'=>$err
            ];
        } else {
            return json_decode($response,true);            
        }
    } 
    

}
