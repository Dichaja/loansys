<?php
session_start();
error_reporting(E_ALL ^ E_NOTICE);
require("../xsert/connect.php");
require_once('../data_files/sys_function.php');
require_once('../data_files/page_settings.php');
error_reporting(0);
check_sess();
?>
<!DOCTYPE html>
<html lang="en">
  <head>
   <meta content="charset=utf-8" /> 
    <?php include('../data_files/link_docs.php') ?>
   <title><?php echo sys_tab_hdr() ?></title>

<script type="text/javascript">
$(document).on('click', '#openClientReg', function() {
  
  // Show modal
  if($('#clientRegModal').length === 0) {
    $('body').append('<div id="clientRegModal" class="modal" style="display:flex;align-items:center;justify-content:center;z-index:1000;">'
      + '<div class="modal-content modal-small-size" id="clientRegModalContent"></div>'
      + '</div>');
  }
  $('#clientRegModal').show();
  $('#clientRegModalContent').html('<div style="text-align:center;padding:40px;">Loading...</div>');
  $.ajax({
    url: '../data_files/client_reg.php',
    type: 'GET',
    success: function(data) {
      // Only extract the form
      var formHtml = $(data).find('form#client_reg').parent().html();
      $('#clientRegModalContent').html(formHtml);
      var uniqueId = 'MEM'+Math.floor(10000 + Math.random() * 90000)
      document.getElementById('member_id').value = uniqueId;
    },
    error: function() {
      $('#clientRegModalContent').html('<div style="color:red;text-align:center;">Failed to load form.</div>');
    }
  });
});
$(document).on('click', '#closeClientRegModal', function() {
  $('#clientRegModal').hide();
});
// Close modal on outside click
$(document).on('click', '#clientRegModal', function(e) {
  if(e.target.id === 'clientRegModal') {
    $('#clientRegModal').hide();
  }
});

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
            alert("Successful...!!");
            location.replace("client_list.php");
          }else{
            alert("Something Went Wrong. Please Try Again..!!!");
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
     }else if(val=='week'){
         $('#period').val(1);
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

$(document).on('click', '#submit_loan', function(e) {
    e.preventDefault();

    let form = $("#form2")[0];
    let essentialForm = $("#essential").html();
    let errorFields = [];

    let clientId = $('#client_id').val();
    let loanAmountField = $('#loan_amount');
    let loanAmount = parseFloat(loanAmountField.val().replace(/,/g,'')) || 0;
    let imgFile = $('#img_file')[0].files[0];

    // -------------------
    // FORM VALIDATION
    // -------------------
    if ($('input[name="name_search"]').val() === '') errorFields.push('name_search');
    if (loanAmountField.val() === '' || loanAmount === 0) errorFields.push('loan_amount');
    if ($('select[name="duration"]').val() === '') errorFields.push('duration');
    if ($('input[name="period"]').val() === '') errorFields.push('period');
    if ($('input[name="interest"]').val() === '') errorFields.push('interest');   
    if (!imgFile) { errorFields.push('img_file'); alert('Please upload a photo.');  }

    if (errorFields.length > 0) {

        errorFields.forEach(field => {
            $('#' + field).css('border','1px solid red');
        });

        $('#' + errorFields[0]).focus();
        return;
    }

    // -------------------
    // LOAN QUALIFICATION
    // -------------------
    $.ajax({
        type: "POST",
        url: "../data_files/post_data.php",
        data: {
            action: "check_loan_qualification",
            client_id: clientId,
            loan_amount: loanAmount
        },

        success: function(resp){

            if(resp.trim() == 'not_qualified'){

                alert('Savings balance is below the required loan limit.');
                $('#loan_amount').css('border','1px solid red').focus();
                return;

            } else if(resp.trim() == 'no_savings'){

                alert('Member has no Savings Balance.');
                console.error('Unexpected response:', resp);
                return;

            }

            // -------------------
            // SUBMIT LOAN
            // -------------------
            let formData = new FormData(form);

            $.ajax({

                type: "POST",
                url: "../data_files/data_src.php",
                data: formData,
                processData: false,
                contentType: false,

                beforeSend: function(){

                    $('#myModal_2')
                    .css('z-index','10')
                    .show()
                    .html('<div class="modal-spin-wrap"><div class="modal-img-spin"><img src="../img_file/loading.gif"></div></div>');

                },

                success: function(response){

                    $('#myModal_2').hide();

                    let res = response.split("_");

                    if(res[0] === '1'){

                        let loanId = res[1];
                        let clientId = res[2];

                        $('#step_1').removeClass('current-item');
                        $('#step_2').addClass('current-item');

                        $("#form_wrap").html(essentialForm);
                        $("#client_id2").val(clientId);
                        $("#loan_id").val(loanId);

                        $("#form2").trigger("reset");

                    }else{

                        alert("Something went wrong!");
                        console.log(response);

                    }

                },

                error:function(xhr){
                    $('#myModal_2').hide();
                    console.log(xhr.responseText);
                    alert("Server error occurred.");
                }

            });

        },

        error:function(){
            alert("Could not verify loan qualification.");
        }

    });

});

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


// Unified select_action handler
$(document).on('change', 'select[name="select_action"]', function() {
  var action_val = $(this).val();
  var select = action_val.split('_');
  var modal = $('#myModal').html();

  if(select[0]=='view') {
    window.open('loan_activity.php?client_id='+select[1],'_self');
  }
  else if(select[0]=='statement') {
    $.ajax({
      type:'POST',
      url:'../data_files/data_src.php',
      data:{'pay_statement': select[1]},
      beforeSend:function(){
        $('#myModal').css({'display':'block','z-index':'6'}).html('<div class="modal-spin-wrap"><div class="modal-img-spin"><img src="../img_file/loading.gif" /></div></div>')
      },
      success:function(data){
        $('#myModal').html(modal)
        $('.modal-content').removeClass('modal-content, modal-large-size').toggleClass('modal-small-size')
        $("#display").html(data);
      }
    });
  }
  else if(select[0]=='pop') {
    var form_display = $("#loan_form").html();
    $.ajax({
      type: 'POST',
      url: '../data_files/data_src.php',
      data:{'check_loan': 1,'check_client': select[1]},
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
    });
  }
  else if(select[0]=='edit') {
    $.ajax({
      type: 'POST',
      url: '../data_files/form_edit.php',
      data:{'edit_mem': select[1]},
      beforeSend:function(){
        $('#myModal_2').css({'display':'block','z-index':'6'}).html('<div class="modal-spin-wrap"><div class="modal-img-spin"><img src="../img_file/loading.gif" /></div></div>')
      },
      success:function(d){
        $('#myModal_2').css('display','none');
        $('#myModal').css('display','block').html(modal);
        $('.modal-content').removeClass('modal-large-size').toggleClass('modal-small-size');
        $('#display').html(d);
      }
    });
  }
  else if(select[0]=='delete') {
    var user = $('#user_type').val();
    $.ajax({
      type:'POST',
      url:'../data_files/data_src.php',
      data:{'check_client':select[1]},
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
    });
  }
  else if(select[0]=='addSavings') {
    // Show modal for savings entry
    if($('#savingsModal').length === 0) {
      $('body').append('<div id="savingsModal" class="modal" style="display:flex;align-items:center;justify-content:center;z-index:1000;">'
        + '<div class="modal-content modal-small-size" id="savingsModalContent"></div>'
        + '</div>');
    }
    $('#savingsModal').show();
    var formHtml = '<form id="savingsEntryForm">'
      + '<h3 style="margin-bottom:16px;">Savings Deposit</h3>'
      + '<div class="form-group"><input type="hidden" name="client_id" value="'+select[1]+'" class="text-input" /></div>'
      + '<div class="form-group"><label>Amount Deposited</label><input type="number" name="amount_depo" class="text-input" required /></div>'
      + '<div class="form-group"><label>Deposit Date</label><input type="date" name="depo_date" class="text-input" required /></div>'
      + '<div class="form-group"><label>Mode of Pay</label><select name="mode_of_pay" class="text-input" required id="mop_1"><option value="">Select</option><option value="01">Cash</option><option value="02">Bank</option><option value="03">Mobile Money</option></select></div>'
      + '<div class="form-group"><label>Account To</label><select name="accTo[]" id="accTo_1" class="text-input">'
      +'<option value="" selected="selected">Select</option>'
      +'</select></div>'
      + '<div class="form-group"><label>Account From</label><input type="text" name="account_from[]" class="text-input" /></div>'
      + '<div class="form-group" style="margin-top:18px;text-align:right;"><button type="submit" class="button-input" style="background:#145FA7;color:#fff;">Submit</button></div>'
      + '</form>';
    $('#savingsModalContent').html(formHtml);
  }
  else if(select[0]=='withdrawSavings') {
    // Show modal for withdrawal entry
    if($('#withdrawModal').length === 0) {
      $('body').append('<div id="withdrawModal" class="modal" style="display:flex;align-items:center;justify-content:center;z-index:1000;">'
        + '<div class="modal-content modal-small-size" id="withdrawModalContent"></div>'
        + '</div>');
    }
    $('#withdrawModal').show();
    var formHtml = '<form id="withdrawEntryForm">'
      + '<h3 style="margin-bottom:16px;">Savings Withdrawal</h3>'
      + '<div class="form-group"><input type="hidden" name="client_id" value="'+select[1]+'" class="text-input" /></div>'
      + '<div class="form-group"><label>Amount Withdrawn</label><input type="number" name="amount_withdraw" class="text-input" required /></div>'
      + '<div class="form-group"><label>Withdraw Date</label><input type="date" name="withdraw_date" class="text-input" required /></div>'
      + '<div class="form-group"><label>Mode of Pay</label><select name="mode_of_pay" class="text-input" required id="mop_1"><option value="">Select</option><option value="01">Cash</option><option value="02">Bank</option><option value="03">Mobile Money</option></select></div>'
      + '<div class="form-group"><label>Target Account</label><select name="account_from" id="accTo_1" class="text-input">'
      +'<option value="" selected="selected">Select</option>'
      +'</select></div>'
      + '<div class="form-group"><label>Account To</label><input type="text" name="account_to" class="text-input" /></div>'
      + '<div class="form-group" style="margin-top:18px;text-align:right;"><button type="submit" class="button-input" style="background:#dc3545;color:#fff;">Withdraw</button></div>'
      + '</form>';
    $('#withdrawModalContent').html(formHtml);
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

$(document).on('click', '.disburse-loan-btn', function(e) {
            e.preventDefault();
             var clientId = $(this).data('client-id');
             var savings_balance = $(this).data('savings');
             var modal = $('#myModal').html();
             var form_display = $("#loan_form").html();
             // Trigger the assign loan action
             $('#pop_' + clientId).trigger('click');
             $('#myModal').css('display','block');
             $('#myModal').html(modal);
          
             $('.modal-content').removeClass('modal-large-size').toggleClass('modal-small-size');
             $("#display").html(form_display);
             $('#step_1').addClass('current-item')
             $("#client_id").val(clientId);
             $('#savings_bal').val(savings_balance);
         });

$(document).on('change','select[name="mode_of_pay"]',function(){
   
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
             $('#accTo_1').html(`<option value="${val}">${selectText}</option>`)
          }else{
           $('#accTo_1').html(d);
          }
       }
    })
})

$(document).on('change', 'select[name="select_actions"]', function() {
            var action_val = $(this).val();
            var select = action_val.split('_');
            
            
});

// Close modal on outside click
  $(document).on('click', '#savingsModal', function(e) {
       if(e.target.id === 'savingsModal') {
             $('#savingsModal').hide();
         }
     });


function loadSavingsTable(){
  $('#savingsTableContainer').load('../data_rp/client_list.php');
}

// Handle savings form submission
  $(document).on('submit', '#savingsEntryForm', function(e) {
              e.preventDefault();
              var formData = $(this).serializeArray();
              var uniqueId = 'SAV'+Date.now()+Math.floor(Math.random()*1000);
              var clientId = formData.find(f=>f.name==='client_id').value;
              var amountDepo = formData.find(f=>f.name==='amount_depo').value;
              var depoDate = formData.find(f=>f.name==='depo_date').value;
              var modeOfPay = formData.find(f=>f.name==='mode_of_pay').value;
              var accountTo = formData.find(f=>f.name==='accTo[]').value;
              var accountFrom = formData.find(f=>f.name==='account_from[]').value;
              var dateEntry = new Date().toISOString().slice(0, 19).replace('T', ' ');
              $.ajax({
                type: 'POST',
                url: '../data_files/post_data.php',
                data: {
                  action: 'add_savings_entry',
                  id: uniqueId,
                  client: clientId,
                  amount_depo: amountDepo,
                  depo_date: depoDate,
                  mode_of_pay: modeOfPay,
                  account_to: accountTo,
                  account_from: accountFrom,
                  date_entry: dateEntry
                },
                success: function(resp) {
                  if(resp.trim()==='success') {
                    alert('Savings entry added successfully!');
                    $('#savingsModal').hide();
                    $('#savingsEntryForm')[0].reset();
                    setTimeout(function(){
                       location.reload();
                     }, 500);
                  } else {
                    alert('Failed to add savings entry.');
                    console.error('Error response:', resp);
                  }
                },
                error: function() {
                  alert('Error occurred while saving.');
                }
              });
            });

// Handle withdraw form submission
  $(document).on('submit', '#withdrawEntryForm', function(e) {
              e.preventDefault();
              var formData = $(this).serializeArray();
              var uniqueId = 'WD'+Date.now()+Math.floor(Math.random()*1000);
              var clientId = formData.find(f=>f.name==='client_id').value;
              var amountWithdraw = formData.find(f=>f.name==='amount_withdraw').value;
              var withdrawDate = formData.find(f=>f.name==='withdraw_date').value;
              var modeOfPay = formData.find(f=>f.name==='mode_of_pay').value;
              var accountTo = formData.find(f=>f.name==='account_to').value;
              var accountFrom = formData.find(f=>f.name==='account_from').value;
              var dateEntry = new Date().toISOString().slice(0, 19).replace('T', ' ');
              $.ajax({
                type: 'POST',
                url: '../data_files/post_data.php',
                data: {
                  action: 'add_withdraw_entry',
                  id: uniqueId,
                  client: clientId,
                  amount_withdraw: amountWithdraw,
                  withdraw_date: withdrawDate,
                  mode_of_pay: modeOfPay,
                  account_to: accountTo,
                  account_from: accountFrom,
                  date_entry: dateEntry
                },
                success: function(resp) {
                  if(resp.trim()==='success') {
                    alert('Withdrawal entry added successfully!');
                    $('#withdrawModal').hide();
                    $('#withdrawEntryForm')[0].reset();
                    setTimeout(function(){
                       location.reload();
                     }, 500);
                  } else {
                    alert('Failed to add withdrawal entry.');
                    console.error('Error response:', resp);
                  }
                },
                error: function() {
                  alert('Error occurred while saving.');
                }
              });
  });
</script>
</head>

<body>

<?php

$limit = 40; //how many items to show per page

if(isset($_POST['post_search'])){
  $client = isset($_POST['name_search']) ? $_POST['name_search'] : '';
  $address = isset($_POST['address']) ? $_POST['address'] : '';
  $gender = isset($_POST['gender']) ? $_POST['gender'] : '';
  $month = isset($_POST['month']) ? $_POST['month'] : '';
  $year = isset($_POST['year']) ? $_POST['year'] : '';
  $branch = isset($_POST['branch_details']) ? $_POST['branch_details'] : '';
  $client_id = isset($_POST['client_id']) ? $_POST['client_id'] : '';
}

if(isset($_GET['client_id'])){
  $client_id = isset($_GET['client_id']) ? $_GET['client_id'] : '';
}

  if(isset($_GET['page'])){
       $client = isset($_GET['client']) ? $_GET['client'] : '';
       $address = isset($_GET['address']) ? $_GET['address'] : '';
       $gender = isset($_GET['gender']) ? $_GET['gender'] : '';
       $client = isset($_GET['client']) ? $_GET['client'] : '';
       $page = isset($_GET['page']) ? $_GET['page'] : 1;
       $month = isset($_GET['month']) ? $_GET['month'] : '';
       $limit = isset($_GET['limit']) ? $_GET['limit'] : 40;
       $branch = isset($_GET['branch_details']) ? $_GET['branch_details'] : '';
       $year = isset($_GET['year']) ? $_GET['year'] : '';
    }

if(isset($_GET['client_id'])){
  $client_id = $_GET['client_id'];
}

    $targetpage = "client_list.php";   //your file name  (the name of this file)    
    
    if(isset($page))
        $start = ($page - 1) * $limit;          //first item to display on this page
    else
        $start = 0;  
                                 //if no page var is given, set start to 0
$qry = '';
$qry=''; 
     $qry = "SELECT c.id, c.first_name, c.last_name, c.contacts AS phone_number, c.residance, c.business_name, c.gender, c.date_created, b.branch_name, g.group_name, c.data_id FROM clients c, branches b, `groups` g ";
       if(!$_SESSION['general_user'])
          $qry .=", user_log u ";
            $qry .= " WHERE c.branch_id = b.id AND c.status='01' AND g.id = c.group_id AND c.group_id !='' AND c.group_id !='0' AND c.group_id IS NOT NULL AND ";
          if(!$_SESSION['general_user'])
              $qry .= " c.branch_id = u.user_branch AND ";
        if(isset($client) || isset($client_id)){
            if($client_id)
              $qry .= " c.id = '$client_id' AND ";
              else
                 $qry .= " CONCAT(c.first_name,' ',c.last_name) LIKE  '%$client%' AND ";
             }
         if(isset($address) && $address!='')
                $qry .= " c.residance = '".$address."' AND ";
            if(isset($month) && $month!='')
                $qry .= " monthname(c.date_created) = '".$month."' AND ";
             if(isset($year) && $year!='')
                $qry .= " date_format(c.date_created,'%Y') = '".$year."' AND ";
               if(isset($gender) && $gender !='')
                  $qry .= " c.gender = '".ucfirst($gender)."' AND ";
                 if(!$_SESSION['general_user'])
                    $qry .= "u.id='".$_SESSION['session_id']."' AND ";
                   if(isset($branch) && $branch!='')
                     $qry .= " b.id = '$branch' AND ";
               $qry2 .= $qry.' 1 ';
             $qry .= " 1 ORDER BY c.first_name, c.last_name ASC LIMIT $start, $limit ";
             

    $query = mysqli_query($connect,$qry2); //total registered clients
     $total_pages= mysqli_num_rows($query); 

    $result = mysqli_query($connect,$qry);

    /* Setup page vars for display. */
    if (!isset($page) ||  $page == 0) $page = 1;                  //if no page var is given, default to 1.
    $prev = $page - 1;                          //previous page is page - 1
    $next = $page + 1;                          //next page is page + 1
    $lastpage = ceil($total_pages/$limit);      //lastpage is = total pages / items per page, rounded up.
    $lpm1 = $lastpage - 1;

    $search = '';
       if(isset($client) && $client !='')
         $search .= ', ' . $client;
        if(isset($gender) && $gender !='')
            $search .= ', '.$gender;
          if(isset($month) && $month !='')
             $search .=  ', '.$month;
            if(isset($year) && $year !='')
             $search .=  ', '.$year;
              if(isset($branch) && $branch !=''){
                $sql = mysqli_query($connect,"SELECT * FROM branches WHERE id='$branch' ");
                   $r = mysqli_fetch_array($sql);
                     $search .= $r[1].', ';
                 }
  
if(isset($_POST['first_name'])){

//define constant
define("FILEREPOSITORY",'profile/');

      $id= date("j").rand(100000,999999);
      $apply = $_POST['apply'];

// --set image attributes for upload
  if(is_uploaded_file($_FILES['img_file']['tmp_name'])){

         $photo_name = $_FILES['img_file']['name'];
         $file_type = $_FILES['img_file']['type'];
         $photo_upd = $_FILES['img_file']['tmp_name'];
         
         //get the extension of the file
         $base = basename($photo_name);
         $extension = substr($base, strlen($base)-4, strlen($base));
         $allowed_extension = array(".jpg",".png",".jpeg",".PNG");

  if(in_array($extension,$allowed_extension)){
             if(!is_dir(FILEREPOSITORY.date("Y-m-d"))){
                  mkdir(FILEREPOSITORY.date("Y-m-d"));
                }

             $dir = date("Y-m-d").'/'.$id.'_'.strtotime(date('Y-m-d H:i:s')).$extension; //returns directory for uploading image
             move_uploaded_file($photo_upd,FILEREPOSITORY.date("Y-m-d").'/'.$id.'_'.strtotime(date('Y-m-d H:i:s')).$extension); //uploads file to respective directory
  }else{
        $response = 'Un-Supported Image File Format. <a href="" id="status_id">Try Again.!</a>';
    }
  //--//  
}

  if($response){

      $status = $response;
  }else{

    //insert query
    $group_id = isset($_POST['group_id']) ? $_POST['group_id'] : '';
    $inst_query = "INSERT INTO clients VALUES ('$id','".ucfirst(strtolower($_POST['first_name']))."','".ucfirst(strtolower($_POST['last_name']))."','".$_POST['contacts']."','".$_POST['email']."','".$_POST['residence']."','".$_POST['occupy']."','".$_POST['gender']."','".$_POST['city']."','".date("Y-m-d H:i:s")."','".date("Y-m-d H:i:s")."','$dir','".$_POST['branch_details']."','01','".$_POST['member_id']."','$group_id') ";

    $inst = mysqli_query($connect, $inst_query);
        
      if($inst)
          $status = 'success';
      else{
        $status = 'err';
        $reason = mysqli_error($connect);
      }
  }

 ?>
 <script type="text/javascript">
        location.replace("client_list.php?action_msg=<?php echo $status ?>&reason=<?php echo $reason ?>");
  </script>
 <?php
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
        <div class="report_header" style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;padding:8px 0 8px 0;">
          <span style="font-size:1.2em;font-weight:600;">Members Report <?php echo $search ?></span>
          <button id="openClientReg" class="button-input" style="background:#145FA7;color:#fff;width:40px;height:40px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.5em;box-shadow:0 2px 8px rgba(0,0,0,0.08);border:none;">
            <span style="display:inline-block;width:24px;height:24px;">
              <svg viewBox="0 0 24 24" width="24" height="24" fill="#fff"><circle cx="12" cy="12" r="12" fill="#145FA7"/><rect x="11" y="5" width="2" height="14" fill="#fff"/><rect x="5" y="11" width="14" height="2" fill="#fff"/></svg>
            </span>
          </button>
        </div>
                <div style="text-align: right;">
          <!--Search Wrapper -->
          <form name="form1" method="post" action="client_list.php" id="form1" style="display:flex;align-items:center;gap:12px;justify-content:flex-end;margin-bottom:8px;">
            <input type="hidden" name="post_search" value="1" />
            <div style="height:40px;border:solid 1px #CCC;background-color:#fff;width:300px;align-items:center;position:relative;" id="drop_wrapper">
              <input type="text" class="search_text" name="name_search" placeholder="Search Client" id="name_search" autocomplete="off" data-src="clients" style="flex:1;border:none;height:38px;padding:0 8px;" />
              <img src="../img_file/search.png" id="open_search" data-usr="" width="18px" height="18px" style="position:absolute;right:8px;top:50%;transform:translateY(-50%);cursor:pointer;" />
              <input type="hidden" name="client_id" value="" id="data_id" />
              <div id="drop-box" class="drop_down drop_large_size" style="width:300px;"></div>
            </div>
            <input type="submit" name="search" value="Search" class="button_search" style="height:38px;min-width:80px;" />
            <span style="padding:5px;font-size:13px;">Page <?php echo $page ?> <b>of</b> <?php echo $lastpage ?></span>
            <span style="background:#ddd;padding:5px 10px;border-radius:5px;margin-left:6px;font-size:12px;"><?php if($next<=$lastpage) echo "<a href='$targetpage?page=$next&limit=$limit&month=$month&year=$year&gender=$gender&branch=$branch&address=$address'>Next</a>"; ?></span>
            <?php if($page!=1) { $prev = $page - 1; ?>
            <span style="background:#ddd;padding:5px 10px;border-radius:5px;margin-left:6px;font-size:12px;"><?php echo "<a href='$targetpage?page=$prev&limit=$limit&month=$month&year=$year&gender=$gender&branch=$branch&address=$address'>Back</a>"; ?></span>
            <?php } ?>
            <span style="background:#ddd;padding:5px 10px;border-radius:5px;margin-left:6px;font-size:12px;"><?php echo "<a href='$targetpage?page=1&limit=$total_pages&limit=$limit&month=$month&year=$year&gender=$gender&branch=$branch&address=$address'>View All</a>"; ?></span>
          </form>
       </div>
     </div>
  </div>
  <div class="report_wrap">
  <div style="font-size:12px;font-weight: normal;display: grid; grid-template-columns: 1fr 1fr;">
    <span>Entries : <?php echo $total_pages ?></span>
    <!--<span style="display: grid; grid-template-columns: 1fr 1fr;">
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
                     <span style="background:#ddd;padding:5px;border-radius:5px;margin-left:10px;font-size:12px;"><?php echo "<a href=\"$targetpage?page=$next&limit=$limit&month=$month&year=$year&gender=$gender&branch=$branch&address=$address\">Next</a>"; ?></span>
                     <?php 
                        if($page!=1) {
                            $prev = $page - 1; ?>
                     <span style="background:#ddd;padding:5px;border-radius:5px;margin-left:10px;font-size:12px;"><?php echo "<a href=\"$targetpage?page=$prev&limit=$limit&month=$month&year=$year&gender=$gender&branch=$branch&address=$address\">Back</a>"; ?></span>
                     <?php } ?>
                     <span style="background:#ddd;padding:5px;border-radius:5px;margin-left:10px;font-size:12px;"><?php echo "<a href=\"$targetpage?page=1&limit=$total_pages&limit=$limit&month=$month&year=$year&gender=$gender&branch=$branch&address=$address\">View All</a>"; ?></span>
                  </div>
        </span>-->
    </div>
</div>
<div id="savingsTableContainer">
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
      <td>Group</td>
      <td>Date Registered</td>      
      <td>Admission Fee</td>
      <td>Loan Status</td>
      <td id="row">&nbsp;</td>
     </tr>
     <?php
       
      if(mysqli_num_rows($result)){
        $count=0;
        while($rw=mysqli_fetch_array($result)){

          //return name initials
           $split = explode(' ',$rw['branch_name']);
           $branch_init = '';
            foreach($split as $key){
              $branch_init .= substr($key,0,1);
            }

          // Compute client savings balance
          $savings_qry = mysqli_query($connect, "SELECT SUM(amount_depo) as balance FROM client_savings WHERE client='".$rw['id']."'");
          $savings_row = mysqli_fetch_array($savings_qry);
          $savings_balance = $savings_row['balance'] ? $savings_row['balance'] : 0;
        ?>
        <tr>
         <td><?php echo $start += 1 ?></td>
         <td><?php echo $rw['data_id'] ?></td>
         <td style="text-transform:capitalize;"><a href="../data_rp/member_profile.php?id=<?php echo $rw['id'] ?>" style="color:#145FA7;font-weight:bold;"><?php echo strtolower($rw['first_name'].' '.$rw['last_name']) ?></a></td>
         <td><?php echo $rw['gender'] ?></td>
         <td><?php echo $rw['residance'] ?></td>
         <td><?php echo $rw['phone_number'] ?></td>         
         <td><?php echo $rw['business_name'] ?></td>
         <!--<td><?php echo $branch_init ?></td>-->
         <td><?php echo $rw['group_name'] ?></td>  
         <td><?php echo date('d/m/y',strtotime($rw['date_created']))?></td> 
         <td>6,000</td>        
         <td><span style="font-weight: bold;font-size: 11px;">
  <?php 
    // Compute savings balance minus withdrawals
    $savings_qry = mysqli_query($connect, "SELECT SUM(amount_depo) as balance FROM client_savings WHERE client='".$rw['id']."'");
    $withdraw_qry = mysqli_query($connect, "SELECT SUM(amount_withdraw) as withdraw FROM client_withdraw WHERE client='".$rw['id']."'");
    $savings_row = mysqli_fetch_array($savings_qry);
    $withdraw_row = mysqli_fetch_array($withdraw_qry);
    $savings_balance = ($savings_row['balance'] ? $savings_row['balance'] : 0) - ($withdraw_row['withdraw'] ? $withdraw_row['withdraw'] : 0);
    $qry = mysqli_query($connect,"SELECT * FROM loan_entries WHERE client='".$rw['id']."'");
    $has_loan = false;
    $ongoing = false;
    if($savings_balance <= 0) {
      echo '<span style="color:#dc3545;">Not Qualified</span>';
    } else if(mysqli_num_rows($qry)){
      while($r = mysqli_fetch_array($qry)){
        $has_loan = true;
        if($r['status']!='00')
          $ongoing = true;
      }
      if($ongoing) {
        // Ongoing loan icon (green check)
        echo '<span title="Ongoing Loan" style="color:#28a745;font-size:1.2em;"><svg width="18" height="18" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="12" fill="#28a745"/><path d="M7 13l3 3 7-7" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg> On-going</span>';
      } else {
        echo 'Qualify';
      }
    }else{
      // Not assigned: show disburse button
      echo '<button class="disburse-loan-btn" data-client-id="'.$rw['id'].'" data-savings="'.$savings_balance.'" style="background:#fd7e14;color:#fff;border:none;border-radius:4px;padding:4px 12px;cursor:pointer;font-weight:bold;display:flex;align-items:center;gap:6px;" title="Assign Loan"><svg width="16" height="16" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="12" fill="#fd7e14"/><path d="M7 12h10M12 7v10" stroke="#fff" stroke-width="2" stroke-linecap="round"/></svg> Disburse</button>';
    }
  ?>
</span></td>     
         <td id="row">
          <select name="select_action" id="<?php echo $rw['id'] ?>" class="text-input" style="width:80px;">
            <option value="">Action</option>
            <option value="edit_<?php echo $rw['id'] ?>" id="edit_<?php echo $rw['id'] ?>">Edit</option>
            <!--<option value="pop_<?php echo $rw['id'] ?>" id="pop_<?php echo $rw['id'] ?>">Disburse</option>-->
            <option value="view_<?php echo $rw['id'] ?>">Loan History</option>
            <option value="statement_<?php echo $rw['id'] ?>">View Statement</option>
            <option value="addSavings_<?php echo $rw['id'] ?>" id="addSavings_<?php echo $rw['id'] ?>">Savings Deposit</option>
            <option value="withdrawSavings_<?php echo $rw['id'] ?>" id="withdrawSavings_<?php echo $rw['id'] ?>">Savings Withdraw</option>
            <option value="delete_<?php echo $rw['id'] ?>" id="delete_<?php echo $rw['id'] ?>">Delete</option>
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
    <form method="post" name="form" id="form2" enctype="multipart/form-data">
      <input type="hidden" name="client" id="client_id" />
      <div class="form-group">
        <div class="label">Savings Balance</div>
          <input type="text" name="savings_bal" class="text-input" id="savings_bal" autocomplete="off" value="" readonly />
      </div>
      <div class="form-group">
        <div class="label">Loan Officer</div>
          <input type="text" name="name_search" class="text-input" id="searchStaff" autocomplete="off" data-src="staff" />
             <input type="hidden" name="data_id" value="" id="data_id" />
           <div id="drop-box" class="drop_down drop_large_size"></div>
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
            <option value="week">Weekly</option>
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
      <!--<div class="form-group">
        <div class="label">Loan Processing Fees Status</div>
        <input type="checkbox" name="loan_processing" value="01" id="loan_processing"/>&nbsp;Paid
      </div>-->
      <div class="form-group">
                        <div class="label">Photo</div>
                          <div class="file-wrapper">
                            <div class="upload-btn-wrapper">
                              <button class="btn upload-file font-weight-500">
                                <span class="upload-btn">
                                    <i class="fas fa-cloud-upload-alt d-block font-50 pb-2"></i>
                                      Click Here to Browse folders
                                  </span>
                                 <span class="upload-select-button" id="blankFile">
                                       Supports JPG, GIF and PNG
                                  </span>
                                  <span class="success">
                                       <i class="far fa-check-circle text-success"></i>
                                   </span>
                               </button>
                           <input type="file" name="img_file" id="img_file" value="" />
                           </div>
                         </div>
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
