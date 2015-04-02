<?php
class Helper {
	public static function export_excel($iteminfo, $result_list) {
		//加载和初始化PHPExcel

		$objPHPExcel = new PHPExcel();
		$objPHPExcel->setActiveSheetIndex(0);
		$objActSheet = $objPHPExcel->getActiveSheet();
		//设置标题
		$objActSheet->setTitle('Usage Statis');
		//设置A1单元格内容和格式
		$title = "Start date:" . $iteminfo['start_time'] . " -- End date:" . $iteminfo['end_time'];
		$export_date = 'Export Date:' . date('Y-m-d H:i:s');
		$objActSheet->setCellValue('A1', $title);
		$objActSheet->setCellValue('F1', $export_date);
		$objActSheet->mergeCells('A1:E1');
		$objActSheet->mergeCells('F1:R1');
		$objStyleA1 = $objActSheet->getStyle('A1');
		$objFontA1 = $objStyleA1->getFont();
		$objFontA1->setName('Calibri');
		$objFontA1->setSize(16);
		$objFontA1->setBold(true);

		//设置标题
		$title = array('Company', 'Account', 'Email', 'Mobile', 'Sales', 'Effective Date', 'Expire Date', 'Login Count', 'Latest Login', 'Public Companies', 'Enterprise Database', 'PE & VC', 'ECON & SECTOR', 'News', 'Announcement', 'Research Report', 'Comps', 'SAM', 'Cornerstone', 'Screener');
		$num = 0;
		foreach (range('A', 'R') as $letter) {
			$objActSheet->setCellValue($letter . '2', $title[$num]);
			$num++;
		}
		//设置宽度
		$objActSheet->getColumnDimension('A')->setWidth(25);
		$objActSheet->getColumnDimension('B')->setWidth(35);
		$objActSheet->getColumnDimension('C')->setWidth(12);
		$objActSheet->getColumnDimension('D')->setWidth(15);
		$objActSheet->getColumnDimension('E')->setWidth(15);
		$objActSheet->getColumnDimension('F')->setWidth(10);
		$objActSheet->getColumnDimension('G')->setWidth(18);
		foreach (range('H', 'R') as $letter) {
			$objActSheet->getColumnDimension($letter)->setAutoSize(true);
		}
		$objStyle2 = $objActSheet->getStyle('A2:R2');
		$objFont2 = $objStyle2->getFont();
		$objFont2->setSize(10);
		$objFont2->setBold(true);
		$objFill2 = $objStyle2->getFill();
		$objFill2->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		$objFill2->getStartColor()->setRGB('FFC000');

		//设置实体内容行的数据
		$value = array('acom_nm', 'acct_name', 'acct_email', 'mobile', 'sales_rep', 'effective_dt', 'expire_dt', 'login', 'last_login', 'company', 'edb', 'pevc', 'ced', 'news', 'announce', 'research', 'comps', 'sam', 'cornerstone', 'screener');
		$listCounter = 2;
		foreach ($result_list as $usage) {
			$listCounter++;
			$m = 0;
			foreach (range('A', 'R') as $letter) {
				$v = $value[$m];
				$objActSheet->setCellValue($letter . $listCounter, $usage[$v]);
				$m++;
			}
		}
		date_default_timezone_set("Asia/Shanghai");
		$outputFileName = 'Usage_Statis_' . date('YmdHis') . '.xls';
		try {
			Helper::excel_download($outputFileName, $objPHPExcel);
		} catch (Exception $exc) {
			echo $exc->getMessage();
		}
	}

	public static function excel_download($outputFileName = '', $objPHPExcel = NULL) {
		if ($outputFileName == '' OR !$objPHPExcel instanceof PHPExcel) {
			throw new Exception('The giving objPHPExcel is not an instance of the PHPExcel class!');
		}

		// Try to determine if the filename includes a file extension.
		// We need it in order to set the MIME type
		if (FALSE === strpos($outputFileName, '.')) {
			throw new Exception('The giving output file name is invalid!');
		}

		// Grab the file extension
		$x = explode('.', $outputFileName);
		$extension = end($x);

		// Load the mime types
		$mimes = array('hqx' => 'application/mac-binhex40',
			'cpt' => 'application/mac-compactpro',
			'csv' => array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel'),
			'bin' => 'application/macbinary',
			'dms' => 'application/octet-stream',
			'lha' => 'application/octet-stream',
			'lzh' => 'application/octet-stream',
			'exe' => array('application/octet-stream', 'application/x-msdownload'),
			'class' => 'application/octet-stream',
			'psd' => 'application/x-photoshop',
			'so' => 'application/octet-stream',
			'sea' => 'application/octet-stream',
			'dll' => 'application/octet-stream',
			'oda' => 'application/oda',
			'pdf' => array('application/pdf', 'application/x-download'),
			'ai' => 'application/postscript',
			'eps' => 'application/postscript',
			'ps' => 'application/postscript',
			'smi' => 'application/smil',
			'smil' => 'application/smil',
			'mif' => 'application/vnd.mif',
			'xls' => array('application/excel', 'application/vnd.ms-excel', 'application/msexcel'),
			'ppt' => array('application/powerpoint', 'application/vnd.ms-powerpoint'),
			'wbxml' => 'application/wbxml',
			'wmlc' => 'application/wmlc',
			'dcr' => 'application/x-director',
			'dir' => 'application/x-director',
			'dxr' => 'application/x-director',
			'dvi' => 'application/x-dvi',
			'gtar' => 'application/x-gtar',
			'gz' => 'application/x-gzip',
			'php' => 'application/x-httpd-php',
			'php4' => 'application/x-httpd-php',
			'php3' => 'application/x-httpd-php',
			'phtml' => 'application/x-httpd-php',
			'phps' => 'application/x-httpd-php-source',
			'js' => 'application/x-javascript',
			'swf' => 'application/x-shockwave-flash',
			'sit' => 'application/x-stuffit',
			'tar' => 'application/x-tar',
			'tgz' => array('application/x-tar', 'application/x-gzip-compressed'),
			'xhtml' => 'application/xhtml+xml',
			'xht' => 'application/xhtml+xml',
			'zip' => array('application/x-zip', 'application/zip', 'application/x-zip-compressed'),
			'mid' => 'audio/midi',
			'midi' => 'audio/midi',
			'mpga' => 'audio/mpeg',
			'mp2' => 'audio/mpeg',
			'mp3' => array('audio/mpeg', 'audio/mpg', 'audio/mpeg3', 'audio/mp3'),
			'aif' => 'audio/x-aiff',
			'aiff' => 'audio/x-aiff',
			'aifc' => 'audio/x-aiff',
			'ram' => 'audio/x-pn-realaudio',
			'rm' => 'audio/x-pn-realaudio',
			'rpm' => 'audio/x-pn-realaudio-plugin',
			'ra' => 'audio/x-realaudio',
			'rv' => 'video/vnd.rn-realvideo',
			'wav' => array('audio/x-wav', 'audio/wave', 'audio/wav'),
			'bmp' => array('image/bmp', 'image/x-windows-bmp'),
			'gif' => 'image/gif',
			'jpeg' => array('image/jpeg', 'image/pjpeg'),
			'jpg' => array('image/jpeg', 'image/pjpeg'),
			'jpe' => array('image/jpeg', 'image/pjpeg'),
			'png' => array('image/png', 'image/x-png'),
			'tiff' => 'image/tiff',
			'tif' => 'image/tiff',
			'css' => 'text/css',
			'html' => 'text/html',
			'htm' => 'text/html',
			'shtml' => 'text/html',
			'txt' => 'text/plain',
			'text' => 'text/plain',
			'log' => array('text/plain', 'text/x-log'),
			'rtx' => 'text/richtext',
			'rtf' => 'text/rtf',
			'xml' => 'text/xml',
			'xsl' => 'text/xml',
			'mpeg' => 'video/mpeg',
			'mpg' => 'video/mpeg',
			'mpe' => 'video/mpeg',
			'qt' => 'video/quicktime',
			'mov' => 'video/quicktime',
			'avi' => 'video/x-msvideo',
			'movie' => 'video/x-sgi-movie',
			'doc' => 'application/msword',
			'docx' => array('application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/zip'),
			'xlsx' => array('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/zip'),
			'word' => array('application/msword', 'application/octet-stream'),
			'xl' => 'application/excel',
			'eml' => 'message/rfc822',
			'json' => array('application/json', 'text/json'),
		);

		// Set a default mime if we can't find it
		if (!isset($mimes[$extension])) {
			$mime = 'application/octet-stream';
		} else {
			$mime = (is_array($mimes[$extension])) ? $mimes[$extension][0] : $mimes[$extension];
		}

		if (strpos($_SERVER['HTTP_USER_AGENT'], "MSIE") !== FALSE) {
			header('Content-Type: "' . $mime . '"');
			header('Content-Disposition: attachment; filename="' . $outputFileName . '"');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header("Content-Transfer-Encoding: binary");
			header('Pragma: public');
		} else {
			header('Content-Type: "' . $mime . '"');
			header('Content-Disposition: attachment; filename="' . $outputFileName . '"');
			header("Content-Transfer-Encoding: binary");
			header('Expires: 0');
			header('Pragma: no-cache');
		}

		//generate excel file
		if (class_exists('PHPExcel_Writer_Excel2007')) {
			$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
			$objWriter->save('php://output');
		} else {
			throw new Exception('PHPExcel Class is not exists!');
		}
	}

	public static function curl_post($url, $data = array(), $timeout = 120, $cookie = '') {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, count($data));
		//$data = http_build_query($data);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		//curl_setopt($ch, CURLOPT_INFILESIZE, $data['filesize']);
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		if ($cookie) {
			curl_setopt($ch, CURLOPT_COOKIE, $cookie);
		}
		//curl_setopt($ch, CURLOPT_HEADER, true);
		//curl_setopt($ch, CURLOPT_PROXY, '192.168.0.26:8080');
		curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
	}

	public static function curl_get($durl, $timeout = 30, $cookie = '') {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $durl);
		curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
		if ($cookie) {
			curl_setopt($ch, CURLOPT_COOKIE, $cookie);
		}
		//curl_setopt ( $ch, CURLOPT_REFERER, _REFERER_ );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$r = curl_exec($ch);
		curl_close($ch);
		return $r;
	}
}