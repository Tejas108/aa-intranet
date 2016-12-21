var $j = jQuery.noConflict();

function handleMenuToggle(){
  $j('.svg-menu').click(function(){
    $j('.main-menu').toggle();
  });
}

function handleMagicNumberToggle(){
  $j('.show-all-dates').click(function(){
    if ($j(this).text() == "Show all Dates") {
      $j(this).text("Hide all Dates");
    } else {
      $j(this).text("Show all Dates");
    }
    $j('.all-numbers-container').fadeToggle('500');
  });
}

function ajaxSubmit(e){
  e.preventDefault();
  //console.log("ajaxSubmit called");
  var newNumberForm = $j(this).serialize();

  $j.ajax({
    type:"POST",
    url: "/wp-admin/admin-ajax.php",
    data: newNumberForm,
    success:function(data){
      window.location=document.location.href;
    }
  });

  return false;
}

function handleDocFrameReload(path){
    //console.log("handleDocFrameReload called");
    var iframe = $j('iframe');
    var src;
    var oldPath = path.split("/");
    var root = oldPath[1];
    var privateRoot = oldPath[0];
    oldPath.shift();
    if(root == "public"){
        src = "/doc-public-iframe/";
    }else if(root == "private"){
        src = "/doc-iframe/";
    }
    if(privateRoot == "private-media"){
        src = "/doc-private-media-iframe/";
    }

    var url = '';

    url = src + "?drawer=" + oldPath.join("*");

    setTimeout(function(){
        iframe.attr('src', url);
    }, 4000);


}

function handleHideUserProfileFields(){
  $j('.user-rich-editing-wrap').css('display','none');
  $j('.user-comment-shortcuts-wrap').css('display','none');
  $j('.user-admin-bar-front-wrap').css('display','none');
}

$j(document).ready(function() {
    handleMenuToggle();
    handleMagicNumberToggle();
    handleHideUserProfileFields();
    $j('#number-form').submit(ajaxSubmit);

    $j('.js-accordion-trigger').bind('click', function(e){
      $j(this).parent().find('.submenu').slideToggle('fast');  // apply the toggle to the ul
      $j(this).parent().toggleClass('is-expanded');
      e.preventDefault();
    });

  });

