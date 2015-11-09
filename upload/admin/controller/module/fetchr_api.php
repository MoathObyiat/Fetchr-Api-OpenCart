<?php
/*
  @Controller: Fetchr Api Controller.
  @Author: Moath Mobarak.
  @Version: 1.0.0
*/
class ControllerModuleFetchrApi extends Controller 
{

  private $error = array();

  public function index() 
  {
    $this->load->model('fetchrapi/fetchr');
    //Install api congig table & added fetchr status.
    $install = $this->model_fetchrapi_fetchr->install();

    $this->load->language('module/fetchr_api');

    $this->document->setTitle($this->language->get('heading_title'));
    $this->getForm();

  }

  public function getForm()
  {
    $config_data = $this->model_fetchrapi_fetchr->getConfig();

    //Get title in form using fetchr language.
    $data['heading_title'] = $this->language->get('heading_title');
    $data['text_form'] = $this->language->get('text_config');

    $data['text_delivery'] = $this->language->get('text_delivery');
    $data['text_fulfildelivery'] = $this->language->get('text_fulfildelivery');
    $data['text_live'] = $this->language->get('text_live');
    $data['text_staging'] = $this->language->get('text_staging');
    $data['entry_username'] = $this->language->get('entry_username');
    $data['entry_password'] = $this->language->get('entry_password');
    $data['entry_servicetype'] = $this->language->get('entry_servicetype');
    $data['entry_accounttype'] = $this->language->get('entry_accounttype');

    $data['button_save'] = $this->language->get('button_save');
    $data['button_cancel'] = $this->language->get('button_cancel');
    $data['button_push'] = 'Push Order';

    //Show error msg.
    if (isset($this->session->data['api_error'])) {
      $data['api_error'] = $this->session->data['api_error'];

      unset($this->session->data['api_error']);
    } else {
      $data['api_error'] = '';
    }

    if (isset($this->error['warning'])) {
      $data['error_warning'] = $this->error['warning'];
    } else {
      $data['error_warning'] = '';
    }

    if (isset($this->error['username'])) {
      $data['error_username'] = $this->error['username'];
    } else {
      $data['error_username'] = '';
    }

    if (isset($this->error['password'])) {
      $data['error_password'] = $this->error['password'];
    } else {
      $data['error_password'] = '';
    }

    //Show breadcrumbs.
    $data['breadcrumbs'] = array();

    $data['breadcrumbs'][] = array(
      'text' => $this->language->get('text_home'),
      'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
    );

    $data['breadcrumbs'][] = array(
      'text' => $this->language->get('heading_title'),
      'href' => $this->url->link('module/fetchr_api', 'token=' . $this->session->data['token'], 'SSL')
    );

    //Push, Save & Cancel button.
    $data['push'] = $this->url->link('module/fetchr_api/orderList', 'token=' . $this->session->data['token'], 'SSL');
   
    $data['action'] = $this->url->link('module/fetchr_api/add', 'token=' . $this->session->data['token'], 'SSL');

    $data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');

    //Fill data in form.
    foreach ($config_data as $key => $value) {
      if ($value['key_config'] == 'username') {
          $data['username'] = $value['value'];

      } else {
        $data['username'] = '';
      }

      if ($value['key_config'] == 'password') {
          $data['password'] = $value['value'];

      } else {
        $data['password'] = '';
      }

      if ($value['key_config'] == 'servicetype') {
          $data['servicetype'] = $value['value'];

      } 
      if ($value['key_config'] == 'accounttype') {
          $data['accounttype'] = $value['value'];

      } else {
        $data['accounttype'] = '';
      }
    }

    $data['header'] = $this->load->controller('common/header');
    $data['column_left'] = $this->load->controller('common/column_left');
    $data['footer'] = $this->load->controller('common/footer');

    $this->response->setOutput($this->load->view('module/fetchr_api_form.tpl', $data));

  }

  public function add() 
  {

    $this->load->language('module/fetchr_api');
    $this->document->setTitle($this->language->get('heading_title'));
    $this->load->model('fetchrapi/fetchr');

    if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
  
      $this->model_fetchrapi_fetchr->saveConfig($this->request->post);
      $this->session->data['success'] = $this->language->get('text_success');

      $this->response->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));

    }else{
      $this->getForm();
    }

  }

  public function orderList() 
  {
    $datalist = [];
    $this->load->model('fetchrapi/fetchr');

    $config_data = $this->model_fetchrapi_fetchr->getConfig();
    foreach ($config_data as $key => $value) {
      
      if ($value['key_config'] == 'accounttype') {
        $accounttype = $value['value'];
      }
      if ($value['key_config'] == 'servicetype') {
        $servicetype = $value['value'];
      }
      if ($value['key_config'] == 'username') {
        $username = $value['value'];
      }
      if ($value['key_config'] == 'password') {
        $password = $value['value'];
      }
      
    }

    //Fetch orders based status 'Ready for Pick up'.
    $orders = $this->model_fetchrapi_fetchr->getOrders();
    
    if (!empty($orders)) {
   
      //Service type = 1 (Fulfilment+Delivery), Service type = 0 (Delivery).
      if ($servicetype == 1) {

        foreach($orders as $key => $order) 
        {
          $item_list = [];

          $products = $this->model_fetchrapi_fetchr->getOrderProducts($order['order_id']);
          $store_info = $this->model_fetchrapi_fetchr->getSetting('config', $order['store_id']);
          $store_shiprate = $this->model_fetchrapi_fetchr->getSetting('cod', $order['store_id']);

          for($i=0;$i<=count($products)-1;$i++)
          {
            $item_list[] = array(
                'client_ref'    => $order['order_id'],
                'name'          => $products[$i]['name'],
                'sku'           => strtolower($products[$i]['sku']),
                'quantity'      => $products[$i]['quantity'],
                'merchant_details' => array(
                    'mobile'  => $store_info['config_telephone'],
                    'phone'   => $store_info['config_telephone'],
                    'name'    => $store_info['config_owner'],
                    'address'   => $store_info['config_address']
                    ),
                'COD'   =>  (!empty($store_shiprate['cod_total'])) ? $store_shiprate['cod_total']: '',
                'price' => $products[$i]['price'],
                'is_voucher' => 'No'
              );
          }

          $datalist[] = array(
            'order' => array(
                'items' => $item_list,
                'details' => array(
                  'status' => '',
                  'discount' => 0,
                  'grand_total' => $order['total'],
                  'payment_method' => $order['payment_code'],
                  'order_id' => $order['order_id'],
                  'customer_firstname' => $order['firstname'],
                  'customer_lastname' => $order['lastname'],
                  'customer_mobile' => $order['telephone'],
                  'customer_email' => $order['email'],
                  //'order_country' => 'AE',
                  'order_address' => $order['payment_address_1']
                )
              )
          );
          $this->send_Fulfilment_Delivery_ToErp($datalist, $order['order_id'], $username, $password);
          unset($datalist);
        }        
        
        //If count order = 1 go view order else go order list.
        if (count($orders) == 1) {
          $this->response->redirect($this->url->link('sale/order/info', 'token=' . $this->session->data['token']. '&order_id=' . $orders[0]['order_id'], 'SSL'));
        }else{
          $this->response->redirect($this->url->link('sale/order', 'token=' . $this->session->data['token'], 'SSL'));
        }
        
      }else{
        
        foreach($orders as $key => $order)
        {
          $data[] = array(
              'order_reference'    => $order['order_id'],
              'name'    => $order['firstname'] . ' ' . $order['lastname'],
              'email'    => $order['email'],
              'phone_number'    => $order['telephone'],
              'address'    => $order['payment_address_1'],
              'city'   => $order['payment_city'],
              'payment_type'   => $order['payment_code'],
              'amount'   => $order['total'],
              'description'   => 'None',
              'comments'  =>  $order['comment']
          );
        }
        
        $datalist = array(
          'username' => $username,
          'password' => $password, 
          'method' => 'create_orders',
          'pickup_location'=>'Dummy location for pickup 001',
          'data' => $data,
        );

        $this->send_Delivry_ToErp($datalist);
        
        //If count order = 1 go view order else go order list.
        if (count($orders) == 1) {
          $this->response->redirect($this->url->link('sale/order/info', 'token=' . $this->session->data['token']. '&order_id=' . $orders[0]['order_id'], 'SSL'));
        }else{
          $this->response->redirect($this->url->link('sale/order', 'token=' . $this->session->data['token'], 'SSL'));
        }
      }
    }else{
      $this->session->data['api_error'] = 'Warning: Not found orders push!';
      $this->response->redirect($this->url->link('module/fetchr_api/', 'token=' . $this->session->data['token'], 'SSL'));
    }

  }

  //Fulfilment+Delivery send To ERP.
  protected function send_Fulfilment_Delivery_ToErp($data, $orderId, $username, $password)
  {
    $response = null;
    try {
        
        $ERPdata = 'ERPdata='.json_encode($data);
        $ch = curl_init();
        $url = 'http://www.menavip.com/client/gapicurl/';
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $ERPdata.'&erpuser='.$username.'&erppassword=' . $password .'&merchant_name=MENA360 API');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        // validate response
        $decoded_response = (array) json_decode($response);
        
        if(!is_array($decoded_response))
            return $response;

        if ($decoded_response['awb'] == 'SKU not found') {
          //Add error occurrd in error.log
          $this->log->write('Fetcher Shipping Error: ' . $decoded_response['awb']);
          //Show error msg for clint.
          $this->session->data['api_error'] = 'Error occurrd in <strong>Order ID: </strong>' . $orderId . ' <strong> Message: </strong>' . $decoded_response['awb'];
          $this->response->redirect($this->url->link('module/fetchr_api/', 'token=' . $this->session->data['token'], 'SSL'));
        }        

        //IF Tracking num found Save in order and change status to 'Fetchr Shipping'.
        if (!empty($decoded_response["response"]->tracking_no) && $decoded_response['success']) {
          $save_track = $this->model_fetchrapi_fetchr->saveTrackingStatus($orderId, $decoded_response["response"]->tracking_no);
        
        }else{
          $this->session->data['api_error'] = $this->errorCode();
          $this->response->redirect($this->url->link('module/fetchr_api/', 'token=' . $this->session->data['token'], 'SSL'));
        }
        
    }catch (Exception $e) {
        echo (string) $e->getMessage();
    }

    return $response;
            
  }

  //Delivery send To ERP.
  protected function send_Delivry_ToErp($data)
  {
    $response = null;
    try {
      
      $ERPdata = 'args='.json_encode($data);
      $ch = curl_init();
      $url = 'http://www.menavip.com/client/api/';
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $ERPdata);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      $response = curl_exec($ch);
      curl_close($ch);
      // validate response
      $decoded_response = json_decode($response, true);

      if(!is_array($decoded_response))
          return $response;

      //IF Tracking number found Save in order and change status to 'Fetchr Shipping'.
      foreach ($data['data'] as $key => $value) {
        if (!empty($decoded_response[$value['order_reference']]) && $decoded_response['status'] == 'success') {
            
            $save_track = $this->model_fetchrapi_fetchr->saveTrackingStatus($value['order_reference'], $decoded_response[$value['order_reference']]);
          
        }else{
          $this->session->data['api_error'] = $this->errorCode($decoded_response['error_code']);
          $this->response->redirect($this->url->link('module/fetchr_api/', 'token=' . $this->session->data['token'], 'SSL'));

        }
      }

    }catch (Exception $e) {
        echo (string) $e->getMessage();
    }

    return $response;            
  }

  protected function validateForm() 
  {
    if (!$this->user->hasPermission('modify', 'module/fetchr_api')) {
      $this->error['warning'] = $this->language->get('error_permission');
    }

    if ((utf8_strlen($this->request->post['username']) < 3) || (utf8_strlen($this->request->post['username']) > 20)) {
      $this->error['username'] = $this->language->get('error_username');
    }

    if ($this->request->post['password'] || (!isset($this->request->get['user_id']))) {
      if ((utf8_strlen($this->request->post['password']) < 4) || (utf8_strlen($this->request->post['password']) > 20)) {
        $this->error['password'] = $this->language->get('error_password');
      }
    }
    return !$this->error;
  }

  //Fuction handle error code return API and show error msg.
  public function errorCode($code = '')
  {
    $msg = '';
    switch ($code) {
      case 1001:
        $msg = 'Error: Order reference is missing in one of the posted orders.';
        break;
      case 1002:
        $msg = 'Error: Name is missing in one of the posted orders.';
        break;
      case 1003:
        $msg = 'Error: Email is missing in one of the posted orders.';
        break;
      case 1004:
        $msg = 'Error: Phone number is missing in one of the posted orders.';
        break;
      case 1005:
        $msg = 'Error: Address is missing in one of the posted orders.';
        break;
      case 1006:
        $msg = 'Error: City is missing in one of the posted orders.';
        break;
      case 1007:
        $msg = 'Error: Payment type is missing in one of the posted orders.';
        break;
      case 1008:
        $msg = 'Error: Amount is missing in one of the posted orders.';
        break;
      case 1009:
        $msg = 'Error: Description is missing in one of the posted orders.';
        break;
      case 1011:
        $msg = 'Error: Method name not found.';
        break;
      case 1012:
        $msg = 'Error: Client phone number of one of the posted orders does not belong to any of the previously provided clients.';
        break;
      case 1013:
        $msg = 'Error: The posted data contains non ascii characters.';
        break;
      case 1014:
        $msg = 'Error: Internal server error.';
        break;
      case 1015:
        $msg = 'Error: Invalid username or password.';
        break;

      default:
        $msg = 'Error: Access denied.';
        break;
    }
    $this->log->write('Fetcher Shipping Error: ' . $msg);
    return $msg;
  }


}
