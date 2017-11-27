$(document).ready(function () {
    $(":submit").click(function () {
        // var card = $("#idcard").val();
        // if (!isCardNo(card)){
        //     swal({
        //         title: "身份证格式错误",
        //         text:"请重新输入!",
        //         type:"error"
        //     });
        //     return false;
        // }
        $.ajax({
            type:"POST",
            url:$("form").attr("action"),
            data:decodeURIComponent($("form").serialize()),
            dataType:"json",
            success:function (data,textStatus) {
                if(data.code === 1){
                    swal({
                        title:data.msg,
                        type:"success"
                    });
                }
                else
                {
                    swal({
                        title:data.msg,
                        type:"error"
                    });
                }

                //额外绑定一个事件，当确定执行之后返回成功的页面的确定按钮，点击之后刷新当前页面或者跳转其他页面
                if (data.url !== ""){
                    $('.confirm').click(function () {
                        window.location = data.url;
                    });
                }
                console.log(data);
            },
            error:function (error) {
                swal({
                    title:"error",
                    type:"error"
                });
                console.log(error);
            }
        });
        return false;
    });
    function findIndex(str,times) {
        var x = str.indexOf("/");
        for (var i = 0; i < times; i++){
            x = str.indexOf(str,x+1);
        }
        return x;
    }
    function getUrl(str,index) {
        var url = str.substring(0,index-1);
        return url;
    }
    function getPara(str,index) {
        var para = str.substring(index+1);
        para = para.split("/");
        console.log("Para");
        console.log(para);
        return para;
    }
    $(".ajaxA").click(function () {
        var str = $(".ajaxA").attr("href");
        var index = findIndex(str,4);
        var url = getUrl(str,index);
        var para = getPara(str,index);
        return false;
        $.ajax({
            type:"GET",
            url:$(".ajaxA").attr("href"),
            // data:decodeURIComponent($("form").serialize()),
            dataType:"json",
            success:function (data,textStatus) {
                console.log(data);
                if(data.code === 1){
                    swal({
                        title:data.msg,
                        type:"success"
                    });
                }
                else
                {
                    swal({
                        title:data.msg,
                        type:"error"
                    });
                }

                //额外绑定一个事件，当确定执行之后返回成功的页面的确定按钮，点击之后刷新当前页面或者跳转其他页面
                if (data.url !== ""){
                    $('.confirm').click(function () {
                        window.location = data.url;
                    });
                }
                console.log(data);
            },
            error:function (error) {
                swal({
                    title:"error",
                    type:"error"
                });
                console.log(error);
            }
        });
        return false;
    })
})

