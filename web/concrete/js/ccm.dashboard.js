ccm_closeDashboardPane=function(e){$(e).closest("div.ccm-pane").fadeOut(120,"easeOutExpo")};ccm_getDashboardBackgroundImageData=function(e,t){$.getJSON(CCM_TOOLS_PATH+"/dashboard/get_image_data",{image:e},function(e){if(e&&t){var n="<div>";n+="<strong>"+e.title+"</strong> "+ccmi18n.authoredBy+" ";e.link?n+='<a target="_blank" href="'+e.link+'">'+e.author+"</a>":n+=e.author;$('<div id="ccm-dashboard-image-caption" class="ccm-ui"/>').html(n).appendTo(document.body).show();setTimeout(function(){$("#ccm-dashboard-image-caption").fadeOut(1e3,"easeOutExpo")},5e3)}})};var lastSizeCheck=9999999;ccm_testFixForms=function(){$(window).width()<=560&&lastSizeCheck>560?ccm_fixForms():$(window).width()>560&&lastSizeCheck<=560&&ccm_fixForms(!0);lastSizeCheck=$(window).width()};ccm_fixForms=function(e){$("form").each(function(){var t=$(this);e?t.attr("original-class")=="form-horizontal"&&t.attr("class","").addClass("form-horizontal"):t.removeClass("form-horizontal")})};ccm_dashboardEqualizeMenus=function(){if($(window).width()<560){$("div.dashboard-icon-list div.well").css("visibility","visible");return!1}var e=-1,t,n=0,r=new Array;$("ul.nav-list").each(function(){if($(this).position().top!=n){e++;r[e]=new Array}r[e].push($(this));n=$(this).position().top});for(t=0;t<r.length;t++){var i=0;for(e=0;e<r[t].length;e++){var s=r[t][e];s.height()>i&&(i=s.height())}for(e=0;e<r[t].length;e++){var s=r[t][e];s.css("height",i)}}$("div.dashboard-icon-list div.well").css("visibility","visible")};$(function(){var e=$("#ccm-page-help").popover({trigger:"click",content:function(){var e=$(this).attr("id")+"-content";return $("#"+e).html()},placement:"bottom",html:!0}).click(function(e){e.stopPropagation()});$(document).click(function(){var t=e.data("popover");t&&t.hide()});$(".launch-tooltip").tooltip({placement:"bottom"});if($("#ccm-dashboard-result-message").length>0){if($(".ccm-pane").length>0){var t=$(".ccm-pane").parent().attr("class"),n=$(".ccm-pane").parent().parent().attr("class"),r=$("#ccm-dashboard-result-message").html();$("#ccm-dashboard-result-message").html('<div class="'+n+'"><div class="'+t+'">'+r+"</div></div>").fadeIn(400)}}else $("#ccm-dashboard-result-message").fadeIn(200)});