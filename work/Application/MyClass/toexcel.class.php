<?php
namespace MyClass;
class toexcel {
	public function exportexcel($data = array(), $title = array(), $filename = 'report') {
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
			$title = implode ( "\t", $title );
			echo "$title\n";
		}
		$s=array(',');
		$r=array('，');
		if (! empty ( $data )) {
			foreach ( $data as $key => $val ) {
				foreach ( $val as $ck => $cv ) {
					$cv=str_replace($s,$r,trim($cv));
					$cv = str_replace(array("\r\n", "\r", "\n"), "。", $cv);
					$data [$key] [$ck] = iconv ( "UTF-8", "GBK", $cv );
				}
				$data [$key] = implode ( "\t", $data [$key] );
			}
			echo implode ( "\n", $data );
		}
	}
}