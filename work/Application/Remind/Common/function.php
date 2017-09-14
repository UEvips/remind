<?php 

/**
 * 重组节点信息为多维数组
 * @param array $node [要处理的节点数组]
 * @param number $pid [父级ID]
 * @return array  [处理后的数组]
 */
function node_merge($node,$access=null,$pid=0){
	$arr=array();
	foreach ($node as $v){
		if (is_array($access)) {
			$v['access']=in_array($v['id'],$access)?'checked':'';
		}
		if ($v['pid']==$pid) {
			$v['child']=node_merge($node,$access,$v['id']);
			$arr[]=$v;
		}
	}
	return $arr;
}
function add_log($data){
	$db=M('log');
	$db->add($data);
}
function  get_log_data($tbs,$befor=array(),$after=array()){	
    $u= session ( 'm' );
	$data=array(
	  'time'=>date('Y-m-d H:i:s'),
	  'ip'=>get_client_ip(),
	  'user'=>$u['username']
	);
	$desc=get_log_desc($tbs);
	$tb=$desc[0];
	$desc=$desc[1];
	$keys=array_keys($desc);
	$befor ['projecttype'] = json_decode($befor['projecttype'],true);
	foreach($befor ['projecttype'] as $v){
		$v['check']&&$befors['projecttype'][]=$v['name'];
	}
	$befor['projecttype']=implode(',',$befors['projecttype']);
	$after ['projecttype'] = json_decode($after['projecttype'],true);
	foreach($after ['projecttype'] as $v){
		$v['check']&&$afters['projecttype'][]=$v['name'];
	}
	$after['projecttype']=implode(',',$afters['projecttype']);
	$tmp='';
	$tem['project']=array('rmk','cont');
	$tem['customer']=array('pwd','rmk');
	foreach($after as $k=>$v){
		if($befor[$k]!=$v&&in_array($k,$keys)){
			if(trim($befor[$k])){
			  if(in_array($k,$tem[$tbs])){
			     $tmp[]='修改了['.$desc[$k].']';
			  }else{
				 $tmp[]='['.$desc[$k].']从['.$befor[$k].']改为['.$v.']';
			  }
			}else{
			  $tmp[]='['.$desc[$k].']从[空]改为['.$v.']';
			}
			
		}
	}
	$data['desc']=implode(',',$tmp);
	$data['rmk']='位置：'.$tb;
	return  $data;
}
function get_log_desc($table){
	$tb=array(
	  'user'=>'用户表',
	  'project'=>'任务表',
	  'task'=>'任务分配表',
	  'upload'=>'附件上传表',
	  'customer'=>'项目表'
	);
	$user=array();
	$project=array(
	    'title'=>'任务名称',
	    'cont'=>'任务描述',
	    'stime'=>'任务开始时间',
	    'etime'=>'任务结束时间',
	    'days'=>'任务工期',
	    'level'=>'任务等级',
	    'status'=>'任务状态',
	    'type'=>'任务类型',
	    'rmk'=>'任务备注',
	    'projecttype'=>'任务类型',
	    'touser'=>'任务负责人',
	);
	$customer=array(
	    'cname'=>'项目登录名',
	    'pwd'=>'项目登录密码',
	    'title'=>'项目名称',
	    'rmk'=>'项目描述'
	);
	$data=$$table;
	return array($tb[$table],$data);
}


    //AJAX返回数据
	function show_json($status = 1, $return = NULL) 
	{
            
                $ret['status']=$status;
		if (!(is_array($return))) 
		{
			if ($return) 
			{
				$ret['result']['message'] = $return;
			}
			exit(json_encode($ret));
		}
		else 
		{
			$ret['result'] = $return;
		}
		if (isset($return['url'])) 
		{
			$ret['result']['url'] = $return['url'];
		}
		exit(json_encode($ret));
	}
    function referer($default = '') {
	$referer = $_SERVER['HTTP_REFERER'];
	$referer['referer'] = substr($referer, -1) == '?' ? substr($referer['referer'], 0, -1) : $referer['referer'];

	if (strpos($referer, 'member.php?act=login')) {
		$referer['referer'] = $default;
	}
	$referer = $referer;
	$referer = str_replace('&amp;', '&', $referer);
	$reurl = parse_url($referer);

	if (!empty($reurl['host']) && !in_array($reurl['host'], array($_SERVER['HTTP_HOST'], 'www.' . $_SERVER['HTTP_HOST'])) && !in_array($_SERVER['HTTP_HOST'], array($reurl['host'], 'www.' . $reurl['host']))) {
		$referer = $siteroot;
	} elseif (empty($reurl['host'])) {
		$referer ='/' . $referer;
	}
	return strip_tags($referer);
}
    
    
     function export($list, $params = array()) 
	{
		if (PHP_SAPI == 'cli') 
		{
			exit('This example should only be run from a Web Browser');
		}
                Vendor("PHPExcel.Classes.PHPExcel");
//		require_once IA_ROOT . '/framework/library/phpexcel/PHPExcel.php';
		$excel = new PHPExcel();
		$excel->getProperties()->setCreator('新桥工单')->setLastModifiedBy('新桥工单')->setTitle('Office 2007 XLSX Test Document')->setSubject('Office 2007 XLSX Test Document')->setDescription('Test document for Office 2007 XLSX, generated using PHP classes.')->setKeywords('office 2007 openxml php')->setCategory('report file');
		$sheet = $excel->setActiveSheetIndex(0);
		$rownum = 1;
		foreach ($params['columns'] as $key => $column ) 
		{
			$sheet->setCellValue($this->column($key, $rownum), $column['title']);
			if (!(empty($column['width']))) 
			{
				$sheet->getColumnDimension($this->column_str($key))->setWidth($column['width']);
			}
		}
		++$rownum;
		$len = count($params['columns']);
		foreach ($list as $row ) 
		{
			$i = 0;
			while ($i < $len) 
			{
				$value = ((isset($row[$params['columns'][$i]['field']]) ? $row[$params['columns'][$i]['field']] : ''));
				$sheet->setCellValue($this->column($i, $rownum), $value);
				++$i;
			}
			++$rownum;
		}
		$excel->getActiveSheet()->setTitle($params['title']);
		$filename = urlencode($params['title'] . '-' . date('Y-m-d H:i', time()));
		ob_end_clean();
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment;filename="' . $filename . '.xls"');
		header('Cache-Control: max-age=0');
		$writer = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
		$writer->save('php://output');
		exit();
	}
