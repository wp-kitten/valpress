!function(e){var t={};function n(a){if(t[a])return t[a].exports;var i=t[a]={i:a,l:!1,exports:{}};return e[a].call(i.exports,i,i.exports,n),i.l=!0,i.exports}n.m=e,n.c=t,n.d=function(e,t,a){n.o(e,t)||Object.defineProperty(e,t,{enumerable:!0,get:a})},n.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},n.t=function(e,t){if(1&t&&(e=n(e)),8&t)return e;if(4&t&&"object"==typeof e&&e&&e.__esModule)return e;var a=Object.create(null);if(n.r(a),Object.defineProperty(a,"default",{enumerable:!0,value:e}),2&t&&"string"!=typeof e)for(var i in e)n.d(a,i,function(t){return e[t]}.bind(null,i));return a},n.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return n.d(t,"a",t),t},n.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},n.p="/",n(n.s=26)}({26:function(e,t,n){e.exports=n(27)},27:function(e,t){function n(e,t,n){return t in e?Object.defineProperty(e,t,{value:n,enumerable:!0,configurable:!0,writable:!0}):e[t]=n,e}var a=void 0!==window.AppLocale&&window.AppLocale;if(!a)throw new Error("AppLocale locale not loaded.");var i=void 0!==window.MenuLocale&&window.MenuLocale;if(!i)throw new Error("MenuLocale locale not loaded.");jQuery((function(e){"use strict";({__ACTION_ADD__:"x0000",__ACTION_REMOVE__:"x0001",__nestable:null,__btnSaveMenu:null,__btnEmptyMenu:null,__placeholder:null,__btnAddToMenu:null,__btnCustomAddToMenu:null,init:function(){this.__nestable=e(".dd"),this.__btnSaveMenu=e(".js-btn-save-menu"),this.__btnEmptyMenu=e(".js-btn-empty-menu"),this.__placeholder=e(".menu-empty"),this.__btnAddToMenu=e(".js-btn-add-to-menu"),this.__btnCustomAddToMenu=e(".js-custom-add-to-menu-button"),this.__initNestable(),this.updateSaveButtonState(this.__ACTION_ADD__,!1),this.__setupListeners()},__initNestable:function(){var t=this;this.__nestable.nestable({maxDepth:20,scroll:!0,callback:function(n,a){console.info(e(a).attr("data-id")),t.updateSaveButtonState(t.__ACTION_ADD__)}})},__hasMenuItems:function(){var e=this.__nestable.find(".dd-item");return e&&e.length>=1},__bindClickMenuItemRemove:function(){var t=this;e(".js-btn-remove",t.__nestable).off("click").on("click",(function(n){if(n.preventDefault(),n.stopPropagation(),confirm(i.confirm_delete_item)){var a=e(n.target).parents(".dd-item").first();a&&(a.addClass("js-deleted hidden"),t.updateSaveButtonState(t.__ACTION_REMOVE__,!0))}}))},__bindCollapsible:function(){e(".dd-collapse",this.__nestable).on("click",(function(t){t.stopPropagation(),t.preventDefault();var n=e(this).parents(".dd-item").first();n&&n.addClass("dd-collapsed")})),e(".dd-expand",this.__nestable).on("click",(function(t){t.stopPropagation(),t.preventDefault();var n=e(this).parents(".dd-item").first();n&&n.removeClass("dd-collapsed")}))},__resetChecked:function(t){var n=t.find(".js-check-input:checked");n&&n.length>=1&&e.each(n,(function(t,n){var a=e(n);a.is(":checked")&&a.prop("checked",!1)}))},__setupListeners:function(){var t=this;this.__hasMenuItems()&&(this.__bindClickMenuItemRemove(),this.__bindCollapsible()),this.__btnAddToMenu.on("click",(function(n){n.preventDefault();var a=e(this),o=e(".collapse.show "+a.attr("data-target"));if(void 0!==o){var s=o.find(".js-check-input:checked");e.each(s,(function(n,a){var o=e(a),s=o.val(),r=o.attr("data-type"),d=o.attr("data-menu-item-id"),l=o.attr("data-title"),u=r+s;t.__nestable.nestable("add",{id:s,selector:u,"menu-item-id":d,type:r,children:[]});var _=e('[data-id="'+s+'"]'),c=e(".dd-handle",_);e("> .dd-content",c).attr("title",l).html(l),e('<a href="#" class="js-btn-remove" title="'+i.delete_text_title+'">'+i.delete_text+"</a>").insertBefore(c)})),s.length>=1&&(t.updateSaveButtonState(t.__ACTION_ADD__),t.__bindClickMenuItemRemove(),t.__resetChecked(o))}})),this.__btnCustomAddToMenu.on("click",(function(n){n.preventDefault();var a=e("#menu-item-title").val(),o=e("#menu-item-url").val(),s=e("#menu-item-data-type").val(),r=t.__uniqueID(),d=s+r;if(!a||a.length<1)return!1;if(!o||o.length<1)return!1;t.__nestable.nestable("add",{id:r,selector:d,"menu-item-id":0,type:s,title:a,url:o,children:[]});var l=e('[data-id="'+r+'"]'),u=e(".dd-handle",l);e("> .dd-content",u).attr("title",a).html(a),e('<a href="#" class="js-btn-remove" title="'+i.delete_text_title+'">'+i.delete_text+"</a>").insertBefore(u),t.updateSaveButtonState(t.__ACTION_ADD__),t.__bindClickMenuItemRemove()})),this.__btnSaveMenu.on("click",(function(o){var s=e(this),r=t.__menuToArray(t),d=e("#menu-items-sortable .js-ajax-loader");s.addClass("no-click"),d.removeClass("hidden");var l={url:a.ajax.url,method:"POST",async:!0,timeout:29e3,data:n({action:"menu_save",menu_id:i.menu_id,menu_items:r},a.nonce_name,a.nonce_value)};e.ajax(l).done((function(e){e?e.success?e.data?showToast(e.data,"success"):showToast(a.ajax.empty_response,"warning"):e.data?showToast(e.data,"warning"):showToast(a.ajax.empty_response,"warning"):showToast(a.ajax.no_response,"warning")})).fail((function(e,t,n){showToast(n,"error")})).always((function(){t.updateSaveButtonState(t.__ACTION_ADD__),d.addClass("hidden")}))})),this.__btnEmptyMenu.on("click",(function(e){e.preventDefault(),confirm(i.confirm_empty_menu)&&(t.__nestable.html(""),t.updateSaveButtonState(t.__ACTION_REMOVE__,!0))}))},__menuToArray:function(t){var n=e(">.dd-list > .dd-item:not(.js-deleted)",t.__nestable),a={};return e.each(n,(function(n,i){var o=e(i),s=o.attr("data-selector"),r=o.attr("data-type");a[n]={id:o.attr("data-id"),type:r,selector:s,menuItemId:o.attr("data-menu-item-id"),children:[]},"custom"===r&&(a[n].title=o.attr("data-title"),a[n].url=o.attr("data-url")),a[n].children=t.__getChildren(t,o)})),a},__getChildren:function(t,n){var a=[],i=e(">.dd-list >.dd-item:not(.js-deleted)",n);return i.length>0&&e.each(i,(function(n,i){var o=e(i),s=o.attr("data-selector"),r=o.attr("data-type");a[n]={id:o.attr("data-id"),type:r,selector:s,menuItemId:o.attr("data-menu-item-id"),children:t.__getChildren(t,o)},"custom"===r&&(a[n].title=o.attr("data-title"),a[n].url=o.attr("data-url"))})),a},__uniqueID:function(){return"_"+Math.random().toString(36).substr(2,9)},updateSaveButtonState:function(e){var t=!(arguments.length>1&&void 0!==arguments[1])||arguments[1];e===this.__ACTION_ADD__&&(this.__placeholder.hide(),this.__btnSaveMenu.removeClass("hidden")),this.__hasMenuItems()||(t||this.__btnSaveMenu.addClass("hidden"),this.__placeholder.show()),t?this.__btnSaveMenu.removeClass("no-click disabled"):this.__btnSaveMenu.addClass("no-click disabled")}}).init()})),jQuery((function(e){"use strict";var t=e("#form-menu-name .js-ajax-loader");e(".js-save-menu-title-button").on("click",(function(o){o.preventDefault();var s=e(this),r=e("#form-menu-name"),d=e(".name-field",r);if(!d||!d.val().length>0)return!1;s.addClass("no-click"),t.removeClass("hidden");var l={url:a.ajax.url,method:"POST",cache:!1,timeout:29e3,data:n({action:"save_menu_name",menu_name:d.val(),menu_id:i.menu_id},a.nonce_name,a.nonce_value)};e.ajax(l).done((function(e){e?e.success?showToast(e.data,"success"):e.data?showToast(e.data,"warning"):showToast(a.ajax.empty_response,"warning"):showToast(a.ajax.no_response,"error")})).fail((function(e,t,n){showToast(n,"error")})).always((function(){s.removeClass("no-click"),t.addClass("hidden")}))}))})),jQuery((function(e){"use strict";var t=e(".form-menu-options .js-ajax-loader");e(".js-menu-save-options-button").on("click",(function(o){o.preventDefault();var s=e(this),r=e('input[name="display_as"]:checked');if(!r||!r.val().length>0)return!1;s.addClass("no-click"),t.removeClass("hidden");var d={url:a.ajax.url,method:"POST",cache:!1,timeout:29e3,data:n({action:"save_menu_options",display_as:r.val(),menu_id:i.menu_id},a.nonce_name,a.nonce_value)};e.ajax(d).done((function(e){e?e.success?showToast(i.text_options_saved,"success"):e.data?showToast(e.data,"warning"):showToast(a.ajax.empty_response,"warning"):showToast(a.ajax.no_response,"error")})).fail((function(e,t,n){showToast(n,"error")})).always((function(){s.removeClass("no-click"),t.addClass("hidden")}))}))})),jQuery((function(e){"use strict";e(".cp-menu-edit-accordion");e(".js-trigger").on("click",(function(t){t.preventDefault();var n=e(this),a=n.next(".js-sign");n.hasClass("collapsed")?a.removeClass("fa-plus").addClass("fa-minus"):a.removeClass("fa-minus").addClass("fa-plus")}))}))}});