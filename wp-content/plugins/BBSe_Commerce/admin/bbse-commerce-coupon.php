<?php
global $wpdb;
$sel = $_GET['sel'];
$title = array(
    'add' => '온라인쿠폰등록',
    'paper' => '오프라인(종이)쿠폰',
    'paper_add' => '오프라인(종이)쿠폰등록',
    'use_list' => '쿠폰사용내역'
);

?>

<div class="wrap">
	<script language="javascript">
	jQuery(document).ready(function($) {
		$('.datepicker').datepicker({
			dateFormat: 'yy-mm-dd',
		});
		$('input[name="coupon_alldate"]').change(function(){
			if($(this).is(":checked") == true){
				$('.datepicker').attr("readonly",true); 
				$('input[name="coupon_sdate"]').val('');
				$('input[name="coupon_edate"]').val('');
			}
			else{
				$('.datepicker').removeAttr("readonly");
			}
		});
		$('input[name="coupon_product_type"]').change(function(){
			if($(this).val() == 'all'){
				$('.product_wrap').addClass('hide');
			}
			else{
				$('.product_wrap').removeClass('hide');
			}
		});
		$(document).on('click','.del_product',function(){
			$(this).parent().parent().remove();
		});
	});
	function checkAll(){
		if(jQuery("#check_all").is(":checked")) jQuery("input[name=check\\[\\]]").attr("checked",true);
		else jQuery("input[name=check\\[\\]]").attr("checked",false);
	}
	function goods_list_popup(pTarget,tNo){
		var popupTitle="쿠폰적용상품선택"
		var chkList="";

		var tCnt=jQuery("input[name='product_id[]']").size();

		for(i=0;i<tCnt;i++){
			if(chkList) chkList +=",";
			chkList +=jQuery("input[name='product_id[]']").eq(i).val();
		}
		var tbHeight = window.innerHeight * .85;
		tb_show("상품조회 ("+popupTitle+")", "<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL.'admin/'; ?>bbse-commerce-coupon-goods_popup.php?height="+tbHeight+"&tNo=&chkList="+chkList+"&TB_iframe=true");
		//thickbox_resize();
		return false;
	}
	// 상품 등록 팝업 닫기
	function remove_popup(){
		tb_remove();
	}
	
	function user_list(){
	
		var popupTitle="회원추가";
		var userClass = jQuery("select[name=user_class\\[\\]]").val();
		var tCnt=jQuery("input[name=coupon_user_list\\[\\]]").size();
		var chkList="";
		
		for(i=0;i<tCnt;i++){
			if(chkList) chkList +=",";
			chkList +=jQuery("input[name=coupon_user_list\\[\\]]").eq(i).val();
		}

		var tbHeight = window.innerHeight * .60;
		var tbWidth = window.innerWidth * .35;
		tb_show(popupTitle, "<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL ?>admin/bbse-commerce-coupon-userInfo-popup.php?popupTitle="+popupTitle+"&#38;height="+tbHeight+"&#38;width="+tbWidth+"&#38;userClass="+userClass+"&#38;chkList="+chkList+"&#38;TB_iframe=true");
		//tb_show(popupTitle, "http://localhost/wp-content/plugins/BBSe_Commerce/admin/bbse-commerce-coupon-userInfo-popup.php?popupTitle="+popupTitle+"&#38;height="+tbHeight+"&#38;width="+tbWidth+"&#38;userClass="+userClass+"&#38;chkList="+chkList+"&#38;TB_iframe=true");
		//thickbox_resize();
		return false;
	}
	
			// 상품 이미지 삭제 시
	function goods_user_remove(userId){
		jQuery("#user_info_list_"+userId).remove();
	}
</script>
	<div style="margin-bottom: 30px;">
		<h2><?php echo (empty($title[$sel]) ? '온라인쿠폰':$title[$sel]); ?>
			<?php
if (empty($sel)) :
    ?>
			<form method="get"
				style="display: inline-block; vertical-align: middle;">
				<input type="hidden" name="page" value="bbse_commerce_coupon" /> <input
					type="hidden" name="sel" value="add" /> <input
					style="color: #FFFFFF; background-color: #137caa; border-color: #4c99ba !important;"
					type="submit" class="button-fill blue" style="margin-left:50px;"
					value="쿠폰등록">
			</form>
			<?php
 elseif ($sel == 'paper') :
    ?>
			<form method="get"
				style="display: inline-block; vertical-align: middle;">
				<input type="hidden" name="page" value="bbse_commerce_coupon" /> <input
					type="hidden" name="sel" value="paper_add" /> <input
					style="color: #FFFFFF; background-color: #137caa; border-color: #4c99ba !important;"
					type="submit" class="button-fill blue" style="margin-left:50px;"
					value="종이쿠폰등록">
			</form>
			<?php
endif;
?>
		</h2>
	</div>

	<div class="clearfix"></div>

	<div class="tabWrap">
		<ul class="tabList">
			<li
				class="<?php echo ((empty($sel) || $sel == 'add') ? 'active':''); ?>"
				style="width: 180px;"><a href="admin.php?page=bbse_commerce_coupon">온라인쿠폰</a></li>
			<li
				class="<?php echo (in_array($sel, array('paper','paper_add')) ? 'active':''); ?>"
				style="width: 180px;"><a
				href="admin.php?page=bbse_commerce_coupon&sel=paper">오프라인(종이)쿠폰</a></li>
			<li
				class="<?php echo (in_array($sel, array('use_list')) ? 'active':''); ?>"
				style="width: 180px;"><a
				href="admin.php?page=bbse_commerce_coupon&sel=use_list">쿠폰사용내역</a></li>
		</ul>
	</div>

	<div class="clearfix"></div>
<?php

// 쿠폰목록
if (empty($sel)) {
    // 일괄삭제
    if ($_POST) {
        foreach ($_POST['check'] as $key => $value) {
            $wpdb->query('
					DELETE FROM bbse_commerce_coupon
					WHERE idx = "' . $value . '"
				');
        }
        echo '
				<script>
					location.href = "/wp-admin/admin.php?page=bbse_commerce_coupon";
				</script>
			';
        die();
    }
    $coupons = $wpdb->get_results('
			SELECT	*
			FROM	bbse_commerce_coupon
			ORDER BY idx DESC
			LIMIT	0,20
		');
    $total = $wpdb->get_var('
			SELECT	COUNT(*)
			FROM	bbse_commerce_coupon
		');
    ?>
	<style>
td {
	text-align: center;
}
</style>
	<div style="margin-top: 20px; position: relative; padding: 40px 0 0;">
		<form name="couponFrm" id="couponFrm" method="post">
			<div style="position: absolute; left: 0; top: 0;">
				<input type="submit" value="일괄삭제" class="button" />
			</div>
			<div style="width: 100%;">
				<table class="dataTbls normal-line-height collapse">
					<colgroup>
						<col width="">
						<col width="">
						<col width="">
						<col width="180px;">
						<col width="">
						<col width="160px">
						<col width="">
						<col width="">
						<col width="">
					</colgroup>
					<tr>
						<th><input type="checkbox" name="check_all" id="check_all"
							onClick="checkAll();"></th>
						<th>번호</th>
						<th>쿠폰이미지</th>
						<th>쿠폰명</th>
						<th>기간</th>
						<th>할인금액</th>
						<th>Action</th>
					</tr>
<?php
    $index = 0;
    foreach ($coupons as $key => $value) {
        ?>
					<tr>
						<td style="text-align: center;"><input type="checkbox"
							name="check[]" id="check[]" value="<?php echo $value->idx; ?>"></td>
						<td><?php echo intval($total - ($index++)); ?></td>
						<td><img style="max-height: 60px;"
							src="<?php echo (empty($value->thumb) ? '':$value->thumb); ?>" /></td>

						<td><?php echo $value->name; ?></td>
						<td><?php echo ($value->alldate == 'on' ? '모든 기간':$value->sdate.' ~ '.$value->edate); ?></td>
						<td><?php echo $value->discount.$value->discount_type; ?></td>

						<td><a
							href="<?php echo add_query_arg(array('page'=> 'bbse_commerce_coupon','sel'=>'add','idx'=>$value->idx)); ?>"
							class="button">수정</a> <a
							href="<?php echo add_query_arg(array('page'=> 'bbse_commerce_coupon','sel'=>'add','idx'=>$value->idx,'del'=>'on')); ?>"
							class="button">삭제</a></td>
					</tr>
<?php
    }
    ?>
					
				</table>
			</div>
		</form>
	</div>
<?php
} // 쿠폰등록
else if ($sel == 'add') {

    // 삭제
    if ($_GET['del'] == 'on') {
        $wpdb->query('
				DELETE FROM bbse_commerce_coupon
				WHERE idx = "' . $_GET['idx'] . '"
			');
        echo '
				<script>
					alert("삭제되었습니다.");
					location.href = "/wp-admin/admin.php?page=bbse_commerce_coupon";
				</script>
			';
        die();
    }
    // DB저장
    if ($_POST) {
        $userCnt = sizeof($_POST['coupon_user_list']);
        $userArr['user_ids'] = $_POST['coupon_user_list'];
        if ($userCnt > 0) {
            $user_ids = serialize($userArr['user_ids']);
        } else {
            $user_ids = "";
        }

        $img = $_POST['coupon_img_url'];
        if (! function_exists('wp_handle_upload')) {
            require_once (ABSPATH . 'wp-admin/includes/file.php');
        }
        $upload_overrides = array(
            'test_form' => false
        );
        if (! empty($_FILES['coupon_img']['name'])) {
            $uploadedfile = $_FILES['coupon_img'];
            $movefile = wp_handle_upload($uploadedfile, $upload_overrides);
            if ($movefile && ! isset($movefile['error'])) {
                $img = $movefile['url'];
            } else {
                $img = $movefile['error'];
            }
        }
        $product = '"' . implode('","', $_POST['product_id']) . '"';
        $cert_time = current_time('timestamp');
        $wpdb->replace('bbse_commerce_coupon', array(
            'idx' => $_POST['idx'],
            'name' => $_POST['coupon_name'],
            'sdate' => $_POST['coupon_sdate'],
            'edate' => $_POST['coupon_edate'],
            'alldate' => $_POST['coupon_alldate'],

            'min_money' => $_POST['coupon_min_money'],
            'discount_type' => $_POST['coupon_type'],
            'discount_sel' => $_POST['coupon_sel'],
            'discount' => $_POST['coupon_discount'],

            'user_class' => $_POST['user_class'][0],

            'user_ids' => $user_ids,

            'product_type' => $_POST['coupon_product_type'],
            'product' => $product,
            'thumb' => $img,
            'create_date' => $cert_time
        ));
        echo '
				<script>
					location.href = "/wp-admin/admin.php?page=bbse_commerce_coupon";
				</script>
			';
        die();
    }
    // 수정
    $coupon = $wpdb->get_row('
			SELECT *
			FROM bbse_commerce_coupon
			WHERE idx= "' . $_GET['idx'] . '"
		');
    $products = explode(',', str_replace('"', '', $coupon->product));
    ?>
	<div style="margin-top: 20px;">
		<form name="couponFrm" id="couponFrm" enctype="multipart/form-data"
			method="post">
			<input type="hidden" name="idx" value="<?php echo $coupon->idx; ?>" />
			<div style="width: 100%;">
				<table class="dataTbls normal-line-height collapse">
					<tbody>
						<tr>
							<th style="width: 150px;">쿠폰명</th>
							<td><input type="text" name="coupon_name"
								value="<?php echo $coupon->name; ?>" /></td>
						</tr>
						<tr>
							<th>쿠폰이미지</th>
							<td><input type="file" name="coupon_img" value="" /> <input
								type="hidden" name="coupon_img_url"
								value="<?php echo $coupon->thumb; ?>" />
								<?php
    if (! empty($coupon->thumb)) {
        echo '<img style="max-height: 100px;" src="' . $coupon->thumb . '" />';
    }
    ?>
							</td>
						</tr>
						<tr>
							<th>쿠폰사용기간</th>
							<td>
<!-- 							<input type="checkbox" name="coupon_alldate" value="on" -->
								<!-- <?php echo ($coupon->alldate == 'on' ? 'checked':''); ?> -->
<!-- 								기간설정안함  -->
								<input type="text" name="coupon_sdate" class="datepicker"
								value="<?php echo $coupon->sdate; ?>" /> ~ <input type="text"
								name="coupon_edate" class="datepicker"
								value="<?php echo $coupon->edate; ?>" /></td>
						</tr>
						<tr>
							<th style="line-height: 19px;">최소결제금액(설정한 금액 이상 주문 시 사용가능)</th>
							<td><input type="text" name="coupon_min_money"
								value="<?php echo $coupon->min_money; ?>" /></td>
						</tr>
						<tr>
							<th>할인적용대상</th>
							<td><select id="user_class" name="user_class[]"
								style="width: 150px;">
                				<?php
    if ($coupon) {
        $ucArr = $wpdb->get_results("SELECT * FROM bbse_commerce_membership_class where no = '" . $coupon->user_class . "' ORDER BY no ASC");
        $class_name = $ucArr[0]->class_name;
        echo '<option value="' . $coupon->user_class . '">' . $class_name . '</option>';
    } else {
        $mclass_rlt = $wpdb->get_results("SELECT * FROM bbse_commerce_membership_class ORDER BY no ASC");
        foreach ($mclass_rlt as $i => $mclass) {
            echo '<option value="' . $mclass->no . '"' . (($coupon->user_class == $mclass->no || ($_REQUEST['mode'] == "add" && $mclass->no == 2)) ? ' selected' : '') . '>' . $mclass->class_name . '</option>';
        }
    }
    ?>
                				</select>
								<button type="button" class="button-small green"
									onClick="user_list();" style="height: 30px;">회원추가</button>
								<ul id="goods-user-list">
                					<?php
    $query = "SELECT * FROM bbse_commerce_coupon where user_class  = '" . $coupon->user_class . "' and idx = '" . $_REQUEST['idx'] . "'";
    $mclass_rlt = $wpdb->get_results($query);

    $users = unserialize($mclass_rlt['0']->user_ids);

    if (sizeof($users) > '0') {
        foreach ($users as $i => $user) {
            $userName = $wpdb->get_results("select name from bbse_commerce_membership where user_id = '" . $user . "'");
            ?>
                        				<li id="user_info_list_<?php echo $user;?>"
										name="user_info_list[]"><input style="float: left"
										; type="text" name="coupon_user_list[]"
										value="<?php echo $userName[0]->name; ?>" disabled="disabled" />
										<img style="float: left"
										; src="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>images/btn_delete.png"
										onClick="goods_user_remove('<?php echo $user; ?>')"
										class="deleteBtn" alt="<?php echo $user; ?>"
										title="<?php echo $user; ?>" /></span>
										<div class="goodsname">
											<input type="hidden" name="coupon_user_list[]"
												value="<?php echo $user; ?>" />
										</div></li>
                        				
                    					<?php
        }
    }
    ?>
                				</ul></td>
						</tr>
						<tr>
							<th>할인적용기준</th>
							<td><select name="coupon_sel" style="width: 100px;">
									<option
										<?php echo ($coupon->discount_sel == 'order' ? 'selected':''); ?>
										value="order">주문금액 할인</option>
									<option
										<?php echo ($coupon->discount_sel == 'product' ? 'selected':''); ?>
										value="product">상품금액 할인</option>
							</select></td>
						</tr>
						<tr>
							<th>할인금액</th>
							<td><span>할인구분</span>&nbsp;&nbsp; <select name="coupon_type"
								style="width: 50px;">
									<option
										<?php echo ($coupon->discount_type == '%' ? 'selected':''); ?>>%</option>
									<option
										<?php echo ($coupon->discount_type == '원' ? 'selected':''); ?>>원</option>
							</select> <span>할인액</span>&nbsp;&nbsp;<input type="text"
								name="coupon_discount" value="<?php echo $coupon->discount; ?>" />
							</td>
						</tr>
						<tr>
							<th>상품선택</th>
							<td><input type="radio" name="coupon_product_type" value="all"
								<?php echo ($coupon->product_type == 'all' ? 'checked':''); ?> />
								전체상품 <input type="radio" name="coupon_product_type"
								value="noall"
								<?php echo ($coupon->product_type == 'noall' ? 'checked':''); ?> />
								특정상품 <input type="hidden" name="coupon_product" value="" />
								<div
									class="product_wrap <?php echo ($coupon->product_type == 'noall' ? '':'hide'); ?>"
									style="border: 1px solid #ededed; border-radius: 4px; padding: 20px 25px; margin: 5px 0 0;">
									<button type="button" class="button-small green"
										onClick="goods_list_popup('','');" style="height: 30px;">상품추가</button>
									<table style="margin: 10px 0 0;"
										class="dataTbls normal-line-height collapse">
										<thead>
											<tr>
												<th>상품이미지</th>
												<th>상품명</th>
												<th>Action</th>
											</tr>
										</thead>
										<tbody>
											<?php
    foreach ($products as $key => $value) {
        $p = $wpdb->get_row('
														SELECT	goods_name,goods_basic_img,goods_add_img
														FROM	bbse_commerce_goods
														WHERE	idx = "' . $value . '"
													');
        $basicImg = '';
        if ($p->goods_basic_img)
            $basicImg = wp_get_attachment_image_src($p->goods_basic_img);
        else {
            $imageList = explode(",", $p->goods_add_img);
            if (sizeof($imageList) > '0')
                $basicImg = wp_get_attachment_image_src($imageList['0']);
            else
                $basicImg['0'] = BBSE_COMMERCE_PLUGIN_WEB_URL . "images/image_not_exist.jpg";
        }
        echo '
														<tr>
															<td><img src="' . $basicImg[0] . '" /></td>
															<td>' . $p->goods_name . '</td>
															<td>
																<input type="hidden" name="product_id[]" value="' . $value . '" />
																<button class="del_product button">삭제</button>
															</td>
														</tr>
													';
    }
    ?>
										</tbody>
									</table>
								</div></td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="btn_wrap" style="margin: 10px 0 0">
				<input type="submit" value="저장하기"
					class="button button-primary button-large" />
			</div>
		</form>
	</div>
<?php
} // 종이쿠폰
else if ($sel == 'paper') {
    // 일괄삭제
    if ($_POST) {
        foreach ($_POST['check'] as $key => $value) {
            $wpdb->query('
					DELETE FROM bbse_commerce_paper_coupon
					WHERE idx = "' . $value . '"
				');
        }
        echo '
				<script>
					location.href = "/wp-admin/admin.php?page=bbse_commerce_coupon&sel=paper";
				</script>
			';
        die();
    }
    // 엑셀업로드
    if ($_FILES) {
        require_once (BBSE_COMMERCE_PLUGIN_ABS_PATH . "lib/excel/PHPExcel.php");

        // -- 읽을 범위 필터 설정 (아래는 A열만 읽어오도록 설정함 => 속도를 증가시키기 위해)
        class MyReadFilter implements PHPExcel_Reader_IReadFilter
        {

            public function readCell($column, $row, $worksheetName = '')
            {
                if (in_array($column, range('A', 'C'))) {
                    return true;
                }
                return false;
            }
        }

        $gb_upfile_name = $_FILES['excel_upload']['name'];
        $gb_upfile_pathinfo = pathinfo($gb_upfile_name);
        $gb_upfile_ext = strtolower($gb_upfile_pathinfo["extension"]);

        if ($gb_upfile_ext != "xls" && $gb_upfile_ext != "xlsx") {
            echo '
					<script>
						alert("엑셀파일만 업로드 가능합니다.(xls,xlsx 확장자)");
						location.href = "/wp-admin/admin.php?page=bbse_commerce_coupon&sel=paper";
					</script>
				';
            die();
        }
        $filterSubset = new MyReadFilter();
        $upfile_path = '';
        if (! function_exists('wp_handle_upload')) {
            require_once (ABSPATH . 'wp-admin/includes/file.php');
        }
        $upload_overrides = array(
            'test_form' => false
        );
        if (! empty($_FILES['excel_upload']['name'])) {
            $uploadedfile = $_FILES['excel_upload'];
            $movefile = wp_handle_upload($uploadedfile, $upload_overrides);
            if ($movefile && ! isset($movefile['error'])) {
                $upfile_path = $movefile['file'];
            } else {
                // 업로드 실패
                echo '
						<script>
							alert("업로드 실패하였습니다. 관리자에게 문의하세요.");
							location.href = "/wp-admin/admin.php?page=bbse_commerce_coupon&sel=paper";
						</script>
					';
                die();
            }
        }
        // 파일 타입 설정 (확장자에 따른 구분)
        $inputFileType = 'Excel2007';
        if ($gb_upfile_ext == "xls") {
            $inputFileType = 'Excel5';
        }

        // 엑셀리더 초기화
        $objReader = PHPExcel_IOFactory::createReader($inputFileType);
        $objReader->setReadDataOnly(true);
        $objReader->setReadFilter($filterSubset);
        $objPHPExcel = $objReader->load($upfile_path);
        $objPHPExcel->setActiveSheetIndex(0);
        $objWorksheet = $objPHPExcel->getActiveSheet();
        $sheetData = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
        $total_rows = count($sheetData);

        $index = 0;
        // 엑셀에서 GET 데이터
        foreach ($sheetData as $rows) {
            if (++ $index == 1)
                continue;
            $wpdb->insert('bbse_commerce_paper_coupon', array(
                'code' => $rows['A'],
                'min_money' => $rows['B'],
                'discount' => $rows['C']
            ));
        } // 엑셀반복문 끝
        echo '
				<script>
					alert("업로드되었습니다.");
					location.href = "/wp-admin/admin.php?page=bbse_commerce_coupon&sel=paper";
				</script>
			';
        die();
    }
    $coupons = $wpdb->get_results('
			SELECT	*
			FROM	bbse_commerce_paper_coupon
			ORDER BY idx DESC
			LIMIT	0,20
		');
    $total = $wpdb->get_var('
			SELECT	COUNT(*)
			FROM	bbse_commerce_paper_coupon
		');
    ?>
	<style>
td {
	text-align: center;
}
</style>
	<div style="margin-top: 20px; position: relative;">
		<form method="post" enctype="multipart/form-data">
			<div style="text-align: right; margin: 0 0 10px;">
				<a
					href="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL.'/admin/coupon_upload.xlsx'; ?>">업로드파일
					다운로드</a> <input type="file" name="excel_upload" /> <input
					type="submit" value="엑셀업로드" class="button" />
			</div>
		</form>
		<form name="couponFrm" id="couponFrm" method="post">
			<div style="position: absolute; left: 0; top: 0;">
				<input type="submit" value="일괄삭제" class="button" />
			</div>
			<div style="width: 100%;">
				<table class="dataTbls normal-line-height collapse">
					<colgroup>
						<col width="">
						<col width="">
						<col width="">
						<col width="180px;">
						<col width="">
						<col width="160px">
						<col width="">
						<col width="">
						<col width="">
					</colgroup>
					<tr>
						<th><input type="checkbox" name="check_all" id="check_all"
							onClick="checkAll();"></th>
						<th>번호</th>
						<th>쿠폰코드</th>
						<th>최소결제금액</th>
						<th>할인금액</th>
						<th>유저</th>
						<th>상태</th>
						<th>Action</th>
					</tr>
<?php
    $index = 0;
    foreach ($coupons as $key => $value) {
        ?>
					<tr>
						<td style="text-align: center;"><input type="checkbox"
							name="check[]" id="check[]" value="<?php echo $value->idx; ?>"></td>
						<td><?php echo intval($total - ($index++)); ?></td>
						<td><?php echo $value->code; ?></td>

						<td><?php echo $value->min_money; ?></td>
						<td><?php echo $value->discount; ?></td>

						<td><?php echo $value->user; ?></td>
						<td><?php echo ($value->status == '' ? '사용가능':'사용완료'); ?></td>

						<td><a
							href="<?php echo add_query_arg(array('page'=> 'bbse_commerce_coupon','sel'=>'paper_add','idx'=>$value->idx)); ?>"
							class="button">수정</a> <a
							href="<?php echo add_query_arg(array('page'=> 'bbse_commerce_coupon','sel'=>'paper_add','idx'=>$value->idx,'del'=>'on')); ?>"
							class="button">삭제</a></td>
					</tr>
<?php
    }
    ?>
					
				</table>
			</div>
		</form>
	</div>

<?php
} // 종이쿠폰 추가
else if ($sel == 'paper_add') {
    // 삭제
    if ($_GET['del'] == 'on') {
        $wpdb->query('
				DELETE FROM bbse_commerce_paper_coupon
				WHERE idx = "' . $_GET['idx'] . '"
			');
        echo '
				<script>
					alert("삭제되었습니다.");
					location.href = "/wp-admin/admin.php?page=bbse_commerce_coupon&sel=paper";
				</script>
			';
        die();
    }
    // DB저장
    if ($_POST) {
        $wpdb->replace('bbse_commerce_paper_coupon', array(
            'idx' => $_POST['idx'],
            'code' => $_POST['coupon_code'],
            'min_money' => $_POST['coupon_min_money'],
            'discount' => $_POST['coupon_discount']
        ));
        echo '
				<script>
					location.href = "/wp-admin/admin.php?page=bbse_commerce_coupon&sel=paper";
				</script>
			';
        die();
    }
    // 수정
    $coupon = $wpdb->get_row('
			SELECT *
			FROM bbse_commerce_paper_coupon
			WHERE idx= "' . $_GET['idx'] . '"
		');
    ?>
	<div style="margin-top: 20px;">
		<form name="couponFrm" id="couponFrm" enctype="multipart/form-data"
			method="post">
			<input type="hidden" name="idx" value="<?php echo $coupon->idx; ?>" />
			<div style="width: 100%;">
				<table class="dataTbls normal-line-height collapse">
					<tbody>
						<tr>
							<th style="width: 150px;">쿠폰코드</th>
							<td><input type="text" name="coupon_code"
								value="<?php echo $coupon->code; ?>" /></td>
						</tr>
						<tr>
							<th style="line-height: 19px;">최소결제금액(설정한 금액 이상 주문 시 사용가능)</th>
							<td><input type="text" name="coupon_min_money"
								value="<?php echo $coupon->min_money; ?>" /></td>
						</tr>
						<tr>
							<th>할인금액</th>
							<td><span>할인액</span><input type="text" name="coupon_discount"
								value="<?php echo $coupon->discount; ?>" /></td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="btn_wrap" style="margin: 10px 0 0">
				<input type="submit" value="저장하기"
					class="button button-primary button-large" />
			</div>
		</form>
	</div>
<?php
} // 쿠폰사용내역
else if ($sel == 'use_list') {
    $coupons = $wpdb->get_results('
			SELECT	*
			FROM	bbse_commerce_coupon_log
			' . (! empty($_GET['s_user']) ? 'user LIKE "%' . $_GET['s_user'] . '%"' : '') . '
			ORDER BY idx DESC
		');
    $total = $wpdb->get_var('
			SELECT	COUNT(*)
			FROM	bbse_commerce_coupon_log
			' . (! empty($_GET['s_user']) ? 'user LIKE "%' . $_GET['s_user'] . '%"' : '') . '
		');
    ?>
	<style>
td {
	text-align: center;
}
</style>
	<div style="margin-top: 20px;">
		<form name="couponFrm" id="couponFrm">
			<div>
				<input type="hidden" name="page" value="bbse_commerce_coupon" /> <input
					type="hidden" name="sel" value="use_list" /> <input type="text"
					name="s_user" value="<?php echo $_GET['s_user']; ?>" /> <input
					type="submit" value="검색" class="button" />
			</div>
			<div style="width: 100%;">
				<table class="dataTbls normal-line-height collapse">
					<tr>
						<th>번호</th>
						<th>아이디</th>
						<th>주문번호</th>
						<th>주문상태</th>
						<th>쿠폰명</th>
						<th>사용여부</th>
						<th>사용일자</th>
					</tr>
<?php

    $orderStatus=Array("PR"=>"입금대기","PE"=>"결제완료","DR"=>"배송준비","DI"=>"배송중","DE"=>"배송완료","OE"=>"구매확정","CA"=>"취소신청","CE"=>"취소완료","RA"=>"반품신청","RE"=>"반품완료","EN"=>"정산완료","PW"=>"정산대기","AR"=>"미수","TR"=>"휴지통");
    $index = 1;
    foreach ($coupons as $key => $value) {
        $order_num = $wpdb->get_var('SELECT order_no FROM bbse_commerce_order WHERE idx = "' . $value->order_id . '" AND order_status <> "TR" ');
        $order_status = $wpdb->get_var('SELECT order_status FROM bbse_commerce_order WHERE idx = "' . $value->order_id . '" AND order_status <> "TR" ');
        $coupon = $wpdb->get_var('SELECT name FROM bbse_commerce_coupon WHERE idx = "' . $value->coupon_id . '"');
        $coupon_log = $wpdb->get_results('SELECT * FROM bbse_commerce_coupon_log WHERE order_id = "' . $value->order_id . '"');

        if ($order_num != null && $order_num != '') {
            ?>
					<tr>
						<td><?php echo ($index++); ?></td>
						<td><?php echo $value->user ?></td>
						<td><?php echo $order_num; ?></td>
						<td><?php echo $orderStatus[$order_status];?></td>
						<td><?php echo $coupon; ?></td>
    					<?php if(sizeof($coupon_log) >'0' ){?>
    					<td><span style="color: red;"><strong>사용</strong></span></td>
    					<?php }else{?>
    					<td><span style="color: blue;"><strong>미사용</strong></span></td>
    					<?php }?>
						<td><?php echo $value->date; ?></td>
					</tr>
<?php

}
    }
    if (count($coupons) < 1) {
        echo '
					<tr><td colspan="5">검색결과가 없습니다.</td></tr>
			';
		}
?>
					
				</table>
			</div>
		</form>
	</div>
<?php		
	}
?>
</div>