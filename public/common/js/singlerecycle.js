/**
 * Created by A.J on 2019/3/8.
 */
$(document).ready(function(){$("table a.twitter").confirm({title:$("#quedingshanchu").text(),content:$("#bukehuifu").text(),confirmButton:$("#jixu").text(),cancelButton:$("#quxiao").text(),onAction:function(t){if("confirm"==t){var n=this.$target;$.post("deletepage",{id:this.$target.next().val(),verification:$("#verification").text()},function(t){"ok"==t?n.parent().parent().remove():$.alert({title:$("#chucuo").text(),content:t,confirmButton:$("#queding").text()})})}}}),$("table a.twhy").confirm({title:$("#quedinghuanyuan").text(),content:$("#huanyuan").text(),confirmButton:$("#jixu").text(),cancelButton:$("#quxiao").text(),onAction:function(t){if("confirm"==t){var n=this.$target;$.post("restorepage",{id:this.$target.next().val(),verification:$("#verification").text()},function(t){"ok"==t?n.parent().parent().remove():$.alert({title:$("#chucuo").text(),content:t,confirmButton:$("#queding").text()})})}}}),$("#zxuan").click(function(){$(this).prop("checked")?$(".gouxuan").prop("checked",!0):$(".gouxuan").prop("checked",!1)}),$("#phuanyuan").click(function(){$.confirm({title:$("#quedinghuanyuan").text(),content:$("#huanyuanxuanzhong").text(),confirmButton:$("#jixu").text(),cancelButton:$("#quxiao").text(),confirm:function(){$.caozuo($(this),"phuanyuan")}})}),$("#pshanchu").click(function(){$.confirm({title:$("#quedingshanchu").text(),content:$("#shanchuxuanzhong").text(),confirmButton:$("#jixu").text(),cancelButton:$("#quxiao").text(),confirm:function(){$.caozuo($(this),"pshanchu")}})})}),$.extend({caozuo:function(t,n){var e="",i=new Array;$(".gouxuan").each(function(t,n){$(this).prop("checked")&&(i.unshift(t),""==e?e=$(this).val():e+=","+$(this).val())}),""!=e?(t.children("span").removeClass("hidden"),$.post("recyclePageBatch",{zcuan:e,cz:n,verification:$("#verification").text()},function(e){t.children("span").addClass("hidden"),$.each(i,function(t,e){switch(n){case"phuanyuan":case"pshanchu":$(".gouxuan:eq("+e+")").parent().parent().remove()}})})):$.alert({title:$("#jinggao").text(),content:$("#zhishaoxuanyixiang").text(),confirmButton:$("#queding").text()})}});