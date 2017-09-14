<?php

namespace Remind\Controller;

use Think\Controller;

class ExcelController extends CommonController {
    

        public function phpexcel1() {
            
//            $_FILES ['up'] ['name'] = 111;
        if (!empty($_FILES ['up'] ['name'])) {

            $path = "excel/" . date('d');
            $info = $this->upload($path);
            import('ORG.Util.ExcelToArrary'); //导入excelToArray类
            
            $ExcelToArrary = new \ExcelToArrary(); //实例化      
//            $res = $ExcelToArrary->read('./Public/Uploads/excel/2017/09/08/59b2608731975.xlsx', "UTF-8", 'xlsx'); //传参,判断office2007还是office2003

            $res = $ExcelToArrary->read('./Public/' . $info['up']['savepath'] . $info['up']['savename'], "UTF-8", $info['up']['ext']); //传参,判断office2007还是office2003
            if(empty($res)){
                exit("导入失败！！！");
            } 
            $title = array(
                '0'=>'entryname',
                '1'=>'subproject',
                '2'=>'title',
                '3'=>'participant',
                '4'=>'endtime',
                '5'=>'jindu',
                '6'=>'content',
                '7'=>'charge',
                );
            foreach($res as $k=>$v){
              foreach($v as $k1=>$v1){
                    if($v1==""){
                        $res[$k][$k1] = $res[$k-1][$k1];
                     }
                }
            }
            foreach($res as $k=>$v){
              foreach($v as $k1=>$v1){
                        $data[$k][$title[$k1]] = $res[$k][$k1];
                        $data[$k]['uid'] ="0";
                    }
            }
            $result = M('work')->addAll($data);
            if (!$result){
                $this->error('导入数据库失败');
                exit();
            } else {
                $log = get_log_data('user');
                $log['cont'] = $log['desc'] = '导入了工单'.$_FILES ['up'] ['name'];
                $filename = './Public/' . $info['up']['savepath'] . $info['up']['savename']; //上传文件绝对路径,unlink()删除文件函数
                if (unlink($filename)) {
                    $this->success('导入成功');
                } else {
                    $this->error('缓存删除失败');
                }
            }
        } else {
            $this->error('(⊙o⊙)~没传数据就导入?!你在逗我?!');
        }
    }

}
