
<script type="text/javascript">
var isloaded=0
window.onload = function(){
  if (document.cookie.indexOf("isloaded")==-1){
    $("#howto-icon").click()
    var expireDate = new Date()
    expireDate.setDate(expireDate.getDate()+100)
    document.cookie = "isloaded=1; path=/;expires=" + expireDate.toGMTString()
    document.cookie="isloaded=1"
  }
}
</script>
<div class="row">
  <header class="span8 offset3">
    <img id="front-logo" src="<?echo BASE;?>images/bookswap-logo.png" alt="BookSwap 2.0" />
  </header>
</div>
<div class="row">
  <div id="search-box" class="span7 offset3">
      <?
        echo form_open('looking',array('class'=>""),array('submit'=>true));
        $options = array (
          'size'=>'14',
          'name'=>'q',
        'id'    => 'front-search',
        'class'=>'span5',
        'Placeholder'=>'To buy or sell: search by title, ISBN, or course number'
        );

        // Create our label for user_name.
        echo form_input($options); 
      $options = array(
        'class' => 'btn btn-inverse',
        'id' => 'front-submit',
        'name'=>'search',
        'value'=>'Search'
        );
      ?><a id="howto-icon" href="#howto-modal" role="button" data-toggle="modal"> <i class="icon-info-sign"></i> </a><?
      echo form_submit($options);
      echo form_close();
    ?>
    
 

  </div>
</div>
<!-- Modal -->
<div id="howto-modal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel">How to use BookSwap</h3>
  </div>
  <div class="modal-body">
    <p>To buy or sell a book, first search by ISBN or course number (ie. Math 230, econ 310, etc).</p>
    <br>
    <p><h3>Buying: </h3>Then view available student postings or amazon prices to buy</p>
    <br>
    <p><h3>Selling: </h3> Login with your netID to post your copy<p>
    <br>
    <p>Only Fall 2012 and Winter 2013 books are currently available. Spring 2013 books will be added soon<p>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
  </div>
</div>

