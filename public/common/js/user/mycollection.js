/**
 * Created by A.J on 2019/3/25.
 */
$(document).ready(function(){$(".shanchu").click(function(){var i=$(this);$.post("removeshoucang",{id:$(this).prev().val(),verification:$("#verification").text()},function(n){i.parent().parent().remove()})})});