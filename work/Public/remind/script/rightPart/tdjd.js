$(function(){
    /*
-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    */
    //初始化 ajax
    var tpl = '';
    tpl += '<div class="tplWrap">';
    tpl += '<div class="title">';
    tpl += '<span>日&emsp;&emsp;程</span>'; 
    tpl += '<svg class="icon" aria-hidden="true">';
    tpl += '<use xlink:href="#icon-you"></use>';
    tpl += '</svg>';
    tpl += '<i>团队进度</i>';
    tpl += '</div>';
    tpl += '<div style="width:100%;height:1px;background:#6db738;margin-top:.267rem;"></div>';
    tpl +=  '<div class="min-table" style="margin-top:.667rem;">';
    tpl += '<table>';
    tpl += '<tr>';
    tpl += '<th>部&emsp;&emsp;门</th>';
    tpl += '<th>参与人</th>';
    tpl += '<th>任务名称</th>';
    tpl += '<th>开始时间</th>';
    tpl += '<th>计划时间</th>';
    tpl += '<th>进&emsp;&emsp;度</th>';
    tpl += '<th>操&emsp;&emsp;作</th>';
    tpl += '</tr>';
    //循环处    
    tpl += '<tr>';
    tpl += '<td>小凤</td>';
    tpl += '<td>';
    tpl += '<select name="selectName" id="selectName" class="selectName">'; 
    
    tpl += '<option>丽萍</option>';   
    tpl += '<option>小凤</option>';   
    tpl += '<option>雅兰</option>';   
    tpl += '<option>舒克</option>';   
    tpl += '<option>小何</option>'; 
    
    tpl += '</select>';
    tpl += '</td>';
    tpl += '<td>2017-07-13 10:26:19</td>';
    tpl += '<td>2017-07-13 10:26:19</td>';
    tpl += '<td class="level urgent"><span>紧&emsp;急</span></td>';
    tpl += '<td>50%</td>';
    tpl += '<td class="handle"><span class="tipbtn">提&emsp;醒</span></td>';
    tpl += '</tr>';
    //结束循环    
    tpl += '</table>';
    tpl += '</div>';
    tpl += '</div>';
    $('.right_box').html(tpl);
})