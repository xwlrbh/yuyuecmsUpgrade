/**
 * Created by A.J on 2019/5/7.
 */
$(document).ready(function(){$("table a.twitter").confirm({title:$("#quedingshanchu").text(),content:$("#bukehuifu").text(),confirmButton:$("#jixu").text(),cancelButton:$("#quxiao").text(),onAction:function(t){if("confirm"==t){var e=this.$target;$.post("selflabelingdel",{id:this.$target.next().val(),verification:$("#verification").text()},function(t){"ok"==t?e.parent().parent().remove():$.alert({title:$("#chucuo").text(),content:t,confirmButton:$("#queding").text()})})}}})});