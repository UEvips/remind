$(function(){
    /*
-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    */
    //初始化 ajax
    var tpl = '';
    tpl += '<div class="tplWrap">';
    tpl += '<div class="title">';
    tpl += '<span>工&emsp;&emsp;单</span>'; 
    tpl += '<svg class="icon" aria-hidden="true">';
    tpl += '<use xlink:href="#icon-you"></use>';
    tpl += '</svg>';
    tpl += '<i>已完成</i>';
    tpl += '</div>';
    tpl += '<div style="width:100%;height:1px;background:#6db738;margin-top:.267rem;"></div>';
    tpl +=  '<div class="min-table" style="margin-top:.667rem;">';
    tpl += '<table>';
    tpl += '<tr>';
    tpl += '<th>选&emsp;&emsp;择</th>';
    tpl += '<th>时&emsp;&emsp;间</th>';
    tpl += '<th>任务名称</th>';
    tpl += '<th>任务类型</th>';
    tpl += '<th>任务内容</th>';
    tpl += '<th>负&ensp;责&ensp;人</th>';
    tpl += '<th>参&ensp;与&ensp;人</th>';
    tpl += '<th>工&emsp;&emsp;期</th>';
    tpl += '<th>操&emsp;&emsp;作</th>';
    tpl += '</tr>';
    //循环处    
    tpl += '<tr>';
    tpl += '<td><input class="checkbox" id="check" type="checkbox"><label for="check"></label></td>';
    tpl += '<td>2017.07.08</td>';
    tpl += '<td class="elli" title="">成都总院光华德阳玉</td>';
    tpl += '<td>文案  设计  程序</td>';
    tpl += '<td class="elli" title="">成都总院光华德阳玉双</td>';
    tpl += '<td>某某某</td>';
    tpl += '<td>';
    tpl += '<select name="selectName" id="selectName">'; 

    tpl += '<option>丽萍</option>';   
    tpl += '<option>小凤</option>';   
    tpl += '<option>雅兰</option>';   
    tpl += '<option>舒克</option>';   
    tpl += '<option>小何</option>'; 

    tpl += '</select>';
    tpl += '</td>';
    tpl += '<td>2天</td>';
    tpl += '<td class="handle"><a class="td_cha">╳</a></td>';
    tpl += '</tr>';
    //结束循环    
    tpl += '</table>';
    tpl += '</div>';
    tpl += '<div class="tab_foot clearfix"><input class="checkbox" id="all_check" type="checkbox"><label class="left" for="all_check"></label><span>全选</span><span>删除</span><span><svg class="icon right" aria-hidden="true"><use xlink:href="#icon-jia"></use></svg></span></div>';
    tpl += '</div>';
    $('.right_box').html(tpl);
})