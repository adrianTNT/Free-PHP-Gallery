// JavaScript Document

// function coded by http://www.TheWebHelp.com 

// function to use on most requests, used on location edit page, user status edit and more
// for external domains use the other/second function
function load_xml_doc(url_to_load, target_div, div_content_while_loading, do_on_load){
	
	// get the element width styling (to restore it later)
	original_element_width_styling = document.getElementById(target_div).style.width;
	original_element_height_styling = document.getElementById(target_div).style.height;
	
	// force a fixed width while loading
	document.getElementById(target_div).style.width = document.getElementById(target_div).offsetWidth+"px";
	document.getElementById(target_div).style.height = document.getElementById(target_div).offsetHeight+"px";
	
	
	// change div content while loading, usually this value should be '<img src="/images/loading_20x20.gif" />'
	// an empty value like '' is valid, it will clear the div content, so just test agains null which would leave div content unchanged while loading
	if(div_content_while_loading != null && div_content_while_loading != 'default'){
		document.getElementById(target_div).innerHTML = div_content_while_loading;
	}
	
	// accept a 'default' value so that we can easy change this across site
	if(div_content_while_loading == 'default' || div_content_while_loading == null){
		document.getElementById(target_div).innerHTML = '<img src="/images/loading_20x20.gif" />';
	}
	
	var xmlhttp;
	if (window.XMLHttpRequest){
		// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp=new XMLHttpRequest();
	} else {
		// code for IE6, IE5
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.onreadystatechange=function(){
		if (xmlhttp.readyState==4 && xmlhttp.status==200){
			// restore element size styling
			document.getElementById(target_div).style.width = original_element_width_styling;
			document.getElementById(target_div).style.height = original_element_height_styling;
			//
			document.getElementById(target_div).innerHTML=xmlhttp.responseText;
			eval(do_on_load);
		}
	}
	xmlhttp.open("GET",url_to_load,true);
	xmlhttp.send();
}



// a function that allows us to run a script in background, without printing anything, but returns the value
function run_script(url_to_load, do_on_load){

	var xmlhttp;
	if (window.XMLHttpRequest){
		// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp=new XMLHttpRequest();
	} else {
		// code for IE6, IE5
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.onreadystatechange=function(){
		if (xmlhttp.readyState==4 && xmlhttp.status==200){
			run_script_return = xmlhttp.responseText;
			// we can use run_script_return inside the do_on_load function
			eval(do_on_load);
			return run_script_return;
		}
	}
	// asynchronous = true/false
	xmlhttp.open("GET", url_to_load, true);
	xmlhttp.send();
}
