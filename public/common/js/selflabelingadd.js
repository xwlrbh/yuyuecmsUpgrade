/**
 * Created by A.J on 2019/5/7.
 */
$(document).ready(function(){var t=HE.getEditor("zhengwen",{autoHeight:!0,autoFloat:!0,topOffset:51,uploadPhoto:!0,uploadPhotoHandler:$("#upload_handyeditor_url").text(),uploadPhotoSize:2e3,uploadPhotoType:"gif,png,jpg,jpeg",uploadPhotoSizeError:$("#sizeError").text(),uploadPhotoTypeError:$("#typeError").text(),skin:"catfish"});$("#baocun").click(function(){if($.catfishcms()){$("#zhengwen").text(t.getHtml());var o=$(this);o.children("span").removeClass("hidden"),$.post("",$("#selflabelingForm").serialize(),function(t){o.children("span").addClass("hidden"),"ok"==t?($("#id").length<1||""==$("#id").val())&&window.location.reload():$.alert({title:$("#chucuo").text(),content:t,confirmButton:$("#queding").text()})})}}),$("#biaoqian").on("change",function(){""!=$.trim($(this).val())&&$.post("selflabelingchk",{id:$("#id").val(),biaoqian:$(this).val(),verification:$("#verification").text()},function(t){"ok"!=t&&$.alert({title:"",content:t,confirmButton:$("#queding").text()})})})});