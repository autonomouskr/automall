<?php 
/*
[테마 수정 시 주의사항]
1. 워드프레스(Wordpress)의 업데이트 방식은 기존 테마/플러그인을 삭제 한 후 재설치 하는 방식입니다.
   업데이트 시 모든 수정 사항이 초기화 되므로 테마를 수정하시는 경우, 차일드테마(Child Theme) 방식을 이용해 주시기 바랍니다.
2. 차일드테마(Child Theme)를 이용한 수정 방법 : https://codex.wordpress.org/ko:Child_Themes
*/

get_header();
GLOBAL $theme_shortname;
?>
<hr />
		<div id="sidebar">
      <?php get_sidebar('main')?>
		</div><!--//#sidebar-->

		<div id="content">
			<?php
			#로케이션
			get_template_part('part/sub', 'location');
			?>

			<div class="page_cont">
				<h2 class="page_title">Error : 404!  Page not found</h2>
				<span class="line"></span>
				<!-- <a href="#" class="prev-page">이전</a> -->
			</div>
			<div id="error404" class="post-404 page type-page status-publish hentry">
				<div id="POST-CONTENT" class="entry-content">
					<h2>페이지 또는 카테고리를 찾을 수 없습니다.</h2>
					<h3>가능성이 높은 원인</h3>
					<ul>
						<li>주소에 오타가 있을 수 있습니다.</li>
						<li>클릭한 링크가 만료된 것일 수도 있습니다.</li>
					</ul>

					<h3>가능한 해결 방법</h3>
					<ul>
						<li>주소를 다시 입력하십시오.</li>
						<li><a href="javascript:void(0);" onclick="history.back();">이전 페이지로 돌아갑니다.</a></li>
						<li><a href="<?php echo home_url('/'); ?>">기본 사이트</a>에서 원하는 정보를 찾습니다.</li>
					</ul>
				</div><!--//#error404-->
			</div><!--//.post-404-error -->
		</div><!--//#content -->
<hr />
<?php get_footer();
