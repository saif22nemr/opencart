<?php
class ControllerCostCost extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('cost/cost');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('cost/cost');

		$this->getList();
	}

	public function add() {
		$this->load->language('cost/cost');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('cost/cost');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$check = $this->model_cost_cost->addCost($this->request->post);
			if($check != 1){
				$this->error['database'] = $this->language->get('error_fail_add');
				$this->getForm();
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('cost/cost', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('cost/cost');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('cost/cost');
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && isset($this->request->post['type']) && $this->request->post['type']  == 'json') {
			//print_r($this->request->post);

			if (!$this->user->hasPermission('modify', 'cost/cost')) {
				$this->response->addHeader('Content-Type: application/json');
				$data = ['status'=>'error','error'=>$this->language->get('error_permission')];
				$this->response->setOutput(json_encode($data));
				return json_encode($data);
			}
			if(!isset($this->request->post['type']) or $this->request->post['type'] != 'json')
				return reponse()->json(['status'=>'error','error'=> 'The type must be json']);
			if(isset($this->request->post['description']) and utf8_strlen($this->request->post['description']) < 1){
				$this->response->addHeader('Content-Type: application/json');
				$data = ['status'=>'error','error'=>$this->language->get('error_description')];
				$this->response->setOutput(json_encode($data));
				return json_encode($data);
			}

			if(isset($this->request->post['value']) and (!is_numeric($this->request->post['value']) or ($this->request->post['value'] < 1  or $this->request->post['value'] > 10000000))){
				$this->response->addHeader('Content-Type: application/json');
				$data = ['status'=>'error','error'=>$this->language->get('error_value')];
				$this->response->setOutput(json_encode($data));
				return json_encode($data);
			}
			//get some info.
			$posts = $this->request->post;
			//print_r($posts);
			$v = $this->model_cost_cost->getCost($this->request->get['cost_id']);
			$posts['admin'] = $v['admin_id'];
			if(!isset($this->request->post['cost_id']))
				$posts['cost_id'] = $v['cost_id'];
			if(!isset($this->request->post['description']))
				$posts['description'] = $v['description'];

			$val = $this->model_cost_cost->editCost($this->request->get['cost_id'],$posts);
			$cost= $this->model_cost_cost->getCost($posts['cost_id']);
			$data = [
						'status' => 'success',
						'success'=>'successfull edit',
						'edit' => $this->url->link('cost/cost/edit', 'user_token=' . $this->session->data['user_token'] . '&cost_id=' . $cost['cost_id'], true),
					];
			if($val == 1) {
				$this->response->addHeader('Content-Type: application/json');
				$this->response->setOutput(json_encode($data));
				return json_encode($data);
				
			exit();
			}

			else return json_encode(['status'=>'error','error'=>'Error in edit']);

		}
		else if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$posts = $this->request->post;
			$posts['cost_id'] = $this->request->get['cost_id'];;
			$this->model_cost_cost->editCost($this->request->get['cost_id'], $this->request->post);
			
			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}
			if(isset($this->request->post['type']) and $this->request->post['type'] == 'json'){
				$cost = $this->model_cost_cost->getCost($this->request->get['cost_id']);
				return json_encode(['data'=>$cost],200);
			}
			$this->response->redirect($this->url->link('cost/cost', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('cost/cost');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('cost/cost');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $cost_id) {
				$this->model_cost_cost->deleteCost($cost_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('cost/cost', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

	protected function getList() {
		$url = '';
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
			$url .= '&sort=' . $this->request->get['sort'];
		} else {
			$sort = 'cost_id';
		}
		

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
			$url .= '&order=' . $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('cost/cost', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['add'] = $this->url->link('cost/cost/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('cost/cost/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);
		

		$data['costs'] = array();

		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$cost_total = $this->model_cost_cost->getTotalCosts();

		$results = $this->model_cost_cost->getCosts($filter_data);

		foreach ($results as $result) {
			$data['costs'][] = array(
				'cost_id'     => $result['cost_id'],
				'name'        => $result['name'],
				'value'       => $result['cost_value'],
				'description' => $result['description'],
				'edit'        => $this->url->link('cost/cost/edit', 'user_token=' . $this->session->data['user_token'] . '&cost_id=' . $result['cost_id'] . $url, true),
				'delete'      => $this->url->link('cost/cost/delete', 'user_token=' . $this->session->data['user_token'] . '&cost_id=' . $result['cost_id'] . $url, true)
			);
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}
		if (isset($this->request->post['selected'])) {
			$data['selected'] = $this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}


		$url = '';

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_cost_id'] = $this->url->link('cost/cost', 'user_token=' . $this->session->data['user_token'] . '&sort=cost_id' . $url, true);
		$data['sort_value'] = $this->url->link('cost/cost', 'user_token=' . $this->session->data['user_token'] . '&sort=cost_value' . $url, true);

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $cost_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('cost/cost', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($cost_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($cost_total - $this->config->get('config_limit_admin'))) ? $cost_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $cost_total, ceil($cost_total / $this->config->get('config_limit_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('cost/cost_list', $data));
	}

	protected function getForm() {
		$data['text_form'] = !isset($this->request->get['cost_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = array();
		}

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('cost/cost', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		if (!isset($this->request->get['cost_id'])) {
			$data['action'] = $this->url->link('cost/cost/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('cost/cost/edit', 'user_token=' . $this->session->data['user_token'] . '&cost_id=' . $this->request->get['cost_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('cost/cost', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->get['cost_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$cost = $this->model_cost_cost->getCost($this->request->get['cost_id']);
			if(isset($cost['cost_id'])){
				$data['selected'] = $cost['admin_id'];
				$data['cost'] = $cost;
			}
			
		}

		$data['user_token'] = $this->session->data['user_token'];


		if (isset($this->request->post['description'])) {
			$data['description'] = $this->request->post['description'];
		} elseif (isset($this->request->get['cost_id'])) {
			$data['description'] = $this->model_cost_cost->getCostDescriptions($this->request->get['cost_id']);
		}

		if (isset($this->request->post['value'])) {
			$data['value'] = $this->request->post['value'];
		} elseif (isset($this->request->get['cost_id'])) {
			$data['value'] = $this->model_cost_cost->getCostValue($this->request->get['cost_id']);
		}
		if(isset($this->request->post['admin']))
			$data['selected'] = $this->request->post['admin'];


		if (isset($this->request->post['admin'])) {
			$data['selected'] = $this->request->post['admin'];
		}
		//get all admin
		$this->load->model('user/user');

		$admins = $this->model_user_user->getUserByGroupId(1);
		foreach ($admins as $index => $admin) {
			$data['admins'][] = [
				'user_id' => $admin['user_id'],
				'name'    => $admin['name'],
			];
		}
		
		$data['entry_admin'] = $this->language->get('entry_admin');
		//$data['layouts'] = $this->model_design_layout->getLayouts();

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('cost/cost_form', $data));
	}
	// protected function validateAjax(){
	// 	if (!$this->user->hasPermission('modify', 'cost/cost')) {
	// 		return json_encode(['error'=>$this->language->get('error_permission')]);
	// 	}
	// 	if(!isset($this->request->post['type']) or $this->request->post['type'] != 'json')
	// 		return reponse()->json(['error'=> 'The type must be json']);
	// 	if(isset($this->request->post['description']) and utf8strlen($this->request->post['description']) < 1)
	// 		return json_encode(['error',$this->language->get('error_description')]);
	// 	if(isset($this->request->post['value']) and !is_numeric($this->request->post['value']) or ($this->request->post['value'] < 1  and $this->request->post['value'] > 10000000))
	// 		return json_encode(['error',$this->language->get('error_value')]);
	// 	return false;
	// }
	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'cost/cost')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		//description
		if (!isset($this->request->post['description']) or utf8_strlen($this->request->post['description']) < 1 or utf8_strlen($this->request->post['description']) > 1000) {
			$this->error['description'] = $this->language->get('error_description');
		}
		//cost_value
		if (!isset($this->request->post['value']) or !is_numeric($this->request->post['value']) or $this->request->post['value'] < 1 or $this->request->post['value'] > 100000000) {
			$this->error['value'] = $this->language->get('error_value');
		}

		if(!isset($this->request->post['type'])){
			if (!isset($this->request->post['admin']) or !is_numeric($this->request->post['admin'])) {
				$this->error['admin'] = $this->language->get('error_value');
			}else{
				$this->load->model('user/user');
				$admin = $this->model_user_user->getUserAdmin($this->request->post['admin']);
				if(!isset($admin['user_id']))
					$this->error['admin'] = $this->language->get('error_admin');
			}
		}

		if (isset($this->request->get['cost_id'])){
			$result = $this->model_cost_cost->getCost($this->request->get['cost_id']);
			if(!isset($result['cost_id']))
				$this->error['cost'] = $this->language->get('error_cost');
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}
		
		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'cost/cost')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	
}
