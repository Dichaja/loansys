
/*

 format page layout
 
*/

$(document).on('click','#datetimepicker, #picker',function(){
  $('#datetimepicker, #picker').datetimepicker({
   inline:false,
   changeMonth: true, 
   changeYear: true,
   yearRange: "1970:+nn",
   maxDate: '7m'
  });
})

$(document).on('click','#print_note',function(){
  $('.col-lg-9').hide();
  $('.close').hide();
  $('.display').css('width','95%');
  window.print()
  $('#myModal').hide();
  $('.col-lg-9').show();
})

$(document).on('click','#print_rpt',function(){
   window.print()
})

$(document).on('click','.expand_link',function(event){   
    $(this).addClass("expand_link_show").removeClass('expand_link').slideDown('slow');
})

$(document).on('click','.expand_link_show',function(event){   
    $(this).addClass("expand_link").removeClass('expand_link_show');
})


$(document).on('click','.close',function(){

  var select = $(this).attr('select'),
       loc = window.location.href;

  if(!select){
    window.open(loc,'_self');
    //window.open(protocol+'//'+host+'/'+path,'_self');
  }else{

    $('#display').html('');
    let id = $(this).attr('select'),
        select_html = $('#'+id).html()
      $('#'+id).html(select_html);
    $("#myModal").css("display","none");  
    console.log(select);  
  }
})


$(document).on('click','#gen_pdf',function(){

let nbPages = 1;
let date = new Date();
let date_val = date.getDate();//return current year
$('#print_rpt, #gen_pdf').css('display','none');

   //create pdf file
  const options = {
      margin: 0.5,
      filename: 'summary_'+date_val+'.pdf',
      image: { 
        type: 'jpg', 
        quality: 0.95
      },
      html2canvas: { 
         dpi: 192, letterRendering: true, width: 1024, height: 1448 * nbPages
      },
      jsPDF: { 
        unit: 'in', 
        format: 'a4', 
        orientation: 'portrait' 
      }
    }

  const element = $('#display').html();
  html2pdf().from(element).set(options).save();

})

$(document).on('click','#get_report',function(){

let nbPages = 1;
let date = new Date();
let date_val = date.getDate();//return current year
$('#get_report, #print_rpt, #gen_pdf, #summary, .grid-2, select[name="select_action"]').css('display','none');
$('#header_wrap').css('display','block')
   //create pdf file
  const options = {
      margin: 0.5,
      filename: 'summary_'+date_val+'.pdf',
      image: { 
        type: 'jpg', 
        quality: 0.95
      },
      html2canvas: { 
         dpi: 192, letterRendering: true, width: 1096
      },
      jsPDF: { 
        unit: 'in', 
        format: 'a4', 
        orientation: 'portrait' 
      }
    }

  const element = $('.col-lg-9').html();
  html2pdf().from(element).set(options).save();

$('#get_report, #print_rpt, #gen_pdf, #summary, .grid-2, select[name="select_action"]').css('display','block');
$('#header_wrap').css('display','none');
})


/*Define a debounce function
function debounce(func, delay) {
    let timer;
    return function() {
        const context = this;
        const args = arguments;
        clearTimeout(timer);
        timer = setTimeout(function() {
            func.apply(context, args);
        }, delay);
    };
}

$(document).on('keyup', 'input[name="name_search"]', debounce(async function() {
    
  var  search = $(this).val();
  var exp = new RegExp(search, "i");
  var results='',count=0, combo;
  var tab = $(this).attr('data-src');
  var search_officer;
  var $namesInput = $('#drop_wrapper');
  var $dropBox = $('#drop-box');

    function setDropBoxWidth() {
        $dropBox.width($namesInput.outerWidth());
        console.log($namesInput.outerWidth())
    }
    
    setDropBoxWidth();

    if (search) {
      if($('#loan_amount').length)
        search_officer='1';

        try {
            $("#drop-box").slideDown().html('<div style="margin:auto;max-height250px;margin:50px auto;text-align:center;"><img src="../img_file/loading.gif" /></div>');
                
            const dataVal = await $.ajax({
                type: 'POST',
                url: '../data_files/name_list.php',
                data: {'search':search,'name_cat': tab,'search_officer':search_officer}
            });

            if (dataVal) {
                async function processValue(value) {
                    combo = value.first_name + ' ' + value.last_name;
                    combo2 = value.last_name + ' ' + value.first_name;
                     if(value.first_name.search(exp) != -1 || value.last_name.search(exp) != -1 || combo.search(exp) != -1 || combo2.search(exp) != -1){
                          results += '<div class="list_items" data="'+value.id+'">'+value.first_name+' '+value.last_name+'</div>';
                   }
                }

                async function processNext(index) {
                    if (index < dataVal.length) {
                        await processValue(dataVal[index]);
                        index++;
                        setTimeout(function() {
                            processNext(index);
                        }, 500);
                    } else {

                        if (results) {
                            $("#drop-box").html(results);
                        } else {
                            $("#drop-box").slideUp();
                        }
                     }
                  }
              processNext(0);
            }else{
              $("#drop-box").slideUp()
            }
          // Adjust the width when the window is resized
          $(window).resize(setDropBoxWidth);
        } catch (error) {
            console.error('Error: ', error);
        }
    }else{
     $("#drop-box").slideUp()
    }
}, 500)); // Debounce delay set to 200 milliseconds 

*/
$(document).on('keyup','input[name="name_search"], #searchStaff',async function(){
    
  var search = $(this).val();
  var exp = new RegExp(search, "i");
  var results='',count=0, combo;
  var tab = $(this).attr('data-src');
  var search_officer;

    try{
         if(search){             
            $("#drop-box").slideDown().html('<div style="max-height250px;margin:50px auto 50px;text-align:center;"><img src="../img_file/loading.gif" /></div>');
               
           // Wrap the AJAX request in a Promise that resolves after a timeout
            const dataPromise = new Promise(resolve => {
                setTimeout(async function() {
                    const data = await $.ajax({
                        type: 'POST',
                        url: '../data_files/name_list.php',
                        data: {'search':search,'name_cat': tab,'search_officer':search_officer}
                    });
                    resolve(data);
                }, 200);
            });

            const data = await dataPromise;
            if(data){
              $.each(data,function(key,value){
                      combo = value.first_name + ' ' + value.last_name;
                      combo2 = value.last_name + ' ' + value.first_name;
                     if(value.first_name.search(exp) != -1 || value.last_name.search(exp) != -1 || combo.search(exp) != -1 || combo2.search(exp) != -1){
                          results += '<div class="list_items" data="'+value.id+'" set-type="'+tab+'">'+value.first_name+' '+value.last_name+'</div>';
                      }
                  })
               }
         }else {
           $("#drop-box").slideUp();
         }

        // Adjust the width when the window is resized
        // $(window).resize(setDropBoxWidth);
      }catch(error){
        console.error('Error: ',error)
    }finally{
      (results!='') ? $("#drop-box").html(results) : $('#drop-box').slideUp();
    }
}) 

$(document).on('click','.list_items',function(){

   var id = $(this).attr('data');
   var txt = $(this).html();
   var dataType = $(this).attr('set-type');
   $('#data_id').val(id);

     if($('#name_search').val()){
        $('#name_search').val(txt).css('text-transform','capitalize');
      }else if($('#searchStaff').length){        
        $('#searchStaff').val(txt).css('text-transform','capitalize')
      }     
    $('#drop-box').slideUp();
 })


/*$(document).on('keyup', 'input[name="name_search"]', function() {
  
  var  search = $(this).val();
  var exp = new RegExp(search, "i");
  var results='',count=0, combo;
  var tab = $(this).attr('data-src')
  
  $("#drop-box").slideDown().html('<div style="margin:auto;max-height250px;margin:50px auto;width:40%;text-align:center;"><img src="../img_file/loading.gif" /></div>');

  if (search) {
    setTimeout(function() {
      $.ajax({
        type: 'POST',
        url: '../data_files/name_list.php',
        data: {
          'search':search,
          'name_cat': tab
        },
        success: function(data) {
         if(data){
          $.each(data, function(key, value) {
                combo = value.first_name+' '+value.last_name;
                combo2 = value.last_name+' '+value.first_name;
                   if(value.first_name.search(exp) != -1 || value.last_name.search(exp) != -1 || combo.search(exp) != -1 || combo2.search(exp) != -1){
                  results += '<div class="list_items" data="'+value.id+'">'+value.first_name+' '+value.last_name+'</div>';
                }
          });
         $("#drop-box").html(results);
        } else{
            $("#drop-box").slideUp();
          }
        }
      });
    }, 2000);
  } else {
    $("#drop-box").slideUp();
  }

})*/


function separator(index) {
   var myVal = "";
   var myDec = "";
   var index_val = "";
   var amtVal = parseFloat(index).toFixed(2);
   var amt_split = amtVal.toString().split('.');

   // Filtering out the trash!
   amt_split[0] = amt_split[0].replace(/[^0-9]/g, "");

   // Setting up the decimal part
   if (!amt_split[1] && amtVal.indexOf(".") > 1) {
      myDec = ".";
   }
   if (amt_split[1]) {
      myDec = "." + parseFloat(amt_split[1]);
   }

   // Adding the thousand separator
   while (amt_split[0].length > 3) {
      myVal = "," + amt_split[0].substr(amt_split[0].length - 3, amt_split[0].length) + myVal;
      amt_split[0] = amt_split[0].substr(0, amt_split[0].length - 3);
   }
   index_val = (amt_split[0] + myVal);

   return index_val;
}

$(document).on('keyup', '#loan_balance', function () {
   var key_val = $(this).val(),
      separator_val = '';
   if (key_val)
      separator_val = separator(remove_seperator(key_val));
   $(this).val(separator_val);
});

/*$(document).on('keydown', '#loan_balance', function (e) {
   var key = e.key;
   if (key === 'Backspace' || key === 'Delete') {
      var input = this;
      var input_val = $(input).val();
      var cursor_position = input.selectionStart;

      // Determine the new cursor position after deleting the character
      var new_cursor_position = cursor_position;
      if (key === 'Backspace' && cursor_position > 0) {
         new_cursor_position--;
      } else if (key === 'Delete' && cursor_position < input_val.length) {
         new_cursor_position++;
      }

      var separator_val = separator(remove_seperator(input_val));
      $(input).val(separator_val);

      // Restore cursor position
      input.setSelectionRange(new_cursor_position, new_cursor_position);
   }
});*/

function remove_seperator(num) {
   num = num.replace(/[^0-9]/g, "");
   return parseInt(num);
}
