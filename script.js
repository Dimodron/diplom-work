window.addEventListener("DOMContentLoaded", function() {
    [].forEach.call( document.querySelectorAll('.tel'), function(input) {
    var keyCode;
    function mask(event) {
        event.keyCode && (keyCode = event.keyCode);
        var pos = this.selectionStart;
        if (pos < 3) event.preventDefault();
        var matrix = "+7 (___) ___ ____",
            i = 0,
            def = matrix.replace(/\D/g, ""),
            val = this.value.replace(/\D/g, ""),
            new_value = matrix.replace(/[_\d]/g, function(a) {
                return i < val.length ? val.charAt(i++) || def.charAt(i) : a
            });
        i = new_value.indexOf("_");
        if (i != -1) {
            i < 5 && (i = 3);
            new_value = new_value.slice(0, i)
        }
        var reg = matrix.substr(0, this.value.length).replace(/_+/g,
            function(a) {
                return "\\d{1," + a.length + "}"
            }).replace(/[+()]/g, "\\$&");
        reg = new RegExp("^" + reg + "$");
        if (!reg.test(this.value) || this.value.length < 5 || keyCode > 47 && keyCode < 58) this.value = new_value;
        if (event.type == "blur" && this.value.length < 5)  this.value = ""
    }

    input.addEventListener("input", mask, false);
    input.addEventListener("focus", mask, false);
    input.addEventListener("blur", mask, false);
    input.addEventListener("keydown", mask, false)

  });
});
$(document).ready(function() {
var input = document.querySelectorAll('#mydata')[0];
 
var dateInputMask = function dateInputMask(elm) {
 
    elm.addEventListener('keyup', function(e) {
    if( e.keyCode < 47 || e.keyCode > 57) {
      e.preventDefault();
    }
 
   var len = elm.value.length;
 
    if(len !== 1 || len !== 3) {
      if(e.keyCode == 47) {
        e.preventDefault();
      }
    }
   if(len === 2) {
    if (e.keyCode !== 8 && e.keyCode !== 46) { 
      elm.value = elm.value+'.';
    }}
 
if(len === 5) {
    if (e.keyCode !== 8 && e.keyCode !== 46) { 
      elm.value = elm.value+'.';
    }}
  });
};
 
dateInputMask(input);
});