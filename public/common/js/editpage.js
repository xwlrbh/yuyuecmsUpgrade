/*** Created by A.J on 2019/3/6.*/$(document).ready(function(){var t=HE.getEditor("zhengwen",{autoHeight:!0,autoFloat:!0,topOffset:51,uploadPhoto:!0,uploadPhotoHandler:$("#upload_handyeditor_url").text(),uploadPhotoSize:2e3,uploadPhotoType:"gif,png,jpg,jpeg,webp",uploadPhotoSizeError:$("#sizeError").text(),uploadPhotoTypeError:$("#typeError").text(),skin:"catfish"});$("#baocun").click(function(){if($.catfishcms()){$("#zhengwen").text(t.getHtml()),""==$("#zhaiyao").val()&&(t.getText().length>500?$("#zhaiyao").val(t.getText().replace(/\n/g," ").substr(0,500)+"..."):$("#zhaiyao").val(t.getText().replace(/\n/g," ")));var i=$(this);i.children("span").removeClass("hidden"),$.post("",$("#writeForm").serialize(),function(t){i.children("span").addClass("hidden"),"ok"==t?($("#id").length<1||""==$("#id").val())&&window.location.reload():$.alert({title:$("#chucuo").text(),content:t,confirmButton:$("#queding").text()})})}}),$("#fabushijian").length>0&&$("#fabushijian").datetimepicker({format:"yyyy-mm-dd hh:ii:ss",autoclose:!0});var i="",e="",a="";if(""!=$("#slt").val()&&(i=$("#suolvetu").html(),$("#suolvetu img").attr("src",$("#slt").val()),$("#shangchuantu").addClass("hidden"),$("#quxiaotu").removeClass("hidden")),""!=$("#zstu").val()&&($("#zstudiv").html('<img src="'+$("#domain").text()+$("#zstu").val()+'" class="img-responsive">'),$("#quxiaozstu").removeClass("hidden")),""!=$("#shipin").val()&&($("#videodiv").html('<h2 class="text-success"><i class="icon-film"></i><i class="icon-ok"></i></h2>'),$("#quxiaoshipin").removeClass("hidden")),""!=$("#zutu").val()){var l=$("#zutu").val();$("#zutu").val(","+$("#zutu").val());var o=l.split(",");$.each(o,function(t,i){$("#groupdiv").html($("#groupdiv").html()+'<div class="col-sm-4 col-md-3"><div class="thumbnail"><img src="'+$("#domain").text()+i+'"><div class="caption text-center"><a href="#!" class="quxiaotupian">'+$("#cancelimage").text()+'</a><div class="hidden">'+i+"</div></div></div></div>")})}if(""!=$("#wenjianzu").val()){var n=$("#wenjianzu").val();$("#wenjianzu").val(","+$("#wenjianzu").val());var s=n.split(",");$.each(s,function(t,i){var e=i.lastIndexOf("/"),a=i.substr(e+1);$("#filesdiv").html($("#filesdiv").html()+'<div class="col-sm-4 col-md-3"><div class="thumbnail"><div class="text-center"><i class="icon-file icon-3x"></i></div><div class="text-center" style="word-wrap:break-word">'+a+'</div><div class="caption text-center"><a href="#!" class="quxiaowenjian">'+$("#deletefiles").text()+'</a><div class="hidden">'+i+"</div></div></div></div>")})}$("#upload").uploadify({auto:!0,fileTypeExts:"*.jpg;*.png;*.gif;*.jpeg",multi:!1,formData:{verification:$("#verification").text()},fileSizeLimit:9999,buttonText:$("#buttonText").text(),showUploadedPercent:!0,showUploadedSize:!1,removeTimeout:3,uploader:$("#upload_thumb_url").text(),onUploadComplete:function(t,i){e=$("#domain").text()+i,$("#bendi .panel-body").html('<img src="'+e+'" class="img-responsive" alt="Responsive image">')}}),$("#queding").click(function(){i=$("#suolvetu").html(),$("#xuanbendi").hasClass("active")&&""!=e?($("#suolvetu").html('<img src="'+e+'" class="img-responsive" alt="Responsive image">'),$("#slt").val(e)):$("#xuanwangluo").hasClass("active")&&""!=a&&($("#suolvetu").html('<img src="'+a+'" class="img-responsive" alt="Responsive image">'),$("#slt").val(a)),""==e&&""==a||($("#shangchuantu").addClass("hidden"),$("#quxiaotu").removeClass("hidden")),$("#myModal").modal("hide")}),$("#quxiaotu").click(function(){$.post("delthumb",{slt:$("#slt").val(),verification:$("#verification").text()},function(t){"ok"==t?($("#suolvetu").html(i),$("#slt").val(""),$("#quxiaotu").addClass("hidden"),$("#shangchuantu").removeClass("hidden")):$.alert({title:$("#chucuo").text(),content:t,confirmButton:$("#queding").text()})})}),$("#wangluodizhi").change(function(){a=$("#wangluodizhi").val(),$("#wangluo .panel-body").html('<img src="'+a+'" class="img-responsive">'),$("#slt").val(a)}),$("#zstuupload").uploadify({auto:!0,fileTypeExts:"*.jpg;*.png;*.gif;*.jpeg;*.webp",multi:!0,formData:{verification:$("#verification").text()},fileSizeLimit:9999,buttonText:$("#buttonText_image").text(),showUploadedPercent:!0,showUploadedSize:!1,removeTimeout:3,uploader:$("#upload_image_url").text(),onUploadComplete:function(t,i){"data/"==i.substr(0,5)?($("#zstu").val(i),$("#zstudiv").html('<img src="'+$("#domain").text()+i+'" class="img-responsive">'),$("#quxiaozstu").removeClass("hidden")):$.alert({title:$("#alertitle").text(),content:i,confirmButton:$("#queding").text()})}}),$("#quxiaozstu").on("click",function(){$.post($("#upload_delfile_url").text(),{delfile:$("#zstu").val(),verification:$("#verification").text()},function(t){$("#zstu").val(""),$("#zstudiv").html(""),$("#quxiaozstu").addClass("hidden")})}),$("#videoupload").uploadify({auto:!0,fileTypeExts:"*.mp4;*.ogg;*.webm;*.flv;*.wav;*.avi;*.rmvb",multi:!1,formData:{verification:$("#verification").text()},fileSizeLimit:9999999,buttonText:$("#buttonText_video").text(),showUploadedPercent:!0,showUploadedSize:!1,removeTimeout:3,uploader:$("#upload_video_url").text(),onUploadComplete:function(t,i){"data/"==i.substr(0,5)?($("#shipin").val(i),$("#videodiv").html('<h2 class="text-success"><i class="icon-film"></i><i class="icon-ok"></i></h2>'),$("#quxiaoshipin").removeClass("hidden")):$.alert({title:$("#alertitle").text(),content:i,confirmButton:$("#queding").text()})}}),$("#quxiaoshipin").click(function(){$.post($("#upload_delfile_url").text(),{delfile:$("#shipin").val(),verification:$("#verification").text()},function(t){$("#shipin").val(""),$("#videodiv").html(""),$("#quxiaoshipin").addClass("hidden")})}),$("#groupupload").uploadify({auto:!0,fileTypeExts:"*.jpg;*.png;*.gif;*.jpeg;*.webp",multi:!0,formData:{verification:$("#verification").text()},fileSizeLimit:9999,buttonText:$("#buttonText_image").text(),showUploadedPercent:!0,showUploadedSize:!1,removeTimeout:3,uploader:$("#upload_image_url").text(),onUploadComplete:function(t,i){"data/"==i.substr(0,5)?($("#zutu").val($("#zutu").val()+","+i),$("#groupdiv").html($("#groupdiv").html()+'<div class="col-sm-4 col-md-3"><div class="thumbnail"><img src="'+$("#domain").text()+i+'"><div class="caption text-center"><a href="#!" class="quxiaotupian">'+$("#cancelimage").text()+'</a><div class="hidden">'+i+"</div></div></div></div>")):$.alert({title:$("#alertitle").text(),content:i,confirmButton:$("#queding").text()})}}),$("#groupdiv").on("click","a",function(){var t=$(this),i=t.next().text();$.post($("#upload_delfile_url").text(),{delfile:i,verification:$("#verification").text()},function(e){var a=$("#zutu").val();$("#zutu").val(a.replace(","+i,"")),t.parent().parent().parent().remove()})}),$("#alias").on("change",function(){""!=$.trim($(this).val())&&$.post($("#upload_alias_url").text(),{id:$.trim($("#id").val()),alias:$.trim($(this).val()),verification:$("#verification").text()},function(t){"ok"!=t&&$.alert({title:"",content:t,confirmButton:$("#queding").text()})})}),$("#filesupload").uploadify({auto:!0,fileTypeExts:"*.doc;*.docx;*.xls;*.xlsx;*.ppt;*.htm;*.html;*.txt;*.zip;*.rar;*.gz;*.bz2;*.pdf;*.apk;*.swf",multi:!0,formData:{verification:$("#verification").text()},fileSizeLimit:999999,buttonText:$("#buttonText_file").text(),showUploadedPercent:!0,showUploadedSize:!1,removeTimeout:3,uploader:$("#upload_file_url").text(),onUploadComplete:function(t,i){if("data/"==i.substr(0,5)){$("#wenjianzu").val($("#wenjianzu").val()+","+i);var e=i.lastIndexOf("/"),a=i.substr(e+1);$("#filesdiv").html($("#filesdiv").html()+'<div class="col-sm-4 col-md-3"><div class="thumbnail"><div class="text-center"><i class="icon-file icon-3x"></i></div><div class="text-center" style="word-wrap:break-word">'+a+'</div><div class="caption text-center"><a href="#!" class="quxiaowenjian">'+$("#deletefiles").text()+'</a><div class="hidden">'+i+"</div></div></div></div>")}else $.alert({title:$("#alertitle").text(),content:i,confirmButton:$("#queding").text()})}}),$("#filesdiv").on("click","a",function(){var t=$(this),i=t.next().text();$.post($("#upload_delfile_url").text(),{delfile:i,verification:$("#verification").text()},function(e){var a=$("#wenjianzu").val();$("#wenjianzu").val(a.replace(","+i,"")),t.parent().parent().parent().remove()})})});