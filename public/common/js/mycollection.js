/**
 * Created by A.J on 2019/3/25.
 */
$(document).ready(function(){$(".shanchu").click(function(){if($.catfishcms()){var i=$(this);i.children("span").removeClass("hidden"),$.post("mycollectiondel",{id:$(this).prev().val(),verification:$("#verification").text()},function(n){i.children("span").addClass("hidden"),i.parent().parent().remove()})}})});