/*** Created by A.J on 2019/3/8.*/$(document).ready(function(){$("#fromdatetime, #todatetime").datetimepicker({format:"yyyy-mm-dd hh:ii:ss"}),$("table a.twitter").confirm({title:$("#quedingshanchu").text(),content:$("#fangruhuishouzhan").text(),confirmButton:$("#jixu").text(),cancelButton:$("#quxiao").text(),onAction:function(t){if("confirm"==t){var n=this.$target;$.post("recyclingproduct",{id:this.$target.next().val(),verification:$("#verification").text()},function(t){"ok"==t?n.parent().parent().remove():$.alert({title:$("#chucuo").text(),content:t,confirmButton:$("#queding").text()})})}}}),$("#zxuan").click(function(){$(this).prop("checked")?$(".gouxuan").prop("checked",!0):$(".gouxuan").prop("checked",!1)}),$("#zhiding").click(function(){$.caozuo($(this),"zhiding")}),$("#weizhiding").click(function(){$.caozuo($(this),"weizhiding")}),$("#tuijian").click(function(){$.caozuo($(this),"tuijian")}),$("#weituijian").click(function(){$.caozuo($(this),"weituijian")}),$("#pshanchu").click(function(){$.confirm({title:$("#quedingshanchu").text(),content:$("#fangruhuishouzhan").text(),confirmButton:$("#jixu").text(),cancelButton:$("#quxiao").text(),confirm:function(){$.caozuo($(this),"pshanchu")}})})}),$.extend({caozuo:function(t,n){var i="",e=new Array;$(".gouxuan").each(function(t,n){$(this).prop("checked")&&(e.unshift(t),""==i?i=$(this).val():i+=","+$(this).val())}),""!=i?(t.children("span").removeClass("hidden"),$.post("productBatch",{zcuan:i,cz:n,verification:$("#verification").text()},function(i){t.children("span").addClass("hidden"),"ok"==i?$.each(e,function(t,i){switch(n){case"zhiding":$(".gouxuan:eq("+i+")").parent().parent().children(":eq(6)").children(":eq(0)").html('<h5 class="text-success"><span class="glyphicon glyphicon-ok"></span> '+$("#yizhiding").text()+"</h5>");break;case"weizhiding":$(".gouxuan:eq("+i+")").parent().parent().children(":eq(6)").children(":eq(0)").html('<h5 class="text-muted">'+$("#meizhiding").text()+"</h5>");break;case"tuijian":$(".gouxuan:eq("+i+")").parent().parent().children(":eq(6)").children(":eq(1)").html('<h5 class="text-success"><span class="glyphicon glyphicon-ok"></span> '+$("#yituijian").text()+"</h5>");break;case"weituijian":$(".gouxuan:eq("+i+")").parent().parent().children(":eq(6)").children(":eq(1)").html('<h5 class="text-muted">'+$("#meituijian").text()+"</h5>");break;case"pshanchu":$(".gouxuan:eq("+i+")").parent().parent().remove()}}):$.alert({title:$("#chucuo").text(),content:i,confirmButton:$("#queding").text()})})):$.alert({title:$("#jinggao").text(),content:$("#zhishaoxuanyixiang").text(),confirmButton:$("#queding").text()})}});