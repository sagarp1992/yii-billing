<?php
namespace powerkernel\yiibilling\models;
use powerkernel\yiicommon\Core;
use Yii;
use yii\base\Model;
class CreditCardForm extends Model
{

    public $number;
    public $type;
    public $expire_month;
    public $expire_year;
    public $cvv2;
    public $first_name;
    public $last_name;
    public $line1;
    public $city;
    public $country_code;
    public $postal_code;
    public $state;
    public $phone;
    public $cid ="";

    public function rules()
    {
        return [
            [['number','type','expire_month','expire_year','cvv2','first_name','line1','city','postal_code'], 'required'],
            ['country_code', 'default', 'value' => "US"],         
            ['number', 'bryglen\validators\CreditCardValidator','message'=>'Invalid card number'],
            ['type', 'in', 'range' => ['visa', 'mastercard', 'amex', 'discover', 'maestro'],'message'=>'Card type is invalid'],
            ['expire_month', 'match', 'pattern' => '/^[0-9]{2}$/','message'=>'Expire month value must be a number and between 01 to 12'],
            ['expire_year', 'match', 'pattern' => '/^[0-9]{4}$/','message'=>'Expire year value must be a number and in four digit.'],
            ['cvv2', 'match', 'pattern' => '/^[0-9]{3,4}$/','message'=>'CVV number value must be a number and in 3-4 digit.'],            
            ['country_code', 'match', 'pattern' => '/^([A-Z]{2}|C2)$/','message'=>'Invalid country code.']            
        ];
    }
    public function country(){
     
     return  array_flip(array (
  
        'ALBANIA'=> 'AL',
    
        'ALGERIA'=> 'DZ',  
      
        'ANDORRA'=> 'AD',  
      
        'ANGOLA'=> 'AO',  
      
        'ANGUILLA'=> 'AI',  
      
        'ANTIGUA & BARBUDA'=> 'AG',  
      
        'ARGENTINA'=> 'AR',  
      
        'ARMENIA'=> 'AM',
        
        'ARUBA'=> 'AW',  
      
        'AUSTRALIA'=> 'AU',
    
        'AUSTRIA'=> 'AT',
    
        'AZERBAIJAN'=> 'AZ',
    
        'BAHAMAS'=> 'BS',
    
        'BAHRAIN'=> 'BH',
    
        'BARBADOS'=> 'BB',
    
        'BELARUS'=> 'BY',
    
        'BELGIUM'=> 'BE',
    
        'BELIZE'=> 'BZ',
    
        'BENIN'=> 'BJ',
    
        'BERMUDA'=> 'BM',
    
        'BHUTAN'=> 'BT',
    
        'BOLIVIA'=> 'BO',
    
        'BOSNIA & HERZEGOVINA'=> 'BA',
    
        'BOTSWANA'=> 'BW',
    
        'BRAZIL'=> 'BR',
    
        'BRITISH VIRGIN ISLANDS'=> 'VG',
    
        'BRUNEI'=> 'BN',
    
        'BULGARIA'=> 'BG',
    
        'BURKINA FASO'=> 'BF',
    
        'BURUNDI'=> 'BI',
    
        'CAMBODIA'=> 'KH',
    
        'CAMEROON'=> 'CM',
    
        'CANADA'=> 'CA',
    
        'CAPE VERDE'=> 'CV',
    
        'CAYMAN ISLANDS'=> 'KY',
    
        'CHAD'=> 'TD',
    
        'CHILE'=> 'CL',
    
        'CHINA'=> 'C2',
    
        'COLOMBIA'=> 'CO',
    
        'COMOROS'=> 'KM',
    
        'CONGO - BRAZZAVILLE'=> 'CG',
    
        'CONGO - KINSHASA'=> 'CD',
    
        'COOK ISLANDS'=> 'CK',
    
        'COSTA RICA'=> 'CR',
    
        'CÔTE D’IVOIRE'=> 'CI',
    
        'CROATIA'=> 'HR',
    
        'CYPRUS'=> 'CY',
    
        'CZECH REPUBLIC'=> 'CZ',
    
        'DENMARK'=> 'DK',
    
        'DJIBOUTI'=> 'DJ',
    
        'DOMINICA'=> 'DM',
    
        'DOMINICAN REPUBLIC'=> 'DO',
    
        'ECUADOR'=> 'EC',
    
        'EGYPT'=> 'EG',
    
        'EL SALVADOR'=> 'SV',
    
        'ERITREA'=> 'ER',
    
        'ESTONIA'=> 'EE',
    
        'ETHIOPIA'=> 'ET',
    
        'FALKLAND ISLANDS'=> 'FK',
    
        'FAROE ISLANDS'=> 'FO',
    
        'FIJI'=> 'FJ',
    
        'FINLAND'=> 'FI',
    
        'FRANCE'=> 'FR',
    
        'FRENCH GUIANA'=> 'GF',
    
        'FRENCH POLYNESIA'=> 'PF',
    
        'GABON'=> 'GA',
    
        'GAMBIA'=> 'GM',
    
        'GEORGIA'=> 'GE',
    
        'GERMANY'=> 'DE',
    
        'GIBRALTAR'=> 'GI',
    
        'GREECE'=> 'GR',
    
        'GREENLAND'=> 'GL',
    
        'GRENADA'=> 'GD',
    
        'GUADELOUPE'=> 'GP',
    
        'GUATEMALA'=> 'GT',
    
        'GUINEA'=> 'GN',
    
        'GUINEA-BISSAU'=> 'GW',
    
        'GUYANA'=> 'GY',
    
        'HONDURAS'=> 'HN',
    
        'HONG KONG SAR CHINA'=> 'HK',
    
        'HUNGARY'=> 'HU',
    
        'ICELAND'=> 'IS',
    
        'INDIA'=> 'IN',
    
        'INDONESIA'=> 'ID',
    
        'IRELAND'=> 'IE',
    
        'ISRAEL'=> 'IL',
    
        'ITALY'=> 'IT',
    
        'JAMAICA'=> 'JM',
    
        'JAPAN'=> 'JP',
    
        'JORDAN'=> 'JO',
    
        'KAZAKHSTAN'=> 'KZ',
    
        'KENYA'=> 'KE',
    
        'KIRIBATI'=> 'KI',
    
        'KUWAIT'=> 'KW',
    
        'KYRGYZSTAN'=> 'KG',
    
        'LAOS'=> 'LA',
    
        'LATVIA'=> 'LV',
    
        'LESOTHO'=> 'LS',
    
        'LIECHTENSTEIN'=> 'LI',
    
        'LITHUANIA'=> 'LT',
    
        'LUXEMBOURG'=> 'LU',
    
        'MACEDONIA'=> 'MK',
    
        'MADAGASCAR'=> 'MG',
    
        'MALAWI'=> 'MW',
    
        'MALAYSIA'=> 'MY',
    
        'MALDIVES'=> 'MV',
    
        'MALI'=> 'ML',
    
        'MALTA'=> 'MT',
    
        'MARSHALL ISLANDS'=> 'MH',
    
        'MARTINIQUE'=> 'MQ',
    
        'MAURITANIA'=> 'MR',
    
        'MAURITIUS'=> 'MU',
    
        'MAYOTTE'=> 'YT',
    
        'MEXICO'=> 'MX',
    
        'MICRONESIA'=> 'FM',
    
        'MOLDOVA'=> 'MD',
    
        'MONACO'=> 'MC',
    
        'MONGOLIA'=> 'MN',
    
        'MONTENEGRO'=> 'ME',
    
        'MONTSERRAT'=> 'MS',
    
        'MOROCCO'=> 'MA',
    
        'MOZAMBIQUE'=> 'MZ',
    
        'NAMIBIA'=> 'NA',
    
        'NAURU'=> 'NR',
    
        'NEPAL'=> 'NP',
    
        'NETHERLANDS'=> 'NL',
    
        'NEW CALEDONIA'=> 'NC',
    
        'NEW ZEALAND'=> 'NZ',
    
        'NICARAGUA'=> 'NI',
    
        'NIGER'=> 'NE',
    
        'NIGERIA'=> 'NG',
    
        'NIUE'=> 'NU',
    
        'NORFOLK ISLAND'=> 'NF',
    
        'NORWAY'=> 'NO',
    
        'OMAN'=> 'OM',
    
        'PALAU'=> 'PW',
    
        'PANAMA'=> 'PA',
    
        'PAPUA NEW GUINEA'=> 'PG',
    
        'PARAGUAY'=> 'PY',
    
        'PERU'=> 'PE',
    
        'PHILIPPINES'=> 'PH',
    
        'PITCAIRN ISLANDS'=> 'PN',
    
        'POLAND'=> 'PL',
    
        'PORTUGAL'=> 'PT',
    
        'QATAR'=> 'QA',
    
        'RÉUNION'=> 'RE',
    
        'ROMANIA'=> 'RO',
    
        'RUSSIA'=> 'RU',
    
        'RWANDA'=> 'RW',
    
        'SAMOA'=> 'WS',
    
        'SAN MARINO'=> 'SM',
    
        'SÃO TOMÉ & PRÍNCIPE'=> 'ST',
    
        'SAUDI ARABIA'=> 'SA',
    
        'SENEGAL'=> 'SN',
    
        'SERBIA'=> 'RS',
    
        'SEYCHELLES'=> 'SC',
    
        'SIERRA LEONE'=> 'SL',
    
        'SINGAPORE'=> 'SG',
    
        'SLOVAKIA'=> 'SK',
    
        'SLOVENIA'=> 'SI',
    
        'SOLOMON ISLANDS'=> 'SB',
    
        'SOMALIA'=> 'SO',
    
        'SOUTH AFRICA'=> 'ZA',
    
        'SOUTH KOREA'=> 'KR',
    
        'SPAIN'=> 'ES',
    
        'SRI LANKA'=> 'LK',
    
        'ST. HELENA'=> 'SH',
    
        'ST. KITTS & NEVIS'=> 'KN',
    
        'ST. LUCIA'=> 'LC',
    
        'ST. PIERRE & MIQUELON'=> 'PM',
    
        'ST. VINCENT & GRENADINES'=> 'VC',
    
        'SURINAME'=> 'SR',
    
        'SVALBARD & JAN MAYEN'=> 'SJ',
    
        'SWAZILAND'=> 'SZ',
    
        'SWEDEN'=> 'SE',
    
        'SWITZERLAND'=> 'CH',
    
        'TAIWAN'=> 'TW',
    
        'TAJIKISTAN'=> 'TJ',
    
        'TANZANIA'=> 'TZ',
    
        'THAILAND'=> 'TH',
    
        'TOGO'=> 'TG',
    
        'TONGA'=> 'TO',
    
        'TRINIDAD & TOBAGO'=> 'TT',
    
        'TUNISIA'=> 'TN',
    
        'TURKMENISTAN'=> 'TM',
    
        'TURKS & CAICOS ISLANDS'=> 'TC',
    
        'TUVALU'=> 'TV',
    
        'UGANDA'=> 'UG',
    
        'UKRAINE'=> 'UA',
    
        'UNITED ARAB EMIRATES'=> 'AE',
    
        'UNITED KINGDOM'=> 'GB',
    
        'UNITED STATES'=> 'US',
    
        'URUGUAY'=> 'UY',
    
        'VANUATU'=> 'VU',
    
        'VATICAN CITY'=> 'VA',
    
        'VENEZUELA'=> 'VE',
    
        'VIETNAM'=> 'VN',
    
        'WALLIS & FUTUNA'=> 'WF',
    
        'YEMEN'=> 'YE',
    
        'ZAMBIA'=> 'ZM',
    
        'ZIMBABWE'=> 'ZW',
      ));
    }

}
