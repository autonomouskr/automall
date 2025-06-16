<table class="dataTbls overWhite collapse">
	<colgroup><col width="24%"><col width=""></colgroup>
	<tr>
		<th>유튜브 URL <span class="op-required">*</span></th>
		<td>
			<input type="text" name="op_url" id="op_url" style="width:95%;" value="" />
			<div class="prd-desc">· 입력하실 유튜브 동영상의 URL을 입력해 주세요.</div>
		</td>
	</tr>
	<tr>
		<th>가로 크기 <span class="op-required">*</span></th>
		<td>
			<input type="text" name="op_width" id="op_width" value="" onkeydown="opCheck_number();"> px
			<div class="prd-desc">· 유튜브 동영상의 가로 크기를 선택해 주세요.</div>
		</td>
	</tr>
	<tr>
		<th>세로 크기 </th>
		<td>
			<input type="text" name="op_height" id="op_height" value="" onkeydown="opCheck_number();"> px
			<div class="prd-desc">· 유튜브 동영상의 세로 크기를 선택해 주세요.</div>
		</td>
	</tr>
	<tr>
		<th>자동실행 여부<span class="op-required">*</span></th>
		<td>
			<input type="radio" name="op_auto" id="op_auto" value="Y">자동 실행&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="op_auto" id="op_auto" value="N" checked="checked">자동 실행 안함
			<div class="prd-desc">· 유튜브 동영상의 자동실행 여부를 선택해 주세요.</div>
		</td>
	</tr>
</table>
<div class="borderBox" style="margin:30px 0 20px;">
	- 동영상의 세로 크기를 입력하지 않은 경우, 유튜브의 정책에 따라 가로 100%, 세로 295px 크기로 자동지정 됩니다.<br/>
	<span style="color:#ED1C24;">- 동영상의 세로 크기는 PC, 태블릿, 모바일과 관계 없이 고정 크기로 플레이 됩니다. </span><br/>
	- 원본 동영상의 가로/세로 비율 및 크기에 의해, 플레이 되는 동영상의 가로/세로 크기가 반영되지 않을 수 있습니다.<br/>
	<span style="color:#ED1C24;">- 원본 동영상의 비율 및 크기를 확인 하신 후 입력해 주시기 바랍니다.</span><br/>
	&nbsp;&nbsp;&nbsp;예) 16:9 동영상(pixel) : 860 x 484 ,&nbsp;&nbsp;&nbsp;4:3 동영상(pixel) : 860 x 645
</div>
