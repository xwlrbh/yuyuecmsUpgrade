/**
 * Created by A.J on 2019/3/15.
 */
$(document).ready(function(){$(".cdfenlei").text($("#caidanfenlei").find("option:selected").text()),$("table a.twitter").confirm({title:$("#quedingshanchu").text(),content:$("#bukehuifu").text(),confirmButton:$("#jixu").text(),cancelButton:$("#quxiao").text(),onAction:function(t){if("confirm"==t){var e=this.$target;$.post("managemenudel",{id:this.$target.next().val(),verification:$("#verification").text()},function(t){"ok"==t?e.parent().parent().remove():$.alert({title:$("#chucuo").text(),content:t,confirmButton:$("#queding").text()})})}}})});