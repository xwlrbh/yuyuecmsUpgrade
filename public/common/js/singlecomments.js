/** * Created by A.J on 2019/3/24. */$(document).ready(function(){$(".yincang").click(function(){var e=$(this);e.children("span").removeClass("hidden"),$.post("pageshenhepinglun",{id:e.siblings(":last").val(),zt:e.siblings(":first").val(),verification:$("#verification").text()},function(n){e.siblings(":first").val(0),e.parent().prev().html('<span class="text-muted">'+$("#meishenhe").text()+"</span>"),e.children("span").addClass("hidden"),e.addClass("hidden").next().removeClass("hidden")})}),$(".qiyong").click(function(){var e=$(this);e.children("span").removeClass("hidden"),$.post("pageshenhepinglun",{id:e.siblings(":last").val(),zt:e.siblings(":first").val(),verification:$("#verification").text()},function(n){e.siblings(":first").val(1),e.parent().prev().html('<span class="text-success"><span class="glyphicon glyphicon-ok"></span> '+$("#yishenhe").text()+"</span>"),e.children("span").addClass("hidden"),e.addClass("hidden").prev().removeClass("hidden")})}),$("table a.twitter").confirm({title:$("#quedingshanchu").text(),content:$("#bukehuifu").text(),confirmButton:$("#jixu").text(),cancelButton:$("#quxiao").text(),confirm:function(){var e=this.$target;$.post("pagecommentdel",{id:this.$target.siblings(":last").val(),verification:$("#verification").text()},function(n){e.parent().parent().remove()})}}),$("#zxuan").click(function(){$(this).prop("checked")?$(".gouxuan").prop("checked",!0):$(".gouxuan").prop("checked",!1)}),$("#shenhe").click(function(){$.caozuo($(this),"shenhe")}),$("#weishenhe").click(function(){$.caozuo($(this),"weishenhe")})}),$.extend({caozuo:function(e,n){var i="",t=new Array;$(".gouxuan").each(function(e,n){$(this).prop("checked")&&(t.unshift(e),""==i?i=$(this).val():i+=","+$(this).val())}),""!=i?(e.children("span").removeClass("hidden"),$.post("pagecommentbatch",{zcuan:i,cz:n,verification:$("#verification").text()},function(i){e.children("span").addClass("hidden"),$.each(t,function(e,i){switch(n){case"shenhe":$(".gouxuan:eq("+i+")").parent().parent().children(":eq(6)").children(":eq(0)").html('<span class="text-success"><span class="glyphicon glyphicon-ok"></span> '+$("#yishenhe").text()+"</span>"),$(".gouxuan:eq("+i+")").parent().siblings(":last").children("a:eq(1)").removeClass("hidden"),$(".gouxuan:eq("+i+")").parent().siblings(":last").children("a:eq(2)").addClass("hidden");break;case"weishenhe":$(".gouxuan:eq("+i+")").parent().parent().children(":eq(6)").children(":eq(0)").html('<span class="text-muted">'+$("#meishenhe").text()+"</span>"),$(".gouxuan:eq("+i+")").parent().siblings(":last").children("a:eq(2)").removeClass("hidden"),$(".gouxuan:eq("+i+")").parent().siblings(":last").children("a:eq(1)").addClass("hidden")}})})):$.alert({title:$("#jinggao").text(),content:$("#zhishaoxuanyixiang").text(),confirmButton:$("#queding").text()})}});