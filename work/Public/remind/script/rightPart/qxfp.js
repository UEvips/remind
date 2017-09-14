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
    tpl +=  '<span class="active">网络部</span><span>企划部</span><span>运营部</span>'; 
    tpl +=  '</div>';
    tpl +=  '<div class="min-table">';
    tpl +=  '<table>';
    tpl +=  '<tr>';
    tpl +=  '<th>姓&emsp;&emsp;名</th><th>电&emsp;&emsp;话</th><th>编&emsp;&emsp;号</th><th>登&#8197;录&#8197;IP</th><th>登录时间</th><th>所属组别</th><th>操&emsp;&emsp;作</th>';
    tpl +=  '</tr>';

    tpl +=  '<tr>';
    tpl +=  '<td>小凤</td>';
    tpl +=  '<td>成都总院光华德阳玉双PC移动站修改</td>';
    tpl +=  '<td>2017-07-13 10:26:19</td>';
    tpl +=  '<td>2017-07-13 10:26:19</td>';
    tpl +=  '<td class="level urgent">紧&emsp;急</td>';
    tpl +=  '<td>50%</td>';
    tpl +=  '<td class="handle"><span class="tipbtn" style="margin-right:.2rem;">编&emsp;辑</span><span class="delbtn">删&emsp;除</span></td>';
    tpl +=  '</tr>';

    tpl +=  '</table>';
    tpl +=  '</div>';
    tpl +=  '</div>';
    $(".right_box").html(tpl);
    //按月 按日
    $(document).on('click','.btns span', function(){
        $(this).addClass("active").siblings("span").removeClass("active");
    })
})