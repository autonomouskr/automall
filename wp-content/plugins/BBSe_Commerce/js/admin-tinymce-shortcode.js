(function() {
	tinymce.create('tinymce.plugins.BbseCommerceTinymceButtons', {
          init : function(editor, url) {
				editor.addCommand("bbseCommerceTinymcePopupCreate", function(obj) {
					selectOption=obj.selectedkey;
					selectTitle=obj.title;
					jQuery("#bbse-tiny-option").remove();
					tb_show('환경설정', bbse_commerce_var.plugin_url+'admin/bbse-commerce-tinymce-option.php?selectOption='+selectOption+'&#38;selectTitle='+selectTitle+'&#38;width=640&#38;height=450&#38;modal=false&#38;TB_iframe=true');
					return false;
				});

				editor.addCommand("bbseCommerceTinymcePopupRemove", function(obj) {
						tb_remove();
						jQuery("#bbse-tiny-option").remove();
				});

				editor.addButton('bbse_commerce_shortcode_button', {
					title : "BBS e-Commerce",
					text: 'BBS e-Commerce',
					icon: false,
					type: 'menubutton',
					menu: [

						{ text : '동영상 입력',
							menu : [
								{ text: '유튜브', onclick: function(e){
									e.stopPropagation();
									tinyMCE.activeEditor.execCommand("bbseCommerceTinymcePopupCreate", {title: this.text() ,selectedkey: "youtube"});
									}
								},
							]
						},
					]
				});
          },
          createControl : function(n, cm) {
               return null;
          },
	});
	/* Start the buttons */
	tinymce.PluginManager.add( 'bbse_commerce_shortcode_script', tinymce.plugins.BbseCommerceTinymceButtons ); // functions.php 에 설정한 plugin_array 이름
})();