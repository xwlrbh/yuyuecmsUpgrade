/**
 * Created by A.J on 2020/2/21.
 */
$(document).ready(function(){1==$("#directly").text()&&$.post("remotepackage",{verification:$("#verification").text()},function(t){"ok"==t?($("#directlyspan").text($("#tishixinxi2").text()),$.upgrading(1)):($.shibai(),$.alert({title:$("#chucuo").text(),content:t,confirmButton:$("#queding").text()}))}),$("#upload").uploadify({auto:!0,fileTypeExts:"*.zip",multi:!1,formData:{verification:$("#verification").text()},fileSizeLimit:9999999,buttonText:$("#buttonText").text(),showUploadedPercent:!0,showUploadedSize:!1,removeTimeout:3,uploader:"upgradepackage",onUploadStart:function(){$("#shengjizhuangtai").text($("#tishixinxi1").text()).removeClass("hidden")},onUploadComplete:function(t,e){"ok"==e?($("#shengjizhuangtai").text($("#tishixinxi2").text()),$.upgrading(0)):($("#shengjizhuangtai").text($("#unsuccessful").text()),$.alert({title:$("#chucuo").text(),content:e,confirmButton:$("#queding").text()}))}}),$.extend({upgrading:function(t){$.post("upgrading",{verification:$("#verification").text(),auto:t},function(e){"ok"==e?($("#directlyspan").text($("#upgradeok").text()).prev("span").addClass("hidden"),$("#shengjizhuangtai").text($("#upgradeok").text())):($("#directlyspan").text($("#unsuccessful").text()).prev("span").addClass("hidden"),1==t&&$.shibai(),$.alert({title:$("#chucuo").text(),content:e,confirmButton:$("#queding").text()}))})},shibai:function(){$("#directlyspan").text($("#unsuccessful").text()).prev("span").addClass("hidden"),$("#shibai").removeClass("hidden"),$("#shougong").removeClass("hidden")}})});