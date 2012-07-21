


function ia_citer_execute(){

	var ia_id = $('#ia_id').val();
	get_ia_metadata_page(ia_id);
}

function get_ia_metadata_page(id){
	 
	var iaurl = "http://www.archive.org/details/" + id + "&output=json&callback=process_metadata"
	
	bObj = new JSONscriptRequest(iaurl); 
	
	// Build the dynamic script tag
	bObj.buildScriptTag(); 
	// Add the script tag to the page
	bObj.addScriptTag();
}


function process_metadata(jsonData) {  
	bObj.removeScriptTag(); 
	
	
	var creators = process_creators(jsonData.metadata.creator);

	var creator = $('#includeauthor:checked').val() ? creators.string : ''
	
	var disambiguation = ($('#disambiguate:checked').val() && creators.lastname) ? ' ('+creators.lastname +')' : ''
	
	var title = "''[["+ jsonData.metadata.title + disambiguation + "]]''";
	
	title = title.replace(/ ([:;])/g, '$1')
	
	var year  = (jsonData.metadata.date != undefined) ? ' (' + jsonData.metadata.date + ')' : '';

	var id  = jsonData.metadata.identifier;
	
	var ia_link = 'http://www.archive.org/details/' +  id 

	var text =  "* " + title + ' ' + creator + year + " {{ext scan link|<a href='" + ia_link + "'>" +  ia_link + '</a>}}' ;

	$('#outputarea').append(text + '<br/>');

  }

function process_creators(creators){
	
	
	var authors = []
	for(var i=0; i<creators.length; i++){
		var parts = creators[i].split(',');
		
		if (parts[parts.length-1].search("[0-9]") > 0 ){ //strip the year
			parts.pop()
		}
		
		if (parts.length == 1 ){
			
			var authorpage = 'Author:' + $.trim(parts[0])
		} else if (parts.length == 2 ){
			var authorpage = 'Author:' + $.trim(parts[1]) + ' ' + $.trim(parts[0])
		}
		
		if (authorpage != undefined){
			var wsauthorlink = 'http://en.wikisource.org/wiki/' + authorpage
			authors.push( "[[<a href='" + wsauthorlink + "'>" + authorpage + '</a>|]]' )
			
			var lastname = $.trim(parts[0])
		}
	}

	var text = 'by ';

	if (authors.length == 0){
		text = ''
	} else if (authors.length == 1){
		text = text + authors.pop()
	} else if (authors.length == 2){
		text = text + authors.pop() + ' and ' + authors.pop()
	} else {
		text = text + authors.pop() 
		while (authors.length > 1 ){
			
			text += ', ' + authors.pop() 
		}
		
		text += ' and ' + authors.pop() 
		
	}
	
	return {string:text, lastname:lastname}

}
