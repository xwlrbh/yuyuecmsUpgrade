/*** Created by A.J on 2019/3/15.*/$(document).ready(function(){$(".lahei").click(function(){var i=$(this);i.children("span").removeClass("hidden"),$.post("laheiqiyong",{id:i.siblings(":last").val(),zt:i.siblings(":first").val(),verification:$("#verification").text()},function(s){i.siblings(":first").val(0),i.parent().prev().html('<span class="text-muted">'+$("#jinyong").text()+"</span>"),i.children("span").addClass("hidden"),i.addClass("hidden").next().removeClass("hidden")})}),$(".qiyong").click(function(){var i=$(this);i.children("span").removeClass("hidden"),$.post("laheiqiyong",{id:i.siblings(":last").val(),zt:i.siblings(":first").val(),verification:$("#verification").text()},function(s){i.siblings(":first").val(1),i.parent().prev().html('<span class="text-success"><span class="glyphicon glyphicon-ok"></span> '+$("#zhengchang").text()+"</span>"),i.children("span").addClass("hidden"),i.addClass("hidden").prev().removeClass("hidden")})})});