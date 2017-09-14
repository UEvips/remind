<?php
namespace MyClass;
class toexcels {
	public function exportexcel($p,$Callback=false) {
		$title=$p['title'];
		$data=$p['data'];
		$filename=$p['filename'];
		$isCallback=(getType($Callback))=='object'?true:false;
		header ( "Content-type:application/octet-stream" );
		header ( "Accept-Ranges:bytes" );
		header ( "Content-type:application/vnd.ms-excel" );
		header ( "Content-Disposition:attachment;filename=" . $filename . ".xls" );
		header ( "Pragma: no-cache" );
		header ( "Expires: 0" );
		// 导出xls 开始
		if (! empty ( $title )) {
			foreach ( $title as $k => $v ) {
				$title [$k] = iconv ( "UTF-8", "GB2312", $v );
			}
			$titles = implode ( "\t", $title );
			echo "$titles\n";
		}
		$datas=array();
		if (! empty ( $data )) {
			foreach ( $data as $key => $val) {
				if($isCallback){
					$val=$Callback($val);
				}				
				foreach ( $title as $ck => $cv ) {
					$datas [$key][$ck] = iconv ( "UTF-8", "GB2312", $val[$ck]);
				}				
				$datas [$key] = implode ( "\t", $datas [$key] );
			}
			echo implode ( "\n", $datas );
		}
		die;
	}
}