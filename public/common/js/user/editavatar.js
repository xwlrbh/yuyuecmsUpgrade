/**
 * Created by A.J on 2019/3/17.
 */
$(document).ready(function(){var a="";$("#upload").uploadify({auto:!0,fileTypeExts:"*.jpg;*.png;*.gif;*.jpeg",multi:!1,formData:{verification:$("#verification").text()},fileSizeLimit:9999,buttonText:$("#buttonText").text(),showUploadedPercent:!0,showUploadedSize:!1,removeTimeout:3,uploader:"uploadavatar",onUploadComplete:function(t,e){a=$("#domain").text()+e,$("#avatar").val(a),$("#avatarImg").attr("src",a)}}),""!=$("#avatar").val()&&$("#avatarImg").attr("src",$("#avatar").val())});