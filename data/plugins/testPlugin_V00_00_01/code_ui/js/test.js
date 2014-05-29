function testPlgLoad(prefix,jsonData)
{
	$.each(jsonData, function(k, v) {
		var o = $('#'+prefix+k);
		if(o.length > 0) {
			if(o.is(":checkbox")) o.prop("checked",(v==1?true:false));
			else o.val(v);
		}
	});
}

function testPlgSave(prefix,cbFunction)
{
	var r = {};
	var lenprfx = prefix.length;
	$('input[id^="'+prefix+'"], select[id^="'+prefix+'"]').each(function( index ) {
		var n = $(this).attr('id').substring(lenprfx);
		if($(this).is(":checkbox")) r[n] = $(this).is(':checked') ? 1 : 0;
		else r[n] = $(this).val();
	});
	cb(cbFunction, r);
}


