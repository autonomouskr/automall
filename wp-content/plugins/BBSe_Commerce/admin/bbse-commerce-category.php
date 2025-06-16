<script type="text/javascript">
	function add_init(){
	
		jQuery("#category_name").val("");
		jQuery("#tIdx").val("");
		jQuery('input:radio[name=use_category]:radio[value=Y]').attr("checked", true);
		jQuery('input:radio[name=tMode]:radio[value=insert]').attr("checked", true);
		jQuery('#tMode_modify').css("display", "none");
		jQuery('#category_delete').css("display", "none");
		jQuery("#category_name").focus();
		jQuery("#user_class").val("");
	}


	// 카테고리 추가
	function add_input(tMode){
	
		if(!tMode){
			var tMode=jQuery(":input:radio[name=tMode]:checked").val();
		}
		var tIdx=jQuery("#tIdx").val();

		var cName=jQuery("#category_name").val();
		var jData=jQuery("#nestable-output").val();
		var cUse=jQuery(":input:radio[name=use_category]:checked").val();
		var pattern = /\"/;
		
		var cUClass=jQuery("#user_class").val();
		
		
		if(pattern.test(cName)){
			alert('카테고리명에 큰따옴표(")는 사용하실 수 없습니다.     ');
			jQuery("#category_name").focus();
			return;
		}
		
		if( tMode != 'delete' && (cUClass == null | cUClass == "")){
			alert('회원등급 입력은 필수 입니다..     ');
			jQuery("#user_class").focus();
			return;
		}

		if(tMode=='delete'&& !tIdx){
			alert('삭제를 원하시는 카테고리를 선택해 주세요.     ');
			jQuery("#category_name").focus();
			return;
		}
		else if(!cName && !cName) {
			if(tMode=='insert') alert('추가하실 카테고리명을 입력해 주세요.     ');
			else if(tMode=='modify') alert('수정하실 카테고리명을 입력해 주세요.     ');
			jQuery("#category_name").focus();
			return;
		}
		
		if(tMode=='delete'){
			rank_input('rank-init');
			if(!confirm('해당 카테고리를 삭제하시겠습니까??\n하위 카테고리도 함께 삭제 됩니다.    ')) return;;
		}
		
		jQuery.ajax({
			type: 'post', 
			async: false, 
			//url: '<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>admin/proc/bbse-commerce-category.exec.php', 
			url: 'http://localhost/wp-content/plugins/BBSe_Commerce/admin/proc/bbse-commerce-category.exec.php',
			data: {tMode:tMode, tIdx:tIdx, cUse:cUse, cName:cName, cUClass:cUClass, jData:jData}, 
			success: function(data){
			
				//alert(data);
				var result = data.split("|||"); 

				if(result['0'] == "success"){
					if(tMode=='insert'){
						if(!jData || jData=="[]") jQuery('#nestable > ol').html(result['1'])
						else jQuery('#nestable > ol').append(result['1'])
						jQuery("#nestable-output").val(result['2']);
					}
					else if(tMode=='modify'){
						jQuery("[data-id='"+tIdx+"']  .dd-handle:first").html(cName);
						jQuery("[data-id='"+tIdx+"']  .select_btn:first").html(result['1']);
					}
					else if(tMode=='delete'){
						jQuery("[data-id='"+tIdx+"']").remove();
						jQuery("#nestable-output").val(result['1']);
						if(!result['1'] || result['1']=="[]"){
							jQuery('#nestable > ol').html("<div class=\"dd-none-list\">카테고리를 등록해 주세요.</div>")
						}
					}
					add_init();
				}
				else if(result['0'] == "dbError"){
					alert("[Error] DB 오류 입니다.   ");
					jQuery("#category_name").focus();					
				}
				else{
					alert("서버와의 통신이 실패했습니다.   ");
				}
			}, 
			error: function(data, status, err){
				alert("서버와의 통신이 실패했습니다.   ");
			}
		});	
	}

	// 정렬순서 저장
	function rank_input(tMode){
		jQuery("#tIdx").val("");

		var jData=jQuery("#nestable-output").val();

		if(jData && jData!="[]") {
			jQuery.ajax({
				type: 'post', 
				async: false, 
				url: '<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>admin/proc/bbse-commerce-category.exec.php', 
				data: {tMode:tMode, jData:jData}, 
				success: function(data){
					//alert(data);
					var result = data.split("|||"); 

					if(tMode=='rank'){
						if(result['0'] == "success"){
							alert("카테고리 순서를 정상적으로 저장하였습니다.   ");
							add_init();
						}
						else if(result['0'] == "dbError"){
							alert("[Error] DB 오류 입니다.   ");
						}
						else{
							alert("서버와의 통신이 실패했습니다.   ");
						}
					}
				}, 
				error: function(data, status, err){
					if(tMode=='rank'){
						alert("서버와의 통신이 실패했습니다.   ");
					}
				}
			});	
		}
	}

	// 카테고리 선택
	function select_input(tIdx,tName,tUse,tUClass){
	
		jQuery("#tIdx").val(tIdx);
		jQuery("#category_name").val(tName);
		jQuery('input:radio[name=use_category]').prop("checked", false);
		jQuery('input:radio[name=use_category]:radio[value='+tUse+']').prop("checked", true);
		jQuery("#user_class").val(tUClass);

		jQuery('#tMode_modify').css("display", "");
		jQuery('input:radio[name=tMode]:radio[value=modify]').attr("checked", true);
		jQuery('#category_delete').css("display", "");
	}
</script>
<div class="wrap">
	<div style="margin-bottom:30px;">
		<h2>상품 카테고리 관리</h2>
		<hr>
	</div>
	<div style="float:left;width:32%;">
		<input type="hidden" name="tIdx" id="tIdx" value="">
		<table class="dataTbls overWhite collapse">
			<colgroup><col width=""><col width=""></colgroup>
			<tr>
				<th colspan="2" style="font-size:14px;">카테고리 등록/수정<img src="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>images/left_refresh.png" onclick="add_init()" width="18" height="18" alt="초기화" title="초기화" style="float:right;cursor:pointer;margin:8px 10px 0 0;"></th>
			</tr>
			<tr>
				<th style="width:120px;">카테고리명</th>
				<td><input type="text" name="category_name" id="category_name" style="width:90%;" value=""></td>
			</tr>
			<tr>
				<th>노출여부</th>
				<td>
					<input type="radio" name="use_category" id="use_category" value="Y" checked style="border:0px;" /> 표시함 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<input type="radio" name="use_category" id="use_category" value="N" style="border:0px;" /> 표시안함
				</td>
			</tr>
			<tr>
				<th>신규등록/수정</th>
				<td>
					<span id="tMode_insert"><input type="radio" name="tMode" id="tMode" value="insert" checked style="border:0px;" /> 신규등록</span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<span id="tMode_modify" style="display:none;"><input type="radio" name="tMode" id="tMode" value="modify" style="border:0px;" /> 수정</span>
				</td>
			</tr>
    		<tr>
    			<th>회원등급</th>
    			<td>
    				<select id="user_class" name="user_class" style="width:150px;">
    				<?php
    					$mclass_rlt = $wpdb->get_results("SELECT * FROM bbse_commerce_membership_class ORDER BY no ASC");
    					foreach($mclass_rlt as $i => $mclass){
    						echo '<option value="'.$mclass->no.'"'.(($result->user_class == $mclass->no || ($_REQUEST['mode']=="add" && $mclass->no==2))? ' selected' : '').'>'.$mclass->class_name.'</option>';
    					}
    				?>
    				</select>
    			</td>
    		</tr>
		</table>
		<table width="100%">
			<tr>
				<td style="font-size:14px;text-align:center;height:80px;">
					<button type="button" class="button-bbse blue" onclick="add_input('');">설정저장</button> &nbsp; <button type="button" class="button-bbse red" id="category_delete" onclick="add_input('delete');" style="display:none;">분류삭제</button>
				</td>
			</tr>
		</table>

	</div>
	<div style="padding-left:3%;float:left;width:65%;">

		<div class="cf category-lists">

		<?php
			$cCnt = $wpdb->get_var("SELECT count(*) FROM `bbse_commerce_category` WHERE `idx`='1'");
			if($cCnt>'0'){
				$nData = $wpdb->get_row("SELECT * FROM `bbse_commerce_category` WHERE `idx`='1'");
		?>
			<div class="dd" id="none-category">
				<ol class="dd-list">
					<li>
						<table class="dataTbls overWhite collapse">
							<colgroup><col width="">
							<tr>
								<th style="font-size:14px;text-align:center;">상품 카테고리 정렬</th>
							</tr>
						</table>

						<div style="margin-top:20px;">
							<button type="button" class="button-bbse blue" onclick="rank_input('rank');">순서저장</button>
						</div>
					</li>
					<li style="margin-left:100px;text-align:right;">
						<button type="button" class="button-small blue" onclick="jQuery('#nestable').nestable('expandAll');" title="전체펼침">+</button>
						<button type="button" class="button-small red" onclick="jQuery('#nestable').nestable('collapseAll');" title="전체숨김">-</button>
					</li>
					<li class="dd-item" data-id="<?php echo $nData->idx;?>">
						<div class="dd-handle"><?php echo $nData->c_name;?></div>
						<div class="select_btn">
							<a href="<?php echo esc_url( home_url( '/' ) ); ?>?bbseCat=<?php echo $nData->idx;?>" target="_blank">
							<img src="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL;?>images/left_icon4.png" width="18" height="18" alt="<?php echo $nData->c_name;?> 카테고리 미리보기" title="<?php echo $nData->c_name;?> 카테고리 미리보기"></a>
						</div>
					</li>
				</ol>
			</div>
		<?php }?>

			<div class="dd" id="nestable">
				<ol class="dd-list">
				<?php
					$cCnt = $wpdb->get_var("SELECT count(*) FROM `bbse_commerce_category` WHERE `idx`>'1'");
					if($cCnt>'0') echo bbse_commerce_get_category_markup();
					else echo "<div class=\"dd-none-list\">카테고리를 등록해 주세요.</div>";
				?>
				</ol>
			</div>
		</div>

		<textarea id="nestable-output" style="display:none;"></textarea>
	</div>

</div>

<script>

jQuery(document).ready(function(){

	jQuery('#category_name').keyup(function(e) {
		if (e.keyCode == 13) add_input('');       
	});

    var updateOutput = function(e){
        var list   = e.length ? e : jQuery(e.target),
            output = list.data('output');
        if (window.JSON) {
            output.val(window.JSON.stringify(list.nestable('serialize')));//, null, 2));
        } else {
            output.val('브라우저에서 JSON을 지원하지 않습니다.    ');
        }
    };

    // activate Nestable for list 1
    jQuery('#nestable').nestable({
        group: 1,
		maxDepth :3
    })
    .on('change', updateOutput);
    
    // output initial serialised data
    updateOutput(jQuery('#nestable').data('output', jQuery('#nestable-output')));

	jQuery('#nestable').nestable('collapseAll'); // expandAll / collapseAll
});
</script>
