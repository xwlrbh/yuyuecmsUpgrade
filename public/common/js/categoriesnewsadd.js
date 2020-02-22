/**
 * Created by A.J on 2019/3/17.
 */
$(document).ready(function(){$("#alias").on("change",function(){""!=$.trim($(this).val())&&$.post("categoriesnewsaliaschk",{id:$.trim($("#id").val()),alias:$.trim($(this).val()),verification:$("#verification").text()},function(t){"ok"!=t&&$.alert({title:"",content:t,confirmButton:$("#queding").text()})})})});