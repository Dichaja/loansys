$(document).ready(function(){
 $('#department').change(function(){
  var department = $(this).val();
  $.ajax({
       type: 'POST',
       url:'../bin/data.php',
       data: {
               'dept' : department
             },
       success: function(data){          
          $('#jobId').html(data);
       }
  });
});

$(document).on('click','#open_search, #name_search',function(){
  var form_data = $('#search-form').html();
  var $namesInput = $('#drop_wrapper');
  var $dropBox = $('#drop-box');
  $("#drop-box").html('');

    function setDropBoxWidth() {
        $dropBox.width($namesInput.outerWidth());
    }

  // setDropBoxWidth();
   
   $("#drop-box").slideDown().css({"display":"block","background":"#FFF","overflow":"hidden","max-height":"500px"}).html(form_data);

  // Adjust the width when the window is resized
  //$(window).resize(setDropBoxWidth);   
 });
})

$(document).on("change","#img_file",function(){

    var filename = $("#img_file").val();
    
    if(/^s*$/.test(filename)){

        $("#blankFile").text("No File Chosen..");
        $(".success").hide();
    }else{
        $("#blankFile").text(filename.replace("C:\\fakepath\\",""));
        $(".success").show();
    }
}) 


$(document).on('click','#close-search',function(){
   $("#drop-box").slideUp('slow').css("display","none");
   var open_search = '<img src="../img_file/search.png" width="18px" height="18px" id="open_search" style="cursor:pointer;" />';
    $("#search_icon").html(open_search);
  })
