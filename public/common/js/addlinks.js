/**
 * Created by A.J on 2019/3/12.
 */
$(document).ready(function(){""!=$("#tubiao").val()&&$("#linkImg").attr("src",$("#domain").text()+$("#tubiao").val());$("#upload").uploadify({auto:!0,fileTypeExts:"*.jpg;*.png;*.gif;*.jpeg",multi:!1,formData:{verification:$("#verification").text()},fileSizeLimit:9999,buttonText:$("#buttonText").text(),showUploadedPercent:!0,showUploadedSize:!1,removeTimeout:3,uploader:"uploadlinkimage",onUploadStart:function(){this.formData.upd=$("#tubiao").val()},onUploadComplete:function(t,i){$("#tubiao").val(i),$("#linkImg").attr("src",$("#domain").text()+i)}}),$("#baocun").click(function(){if($.catfishcms()){var t=$(this);t.children("span").removeClass("hidden"),$.post("",$("#linksForm").serialize(),function(i){t.children("span").addClass("hidden"),"ok"==i?($("#id").length<1||""==$("#id").val())&&window.location.reload():$.alert({title:$("#chucuo").text(),content:i,confirmButton:$("#queding").text()})})}})});