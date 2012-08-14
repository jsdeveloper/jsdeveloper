$(document).ready(function () {
	$(":input[type='text'],:input[type='password'],textarea").wijtextbox();
	$(":input[type='checkbox']").wijcheckbox();
	$(":input[type='submit']").button();
	
	$(".left-menu a").hover(
		function () {
			var className = $(this).children("span").attr("id");
			$(this).children("span").addClass(className + "-hover");
		}, function () {
			if (!$(this).hasClass("focus")) {
				var className = $(this).children("span").attr("id");
				$(this).children("span").removeClass(className + "-hover");
			}
		}	
	);
});

function getPager()
{
	$(function()
	{
		$("#prepage").wijpager({ pageCount: 150, pageIndex: 0, mode: "numericFirstLast" });
	});
}

function pDDL()
{
    $(function()
    {
		$("#year_from").wijdropdown();
		$("#year_to").wijdropdown();
		$("#price_from").wijdropdown();
		$("#price_to").wijdropdown();
		$("#mileage_from").wijdropdown();
		$("#mileage_to").wijdropdown();
		$("#feature_fuel_id").wijdropdown();
		$("#feature_gearbox_id").wijdropdown(); 
		$("#feature_damage_id").wijdropdown(); 
		$("#model_id").wijdropdown();
		$("#feature_ac_id").wijdropdown();
		$("#feature_seats_id").wijdropdown();
		$("#feature_doors_id").wijdropdown();
		$("#feature_class_id").wijdropdown();
		$("#feature_airbag_id").wijdropdown();
		$("#order_by").wijdropdown();
		
        $.get('/API/Make/List', function(data)
        {
            var dataMake = $.parseJSON(JXG.decompress(data));
			
			var items = '<option value="0">-- All --</option>';
			
			alert(dataMake);
			
			$.each(dataMake, function (i, Make) {
				items += "<option value='" + Make.id + "'>" + Make.Name + "</option>";
				});
			$("#make_id").html(items).wijdropdown();
			
			$("#make_id").change(function(){
				$.get('/API/Model/List/' + $("#make_id > option:selected").attr("value"), function(data){
					
					var dataModel = $.parseJSON(JXG.decompress(data));
					
					var items = '<option value="0" >-- All --</option>';
					
					$.each(dataModel, function(i, Model){
						items += "<option value=" + Model.Id + ">" + Model.Name + "</option>";  
						});
						$("#model_id").html(items).wijdropdown("refresh");
					});
				});	
        });
    });
}
 
