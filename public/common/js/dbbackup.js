/*** Created by A.J on 2019/9/2.*/$(document).ready(function(){$("table a.shanchu").confirm({title:$("#quedingshanchu").text(),content:$("#fangruhuishouzhan").text(),confirmButton:$("#jixu").text(),cancelButton:$("#quxiao").text(),onAction:function(t){if("confirm"==t){var n=this.$target;$.post("deldbbackup",{fn:this.$target.siblings(":first").val(),verification:$("#verification").text()},function(t){"ok"==t?n.parent().parent().remove():$.alert({title:$("#chucuo").text(),content:t,confirmButton:$("#queding").text()})})}}}),$("table a.huanyuan").confirm({title:$("#quedinghuanyuan").text(),content:$("#huanyuanshuoming").text(),confirmButton:$("#jixu").text(),cancelButton:$("#quxiao").text(),onAction:function(t){if("confirm"==t){this.$target;$("#dbbackupModal").modal(),$.post("redbbackup",{fn:this.$target.siblings(":first").val(),verification:$("#verification").text()},function(t){$("#dbbackupModal").modal("hide"),"ok"==t?$.alert({title:"",content:$("#redbbackupok").text(),confirmButton:$("#queding").text()}):$.alert({title:$("#chucuo").text(),content:t,confirmButton:$("#queding").text()})})}}}),$("#submitupreform").confirm({title:$("#quedingshangchuan").text(),content:$("#huanyuanshuoming").text()+"<br>"+$("#shangchuanshuoming").text(),confirmButton:$("#jixu").text(),cancelButton:$("#quxiao").text(),onAction:function(t){"confirm"==t&&(""==$("#bkfile").val()?$.alert({title:$("#chucuo").text(),content:$("#notselected").text(),confirmButton:$("#queding").text()}):$("#upreform").submit())}})});