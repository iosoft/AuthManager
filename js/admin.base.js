/*
	@! AuthManager v3.0
	@@ User authentication and management web application
-----------------------------------------------------------------------------	
	** author: StitchApps
	** website: http://www.stitchapps.com
	** email: support@stitchapps.com
	** phone support: +91 9871084893
-----------------------------------------------------------------------------
	@@package: am_authmanager3.0
*/
function checkUncheckAll(theElement) {
	var theForm = theElement.form, z = 0;
	for(z=0; z<theForm.length;z++) {
		if(theForm[z].type == 'checkbox' && theForm[z].name != 'checkall') {
			theForm[z].checked = theElement.checked;
			var tr = "td" + theForm[z].value;
				if(theForm[z].checked) {
					document.getElementById(tr).style.background = "#efefef";
				}
				else {
					document.getElementById(tr).style.background = "transparent"; }
				}
		 }
}

function highlight(checkbox) {
	if(document.getElementById) {
		var tr = eval("document.getElementById(\"td" + checkbox.value + "\")");
	} else {
		return;
	}
	
	if(tr.style) {
		if (checkbox.checked) {
			tr.style.backgroundColor = "#efefef";
		} else {
			tr.style.backgroundColor = "transparent";
		}
   }
}

$(function() {
	function activateTab() {
        var activeTab = $('[href=' + window.location.hash.replace('/', '') + ']');
        activeTab && activeTab.tab('show');
    }
	
	// initialize the function
	activateTab();

	$(window).hashchange(function(e) {
		activateTab();
	});

	$('a[data-toggle="tab"], a[data-toggle="pill"]').on('shown', function() {
		window.location.hash = '/' + $(this).attr('href').replace('#', '');
	});
});