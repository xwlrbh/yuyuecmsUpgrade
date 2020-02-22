/**
 * Created by A.J on 2019/3/9.
 */
$(document).ready(function(){$("#biaoqian").on("change",function(){""!=$.trim($(this).val())&&$.post("newslabelconfchk",{id:$("#id").val(),biaoqian:$(this).val(),verification:$("#verification").text()},function(i){"ok"!=i&&$.alert({title:"",content:i,confirmButton:$("#queding").text()})})})});