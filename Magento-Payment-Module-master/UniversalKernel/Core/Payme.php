<?php 
class Payme 
{
	private $MerchantId = null;
	private $MerchantIdCode = null;
	private $MerchantKey = null;
	private $CheckoutUrl = null;
	private $DB = null;
	
	function __construct($DB){
		$this->BD = $DB;
		$this->Security = new Security();
	}

	public function MerchantKey($MerchantKey){
		$this->MerchantKey = $MerchantKey;
	}
	public function  FindMerchantKey($Get, $Sql = null){
		$this->BD->sql = "Select t.merchant_id MerchantId,
								 IF(is_flag_test IN ('Y'), t.merchant_key_test, t.merchant_key) MerchantKey,
								 endpoint_url CheckoutUrl,
								 endpoint_url_pay_sys,
						 		 is_flag_test,
						 		 is_flag_send_tovar,
								 callback_timeout
							From {TABLE_PREFIX}payme_config t
				/*		   Where IF(is_flag_test IN ('Y'), t.merchant_key_test, t.merchant_key) = :p_merchant_key */
				";
		$this->BD->param(':p_merchant_key', $this->MerchantKey);
		$ret = $this->BD->query();
		
		
		// $this->MerchantKey = null;
		while ( $o = $ret->fetch () ) {
			$this->MerchantIdCode = $o->MerchantId;
			$this->MerchantId 		= $this->Security->Decode($o->MerchantId);
			if($this->MerchantKey == html_entity_decode($this->Security->Decode($o->MerchantKey)))
				$this->MerchantKey 		= $this->Security->Decode($o->MerchantKey);
			else 
			{
				$this->MerchantKey 		= null;
				$this->MerchantId		= null;
			}
			$this->CheckoutUrl 		= $o->CheckoutUrl;
		}
		
		if(empty($this->MerchantKey) or empty($this->MerchantId))
		{
			exit(json_encode($this->Error($Get['id'], '-32504', __METHOD__)));
		}
	}
	
// -- метод	
	/* -- Если оплата возможна — метод CheckPerformTransaction возвращает результат allow. Если оплата невозможна метод возвращает ошибку. 
	 * На этапе проверки возможности проведения транзакции, рекомендуется проверить все системы задействованные при выполнении методов: CreateTransaction и PerformTransaction. 
	 * Если нарушена работа хотя бы одной из задействованных систем, то при выполнении вышеуказанных методов необходимо вернуть ошибку -32400 (Системная ошибка).
	 * */
	public function CheckPerformTransaction($Get, $Sql = null){
		
		$this->BD->sql = "SELECT t.transaction_id, 
								 t.amount
						    FROM {TABLE_PREFIX}payme_transactions t
						   WHERE t.transaction_id = :p_order_id
						     AND t.state IN ('0', '1')";
		
		if(isset($Get['params']['account']['order_id']))
			$this->BD->param(':p_order_id', $Get['params']['account']['order_id']);
		if(isset($Get['params']['amount']))
			$this->BD->param(':p_amount', $Get['params']['amount']);
		$ret = $this->BD->query ();
		$Param = $this->Error($Get['id'], '-31050', __METHOD__);
		while ( $o = $ret->fetch () ) {
			$this->transaction_id 		= $o->transaction_id;
			$Param = array();
			if($o->amount == $Get['params']['amount'])
			{
				$this->BD->sql = "UPDATE {TABLE_PREFIX}payme_transactions Set state = '1'
						           WHERE transaction_id = :p_transaction_id";
				$this->BD->param(':p_transaction_id', $o->transaction_id);
				$ret = $this->BD->query ();
				//$this->DopSelect('CheckPerformTransaction', $Sql, $o);
				$Param['result']['allow'] = true;
			}
			else
				$Param = $this->Error($Get['id'], '-31001', __METHOD__.':amount');
			break;
		}
		
		return $Param;
	}
	/*
	 * Метод CreateTransaction возвращает список получателей платежа. Когда инициатор платежа является получателем, поле receivers можно опустить или присвоить ему значение NULL. 
	 * Если транзакция уже создана, приложение мерчанта производит базовую проверку транзакции и возвращает результат проверки в Paycom.
	*/
	public function CreateTransaction($Get, $Sql = null){
		$this->BD->sql = "SELECT t.transaction_id, 
								 t.cms_order_id,
								 t.order_id,
								 t.amount,
								 t.paycom_transaction_id,
								 t.state
						    FROM {TABLE_PREFIX}payme_transactions t
						   WHERE t.transaction_id = :p_order_id
						    /* AND t.state IN ('1')*/";
		
		$this->BD->param(':p_order_id', $Get['params']['account']['order_id']);
		if(isset($Get['params']['amount']))
			$this->BD->param(':p_amount', $Get['params']['amount']);
		$ret = $this->BD->query ();
		
		$Param = $this->Error($Get['id'], '-31050', __METHOD__);
		
		while ( $o = $ret->fetch () ) {
			if($o->amount == $Get['params']['amount'] and $o->state == 1)
			{
				if($Get['params']['id'] == $o->paycom_transaction_id or is_null($o->paycom_transaction_id))
				{
					$this->transaction_id 		= $o->transaction_id;
					$Param = array();
				
					if(is_null($o->paycom_transaction_id)){
						$this->BD->sql = "UPDATE {TABLE_PREFIX}payme_transactions 
											 Set paycom_transaction_id = :p_paycom_transaction_id, paycom_time = :p_paycom_time, paycom_time_datetime = :p_paycom_time_datetime
							           	   WHERE transaction_id = :p_transaction_id";
						$this->BD->param(':p_transaction_id', $o->transaction_id);
						$this->BD->param(':p_paycom_transaction_id', $Get['params']['id']);
						$this->BD->param(':p_paycom_time_datetime', Format::timestamp2datetime($Get['params']['time']));
						$this->BD->param(':p_paycom_time', $Get['params']['time']);
						$ret = $this->BD->query ();
						
					//	$this->DopSelect('CreateTransaction', $Sql, $o);
					}
					$Param = $this->Result($Get['id'], $this->transaction_id);
				
				}
			}
			else if($o->amount == $Get['params']['amount'] and $o->state != 1)
			{
				$Param = $this->Error($Get['id'], '-31050', __METHOD__.':state');
			}
			else
				$Param = $this->Error($Get['id'], '-31001', __METHOD__.':amount');
		}
		
		return $Param;
	}
	
	
	/*
	 * Метод CancelTransaction отменяет как созданную, так и проведенную транзакцию.
	 * */
	public function CancelTransaction($Get, $Sql = null){

		$this->BD->sql = "SELECT t.transaction_id, 
								 t.cms_order_id,
								 t.order_id,
								 t.amount,
								 t.paycom_transaction_id,
								 t.state
						    FROM {TABLE_PREFIX}payme_transactions t
						   WHERE t.paycom_transaction_id = :p_paycom_transaction_id";
		
		$this->BD->param(':p_paycom_transaction_id', $Get['params']['id']);
		$ret = $this->BD->query ();
		$Param = $this->Error($Get['id'], '-31050', __METHOD__);
		
		while ( $o = $ret->fetch () ) {
			if(in_array($o->state, array(1,2, -1, -2)))
			{
				$this->transaction_id 		= $o->transaction_id;
				$Param = array();
				
				if($Get['params']['id'] == $o->paycom_transaction_id and in_array($o->state, array(1,2)))
				{
					$this->BD->sql = "UPDATE {TABLE_PREFIX}payme_transactions 
							             Set reason=:p_reason, state = :p_state, cancel_time=NOW()
						           	   WHERE transaction_id = :p_transaction_id";
					$this->BD->param(':p_transaction_id', $o->transaction_id);
					$this->BD->param(':p_reason', $Get['params']['reason']);
					$this->BD->param(':p_state', $o->state==1?-1:-2);
					
					$ret = $this->BD->query ();
					$this->DopSelect('CancelTransaction', $Sql, $o);
				
				}
			//	print_r($sql);exit();
				$Param = $this->Result($Get['id'], $this->transaction_id);

			}
			else
				$Param = $this->Error($Get['id'], -31007, __METHOD__.':state');
		}
		
		return $Param;
	}
	/*
	 * CheckTransaction
	 * */
	public function CheckTransaction($Get, $Sql = null){
		$this->BD->sql = "SELECT t.transaction_id, 
								 t.cms_order_id,
								 t.amount,
								 t.state
						    FROM {TABLE_PREFIX}payme_transactions t
						   WHERE t.paycom_transaction_id = :p_paycom_transaction_id";
		
		$this->BD->param(':p_paycom_transaction_id', $Get['params']['id']);
		$ret = $this->BD->query ();
		$Param = $this->Error($Get['id'], '-31050', __METHOD__);
		
		while ( $o = $ret->fetch () ) {
			$this->transaction_id 		= $o->transaction_id;
			$Param = array();
			$Param = $this->Result($Get['id'], $this->transaction_id);
		}
		
		return $Param;
	}
	
	/* 
	 * Метод PerformTransaction зачисляет средства на счет мерчанта и выставляет у заказа статус «оплачен». 
	 */
	public function PerformTransaction($Get, $Sql = null){
		$this->BD->sql = "SELECT t.transaction_id, 
								 t.amount,
								 t.paycom_transaction_id,
								 t.state,
								 t.cms_order_id,
								 t.order_id
						    FROM {TABLE_PREFIX}payme_transactions t
						   WHERE t.paycom_transaction_id = :p_paycom_transaction_id";
		
		$this->BD->param(':p_paycom_transaction_id', $Get['params']['id']);
		$ret = $this->BD->query ();
		$Param = $this->Error($Get['id'], '-31050', __METHOD__);
		
		while ( $o = $ret->fetch () ) {

			$this->transaction_id 		= $o->transaction_id;
			$Param = array();
			
			if($Get['params']['id'] == $o->paycom_transaction_id and in_array($o->state, array(1)))
			{
				$this->BD->sql = "UPDATE {TABLE_PREFIX}payme_transactions 
						             Set perform_time=NOW(), state = :p_state, cancel_time=null
					           	   WHERE transaction_id = :p_transaction_id";
				$this->BD->param(':p_transaction_id', $o->transaction_id);
				$this->BD->param(':p_state', 2);
				
				$ret = $this->BD->query ();
				
				$this->DopSelect('PerformTransaction', $Sql, $o);
			}
			
			$Param = $this->Result($Get['id'], $this->transaction_id);
		}
		
		return $Param;
	}
	/*
	 * Метод ChangePassword Для изменения пароля доступа биллингу мерчанта Paycom использует метод ChangePassword 
	 */
	public function ChangePassword($Get, $Sql = null){
		$this->BD->sql = "SELECT t.merchant_id,
								 IF(is_flag_test IN ('Y'), t.merchant_key_test, t.merchant_key) MerchantKey
						    FROM {TABLE_PREFIX}payme_config t
						   WHERE t.merchant_id = :p_merchant_id";
		
		$this->BD->param(':p_merchant_id',$this->MerchantIdCode);
		$ret = $this->BD->query ();
		
		$Param = $this->Error($Get['id'], '-32400', __METHOD__); //32504
		$Param = array("result"=>array('success'=>$this->BD->sql));
		while ( $o = $ret->fetch () ) {
			$this->BD->sql = "UPDATE {TABLE_PREFIX}payme_config 
								 Set merchant_key = :p_merchant_key WHERE merchant_id = :p_merchant_id";
			$this->BD->param(':p_merchant_id', $this->MerchantIdCode);
			$this->BD->param(':p_merchant_key', $this->Security->Encode($Get['params']['password']));
			$ret = $this->BD->query();
			$Param = array("result"=>array('success'=>true));
		}
	
		return $Param;
	}
//--	
	public function Error(
			$Id, 				// Идентификатор транзакции Paycom. 
			$CodeError=null,	// Код ошибки.
			$Data = __METHOD__	// Дополнительные сведения об ошибке.
			){
		$Param['id'] = $Id;
		$Param['error'] = array(
				'code'=>(int)$CodeError,
				'message'=>array("ru"=>$this->Sp_Error($CodeError, 'ru'),"uz"=>$this->Sp_Error($CodeError, 'uz'),"en"=>$this->Sp_Error($CodeError, 'en')), // Локализованный текст сообщения об ошибке. Сообщение выводится пользователю.
				"data" => $Data,
				"time"=>Format::timestamp(true)
		);
		
		return $Param;
	}
	public function Result($Id, $Transaction_id)
	{
		
		$this->BD->sql = "SELECT t.transaction_id,
							     t.paycom_transaction_id,
							     t.paycom_time,
							     t.paycom_time_datetime,
							     t.create_time,
							     t.perform_time,
							     t.cancel_time,
							     t.amount,
							     t.state,
							     t.reason,
							     t.receivers,
							     t.order_id,
							     t.cms_order_id
						    FROM {TABLE_PREFIX}payme_transactions t
						   WHERE t.transaction_id = :p_transaction_id";
		
		$this->BD->param(':p_transaction_id', $Transaction_id);
		$ret = $this->BD->query ();
		$Param = $this->Error($Id, '-31003', __METHOD__);
		
		while ( $o = $ret->fetch () ) {
			$Param = array();
			$Param['id'] = $Id;
			$Param['result'] = array(
					'id'			=> $o->paycom_transaction_id,
					'receivers'		=> $o->receivers,
					"perform_time"	=> is_null($o->perform_time)?0:(Format::datetime2timestamp($o->perform_time)*1000),  // Время проведения транзакции в биллинге мерчанта.
					"state"			=> (int)$o->state,
					"create_time"	=> is_null($o->create_time)?0:(Format::datetime2timestamp($o->create_time)*1000),
					"cancel_time"	=> is_null($o->cancel_time)?0:(Format::datetime2timestamp($o->cancel_time)*1000),
					"reason"		=> is_null($o->reason)?null:(int)$o->reason,
					"transaction"	=> $o->order_id, //Номер или идентификатор транзакции в биллинге мерчанта. Формат строки определяется мерчантом.
					"time"			=> (int)$o->paycom_time
			);
		}
		return $Param;
	}
// настроекаларни саклаб куяди
	public function PaymeConfig($Get=null){
		$this->CreateTable();
		$this->BD->sql = "INSERT INTO `{TABLE_PREFIX}payme_config` (`kass_id`, redirect, `merchant_id`, `merchant_key`, `merchant_key_test`, `endpoint_url`, `endpoint_url_pay_sys`, `is_flag_test`, `is_flag_send_tovar`, `callback_timeout`) VALUES
							(1, :p_redirect, :p_merchant_id, :p_merchant_key, :p_merchant_key_test, :p_endpoint_url, :p_endpoint_url_pay_sys, :p_is_flag_test, :p_is_flag_send_tovar, IFNULL(:p_callback_timeout, 0))
							 ON DUPLICATE KEY UPDATE 
							    merchant_id = :p_merchant_id,
							    merchant_key = :p_merchant_key,
							    merchant_key_test = :p_merchant_key_test,
							    endpoint_url = :p_endpoint_url,
							    endpoint_url_pay_sys = :p_endpoint_url_pay_sys,
							    is_flag_test = :p_is_flag_test,
							    is_flag_send_tovar = :p_is_flag_send_tovar,
							    callback_timeout = IFNULL(:p_callback_timeout, 0),
								redirect =:p_redirect";
		$this->BD->param(':p_merchant_id', $this->Security->Encode($Get['merchant_id']));  
		$this->BD->param(':p_merchant_key_test', $this->Security->Encode($Get['merchant_key_test'])); 
		$this->BD->param(':p_merchant_key',  $this->Security->Encode($Get['merchant_key']));
		
		$this->BD->param(':p_redirect', $Get['redirect']);
		$this->BD->param(':p_endpoint_url_pay_sys', $Get['checkout_url']);
		$this->BD->param(':p_endpoint_url', $Get['endpoint_url']);
		$this->BD->param(':p_is_flag_test', $Get['status_test']);
		$this->BD->param(':p_is_flag_send_tovar', $Get['status_tovar']);
		$this->BD->param(':p_callback_timeout', $Get['callback_pay']);
		$Param = $this->BD->query();
		//exit($this->BD->sql);
		return $Param;
	}
	//-- Заказни яратади
	public function InsertOredr($Get=null){
		$this->BD->sql = "INSERT INTO `{TABLE_PREFIX}payme_s_state` (`code`, `name`) VALUES
							(-2, 'Транзакция отменена после завершения (начальное состояние 2).'),
							(-1, 'Транзакция отменена (начальное состояние 1).'),
							(0, 'ожидание подтверждения'),
							(1, 'Транзакция успешно создана, ожидание подтверждения (начальное состояние 0).'),
							(2, 'Транзакция успешно завершена (начальное состояние 1).'),
							(3, 'Заказ выполнен. Невозможно отменить транзакцию. Товар или услуга предоставлена покупателю в полном объеме.');";
		$ret = $this->BD->query();
		$this->BD->sql = "INSERT INTO {TABLE_PREFIX}payme_transactions VALUES
							(Null, NULL, :p_paycom_time, NOW(), NOW(), null, NULL, :p_amount, 0, NULL, NULL, :p_order_id, :p_cms_order_id, :p_is_flag_test)";
	
		$this->BD->param(':p_paycom_time', Format::timestamp2milliseconds(time()));
		
		$this->BD->param(':p_amount', $Get['Amount']);  
		
		$this->BD->param(':p_order_id', is_null($Get['OrderId'])?0:$Get['OrderId']); 
		
		$this->BD->param(':p_cms_order_id', is_null($Get['CmsOrderId'])?0:$Get['CmsOrderId']); 
		$this->BD->param(':p_is_flag_test', $Get['IsFlagTest']);
	
		$Param = $this->BD->query();
		$Param = array();
		$this->BD->sql = "SELECT t.transaction_id,
							     t.paycom_transaction_id,
							     t.paycom_time,
							     t.paycom_time_datetime,
							     t.create_time,
							     t.perform_time,
							     t.cancel_time,
							     t.amount,
							     t.state,
							     t.reason,
							     t.receivers,
							     t.order_id,
							     t.cms_order_id,
								 c.redirect
						    FROM {TABLE_PREFIX}payme_transactions t
							Join {TABLE_PREFIX}payme_config c 
							  On 1 = 1
						   WHERE t.cms_order_id = :p_order_id
				 			Limit 0,1";
		$this->BD->param(':p_order_id', is_null($Get['OrderId'])?0:$Get['OrderId']);
		$ret = $this->BD->query ();
		$Param = $this->Error($Get['OrderId'], '-31003', __METHOD__);
		while ( $o = $ret->fetch () ) {
			$Param = array();
			$Param = array(
					'id'			=> $o->transaction_id,
					'Redirect'		=> $o->redirect,
					'paycom_time'	=> $o->paycom_time,
					/*"transaction"	=> $o->order_id, //Номер или идентификатор транзакции в биллинге мерчанта. Формат строки определяется мерчантом.
					"transaction"	=> $o->cms_order_id,*/
					"time"			=> (int)$o->paycom_time
			);
		}
		return $Param;
	}
	//-- Def настроекаларни юклиди
	public function Insert($Sql=null){
		if(is_array($Sql))
		{
			foreach($Sql as $k=>$v)
			{
				$this->BD->sql = $v;
				$Param = $this->BD->query();
			}
		}
		return $Param;
	}
	
	private function CreateTable(){
		$SqlList = $this->ScanFile("/Install_Bd/");
		
		if(!is_null($SqlList))
		{
			foreach($SqlList as $k=>$v)
			{
				$this->BD->sql = $v;
				$Param = $this->BD->query();
			}
		}
		//echo'<pre>';print_r($Get);exit('<pre>');
		// DROP TABLE `payme_config`, `payme_s_state`, `payme_transactions`;
	} 
	
	private function ScanFile($BasePath)
	{
		$FileList = null;
		$dir = opendir(__DIR__.$BasePath);
		$i=0;
		while($file = readdir($dir))
		{
			if (($file != ".") && ($file != "..")) {
				if(is_file(__DIR__.$BasePath.$file))
				{
					$FileList[] = file_get_contents(__DIR__.$BasePath.$file);
				}
			}
		}
		closedir($dir);
		
		return $FileList;
	}
	private function DopSelect($Type, $Sql, $o){
		if(!is_null($Sql))
		{
			if(is_array($Sql[$Type]))
			{
				foreach($Sql[$Type] as $k=>$v)
				{
					$this->BD->sql = str_replace('{TABLE_PREFIX}', TABLE_PREFIX, $v['Sql']);
					$this->BD->param(':p_transaction_id', $this->transaction_id);
					if(isset($o->cms_order_id))
						$this->BD->param(':p_cms_order_id', $o->cms_order_id);
					if(isset($o->cms_order_id))
						$this->BD->param(':p_order_id', $o->order_id);

					foreach($v['Param'] as $k1=>$v1)
					{
						$this->BD->param($k1, $v1);
					}
					$ret = $this->BD->query ();
					/*
					if($Type=='CancelTransaction')
						print_r($this->BD->sql);exit();*/
					//	$sql[] = $this->BD->sql;
				}
			}
		}
	}
// Тулов утганлиги тугрисида малумот
	public function OrderReturn($order_id=null){
		if(is_null($order_id)){
			$order_id = $_GET['order_id'];
		}
		$return = $this->Result('', $order_id);
		$GetHtml = file_get_contents(__DIR__.'/View/OrderReturn.html');
		//print_r($return); exit();
		$Str = $this->TransactionState($return['result']['state']);
		$GetHtml = str_replace('{OREDR_RETURN}', $Str, $GetHtml);
	
		return $GetHtml;
	}

//  Состояния транзакции (Transaction State)
	public function TransactionState($code){
		$State[0] = 'Транзакция успешно создана.';
		$State[1] = 'Транзакция успешно создана, ожидание подтверждения.';
		$State[2] = 'Транзакция успешно завершена.';
		$State[-1] = 'Транзакция отменена.';
		$State[-2] = 'Транзакция отменена после завершения.';
		
		return $State[$code];
	}	
//  Причина отмены транзакции (Reason)
	public function Reason($code){
		$State[1] = 'Один или несколько получателей не найдены или не активны в Paycom.';
		$State[2] = 'Ошибка при выполнении дебетовой операции в процессингом центре.';
		$State[3] = 'Ошибка выполнения транзакции.';
		$State[4] = 'Транзакция отменена по таймауту.';
		$State[5] = 'Возврат денег.';
		$State[10] = 'Неизвестная ошибка.';
		
		return $State[$code];
	}
//  Состояния чека
	public function Cheka($code){
		$State[0] = 'Чек создан. Ожидание подтверждения оплаты.';
		$State[1] = 'Первая стадия проверок. Создание транзакции в биллинге мерчанта.';
		$State[2] = 'Списание денег с карты.';
		$State[3] = 'Закрытие транзакции в биллинге мерчанта.';
		$State[4] = 'Чек оплачен.';
		$State[20] = 'Чек стоит на паузе для ручного вмешательства.';
		$State[21] = 'Чек в очереди на отмену.';
		$State[30] = 'Чек в очереди на закрытие транзакции в биллинге мерчанта.';
		$State[50] = 'Чек отменен.';
		
		return $State[$code];
	}	
	
//	Ошибки, которые возвращает определённый метод, см. в описании метода.
	public function Sp_Error($code, $Lang){
//		Общие ошибки
		$Error['-32300'] = 'Ошибка возникает если метод запроса не POST.';
		$Error['-32700'] = 'Ошибка парсинга JSON.';
		$Error['-32600'] = 'Отсутствуют обязательные поля в RPC-запросе или тип полей не соответствует спецификации.';
		$Error['-32601'] = 'Запрашиваемый метод не найден. В RPC-запросе имя запрашиваемого метода содержится в поле data.';
		$Error['-32504'] = 'Недостаточно привилегий для выполнения метода.';
		$Error['-32400'] = 'Системная (внутренняя ошибка). Ошибку следует использовать в случае системных сбоев: отказа базы данных, отказа файловой системы, неопределенного поведения и т.д.';
//		Ошибки в ответах сервера мерчанта
		$Error['-31001'] = 'Неверная сумма. Ошибка возникает когда сумма транзакции не совпадает с суммой заказа. Актуальна если выставлен одноразовый счёт.';
		$Error['-31003'] = 'Транзакция не найдена.';
		$Error['-31007'] = 'Невозможно отменить транзакцию. Товар или услуга предоставлена потребителю в полном объеме.';
		$Error['-31008'] = 'Невозможно выполнить операцию. Ошибка возникает если состояние транзакции, не позволяет выполнить операцию.';
		$Error['-31050'] = 'Ошибки связанные с неверным пользовательским вводом “account“, например: введенный логин не найден, введенный номер телефона не найден и т.д. В ошибках, локализованное поле “message';
		$Error['-31099'] = '“ обязательно. Поле “data“ должно содержать название субполя “account“.';
		
//		Ошибки метода CheckPerformTransaction
		$Error['-31001'] = 'Неверная сумма.';
		$Error['-31050'] = 'Ошибки неверного ввода данных покупателем account, например: введенный логин не найден, введенный номер телефона не найден и т.д. Локализованное поле “message“ обязательно.';
		$Error['-31099'] = 'Поле “data“ должно содержать название субполя “account“.';
//		Ошибки метода CreateTransaction
		$Error['-31001'] = 'Неверная сумма.';
		$Error['-31008'] = 'Невозможно выполнить операцию.';
		$Error['-31050'] = 'Ошибки неверного ввода данных покупателем account, например: не найден введёный логин, не найден введенный номер телефона и т.д. Локализованное поле message обязательно.';
		$Error['-31099'] = 'Поле data должно содержать название субполя account.';
//		Ошибки метода PerformTransaction
		$Error['-31003'] = 'Транзакция не найдена.';
		$Error['-31008'] = 'Невозможно выполнить данную операцию.';
		$Error['-31050'] = 'Ошибки неверного ввода данных покупателем account, например: не найден введёный логин, не найден введенный номер телефона и т.д. Локализованное поле message обязательно.';
		$Error['-31099'] = 'Поле data должно содержать название субполя account.';
// 		Ошибки метода CancelTransaction
		$Error['-31003'] = 'Транзакция не найдена.';
		$Error['-31007'] = 'Заказ выполнен. Невозможно отменить транзакцию. Товар или услуга предоставлена покупателю в полном объеме.';
// 		Ошибки метода CheckTransaction
		$Error['-31003'] = 'Транзакция не найдена';
		return $Error[$code];
	}
	
	//-- Тест учун ясалган
	public function Test($Get, $not_json =false){
		$Url = 'http://magento.uz/payme/callback/start';
		$this->Param = $Get;
		/*$this->Param['jsonrpc'] = '2.0';
		$this->Param['id'] = '1';
		$this->Param['method'] = 'PerformTransaction';
		$this->Param['params'] = array('id'=>1111111111);*/
	
		$ch = curl_init();
		$content = json_encode($this->Param);
	
		curl_setopt($ch, CURLOPT_URL, $Url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'Authorization:Basic '.base64_encode('Paycom:&qtyKDwu8HXm80rRxyXMDmSbQmIIs8G99cST')));
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	
		$server_output = curl_exec ($ch);
		curl_close ($ch);
		$this->Param = json_decode($server_output, true);
	
		if($not_json) exit($server_output);
		$this->Out = $server_output;
	
		return $this->Param;
	}
}
?>