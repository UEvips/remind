$(function(){
    //父选项点击
    $('.menu').on('click', function(){
        $(this).toggleClass('active');
        $(this).find('#arrow').toggleClass('active');
        $(this).next('.list').slideToggle();
    })
    //子选项点击
    $('.list li').on('click', function(){
        $('.list li').removeClass('active');
        $(this).addClass('active')
        $('.menu').removeClass('on');
        $(this).parent('.list').prev('.menu').addClass('on');
    })
})
/*
-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    */