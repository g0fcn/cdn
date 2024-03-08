jQuery(function($){function getKeys(e){var t=[];for(var n in e){if(!e.hasOwnProperty(n)){continue}t.push(n)}return t}var last_ids=[],target,getDocHeight=function(){var D=document;return Math.max(Math.max(D.body.scrollHeight,D.documentElement.scrollHeight),Math.max(D.body.offsetHeight,D.documentElement.offsetHeight),Math.max(D.body.clientHeight,D.documentElement.clientHeight))},add_conditions_button=function($el,id,value){value=value||"";$el.find(".locations-row-links").empty().html('<input type="hidden" name="'+id+"\" value='"+value+'\' /><a href="#" class="be-cm-conditions">'+be_cm.lang.conditions+'</a> <a class="be-cm-remove" href="#"><span class="dashicons dashicons-no-alt"></span></a>');return $el};$(".menu-locations .locations-row-links").empty();function getPaginationPages(e){e.preventDefault();var $this=$(this),$originalInput=$("#be-cm-original-conditions");if($this.data("active")){return}var type=$this.data("type"),tab=$this.parents("#visibility-tabs").find(".be-visibility-type-options[data-type="+type+"]"),id=$this.parents(".be-cm-conditions-container").data("item"),selected=$(".menu-location-menus[data-item="+id+"] .locations-row-links input").val(),childCallback=function(){var $inputs=$('.be-visibility-options[data-type="'+this.dataset.type+'"] .be-visibility-items-page input');if(this.checked){$inputs.bind("change",{type:this.dataset.type},parentChanged)}else{$inputs.unbind("change",parentChanged)}},parentChanged=function(e){var $children=$('.be-visibility-options[data-type="'+e.data.type+'"] input[data-parent="'+this.dataset.slug+'"]');if($children.length){$children.prop("checked",this.checked).trigger("change")}};$.ajax({url:ajaxurl,type:"post",data:{action:"be_create_inner_page",type:type,selected:selected,original_values:$originalInput.val(),},beforeSend:function(){tab.html('<div class="tb_slider_loader"></div>')},success:function(data){data=JSON.parse(data);tab.html(data.html);if("page"===type||"category"===type){document.querySelector(".tf_cm_select_sub input[data-type="+type+"]").addEventListener("change",childCallback)}$this.data("active","on");$("#be-cm-original-conditions").val(data.original_values)}})}function showPaginationPage(e){e.preventDefault();var $this=$(this),tab=$this.parents(".be-visibility-options"),items_inner=$this.parents(".be-visibility-items-inner"),pagination=$(".be-visibility-pagination",items_inner),current_page=parseFloat($(".be-visibility-pagination .current",items_inner).text()),go_to_page=1;if($this.hasClass("next")){go_to_page=current_page+1}else{if($this.hasClass("prev")){go_to_page=current_page-1}else{if($this.hasClass("page-numbers")){go_to_page=parseFloat($this.text())}}}var inner_item=$(".be-visibility-items-inner",tab);$.ajax({url:ajaxurl,type:"post",data:{action:"be_create_page_pagination",current_page:go_to_page,num_of_pages:items_inner.data("pages"),},beforeSend:function(){$(".tb_slider_loader",tab).remove();pagination.hide();inner_item.append('<div class="tb_slider_loader"></div>')},success:function(data){$(".tb_slider_loader",tab).remove();$(".be-visibility-items-page-"+go_to_page,items_inner).removeClass("is-hidden");pagination.html(data).show()}})}function add_assignment($menu_row,new_id,selected_menu,condition_value){var clone=$menu_row.clone().removeClass("cm-location");clone.find(".menu-location-title").empty();var menu_id=clone.find("select").attr("name").match(/menu-locations\[(.*)\]/)[1];if(new_id==null){if(typeof last_ids[menu_id]=="undefined"){last_ids[menu_id]=parseInt($(getKeys(be_cm.options[menu_id])).last()[0]);if(!$.isNumeric(last_ids[menu_id])){last_ids[menu_id]=1}}new_id=last_ids[menu_id]++}clone.find("select").find('option[value="0"]').text(be_cm.lang.disable_menu).before('<option value=""></option>').end().val(selected_menu).attr("name","be_cm["+menu_id+"]["+new_id+"][menu]");clone=add_conditions_button(clone,"be_cm["+menu_id+"]["+new_id+"][condition]",condition_value);clone.insertBefore(jQuery('.menu-locations tr[data-menu="'+menu_id+'"]'));var menu_num=$(".menu-location-menus select").length,conditions_num=$(".cm-replacement-button").length+1;clone.find(".menu-location-menus").attr("data-item",menu_id+new_id);if(menu_num===conditions_num){$(".be-cm-conditions-container:first").addClass("be-cm-conditions-container-"+menu_id+new_id).data("item",menu_id+new_id)}if(menu_num>conditions_num){$(".be-cm-conditions-container:first").clone().removeClass().addClass("be-cm-conditions-container be-admin-lightbox tf_clearfix be-cm-conditions-container-"+menu_id+new_id).data("item",menu_id+new_id).insertAfter(".be-cm-conditions-container:last")}$(".lightbox_container",".be-cm-conditions-container-"+menu_id+new_id).html("")}$("body").on("click",".be-cm-conditions",function(e){e.preventDefault();var $this=$(this),toggle_text=$this.html();$this.html('<span class="tb_slider_loader_wrapper"><span class="tb_slider_loader"></span></span>'+toggle_text);target=$(this).prev();var top=$(document).scrollTop()+80,$lightbox=$(".be-cm-conditions-container-"+$(this).parents(".menu-location-menus").data("item"));
if(!$lightbox.hasClass("is-loaded")){$.ajax({"type":"POST",url:ajaxurl,data:{action:"be_cm_get_conditions",selected:target.val(),},success:function(data){$(".tb_slider_loader_wrapper",$this).remove();$("#be-cm-overlay").show();$(".lightbox_container",$lightbox).append(data);$lightbox.show().css("top",getDocHeight()).animate({"top":top},800);$("#visibility-tabs",$lightbox).tabs();$("#visibility-tabs .be-visibility-inner-tabs",$lightbox).tabs()}})}else{$(".tb_slider_loader_wrapper",$this).remove();$("#be-cm-overlay").show();$lightbox.show().css("top",getDocHeight()).animate({"top":top},800);$("#visibility-tabs",$lightbox).tabs();$("#visibility-tabs .be-visibility-inner-tabs",$lightbox).tabs()}return false}).on("click","#visibility-tabs .be-visibility-tab",getPaginationPages).on("click",".be-visibility-pagination a.page-numbers",showPaginationPage).on("click",".be-cm-close, #be-cm-overlay",function(e){e.preventDefault();var lightbox=$(this).parents(".be-cm-conditions-container");lightbox.animate({"top":getDocHeight()},800,function(){$("#be-cm-overlay").hide();lightbox.hide().addClass("is-loaded")});return false}).on("click",".be-mc-add-assignment",function(e){add_assignment($("#locations-"+$(this).closest("tr").attr("data-menu")).closest("tr"));return false}).on("click",".be-cm-save",function(){var $lightbox=$(this).parents(".be-cm-conditions-container"),id=$lightbox.data("item"),data=$("form",$lightbox).serialize(),originalValues=$("#be-cm-original-conditions",$lightbox).val();originalValues=""===originalValues?[]:originalValues.split("&");var selectedData=""===data?[]:data.split("&");data=originalValues.concat(selectedData).concat();for(var i=0;i<data.length;++i){for(var j=i+1;j<data.length;++j){if(data[i]===data[j]){data.splice(j--,1)}}}data=data.join("&");target.val(data);$(".menu-location-menus[data-item="+id+"]").val(data);$(".be-cm-close",$lightbox).click();return false}).on("click",".be-cm-remove",function(){$(this).closest("tr").fadeOut(function(){$(this).remove()});var item=$(this).closest(".menu-location-menus").data("item");if($(".menu-location-menus[data-item]").length>1){$(".be-cm-conditions-container-"+item).remove()}else{$(".be-cm-conditions-container").removeClass().addClass("be-cm-conditions-container be-admin-lightbox tf_clearfix")}return false}).on("click",".be-cm-conditions-container .uncheck-all",function(){$(this).before('<span class="tb_slider_loader_wrapper"><span class="tb_slider_loader"></span></span>');var lightbox=$(this).parents(".be-cm-conditions-container"),id=lightbox.data("item");$(".menu-location-menus[data-item="+id+"] .locations-row-links input[type=hidden]").val("");$("#be-cm-original-conditions",lightbox).val("");$("input:checkbox",lightbox).removeAttr("checked");$(this).parent().find(".tb_slider_loader_wrapper").remove();$(".be_all_condition_checked",lightbox).removeClass("be_all_condition_checked");return false}).on("click",".be-cm-conditions-container .be_apply_all_conditions",function(){var $this=$(this),$innerTab=$this.parents(".be-visibility-inner-tab").find(".be-visibility-items-inner");if($this.prop("checked")){$innerTab.addClass("be_all_condition_checked")}else{$innerTab.removeClass("be_all_condition_checked")}});$.each(be_cm.nav_menus,function(i,v){$("#locations-"+v).closest("tr").after('<tr class="cm-replacement-button" data-menu="'+v+'"><td>&nbsp;</td><td><a href="#" class="be-mc-add-assignment">'+be_cm.lang.add_assignment+"</a></td></tr>")});$.each(be_cm.options,function(menu,assignments){if(typeof assignments=="object"){$.each(assignments,function(id,value){add_assignment($("#locations-"+menu).closest("tr"),id,value["menu"],value["condition"]);last_ids[menu]=++id})}});$("#be-cm-about").appendTo("p.button-controls")});