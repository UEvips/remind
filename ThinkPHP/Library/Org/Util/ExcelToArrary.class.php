<?php

header("content-type:text/html;charset=utf-8");

class ExcelToArrary {

    public function __construct() {

        Vendor("PHPExcel.Classes.PHPExcel"); //引入phpexcel类(留意路径,不了解路径可以查看下手册)  
        Vendor("PHPExcel.Classes.PHPExcel.IOFactory"); //引入phpexcel类(留意路径)      
    }

    public function read($filename, $encode, $file_type) {
        if (strtolower($file_type) == 'xls') {//判断excel表类型为2003还是2007  
            Vendor("PHPExcel.Classes.PHPExcel.Reader.Excel5"); //引入phpexcel类(留意路径)  
            $objReader = PHPExcel_IOFactory::createReader('Excel5');
        } elseif (strtolower($file_type) == 'xlsx') {
            Vendor("PHPExcel.Classes.PHPExcel.Reader.Excel2007"); //引入phpexcel类(留意路径)   
            $objReader = PHPExcel_IOFactory::createReader('Excel2007');
        }
        $objReader->setReadDataOnly(true);
        $objPHPExcel = $objReader->load($filename);
        $num = $objPHPExcel->getSheetCount();  //读取工作表个数
        for($i=0;$i<$num;$i++){
            $excelData[] = $objPHPExcel->getSheet($i)->toArray(); // 读取工作表数据
        }
        
        foreach($excelData as $k=>$v){
            foreach($v as $k1=>$v1){
                if($k1>1){
                    $data[] = $v1;
                } 
            }
        }
        return $data;
    }

}
