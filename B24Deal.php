<?
class B24Deal{
		
		public $url = '';
		public $order = array();
		
		public function curlB24Init($queryUrl, $queryData)
		{
			$curl = curl_init();
			curl_setopt_array($curl, array(
				CURLOPT_SSL_VERIFYPEER => 0,
				CURLOPT_POST           => 1,
				CURLOPT_HEADER         => 0,
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_URL            => $queryUrl,
				CURLOPT_POSTFIELDS     => $queryData,
			));
			
			$result = curl_exec($curl);
			curl_close($curl);
			
			$result = json_decode($result, 1);
			
			return $result;
		}
		
		public function getB24Company(){
			//checkCompany
			$queryUrl  = $this->url.'crm.company.list';	
			$queryData = http_build_query(
				array(
					'filter'=> array(					
						"EMAIL"=> str_replace(" ","",$this->order["EMAIL"])				
					),				
					'select'=> array('ID'),
					'params' => array(
						'REGISTER_SONET_EVENT' => 'N'
					)
				)
			);
			$data = $this->curlB24Init($queryUrl, $queryData);		
			if(isset($data["result"][0]["ID"])){
				return  $data["result"][0]["ID"];	
			}			
										
			//addCompany
			$queryUrl  = $this->url.'crm.company.add';	
				$queryData = http_build_query(
					array(
						'fields'=> array(
							"TITLE"=> $this->order["DESCRIPTION"],							
							"OPENED"=> "Y", 
							"ASSIGNED_BY_ID"=> 1, 							
							"PHONE"=> array(array("VALUE"=> str_replace('-','',$this->order["PHONE"])	, "VALUE_TYPE"=> "WORK")),				
							"EMAIL"=> array(array("VALUE"=> str_replace(" ","",$this->order["EMAIL"])	, "VALUE_TYPE"=> "WORK"))
						),				
						'params' => array(
							'REGISTER_SONET_EVENT' => 'N'
						)
					)
				);
			$data = $this->curlB24Init($queryUrl, $queryData);							
			return  $data["result"];	
			
		}
		public function getB24Contact(){
			//checkContact
			$queryUrl  = $this->url.'crm.contact.list';	
			$queryData = http_build_query(
				array(
					'filter'=> array(					
						"EMAIL"=> str_replace(" ","",$this->order["EMAIL"])				
					),				
					'select'=> array('ID'),
					'params' => array(
						'REGISTER_SONET_EVENT' => 'N'
					)
				)
			);
			$data = $this->curlB24Init($queryUrl, $queryData);		
			if(isset($data["result"][0]["ID"])){
				return  $data["result"][0]["ID"];	
			}			
			list($LAST_NAME,$NAME,$SECOND_NAME) = explode(" ",trim($this->order["FIO"]));
								
			//addContact
			$queryUrl  = $this->url.'crm.contact.add';	
				$queryData = http_build_query(
					array(
						'fields'=> array(
							"NAME"=> $NAME, 
							"SECOND_NAME"=> $SECOND_NAME, 
							"LAST_NAME"=> $LAST_NAME, 
							"OPENED"=> "Y", 
							"ASSIGNED_BY_ID"=> 1, 
							"TYPE_ID"=> "CLIENT",
							"SOURCE_ID"=> "SELF",					
							"PHONE"=> array(array("VALUE"=> str_replace('-','',$this->order["PHONE"])	, "VALUE_TYPE"=> "WORK")),				
							"EMAIL"=> array(array("VALUE"=> str_replace(" ","",$this->order["EMAIL"])	, "VALUE_TYPE"=> "WORK"))
						),				
						'params' => array(
							'REGISTER_SONET_EVENT' => 'N'
						)
					)
				);
			$data = $this->curlB24Init($queryUrl, $queryData);							
			return  $data["result"];	
			
		}
		
		public function getB24Product($p){
			$queryUrl  = $this->url.'crm.product.list';	
			$queryData = http_build_query(
				array(
					'filter'=> array(										
						"NAME"=> $p["NAME"]				
					),				
					'select'=> array('ID')				
				)
			);
			$data = $this->curlB24Init($queryUrl, $queryData);		
			if(isset($data["result"][0]["ID"])){
				return  $data["result"][0]["ID"];	
			}		
			
			$queryUrl  = $this->url.'crm.product.add';	
				$queryData = http_build_query(
					array(
						'fields'=>array(
							"NAME"=> $p["NAME"], 
							"CURRENCY_ID"=> "RUB", 
							"PRICE"=> str_replace(" ","",$p["PRICE"]), 
							"SORT"=> 500													
						)
					)
				);
			
			$data = $this->curlB24Init($queryUrl, $queryData);												
			return  $data["result"];	
		}
		public function addB24Deal(){
			$queryUrl  = $this->url.'crm.deal.add.json';	
			$fields = array(
						"TITLE"=> "Заказ из интернет магазина №".$this->order['ORDER_ID'], 					
						"CONTACT_ID"=> $this->getB24Contact(),						
						"OPENED"=> "Y", 
						"ASSIGNED_BY_ID"=> 4, 					
						"CURRENCY_ID"=> "RUB", 										
						"BEGINDATE"=> date('Y-m-d')
				);
				if(isset($this->order['DESCRIPTION']) && $this->order['DESCRIPTION']!=''){
					$fields["COMPANY_ID"] = $this->getB24Company();
				}
			
			$queryData = http_build_query(									
				array(
					'fields'=> $fields,			
					'params' => array(
						'REGISTER_SONET_EVENT' => 'N'
					)
				)
			);        
			$data = $this->curlB24Init($queryUrl, $queryData);	
			return $data['result'];
		}
		
		public function addB24ProductsToDeal(){			
			$rows = array();
			foreach ($this->order['ITEMS'] as $p){				
				array_push($rows,(array("PRODUCT_ID"=> $this->getB24Product($p), "PRICE"=> str_replace(" ","",$p["PRICE"]), "QUANTITY"=> $p["QUANTITY"])));
			}
			
			//add product
			$queryUrl  = $this->url.'crm.deal.productrows.set';	
			$queryData = http_build_query(
				array(
					'id'=>$this->addB24Deal(),
					'rows'=>$rows
				)
			);        
			$this->curlB24Init($queryUrl, $queryData);
		}			
		
		
	};