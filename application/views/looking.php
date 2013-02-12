<div id ="main"><?$formcount=0;?>
  <div class = "row" id="search-query">
    <?$results_count = count($books);
    if ($results_count<1){?>
      <span class="offset4 span5"> No books match your search. Try searching by different criteria.</span>
    <?}else if ($results_count==1){?>
      <span class="offset4 span5">1 match for your search: "<?echo($q);?>"</span>
    <?}else{?>
      <span class="offset4 span5"><?echo($results_count);?> match for your search: "<?echo($q);?>"</span>
      <?}?>
  </div>
  <?$covernaurl = BASE . "images/book-covers/covernotavailable.jpg";?>
  <div class = "row"  id="results">
    <? foreach ($books as $book){?>
      <div class = "result span8 offset2">
        <div class="book">
          <h3 class="title"><?echo($book->title);?></h3>
          <div class="cover">
              <?$coverurl = BASE . "images/book-covers/". $book->isbn . ".jpg";
              try{
                $coverfile = filesize(getcwd() . "/images/book-covers/". $book->isbn . ".jpg");
              }catch (Exception $e) {
                $coverfile=0;
              }  
                if ($coverfile!=0){
                  ?><img src=<?echo $coverurl;?>  class="img-polaroid book-cover"/><?
                }else{
                  ?><img src=<?echo $covernaurl;?>  class="img-polaroid book-cover"/>
              <?}?>
          </div>
          <div class="info">
            <div class="book-info span3">
              <div class="by-line">By <span class="author"><?echo($book->authors);?></span></div>
              <div class="edition">ISBN: <?echo($book->isbn);?></div>
              <div class="edition"><?echo($book->edition);?> Edition</div>
              <div class="book-course">Required for: <?echo($book->subject . " " . $book->course);?></div>
            </div>
            <div class="span3 price-info">
              <div class="store-price">Norris Bookstore Price: <?echo($book->bookstore_price);?></div>
              <div class="amzn-list">Amazon List Price: <?echo($book->amzn_list_price);?></div>
              <div class="amzn-new">Amazon New Price: <?echo($book->amzn_new_price);?></div>
              <div class="amzn-used">Amazon Used Price: <?echo($book->amzn_used_price);?></div>
              <div class="amzn-link"><a href="<?echo($book->amzn_link);?>" target="_blank">Buy on Amazon</a></div>
            </div>
          </div>
          <div class="student-posts">
            <div class="sellers">
                <?if(count($book->posts)>=1){?>
                  <button type="button" class="btn btn-danger" data-toggle="collapse" data-target="#sellers-list-book-<?echo($book->isbn);?>">
                    <i class="icon-list"></i> <?echo(count($book->posts));?> posting<?if(count($book->posts)>1){echo('s');}?>
                  </button>  
                  starting at $<?$from=$book->from;echo($from[0]->price);?> dollars.
                  <div id="sellers-list-book-<?echo($book->isbn);?>" class="accordion-body collapse">
                    <? foreach ($book->posts as $post){?>
                      <div class = "posting row">
                        <div class="span2"> Price: <span class="price"><?echo($post->price);?></span></div>
                        <div class="span2"> Condition: <span class="price"><?echo($post->condition);?></span></div>
                        <button type="button" class="btn btn-mini btn-info" data-toggle="collapse" data-target="#book-<?echo($book->id);?>-post-<?echo($post->id);?>">
                          <i class="icon-chevron-down"></i> Details
                        </button>
                        <div id="book-<?echo($book->id);?>-post-<?echo($post->id);?>" class="accordion-body collapse posting-detail span5">
                          <?$sellerdata=$post->sellerdata;$sellerdata=$sellerdata[0];?>
                          Seller: <?echo($sellerdata->first_name);?>
                          <br>
                          Email: <a href="mailto:<?echo($sellerdata->email);?>?subject=BookSwap: I'd like to buy the book you posted&amp;body=I'd like to buy your copy of <?echo($book->title);?>">
                                        <?echo($sellerdata->email);?>
                                      </a>
                          <br>
                          Notes:<span class="notes"><?echo($post->notes);?></span>
                        </div>  
                      </div>
                    <?}?>
                  </div>
                <?}else{?>
                  <div class="span5 no-sellers">
                      <button type="button" class="btn btn-danger disabled">
                      There are no student postings for this book
                    </button>
                  </div>
                <?}?>
            </div>
          </div>
          <?if($seller){?>
            <div class="sell-yours button-box">
              <button class="btn-success btn" data-toggle="collapse" data-target="#postform-container-<?echo($formcount);?>">Sell yours</button>
              <span>Have a copy to sell?</span>
              <div id="postform-container-<?echo($formcount);?>"  class="accordion-body collapse">
                <?$this->load->view('postform',array('book'=>$book));?>
              </div>
            </div>
            <?$formcount=$formcount+1;?>
          <?}else{?>
            <div class="sell-yours please-login">
              <button class="btn-success btn disabled">Sell yours</button>
              <span>Log in to sell your copy!</span>
            </div>
          <?}?>
        </div>
      </div>
    <?}?>
  </div>
</div>

<?
  function checkRemoteFile($url){
   $ch = curl_init($url);
curl_setopt($ch, CURLOPT_NOBODY, true); // set to HEAD request
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // don't output the response
curl_exec($ch);
$valid = curl_getinfo($ch, CURLINFO_HTTP_CODE) == 200;
curl_close($ch);
return $valid;
  }