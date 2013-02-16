<div id ="main"><?php $formcount=0;?>
  <div class = "row" id="search-query">
    <?php $results_count = count($books);
    if ($results_count<1){?>
      <span class="offset4 span5"> No books match your search. Try searching by different criteria.</span>
    <?php }else if ($results_count==1){?>
      <span class="offset4 span5">1 match for your search: "<?php echo($q);?>"</span>
    <?php }else{?>
      <span class="offset4 span5"><?php echo($results_count);?> match for your search: "<?php echo($q);?>"</span>
      <?php }?>
  </div>
  <?php $covernaurl = base_url() . "img/covernotavailable.jpg";?>
  <div class = "row"  id="results">
    <?php  foreach ($books as $book){?>
      <div class = "result span8 offset2">
        <div class="book">
          <h3 class="title"><?php echo($book->title);?></h3>
          <div class="cover">
              <?php $coverurl = base_url() . "img/book-covers/". $book->isbn . ".jpg";
              try{
                $coverfile = filesize(getcwd() . "/img/book-covers/". $book->isbn . ".jpg");
              }catch (Exception $e) {
                $coverfile=0;
              }  
                if ($coverfile!=0){
                  ?><img src=<?php echo $coverurl;?>  class="img-polaroid book-cover"/><?php
                }else{
                  ?><img src=<?php echo $covernaurl;?>  class="img-polaroid book-cover"/>
              <?php }?>
          </div>
          <div class="info">
            <div class="book-info span3">
              <div class="by-line">By <span class="author"><?php echo($book->authors);?></span></div>
              <div class="edition">ISBN: <?php echo($book->isbn);?></div>
              <div class="edition"><?php echo($book->edition);?> Edition</div>
              <div class="book-course">Required for: <?php echo($book->subject . " " . $book->course);?></div>
            </div>
            <div class="span3 price-info">
              <div class="store-price">Norris Bookstore Price: <?php echo($book->bookstore_price);?></div>
              <div class="amzn-list">Amazon List Price: <?php echo($book->amzn_list_price);?></div>
              <div class="amzn-new">Amazon New Price: <?php echo($book->amzn_new_price);?></div>
              <div class="amzn-used">Amazon Used Price: <?php echo($book->amzn_used_price);?></div>
              <div class="amzn-link"><a href="<?php echo($book->amzn_link);?>" target="_blank">Buy on Amazon</a></div>
            </div>
          </div>
          <div class="student-posts">
            <div class="sellers">
                <?php if(count($book->posts)>=1){?>
                  <button type="button" class="btn btn-danger" data-toggle="collapse" data-target="#sellers-list-book-<?php echo($book->isbn);?>">
                    <i class="icon-list"></i> <?php echo(count($book->posts));?> posting<?php if(count($book->posts)>1){echo('s');}?>
                  </button>  
                  starting at $<?php $from=$book->from;echo($from[0]->price);?> dollars.
                  <div id="sellers-list-book-<?php echo($book->isbn);?>" class="accordion-body collapse">
                    <?php  foreach ($book->posts as $post){?>
                      <div class = "posting row">
                        <div class="span2"> Price: <span class="price"><?php echo($post->price);?></span></div>
                        <div class="span2"> Condition: <span class="price"><?php echo($post->condition);?></span></div>
                        <button type="button" class="btn btn-mini btn-info" data-toggle="collapse" data-target="#book-<?php echo($book->id);?>-post-<?php echo($post->id);?>">
                          <i class="icon-chevron-down"></i> Details
                        </button>
                        <div id="book-<?php echo($book->id);?>-post-<?php echo($post->id);?>" class="accordion-body collapse posting-detail span5">
                          <?php $sellerdata=$post->sellerdata;$sellerdata=$sellerdata[0];?>
                          Seller: <?php echo($sellerdata->first_name);?>
                          <br>
                          Email: <a href="mailto:<?php echo($sellerdata->email);?>?subject=BookSwap: I'd like to buy the book you posted&amp;body=I'd like to buy your copy of <?php echo($book->title);?>">
                                        <?php echo($sellerdata->email);?>
                                      </a>
                          <br>
                          Notes:<span class="notes"><?php echo($post->notes);?></span>
                        </div>  
                      </div>
                    <?php }?>
                  </div>
                <?php }else{?>
                  <div class="span5 no-sellers">
                      <button type="button" class="btn btn-danger disabled">
                      There are no student postings for this book
                    </button>
                  </div>
                <?php }?>
            </div>
          </div>
          <?php if($seller){?>
            <div class="sell-yours button-box">
              <button class="btn-success btn" data-toggle="collapse" data-target="#postform-container-<?php echo($formcount);?>">Sell yours</button>
              <span>Have a copy to sell?</span>
              <div id="postform-container-<?php echo($formcount);?>"  class="accordion-body collapse">
                <?php $this->load->view('postform',array('book'=>$book));?>
              </div>
            </div>
            <?php $formcount=$formcount+1;?>
          <?php }else{?>
            <div class="sell-yours please-login">
              <button class="btn-success btn disabled">Sell yours</button>
              <span>Log in to sell your copy!</span>
            </div>
          <?php }?>
        </div>
      </div>
    <?php }?>
  </div>
</div>

<?php
  function checkRemoteFile($url){
   $ch = curl_init($url);
curl_setopt($ch, CURLOPT_NOBODY, true); // set to HEAD request
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // don't output the response
curl_exec($ch);
$valid = curl_getinfo($ch, CURLINFO_HTTP_CODE) == 200;
curl_close($ch);
return $valid;
  }