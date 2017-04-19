<?php
class ModelTotalFreeShip extends Model {
	public function getTotal(&$total_data, &$total, &$taxes) {
		$this->language->load('total/free_ship');
                                            $this->load->model('setting/setting');
                                        
                                            if($this->config->get('free_ship_status') && isset($this->session->data['shipping_method']['code']) && isset($this->session->data['payment_method']['code'])){
                                                    $ship_method = $this->config->get('ship_method');
                                                    $kind = $this->config->get('kind');
                                                    $condition_value = $this->config->get('condition_value');
                                                    $status = $this->config->get('status');
                                                    for($i=0;$i<10;$i++) {
                                                    $shipping = explode('.', $this->session->data['shipping_method']['code']);
                                                      
                                                      if( $status[$i] == '1' &&  $shipping[0] == $ship_method[$i] &&  $this->session->data['payment_method']['code'] == $kind[$i] && $this->cart->getSubTotal() >= $condition_value[$i])
                                                      {$free_ship = 0 -  $this->session->data['shipping_method']['cost'];
                                                            $total_data[] = array(
			'code'       => 'free_ship',
			'title'      => $this->language->get('text_free_ship'),
			'text'       => $this->currency->format($free_ship),
			'value'      =>  $free_ship,
			'sort_order' => $this->config->get('free_ship_sort_order')
                                                                                                    );
                                                            $total -= $this->session->data['shipping_method']['cost']; 
                                                            break;}
                                                        
                                                      elseif(  $status[$i] == '1' &&  'all' == $ship_method[$i] &&  $this->session->data['payment_method']['code'] == $kind[$i] &&  $this->cart->getSubTotal()>= $condition_value[$i])
                                                      {$free_ship = 0 -  $this->session->data['shipping_method']['cost'];
                                                            $total_data[] = array(
			'code'       => 'free_ship',
			'title'      => $this->language->get('text_free_ship'),
			'text'       => $this->currency->format($free_ship),
			'value'      =>  $free_ship,
			'sort_order' => $this->config->get('free_ship_sort_order')
                                                                                                    );
                                                            $total -= $this->session->data['shipping_method']['cost']; 
                                                            break;}
                                                      
                                                      elseif( $status[$i] == '1' &&  $this->session->data['shipping_method']['code'] == $ship_method[$i] &&  'all' == $kind[$i] &&  $this->cart->getSubTotal()>= $condition_value[$i])
                                                          
                                                      {$free_ship = 0 -  $this->session->data['shipping_method']['cost'];
                                                            $total_data[] = array(
			'code'       => 'free_ship',
			'title'      => $this->language->get('text_free_ship'),
			'text'       => $this->currency->format($free_ship),
			'value'      =>  $free_ship,
			'sort_order' => $this->config->get('free_ship_sort_order')
                                                                                                    );
                                                            $total -= $this->session->data['shipping_method']['cost']; 
                                                            break;}
                                                      
                                                      elseif( $status[$i] == '1' &&   $shipping[0] == $ship_method[$i] &&  'all' == $kind[$i] &&  $this->cart->getSubTotal()>= $condition_value[$i])
                                                      {$free_ship = 0 -  $this->session->data['shipping_method']['cost'];
                                                            $total_data[] = array(
			'code'       => 'free_ship',
			'title'      => $this->language->get('text_free_ship'),
			'text'       =>$this->currency->format($free_ship),
			'value'      =>  $free_ship,
			'sort_order' => $this->config->get('free_ship_sort_order')
                                                                                                    );
                                                            $total -= $this->session->data['shipping_method']['cost']; 
                                                            break;}
                                                      
                                                      elseif( $status[$i] == '1' &&  'all' == $ship_method[$i] &&  'all' == $kind[$i] && $this->cart->getSubTotal()>= $condition_value[$i])
                                                      {$free_ship = 0 -  $this->session->data['shipping_method']['cost'];
                                                            $total_data[] = array(
			'code'       => 'free_ship',
			'title'      => $this->language->get('text_free_ship'),
			'text'       => $this->currency->format($free_ship),
			'value'      =>  $free_ship,
			'sort_order' => $this->config->get('free_ship_sort_order')
                                                                                                    );
                                                            $total -= $this->session->data['shipping_method']['cost']; 
                                                            break;}
                                                        
                                                            
                                                    }
                                            }
	}
}
?>
