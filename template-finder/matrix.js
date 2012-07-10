
$(document).ready(function() {
	
	function update_template_display() {
		$('.templatepresent').removeClass('templatepresent');
		
		var template = $("#template_select option:selected").text();
		
		for (var page in window.template_usage_by_page){
			
			if ($.inArray( template, window.template_usage_by_page[page]['templates'] ) > -1 ){
				//console.log(window.template_usage_by_page[page]['id']);
				$('#page'+window.template_usage_by_page[page]['id']).addClass('templatepresent');
			}
		}
	}
	
	
	$('#template_select').change(function(){ 
		update_template_display();
	});

	update_template_display();
});
