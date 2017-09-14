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
    tpl += '<i>待分配</i>';
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
    tpl += '<td class="handle threeBn"><span class="tipbtn" style="margin-right:.28rem;">提&emsp;醒</span><span class="tipbtn" style="margin-right:.28rem;">审&emsp;核</span><span class="td_cha">╳</span></td>';
    tpl += '</tr>';
    //结束循环    
    tpl += '</table>';
    tpl += '</div>';
    tpl += '<div class="tab_foot clearfix"><input class="checkbox" id="all_check" type="checkbox"><label class="left" for="all_check"></label><span>全选</span><span>删除</span><span><svg class="icon right" aria-hidden="true"><use xlink:href="#icon-jia"></use></svg></span></div>';
    tpl += '</div>';
    $('.right_box').html(tpl);
})