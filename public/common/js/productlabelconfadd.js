/**
 * Created by A.J on 2019/3/8.
 */
$(document).ready(function(){$("#biaoqian").on("change",function(){""!=$.trim($(this).val())&&$.post("productlabelconfchk",{id:"",biaoqian:$(this).val(),verification:$("#verification").text()},function(t){"ok"!=t&&$.alert({title:"",content:t,confirmButton:$("#queding").text()})})})});