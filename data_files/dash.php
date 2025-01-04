<?php
error_reporting(E_ALL ^ E_NOTICE);
require("../xsert/connect.php");
require_once('sys_function.php');
require_once('page_settings.php');

check_sess(); //check user loggin

?>
<!DOCTYPE html>
<html lang="en">
  <head>
   <meta content="charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="../data_css/dash_style.css" />
    <link rel="stylesheet" type="text/css" href="../data_css/css-circular-prog-bar.css" /> 
    <?php include('link_docs.php') ?>
    <script type="text/javascript" src="../data_scripts/Chart.min.js"></script>
   <title><?php echo sys_tab_hdr() ?></title>


<script type="text/javascript">

$(document).on('click','#datetimepicker, #picker',function(){
  $('#datetimepicker, #picker').datetimepicker({
   inline:false,
  })
})

$(document).on('change','#year_srch',function(){
    var year_val = $(this).val();
    chart_display(year_val);
})

$(document).on('click','#print',function(){
  window.print();
});

$(document).on('click','#view, #add, #make, #most_client, #most_purchase',function(){
   var link = $(this).attr('data-link');
   window.open(link,'_self');
})

$(function(){

  $('.cat_tot').each(function(){
     var html = $(this).html();
     $(this).html(separator(html));
  });
  
  //return current year
  var date = new Date();
  var year_cur = date.getFullYear();//return current year

   chart_display(year_cur);
   console.log(year_cur)
})
 
async function chart_display(year){

try{

if(year==='')
  year = 'General';

// Show the loading spinner
    $('#myModal').
    css({'display':'block','z-index':'6'}).
    html('<div class="modal-spin-wrap"><div class="modal-text"></div><div class="modal-img-spin"><img src="../img_file/loading.gif" /></div></div>');

const arr =  await $.getJSON('data_json.php?year='+year);

var container = $('#chart-container').html();
      $('#chart-container').html(container);

var category = {
   Payments : [],
   Loans:[],
   Extend:[]
}

var len = arr.length;

var monthOrder = [
  "January",
  "February",
  "March",
  "April",
  "May",
  "June",
  "July",
  "August",
  "September",
  "October",
  "November",
  "December"
];

var total_loans = 0,
     total_pay = 0,
     total_due=0;

// Initialize the category arrays with zero values for all months
for (var i = 0; i < 12; i++) {
  category.Payments[i] = 0;
  category.Loans[i] = 0;
  category.Extend[i] = 0;
}

// Update the category arrays with the actual values from the data
for (var i = 0; i < len; i++) {
  var monthIndex = monthOrder.indexOf(arr[i].month);
  if (arr[i].account == "Payments") {
    category.Payments[monthIndex] = parseFloat(arr[i].amount);
     total_pay += parseFloat(arr[i].amount);
  }
  if (arr[i].account == "Extend") {
    category.Extend[monthIndex] = parseFloat(arr[i].amount);
  }
  if (arr[i].account == "Loans") {
    category.Loans[monthIndex] = parseFloat(arr[i].amount);
    total_loans += parseFloat(arr[i].amount);
    total_due += parseFloat(arr[i].month_bal);
    console.log(arr[i].month_bal+' - ');
  }
}

var ctx = $("#line-chart");

var data = {
  labels: monthOrder,
  datasets: [
    {
      label: "Payments",
      data: category.Payments,
      backgroundColor: "#90EE90",
      borderColor: "#90EE90",
      fill: false,
      lineTension: 0,
      lineRadius: 5
    },
    {
      label: "Loans",
      data: category.Loans,
      backgroundColor: "rgb(77, 77, 253)",
      borderColor: "#fff",
      fill: false,
      lineTension: 0,
      lineRadius: 5
    },
    {
      label: "Extended",
      data: category.Extend,
      backgroundColor: "#ff7f7f",
      borderColor: "#fff",
      fill: false,
      lineTension: 0,
      lineRadius: 5
    }
  ]
};

var title_options = {
  title: {
    display: true,
    position: "top",
    text: "Loans Against Payments Performance - "+year,
    fontSize: 18,
    fontColor: "#333"
  },
  legend: {
    display: true,
    position: "bottom"
  }
};

var chart = new Chart(ctx, {
  type: "bar",
  data: data,
  options: title_options
});

 $('#total_loans').html(seperator(total_loans));
 $('#total_pay').html(seperator(total_pay));
 $('#due-balance').html(seperator(total_due));

}catch(error){
   console.log(error);
}finally{
  $('#myModal').css('display','none');
}
 
}



function seperator(index){
   var myVal="";
   var myDec="";
   var index_val="";
   var amtVal = parseFloat(index).toFixed(2);
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
        index_val = (amt_split[0]+myVal); 
    
    return index_val;
}

</script>

</head>

<body>

<!-- The Modal -->
<div id="myModal" class="modal">
  <!-- Modal content -->
  <div class="modal-content">
    <span class="close" id="spanModal" select="">&times;</span>
      <span id="display"></span>
  </div>
</div>
   
   <!-- Main Content Wrapper -->
<div class="main_bd_wrap">
  
  <?php        
    tp_hdr(); //Page Header, Menu
       side_menu_content(); // Side Menu
      ?>
         <!-- Main Content Side-Right -->
           <div class="main-sidebar col-lg-9">
            <!-- Dashboard Wrapper -->
<div class="dash-wrap">
         <div class="welcome-seg seg">
           <header class="card__header">
             <img src="../img_file/male.jpg" class="card_img" />
               <div> 
                  <h3>User,&nbsp;<?php echo $_SESSION['sess_user'] ?></h3>
                     <span style="display:inline-block;font-size:18px;color:#fd7e14;"><?php echo $_SESSION['user_type'] ?></span>
                </div>
           </header>
         </div>
         <div class="evalutaion-seg seg">
             <!-- Icon Links --> 
             <div style="font-weight: bold;">UGX</div> 
             <div style="display: grid;grid-template-columns: 3fr 1fr;align-items: center; margin: 10px auto;">
               <div>
                   <div style="display: grid;grid-template-columns: repeat(3, 1fr);align-items: center; margin: 10px auto;gap:10px;">
                    <span style="display: inline-block;width:90%;border-radius:5px;color:#fff;  background: linear-gradient(159deg, rgb(77, 77, 253) 0%, rgb(108, 143, 234) 100%);padding:10px;">
                      <span>Total Loans</span>
                      <span style="font-size:16px" id="total_loans"><!--<?php // echo  number_format(loan_performance($connect,'total_loans')) ?>--></span>
                    </span>
                    <span style="display: inline-block;width:90%;border-radius:5px;color:#fff;background: linear-gradient(159deg, rgb(30, 202, 123) 0%, rgb(81, 213, 152) 59%);padding:10px;">
                      <span>Total Payments</span>
                      <span style="font-size:16px" id="total_pay"><?php // echo number_format(loan_performance($connect,'payments')); ?></span>
                    </span>
                    <span style="display: inline-block;width:90%;border-radius:5px;color:#fff;background: linear-gradient(31deg, rgb(254, 208, 63) 0%, rgb(230, 190, 63) 110%);padding:10px;">
                      <span>Balance Due</span>
                      <span style="font-size:16px;display: inline-block;width:100%" id="due-balance"></span>
                    </span>
                  </div>
               </div>
               <div>
                <span id="add" data-link="../data_files/client_reg.php" style="display: inline-block;width:100%;margin-bottom: 5px;">Add Client</span>
                <span id="make" data-link="../data_rp/client_list.php" style="display: inline-block;width:100%;margin-bottom: 5px;">Assign Loan</span>
                <span id="view" data-link="../data_rp/loan_payments.php" style="display: inline-block;width:100%;margin-bottom: 5px;">Payments Report</span>
              </div>
            </div>
            
         </div>         
         <div class="client-stat seg">
            <span style="font-size:24px;display:inline-block;width:100%;text-align: center;color:#53777A;">
                <?php echo date('F, Y') ?>
            </span>
            <span style="font-size:14px;display:inline-block;width:100%;text-align: center;color:#53777A;">
              Loan Default Rate
            </span>
            <!-- Circular Percentage of attendance -->
            <?php
               
               //return number of clients wit loans
             $count_number = 0;

             $qry = "SELECT * FROM clients c, loan_entries l, branches b ";
             if(!$_SESSION['general_user'])
                 $qry .= ", user_log u ";
                    $qry .= " WHERE c.id = l.client AND c.branch_id = b.id AND ";
                if(!$_SESSION['general_user'])
                   $qry .= " u.user_branch = c.branch_id AND ";
                  if(!$_SESSION['general_user'])
                       $qry .= "u.id='".$_SESSION['session_id']."' AND ";
                  $qry .= " 1 ";
              $sql = mysqli_query($connect,$qry);

             while ($r = mysqli_fetch_array($sql)) {
                if($r['status']!='03')
                  $count_number += 1;
             }

             $rate = round((loan_performance($connect,'default_status','',date('Y')) / $count_number) * 100); // return percentage
            ?>
            <div class="progress-circle p<?php echo $rate ?>" style="margin: 10px auto;">

               <span><?php echo $rate; ?>%</span>
                  <div class="left-half-clipper">
                    <div class="first50-bar"></div>
                    <div class="value-bar"></div>
                 </div>
            </div>
            <div style="display:block;text-align: center;width:100%;color:#53777A;">
              <span style="font-size:24px;"><?php echo loan_performance($connect,'default_status','',date('Y')); ?></span>&nbsp;<span style="font-size:18px;display: inline-block;">Defaulter(s)</span>
            </div>
         </div>
          <!--Display Chart-->
        <div class="seg">
              <div id="chart-container" >
                 <div style="width:30%;margin:10px auto;">
                   <?php 
                    $sql = mysqli_query($connect,"SELECT date_format(date_entry,'%Y') as 'Year' FROM loan_entries GROUP BY Year ASC ");
                           if(mysqli_num_rows($sql)){
                              while($r = mysqli_fetch_array($sql)){
                                 $array[]=$r[0];
                               }
                            }

                            $sql = mysqli_query($connect,"SELECT date_format(pay_date,'%Y') as 'Year' FROM loan_payments GROUP BY Year ASC ");
                                if(mysqli_num_rows($sql)){
                                  while($r = mysqli_fetch_array($sql)){
                                     $array[]=$r[0];
                                  }
                              }
                        $array = array_unique($array);
                   ?>
                   <select name="year_srch" id="year_srch" style="width: 100%;padding: 10px 0;background-color: #ECF0F1;border: 2px solid transparent;border-radius: 3px;text-align: center;border-radius: 3px;font-size: 13px;">
                      <option value="" selected="selected">Search By Year</option>
                      <?php
                         foreach($array as $val){
                               echo '<option value="'.$val.'">'.$val.'</option>';
                             }
                      ?>
                      <option value="">General</option>
                    </select>
                 </div>
               <canvas id="line-chart"></canvas>
              </div>
         </div>
         <div class="seg">
            <div style="display:block;text-align: center;width:100%;color:#FF726F;">              
               <span style="font-size:24px;width:100%;display: inline-block;"><?php 
                  $count = 0;
                 $qry = "SELECT * FROM clients c, branches b ";
                   if(!$_SESSION['general_user'])
                     $qry .= ", user_log u ";
                       $qry .= " WHERE c.branch_id = b.id AND ";
                     if(!$_SESSION['general_user'])
                      $qry .= " u.user_branch = c.branch_id AND ";
                  if(!$_SESSION['general_user'])
                       $qry .= "u.id='".$_SESSION['session_id']."' AND ";
                  $qry .= ' 1 ';
              $sql = mysqli_query($connect,$qry);
             while ($r = mysqli_fetch_array($sql)) {
                $count += 1;
             }
               echo $count
               ?></span>
                   <span style="font-size:18px;display: inline-block;">Clients Registered</span>
               </div>
               <div style="display: block;text-align:center;font-weight: bold;font-size:14px;margin: 5px auto;color:#555;border-bottom:solid 1px #ccc;">Most Active</div>
               <div style="display: block;width: 100%;margin:5px auto;color:#555;text-transform: capitalize;text-align: center;">
                 <?php
              $qry = "SELECT COUNT(l.id) as 'count', CONCAT(c.first_name,' ',c.last_name) as 'client', c.id FROM clients c, loan_entries l, branches b ";
                 if(!$_SESSION['general_user'])
                      $qry .= ", user_log u ";
                        $qry .= " WHERE c.id = l.client AND c.branch_id = b.id AND ";
                   if(!$_SESSION['general_user'])
                       $qry .= " u.user_branch = c.branch_id AND ";
                     if(!$_SESSION['general_user'])
                       $qry .= "u.id='".$_SESSION['session_id']."' AND ";
                  $qry .= ' 1 GROUP BY l.client ORDER BY count DESC LIMIT 0,1 '; 

                    $sql = mysqli_query($connect,$qry);
                    if(mysqli_num_rows($sql)){
                       $r = mysqli_fetch_array($sql);
                        echo '<span style="display:inline-block;width:100%" data-link="../data_rp/loan_activity.php?client_id='.$r['id'].'" id="most_client">'.$r['client'].'</span>';
                    }else{
                      echo 'N/a';
                    }
                 ?>
               </div>
       </div>
    </div>
     </div> <!-- End -->
        </div>
    <?php
      echo footer_sec(); //footer section
    ?>
  </div>
  </body>
</html>