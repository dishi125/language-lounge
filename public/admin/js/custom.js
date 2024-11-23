/**
 *
 * You can write your JS code here, DO NOT touch the default style file
 * because it will make it harder for you to update.
 *
 */

"use strict";

function copyTextLink(link,message = ''){
    let msg = message || 'Link';
    navigator.clipboard.writeText(link);
    showToastMessage(`The ${msg} is Copied`,true);
}

function showToastMessage(message,isSuccess){
    if(iziToast){
        if(isSuccess == true){
            iziToast.success({
                title: '',
                message: message,
                position: 'topRight',
                progressBar: false,
                timeout: 1000,
            });
        }else {
            iziToast.error({
                title: '',
                message: message,
                position: 'topRight',
                progressBar: false,
                timeout: 1500,
            });
        }
    }
}

$(document).on('click', '.copy_link_clipboard', function (){
    var $temp = $("<input>");
    $("body").append($temp);
    $temp.val($(this).attr('data-link')).select();
    $temp.focus();
    document.execCommand("copy");
    $temp.remove();
    // alert("Phone number is copied.");
    showToastMessage("Link is copied.",true);
})
