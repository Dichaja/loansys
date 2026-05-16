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
    <?php include('../data_files/link_docs.php') ?>
   <title><?php echo sys_tab_hdr() ?></title>

<script type="text/javascript">

//closes dialog box
$(document).on('click','.close',function(){
  
  var loan = $("#loan_id2").val();
  if(loan==''){
    $(".modal").css("display","none");
    location.replace("client_list.php");
  }else{
    if(confirm("Do Wish to Cancel Loan...!!!")){
      $.ajax({
        type:'POST',
        url:'../data_files/amortize.php',
        data:{
          'delete_loan':loan
        },
        success:function(data){
          if(data==1){
            alert("Successfull...!!");
            location.replace("client_list.php");
          }else{
            alert("Something Went Wrong. Please Try Agan..!!!");
            location.replace("client_list.php");
          }
        }
      });
    }else{
      return false;
    }
  }
  
});

$(document).on('click','#submit',function(e){
   
  e.preventDefault();
  let content, load_content;
  var form = $('#edit_mem')[0];
  var formData = new FormData(form);
    $.ajax({
       type: 'POST',
       url: '../data_files/post_data.php',
       processData: false,
       contentType: false,
       data: formData,
       beforeSend:function(){
         load_content = $('#loading_wrap').html();
         $('.modal-content').removeClass('modal-small-size').toggleClass('modal-min-size');
         $('#display').html(load_content)
       },
       success:function(d){
         if(d)
          content = $('#success_wrap').html();          
            $('#display').html(content);
       }
    })
})

$(document).on('change','#duration',function(){

   let val = $(this).val();
     if(val=='day'){
         $('#period').val(30);
         $('#interest').val(20);
     }else{
       $('#period').val('');
       $('#interest').val('');
     }
})

/*
$(document).on('click','#submit_loan',function(event){

  event.preventDefault();
  let essential_form = $("#essential").html(),
      text_val = 0,
        no_val=0;
  if($('input[name="name_search"]').val()==''){
     non_val = $('input[name="name_search"]').attr('id');
     text_val=1;
  }
  if($('#loan_amount').val()=='0' || $('#loan_amount').val()==''){
     non_val = $('#loan_amount').attr('id');
     text_val=1;
  }
  if($('select[name="duration"]').val()==''){
      non_val=$('select[name="duration"]').attr('id');
      text_val=1;
  } 
  if($('input[name="period"]').val()==''){
      no_val=$('input[name="period"]').attr('id');
      text_val=1;
  }
  if($('input[name="interest"]').val()==''){
     non_val = $('input[name="interest"]').attr('id');
     text_val=1;
  }
  
  
  if(text_val==1){
     $('#'+non_val).css('border','solid 1px #F00').focus();
  }else{

  $.ajax({
    type:"POST",
    url:"../data_files/data_src.php",
    data:$("#form2").serialize(),
    beforeSend:function(){
     $('#myModal_2').css({'z-index':'10','display':'block'}).html('<div class="modal-spin-wrap"><div class="modal-img-spin"><img src="../img_file/loading.gif" /></div></div>')
    },
    success:function(d){
      $('#myModal_2').css('display','none');
          
      var split = d.split("_");

      if(split[0]==1){
            $('#step_1').toggleClass('current-item');
            $('#step_2').addClass('current-item');
            $("#form_wrap").html(essential_form);
            $("#client_id2").val(split[2]);
            $("#loan_id").val(split[1]);              
          }else{
            alert("Un-successfull. Something Went Wrong...!!!");
          }
        $("#form2").trigger("reset");
     }
   })
  }
}) */

$(document).on('click', '#submit_loan', function(event) {
  event.preventDefault();
  
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
    $.ajax({
      type: "POST",
      url: "../data_files/data_src.php",
      data: form.serialize(),
      beforeSend: function() {
        $('#myModal_2').css({'z-index':'10','display':'block'}).html('<div class="modal-spin-wrap"><div class="modal-img-spin"><img src="../img_file/loading.gif" /></div></div>');
      },
      success: function(response) {
        $('#myModal_2').css('display','none');
        let [status, loanId, clientId] = response.split("_");
        
        if (status === '1') {
          $('#step_1').toggleClass('current-item');
          $('#step_2').addClass('current-item');
          $("#form_wrap").html(essentialForm);
          $("#client_id2").val(clientId);
          $("#loan_id").val(loanId);
        } else {
          alert("Unsuccessful. Something went wrong!");
          console.log(response);
        }
        
        form.trigger("reset");
      }
    });
  }
})


$(document).on('click','#security_button',function(event){

  event.preventDefault();
  var guarantor_form = $("#guarantor_content").html();

  $.ajax({
    type:"POST",
    url:"../data_files/data_src.php",
    data:$("#form").serialize(),
    beforeSend:function(){
     $('#myModal_2').css({'display':'block','z-index':'10'}).html('<div class="modal-spin-wrap"><div class="modal-img-spin"><img src="../img_file/loading.gif" /></div></div>')
    },
    success:function(d){
      $('#myModal_2').css('display','none');
           
      var split = d.split("_");

          if(split[0]==1){
            $('#step_2').toggleClass('current-item');
            $('#step_3').addClass('current-item');
            $("#form_wrap").html(guarantor_form);
            $("#client_id3").val(split[2]);
            $("#loan_id2").val(split[1]);                 
          }else{
            alert("Something Went Wrong.Please Try Again..!!!");
          }
      $("#form").trigger("reset");
    }
  })

})

$(document).on('click','#guarantor_button',function(event){

  event.preventDefault();
  var content='', load_content='';

  $.ajax({
     type:'POST',
     url:'../data_files/data_src.php',
     data:$("#guarantor").serialize(),
     beforeSend:function(){
         load_content = $('#loading_wrap').html();
         $('.modal-content').removeClass('modal-small-size').toggleClass('modal-min-size');
         $('#display').html(load_content)
       },
       success:function(d){
         if(d=='success'){
             content = $('#success_wrap').html();
           }else{
             content = $('#error_wrap').html();
           }
         $('#display').html(content);
       }
  })

})

$(document).on('change','select[name="select_action"]',function(){
 
  var modal = $('#myModal').html();
  var action_val = $(this).val();
  var select = action_val.split('_');

  if(select[0]=='view'){
    window.open('loan_activity.php?client_id='+select[1],'_self');
  }

  if(select[0]=='statement'){

      $.ajax({
        type:'POST',
        url:'../data_files/data_src.php',
        data:{
          'pay_statement': select[1]
        },
        beforeSend:function(){
          $('#myModal').css({'display':'block','z-index':'6'}).html('<div class="modal-spin-wrap"><div class="modal-img-spin"><img src="../img_file/loading.gif" /></div></div>')
         },
        success:function(data){

          $('#myModal').html(modal)
          $('.modal-content').removeClass('modal-content, modal-large-size').toggleClass('modal-small-size')
          $("#display").html(data);
        }
      })
  }

  if(select[0]=='pop'){ //assigns new loan to client
    
  var form_display = $("#loan_form").html();
     
   $.ajax({
      type: 'POST',
      url: '../data_files/data_src.php',
      data:{
        'check_loan': 1,
        'check_client': select[1] 
      },
      beforeSend:function(){
        $('#myModal_2').css({'display':'block','z-index':'6'}).html('<div class="modal-spin-wrap"><div class="modal-img-spin"><img src="../img_file/loading.gif" /></div></div>')
      },
      success: function(d){

       $('#myModal_2').css('display','none');
          $('#myModal').css('display','block');
          $('#myModal').html(modal);

          if(d!='success'){          
          
             $('.modal-content').removeClass('modal-large-size').toggleClass('modal-small-size');
             $("#display").html(form_display);
             $('#step_1').addClass('current-item')
             $("#client_id").val(select[1]);
          }else{

              var content = $('#warning_wrap').html();
              $('.modal-content').removeClass('modal-large-size').toggleClass('modal-min-size');
              $("#display").html(content);
          }
      }
  })              
}

if(select[0]=='edit'){
      $.ajax({
         type: 'POST',
         url: '../data_files/form_edit.php',
         data:{
           'edit_mem': select[1]
         },beforeSend:function(){
           $('#myModal_2').css({'display':'block','z-index':'6'}).html('<div class="modal-spin-wrap"><div class="modal-img-spin"><img src="../img_file/loading.gif" /></div></div>')
        },
        success:function(d){
            
            $('#myModal_2').css('display','none');
            $('#myModal').css('display','block').html(modal);
            $('.modal-content').removeClass('modal-large-size').toggleClass('modal-small-size');
            $('#display').html(d);
        }
      })
    }

  if(select[0]=='delete'){
    var user = $('#user_type').val();
    $.ajax({
        type:'POST',
        url:'../data_files/data_src.php',
        data:{
          'check_client':select[1]
        },
        success:function(data){
         if(confirm("Do You Wish to Delete...?")){
            if(data=='01'){
              if(confirm("Already in Use. Do You Wish To Continue")){
                if(user!='admin'){
                   alert('Denied. Please Contact Admin...!');
                 } else {
                   del('clients', select[1])
                 }
               }else{
                 return false
               }
             }else{
                del('clients', select[1]);
             } 
          }else{
            return false;
          }
        }
    })
  }
});


function del(tab, index){

  $.ajax({
           type:'POST',
           url: '../data_files/data_src.php',
           data:{
             'del_id': index,
             'del_tab':tab
      },
      beforeSend:function(){
       $('#myModal_2').css({'display':'block','z-index':'6'}).html('<div class="modal-spin-wrap"><div class="modal-img-spin"><img src="../img_file/loading.gif" /></div></div>')
        },
        success: function(d){
         location.replace('client_list.php?action_msg='+d);
      }
  })
}


$(document).on('click','#loan', function(event){
   var loan = $("#loan_id2").val();

   $.ajax({
      type:"POST",
      url:"../data_files/amortize.php",
      data: $("#guarantor").serialize(),
      success:function(response){
        $("#amortize").html(response);
      }
   })
});

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
            $("#loan_amount").val(amt_split[0]+myVal+myDec);
        }
})

$(document).on('blur','#edit_id',function(){
   var val = $('#edit_id').val();
     $.ajax({
       type: 'POST',
       url: '../data_files/data_src.php',
       data:{
         'check_mem_id': val
       },
       success:function(d){
         if(d=='success'){
           $('#edit_id').css('border','solid 1px #F00').focus()
           $('#err_msg').html('( Member Id Alreay in Use...! )');
           $('#submit').attr('id','disabled');
         }else{
           $('#edit_id').css('border','solid 1px #CCC');
           $('#err_msg').html('');
           $('#disabled').attr('id','submit');
         }
       }
    })
})

$(document).on('click','#disabled',function(){
  $('#edit_id').focus();
})

</script>
</head>

<body>


<?php

$limit = 40; //how many items to show per page

if($_POST['post_search']){
     $client = $_POST['name_search'];
     $address = $_POST['address'];
     $gender = $_POST['gender']; 
     $month = $_POST['month'];
     $year = $_POST['year'];
     $branch = $_POST['branch_details'];
     $client_id = $_POST['client_id'];
  }

  if($_GET['page']){
       $client = $_GET['client'];
       $address = $_GET['address'];
       $gender = $_GET['gender'];
       $client = $_GET['client']; 
       $page = $_GET['page'];
       $month = $_GET['month'];
       $limit = $_GET['limit'];
       $branch = $_GET['branch_details'];
       $year = $_GET['year'];
    }       

    $targetpage = "client_list.php";   //your file name  (the name of this file)    
    
    if($page) 
        $start = ($page - 1) * $limit;          //first item to display on this page
    else
        $start = 0;                             //if no page var is given, set start to 0

   
     $qry = "SELECT * FROM clients c, branches b ";
       if(!$_SESSION['general_user'])
          $qry .=", user_log u ";
            $qry .= " WHERE c.branch_id = b.id AND c.status='01' AND ";
          if(!$_SESSION['general_user'])
              $qry .= " c.branch_id = u.user_branch AND ";
        if($client !=''){
            if($client_id)
              $qry .= " c.id = '$client_id' AND ";
              else
                 $qry .= " CONCAT(c.first_name,' ',c.last_name) LIKE  '%$client%' AND ";
             }
         if($address!='')
                $qry .= " c.residence = '".$address."' AND ";
            if($month!='')
                $qry .= " monthname(c.date_created) = '".$month."' AND ";
             if($year!='')
                $qry .= " date_format(c.date_created,'%Y') = '".$year."' AND ";
               if($gender!='')
                  $qry .= " c.gender = '".ucfirst($gender)."' AND ";
                 if(!$_SESSION['general_user'])
                    $qry .= "u.id='".$_SESSION['session_id']."' AND ";
                   if($branch)
                     $qry .= " b.id = '$branch' AND ";
               $qry2 .= $qry.' 1 ';
             $qry .= " 1 ORDER BY c.first_name, c.last_name ASC LIMIT $start, $limit ";
             

    $query = mysqli_query($connect,$qry2); //total registered clients
     $total_pages= mysqli_num_rows($query); 

    $result = mysqli_query($connect,$qry);

    /* Setup page vars for display. */
    if ($page == 0) $page = 1;                  //if no page var is given, default to 1.
    $prev = $page - 1;                          //previous page is page - 1
    $next = $page + 1;                          //next page is page + 1
    $lastpage = ceil($total_pages/$limit);      //lastpage is = total pages / items per page, rounded up.
    $lpm1 = $lastpage - 1;

    $search = '';
       if($client)
         $search .= ', ' . $client;
        if($gender)
            $search .= ', '.$gender;
          if($month)
             $search .=  ', '.$month;
            if($year)
             $search .=  ', '.$year;
              if($branch){
                $sql = mysqli_query($connect,"SELECT * FROM branches WHERE id='$branch' ");
                   $r = mysqli_fetch_array($sql);
                     $search .= $r[1].', ';
                 }
?> 
   <!-- Main Content Wrapper -->
<div class="main_bd_wrap">
  
  <?php

    tp_hdr(); //Page Header, Menu
       side_menu_content(); // Side Menu
      ?>
         <!-- Main Content Side-Right -->
           <div class="main-sidebar col-lg-9">
<div style="width:95%;margin: auto;">
  <?php if($_GET){ include('../data_files/action_msg.php'); } ?>
  <div class="report_wrap">
        <div id="header_wrap">
          <div id="header_tpl"><?php echo po_address($connect) ?></div>
        </div>               
        <div class="report_header" style="align-items: center;">
                <span>Clients <?php echo $search ?></span>
                <div style="text-align: right;">
          <!--Search Wrapper -->
           <form name="form1" method="post" action="client_list.php" id="form1">
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
    <span>Entries : <?php echo $total_pages ?></span>
    <span style="display: grid; grid-template-columns: 1fr 1fr;">
              <div style="width:100%;text-align:right;font-size:14px;">
                <span style="display:inline-block;text-align: right;font-size: 12px;font-weight: normal;">
                  <span id="get_report" class="print_layout">Generate PDF</span>
                  <span class="print_layout"><a href="../export/export_clients.php" style="text-decoration: none; color:#000; ">Export Data</a></span>
                  </span>
                  <span id="print_rpt">
                    <span>Print</span>
                    <span><img src="../img_file/print-icon.svg" width="20" height="20"></span>
                  </span>
                </span>
              </div>
              <div style="width:100%;text-align:right;font-size:14px;">
                     <span style="padding:5px;">Page <?php echo $page ?> <b>of</b> <?php echo $lastpage ?></span>
                     <span style="background:#ddd;padding:5px;border-radius:5px;margin-left:10px;font-size:12px;"><?php echo "<a href=\"$target_page?page=$next&limit=$limit&month=$month&year=$year&gender=$gender&branch=$branch&address=$address\">Next</a>"; ?></span>
                     <?php 
                        if($page!=1) {
                            $prev = $page - 1; ?>
                     <span style="background:#ddd;padding:5px;border-radius:5px;margin-left:10px;font-size:12px;"><?php echo "<a href=\"$target_page?page=$prev&limit=$limit&month=$month&year=$year&gender=$gender&branch=$branch&address=$address\">Back</a>"; ?></span>
                     <?php } ?>
                     <span style="background:#ddd;padding:5px;border-radius:5px;margin-left:10px;font-size:12px;"><?php echo "<a href=\"$target_page?page=1&limit=$total_pages&limit=$limit&month=$month&year=$year&gender=$gender&branch=$branch&address=$address\">View All</a>"; ?></span>
                  </div>
        </span>
    </div>
</div>
  <form name="form_list" id="form_list" method="post" action="client_list.php">
    <table align="center" cellpadding="5" cellspacing="0" class="report_display" width="100%">
     <tr>
      <td></td>
      <td>Id</td>
      <td>Member</td>
      <td>Gender</td>
      <td>Contacts</td>
      <td>Residence</td>
      <td>Business Name</td>
      <td>Branch</td>
      <td>Loan Status</td>
      <td>Date Registered</td>
      <td id="row">&nbsp;</td>
     </tr>
     <?php
       
      if(mysqli_num_rows($result)){
        $count=0;
        while($rw=mysqli_fetch_array($result)){

          //return church name initials
           $split = explode(' ',$rw[16]);
           $branch_init = '';
            foreach($split as $key){
              $branch_init .= substr($key,0,1);
            } 
        ?>
        <tr>
         <td><?php echo $start += 1 ?></td>
         <td><?php echo $rw['data_id'] ?></td>
         <td style="text-transform:capitalize;"><?php echo strtolower($rw[1].' '.$rw[2]) ?></td>
         <td><?php echo $rw[7] ?></td>
         <td><?php echo $rw[3] ?></td>
         <td><?php echo $rw[5] ?></td>         
         <td><?php echo $rw[6] ?></td>
         <td><?php echo $branch_init ?></td>
         <td><span style="font-weight: bold;font-size: 11px;"><?php 
              $qry = mysqli_query($connect,"SELECT * FROM loan_entries WHERE client='".$rw[0]."'");
                if(mysqli_num_rows($qry)){
                  $loan_status = 'qualify';
                  while($r = mysqli_fetch_array($qry)){
                    if($r['status']!='00')
                      $loan_status = 'on-going';
                  }
                  echo $loan_status;
                }else{
                  echo 'Not Assigned';
                }
          ?></span></td>
          <td><?php echo date('d/m/y',strtotime($rw['date_created']))?></td>         
         <td id="row">
          <select name="select_action" id="<?php echo $rw[0] ?>" class="text-input" style="width:80px;">
            <option value="">Action</option>
            <option value="edit_<?php echo $rw[0] ?>" id="edit_<?php echo $rw[0] ?>">Edit</option>
            <option value="pop_<?php echo $rw[0] ?>" id="pop_<?php echo $rw[0] ?>">Assign Loan</option>
            <option value="view_<?php echo $rw[0] ?>">Loan History</option>
            <option value="statement_<?php echo $rw[0] ?>">View Statement</option>
            <option value="delete_<?php echo $rw[0] ?>" id="delete_<?php echo $rw[0] ?>">Delete</option>
         </select>
          </td>
         </tr>
        <?php
        }
       }else{
         
        ?>
          <tr>
               <td colspan="10"class="bottom_line" style="color: #145FA7;"><div style="width:40%;height:400px;padding:20px;">No Search Record(s) Found....</div></td>
           </tr>
        <?php
       }
      ?>
        <tr  id="top">
          <td colspan="10">
           </td>
       </tr>
    </table> 
   </form>
  </div>
</div>

<div id="essential">
<form method="post" name="form" id="form">
    <input type="hidden" name="client2" id="client_id2" />
    <input type="hidden" name="loan" id="loan_id" />
      <div class="form-group">
        <div class="label">Security Name</div>
        <input type="text" name="security_name" class="text-input" />
      </div>
      <div class="form-group">
        <div class="label">Value</div>
        <input type="text" name="value" class="text-input" id="loan_amount" value="00.0" />
      </div>
      <div class="form-group">
        <div class="label">Type</div>
        <input type="text" name="type_sec" class="text-input" />
      </div>
      <div class="form-group">
        <div class="label">Serial No</div>
        <input type="text" name="serial" class="text-input" />
      </div>      
      <div class="form-group">
        <div class="label">Description</div>
        <input type="text" name="desc" class="text-input" />
      </div>
      <div class="form-group">
        <button type="submit" name="btn-security" class="button-input" id="security_button">Submit</button>
      </div>
      <input type="hidden"  value="entry" name="security-entry" />
    </form>
  </div>
  <div id="guarantor_content">
    <form method="post" name="guarantor" id="guarantor">
    <input type="hidden" name="client3" id="client_id3" />
    <input type="hidden" name="loan2" id="loan_id2" />
       <!--<div style="display: grid;grid-template-columns: 4fr 2fr">
         <div></div>
         <div>
           <div style="display: grid;grid-template-columns: repeat(2, 1fr);">
             <span id="loan" class="loan">Amortize Loan</span>
             <span id="print" class="loan">Print</span>
           </div>
         </div>
       </div>
      <div class="form-group" id="amortize">
      </div>-->
      <div class="form-group">
        <div class="label">Names</div>
        <input type="text" name="guarantor_name" class="text-input" />
      </div>
      <div class="form-group">
        <div class="label">Contacts</div>
        <input type="text" name="contacts" class="text-input" />
      </div>
      <div class="form-group">
        <div class="label">Email</div>
        <input type="text" name="email" class="text-input" />
      </div>
      <div class="form-group">
        <div class="label">Gender</div>
        <select name="gender" class="text-input">
            <option selected="selected"  value="">--Select--</option>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
        </select>
      </div>      
      <div class="form-group">
        <div class="label">Residence</div>
        <input type="text" name="residence" class="text-input" />
      </div>
      <div class="form-group">
        <div class="label">Occupation</div>
        <input type="text" name="occupy" class="text-input" />
      </div>
      <div class="form-group">
        <button type="submit" name="btn-guarantor" class="button-input" id="guarantor_button">Submit</button>
      </div>
      <input type="hidden" name="guarantor-entry"  value="entry" />
     </form>
</div>
<div id="loan_form">

<div class="form_header">Assign Loan Form</div>
  <section class="step-wizard">
     <ul class="step-wizard-list">
       <li class="step-wizard-item" id="step_1">
           <span class="progress-count">1</span>
           <span class="progress-label">Loan Details</span>
       </li>
       <li class="step-wizard-item" id="step_2">
           <span class="progress-count">2</span>
           <span class="progress-label">Loan Security</span>
       </li>
       <li class="step-wizard-item" id="step_3">
           <span class="progress-count">3</span>
           <span class="progress-label">Loan Guarantor</span>
       </li>
     </ul>
   </section>

  <section id="form_wrap">
    <form method="post" name="form" id="form2">
      <input type="hidden" name="client" id="client_id" />
      <div class="form-group">
        <div class="label">Loan Officer</div>
          <input type="text" name="name_search" class="text-input" id="searchStaff" autocomplete="off" data-src="staff" />
             <input type="hidden" name="data_id" value="" id="data_id" />
           <div id="drop-box" class="drop_down drop_large_size" style="width:645px;"></div>
      </div>
      <div class="form-group">
        <div class="label">Loan Amount</div>
        <input type="text" name="loan_amount" class="text-input" id="loan_amount" />
      </div>
      <div class="form-group">
      <div style="display:grid; grid-template-columns: repeat(2, 1fr);gap:10px;">
        <div>
          <div class="label">Period Category</div>
           <select name="duration" class="text-input" id="duration">
            <option selected="selected">Select</option>
            <option value="day">Day</option>
            <option value="month">Month</option>
            <option value="year">Year</option>
           </select>
        </div>
         <div>
           <div class="label">Period</div>
           <input type="text" name="period" class="text-input" id="period" />
         </div>
      </div>
      <div class="form-group">
        <div class="label">Interest Rate</div>
        <input type="text" name="interest" class="text-input" id="interest" />
      </div>
      <div class="form-group">
        <div class="label">Issue Date</div>
        <input type="text" name="date" class="text-input" id="datetimepicker" autocomplete="off" value="<?php echo date('Y-m-d H:i:s') ?>" />
      </div>
      <div class="form-group">
        <div class="label">Loan Processing Fees Status</div>
        <input type="checkbox" name="loan_processing" value="01" id="loan_processing"/>&nbsp;Paid
      </div>
      <div class="form-group" id="button">        
        <button type="submit" name="btnSubmit" class="button-input" id="submit_loan">Submit</button>
      </div>
    </form>
</div>

<!-- returns seach form elements -->
<div id="search-form">
    <div class="form_element">
     <input type="text" name="address" placeholder="Address" class="text-input"/>
    </div>
    <div class="form_element">
      <select name="gender" id="gender" class="text-input">
        <option selected="selected" value="">Gender</option>
        <option value="male">Male</option>
        <option value="female">Female</option>
      </select>
    </div>
    <div class="form_element">
      <select name="month" id="gender" class="text-input">
        <option selected="selected" value="">Search By Month</option>
         <?php 
          $array_month = array('January','February','March','April','May','June','July','August','September','October','November','December');
          foreach($array_month as $val){
            echo '<option value="'.$val.'">'.$val.'</option>';
          }
        ?>
      </select>
    </div>
    <div class="form_element">
     <input type="text" name="year" placeholder="Year" class="text-input"/>
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
<?php } 

      echo footer_sec(); //footer section
    ?>
  </div>
  </body>
</html>