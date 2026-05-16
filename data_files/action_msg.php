         
<?php
  // Returns Action Response Msg after Form Post

              if($_GET['action_msg']=="success"){
                  ?>
                <div class="get_action success_action">
                  <span>Action Successful...!!!</span>
                  <span style="float:right;margin-right:5px;cursor:pointer" id="times">&times</span>
                </div><?php } 
                if($_GET['action_msg']=="err"){?>
                <div class="get_action err">
                  <span>Something Went Wrong...!!!</span>
                  <span style="float:right;margin-right:5px;cursor:pointer" id="times">&times</span>
                </div><?php }
                if($_GET['action_msg']=="exist"){?>
                <div class="get_action exist">
                  <span>Member With Simillar Details Already Exist...!!!</span>
                  <span style="float:right;margin-right:5px;cursor:pointer" id="times">&times</span>
                </div>
        <?php } ?>