$(function(){

    /*
-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    */
    //初始化 ajax
    var tpl = '';
    tpl += '<div class="tplWrap">';
    tpl +=  '<div class="title">';
    tpl +=  '<span>日&emsp;&emsp;程</span>'; 
    tpl +=  '<svg class="icon" aria-hidden="true"><use xlink:href="#icon-you"></use></svg>';
    tpl +=  '<i>个人进度</i>';
    tpl +=  '</div>';
    tpl +=  '<div style="width:100%;height:1px;background:#6db738;margin-top:.267rem;"></div>';
    tpl +=  '<div class="btns">';
    tpl +=  '<span class="active">按日查看</span><span>按月查看</span>'; 
    tpl +=  '</div>';
    tpl +=  '<div class="min-table">';
    tpl +=  '<table>';
    tpl +=  '<tr>';
    tpl +=  '<th>姓&emsp;&emsp;名</th><th>任务名称</th><th>开始时间</th><th>紧急程度</th><th>进&emsp;&emsp;度</th><th>操&emsp;&emsp;作</th>';
    tpl +=  '</tr>';

    tpl +=  '<tr>';
    tpl +=  '<td>小凤</td>';
    tpl +=  '<td  class="elli" title="">成都总院光华德阳玉双PC移动站修改</td>';
    tpl +=  '<td>2017-07-13 10:26:19</td>';
    tpl +=  '<td class="level urgent"><span>紧&emsp;急</span></td>';
    tpl +=  '<td>50%</td>';
    tpl +=  '<td class="handle"><span class="tipbtn">提&emsp;醒</span></td>';
    tpl +=  '</tr>';

    tpl +=  '</table>';
    tpl +=  '</div>';
    tpl +=  '</div>';
    $('.right_box').html(tpl);
    //按月 按日
    $(document).on('click','.btns span', function(){
        $(this).addClass('active').siblings('span').removeClass('active');
    })
})
