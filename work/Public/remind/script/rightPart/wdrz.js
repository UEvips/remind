$(function(){
    /*
-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    */
    //初始化 ajax
    var tpl = '';
    tpl+= '<div class="tplWrap">';
    tpl+= '<div class="title">';
    tpl+= '<span>日&emsp;&emsp;志</span>'; 
    tpl+= '<svg class="icon" aria-hidden="true">';
    tpl+= '<use xlink:href="#icon-you"></use>';
    tpl+= '</svg>';
    tpl+= '<i>我的日志</i>';
    tpl+= '</div>';
    tpl+= '<div style="width:100%;height:1px;background:#6db738;margin-top:.267rem;"></div>';
    tpl+= '<div class="min-table" style="margin-top:.667rem;">';
    tpl+= '<div class="t_head clearfix">';                              
    tpl+= '<div class="datePick left">';
    tpl+= '<input type="text" id="ECalendar_case1" placeholder="选择开始时间" readonly />';
    tpl+= '</div>';
    tpl+= '<div class="to left">&ensp;to&ensp;</div>';
    tpl+= '<div class="datePick left">';
    tpl+= '<input type="text" id="ECalendar_case2" placeholder="选择结束时间" readonly />';
    tpl+= '</div>';
    tpl+= '<div class="left search">搜索</div>';
    tpl+= '</div>';
    tpl+= '<div class="list_contain clearfix">';
    //list_one 循环处
    for(var i=0;i<8;i++){
        tpl+= '<div class="list_one">';
        tpl+= '<div class="one_time clearfix">';
        tpl+= '<p class="left">创建时间：2017.07.08</p>';
        tpl+= '<p class="right">周丽萍</p>';
        tpl+= '</div>';
        tpl+= '<div class="one_txt">';
        tpl+= '<div class="noon t_box clearfix">';
        tpl+= '<p class="left">上&emsp;&emsp;午：</p>';
        tpl+= '<ul class="right">';
        tpl+= '<li>1、项目名称-类型-工作内容工作内容</li>';
        tpl+= '<li>2、项目名称-类型-工作内容工作内容</li>';
        tpl+= '</ul>';
        tpl+= '</div>';
        tpl+= '<div class="afternoon t_box clearfix">';
        tpl+= '<p class="left">下&emsp;&emsp;午：</p>';
        tpl+= '<ul class="right">';
        tpl+= '<li>1、项目名称-类型-工作内容工作内容</li>';
        tpl+= '<li>2、项目名称-类型-工作内容工作内容</li>';
        tpl+= '</ul>';
        tpl+= '</div>';
        tpl+= '<div class="tomorrow t_box clearfix">';
        tpl+= '<p class="left">明天安排：</p>';
        tpl+= '<ul class="right">';
        tpl+= '<li>1、项目名称-类型-工作内容工作内容工作内容</li>';
        tpl+= '<li>2、其他其他其他其他其他其他其他其他其他其他</li>';
        tpl+= '</ul>';
        tpl+= '</div>';
        tpl+= '</div>';
        tpl+= '<div class="one_foot clearfix">';
        tpl+= '<div class="right one_right clearfix">';
        tpl+= '<p class="change"><svg class="icon" aria-hidden="true">';
        tpl+= '<use xlink:href="#icon-xiugai"></use>';
        tpl+= '</svg>&ensp;修改</p>';
        tpl+= '<p class="delete"><svg class="icon" aria-hidden="true">';
        tpl+= '<use xlink:href="#icon-shanchu"></use>';
        tpl+= '</svg>&ensp;删除</p>';
        tpl+= '<p class="more">展开&ensp;<svg class="icon" aria-hidden="true">';
        tpl+= '<use xlink:href="#icon-jiantouyoushuang-"></use>';
        tpl+= '</svg></p>';
        tpl+= '</div>';
        tpl+= '</div>';
        tpl+= '</div>';
    }
    //list_one循环结束
    tpl+= '</div>';
    tpl+= '</div>';

    tpl+= '<div class="tab_foot clearfix"><input class="checkbox" id="all_check" type="checkbox"><label class="left" for="all_check"></label><span>全选</span><span>删除</span><span><svg class="icon right" aria-hidden="true"><use xlink:href="#icon-jia"></use></svg></span></div>';
    tpl+= '</div>';
    $('.right_box').html(tpl);
    //时间选择器实例化
    $("#ECalendar_case1").ECalendar({
        type:"date",
        stamp:false,
        skin:"#6db738",
        offset:[0,5]
    });
    $("#ECalendar_case2").ECalendar({
        type:"date",
        stamp:false,
        skin:'#6db738',
        offset:[0,2]
    });
})