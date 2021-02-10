
// Go back button in admin pages header
$(document).ready(function(){
	$('.goback').click(function(){
		parent.history.back();
		return false;
	});
});

  $('.continue-draft').click(function(){
    $('.gallery_draft').toggle();
  });



  $('#renvoyer-mission').click(function(){
    var mission = $(this).data('id');

    $('#Resendmission #mission_id').val(mission);
    $('#Resendmission').modal('show');
  });






function go_full_screen(){
  var elem = document.documentElement;
  if (elem.requestFullscreen) {
    elem.requestFullscreen();
  } else if (elem.msRequestFullscreen) {
    elem.msRequestFullscreen();
  } else if (elem.mozRequestFullScreen) {
    elem.mozRequestFullScreen();
  } else if (elem.webkitRequestFullscreen) {
    elem.webkitRequestFullscreen();
  }
}

$('body .send_btn').click(function(){
var sinistre_id = $(this).attr('data-sinistre');
var etape = $(this).attr('data-etape');
$('#sendmission #sinistre_id').val(sinistre_id);
$('#sendmission #etape').val(etape);
});

    $(document).ready(function() {
        $('.datepicker').datepicker({
              lang: 'fr',
        });
        $('#datepickerFrom').datepicker({
              lang: 'fr',
        });
        $('#datepickerTo').datepicker({
              lang: 'fr',
        });
        $("#lightgallery").lightGallery({
              lang: 'fr',
              loop  : false,
        }); 
    });




// Delete Sinistre and user modal
	$('body .remove-sinistre-btn ,  body .remove-user-btn').click(function(){
	  var link = $(this).attr('data-id');
	  $('body #deleteModal .btn-delete-modal').attr('href',link);
	  $('#deleteModal').modal('show');
	  return false;
	});  

	
	// set notifications as seen	
	$('body .setAllseen').click(function(){
	    $.ajax({
	     url:'/notifications/setNoficationsAsSeen',
	     type:"POST",
	      cache:false,
	     success:function(response){
	        $('.notificationsList').html('');
	        $('.notificationsCount').html('');
	        $('.dropdownNotification').removeClass('open');
	     },
	    }); 
	    return false;
	});  





if($.cookie("colapse") == 'ok') {
    $('body').addClass('sidebar-xs');
}
$('.sidebar-main-toggle').on('click', function(){

    if($.cookie("colapse") == 'ok') {
        $.removeCookie('colapse', { path: '/' });
        return false;
    } else {
        $.cookie('colapse', 'ok' ,{ expires: 7, path : '/'});
    }
});
 var today = new Date();
var dd = today.getDate();
var mm = today.getMonth() + 1; //January is 0!

var yyyy = today.getFullYear();
if (dd < 10) {
  dd = '0' + dd;
} 
if (mm < 10) {
  mm = '0' + mm;
} 
var today = dd + '/' + mm + '/' + yyyy;



$( document ).ready(function() {
    $('body #la_date').append(' ' + today);
});
document.querySelectorAll('.table-responsive').forEach(function (table) {
    let labels = Array.from(table.querySelectorAll('th')).map(function (th) {
        return th.innerText
    })
    table.querySelectorAll('td').forEach(function (td, i) {
        td.setAttribute('data-label', labels[i % labels.length]);
    })
});



$('.show_pay_modal').click(function(e){
  e.preventDefault;
  var url = $(this).attr('href');
  $('#paySinistreModal').modal('show');
  $('.btn-payer-colaborator').attr('href',url);
  return false;
});


$('select.colaborator_payments_list').change(function(){
  var url = $(this).val();
  window.location.replace(url);
});


$('select.colaborator_mission_list').change(function(){
  var colaborateur = $(this).val();
  var url = new URL(document.URL);
  
  if(window.location.href.indexOf('colaborateur=') > 0){
      url.searchParams.set('colaborateur', colaborateur);
  }else{
      url.searchParams.append('colaborateur', colaborateur);
  }

  window.location.replace(url);
});

$('select.assistane_mission_list').change(function(){
  var assisstante = $(this).val();
  var url = new URL(document.URL);
  
  if(window.location.href.indexOf('assisstante=') > 0){
      url.searchParams.set('assisstante', assisstante);
  }else{
      url.searchParams.append('assisstante', assisstante); 
  }
  
  window.location.replace(url);
});



    
    // stop the user from multiple send the missions
    $('body .envoyer-les-images').click(function (){
        var  id = $(this).data('id');
        $('#mission_to_upload_id').val(id);
        $('body .dropzone-uploader-section').show();
    });

 
    // Send gallery
    $('body .envoyer-les-images').click(function(){
        var mission_id = $(this).attr('data-id');
        $('body #mission-id').val(mission_id);
        return false;
    });



    // show the dropZone Uploader Tab
    $('#uploadfiles').click(function (){
            $('#MediaUploaderModal .modal-header a').removeClass('active');
            $(this).addClass('active');
            $('.dropzone').show();
            $('#MediaUploaderModal #mediaList').hide();        
    });

     // stop the user from multiple send the missions
    $('body .btn-envoyermission').click(function (e){
        e.preventDefault();
        if(!$('#sendmission [name="colaborator_id"]').val()){
            $('#sendmission .alert').show();
            return false;
        }else {
             $('#sendmission .alert').hide();
        }
        $(this).attr('disabled',true);
        $(this).attr('disabled', 'disabled');
        $(this).closest('form').submit();

    });



    if($('body .dropzone').length){

            Dropzone.autoDiscover = false;
            $(function() {
                var myDropzone = new Dropzone(".dropzone", {
                    init: function() {
                        this.on("sending", function(file, xhr, formData){
                            var mission_id  = $('#mission_to_upload_id').val();
                            formData.append("mission_id", mission_id);
                        });
                    },
                    url: "/missions/upload/",
                    paramName: "file",
                    maxFilesize: 100,
                    maxFiles: 120,
                    addRemoveLinks: true,
                    clickable:true,
                    acceptedFiles: "image/*",
                    success: function(file, response){
                        $('.send_imgs_and_notity').show();
                    },
                    error : function() {
                      //  alert('upload photos error');
                    }
                });
            });

    }

    

    

    $('body .select_compagnie').change(function(event) {
        if($(this).val() == 'autre'){
            $('#autre_compagnie').show();
        }else {
            $('#autre_compagnie').hidden();
        }
        
    });

    $('body .send_imgs_and_notity').click(function(event) {
        $(this).attr('disabled',true);
        $(this).attr('disabled', 'disabled');
        $(this).val('veuillez patienter ...');
        
        Dropzone.forElement('.dropzone').removeAllFiles(true);

        var mission_id = $('#mission_to_upload_id').val();
        $.ajax({
        url:'/missions/confirm_uploaded/',
         type:"POST",
         data: {id : mission_id},
         cache:false,
         success:function(response){
            $('tr#mission_'+mission_id).remove();
            $('.send_imgs_and_notity').hide();
            $('body .send_imgs_and_notity').attr('disabled', 'false');
            $('body .send_imgs_and_notity').removeAttr('disabled');
            $('body .send_imgs_and_notity').prop("disabled", false);
            $('body .dropzone-uploader-section').hide();
            $('body .send_imgs_and_notity').val('Envoyer images');
         },
        }); 
        return false;
    });
