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
})

