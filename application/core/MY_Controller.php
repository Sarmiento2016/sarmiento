<?php
class MY_Controller extends CI_Controller{
	protected $_subject;
    protected $_model;
	protected $logout      = '/login/logout/';
	protected $_session_data;
	protected $_config;
	protected $_upload;
	
	protected $emailCopy	= array();
	protected $emailSubjet	= 'SRP INFO';
	protected $emailFrom	= 'diego.nieto@xnlatam.com';
	protected $emailTo		= 'diego.nieto@xnlatam.com';
	
	
    public function __construct($subjet, $model){
    	$this->_subject		= $subjet;
		$this->_upload 	= './uploads/';
        parent::__construct();
		
		$this->load->library(array('table','pdf'));
		
        if($this->_model != ''){
            $this->load->model($this->_model, 'model');    
        }
        $this->load->model('m_priv_rol');
    }
    
    
/*-------------------------------------------------------------------------------   
 --------------------------------------------------------------------------------
            Tabla de registros
 --------------------------------------------------------------------------------
 --------------------------------------------------------------------------------*/

    function table($mensaje = NULL, $db = NULL){
         $this->load->library('Graficos');
        
        if($mensaje != NULL) {
            $db['mensaje'] = $mensaje;
        }
        
        $db['registros']   = $this->model->getRegistros();
        $this->armar_vista('table', $db);
    }
    
/*-------------------------------------------------------------------------------   
 --------------------------------------------------------------------------------
            ABM Basico
 --------------------------------------------------------------------------------
 --------------------------------------------------------------------------------*/ 
    
    function armar_abm($id = NULL, $db = NULL){
        $vista              = 'abm';
        $db['fields']       = $this->model->getFields();
        $db['id_table']     = $this->model->getId_Table();
        
        if($this->input->post($db['id_table'])){
            $id_post = $this->input->post($db['id_table']);
        }
                
        // DELETE 
        
        if($this->input->post('eliminar')){
            if (method_exists($this, 'before_delete')) {
                $registro = $this->before_delete($id_post);
            }  
            
            $this->model->delete($id_post);
            
            if (method_exists($this, 'after_delete')) {
                $registro = $this->after_delete($id_post);
            }
            
            $db['mensaje']  = 'update_ok';
            $vista = 'table';
        }
        
        // RESTAURAR
        
        if($this->input->post('restaurar')){
            if (method_exists($this, 'before_restore')) {
                $registro = $this->before_restore($id_post);
            }       
            
            $this->model->restore($id_post);
            
            if (method_exists($this, 'after_restore')) {
                $registro = $this->after_restore($id_post);
            }       
            
            $db['mensaje']  = 'update_ok';
            $vista = 'table';
        }
        
        // UPDATE
        
        if($this->input->post('modificar')){
            foreach ($db['fields'] as $field) {
                if($this->input->post($field) !== NULL){
                    $registro[$field] = $this->input->post($field);
                }
            }
        
            if($db['campos'] !== NULL){
                foreach ($db['campos'] as $campo) {
                    if($campo[0] == 'checkbox'){
                        if($this->input->post($campo[1]) !== null){
                            $registro[$campo[1]] = 1;
                        }else{
                            $registro[$campo[1]] = 0;
                        }
                    }
                }    
            } 
            
            if (method_exists($this, 'before_update')) {
                $registro = $this->before_update($registro);
            }       
            
            if(is_array($registro)){
                $this->model->update($registro, $id_post);
                
                if (method_exists($this, 'after_update')) {
                    $registro = $this->after_update($registro, $id_post);
                }  
                 
                $db['mensaje']  = 'update_ok';
                $vista = 'table';    
            }else{
                $db['mensaje']  = $registro;
                $vista          = 'abm';
            }            
        }            
                    
        // INSERT 
            
        if($this->input->post('agregar') || $this->input->post('agregar_per')){
            foreach ($db['fields'] as $field) {
                if($this->input->post($field) !== NULL){
                    $registro[$field] = $this->input->post($field);
                }
            }
            
            if($db['campos'] !== NULL){
                foreach ($db['campos'] as $campo) {
                    if($campo[0] == 'checkbox'){
                        if($this->input->post($campo[1]) !== null){
                            $registro[$campo[1]] = 1;
                        }else{
                            $registro[$campo[1]] = 0;
                        }
                    }
                }    
            }
            
            if (method_exists($this, 'before_insert')) {
                $registro = $this->before_insert($registro);
            }
            
            if(is_array($registro)){
                $id = $this->model->insert($registro);
            
                if (method_exists($this, 'after_insert')) {
                    $registro = $this->after_insert($registro, $id);
                }    
                
                if($this->input->post('agregar')){
                    $db['mensaje']  = 'insert_ok';
                    $vista = 'table';
                } else {
                    $db['mensaje']  = 'insert_ok';
                    $vista = 'abm';
                }
            }else{
                $db['mensaje']  = $registro;
                $vista          = 'abm';
            }  
        }

        // Carga de datos en el formulario
        
        if($id){
            $db['registro'] = $this->model->getRegistros($id); 
        }else{
            $db['registro'] = FALSE;
        }
        
        // ARMADO DE VISTA
        
        if($vista != 'table'){
            $this->armar_vista($vista, $db);
        }else{
            $this->table($db['mensaje']);
        }
    }
	
/*-------------------------------------------------------------------------------	
 --------------------------------------------------------------------------------
 			Armado de vista
 --------------------------------------------------------------------------------
 --------------------------------------------------------------------------------*/
	
	function armar_vista($vista, $db = NULL, $session_data = NULL){
	    if($this->session->userdata('logged_in')){
            $session = $this->session->userdata('logged_in');
                
            $db['menu']             = $this->m_priv_rol->getMenu($session['rol_id'], $session['banco']);
            $db['session_data']     = $session;
            $db['subjet']           = ucwords($this->_subject);
                
            $this->load->view('plantilla/head', $db);
            $this->load->view('plantilla/menu-top');
            $this->load->view('plantilla/menu-left');
            $this->load->view($this->_subject.'/'.$vista);
            $this->load->view('plantilla/footer'); 
        } else  {
            redirect($this->logout, 'refresh');
        }
	}

/*-------------------------------------------------------------------------------	
 --------------------------------------------------------------------------------
 			Armado de vista crud
 --------------------------------------------------------------------------------
 --------------------------------------------------------------------------------*/
	
	public function armar_vista_crud($output = null){
		$db['permisos']		=  $this->getPermisos();
		$db['alertas_user']	=  $this->getAlertas();
			
		if($db['permisos']['ver'] == 0){
			redirect($this->logout, 'refresh');
		}else{
			$db['permisos_menu']	= $this->m_permisos->getRegistros($this->_session_data['id_perfil'], 'id_perfil');
			$db['config']			= $this->_config;
			$db['session_data'] 	= $this->_session_data;
			$db['subjet']			= ucwords($this->_subject);
			
			$this->load->view('plantilla/head', $db);
			$this->load->view('plantilla/menu-top');
			$this->load->view('plantilla/menu-left');
			$this->load->view($this->_subject.'/crud', $output);
			$this->load->view('plantilla/footer');
		}
	}
	
/*-------------------------------------------------------------------------------	
 --------------------------------------------------------------------------------
 			Permisos
 --------------------------------------------------------------------------------
 --------------------------------------------------------------------------------*/	

	function getPermisos(){
		$permisos = $this->m_permisos->getPermisos($this->_subject, $this->_session_data['id_perfil']);
		
		foreach ($permisos as $row) {
			$db['permisos']['ver']			= $row->ver;
			$db['permisos']['agregar']		= $row->agregar;
			$db['permisos']['modificar']	= $row->modificar;
			$db['permisos']['eliminar'] 	= $row->eliminar;
		}

		return $db['permisos'];
	}
	
/*-------------------------------------------------------------------------------	
 --------------------------------------------------------------------------------
 			Cargar alertas del usuario
 --------------------------------------------------------------------------------
 --------------------------------------------------------------------------------*/	
	
	function getAlertas(){
		if($this->_session_data['id_perfil'] == 1){
			$db['alertas_user'] = $this->m_alertas->getAlertas($this->_session_data['id_usuario']);
		}else{
			$db['alertas_user'] = $this->m_alertas->getAlertas($this->_session_data['id_usuario'], $this->_session_data['id_ente']);
		}
		
		return $db['alertas_user'];	
	}


/*-------------------------------------------------------------------------------	
 --------------------------------------------------------------------------------
 			Alertas : usuarios
 --------------------------------------------------------------------------------
 --------------------------------------------------------------------------------*/	
	
	function alerta($alerta){
		$session_data = $this->session->userdata('logged_in');
		
		$registro = array(
			'alerta'		=> $alerta,
			'id_usuario'	=> $session_data['id_usuario'],
			'id_ente'		=> $session_data['id_ente'],
			'visto'			=> 0
		);
		
		$this->m_alertas->insert($registro);
	}

/*-------------------------------------------------------------------------------	
 --------------------------------------------------------------------------------
 			Alertas : bancos
 --------------------------------------------------------------------------------
 --------------------------------------------------------------------------------*/	

	function alerta_banco($alerta){
		$bancos = $this->m_usuarios->getRegistros(1, 'id_perfil');
		
		if($bancos){
			foreach ($bancos as $row) {
				$registro = array(
					'alerta'		=> $alerta,
					'id_usuario'	=> $row->id_usuario,
					'visto'			=> 0
				);
				
				$this->m_alertas->insert($registro);
			}
		}
	}
	
/*-------------------------------------------------------------------------------	
 --------------------------------------------------------------------------------
 			Exportación de tablas
 --------------------------------------------------------------------------------
 --------------------------------------------------------------------------------*/	
	
	function armarExport(){
		$tabla = strip_tags($this->input->post('datos_a_enviar'), '<table><tr><td><th>');
		
		if($this->input->post('export') == 'pdf'){
			foreach ($this->input->post('cabeceras') as $cabecera) {
				if($cabecera != 'Opciones'){
					$cabeceras[] = utf8_decode($cabecera);
				}
			}
			$this->armarPdf($tabla, $cabeceras);
		}else if($this->input->post('export') == 'excel'){
			$this->armarExcel($tabla);
		}else if($this->input->post('export') == 'print'){
			$this->armarPrint($tabla);
		}else{
			$post_data = $this->input->post(NULL, TRUE); 
			foreach ($post_data as $key => $value) {
				log_message('ERROR', 'No entro el key => '.$key);
			}
			
		}
	}
	
/*-------------------------------------------------------------------------------	
 --------------------------------------------------------------------------------
 			Exportación de tablas: Excel
 --------------------------------------------------------------------------------
 --------------------------------------------------------------------------------*/	

	function armarExcel($tabla){
		header("Content-type: application/vnd.ms-excel; name='excel'; charset=UTF-8");
		header("Content-Disposition: filename=ficheroExcel.xls");
		header("Pragma: no-cache");
		header("Expires: 0");

		echo "\xEF\xBB\xBF";
        $tabla = str_replace("Opciones", "", $tabla);
		echo $tabla;
	}
	
/*-------------------------------------------------------------------------------	
 --------------------------------------------------------------------------------
 			Exportación de tablas: Impresión
 --------------------------------------------------------------------------------
 --------------------------------------------------------------------------------*/	
	
	function armarPrint($tabla){
	    $tabla = str_replace("Opciones", "", $tabla);
		echo $tabla;
		echo '<script>
				window.print();
				setTimeout(window.close, 0);
			</script>';
	}

/*-------------------------------------------------------------------------------	
 --------------------------------------------------------------------------------
 			Exportación de tablas: PDF
 --------------------------------------------------------------------------------
 --------------------------------------------------------------------------------*/	
	
	function armarPdf($tabla, $cabeceras){
		$dom = new DOMDocument();
		$doc = $dom->loadXml($tabla);
			
		$contador = 0;
			
		if(!empty($doc)){
			$dom->preserveWhiteSpace = false;                        // borramos los espacios en blanco 
			$tables  = $dom->getElementsByTagName('table');           // obtenemos el tag table
			$rows    = $tables->item(0)->getElementsByTagName('tr');    // array con todos los tr
			$i       = 0;                                                  // recorremos el array
				
			foreach ($rows as $row){ 
				$cols = $row->getElementsByTagName('td');
					
				foreach ($cabeceras as $key => $value) {
					if(isset($cols->item($key)->nodeValue) ){
						$registros[$i][$value] = $cols->item($key)->nodeValue;
					}else{
						$registros[$i][$value] = '-';
					}
				}
					
				$k = 0;
					
				foreach ($registros[$i] as $key => $value) {
					if($value == '-' || $value == ''){
						$k = $k + 1;
					}
					
					if($k == count($registros[$i])){
						unset($registros[$i]);
					}
				}
				$i = $i + 1;
			}
			
			// set HTTP response headers
		   	$data['title']		= 'Registros'; 
			$data['author']		= 'Admin';
			$data['content']	= $registros; 
				
			$this->load->view('plantilla/pdf', $data);
		 	
		}
	}

/*-------------------------------------------------------------------------------	
 --------------------------------------------------------------------------------
 			Enviar email superadmin
 --------------------------------------------------------------------------------
 --------------------------------------------------------------------------------*/	
 
    function emailAdmin($mensaje){
		$emailConfig = array(
			'protocol'       => 'smtp',
			'smtp_host'      => '192.168.100.26',
			//'smtp_port' => '',
			'smtp_user'      => '',
			'smtp_pass'      => '',
			'charset'        => 'utf-8',
			'mailtype'       => 'html',
			'newline'        => '\\r\ ',
			'crlf'           => '\\r\ '
		);
        
		$this->load->library('email',$emailConfig);
		$this->email->set_newline('\\r\ ');

		$this->email->from($this->emailFrom, 'SRP');
		$this->email->to($this->emailTo); 
		
		$copy = '';
		foreach ($this->emailCopy as $email) {
			$this->email->cc($email); 
			$copy = $email.', ';
		}
		
		$this->email->subject($this->emailSubjet);
		$this->email->message($mensaje);	
		
		$this->email->send();
		
		$registro = array(
			'mail_to'		=> $this->emailTo,
			'mail_copy'		=> $copy,
			'mail_subject'	=> $this->emailSubjet,
			'message'		=> $mensaje,
			'mail_from'		=> $this->emailFrom,
			'debugger'		=> $this->email->print_debugger(),
		);
		
		$this->m_emails->insert($registro);
	}
}