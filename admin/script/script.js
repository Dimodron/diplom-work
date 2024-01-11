
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
//Мероприятия
$( "#image" ).on('load',function(){
		$("#image").css('display','block');
		$("#no_image").css('display','none');
});
$("#picture").change(function() {
	$("#image").attr('src',$("#picture").val());
	$("#no_image").css('display','block');
	$("#image").css('display','none');
	$( "#image" ).on('load',function(){
		$("#no_image").css('display','none');
		$("#image").css('display','block');
	});
});
$(".event_themes_add input[type='submit']").click(function(){
	var sel_val=$(".event_themes_add select").val();
	if(sel_val!=0){
		$('.event_themes_add select').css('border','');
	}else{
		$('.event_themes_add select').css('border','1px solid red');
		return false;
	}
	if($('#no_image').css('display')!='block'){
		$('#picture').css('border','');
	}else{
		$('#picture').css('border','1px solid red');
		
		return false;
	}
});
//Расписание
$(".event_time_add input[type='submit']").click(function(){
	var sel_val=$(".event_time_add select").val();
	if(sel_val!=0){
		$('.event_time_add select').css('border','');
	}else{
		$('.event_time_add select').css('border','1px solid red');
		return false;
	}
});


//сортировка 
$(".sort").click(function(){	
	var name=$(this).attr('id');
	var type=$(this).attr('name');
	
	$("input[name='orderby']").attr('value',name);
	console.log("set orderby "+name);
	if(type=="ASC" || type==null){
		console.log("change on DESC");
		$("input[name='ordertype']").attr('value',"DESC");
	}else{
		console.log("change on ASC");
		$("input[name='ordertype']").attr('value',"ASC");
	}

	$(".opt_line form").submit();
});

//активность или проход для всех
$( ".activ,.visit" ).change(function() {
	var val=$(this).attr('value');
	var name=$(this).attr('name');
	var clas=$(this).attr('class');
	var check=($(this).is(':checked'))?'1':'0';
	 $.ajax({type: "POST",
		url: "index.php?act=activ",
		data:{id:val,activity:check,table_name:name,column:clas},
		success: function (result) {
		 if (result!='11') $(this).prop("checked", false);//сделать проверку на ошибку изменения в бд
		}
	});
}); 
//заказы
$(".orders_add input[type='submit']").click(function(){
	var event_val=$(".orders_add li .event").val();
	var date_val=$(".orders_add li .date").val();
	var time_val=$(".orders_add li .time").val();
	if(event_val!=0 && event_val!=undefined){$('.orders_add li .event').css('border','');}else{$('.orders_add li .event').css('border','1px solid red');return false;}
	if(date_val!=0 && date_val!=undefined){$('.orders_add li .date').css('border','');}else{$('.orders_add li .date').css('border','1px solid red');return false;}
	if(time_val!=0 && time_val!=undefined){
		$('.orders_add li .time').css('border','');
		$(".orders_add input[name='event_time_id']").attr('value',time_val)}else{
		$('.orders_add li .time').css('border','1px solid red');
		return false;}
});
$(".orders_add .event").change(function() {
	$(".orders_add .date").remove();
	$(".orders_add .time").remove();
	var opt_val=$(this).val();
	$.ajax({type: "POST",
		url: "index.php?act=orders_select",
		data:{id:opt_val},
		success: function (result) {
			$(".orders_add .event").after(result);
			$(".orders_add .date").change(function() {
				var dat_val=$(this).val();
				for(var i=0;i<$(".orders_add .time").length;i++){
					if(dat_val==$(".orders_add .time").eq(i).attr("id")){
						$(".orders_add .time").eq(i).css('display','block');
					}else{
						$(".orders_add .time").eq(i).css('display','none');
					}
				}
			});
		}
});
});
//пагенация
$(".page").click(function(){
	var page=$(this).attr('value');
	$("input[name='page']").attr('value',page);
	$(".opt_line form").submit();
});



