/*** Created by A.J on 2019/3/8.*/$(document).ready(function(){$("#fromdatetime, #todatetime").datetimepicker({format:"yyyy-mm-dd hh:ii:ss"}),$("table a.twitter").confirm({title:$("#quedingshanchu").text(),content:$("#fangruhuishouzhan").text(),confirmButton:$("#jixu").text(),cancelButton:$("#quxiao").text(),onAction:function(t){if("confirm"==t){var n=this.$target;$.post("recyclingpage",{id:this.$target.next().val(),verification:$("#verification").text()},function(t){"ok"==t?n.parent().parent().remove():$.alert({title:$("#chucuo").text(),content:t,confirmButton:$("#queding").text()})})}}}),$("#zxuan").click(function(){$(this).prop("checked")?$(".gouxuan").prop("checked",!0):$(".gouxuan").prop("checked",!1)}),$("#pshanchu").click(function(){$.confirm({title:$("#quedingshanchu").text(),content:$("#fangruhuishouzhan").text(),confirmButton:$("#jixu").text(),cancelButton:$("#quxiao").text(),confirm:function(){$.caozuo($(this),"pshanchu")}})})}),$.extend({caozuo:function(t,n){var e="",i=new Array;$(".gouxuan").each(function(t,n){$(this).prop("checked")&&(i.unshift(t),""==e?e=$(this).val():e+=","+$(this).val())}),""!=e?(t.children("span").removeClass("hidden"),$.post("pageBatch",{zcuan:e,cz:n,verification:$("#verification").text()},function(e){t.children("span").addClass("hidden"),$.each(i,function(t,e){switch(n){case"pshanchu":$(".gouxuan:eq("+e+")").parent().parent().remove()}})})):$.alert({title:$("#jinggao").text(),content:$("#zhishaoxuanyixiang").text(),confirmButton:$("#queding").text()})}});