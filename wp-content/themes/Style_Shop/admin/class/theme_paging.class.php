<?php
//사용자 페이지 페이징 처리 클래스
class themePaging
{ 
    //한 페이지에 보여줄 ROW 수 
    /* 
     * ex) 
     * 1번 게시물~~~ 
     * 2번 게시물 ~~~~ 
     * 3번 게시물 ~~~~~ 
     *  
     * 한 페이지에 3개의 게시물을 보여준다면 $page_row = 3 
     */
    private $page_row; 
          
    //페이지 블럭 단위 ( 1개의 블럭에 몇개의 페이지를 보여줄지 결정 ) 
    //ex) |이전 블럭|이전|1|2|3|4|5|다음|다음 블럭| 일 경우 $page_block = 5 
    private $page_block; 
          
    //게시물 전체 갯수 
    private $total_count;    
      
    //페이지 전체 수 
    private $total_page_count;   
      
    //현재 블럭 위치 
    /* 
     * ex) 
     * |이전 블럭|이전|1|2|3|4|5|다음|다음 블럭| 일 경우 $current_block = 1 
     * |이전 블럭|이전|6|7|8|9|10|다음|다음 블럭| 일 경우 $current_block = 2 
     */
    private $current_block;  
      
    //블럭 전체 수 
    private $total_block_count;  
      
    //현재 페이지 번호 
    //ex) |이전 블럭|이전|1|2|3|4|5|다음|다음 블럭| 에서 현재 3페이지 라면 $current_page = 3 
    private $current_page;   
      
    //페이지 이동시 URL (페이징을 하고 있는 페이지 url) 
    //ex) /unolee/book_list.php? 
    private $link_url;   

	//콜백함수
	private $func; 

    //페이징 결과 html 저장 변수 
    private $page_result; 
    
      
    private $page_start_num; 
    private $page_end_num; 

    /** 
     *  @Desc   : 멤버 변수초기화 작업 
     */
    public function initPaging($param = array()) 
    { 
        $this->page_result = ""; 
                  
        $this->page_row  = $param['page_row']; 
        $this->page_block    = $param['page_block'];      
        $this->total_count   = $param['total_count']; 
        $this->current_page = $param['current_page']; 
        $this->link_url      = $param['link_url'];
        $this->func      = $param['func'];

        //페이지 전체수 = (전체 게시물 수 / 한 페이지 row 수) 의 올림(ceil)한 값 
        //ex) 페이지 전체수 (14 페이지) = 전체 게시물 수 (40개) / 한 페이지 row 수(3개) 의 올림 
        $this->total_page_count = ceil($this->total_count / $this->page_row);   
          
        //현재 블럭 위치 
        //ex) 현재 블럭 위치 (2) = 현재 페이지(6페이지) / 페이지 블럭 단위(5) 의 올림 
        $this->current_block = ceil($this->current_page / $this->page_block); 
  
        //블럭 전체 수 
        //ex) 블럭 전체수(3) = 페이지 전체 수(14 페이지) / 페이지 블럭(5) 의 올림  
        $this->total_block_count = ceil($this->total_page_count / $this->page_block);   
    } 
      
    /* 
     * 페이징 결과 html을 만든다. 
     * ex) 
     * |이전 블럭|이전 페이지|1|2|3|4|5|다음 페이지|다음 블럭| 
     */
    public function getPaging() 
    { 

		if($this->total_count > 0) {

			/*페이지 시작 번호 
			 * ex) 
			 * 첫번째 블럭(1) 일 경우->  |이전 블럭|이전|1|2|3|4|5|다음|다음 블럭|  $this->page_start_num = 1 
			 * 두번째 블럭(2) 일 경우->  |이전 블럭|이전|6|7|8|9|10|다음|다음 블럭|  $this->page_start_num = 6 
			 */
			$this->page_start_num = ($this->current_block - 1) * $this->page_block + 1; 
	  
			/*페이지 끝 번호 
			 * ex) 
			 * 첫번째 블럭(1) 일 경우->  |이전 블럭|이전|1|2|3|4|5|다음|다음 블럭|  $this->page_end_num = 5 
			 * 두번째 블럭(2) 일 경우->  |이전 블럭|이전|6|7|8|9|10|다음|다음 블럭|  $this->page_end_num = 10 
			 */     
			$this->page_end_num = $this->current_block * $this->page_block;  
			  
	  
			//페이지 끝 번호가 페이지 전체 수 보다 클 경우 
			if( $this->page_end_num > $this->total_page_count) 
				$this->page_end_num = $this->total_page_count; 
				  
			//페이지 시작 번호가 페이지 끝 번호보다 클 경우 
			if($this->page_start_num > $this->page_end_num) 
				$this->page_start_num = $this->page_end_num;   
				  

			//페이징 결과 html <div>태그로 시작 
			//트위터 부트스트랩에 사용되는 css 클래스 사용(pagination,disabled,active) 
			$this->page_result = "<div class='article'><div class='paging'>\n"; 

			//처음 페이지로 이동
			if($this->total_page_count > 1) {
				if($this->func) $pageProc = $this->func."(1);";
				else $pageProc = "location.href='{$this->link_url}&page=1';";
				$this->page_result .= "<button type=\"button\" class=\"first\" title=\"처음페이지\" onclick=\"".$pageProc."\"><span>&lt;&lt;</span></button>\n";
			}else{
				$this->page_result .= "<button type=\"button\" class=\"first\" title=\"처음페이지\"><span>&lt;&lt;</span></button>\n";
			}

			//'이전 블럭' 생성 
			if($this->current_block == 1) 
			{ 
				//첫번째 블럭(1) 일 경우 '이전 블럭' 비활성화 
				$this->page_result .= " <button type=\"button\" class=\"prev\" title=\"이전블럭\"><span>&lt;</span></button>"; 
			} 
			else
			{ 
				//이동할 페이지 번호 지정  
				//(ex)$current_block=2 , $page_block=5 일 경우 '이전 블럭' 선택시 '5 페이지로 이동' 
				$move_page_number = ($this->current_block - 1) * $this->page_block; 
				if($this->func) $pageProc = $this->func."(".$move_page_number.");";
				else $pageProc = "location.href='{$this->link_url}&page={$move_page_number}';";
				$this->page_result .= "<button type=\"button\" class=\"prev\" title=\"이전블럭\" onclick=\"".$pageProc."\"><span>&lt;</span></button>\n"; 
			} 
			  
			/*
			//'이전 페이지' 생성 
			if($this->current_page == 1) 
			{ 
				//첫번째 페이지(1) 일 경우 '이전 페이지' 비활성화 
				$this->page_result .= "<li class='disabled'><a href='#'>이전 페이지</a></li>"; 
			} 
			else
			{ 
				//이동할 페이지 번호 지정  (현재 페이지 번호 - 1) 
				$move_page_number = $this->current_page - 1; 
				$this->page_result .= "<li><a href='{$this->link_url}?page={$move_page_number}'>이전 페이지</a></li>"; 
			}
			*/
			  
			//페이지 번호 
			$this->page_result .= "<span class=\"page\">\n";
			for($i = $this->page_start_num; $i <= $this->page_end_num; $i++) 
			{ 
				if($i == $this->current_page) {
					$this->page_result .= "<button type=\"button\" class=\"here\"><span>$i</span></button>\n";
				}else{

					if($this->func) $pageProc = $this->func."(".$i.");";
					else $pageProc = "location.href='{$this->link_url}&page={$i}';";
					$this->page_result .= "<button type=\"button\" onclick=\"".$pageProc."\"><span>$i</span></button>\n";
				}
			}
			$this->page_result .= "</span>\n";
			/*
			//'다음 페이지' 생성 
			if($this->current_page == $this->total_page_count) 
			{ 
				//현재 마지막 페이지에 있을 경우 '다음 페이지' 비활성화 
				$this->page_result .= "<li class='disabled'><a href='#'>다음 페이지</a></li>"; 
			} 
			else
			{ 
				//이동할 페이지 번호 지정 (현재 페이지 번호 + 1) 
				$move_page_number = $this->current_page + 1; 
				$this->page_result .= "<li><a href='{$this->link_url}?page={$move_page_number}'>다음 페이지</a></li>"; 
			} 
			*/
			  
			//'다음 블럭' 생성 
			if($this->total_block_count == $this->current_block)  
			{ 
				//현재 마지막 블럭에 있을 경우 ' 다음 블럭' 비활성화 
				$this->page_result .= "<button type=\"button\" class=\"next\" title=\"다음블럭\"><span>&gt;</span></button>\n"; 
			} 
			else
			{ 
				//이동할 페이지 번호 지정 (현재 블럭의 마지막 페이지 번호 + 1) 
				$move_page_number = $this->page_end_num + 1;
				if($this->func) $pageProc = $this->func."(".$move_page_number.");";
				else $pageProc = "location.href='{$this->link_url}&page={$move_page_number}';";
				$this->page_result .= "<button type=\"button\" class=\"next\" title=\"다음블럭\" onclick=\"".$pageProc."\"><span>&gt;</span></button>\n";
			} 

			//마지막 페이지로 이동
			if($this->total_page_count > 1) {
				if($this->func) $pageProc = $this->func."(".$this->total_page_count.");";
				else $pageProc = "location.href='{$this->link_url}&page={$this->total_page_count}';";
				$this->page_result .= "<button type=\"button\" class=\"last\" title=\"마지막페이지\" onclick=\"".$pageProc."\"><span>&gt;&gt;</span></button>\n";
			}else{
				$this->page_result .= "<button type=\"button\" class=\"last\" title=\"마지막페이지\"><span>&gt;&gt;</span></button>\n";
			}

			$this->page_result .= "</div></div>\n"; 

		}else{//게시물이 없을경우 표시하지 않음

			$this->page_result = "";

		}
        return $this->page_result; 
    } 

}
?>