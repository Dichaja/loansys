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
 
//closes dialog box 
$(document).on('click','.pop',function(){

 var myModal = $('#myModal');
     myModal.css("display","block");
 var form_display = $("#loan").html();
 var period = $(this).attr("loan-period");
 var data_pop = $(this).attr("data");
 var index  = data_pop.split("_");

    $.ajax({
        type:"POST",
        url:"../data_files/data_src.php",
        data:{
          'get_loan':index[0],
          'period':period
        },
        success:function(data){
          $("#display").html(data);
            $("#client_id").val(index[0]);
        }
    });
     
})

$(document).on('click','.loan',function(){
  var id = $(this).attr('id');
  var loan = $(this).attr('data-set');
  var pay_form = $('#details').html();

  $("#loan").removeClass('loan').html("Pay Loan");
  
  $.ajax({
    type:'POST',
    url:'../data_files/data_src.php',
    data:{
      'armotize':loan
    },
    success:function(data){
      $("#loan").addClass('loan2');
      $("#details").html(data);
    }
  });
});

$(document).on('click','.loan2',function(){
  var loan = $(this).attr('data-set');
  $.ajax({
    type:'POST',
    url:'../data_files/data_src.php',
    data:{
      'get_loan':loan
    },
    success:function(data){
      $("#display").html(data);
    }
  })
})

$(document).on('click','#extend',function(){

  // Define the HTML template
const row = `
  <div class="grid-2 margin-2">
    <div>Loan Period</div>
    <div><input type="text" name="period" value="" class="text-input" id="period" /></div>
  </div>
  <div class="grid-2 margin-2">
    <div>Issue Date</div>
    <div><input type="text" name="new_date" class="text-input" id="datetimepicker" /></div>
  </div>
  <div class="grid-2 margin-2">
    <div>New Balance</div>
    <div><input type="text" name="loan_balance" class="text-input" id="loan_balance" /></div>
  </div>`;

 const due_bal = $('#due_bal').html();
// Show loading spinner in modal
$('#myModal_2')
  .css({ 'z-index': '10', 'display': 'block' })
  .html('<div class="modal-spin-wrap"><div class="modal-img-spin"><img src="../img_file/loading.gif" /></div></div>');

// Hide modal and perform subsequent actions after a delay
setTimeout(function() {

  $('#myModal_2').css('display', 'none');
  $("#extend_loan").html(row);
  $('#loan_balance').val(due_bal);
  $(".pay_data").css('display', 'none');
  $('#submit_loan').attr('id', 'submit_extend');
}, 500);

})

$(document).on('click','#apprehend',function(){
  $("#removeRow").css("display","none");
  $("#amount_entry").css('display','block');
});

$(document).on('change','select[name="select_action"]',function(){

  var loan_pop = $(this).val();
  var index = loan_pop.split("_");
  var period = $(this).attr("loan-period");
  var id = $(this).attr('id');
  var split = id.split("_");
  var user_type = $("#usr").val();
  var srch_val = $('#srch_val').val();
  var modal = $('#myModal').html();  

  if(index[1]=='loan'){ 

    $.ajax({
        type:"POST",
        url:"../data_files/data_src.php",
        data:{
          'get_loan':index[0],
          'srch_val': srch_val
        },
        beforeSend:function(){
          $('#myModal').css({'display':'block','z-index':'6'}).html('<div class="modal-spin-wrap"><div class="modal-img-spin"><img src="../img_file/loading.gif" /></div></div>')
         },
        success:function(data){
             $('#myModal').html(modal);
             $('.modal-content').removeClass('modal-small-size').addClass('modal-medium-size')
             $("#display").html(data);
          $(".close").attr("select",id);
        }
    })
  }

if(index[1]=='statement'){

   var client = $('#postClient_'+split[1]).val();

      $.ajax({
        type:'POST',
        url:'../data_files/data_src.php',
        data:{
          'pay_statement': client,
          'post_loan' : index[0]
        },
        beforeSend:function(){
          $('#myModal').css({'display':'block','z-index':'6'}).html('<div class="modal-spin-wrap"><div class="modal-img-spin"><img src="../img_file/loading.gif" /></div></div>')
         },
        success:function(data){

          $('#myModal').html(modal)
          $('.modal-content').removeClass('modal-content, modal-large-size').toggleClass('modal-small-size')
          $("#display").html(data);
          $("#spanModal").attr("select",id);
        }
      })
  }
  if(index[1]=='view'){

     $.ajax({
        type:"POST",
        url:"../data_files/data_src.php",
        data:{
          'loan_pay':index[0] 
        },
        beforeSend:function(){
          $('#myModal').css({'display':'block','z-index':'6'}).html('<div class="modal-spin-wrap"><div class="modal-img-spin"><img src="../img_file/loading.gif" /></div></div>')
         },
        success:function(data){
          $('#myModal').css('display','none');
          $("#rows_"+split[1]).after(data).slideDown('slow');
        }
    });
}

if(index[1]=='edit'){

  var loan_id;

     $.ajax({
        type:"POST",
        url:"../data_files/form_edit.php",
        data:{
          'edit_loan':index[0]
        },
        beforeSend:function(){
          $('#myModal').css({'display':'block','z-index':'6'}).html('<div class="modal-spin-wrap"><div class="modal-img-spin"><img src="../img_file/loading.gif" /></div></div>')
         },
        success:function(data){

          $('#myModal').html(modal)
          $('.modal-content').toggleClass('modal-medium-size').addClass('modal-small-size');
          $("#display").html(data);
          loan_id = $('#loan_id').val();
          $('#post_id').val(loan_id);
          $('#step_1').addClass('current-item');
          $('#step_2').addClass('current-item');
          $('#step_3').addClass('current-item');
        }
    });
}

if(index[1]=='delete'){

  var user = $('#user_type').val();
  
     $.ajax({
        type:"POST",
        url:"../data_files/data_src.php",
        data:{
          'delete_loan':index[0]
        },
        success:function(data){
          if(data=='1'){
            if(confirm("Already in Use. Do You Wish To Continue")){
                if(user=='data_clerk')
                   alert('Denied. Please Contact Admin...!')
                else
                  delete_loan(index[0]);
            }else{
              return false;
            }
          }else{
            if(confirm("Do You want to Delete..?")){
               delete_loan(index[0]);
            }else{
              return false;
            }
          }
        }

    });
  }

if(index[1]=='amortize'){

     $.ajax({
        type:"POST",
        url:"../data_files/amortize.php",
        data:{
          'loan2':index[0]
        },
        beforeSend:function(){
          $('#myModal').css({'display':'block','z-index':'6'}).html('<div class="modal-spin-wrap"><div class="modal-img-spin"><img src="../img_file/loading.gif" /></div></div>')
         },
        success:function(data){

          $('#myModal').html(modal)
          $('.modal-content').removeClass('modal-small-size').addClass('modal-medium-size')
          $("#display").html(data);
          $("#spanModal").attr("select",id);
        }
    })
  }

})


$(document).on('keyup','input[name="pay_loan[]"]', function(){
 
 var myVal = "",
     myDec = "",
     elem = $(this),
     amtVal = elem.val(),
     amt_split = amtVal.toString().split('.');

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
    elem.val(amt_split[0]+myVal+myDec);
});

function delete_loan(loan){
  $.ajax({
    type:"POST",
    url:"../data_files/data_src.php",
    data:{
      'confirm_loan_delete' : loan
    },
    success:function(data){
      if(data=='1'){
      alert("Successfull...!!!");
      location.replace("loan_activity.php");
      }else{
        alert("Something Went Wrong. Please Try Again...!!!");
        console.log(data);
      }
    }
  })
}


$(document).on('keyup','#loan_amount', function(){
 
 var myVal = "";
 var myDec = "";
 var attr = $(this).attr('id');
 var index_val = attr.split('_');
 var priceVal = $(this).val();

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
      $("#loan_amount").val(amt_split[0]+myVal+myDec);
})

$(document).on('click','#edit_loan',function(e){

    e.preventDefault();
  let form = $("#form2");
  let essentialForm = $("#essential").html();
  let errorFields = [];
  
  // Validate form fields
  if ($('input[name="name_search"]').val() === '') {
    errorFields.push('name_search');
  }
  if ($('#loan_amount').val() === '' || $('#loan_amount').val() === '0') {
    errorFields.push('loan_amount');
  }
  if ($('select[name="duration"]').val() === '') {
    errorFields.push('duration');
  }
  if ($('input[name="period"]').val() === '') {
    errorFields.push('period');
  }
  if ($('input[name="interest"]').val() === '') {
    errorFields.push('interest');
  }
  
  if (errorFields.length > 0) {
    // Apply error styling to the fields with errors
    errorFields.forEach(field => {
      $('#' + field).css('border', 'solid 1px #F00').focus();
    });
  } else {

    load_content = $('#loading_wrap').html();

    $.ajax({
       type:'POST',
       url: '../data_files/post_data.php',
       data: form.serialize(),
        beforeSend:function(){
        $('#myModal_2').css({'display':'block','z-index':'10'}).html('<div class="modal-spin-wrap"><div class="modal-img-spin"><img src="../img_file/loading.gif" /></div></div>')
      },
      success:function(d){

         $('.modal-content').removeClass('modal-small-size').toggleClass('modal-min-size');
          $('#myModal_2').css('display','none');
           if(d.trim()=='success'){
               content = $('#success_wrap').html();
             }else{
             content = $('#error_wrap').html();
             console.log(d)
           }
         $('#display').html(content);
      }
    })
  }
})

$(document).on('click','#submit_loan',function(e){

  e.preventDefault();
  var val,id,split,statusEntry;

   $('input[name="pay_loan[]"]').each(function(){
       val = $(this).val();
       id = $(this).attr('id');
       split = id.split('_');
        if(val){
           if($('#mop_'+split[1]).val()==''){
              statusEntry = split[1];
           }
        }
   })

 if(!statusEntry){
   var loan = $('input[name="loan"]').val();
     $.ajax({
        type: 'POST',
        url: '../data_files/post_data.php',
        data:$('#loans').serialize(),
        beforeSend:function(){
          $('#myModal_2').css({'z-index':'7','display':'block'}).html('<div class="modal-spin-wrap"><div class="modal-img-spin"><img src="../img_file/loading.gif" /></div></div>')
         },
         success:function(d){

          let date = $('#datetimepicker').val();
          let amt_paid = $('#pay').val();
          let client = $('#client_loan').val(), loan = $('#loan_id').val();

          $('#display').html('');
          $('#myModal_2').css('display','none');
          $('.modal-content').toggleClass('modal-min-size');

          var split = d.split('_');
           if(split[0].trim()=='success'){
            display_receipt(loan,split[1]);
            console.log(d)
           }else if(d=='duplicate'){
              
             content = $('#warning_wrap').html();
             $('#display').html(content);             
             $('#display div').css('width','80%')
             $('#warning_msg').html('<span style="display:inline-block;width:100%">Payment Details Already Exist. Do You wish to Continue...!</span><div style="display:grid;grid-template-columns: repeat(2,1fr);gap:10px;width:35%;margin:15px auto;"><span id="yes_dup" data-loan="'+loan+'" data-client="'+client+'" data-paid="'+amt_paid+'" data-date="'+date+'">Yes</span><span id="no_dup">No</span></div>');
           }else{
             content = $('#error_wrap').html();
             $('#display').html(content);
           }
        }
    })
  }else{
    $('#mop_'+statusEntry).css('border','solid 1px #F00').focus();
  }
})

$(document).on('click','#submit_extend',function(e){
    
    e.preventDefault()
      $.ajax({
         type: 'POST',
         url: '../data_files/post_data.php',
         data: $('#loans').serialize(),
         beforeSend: function(){

             $('#myModal_2')
                  .css({ 'z-index': '10', 'display': 'block' })
                  .html('<div class="modal-spin-wrap"><div class="modal-img-spin"><img src="../img_file/loading.gif" /></div></div>');
         },
         success: function(d){
            $('.modal-content').removeClass('modal-medium-size').toggleClass('modal-min-size');
               $('#myModal_2').css('display','none');
                 if(d.trim()=='success'){
                      content = $('#success_wrap').html();
                  }else{
                     content = $('#error_wrap').html();
                   }
              $('#display').html(content);
            console.log(d);
         }
      })
})

$(document).on('click','#yes_dup',function(){

    let client = $(this).attr('data-client'), loan = $(this).attr('data-loan'), amt_paid = $(this).attr('data-paid'), date = $(this).attr('data-date');
    $.ajax({
       type: 'POST',
       url: '../data_files/post_data.php',
       data:{
         'pay_loan': amt_paid,
         'loan_client': client,
         'loan' : loan,
         'pay_date' : date,
         'allow_dup' : 1
       },
        beforeSend:function(){
          $('#display').html('<div class="modal-spin-wrap"><div class="modal-img-spin"><img src="../img_file/loading.gif" /></div></div>')
       },
       success:function(d){
         var split = d.split('_');
           if(split[0].trim()=='success'){
               display_receipt(loan,split[1]);
             }else{
               content = $('#error_wrap').html();
               $('#display').html(content);
           }
       }
    })
})

$(document).on('click','#no_dup', function(){
    var loc = window.location.href;
    window.open(loc,'_self');
})

$(document).on('blur','input[name="pay_loan[]"]',function(){
    var elem = $(this),
        id = elem.attr('id'),
        split = id.split('_'),
        index = split[1],
        count = $('.rows').length,
        new_index = count+1,
        mop_data = $('#mop_'+split[1]).html();

    var row = (`<div class="grid-6-items row_block rows" id="rows_${new_index}">
             <div>${new_index}</div>
             <div><input type="text" name="pay_date[]" id="datetimepicker" class="text-input" autocomplete="off" value="" placeholder="dd/mm/yyyy" /></div>
             <div><input type="text" name="pay_loan[]" class="text-input" id="pay_${new_index}" /></div>
             <div>
                <select name="mop[]" id="mop_${new_index}" class="text-input">
                </select>
              </div>
              <div>
                <select name="accTo[]" id="accTo_${new_index}" class="text-input"></select></div>
              <div><input type="text" name="accNo[]" class="text-input" id="accNo_${new_index}" value="" /></div>
            </div>
          </div>`);
    
    if($('#pay_'+count).val()!=''){
       $('#rows_'+count).after(row);
       $('#mop_'+new_index).html(mop_data);
    }

})

$(document).on('change','select[name="mop[]"]',function(){
   
    var val = $(this).val(),
        id = $(this).attr('id'),
        split = id.split('_'),
        selectText = $(this).find('option:selected').text();;

      $.ajax({
         type: 'POST',
         url : '../data_files/post_data.php',
         data:{
           'returnMop': val
         },
         success:function(d){
          if(selectText=='Cash'){
             $('#accTo_'+split[1]).html(`<option value="${val}">${selectText}</option>`)
          }else{
           $('#accTo_'+split[1]).html(d);
          }
       }
    })
})

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

</script>
</head>
<body>

<?php

$limit = 40;
$year = date('Y');            
//how many items to show per page

if($_POST['rows']){
      
      $client = $_POST['name_search'];
      $client_id = $_POST['client_id'];
      $month = $_POST['month'];
      $year = $_POST['year'];
      $pay_status = $_POST['pay_status'];
      $date = $_POST['date'];
      $date2 = $_POST['date2'];
      $page = $_POST['page'];
}

if($_POST['post_search']){
  $client_src = $_POST['name_search'];
  $client_id = $_POST['client_id'];
  $month = $_POST['month'];
  $year = $_POST['year'];
  $pay_status = $_POST['pay_status'];
  $date = $_POST['date'];
  $date2 = $_POST['date2'];
  $branch = $_POST['branch_details'];
}

if($_GET){

      $client_src = $_GET['client'];
      $client_id = $_GET['client_id'];
      $month = $_GET['month'];
      $year = $_GET['year'];
      $pay_status = $_GET['pay_status'];
      $date = $_GET['date'];
      $date2 = $_GET['date2'];
      $page = $_GET['page'];
      $limit=$_GET['limit']; 
      $branch = $_GET['branch']; 
      $loan_id = $_GET['loan'];
} 

if($_POST['edit_loan']){

  $id = $_POST['edit_loan'];
  $loan = str_replace(",", "", $_POST['loan_amount']);
  $interest = $_POST['interest'];
  $period = $_POST['period'];
  $duration = $_POST['duration'];
  $date = $_POST['date'];

  $update = mysqli_query($connect,"UPDATE loan_entries SET loan_amount='$loan',interest='$interest',period='$period',duration='$duration',date_entry='".date("Y-m-d",strtotime($date))."' WHERE id='$id' ");
  if(mysqli_affected_rows($connect)){
    echo '<script type="text/javascript">
       location.replace("loan_activity.php?action_msg=success&page='.$page.'&limit='.$limit.'&client='.$client.'&client2='.$client2.'&month='.$month.'&year='.$year.'&pay_status='.$pay_status.'&date='.$date.'");
     </script>';
  }else{    
    echo '<script type="text/javascript">
        location.replace("loan_activity.php?action_msg=err&reason='.$id.'&page='.$page.'&limit='.$limit.'&client='.$client.'&client2='.$client2.'&month='.$month.'&year='.$year.'&pay_status='.$pay_status.'&date='.$date.'");
      </script>';
  }
}


// Returns Title of Search Results
    $search = ''; 

         if($client_id){
              $sql = mysqli_query($connect,"SELECT * FROM clients WHERE id='$client_id'");
              $r = mysqli_fetch_array($sql);
              $search .= ", ".$r[1].' '.$r[2];
            }else if($client_src){
              $search .= $client_src . ', ';
            }
           if($date && $date2){
               $search .= ', '.date('d/m/Y',strtotime($date)).' To '.date('d/m/Y',strtotime($date2));
              }else if($date!='' && $date2==''){
                $search .= date('d/m/Y',strtotime($date)) . ', ';
                }
              if($month)
                  $search .= ' '.$month.', ';                
                if($year) 
                    $search .= ' '.$year.', ';                  
                  if($pay_status=='02')
                      $search .= 'Defaulter(s) Category, ';            
                   if($pay_status=='00')
                       $search .= 'Non Defaulter(s) Category, '; 
                      if($branch){
                           $sql = mysqli_query($connect,"SELECT * FROM branches WHERE id='$branch' ");
                                  $r = mysqli_fetch_array($sql);
                              $search .= ', '.$r[1];
                         } 

                         //End

if($_POST['pagination']){

         $page = $_POST['pagination'];
    }

$targetpage = "loan_activity.php";   //your file name  (the name of this file)

if($page) 
  $start = ($page - 1) * $limit;          //first item to display on this page
else
  $start = 0;                             //if no page var is given, set start to 0
   
$qry = "SELECT c.id, CONCAT(c.first_name,' ',c.last_name) as 'client_names', c.contacts, c.email, l.id as 'loan_id', l.loan_amount, l.interest, l.duration, l.period, l.date_entry, l.status, b.branch_name, c.data_id, l.loan_fees, CONCAT(s.first_name,' ',s.last_name) AS 'loan_officer' FROM clients c, loan_entries l LEFT JOIN staff s ON s.id = l.loan_officer, branches b ";
    if(!$_SESSION['general_user'])
       $qry .= ", user_log u ";
         $qry .= " WHERE c.id = l.client AND c.branch_id = b.id AND ";
          if(!$_SESSION['general_user'])
             $qry .= " u.user_branch = c.branch_id AND ";
            if($client_id)
               $qry .= " c.id = '$client_id' AND ";
             else if($client_src)
               $qry .= " CONCAT(c.first_name,' ',c.last_name) LIKE  '%".$client_src."%' AND ";              
          if($month != '')
               $qry .= " monthname(l.date_entry) = '".$month."' AND ";
            if($date !='' && $date2 !='')
                      $qry .= " l.date_entry BETWEEN '".date('Y-m-d',strtotime($date))."' AND '".date('Y-m-d',strtotime($date2))."' AND ";
              if($date != '' && $date2=='')
                     $qry .= " l.date_entry = '".date('Y-m-d',strtotime($date))."' AND ";
                if($year != '')
                   $qry .= " date_format(l.date_entry,'%Y') = '".$year."' AND ";
                     if($loan_id!='')
                         $qry .= " l.id = '$loan_id' AND ";
                     if($pay_status)
                         $qry .=  " l.status = '".$pay_status."' AND ";
                    if(!$_SESSION['general_user'])
                       $qry .= "u.id='".$_SESSION['session_id']."' AND ";
                     if($branch)
                         $qry .= " b.id = '$branch' AND ";
                  $qry2 .= $qry. " l.status!='03' AND 1 ";
                  $qry_tot .= $qry . ' 1';
              $qry .= " 1 ORDER BY l.date_entry DESC ";
              ($limit!='') ? $qry .= "LIMIT $start, $limit " : $limit = mysqli_num_rows(mysqli_query($connect,$qry_tot)) ;

   $query = mysqli_query($connect,$qry2);//total loans registered
     $total_pages = mysqli_num_rows(mysqli_query($connect,$qry_tot));//
     $total_disbursh = mysqli_num_rows($query);
       $result = mysqli_query($connect,$qry);
    /* Setup page vars for display. 
    if ($page == 0) $page = 1;  //if no page var is given, default to 1.
    $first=1;
    $prev = $page - 1;
    
    $lastpage = ceil($total_pages/$limit);      //lastpage is = total pages / items per page, rounded up. 

    //previous page is page - 1 
    if($page==$lastpage)
    $next=$lastpage;
    else
    $next = $page + 1; //next page is page + 1 

    $lpm1 = $lastpage - 1; //last page minus 1*/

    $last_page = ceil($total_pages / 7);//$limit); //divide total row count by limit size
      if($page==0)
        $page=1;
      //previous page is page - 1 
       if($page==$lastpage2){
           $next=$lastpage2;
         } else {
           $next = $page + 1; //next page is page + 1 
       }

       ?>
       <input type="hidden" id="srch_val" value="<?php echo $client.','.$client_id.','.$date.','.$date2.','.$month.','.$year.','.$pay_status ?>" />

   <!-- Main Content Wrapper -->
<div class="main_bd_wrap">
  
  <?php        
    tp_hdr(); //Page Header, Menu
       side_menu_content(); // Side Menu
      ?>
         <!-- Main Content Side-Right -->
           <div class="main-sidebar col-lg-9">
            <?php if($_GET){ include('../data_files/action_msg.php'); } ?>
             
   <div class="report_wrap">
        <div id="header_wrap">
          <div id="header_tpl"><?php echo po_address($connect) ?></div>
        </div>               
        <div class="report_header" style="align-items: center;">
                <span>Loans Performance Overview <?php echo $search ?></span>
                <div style="text-align: right;">
          <!--Search Wrapper -->
           <form name="form1" method="post" action="loan_activity.php" id="form1">
            <input type="hidden" name="post_search" value="1" />
             <div style="width:100%;height:40px;display: grid;grid-template-columns: 2fr 1fr;">
               <div style="height:40px;border:solid 1px #CCC;background-color:#fff;width:300px;" id="drop_wrapper">
                  <input type="text" class="search_text" name="name_search" placeholder="Search Client" id="name_search" autocomplete="off" data-src="clients" />
                      <img src="../img_file/search.png" id="open_search" data-usr="" width="18px" height="18px" />
                       <input type="hidden" name="client_id" value="" id="data_id" />
                      <div id="drop-box" class="drop_down drop_large_size"></div>
                   </div> 
                 <input type="submit" name="search" value="Search" class="button_search">
              </div>                      
           </form>
       </div>
     </div>
  </div>
<div class="report_wrap">
  <div style="font-size:12px;font-weight: normal;display: grid; grid-template-columns: 1fr 1fr;">
    <span>Loans Disbursed : <?php echo $total_disbursh ?></span>
    <span style="display: grid; grid-template-columns: 1fr 1fr;">
              <div style="width:100%;text-align:right;font-size:14px;">
                <span style="display:inline-block;text-align: right;font-size: 12px;font-weight: normal;">
                  <span id="get_report" style="display: inline-block;margin-right: 10px;border-radius: 5px;background-color: #ccc;padding: 5px;cursor: pointer;">Generate PDF</span>
                  <span id="print_rpt">
                    <span>Print</span>
                    <span><img src="../img_file/print-icon.svg" width="20" height="20"></span>
                  </span>
                </span>
              </div>
              <div style="width:100%;text-align:right;font-size:14px;">
                     <span style="padding:5px;">Page <?php echo $page ?> <b>of</b> <?php echo $last_page ?></span>
                     <span style="background:#ddd;padding:5px;border-radius:5px;margin-left:10px;font-size:12px;"><?php echo "<a href=\"$target_page?page=$next&limit=$limit&month=$month&year=$year&pay_status=$pay_status&date=$date&date2=$date2&branch=$branch\">Next</a>"; ?></span>
                     <?php 
                        if($page!=1) {
                            $prev = $page - 1; ?>
                     <span style="background:#ddd;padding:5px;border-radius:5px;margin-left:10px;font-size:12px;"><?php echo "<a href=\"$target_page?page=$prev&limit=$limit&month=$month&year=$year&pay_status=$pay_status&date=$date&date2=$date2&branch=$branch\">Back</a>"; ?></span>
                     <?php } ?>
                     <span style="background:#ddd;padding:5px;border-radius:5px;margin-left:10px;font-size:12px;"><?php echo "<a href=\"$target_page?page=1&limit=$total_pages&month=$month&year=$year&pay_status=$pay_status&date=$date&date2=$date2&branch=$branch\">View All</a>"; ?></span>
                  </div>
        </span>
    </div>
</div>
 
  <table align="center" cellpadding="5" cellspacing="0" width="100%" class="report_display">                
     <tr>
      <td>No</td>
      <td>Issue Date</td>
      <td>Id</td>
      <td>Client</td>
      <td>Branch</td> 
      <td>Loan Officer</td>     
      <td>Status (Days)</td>
      <td align="center">Loan</td>
      <td align="">Loan Fees</td>
      <td align="center">Payments</td>
      <td align="right">Balance</td>
      <td></td>
     </tr>
     <?php

       if(mysqli_num_rows($result)){

        $count=0;
        $total_loan=0;
        $total_pay=0;
        $total_bal=0;

        while($rw=mysqli_fetch_array($result)){

          $loan=(round($rw[6]/100,2)*$rw[5])+$rw[5];
          $acc_int=0;
          $bal=0;
          $overdue = "";
          $count += 1;
          $total_pay += $rw['amount_paid'];
          $total_fees += $rw['loan_fees'];

          //return church name initials
           $split = explode(' ',$rw['branch_name']);
           $branch_init = '';
            foreach($split as $key){
              $branch_init .= substr($key,0,1);
            } 
         
         
          if($rw['status']=='00' OR $rw['status']=='03'){
            $status_period=0;
          }else if(loan_status($connect,$rw['loan_id'],$status)<=0){
            mysqli_query($connect,"UPDATE loan_entries SET status='00' WHERE id = '".$rw['loan_id']."' ");
            $status_period=0;
          }else{
            $status_period = ($rw[8]-return_period($rw[9],$rw[7]));//returns period remain for loan
           }
          
        ?>
        <tr <?php if($rw['status']=='03'){ echo 'style="color:#F00;" '; } ?>>
         <td><?php echo $start+=1 ?></td>
         <td><?php echo date("d-m-Y",strtotime($rw[9])) ?></td>
         <td><?php echo $rw['data_id'] ?></td>
         <td style="text-transform:capitalize;"><?php echo strtolower($rw[1]) ?></td>  
         <td><?php echo $branch_init ?></td>
         <td><?php echo $rw['loan_officer'] ?></td>       
         <td><?php 
              if($status_period < 0 ){ 
                echo '<span style="color:#F00">'.$status_period.'</span>';
                  if($status_period >= -7)
                    mysqli_query($connect,"UPDATE loan_entries SET status='02' WHERE id = '".$rw['loan_id']."' ");
                  else
                    extend_loan($connect,$rw['loan_id']);
                    //mysqli_query($connect,"UPDATE loan_entries SET status='02' WHERE id = '".$rw['loan_id']."' ");
               }else{
                  echo '<span>'.$status_period.'</span>';
            } ?></td>
         <td align="right"><?php echo number_format($rw[5]) ?></td>
         <td align="right"><?php echo number_format($rw['loan_fees']) ?></td>
         <td align="right"><?php echo number_format(loan_status($connect,$rw['loan_id'],'payments')); $total_pay += loan_status($connect,$rw['loan_id'],'payments')  ?></td>
         <td align="right">
             <?php
                                  $total_loan_balance += loan_status($connect,$rw['loan_id'],$status);
                                  echo number_format(loan_status($connect,$rw['loan_id'],$status));
            ?></td>
      <td>
        <input type="hidden" name="clientId" id="postClient_<?php echo $count ?>" value="<?php echo $rw[0] ?>" />
        <select name="select_action" class="text-input" id="action_<?php echo $count ?>" style="width:80px;">
             <option value="" selected="selected">Action</option>
             <option value="<?php echo $rw[4] ?>_loan" >Pay Loan</option>
             <option value="<?php echo $rw[4] ?>_amortize">Preview Amortization</option>
             <option value="<?php echo $rw[4] ?>_statement">View Statement</option>
             <?php if(!$rw['amount_paid']){
               //edit only loan has no payment
              ?>
               <option value="<?php echo $rw[4] ?>_edit" id="edit2_<?php echo $count?>">Edit Loan</option>
               <option value="<?php echo $rw[4] ?>_delete" id="delete_<?php echo $count?>">Delete</option>
              <?php } ?>
          </select>
         </td>
       </tr>
        <?php
        if($rw['status']!='03')
            $total_loan += $rw[5];          
          $total_bal += $bal; 
        }
        ?>
         <tr style="font-weight:bold;">
           <td colspan="7">Total</td>
           <td align="right"><?php echo number_format($total_loan) ?></td>
           <td align="right"><?php echo number_format($total_fees) ?></td>
           <td align="right"><?php echo number_format($total_pay) ?></td>
           <td align="right"><?php echo number_format($total_loan_balance) ?></td>
           <td></td>
         </tr>
        <?php
       }else{
          ?>
          <tr>
            <td style="height:100px;width:70%" colspan="12">
             No Result(s) Found.
            </td>
          </tr>
          <?php
        }
      ?>
       <tr>
          <td colspan="12">
           <div style="width:100%;text-align:right;font-size:14px;">
                     <span style="padding:5px;">Page <?php echo $page ?> <b>of</b> <?php echo $last_page ?></span>
                     <span style="background:#ddd;padding:5px;border-radius:5px;margin-left:10px;font-size:12px;"><?php echo "<a href=\"$target_page?page=$next&limit=$limit&month=$month&year=$year&pay_status=$pay_status&date=$date&date2=$date2&branch=$branch\">Next</a>"; ?></span>
                     <?php 
                        if($page!=1) {
                            $prev = $page - 1; ?>
                     <span style="background:#ddd;padding:5px;border-radius:5px;margin-left:10px;font-size:12px;"><?php echo "<a href=\"$target_page?page=$prev&limit=$limit&month=$month&year=$year&pay_status=$pay_status&date=$date&date2=$date2&branch=$branch\">Back</a>"; ?></span>
                     <?php } ?>
                     <span style="background:#ddd;padding:5px;border-radius:5px;margin-left:10px;font-size:12px;"><?php echo "<a href=\"$target_page?page=1&limit=$total_pages&month=$month&year=$year&pay_status=$pay_status&date=$date&date2=$date2&branch=$branch\">View All</a>"; ?></span>
                  </div>
           </td>
       </tr>
    </table>
  </div>
</div>

<!-- Search Form -->
<div id="search-form">
    <div class="form_element">
  <input type="text" name="date" placeholder="Date From" class="text-input" id="datetimepicker" autocomplete="off" />
    </div>
    <div class="form_element">
     <input type="text" name="date2" placeholder="Date To" class="text-input" id="picker" autocomplete="off" />
    </div>
    <div class="form_element">
      <select name="month" id="month" class="text-input">
        <option selected="selected" value="">Search Month</option>
         <?php 
          $array_month = array('January','February','March','April','May','June','July','August','September','October','November','December');
          foreach($array_month as $val){
            echo '<option value="'.$val.'">'.$val.'</option>';
          }
        ?>
      </select>
    </div>
    <div class="form_element">
     <input type="text" name="year" placeholder="Year" class="text-input" id="year" autocomplete="off" />
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
</div>
<?php } ?>

 <div class="form_element" style="font-size: 13px;">
     <input type="radio" name="pay_status" class="input_layout" id="radio1" value="02" />&nbsp;&nbsp;Defaulters
     <br>
     <input type="radio" name="pay_status" class="input_layout" id="radio2" value="00" />&nbsp;&nbsp;Non Defaulters
     <br>
     <input type="radio" name="pay_status" class="input_layout" id="radio2" value="03" />&nbsp;&nbsp;Extended Loans
    </div>
</div>
    <?php
      echo footer_sec(); //footer section
    ?>
  </div>
  </body>
</html>