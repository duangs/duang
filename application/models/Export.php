<?php
class ExportModel {
	private $_db;
	private $_mongo;
	public function __construct() {
		$this->_db = Yaf_Registry::get('db');
		$this->_mongo = Yaf_Registry::get('mongo');
	}

	public function __destruct() {
		$this->_db->close();
	}

	public function _deal_acct_list($acct_list) {
		$acct_str = '';
		$acct_array = array();
		foreach ($acct_list as $acct) {
			$acct_array[] = (int) $acct['acct_id'];
		}
		return $acct_array;
	}

	public function getAllData($query, $offset = 0, $limit = 20) {
		$acct_list = array();
		//获取账户list
		$acct_list = $this->_get_acct_list($query['int'], $query['acom_id'], $query['q'], $query['email'], $query['mobile'], $query['sales'], $query['com'], $query['active_start'], $query['active_end'], $query['regist_start'], $query['regist_end'], $query['status'], $query['active'], $query['from_type'], $limit, $offset);
		if (empty($acct_list)) {
			return array();
		}

		$start = new MongoDate(strtotime($query['start_time'] . ' 00:00:00'));
		$end = new MongoDate(strtotime($query['end_time'] . ' 23:59:59'));

		$collection = $this->_mongo->selectCollection('ops_visit_log');
		$cursor = $collection->find(array(
			'u' => array('$in' => $this->_deal_acct_list($acct_list)),
			'ts' => array('$gte' => $start),
			'ts' => array('$lte' => $end),
		)
		)->skip($offset)->limit($limit);
		$log_list = array();
		while ($cursor->hasNext()) {
			$log_list[] = $cursor->getNext();
		}
		//统计数据
		$result = $this->_deal_data($log_list, $acct_list);

		return $result;

	}

	public function _deal_data($result, $acct_list) {
		$temp = array('dologin', 'fin', 'edb', 'pcr', 'pevc', 'ced', 'news', 'rrp', 'comps', 'sam');
		$temp = array(0 => 'login', 1 => 'www', 2 => 'company', 3 => 'edb', 4 => 'ced', 5 => 'pevc', 6 => 'news', 7 => 'announce', 8 => 'comps', 9 => 'sam', 10 => 'paam', 11 => 'research', 12 => 'cornerstone', 13 => 'screener');
		var_dump($temp);
		exit;
		foreach ($temp as $t) {
			$temp_data[$t] = 0;
		}

		$count_data = array();
		foreach ($acct_list as $acct) {
			//修正一下最后登录时间
			if ($acct['last_login']) {
				$acct['last_login'] = date('Y-m-d H:i:s', (strtotime($acct['last_login']) + 8 * 3600));
			} else {
				$acct['last_login'] = '';
			}
			$count_data[$acct['acct_id']] = array_merge($acct, $temp_data);
		}
		var_dump($count_data);
		//log查询结果为空时，直接返回
		if (empty($result)) {
			return $count_data;
		}

		//组织数据
		foreach ($result as $res) {
			$acct_id = $res['u'];
			$component = $res['c'];
			if (!isset($count_data[$acct_id]) || $component > 13) {
				continue;
			}
			$count_data[$acct_id][$temp[$component]]++;
			//登录次数
			if ($res['a'] == 4) {
				// print_r($res['p']);
				$count_data[$acct_id]['login']++;
			}
		}
		return $count_data;
	}

	/**
	 * 获取账户列表
	 * @param type $acom_id
	 * @return type
	 */
	public function _get_acct_list($int, $acom_id, $q, $email, $mobile, $sales, $com, $active_start, $active_end, $regist_start, $regist_end, $status, $active, $from_type, $limit = 0, $offset = 30) {
		$select = "SELECT ca.acct_id, ca.acct_name, ca.acct_email, ca.mobile, ca.acom_id, ca.sales_rep, ca.effective_dt, ca.expire_dt, ca.last_login, date(ca.crt_stmp) crt_stmp, cc.acom_nm ";
		$from = " FROM cust_account ca  LEFT JOIN cust_company cc ON ca.acom_id = cc.acom_id ";
		$where = "WHERE ca.acct_id != 0 ";
		//公司名称关键字
		if ($com) {
			$where .= " AND cc.acom_nm like " . mysqli_escape_string('%' . $com . '%');
		}

		//激活时间
		if ($active_start) {
			$where .= " AND ca.active_dt >= '" . $active_start . "'";
		}

		if ($active_end) {
			$where .= " AND ca.active_dt <= '" . $active_end . "'";
		}

		//注册时间
		if ($regist_start) {
			$where .= " AND ca.crt_stmp >= '" . $regist_start . "'";
		}

		if ($regist_end) {
			$where .= " AND ca.crt_stmp <= '" . $regist_end . " 23:59:59'";
		}

		//账户状态
		if ($status) {
			$where .= " AND ca.acct_status = " . $status;
		}

		//是否Chinascope内部账号
		if ($int) {
			$where .= " AND ca.csf_internal = 0 ";
		}

		//公司ID
		if ($acom_id) {
			$where .= " AND ca.acom_id in (" . $acom_id . ") ";
		}

		//用户名关键字
		if ($q) {
			$where .= " AND ca.acct_name like " . mysqli_escape_string('%' . $q . '%');
		}

		//邮箱
		if ($email) {
			$where .= " AND ca.acct_email like " . mysqli_escape_string('%' . $email . '%');
		}

		//手机号码
		if ($mobile) {
			$where .= " AND ca.mobile like " . mysqli_escape_string('%' . $mobile . '%');
		}

		//销售代表
		if ($sales) {
			$where .= " AND ca.sales_rep like '%" . $sales . "%' ";
		}

		if ($from_type) {
			$where .= " AND ca.from_type = " . $from_type;
		}

		if ($active !== '') {
			$where .= " AND ca.is_active = '$active' ";
		}

		$offset = $offset * $limit;
		$condition = " LIMIT " . $offset . "," . $limit;
		$sql = $select . $from . $where . $condition;
		$result = $this->_db->query($sql);
		if ($result->num_rows == 0) {
			return array();
		}

		// $list = $result->fetch_assoc();
		$acct_list = array();
		while ($row = $result->fetch_assoc()) {
			$row['effective_dt'] = $row['effective_dt'] == '0000-00-00' ? '' : $row['effective_dt'];
			$row['expire_dt'] = $row['expire_dt'] == '0000-00-00' ? '' : $row['expire_dt'];
			$acct_list[] = $row;
		}
		// foreach($acct_list as $k=>$v){
		// $acct_list[$k]['effective_dt'] = $acct_list[$k]['effective_dt'] == '0000-00-00' ? '' : $acct_list[$k]['effective_dt'];
		// $acct_list[$k]['expire_dt'] = $acct_list[$k]['expire_dt'] == '0000-00-00' ? '' : $acct_list[$k]['expire_dt'];
		// }
		$result->free();
		return $acct_list;
	}

}