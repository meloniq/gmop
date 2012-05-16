
function xb_get_element(a) {
  return typeof(a) === "string" ? jQuery("#" + a)[0] : a
}


function toggle_class(c, d, a) {
  var b = typeof(c) === "string" ? jQuery("#" + c) : jQuery(c);
  if (b.length) {
  	if (d) {
  		b.addClass(d)
  	}
  	if (a) {
  		b.removeClass(a)
  	}
  	return true
  }
  return false
}


function toggle_object_tabs(tab_obj) {
  var size = valid_object_tabs_ids.length;
  for (var i=0; i < size; i++) {
    toggle_class(xb_get_element(valid_object_tabs_ids[i]), 'unhighlight', 'highlight');
    xb_get_element(valid_object_tabs_ids[i]+'_content').style.display = 'none';
  }
  if (typeof(tab_obj)!='undefined') {
    toggle_class(xb_get_element(tab_obj), 'highlight', 'unhighlight');
    xb_get_element(tab_obj.id+'_content').style.display = 'block';
  }
}


function markerClick(contentString, latlng) {
  return function() {
    // open an info window with the information
    map.openInfoWindowHtml(latlng, contentString, {maxWidth:450, maxHeight:400, autoScroll:true});
  }
}


function toggleGroup(type) {
  for (var i = 0; i < markerGroups[type].length; i++) {
    var marker = markerGroups[type][i];
    if (marker.isHidden()) {
      marker.show();
    } else {
      marker.hide();
    }
  } 
}


function toggleGroupp(type) {
  var newtype = type + "Checkbox";
  if(document.getElementById(newtype).checked){
    for (var i = 0; i < markerGroups[type].length; i++) {
      var marker = markerGroups[type][i];
      marker.show();
    }
  } else {
    for (var i = 0; i < markerGroups[type].length; i++) {
      var marker = markerGroups[type][i];
      marker.hide();
    }
  } 
}
