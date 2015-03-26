<?php
class IndexController extends Yaf_Controller_Abstract {

	private $_layout;

	public function init() {
		$this->_layout = Yaf_Registry::get('layout');
	}

	public function indexAction() {

		$page = $this->getRequest()->getParam('page', 1); //) ?: 0; //unused - see Bootstrap::_initRoutes
		$count = 13;
		$topic = new TopicModel();

		/*view*/
		$this->_view->entries = $topic->fetchAll(null, 'date_created', $count, ($page - 1) * $count);
		$this->_view->page = $page;

		$page_config = Yaf_Application::app()->getConfig()->pagination->toArray();
		$page_config['base_url'] = '/index/page/';
		$page_config['total_rows'] = $topic->getAdapter()->fetchOne("SELECT count(*) FROM topic");
		$page_config['per_page'] = $count;

		$pagination = new Pagination($page_config);
		$pagination->initialize($page_config);

		$this->_view->pagination = $pagination->create_links();

		/*layout*/
		$this->_layout->meta_title = 'A Blog';
	}

	public function ttAction() {
		$db = new Zend_Db_Adapter_Pdo_Sqlite(
			Yaf_Application::app()->getConfig()->database->params2->toArray()
		);
		$re = $db->query('select * from blog');
		if ($re) {
			while ($row = $re->fetch()) {
				$topic = new TopicModel();
				$topic->title = $row['content'];
				$topic->content = $row['title'];
				$topic->date_created = $row['date_created'];
				$topic->insert(array('title' => $row['content'], 'content' => $row['title'], 'date_created' => $row['date_created']));
			}
		}
		return false;
	}

}
