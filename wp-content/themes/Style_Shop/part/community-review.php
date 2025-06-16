<style>
.bestReviewListWrap {float:left;margin:50px 0 0 0;width:100%;}
  .bestReviewListWrap .sorting_select {float:right;width:auto;margin:0 0 10px;;}
  .bestReviewListWrap ul.bestReviewList {float:left;width:100%;border-top:2px solid #666666;}
    .bestReviewListWrap ul.bestReviewList li {float:left;padding:15px 0;width:100%;border-bottom:1px solid #666}

      ul.bestReviewList li > div {float:left}

      ul.bestReviewList li .thumbnail {width:86px;height:86px}
        ul.bestReviewList li .thumbnail img {width:86px;height:86px}
      ul.bestReviewList li .subjects {padding:0 1%;width:58%;height:86px;line-height:1.5em;}
        ul.bestReviewList li .subjects h3 {font-weight:bold;}
        ul.bestReviewList li .subjects .mobileBlock {display:none}
        ul.bestReviewList li .subjects p {margin:1.3em  0 0;width:100%;height:4.5em;overflow:hidden;}
      ul.bestReviewList li .stars {width:10%;text-align:center;}
      ul.bestReviewList li .author {width:10%;text-align:center;}
      ul.bestReviewList li .date {float:right;width:10%;text-align:center;}
@media only screen and (max-width: 1023px) {

      ul.bestReviewList li .subjects {padding:0 2%;width:69%;}
        ul.bestReviewList li .subjects .mobileBlock {display:block;}
        ul.bestReviewList li .subjects p {height:3em;}
      ul.bestReviewList li .stars,
      ul.bestReviewList li .author,
      ul.bestReviewList li .date {display:none}
}
</style>
<h2 class="page_title">베스트 후기</h2>
<div class="bestReviewListWrap">

  <p class="sorting_select">
    <select name="" id="dummyID32" title="리스트 갯수를 선택해주세요.">
      <option value="">5개씩 보기</option>
      <option value="">10개씩 보기</option>
      <option value="">20개씩 보기</option>
    </select>
  </p>

  <ul class="bestReviewList">
    <?php for($i=1; $i<6; $i++){?>
    <li>
      <div class="thumbnail">
        <img src="<?php bloginfo('template_url')?>/_temp/@thumb_200x200.png" alt="첨부이미지" />
      </div>
      <div class="subjects">
        <h3>후기 제목</h3>
        <div class="mobileBlock">
          <span class="bb_cmt_star cmt<?php echo $i?>">별점 5점/5점</span> 작*자 2014-07-07
        </div>
        <p>후기 요약 후기 요약 후기 요약 후기 요약 후기 요약 후기 요약 후기 요약 후기 요약 후기 요약 후기 요약 후기 요약 후기 요약 후기 요약 후기 요약 후기 요약 후기 요약 후기 요약 후기 요약 후기 요약 후기 요약 후기 요약 후기 요약 후기 요약 후기 요약 후기 요약 후기 요약 후기 요약 후기 요약 후기 요약 후기 요약 후기 요약 후기 요약 후기 요약 후기 요약 후기 요약 후기 요약 후기 요약 후기 요약 후기 요약 후기 요약 </p>
      </div>
      <div class="stars"><span class="bb_cmt_star cmt<?php echo $i?>">별점 5점/5점</span></div>
      <div class="author">작*자</div>
      <div class="date">2014-07-07</div>
    </li>
    <?php }?>
  </ul>
</div>