/**
 * Created by A.J on 2019/11/7.
 */
$(document).ready(function(){$("#baocun").click(function(){if($.catfishcms()){var t=$(this);t.children("span").removeClass("hidden"),$.post("",$("#catfishForm").serialize(),function(n){t.children("span").addClass("hidden"),"ok"!=n?$.alert({title:$("#chucuo").text(),content:n,confirmButton:$("#queding").text()}):0==$("#ceshi").length&&window.location.reload()})}}),$("#ceshi").on("click",function(){var t=$(this);t.children("span").removeClass("hidden"),$.post("csmail",{verification:$("#verification").text()},function(n){t.children("span").addClass("hidden"),"ok"==n?$.alert({title:"",content:$("#mailsuccessfully").text(),confirmButton:$("#queding").text()}):$.alert({title:$("#chucuo").text(),content:n,confirmButton:$("#queding").text()})})})});