/** * Created by A.J on 2019/3/15. */$(document).ready(function(){""!=$("#tubiao").val()&&$("#tubiaoImg").attr("src",$("#tubiao").val());var t="";$("#upload").uploadify({auto:!0,fileTypeExts:"*.jpg;*.png;*.gif;*.jpeg",multi:!1,formData:{verification:$("#verification").text()},fileSizeLimit:9999,buttonText:$("#buttonText").text(),showUploadedPercent:!0,showUploadedSize:!1,removeTimeout:3,uploader:"uploadimage",onUploadComplete:function(i,o){t=$("#domain").text()+o,$("#tubiao").val(t),$("#tubiaoImg").attr("src",t)}}),""!=$("#icotubiao").val()&&$("#icotubiaoIco").attr("src",$("#icotubiao").val());var i="";$("#upload_ico").uploadify({auto:!0,fileTypeExts:"*.ico",multi:!1,formData:{verification:$("#verification").text()},fileSizeLimit:9999,buttonText:$("#icobuttonText").text(),showUploadedPercent:!0,showUploadedSize:!1,removeTimeout:3,uploader:"uploadIco",onUploadComplete:function(t,o){i=$("#domain").text()+o,$("#icotubiao").val(i),$("#icotubiaoIco").attr("src",i)}}),$("#gudingbi").prop("checked")&&$("#kuangaobi").removeClass("hidden"),$("#gudingbi").change(function(){$("#gudingbi").prop("checked")?$("#kuangaobi").removeClass("hidden"):$("#kuangaobi").addClass("hidden")}),$("form").submit(function(t){if(!/^(http:\/\/|https:\/\/)[^ |,]+\/$/.test($("#domainid").val()))return $.alert({title:$("#chucuo").text(),content:$("#yumingtishi").text(),confirmButton:$("#queding").text()}),setTimeout(function(){$("#submitid").find("span:eq(0)").addClass("hidden")},1),!1})});