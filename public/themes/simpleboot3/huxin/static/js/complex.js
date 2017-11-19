// JavaScript Document
//滚动的时候地址栏不隐藏，safari浏览器可以通过下面js代码来隐藏地址栏，其他浏览器经测试不可以
	window.addEventListener('load', function(){
	   setTimeout(function(){ window.scrollTo(0, 1); }, 100);
	});
//banner-js
    $(function() {
        $(".flexslider-banner").flexslider({
            animation: "slide",
            slideshowSpeed: 4000, //展示时间间隔ms
            animationSpeed: 400, //滚动时间ms
            touch: true //是否支持触屏滑动
        });
    });	
//主题街-js
	$(function() {
		$(".flexslider-ztj").flexslider({
			animation: "slide",
			animationLoop: false,
			itemWidth: 210,
			itemMargin: 5,
			minItems: 3,
			maxItems: 3
		   // pausePlay: true
		});
	});	