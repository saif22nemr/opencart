<?php
class ControllerCustomDescription extends Controller {
	private $error = array();

	public function index() {
		//echo 'this from admin custom controller';
		$this->load->language('custom/description');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('custom/description');
		//$this->load->view('custom/description');
		//$val = $this->modle_custom_description->getDescription();
		//print_r($val);
		//exit();
		$this->getList();
	}

		public function add() {
			$this->load->language('custom/description');

			$this->document->setTitle($this->language->get('heading_title'));

			$this->load->model('custom/description');

			if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
				$this->model_custom_description->addDescription($this->request->post);

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

				$this->response->redirect($this->url->link('custom/description', 'user_token=' . $this->session->data['user_token'] . $url, true));
			}

			$this->getForm();
		}

		public function edit() {
			$this->load->language('custom/description');

			$this->document->setTitle($this->language->get('heading_title'));

			$this->load->model('custom/description');

			if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
				$this->model_custom_description->editDescription($this->request->get['id'], $this->request->post);

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

				$this->response->redirect($this->url->link('custom/description', 'user_token=' . $this->session->data['user_token'] . $url, true));
			}

			$this->getForm();
		}

		public function delete() {
			$this->load->language('custom/description');

			$this->document->setTitle($this->language->get('heading_title'));

			$this->load->model('custom/description');
			if (isset($this->request->post['selected']) && is_array($this->request->post['selected']) && $this->validateDelete()) {
				foreach ($this->request->post['selected'] as $id) {
					if(!is_numeric($id))
						$this->error['error'][] = 'Invalid Id '.$id;
					$this->model_custom_description->deleteDescription($id);
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

				$this->response->redirect($this->url->link('custom/description', 'user_token=' . $this->session->data['user_token'] . $url, true));
			}

			$this->getList();
		}

		protected function getList() {
			if (isset($this->request->get['filter_description'])) {
				$filter_description = $this->request->get['filter_description'];
			} else {
				$filter_description = '';
			}
			if (isset($this->request->get['filter_name'])) {
				$filter_name = $this->request->get['filter_name'];
			} else {
				$filter_name = '';
			}
			if (isset($this->request->get['sort'])) {
				$sort = $this->request->get['sort'];
			} else {
				$sort = 'name';
			}

			if (isset($this->request->get['order'])) {
				$order = $this->request->get['order'];
			} else {
				$order = 'ASC';
			}

			if (isset($this->request->get['page'])) {
				$page = $this->request->get['page'];
			} else {
				$page = 1;
			}

			$url = '';

			if (isset($this->request->get['filter_description'])) {
				$url .= '&filter_description=' . urlencode(html_entity_decode($this->request->get['filter_description'], ENT_QUOTES, 'UTF-8'));
			}
			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

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
				'href' => $this->url->link('custom/description', 'user_token=' . $this->session->data['user_token'] . $url, true)
			);

			$data['add'] = $this->url->link('custom/description/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
			$data['delete'] = $this->url->link('custom/description/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

			$data['descriptions'] = array();

			$filter_data = array(
				'filter_description'       => $filter_description,
				'filter_name'       => $filter_description,
				'sort'                     => $sort,
				'order'                    => $order,
				'start'                    => ($page - 1) * $this->config->get('config_limit_admin'),
				'limit'                    => $this->config->get('config_limit_admin')
			);

			$description_total = $this->model_custom_description->getTotalDescription($filter_data);
			//print_r($this->model_custom_description);


			$results = $this->model_custom_description->getDescription($filter_data);

			foreach ($results as $result) {

				$data['descriptions'][] = array(
					'id'					 => $result['id'],
					'user_id'    	 => $result['user_id'],
					'name'         => $result['name'],
					'description'  => $result['description'],
					'edit'				 => $this->url->link('custom/description/edit', 'user_token=' . $this->session->data['user_token'] . '&id='.$result['id'], true),
				);
			}

			$data['user_token'] = $this->session->data['user_token'];

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
				$data['selected'] = (array)$this->request->post['selected'];
			} else {
				$data['selected'] = array();
			}

			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_description'])) {
				$url .= '&filter_description=' . urlencode(html_entity_decode($this->request->get['filter_description'], ENT_QUOTES, 'UTF-8'));
			}

			if ($order == 'ASC') {
				$url .= '&order=DESC';
			} else {
				$url .= '&order=ASC';
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$data['sort_name'] = $this->url->link('custom/description', 'user_token=' . $this->session->data['user_token'] . '&sort=name' . $url, true);

			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			$pagination = new Pagination();
			$pagination->total = $description_total;
			$pagination->page = $page;
			$pagination->limit = $this->config->get('config_limit_admin');
			$pagination->url = $this->url->link('custom/description', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

			$data['pagination'] = $pagination->render();

			$data['results'] = sprintf($this->language->get('text_pagination'), ($description_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($description_total - $this->config->get('config_limit_admin'))) ? $description_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $description_total, ceil($description_total / $this->config->get('config_limit_admin')));

			$data['filter_name'] = $filter_name;




			$data['sort'] = $sort;
			$data['order'] = $order;

			$data['header'] = $this->load->controller('common/header');
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['footer'] = $this->load->controller('common/footer');

			$this->response->setOutput($this->load->view('custom/description', $data));
		}

		protected function getForm() {
			//type form if for add or eidt
			$data['text_form'] = !isset($this->request->get['id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

			$data['user_token'] = $this->session->data['user_token'];

			if (isset($this->request->get['id'])) {
				$data['id'] = $this->request->get['id'];
			} else {
				$data['id'] = 0;
			}
			//for error
			if (isset($this->error['warning'])) {
				$data['error_warning'] = $this->error['warning'];
			} else {
				$data['error_warning'] = '';
			}

			if (isset($this->error['description'])) {
				$data['error_description'] = $this->error['description'];
			} else {
				$data['error_description'] = '';
			}
			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_description'])) {
				$url .= '&filter_description=' . urlencode(html_entity_decode($this->request->get['filter_description'], ENT_QUOTES, 'UTF-8'));
			}


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
				'href' => $this->url->link('custom/description/add', 'user_token=' . $this->session->data['user_token'] . $url, true)
			);

			if (!isset($this->request->get['id'])) {
				$data['action'] = $this->url->link('custom/description/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
			} else {
				$data['action'] = $this->url->link('custom/description/edit', 'user_token=' . $this->session->data['user_token'] . '&id=' . $this->request->get['id'] . $url, true);
			}

			$data['cancel'] = $this->url->link('custom/description', 'user_token=' . $this->session->data['user_token'] . $url, true);
			$data['repair'] = $this->url->link('custom/description', 'user_token=' . $this->session->data['user_token'] . $url, true);

			if (isset($this->request->get['id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
				$customer_info = $this->model_custom_description->getDescriptionForUser($this->request->get['id']);
			}
			
			if (isset($this->request->post['description']))
				$data['description'] = $this->request->post['description'];
			 elseif (!empty($customer_info)) {
				$data['description'] = $customer_info['description'];
			} else {
				$data['description'] = '';
			}

			if(isset($this->request->post['selected']) and is_array($this->request->post['selected']))
				$data['selected'] = $this->request->post['selected'];


			// Custom Fields
			$this->load->model('user/user');

			$data['users'] = array();

			$filter_data = array(
				'sort'  => 'username',
				'order' => 'ASC'
			);
			if(isset($this->request->get['id'])){
				$this->load->model('custom/description');
				$description = $this->model_custom_description->getDescriptionForUser($this->request->get['id']);
				$data['select'] = $this->request->get['id'];
			}
			$users = $this->model_user_user->getUsers($filter_data);

			foreach ($users as $user) {
				$data['users'][] = array(
					'name'               => $user['firstname'] . ' ' . $user['lastname'],
					'user_id'            => $user['user_id'],
				);
			}



			$data['header'] = $this->load->controller('common/header');
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['footer'] = $this->load->controller('common/footer');

			$this->response->setOutput($this->load->view('custom/description_form', $data));
		}

		protected function validateForm() {
			if (!$this->user->hasPermission('modify', 'custom/description')) {
				$this->error['warning'] = $this->language->get('error_permission');
			}
			// print_r($this->request->post);
			// exit();

			//if from edit page : there id
			if(!isset($this->request->get['id'])  || !is_numeric($this->request->get['id']) || (int)$this->request->get['id'] <= 0 || (int)$this->request->get['id'] > 100000000){
				//$this->error['description_not_exist'] = $this->language->get('error_id');
			}else {
				$opinion = $this->model_custom_description->getDescriptionForUser($this->request->get['id']);
				if(!$opinion)
					$this->error['description_not_exist'] = $this->language->get('error_id');
			}
			if ((utf8_strlen($this->request->post['description']) < 1) || (utf8_strlen(trim($this->request->post['description'])) > 1000)) {
				$this->error['description'] = $this->language->get('error_description');
			}

			if(!isset($this->request->post['user']) || !is_numeric($this->request->post['user']) || (int)$this->request->post['user'] <= 0 || (int)$this->request->post['user'] > 100000000){
				$this->error['user'] = $this->language->get('error_user');
			}else {
				$this->load->model('user/user');
				$user = $this->model_user_user->getUser($this->request->post['user']);
				if(!$user)
					$this->error['user'] = $this->language->get('error_not_exist');
			}


			if ($this->error && !isset($this->error['warning'])) {
				$this->error['warning'] = $this->language->get('error_warning');
			}

			return !$this->error;
		}

		protected function validateDelete() {
			if (!$this->user->hasPermission('modify', 'customer/customer')) {
				$this->error['warning'] = $this->language->get('error_permission');
			}

			return !$this->error;
		}

		protected function validateUnlock() {
			if (!$this->user->hasPermission('modify', 'customer/customer')) {
				$this->error['warning'] = $this->language->get('error_permission');
			}

			return !$this->error;
		}


	}

