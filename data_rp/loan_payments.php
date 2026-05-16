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
   <title><?php echo sys_tab_hdr() ?></title> 
    <?php include('../data_files/link_docs.php') ?>
  <script type="text/javascript" src="../data_scripts/jspdf.debug.js"></script>
  <script type="text/javascript" src="../data_scripts/html2canvas.min.js"></script>
  <script type="text/javascript" src="../data_scripts/html2pdf.min.js"></script>  

<script type="text/javascript">

$(function(){
  
  let tot_pay = $('#total_pay').html();
  let tot_bal = $('#total_bal').html();
  let defaulters = $('#summary_stats').attr("data-due");
  let paid = $('#summary_stats').attr('data-paid');
  let count = 0;

  $('.report_display tr').each(()=>{
     count+=1;
  })
  
  
  $('#summary_default').html(defaulters);
  $('#summary_non_default').html(paid);
  $('#summary_paid').html(tot_pay);
  $('#summary_bal').html(tot_bal);
  var tot_collect = remove_filter(tot_pay)+remove_filter(tot_bal);
  $('#summary_collection').html(seperator(tot_collect));
  ((defaulters+paid)==0) ? $('#tot_count').html(1) : $('#tot_count').html(count-2);
    
})

$(document).on('keyup','#names',function() {
  
  var  search = $(this).val();
  var exp = new RegExp(search, "i");
  var results='',count=0, combo;

if(search){
  
  $.ajax({
    type:'POST',
    url:'../data_files/name_list.php',
    data:{
      'search':search,
      'name_cat': 'clients'
    },
    beforeSend:function(){
      $("#drop-box").slideDown().html('<div style="margin:auto;max-height250px;margin-bottom:50px;margin-top:50px;width:40%;"><img src="../img_file/loading.gif" /></div>');
    },
    success:function(data){

    $.each(data,function(key,value){
      combo = value.first_name+' '+value.last_name;
      combo2 = value.last_name+' '+value.first_name;
       if(value.first_name.search(exp) != -1 || value.last_name.search(exp) != -1 || combo.search(exp) != -1 || combo2.search(exp) != -1){
        count+=1;
         results += '<div class="list_items" data="'+value.id+'">'+value.first_name+' '+value.last_name+'</div>';
        }
     })

     if(results)
      $("#drop-box").html(results);
     else 
      $("#drop-box").slideUp();
    }
  })
} 
else 
  $("#drop-box").slideUp();
})

$(document).on('click','.list_items',function(){

   var id = $(this).attr('data');
   var txt = $(this).html();

   $('#names').val(txt).css('text-transform','capitalize');
   $('#member_id').val(id);

   //displays search form
    var form_data = $('#search-form').html();

    $("#search_wrap").css({"border-left":"solid 1px #225C97","border-right":"solid 1px #225C97","border-top":"solid 1px #225C97"});
    $("#drop-box").css({"display":"block","background":"#FFF","overflow":"hidden","max-height":"500px"}).html(form_data);
    $('#open_search').attr('id','close-search');
 })

$(document).on('click','#open_search',function(){
  var usr = $(this).attr("data-usr");
  var form_data = $('#search-form').html();
  
   $("#drop-box").slideDown('slow').html(form_data);
    var close = '<img src="../img_file/search.png" width="18px" height="18px" id="close-search" style="cursor:pointer;" />';
    $("#search_icon").html(close);    
})

$(document).on('click','#close-search',function(){
      $("#drop-box").slideUp('slow').css("display","none");
   var open_search = '<img src="../img_file/search.png" width="18px" height="18px" id="open_search" style="cursor:pointer;" />';
      $("#search_icon").html(open_search);
})

$(document).on('click','#summary',function(){

   var summary_details  = $('#rpt_summary').html();
   var header = $('.report_wrap').html();
   var tmp_header = $('#header_wrap').html();

    $('#myModal_2').css({'display':'block'},{'z-index':'10'}).html('<div class="modal-spin-wrap"><div class="modal-img-spin"><img src="../img_file/loading.gif" /></div></div>')

    setTimeout(function(){

       $('#myModal_2').css('display','none');
       $('#myModal').css('display','block');
       $('.modal-content').toggleClass('modal-small-size')
       $('#display').html('<div style="width:100%">'
           +'<div style="text-align:right;margin:35px 0 5px;">'
            +'<span style="border-radius: 5px;background-color:#CCC;padding:5px;margin-right:10px;cursor:pointer;" id="gen_pdf">Generate PDF</span></div>'
            +'<div id="modal_header"></div>'
            +'<div id="summary_content"></div>'
        +'</div>');
         $('#modal_header').html(tmp_header+header);
         $('#summary_content').html(summary_details).css({'margin':'10px auto','width':'70%'});
         $('#summary').css('display','none');
    },1000)
})

$(document).on('change','select[name="select_action"]',function(){

    let val = $(this).val();
    let split = val.split('_');
    let modal = $('#myModal').html();
  
  if(split[0]=='edit'){
      $.ajax({
         type: 'POST',
         url: '../data_files/form_edit.php',
         data:{
           'edit_loan_payment': split[1]
         },
       beforeSend:function(){
        $('#myModal').css('display','block').html('<div class="modal-spin-wrap"><div class="modal-img-spin"><img src="../img_file/loading.gif" /></div></div>')
        },
       success:function(d){

         $('#myModal').html(modal);
         $('.modal-content').toggleClass('modal-small-size');
         $('#display').html(d);
       }
    })
   }

 if(split[0]=='delete'){

  if(confirm('Do You Want To Delete...?')){
     $.ajax({
         type: 'POST',
         url: '../data_files/data_src.php',
         data:{
           'del_loan_payment': split[1]
         },
       beforeSend:function(){
        $('#myModal').css('display','block').html('<div class="modal-spin-wrap"><div class="modal-img-spin"><img src="../img_file/loading.gif" /></div></div>')
        },
       success:function(d){

         $('#myModal').html(modal);
          $('.modal-content').toggleClass('modal-small-size');
           del_payment(d,split[1]);
       }
    })
  }
}

//return loan receipt
    if(split[0]=='receipt'){
     
      $('#myModal').css('display','block').html('<div class="modal-spin-wrap"><div class="modal-img-spin"><img src="../img_file/loading.gif" /></div></div>')
      setTimeout(function(){
        $('#myModal').html(modal)
        $('.modal-content').toggleClass('modal-min-size');
        display_receipt(split[2],split[1]);
      },1000) 
  }
})

$(document).on('keyup','#pay', function(){
 
 var myVal = "";
 var myDec = "";

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
      $("#pay").val(amt_split[0]+myVal+myDec);
})

$(document).on('click','#submit', function(e){
   
   e.preventDefault();
    $.ajax({
        type: 'POST',
        url: '../data_files/send_email.php',
        data:$('#loan_pay_edit').serialize(),
        beforeSend:function(){
        $('#myModal_2').css({'display':'block','z-index':'10'}).html('<div class="modal-spin-wrap"><div class="modal-img-spin"><img src="../img_file/loading.gif" /></div></div>')
        },
        success:function(data){
          console.log(data);
          var content = $('#success_wrap').html()
          $('.modal-content').toggleClass('modal-min-size')
           $('#display').html(content)
            $('#myModal_2').css('display','none');
        }
    })
})

$(document).on('click','#delete', function(e){
   
   e.preventDefault();
    $.ajax({
        type: 'POST',
        url: '../data_files/send_email.php',
        data:$('#del_pay').serialize(),
        beforeSend:function(){
        $('#myModal_2').css({'display':'block','z-index':'10'}).html('<div class="modal-spin-wrap"><div class="modal-img-spin"><img src="../img_file/loading.gif" /></div></div>')
        },
        success:function(data){
         var content = $('#success_wrap').html()
         $('.modal-content').toggleClass('modal-min-size')
          $('#display').html(content)
           $('#myModal_2').css('display','none');
           console.log(data);
        }
    })
})

function del_payment(status,id){

  if(status!='success'){
    $.ajax({
       type: 'POST',
       url: '../data_files/form_edit.php',
       data:{
         'confirm_del': id
       },
       success: function(data){
         $('#display').html(data);
       }
    })
  } else {
    $.ajax({
      type: 'POST',
      url: '../data_files/send_email.php',
      data:{
        'loan_id': id,
        'data_manage':'delete'
      },
      success: function(d){
         var content = $('#sucess_wrap').html();
         $('#display').html(d)
      }
    })
  }
}

function display_receipt(loan,pay_id){

   $.ajax({
      type:'POST',
      url: '../data_files/data_src.php',
      data:{
        'get_receipt':loan,
        'pay_id':pay_id
      },
      success:function(d){
        $('#display').html(d);
      }
   }) 
}

function seperator(index){

   var myVal="";
   var myDec="";
   var index_val="";
   var amt_split = index.toString();

      // Filtering out the trash!
        amt_split = amt_split.replace(/[^0-9]/g,"");

  // Adding the thousand separator
        while(amt_split.length > 3 ) {
            myVal = ","+amt_split.substr(amt_split.length-3, amt_split.length )+ myVal;
            amt_split= amt_split.substr(0, amt_split.length-3);           
          }
        index_val = (amt_split + myVal + myDec); 
    return index_val;
}

function remove_filter(index){
  
  let int_val = index.toString();
  let new_int=0;
  // Filtering out the seperator!
  int_val = int_val.replace(/[^0-9]/g,"");
  return parseFloat(int_val);
}

</script>
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
    <div class="grid-2" style="display: grid;">
        <div></div>
        <div style="text-align: right;">
             <!--Search Wrapper -->
                <form name="form1" method="post" action="loan_payments.php" id="form1">
                  <input type="hidden" name="post_search" value="1" />
                    <div style="width:100%;height:40px;display: grid;grid-template-columns: 2fr 1fr;">
                     <div style="height:40px;border:solid 1px #CCC;background-color:#fff;width: inherit;" id="drop_wrapper">
                        <input type="text" class="search_text" name="name_search" placeholder="Search Client" id="name_search" autocomplete="off" data-src="clients" />
                          <img src="../img_file/search.png" id="open_search" data-usr="" width="18px" height="18px">
                        <input type="hidden" name="member_id" value="" id="member_id">
                      <div id="drop-box" class="drop_down drop_large_size"></div>
                   </div> 
                 <input type="submit" name="search" value="Search" class="button_search">
              </div>                      
           </form>
       </div>
    </div>
    <?php    
            
    $check_date = date('Y-m-d');
    
    if($_POST['post_search']){

     $search_date = $_POST['search_date'];
      if($search_date)
        $check_date = date('Y-m-d',strtotime($search_date));

        $branch = $_POST['branch_details'];
        $pay_status = $_POST['pay_status'];
        $search_names = $_POST['mem_names'];
    }  

    $start_date = date_set_back($check_date,37);

    if($_GET['id']){

       $loan_pay_id = $_GET['id'];
       $loan = $_GET['loan'];

        $s = mysqli_query($connect,"SELECT c.id, CONCAT(c.first_name,' ', c.last_name) as 'client_name' FROM clients c, loan_entries l, loan_payments p WHERE c.id = l.client AND l.id = p.loan AND p.id='$loan_pay_id' ");
         $r = mysqli_fetch_array($s);
         $search_names = $r['client_name'];
    }   

    $search='';
     if($check_date)
       $search .= ', '.date('d/m/Y',strtotime($check_date));
      if($search_names)
         $search .= ', '.$search_names;
       if($branch){
              $q = mysqli_query($connect,"SELECT * FROM branches WHERE id='$branch' ");
                $rows = mysqli_fetch_array($q);
                 $search .= ', '.$rows[1];
           }
          if($pay_status){
              if($pay_status=='01')
                 $search .= ', Non Defaulters ';
                if($pay_status=='00')
                   $search .= ', Defaulters ';
          }
 

  $qry = "SELECT c.id, CONCAT(c.first_name,' ', c.last_name) as 'client_name', l.loan_amount, l.interest, l.period, l.date_entry, l.duration, l.id as 'loan', l.status, b.branch_name, c.data_id FROM clients c LEFT JOIN loan_entries l ON c.id = l.client LEFT JOIN branches b ON b.id = c.branch_id ";
             if(!$_SESSION['general_user'])
                       $qry .=" LEFT JOIN user_log u ON c.branch_id = u.user_branch ";
                  $qry .= " WHERE l.id != 'NULL' AND l.date_entry BETWEEN '$start_date' AND '$check_date' AND  ";
               if(!$_SESSION['general_user'])
                    $qry .= "u.id='".$_SESSION['session_id']."' AND ";
                   if($branch)
                       $qry .= " b.id = '$branch' AND ";
                     if($search_names)
                        $qry .= " CONCAT(c.first_name,' ', c.last_name) LIKE '%$search_names%' AND ";
                      if($loan)
                          $qry .= " l.id = '$loan' AND ";
                  $qry .= " 1 ORDER BY l.date_entry DESC ";

       $sql = mysqli_query($connect,$qry);
     ?>
     <div class="report_wrap">
            <div id="header_wrap">
              <div id="header_tpl"><?php echo po_address($connect) ?></div>
           </div>               
               <div class="report_header" style="align-items: center;">
                <span>Loan Payments Activity <?php echo $search ?></span>
                <span style="display:inline-block;text-align: right;font-size: 12px;font-weight: normal;">
                  <span id="get_report" style="display: inline-block;margin-right: 10px;border-radius: 5px;background-color: #ccc;padding: 5px;cursor: pointer;">Generate PDF</span>
                  <span  style="display: inline-block;margin-right: 10px;border-radius: 5px;background-color: #ccc;padding: 5px;cursor: pointer;"><a href="../export/export_payments.php<?php if(isset($_POST)){ echo '?'. 'branch=' . $branch . '&pay_status=' . $_POST['pay_status'] . '&mem_names=' . $_POST['mem_names'] . '&search_date=' . $_POST['search_date']; } ?>" style="text-decoration: none; color:#000; ">Export Data</a></span>
                  <span id="print_rpt">
                    <span>Print</span>
                    <span><img src="../img_file/print-icon.svg" width="20" height="20"></span>
                  </span>
                </span>
               </div>
          </div>

            <div id="rpt_summary" style="width:40%;margin: 10px 0 20px;">
              <div class="summary-grid">
                  <div style="font-weight:bold">Total Number</div>
                  <div id="tot_count"><span><img src="../img_file/loader.gif" /></span></div>
              </div>
              <div class="summary-grid">
                  <div style="font-weight:bold">Number of Defaulter(s)</div>
                  <div id="summary_default"><span><img src="../img_file/loader.gif" /></span></div>
              </div>
              <div class="summary-grid">
                  <div style="font-weight:bold">Number of Non Defaulter(s)</div>
                  <div id="summary_non_default"><span><img src="../img_file/loader.gif" /></span></div>
              </div>
              <div class="summary-grid">
                  <div style="font-weight:bold">Expected Total Collection</div>
                  <div id="summary_collection"><span><img src="../img_file/loader.gif" /></span></div>
              </div>
              <div class="summary-grid">
                  <div style="font-weight:bold">Total Amount Paid</div>
                  <div id="summary_paid"><span><img src="../img_file/loader.gif" /></span></div>
              </div>
              <div class="summary-grid">
                  <div style="font-weight:bold">Total Balance Due</div>
                  <div id="summary_bal"><span><img src="../img_file/loader.gif" /></span></div>
              </div>
              <div class="summary-grid">
                <div style="font-weight:bold"></div>  
                <div><span style="border-radius:5px;background-color: #CCC;padding:5px;width:auto;cursor: pointer" id="summary">Get Summary</span>
              </div>
            </div>
          </div>
             <table align="center" cellpadding="5" cellspacing="0" width="100%" class="report_display">
                 <tr>
                    <td>No</td>
                    <td>Member No</td>
                    <td>Member</td>
                    <td>Branch</td>
                    <td>Loan Date</td>
                    <td>Loan Amount</td>
                    <td>Date</td>                          
                    <td>Amount Paid</td>
                    <td>Period Bal</td>
                    <td>Status</td>
                    <td>Running Bal</td>
                    <td></td>
                 </tr>
              <?php
              
                  $count = 0;
                  $general_pay = 0;
                  $tot_loan = 0;
                  $tot_paid = 0;


              if(mysqli_num_rows($sql)){
                    while($r = mysqli_fetch_array($sql)){
                      $status = '';
                      $return_row = '01';
                      $tot_loan += $r['loan_amount']; 
                      $paid = 0; 
                      $count += 1;
                      $prevPay = 0; 
                      
                      //Return Branch Name Initials
                        $split = explode(' ',$r['branch_name']);
                        $branch_init = '';
                         foreach($split as $key){
                          $branch_init .= substr($key,0,1);
                         }

                  $qry=mysqli_query($connect,"SELECT * FROM loan_payments WHERE loan='".$r['loan']."' AND pay_date >= '".date('Y-m-d',strtotime($r['date_entry']))."' AND pay_date <= '".date('Y-m-d',strtotime($check_date))."'  ");
                          if(mysqli_num_rows($qry)){
                            while ($rs = mysqli_fetch_array($qry)) {
                              $prevPay += $rs['amount_paid'];
                            }
                          }

                  $q_str = "SELECT p.pay_date, p.amount_paid  FROM loan_entries l , loan_payments p WHERE l.id = p.loan AND l.id='".$r['loan']."' AND pay_date='$check_date' ";
                   $q = mysqli_query($connect,$q_str);
                   if(mysqli_num_rows($q)){
                      $rw = mysqli_fetch_array($q);
                      $paid = $rw['amount_paid'];
                      $total_pay += $paid;
                      $tot_paid += 1;
                   } else {
                      $tot_due += 1;
                   }
                  
                  //period status
                  $elapsePeriod = elaspe_period($r['date_entry'],$check_date, $r['duration']);

                  // pmt value
                  $pmt = round(($r['loan_amount'] * ($r['interest']/100)+$r['loan_amount']) / $r['period']);
                  
                  $accBal = ($pmt * $elapsePeriod); // return current date running bal

                  //return payment status
                  if(($pmt * $elapsePeriod) - $prevPay < 0){
                      if($paid){
                        $status = 'Paid';
                      }else{
                        $paid = $pmt;
                        $status = 'Advance';                        
                      }
                        $pmt = 0;
                  }else if(($pmt * $elapsePeriod) > $prevPay){
                     //
                      if($paid){
                         $status='Paids';
                         $pmt = $pmt - $paid;//($pmt * $elapsePeriod) - $prevPay;
                        }else{
                          if(($pmt * $elapsePeriod) - $prevPay > 0){
                            $pmt =  (($pmt * $elapsePeriod) - $prevPay);
                            $status = 'Due Balance';
                          }else{
                            $status = 'Due Balancez';
                          }                         
                      }
                  } else{
                    $pmt = 0;
                    $status='-';
                  }
                   
                ?>
                    <tr>
                        <td><?php echo $count ?></td>
                        <td><?php echo $r['data_id'] ?></td>
                        <td style="text-transform: capitalize;"><?php echo $r['client_name'] ?></td>
                        <td><?php echo $branch_init ?></td>
                        <td><?php echo date('d/m/y',strtotime($r['date_entry']))?></td>
                        <td><?php echo number_format($r['loan_amount']) ?></td>
                        <td><?php echo date('d/m/y',strtotime($check_date)); ?></td>
                        <td><?php echo number_format($paid) ?></td>
                        <td><?php echo number_format($pmt); ?></td>
                        <td><?php echo $status ?></td>  
                        <td><?php echo number_format($accBal); ?></td>                       
                        <td><select name="select_action" id="action_<?php echo $count ?>" class="text-input" style="width:80px;">
                            <option value="">Action</option>
                            <option value="edit_<?php echo $row[0] ?>">Edit</option>
                            <option value="receipt_<?php echo $row[0].'_'.$r['loan'] ?>">View Receipt</option>
                            <option value="delete_<?php echo $row[0] ?>">Delete</option>
                          </select></td>
                  </tr>
                <?php
              }   
          }else {
              ?>
                <tr>
                   <td colspan="11"><div style="height:90px;width:100%">No Record(s) Found</div></td>
                </tr>
          <?php
          }
       ?>
            <tr style="font-weight: bold;">
              <td colspan="5">Total</td>
              <td id="total_loan"><?php echo number_format($tot_loan) ?></td>
              <td></td>
              <td id="total_pay"><?php echo number_format($total_pay) ?></td>
              <td id="total_bal"><?php echo number_format($tot_bal) ?></td>
              <td colspan="2"><span id="summary_stats" data-paid="<?php echo $tot_paid ?>" data-due="<?php echo $tot_due ?>"></span></td>
            </tr>
          </table>
        </div>
    <?php
      echo footer_sec(); //footer section
    ?>
  </div>

<!-- returns seach form elements -->
<div id="search-form">
    <div class="form_element">
     <input type="text" id="datetimepicker" name="search_date" value="" class="text-input" placeholder="yyyy/mm/dd" />
    </div>
    <?php
      if($_SESSION['general_user']) { ?>
<div class="form_element">
  <select name="branch_details" class="text-input">
    <option value="" selected="selected">Search Branch</option>
     <?php
       $sql = mysqli_query($connect,"SELECT * FROM branches");
         if(mysqli_num_rows($sql)){
            while($r = mysqli_fetch_array($sql)){
              if($r[1])
               echo '<option value="'.$r['id'].'">'.$r['branch_name'].'</option>';
           }
        }
      ?>
   </select>
</div><?php } ?>
    <div class="form_element" style="font-size:13px;">
     <input type="radio" name="pay_status" class="input_layout" id="radio1" value="01" />&nbsp;Paid<br>
     <input type="radio" name="pay_status" class="input_layout" id="radio2" value="00" />&nbsp;Over-due<br>
     </div>
</div>
  </body>
</html>