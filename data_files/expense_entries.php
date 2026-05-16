<?php
session_start();
error_reporting(E_ALL ^ E_NOTICE);
require("../xsert/connect.php");
require_once('../data_files/sys_function.php');
require_once('../data_files/page_settings.php');

check_sess(); //check user loggin

?>
<!DOCTYPE html>
<html lang="en">
  <head>
   <meta content="charset=utf-8" /> 
    <?php include('link_docs.php') ?>
   <title><?php echo sys_tab_hdr() ?></title>

<script type="text/javascript">

$(function(){

var date = new Date();
   var year_cur = date.getFullYear();//return current year
   var caption = '/'+year_cur.toString().substring(2);
   return_id(caption);

})

//check for already exist expense index
function return_id(caption){
 
 $.getJSON('id_index.php?tab=expense&tab_index=exp_ac', function (data) {

     if(data!=''){
    data.unshift('00000'+caption);
    var len = data.length;
    var check_id, index, id, check;
    
    for(var x=1; x<=len; x++){
      if(x<10)
        check_id = '0000'+x;
      if(x>9)
        check_id = '000'+x;
      if(x>99)
        check_id = '00'+x;
     
    check = data.indexOf(check_id + caption);
     if(check=='-1'){
      index=(check_id + caption)
      x=len;
     }
   }
   $('#ac_id').val(index);
  } else {
     $('#ac_id').val('00001'+caption);
   }
  });
}

function return_voucher(index,tab){
   var content = $('#myModal').html();
   $.ajax({
      type:'POST',
      url: 'src_data.php',
      data:{
        'post_id': index,
        'note_post':'Payment Voucher',
        'tab':tab
      },
        beforeSend:function(){
              $('#myModal').css('display','block').html('<div style="margin:auto;margin-top:200px;margin-bottom:100px;width:10%;"><img src="../img_data/loading.gif" /></div>');
            },
        success:function(d){
              $('#myModal').html(content);
              $('#display_large').html(d);
        }
   })
}

$(document).on('keyup','input[name="expense[]"]',function(){

var keys = $(this).val();
var id = $(this).attr('id');
var id_index = id.split('_');

$.ajax({
    type: "POST",
    url: "data_src.php",
    data:{
      'get_expense': keys,
      'index' : id_index[1]
    },    
    success: function(data){

        if(data==0){
          $("#list_"+id_index[1]).slideUp();
         }else{
          $("#list_"+id_index[1]).slideDown().html(data);
        }
     }
  });
})

$(document).on('keyup','input[name="cost[]"]', function(){
 
 var myVal = "";
 var myDec = "";
 
 var id = $(this).attr("id");
 var amtVal = $(this).val();
 var amt_split = amtVal.toString().split('.');

      // Filtering out the trash!
        amt_split[0] = amt_split[0].replace(/[^0-9]/g,""); 

      // Setting up the decimal part
        if ( ! amt_split[1] && amtVal.indexOf(".") > 1 ) {myDec = "."}
        if ( amt_split[1] ) { myDec = "."+ parseFloat(amt_split[1]) }

  // Adding the thousand separator
        while(amt_split[0].length > 3 ) {
            myVal = ","+amt_split[0].substr(amt_split[0].length-3, amt_split[0].length )+ myVal;
            amt_split[0]= amt_split[0].substr(0, amt_split[0].length-3);            
           }
    $(this).val(amt_split[0]+myVal+myDec);
    calculateTotal();
})

$(document).on('click','.list_items',function(){

 var list_attr = $(this).attr('data-set');
 var list_val = $(this).html();
 var index = $(this).attr('index');

 $('#expense_'+index).val(list_val);
 $('#exp_'+index).val(list_attr);
 $('#list_'+index).slideUp('fast');

  //adds new row
  var count = $('.rows').length;
  var new_count = count+1;
  var cur_index = $('#hidden_cur').val();

  var row = ('<div id="wrap_'+new_count+'" class="rows row_block grid-5-items">'
                    +'<div>'+new_count+')</div>'
                    +'<div>'
                        +'<input type="text" name="expense[]" id="expense_'+new_count+'" class="text-input" placeholder="Expense Item" />'
                        +'<div id="list_'+new_count+'" class="drop_down drop_small_size"></div>'
                        +'<input type="hidden" name="expense_id[]"  id="exp_'+new_count+'" /></div>'
                    +'<div><input type="text" class="text-input" name="qty[]" id="qty_'+new_count+'" placeholder="00.0" /></div>'
                    +'<div><input type="text" name="cost[]" value="" id="cost_'+new_count+'" class="text-input" data="" placeholder="00.0" /></div>'
                    +'<div><input type="text" class="text-input" name="amt[]" id="amount_'+new_count+'" placeholder="00.0" readonly /></div>'
              +'</div>');

  if(list_attr==0){
    $(".drop_down").slideUp('fast');
    $("#expense_"+index).val('');
  }

  if($('#expense_'+count).val()){
    $('#wrap_'+count).after(row);
  }

})

$(document).on('click','#submit',function(e){
  
  e.preventDefault();
  var vouch = $('#ac_id').val();
  $.ajax({
      type:'POST',
      url: 'post_data.php',
      data:$('#form1').serialize(),
        beforeSend:function(){
          $('#myModal_2').css({'display':'block'},{'z-index':'6'}).html('<div class="modal-spin-wrap"><div class="modal-img-spin"><img src="../img_file/loading.gif" /></div></div>')
         },
      success:function(d){
           location.replace('expense_entries.php?action_msg='+d);
      }
  })
})

</script>

<style type="text/css">

.account_no{
  display:none;
}

</style>
</head>

<body>
   
   <!-- Main Content Wrapper -->
<div class="main_bd_wrap">
  
  <?php        
    tp_hdr(); //Page Header, Menu
       side_menu_content(); // Side Menu
      ?>
         <!-- Main Content Side-Right -->
           <div class="main-sidebar col-lg-9">

            <!-- form header -->
 <div class="form_wrap_large">
          <!--form header-->
          <?php if($_GET){ include('action_msg.php'); } ?>
         <div class="form_header">Manage Expenses</div>
      <form name="form1" id="form1" method="post" action="" autocomplete="off">
          <table cellpadding="5" cellspacing="0" align="center" width="100%" class="form_table">
            <input type="hidden" name="order_cost_expenses" value="<?php echo $_GET['order_no'] ?>" />
            <input type="hidden" name="hidden_cur" id="hidden_cur" value="" />
            <input type="hidden" name="currency" id="cur_select" value="" />
             <tr>
                <td>Voucher No</td>
                <td><input type="text"  name="ac_id" id="ac_id" required="required" value="" class="text-input" /></td>
             </tr>
             <tr>
              <td>Date</td>
              <td><input type="text" name="date" id="picker" value="<?php echo date('Y/m/d H:i') ?>" required="required" class="text-input" /></td>
             </tr>
             <tr>
               <td colspan="2">
                <div style="width:100%;display:block;margin-bottom:10px;"><b>Detail(s)</b></div>
                <div class="row_block grid-5-items top_bg" style="font-weight: bold;padding:5px 0;font-size:12px;">
                     <div>No</div>
                     <div>Particulars (s)</div>
                     <div>Unit(s)</div>
                     <div>Cost</div>
                     <div>Amount</div>
                  </div>
              
              <div id="list_block" >
                <div id="wrap_1" class="rows row_block grid-5-items">
                    <div>1)</div>
                    <div>
                        <input type="text" name="expense[]" id="expense_1" class="text-input" placeholder="Expense Item" />
                        <div id="list_1" class="drop_down drop_small_size"></div>
                        <input type="hidden" name="expense_id[]"  id="exp_1" /></div>                         
                    <div><input type="text" class="text-input" name="qty[]" id="qty_1" placeholder="00.0" /></div>
                    <div><input type="text" name="cost[]" value="" id="cost_1" class="text-input" data="" placeholder="00.0" /></div>
                    <div><input type="text" class="text-input" name="amt[]" id="amount_1" placeholder="00.0" readonly /></div>
                  </div>
                <div class="row_block grid-5-items">
                    <div>Total</div>
                    <div>&nbsp;</div>
                    <div>&nbsp;</div>
                    <div id="total_amt">00.0</div>
                  </div>
                  <div class="bottom_frm_align">
                     <div></div>
                     <div>Paid To</div>
                     <div><input type="text" class="text-input" name="paid_to" id="paid_to" /></div>
                  </div>
                  <div class="bottom_frm_align">
                     <div></div>
                     <div>Mode of Pay</div>
                     <div>
                       <select name="mop" id="mop" class="text-input">
                         <option selected="selected" value="">Select</option>
                          <?php
                              $sql = mysqli_query($connect,"SELECT * FROM mop");
                                while($r = mysqli_fetch_array($sql)){
                                   echo '<option value="'.$r[0].'">'.$r[1].'</option>';
                                }
                            ?>
                        </select></div>
                  </div>
                  <div class="bottom_frm_align account_no">
                     <div></div>
                     <div>Account From</div>
                     <div>
                      <select name="mop_account" id="mop_account" class="text-input">
                        <option selected="selected" value="">Select</option>
                      </select></div>
                  </div>
                  <div class="bottom_frm_align account_no">
                     <div></div>
                     <div>Account To</div>
                     <div>
                      <input type="text" class="text-input" name="acc_to" id="acc_to" /></div>
                  </div>
                  <div class="bottom_frm_align account_no">
                     <div></div>
                     <div>Account No</div>
                     <div>
                      <input type="text" class="text-input" name="acc_to_ac" id="acc_to_ac" /></div>
                  </div>
                  <div class="bottom_frm_align">
                     <div></div>
                     <div></div>
                     <div>
                      <input type="submit" name="submit"  value="Submit" class="button-input" id="submit" /></div>
                  </div>
                 </div></td>
             </tr>                  
          </table>
       </form>
    </div>
        </div>
    <?php
      echo footer_sec(); //footer section
    ?>
  </div>
  </body>
</html>