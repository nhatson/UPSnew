<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_Ups
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\Ups\Model;

use Bss\Ups\Helper\Data;
use Bss\Ups\Model\Xml;
use Magento\Framework\Filesystem\DirectoryList;

class Ups
{
    public $package_number="";
    public $package_numbers="";
    
    private  $ups_key = "";
    private  $user_id = "";
    private  $user_id_pass = "";
    private  $ups_url_rate = "";
    public $solo_price= NULL;
    public $multi_price = NULL;
    public $request_data="";
    public $response_data ="";
    private $access_key = "";
    private $description = "Kunstpflanzen";
    private $shipper_account = "";
    public $shipper_name = "";
    public $shipper_phone = "";
    public $shipper_address = "";
    public $shipper_city = "";
    public $shipper_zip = "";
    public $shipper_country = "";
    public $posturl ="";
    public $weight ="";
    
    public $ShipTo = "";
    public $ShipFrom = "";
    
    public $shipper_from_zip = "";
    public $shipper_from_city = "";
    public $shipper_from_country = "";
    public $shipper_from_phone = "";
    public $b_order_id ="";
    
    public $customer =array();
    
    private $UrlShipConfirm = "";
    private $UrlShipAccept = "";
    private $UrlShipVoid = "";
    
    private $path_upsDigest = "";
    private $path_upsXML ="";
    private $path_upsLabels ="";
    public $message_buffer = "";
    public $ups_error=0;
    
    private $UPS_STATUS_SEND_TO_UPS = "an UPS übermittelt";
  

    public $a_params =array('order_id'=>'','paketanzahl'=>'','paketkilo'=>'','nach'=>'','paketart'=>'','wert'=>'','payment_method'=>'','shipping_address_id'=>'','cost'=>0);
    /**
     * Data
     *
     * @var Data
     */
    protected $helper;
    protected $xmlClass;
    protected $directory;

    //add
    public $invoice_increment_id = "";
	public function __construct (
		Data $helper,
		Xml $xmlClass,
        DirectoryList $directory
	) {
		$this->helper = $helper;
		$this->xmlClass = $xmlClass;
        $this->directory = $directory;
		$this->setVar();
	} 
   
   public function setVar() {
        $this->ups_key = $this->helper->getZugangsdaten('ups_upskey');
        $this->user_id =  $this->helper->getZugangsdaten('ups_userid');
        $this->user_id_pass =  $this->helper->getZugangsdaten('ups_password');
        $this->access_key =  $this->helper->getZugangsdaten('ups_accesskey');
        $this->shipper_account =  $this->helper->getZugangsdaten('ups_shipperaccount');
		
    	$this->shipper_name = $this->helper->getVersandvon('ups_firma');
    	$this->shipper_AttentionName = $this->helper->getVersandvon('ups_angezeigtername');
    	$this->shipper_phone = $this->helper->getVersender('ups_telefon');
    	$this->shipper_address = $this->helper->getVersender('ups_strasse');
    	$this->shipper_city = $this->helper->getVersender('ups_ort');
    	$this->shipper_zip = $this->helper->getVersender('ups_plz');
    	$this->shipper_country = $this->helper->getVersender('ups_countrycode');
    		
    	$this->shipper_ResidentialAddress = $this->helper->getVersandvon('ups_strasse2');
    	$this->shipper_from_zip = $this->helper->getVersandvon('ups_plz2');
    	$this->shipper_from_city = $this->helper->getVersandvon('ups_ort2');
    	$this->shipper_from_country = $this->helper->getVersandvon('ups_countrycode2');
    	$this->shipper_from_phone = $this->helper->getVersandvon('ups_telefon2');
            
        $this->path_upsDigest = $this->directory->getPath('app') .DIRECTORY_SEPARATOR.'code'.DIRECTORY_SEPARATOR.'Bss'.DIRECTORY_SEPARATOR.'Ups'.DIRECTORY_SEPARATOR.'ups'.DIRECTORY_SEPARATOR.'upsDigest';
        $this->path_upsXML = $this->directory->getPath('app') .DIRECTORY_SEPARATOR.'code'.DIRECTORY_SEPARATOR.'Bss'.DIRECTORY_SEPARATOR.'Ups'.DIRECTORY_SEPARATOR.'ups'.DIRECTORY_SEPARATOR.'upsXML';
        $this->path_upsLabels = $this->directory->getPath('app') .DIRECTORY_SEPARATOR.'code'.DIRECTORY_SEPARATOR.'Bss'.DIRECTORY_SEPARATOR.'Ups'.DIRECTORY_SEPARATOR.'ups'.DIRECTORY_SEPARATOR.'upsLabels';
		
    	//set path
    	if($this->helper->getPfade('ups_testmode') == 1){
            $this->UrlShipConfirm = $this->helper->getPfade('ups_urlshipconfirmtest');
            $this->UrlShipAccept = $this->helper->getPfade('ups_urlshipaccepttest');
            $this->UrlShipVoid = $this->helper->getPfade('ups_urlshipvoidtest');
        }else{
            $this->UrlShipConfirm = $this->helper->getPfade('ups_urlshipconfirm');
            $this->UrlShipAccept = $this->helper->getPfade('ups_urlshipaccept');
            $this->UrlShipVoid = $this->helper->getPfade('ups_urlshipvoid');
    	}   	
   }

   public function set_params($b_zip,$b_country_id,$b_weight)
   {
        $this->customer['zip'] = $b_zip;
        $this->customer['country'] = $b_country_id;
        $this->customer['weight'] = $b_weight;
		
        $this->ups_url_rate = $this->helper->getPfade('ups_preisabfrage');
   }


    public function ups_xml_init($ups_case)
    {
        switch ($ups_case)
        {
            case "rate":
            {
                $this->posturl = $this->ups_url_rate;
                break;
            }
            case "void":
            {
                $this->posturl = $this->UrlShipVoid;
                break;
            }
        }
        if($ups_case =='ship' || $ups_case == 'ship_retour'){
            $this->request_data .= "<?xml version=\"1.0\"?>
                                <AccessRequest xml:lang=\"en-US\">
                                <AccessLicenseNumber>".$this->access_key."</AccessLicenseNumber>
                                <UserId>".$this->user_id."</UserId>
                                <Password>".$this->user_id_pass."</Password>
                                </AccessRequest>
                                <?xml version=\"1.0\"?>
                                <ShipmentConfirmRequest xml:lang=\"en-US\">
                                <Request>
                                <TransactionReference>";
            
              if($ups_case == 'ship_retour'){
                  $this->request_data .= "<CustomerContext>TR01</CustomerContext>";
              }
              else {
                  $this->request_data .= "<CustomerContext>Ship Confirm / nonvalidate</CustomerContext>";
              }
              $this->request_data .= "<XpciVersion>1.0001</XpciVersion>
                                </TransactionReference>
                                <RequestAction>ShipConfirm</RequestAction>
                                <RequestOption>nonvalidate</RequestOption>
                                </Request>
                                <LabelSpecification>
                                <LabelPrintMethod>
                                <Code>GIF</Code>
                                </LabelPrintMethod>
                                <HTTPUserAgent>Mozilla/5.0</HTTPUserAgent>
                                <LabelImageFormat>
                                <Code>GIF</Code>
                                </LabelImageFormat>
                                </LabelSpecification>
                                <Shipment>";
              if($ups_case == 'ship_retour'){
                  $this->request_data .= "<Description>Bestellung: ".$this->b_order_id."</Description><ReturnService><Code>9</Code></ReturnService>";
              }
              else{
                  $this->request_data .= "<Description>".$this->description."</Description>";
              }
           
            
             $this->request_data .= "<Shipper>
                                <Name>".$this->shipper_name."</Name>
                                <AttentionName>".$this->shipper_AttentionName."</AttentionName>
                                <PhoneNumber>".$this->shipper_phone."</PhoneNumber>
                                <ShipperNumber>".$this->shipper_account."</ShipperNumber>
                                <Address>
                                <AddressLine1>".$this->repl($this->shipper_address)."</AddressLine1>
                                <AddressLine2></AddressLine2>
                                <AddressLine3></AddressLine3>
                                <City>".$this->repl($this->shipper_city)."</City>
                                <PostalCode>".$this->shipper_zip."</PostalCode>
                                <CountryCode>".$this->shipper_country."</CountryCode>
                                </Address>
                                </Shipper>";
            
        }
        else{
            
                $this->request_data .= "<?xml version=\"1.0\"?>
                                <AccessRequest xml:lang=\"en-US\">
                                <AccessLicenseNumber>".$this->access_key."</AccessLicenseNumber>
                                <UserId>".$this->user_id."</UserId>
                                <Password>".$this->user_id_pass."</Password>
                                </AccessRequest>";
        }
		
    }
    
    public function get_ups_void_data($trackingnumber){
        
        $this->request_data .="<?xml version=\"1.0\"?>
			<VoidShipmentRequest>
			<Request>
			<TransactionReference>
			<CustomerContext>Ship Void</CustomerContext>
			<XpciVersion>1.0001</XpciVersion>
			</TransactionReference>
			<RequestAction>1</RequestAction>
			<RequestOption></RequestOption>
			</Request>
			<ShipmentIdentificationNumber>{$trackingnumber}</ShipmentIdentificationNumber> </VoidShipmentRequest>
			";

    }

    public function send_ups_xml()
    {
		// wrj
		//print_r($this->request_data);
        $ci = curl_init();
		
         curl_setopt ($ci, CURLOPT_URL, $this->posturl);
         curl_setopt ($ci, CURLOPT_HEADER, 0);
         curl_setopt ($ci, CURLOPT_SSL_VERIFYPEER, FALSE);
         curl_setopt ($ci, CURLOPT_POST, 1);
         curl_setopt ($ci, CURLOPT_POSTFIELDS, $this->request_data);
         curl_setopt ($ci, CURLOPT_RETURNTRANSFER, 1);
         $this->response_data = curl_exec ($ci);

         curl_close ($ci);
         //print_r($this->request_data);die;
    }

    
    public function show_data($ups_case)
    {
        switch($ups_case)
        {
            
           case "void":
           {             
                global $xml;
                $obj = $this->xmlClass;
                $obj->__xml($this->response_data,'xml');
                //new xml($this->response_data,"xml");
             
                if (($xml["VoidShipmentResponse_Response"][0]->ResponseStatusDescription[0]) == "Failure") {
		
                    $this->message_buffer .= "Beim abbrechen des Auftrags ist ein Fehler aufgetreten: ".$xml["VoidShipmentResponse_Response_Error"][0]->ErrorDescription[0];
                    $this->ups_error =1;
                }
                else{
                	$this->message_buffer .= "Erfolgreich! Ups Meldung: ".$xml["VoidShipmentResponse_Response"][0]->ResponseStatusCode[0].".ResponseStatusBeschreibung: ".$xml["VoidShipmentResponse_Response"][0]->ResponseStatusDescription[0];
                	
                }
            
             break;
           }
        }
        
    }

    public function repl($str) {
	/* Should by a private public function - Replace forbidden characters */
	return str_replace (array('ä', 'ö', 'ü', 'ß', '&','Ä','Ö','Ü'), array('ae', 'oe', 'ue', 'ss', 'und', 'Ae', 'Oe', 'Ue'), $str);
    }

    public function get_price_multi($counter)
    {
	  $this->request_data .='<RatingServiceSelectionRequest xml:lang="en-US">
                 <Request>
                     <TransactionReference>
                        <CustomerContext>Rating and Service</CustomerContext>
                        <XpciVersion>1.0</XpciVersion>
                     </TransactionReference>
                    <RequestAction>Rate</RequestAction>
                    <RequestOption>rate</RequestOption>
                 </Request>
                <PickupType>
		    <Code>01</Code>
		</PickupType>
                <Shipment>
                    <Service>
                        <Code>11</Code>
                        <Description></Description>
                    </Service>
                    <Shipper>
                        <Name>'.$this->shipper_name.'</Name>
			<AttentionName>'.$this->shipper_AttentionName.'</AttentionName>
			<ShipperNumber>'.$this->shipper_account.'</ShipperNumber>
			<PhoneNumber>'.$this->shipper_phone.'</PhoneNumber>
			<EMailAddress></EMailAddress>
                        <Address>
                            <City>'.$this->shipper_city.'</City>
                            <PostalCode>'.$this->shipper_zip.'</PostalCode>
                            <CountryCode>'.$this->shipper_country.'</CountryCode>
                            <StateProvinceCode></StateProvinceCode>
                        </Address>
                    </Shipper>
                    <ShipTo>
						<Address>
                              <PostalCode>'.$this->customer['zip'].'</PostalCode>
                              <CountryCode>'.$this->customer['country'].'</CountryCode>
                              <StateProvinceCode></StateProvinceCode>
                        </Address>
                        
                    </ShipTo>
                    <ShipFrom>
                        <Address>
                            <PostalCode>'.$this->shipper_from_zip.'</PostalCode>
                            <CountryCode>'.$this->shipper_from_country.'</CountryCode>
                            <ResidentialAddress>'.substr($this->repl($this->shipper_ResidentialAddress),0,35).'</ResidentialAddress>
                            <StateProvinceCode></StateProvinceCode>
                        </Address>
                    </ShipFrom>';

                    for($i=0;$i<$counter;$i++)
                    {
                        $this->request_data .='<Package>
                        <PackagingType><Code>02</Code></PackagingType>
						<Dimensions>
                            <UnitOfMeasurement>
                                <Code>CM</Code>
                            </UnitOfMeasurement>
                            <Length>60</Length>
                            <Width>7</Width>
                            <Height>5</Height>
                        </Dimensions>
                        <PackageWeight>
                            <UnitOfMeasurement><Code>KGS</Code></UnitOfMeasurement>
                            <Weight>'.$this->customer['weight'].'</Weight>
                        </PackageWeight>
                        </Package>';
                    }


        $this->request_data .= '<RateInformation><NegotiatedRatesIndicator/></RateInformation>
            </Shipment>
            </RatingServiceSelectionRequest>';
    }

    public function get_price_single()
    {
        $this->request_data .= '<RatingServiceSelectionRequest xml:lang="en-US">
                   <Request>
                     <TransactionReference>
                        <CustomerContext>Rating and Service</CustomerContext>
                        <XpciVersion>1.0</XpciVersion>
                     </TransactionReference>
                    <RequestAction>Rate</RequestAction>
                    <RequestOption>rate</RequestOption>
                 </Request>
                <PickupType>
		    <Code>01</Code>
		</PickupType>
                <Shipment>
                    <Service>
                        <Code>11</Code>
                        <Description></Description>
                    </Service>
                    <Shipper>
                        <Name>'.$this->shipper_name.'</Name>
			<AttentionName>'.$this->shipper_AttentionName.'</AttentionName>
			<ShipperNumber>'.$this->shipper_account.'</ShipperNumber>
			<PhoneNumber>'.$this->shipper_phone.'</PhoneNumber>
			<EMailAddress></EMailAddress>
                        <Address>
                            <City>'.$this->shipper_city.'</City>
                            <PostalCode>'.$this->shipper_zip.'</PostalCode>
                            <CountryCode>'.strtoupper($this->shipper_country).'</CountryCode>
                            <StateProvinceCode></StateProvinceCode>
                        </Address>
                    </Shipper>
                    <ShipTo>
                        <Address>
                              <PostalCode>'.$this->customer['zip'].'</PostalCode>
                              <CountryCode>'.strtoupper($this->customer['country']).'</CountryCode>
                              <StateProvinceCode></StateProvinceCode>
                        </Address>
                    </ShipTo>
                    <ShipFrom>
                        <Address>
                            <PostalCode>'.$this->shipper_from_zip.'</PostalCode>
                            <CountryCode>'.$this->shipper_from_country.'</CountryCode>
                            <ResidentialAddress>'.substr($this->repl($this->shipper_ResidentialAddress),0,35).'</ResidentialAddress>
                            <StateProvinceCode></StateProvinceCode>
                        </Address>
                    </ShipFrom>
                        <Package>
                            <PackagingType><Code>02</Code></PackagingType>
							<Dimensions>
                            <UnitOfMeasurement>
                                <Code>CM</Code>
                            </UnitOfMeasurement>
                            <Length>60</Length>
                            <Width>7</Width>
                            <Height>5</Height>
                        </Dimensions>
                            <PackageWeight>
                                <UnitOfMeasurement><Code>KGS</Code></UnitOfMeasurement>
                                <Weight>'.$this->customer['weight'].'</Weight>
                            </PackageWeight>
                        </Package>
                        <RateInformation><NegotiatedRatesIndicator/></RateInformation>
                    </Shipment>
                    </RatingServiceSelectionRequest>';
    }
    
    public function fon($str){
	return str_replace (array(' '), array(''), $str);
    }
    
    public function req($whatDigest) {
	$this->request_data .=  "<?xml version=\"1.0\"?>
	<AccessRequest xml:lang=\"en-US\">
            <AccessLicenseNumber>".$this->access_key."</AccessLicenseNumber>
            <UserId>".$this->user_id."</UserId>
            <Password>".$this->user_id_pass."</Password>
	</AccessRequest>
	<?xml version=\"1.0\"?>
	<ShipmentAcceptRequest>
	<Request>
	<TransactionReference>
	<CustomerContext>TR01</CustomerContext>
	<XpciVersion>1.0001</XpciVersion>
	</TransactionReference>
	<RequestAction>ShipAccept</RequestAction>
	<RequestOption>01</RequestOption>
	</Request>
	<ShipmentDigest>".$whatDigest."</ShipmentDigest>
	</ShipmentAcceptRequest>";
    }
    
    public function ups_xml_ShipTo(){
         
        $buffer_company ="";
        $buffer_name ="";
        
        
        if($this->ShipTo['company'] == $this->shipper_name){
           $buffer_name =$this->ShipTo['company'];
        }
        else{
           $buffer_name = $this->repl($this->ShipTo['prefix'])." ".$this->repl($this->ShipTo["firstname"])." ".$this->repl($this->ShipTo["lastname"]);;
        }        
        //var_dump($this->ShipTo);die;
        if($this->ShipTo['company'] !=""){
           $buffer_company = $this->repl($this->ShipTo['company']);
        }else{
           $buffer_company = $this->repl($this->ShipTo['prefix'])." ".$this->repl($this->ShipTo["firstname"])." ".$this->repl($this->ShipTo["lastname"]);
        }
		$phone_number = $this->fon($this->ShipTo['telephone']);
		$phone_number = substr($phone_number,0,14);
		
       $this->request_data .= "<ShipTo>
                <CompanyName>".substr($buffer_company,0,35)."</CompanyName>
                <AttentionName>".substr($buffer_name,0,35)."</AttentionName>
                <PhoneNumber>".$phone_number."</PhoneNumber>
                <Address>
                    <AddressLine1>".substr($this->repl($this->ShipTo['street']),0,35)."</AddressLine1>
                    <AddressLine2></AddressLine2>
                    <AddressLine3></AddressLine3>
                    <PostalCode>".$this->ShipTo['postcode']."</PostalCode>
                    <City>".$this->repl($this->ShipTo['city'])."</City>
                    <CountryCode>".$this->ShipTo['country_id']."</CountryCode>
                </Address>
            </ShipTo>";
        
    }
    
    public function ups_xml_ShipFrom(){
        //var_dump($this->ShipFrom);die;
       $buffer_company ="";
       $buffer_name ="";
       if($this->ShipFrom['CompanyName'] == $this->shipper_name){
           $buffer_name =$this->ShipFrom['CompanyName'];
       }
       else{
           $buffer_name = $this->repl($this->ShipFrom['Prefix'])." ".$this->repl($this->ShipFrom["firstname"])." ".$this->repl($this->ShipFrom["lastname"]);;
       }
        
       if($this->ShipFrom['CompanyName'] !=""){
           $buffer_company = $this->repl($this->ShipFrom['CompanyName']);
       }else{
           $buffer_company = $this->repl($this->ShipFrom['Prefix'])." ".$this->repl($this->ShipFrom["firstname"])." ".$this->repl($this->ShipFrom["lastname"]);
       }

        
        $this->request_data .= "<ShipFrom>
            <CompanyName>".substr($buffer_company,0,35)."</CompanyName>
            <AttentionName>".substr($buffer_name,0,35)."</AttentionName>
            <PhoneNumber>".$this->ShipFrom['PhoneNumber']."</PhoneNumber>
            <Address>
                <AddressLine1>".substr($this->repl($this->ShipFrom['AddressLine1']),0,35)."</AddressLine1>
                <AddressLine2></AddressLine2>
		<AddressLine3></AddressLine3>
                <City>".$this->ShipFrom['City']."</City>
                <PostalCode>".$this->ShipFrom['PostalCode']."</PostalCode>
                <CountryCode>".$this->ShipFrom['CountryCode']."</CountryCode>
            </Address>
            </ShipFrom>
            <PaymentInformation>
                <Prepaid>
                    <BillShipper>
                        <AccountNumber>".$this->shipper_account."</AccountNumber>
                    </BillShipper>
                 </Prepaid>
            </PaymentInformation>
            <ReferenceNumber>
		<Value>".$this->invoice_increment_id."</Value>
            </ReferenceNumber>
            <ReferenceNumber>
		<Value>".$this->increment_id."</Value>
            </ReferenceNumber>				
            <Service>
            <Code>11</Code>
            </Service>";
        
    }
   
   //handling muli package
   public function upsShipmentConfirmRequest($s){  
          
          $this->ups_xml_init($s);
          $this->ups_xml_ShipTo();
          $this->ups_xml_ShipFrom();

            #echo $this->a_params['nach'];
            if ($this->a_params['nach'] == "ja") {
                
                $this->request_data .= "<ShipmentServiceOptions>
                        <COD>
                            <CODCode>3</CODCode>
                            <CODFundsCode>1</CODFundsCode>
                            <CODAmount>
                                <CurrencyCode>EUR</CurrencyCode>
									<MonetaryValue>".$this->a_params['wert']."</MonetaryValue>                                    
                             </CODAmount>
                        </COD>
                     </ShipmentServiceOptions>";
            }
            
            for($d=1;$d<($this->a_params['paketanzahl']+1);$d++){
                $this->request_data .= "<Package>";
                
                if($s =="ship_retour") $this->request_data .= "<Description>Bestellung ".$this->b_order_id."</Description>";
                
               $this->request_data .= "<PackagingType>
                            <Code>02</Code>
                        </PackagingType>
                        <Dimensions>
                            <UnitOfMeasurement>
                                <Code>CM</Code>
                            </UnitOfMeasurement>
                            <Length>60</Length>
                            <Width>7</Width>
                            <Height>5</Height>
                        </Dimensions>
                        <PackageWeight>
                            <UnitOfMeasurement>
                            <Code>KGS</Code>
                            </UnitOfMeasurement>
                            <Weight>".$this->a_params['paketkilo']."</Weight>
                        </PackageWeight>
                        </Package>
						<RateInformation><NegotiatedRatesIndicator/></RateInformation>";
            }
            
            $this->request_data .= "</Shipment></ShipmentConfirmRequest>";

   }
      
   
   public function upsShipmentAcceptResponse(){
       $this->posturl =  $this->UrlShipAccept;
       $this->send_ups_xml();        
        
        global $xml;
        $obj = $this->xmlClass;
        $obj->__xml($this->response_data,'xml');
        $i = 0;
        if ($xml["ShipmentAcceptResponse_Response"][$i]->ResponseStatusDescription[0] == "Failure") {
        	
        	$this->message_buffer .=  "ABBRUCH: ".$xml["ShipmentAcceptResponse_Response"][$i]->ResponseStatusDescription[0].": ".$xml["ShipmentAcceptResponse_Response_Error"][$i]->ErrorDescription[0];
        	$this->ups_error = 1;
        	
        } else if ($xml["ShipmentAcceptResponse_Response"][$i]->ResponseStatusDescription[0] == "Success") {
        	
        	$this->message_buffer .= "Erfolgreich! Ups Meldung:".$xml["ShipmentAcceptResponse_Response"][$i]->ResponseStatusDescription[0];
        	
        	while (list($cc)=@each($xml["ShipmentAcceptResponse_ShipmentResults_PackageResults"])) {
        		$label = $xml["ShipmentAcceptResponse_ShipmentResults_PackageResults_LabelImage"][$i]->GraphicImage[0];
        		$label2 = $xml["ShipmentAcceptResponse_ShipmentResults_PackageResults_LabelImage"][$i]->HTMLImage[0];
        		$giffile = $xml["ShipmentAcceptResponse_ShipmentResults_PackageResults"][$i]->TrackingNumber[0].".gif";
        		$xmlfile = $xml["ShipmentAcceptResponse_ShipmentResults_PackageResults"][$i]->TrackingNumber[0]."_response.xml";
        		$trackingnumber = $xml["ShipmentAcceptResponse_ShipmentResults_PackageResults"][$i]->TrackingNumber[0];
        		$CODfile = $xml["ShipmentAcceptResponse_ShipmentResults_PackageResults"][$i]->TrackingNumber[0]."_COD.html";
        		$CODImage = isset($xml["ShipmentAcceptResponse_ShipmentResults_CODTurnInPage_Image"][$i]->GraphicImage[1])?$xml["ShipmentAcceptResponse_ShipmentResults_CODTurnInPage_Image"][$i]->GraphicImage[1]:null;
				
        		$gif = base64_decode($label);
        		$gif2 = base64_decode($label2);
        		$CODgif = base64_decode($CODImage);
        	
        		$xdat = fopen($this->path_upsLabels."/".$giffile, "w");
        		fputs($xdat,$gif);
        		fclose($xdat);
        		//$xdat = fopen($this->path_upsLabels."/test.gif", "w");
        		//fputs($xdat,$CODgif);
        		//fclose($xdat);
				$xdat = fopen($this->path_upsLabels."/".$CODfile, "w");
        		fputs($xdat,$CODgif);
        		fclose($xdat);
        		$xdat = fopen($this->path_upsLabels."/".$trackingnumber.".html", "w");
        		fputs($xdat,$gif2);
        		fclose($xdat);
        	
        		$this->package_numbers .= ";".$trackingnumber;
        	
        		$this->ups_status = $this->UPS_STATUS_SEND_TO_UPS;
        		$void = 0;
        	
        	
        		$xdat = fopen($this->path_upsXML."/".$xmlfile, "w");
        		fputs($xdat,$this->response_data);
        		fclose($xdat);
        	
        		$i++;
        	}
        	
        	
        	
        } else {
        	echo "<b>ABBRUCH: Unbekannter Fehler von UPS!</b><br><br>Request<br>";
				echo $xml["ShipmentAcceptResponse_Response"][0]->ResponseStatusDescription[0];
				print_r($this->request_data);
				echo "<hr>Response\n\n\n";
				print_r($this->response_data);
				exit;
        	exit;
        }
           
 
   }
   
   //handling muli package
   public function upsShipmentConfirmRequestSingle($bcount,$s){
       
       $this->ups_xml_init($s);
       $this->ups_xml_ShipTo();
       $this->ups_xml_ShipFrom();

            #echo $this->a_params['nach'];
            if ($this->a_params['nach'] == "ja" && $bcount==1) {
                
                $this->request_data .= "<ShipmentServiceOptions>
                        <COD>
                            <CODCode>3</CODCode>
                            <CODFundsCode>1</CODFundsCode>
                            <CODAmount>
                                <CurrencyCode>EUR</CurrencyCode>
                                <MonetaryValue>".$this->a_params['wert']."</MonetaryValue>
                            </CODAmount>
                        </COD>
                     </ShipmentServiceOptions>";
            }
            $this->request_data .= "<Package>";
            
            if($s =="ship_retour") $this->request_data .= "<Description>Bestellung ".$this->b_order_id."</Description>";
            
            
            $this->request_data .= "<PackagingType>
                            <Code>02</Code>
                        </PackagingType>
                        <Dimensions>
                            <UnitOfMeasurement>
                                <Code>CM</Code>
                            </UnitOfMeasurement>
                            <Length>60</Length>
                            <Width>7</Width>
                            <Height>5</Height>
                        </Dimensions>
                        <PackageWeight>
                            <UnitOfMeasurement>
                            <Code>KGS</Code>
                            </UnitOfMeasurement>
                            <Weight>".$this->a_params['paketkilo']."</Weight>
                        </PackageWeight>
                        </Package>
						<RateInformation><NegotiatedRatesIndicator/></RateInformation>
                        </Shipment>
                        </ShipmentConfirmRequest>";
            
            
   }
   
   public function upsShipmentConfirmResponseSingle($bcount){
       
      //send xml data
      $this->posturl = $this->UrlShipConfirm;
	  
      $this->send_ups_xml();
       
      global $xml;
      //$obj = new xml($this->response_data,"xml");  
      $obj = $this->xmlClass;
      $obj->__xml($this->response_data,'xml');
      //if (!$i) $i = '0';
     
      $i = $bcount-1;

      //error?
      if (($xml["ShipmentConfirmResponse_Response"][0]->ResponseStatusDescription[0]) == "Failure") {
          $this->message_buffer .= "Beim versenden ist ein Fehler aufgetreten : ".$xml["ShipmentConfirmResponse_Response_Error"][0]->ErrorDescription[0];
          $this->ups_error = 1;
      }
      
      //save parcel
      $label = isset($xml["ShipmentConfirmResponse"][0]->ShipmentDigest[$i])?$xml["ShipmentConfirmResponse"][0]->ShipmentDigest[$i]:'';
      $giffile = isset($xml["ShipmentConfirmResponse"][0]->ShipmentIdentificationNumber[$i])?$xml["ShipmentConfirmResponse"][0]->ShipmentIdentificationNumber[$i]:''.".dat";
      $xmlfile = isset($xml["ShipmentConfirmResponse"][0]->ShipmentIdentificationNumber[$i])?$xml["ShipmentConfirmResponse"][0]->ShipmentIdentificationNumber[$i]:''."_request.xml";
      $gif = $label;
      $ups_versanddatum = date("d.m.Y H:i:s");
      $this->package_number =isset($xml["ShipmentConfirmResponse"][0]->ShipmentIdentificationNumber[$i])?$xml["ShipmentConfirmResponse"][0]->ShipmentIdentificationNumber[$i]:'';
	  
      $this->a_params['cost'] = isset($xml["ShipmentConfirmResponse_NegotiatedRates_NetSummaryCharges_GrandTotal"][0]->MonetaryValue[$i])?$xml["ShipmentConfirmResponse_NegotiatedRates_NetSummaryCharges_GrandTotal"][0]->MonetaryValue[$i]:'0' + $this->a_params['cost'];
      
      /* ueberfluessig?
      * $ups_paketanzahl = $this->$a_params['paketanzahl'];
      * $ups_paketkilo = $xml["ShipmentConfirmResponse_BillingWeight"][0]->Weight[$i];
       */
      $xdat = fopen($this->path_upsDigest."/".$giffile.".dat", "w");
      fputs($xdat,$gif);
      fclose($xdat);
      $xdat = fopen($this->path_upsXML."/".$xmlfile, "w");
      fputs($xdat,isset($xyz)?$xyz:null);
      fclose($xdat);
      $xdat = fopen($this->path_upsXML."/".$xmlfile."_sent.xml", "w");
      fputs($xdat,isset($y)?$y:null);
      fclose($xdat);

   }
   
   public function upsShipmentAcceptRequestSingle(){

       $dfile=$this->package_number;
       $xdat = fopen($this->path_upsDigest."/".$dfile.".dat", "r") or
            die("<b>ABBRUCH: Digest File für Trackingnummer {$this->package_number} nicht gefunden!</b>");
        
       $digest = fgets($xdat);
       fclose($xdat);
       
       // noch offen
       $this->req($digest);
       
   }
   
   public function upsShipmentAcceptResponseSingle($bcount){

      $this->posturl = $this->UrlShipAccept;
       $this->send_ups_xml();
  
      global $xml;
      $obj = $this->xmlClass;
      $obj->__xml($this->response_data,'xml');
      if ($xml["ShipmentAcceptResponse_Response"][0]->ResponseStatusDescription[0] == "Failure") {
      	$this->message_buffer .= "ABBRUCH: ".$xml["ShipmentAcceptResponse_Response"][0]->ResponseStatusDescription[0].": ".$xml["ShipmentAcceptResponse_Response_Error"][$i]->ErrorDescription[0];
      	$this->ups_error = 1;
      	
      } else if ($xml["ShipmentAcceptResponse_Response"][0]->ResponseStatusDescription[0] == "Success") {
      	
      	$this->message_buffer .= "Erfolgreich! Ups Meldung: ".$xml["ShipmentAcceptResponse_Response"][0]->ResponseStatusDescription[0];
      	
      	$i=0;
      	$i = $bcount-1;
      	$label = $xml["ShipmentAcceptResponse_ShipmentResults_PackageResults_LabelImage"][0]->GraphicImage[$i];
      	$label2 = $xml["ShipmentAcceptResponse_ShipmentResults_PackageResults_LabelImage"][0]->HTMLImage[$i];
      	$giffile = $xml["ShipmentAcceptResponse_ShipmentResults_PackageResults"][0]->TrackingNumber[$i].".gif";
      	$xmlfile = $xml["ShipmentAcceptResponse_ShipmentResults_PackageResults"][0]->TrackingNumber[$i]."_response.xml";
      	$trackingnumber = $xml["ShipmentAcceptResponse_ShipmentResults_PackageResults"][0]->TrackingNumber[$i];
      	$CODfile = $xml["ShipmentAcceptResponse_ShipmentResults_PackageResults"][0]->TrackingNumber[$i]."_COD.html";
      	$CODImage = isset($xml["ShipmentAcceptResponse_ShipmentResults_CODTurnInPage_Image"][0]->GraphicImage[$i])?$xml["ShipmentAcceptResponse_ShipmentResults_CODTurnInPage_Image"][0]->GraphicImage[$i]:null;
      	
      	$gif = base64_decode($label);
      	$gif2 = base64_decode($label2);
      	$CODgif = base64_decode($CODImage);
      	
      	$xdat = fopen($this->path_upsLabels."/".$giffile, "w");
      	fputs($xdat,$gif);
      	fclose($xdat);
      	$xdat = fopen($this->path_upsLabels."/".$CODfile, "w");
      	fputs($xdat,$CODgif);
      	fclose($xdat);
      	$xdat = fopen($this->path_upsLabels."/".$trackingnumber.".html", "w");
      	fputs($xdat,$gif2);
      	fclose($xdat);
      	
      	$this->package_numbers .= ";".$trackingnumber;
      	
      	$this->ups_status = $this->UPS_STATUS_SEND_TO_UPS;
      	$void = 0;
      	
      	
      	$xdat = fopen($this->path_upsXML."/".$xmlfile, "w");
      	fputs($xdat,$this->response_data);
      	fclose($xdat);
      	
      } else {
      	echo "<b>ABBRUCH: Unbekannter Fehler von UPS!</b><br><br>Request<br>";
				echo $xml["ShipmentAcceptResponse_Response"][0]->ResponseStatusDescription[0];
				print_r($this->request_data);
				echo "<hr>Response\n\n\n";
				print_r($this->response_data);
				exit;
      	exit;
      }
      
      
   }
   
   public function get_orderId($shid){
       $connection = Mage::getSingleton('core/resource')->getConnection('core_read');
       $select_order_id = $connection->select("SELECT 'order_id'")->from($tablePrefix .'sales_flat_shipment')->where("`entity_id`='".$shid."'")->order(""); 
       $if_order_id = $connection->fetchAll($select_order_id);
       foreach($if_order_id as $ioid){
            $b_oid = $ioid['order_id'];
       }
       return $b_oid;
   }
}
