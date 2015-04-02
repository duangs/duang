<?php
class ExportController extends Yaf_Controller_Abstract {
	private $_layout;

	public function init() {
		$this->_layout = Yaf_Registry::get('layout');
	}

	public function indexAction() {
		//接收表单数据
		$iteminfo = array();
		//q=&com=&acom_id=&active_start=&active_end=&regist_start=&regist_end=&sales=&status=&int=0&active=&start_time=2015-03-01&end_time=2015-03-31&from_type=
		foreach (array('q', 'email', 'mobile', 'com', 'active_start', 'active_end', 'regist_start', 'regist_end', 'sales', 'status', 'int', 'acom_id', 'start_time', 'end_time', 'active', 'from_type') as $item) {
			$iteminfo[$item] = '';
		}
		$iteminfo['start_time'] = '2014-03-01';
		$iteminfo['end_time'] = '2015-03-31';
		//获取统计数据
		$result_list = (new ExportModel)->getAllData($iteminfo, 0, 99999);
		Helper::export_excel($iteminfo, $result_list);
		die();
	}

}