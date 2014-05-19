	var outerLayout;

	var layoutSettings_Outer = {
		name: "outerLayout" // NO FUNCTIONAL USE, but could be used by custom code to 'identify' a layout
		// options.defaults apply to ALL PANES - but overridden by pane-specific settings
	,	defaults: {
			size:					"auto"
		,	minSize:				40
		,	paneClass:				"pane" 		// default = 'ui-layout-pane'
		,	resizerClass:			"resizer"	// default = 'ui-layout-resizer'
		,	togglerClass:			"toggler"	// default = 'ui-layout-toggler'
		,	buttonClass:			"button"	// default = 'ui-layout-button'
		,	contentSelector:		".content"	// inner div to auto-size so only it scrolls, not the entire pane!
		,	contentIgnoreSelector:	"span"		// 'paneSelector' for content to 'ignore' when measuring room for content
		,	togglerLength_open:		35			// WIDTH of toggler on north/south edges - HEIGHT on east/west edges
		,	togglerLength_closed:	35			// "100%" OR -1 = full height
		,	hideTogglerOnSlide:		true		// hide the toggler when pane is 'slid open'
		,	togglerTip_open:		"Close This Pane"
		,	togglerTip_closed:		"Open This Pane"
		,	resizerTip:				"Resize This Pane"
		//	effect defaults - overridden on some panes
		,	fxName:					"slide"		// none, slide, drop, scale
		,	fxSpeed_open:			750
		,	fxSpeed_close:			1500
		,	fxSettings_open:		{ easing: "easeInQuint" }
		,	fxSettings_close:		{ easing: "easeOutQuint" }
	}
	,	north: {
			spacing_open:			0			// cosmetic spacing
		,	size:		87
		,	togglerLength_open:		0			// HIDE the toggler button
		,	togglerLength_closed:	-1			// "100%" OR -1 = full width of pane
		,	resizable: 				false
		,	slidable:				false
		//	override default effect
		,	fxName:					"none"
		}
	,	west: {
			size:					250
		,	spacing_closed:			21			// wider space when closed
		,	togglerLength_closed:	21			// make toggler 'square' - 21x21
		,	togglerAlign_closed:	"top"		// align to top of resizer
		,	togglerLength_open:		21			// NONE - using custom togglers INSIDE west-pane
		,	togglerTip_open:		"Close West Pane"
		,	togglerTip_closed:		"Open West Pane"
		,	resizerTip_open:		"Resize West Pane"
		,	slideTrigger_open:		"click" 	// default
		,	initClosed:				false
		,	onresize_end:			function () { resizeWidgets(); }
		//	add 'bounce' option to default 'slide' effect
		,	fxSettings_open:		{ easing: "easeOutBounce" }
		}
	,	center: {
			paneSelector:			"#mainContent" 			// sample: use an ID to select pane instead of a class
		,	minWidth:				200
		,	minHeight:				200
		,	onresize_end:			function () { aafw_recalcCenterObjectDimension(); }
		}
	};

	outerLayout = $("body").layout( layoutSettings_Outer );

            function closeModal(o){
                $("#mb_overlay").fadeOut(500,function(){$(this).remove();})
                $("#"+o.attr("dock")).find("img:first").remove();
                $("#"+o.attr("id")+"_dock").remove();
                $("#"+o.attr("id")).remove();
            }


            function initDock(o,docID){
                if(o.hasClass("dock")) return;
                var opt= o.get(0).options;
                var docEl=$("<span>").attr("id",o.attr("id")+"_dock").css({width:opt.dockedIconDim+5,display:"inline-block"});
                var icon= $("<img>").attr("src",opt.iconPath+"icons/"+(o.attr("icon")?o.attr("icon"):"restore.png")).css({opacity:.4,width:opt.dockedIconDim,height:opt.dockedIconDim, cursor:"pointer"});
                icon.click(function(){o.mb_iconize()});
                docEl.append(icon);
                $("#"+docID).append(docEl);
                o.attr("dock",o.attr("id")+"_dock");
            }

            function iconize(o){
                $("#"+o.attr("dock")).find("img:first").hide();
            }
            function restore(o){
                $("#"+o.attr("dock")).find("img:first").show();
            }
            function close(o){
	                $("#"+o.attr("dock")).find("img:first").remove();
	                $("#"+o.attr("id")+"_dock").remove();
	                $("#"+o.attr("id")).remove();
            }
            function cp_resize(o){
		var maxWidth = $('#mainContent').width() - 5;
		var maxHeight = $('#mainContent').height() - 10;
		if(o.outerWidth() > maxWidth || o.outerHeight() > maxHeight) {
			var newWidth = o.outerWidth() > maxWidth ? maxWidth : o.outerWidth();
			var newHeight = o.outerHeight() > maxHeight ? maxHeight : o.outerHeight();
			o.mb_resizeTo(newHeight, newWidth);
		}

		if(o.outerWidth() == maxWidth && o.outerHeight() == maxHeight) {
			o.attr("fullscreen",true);
		}else{
			o.attr("fullscreen",false);
		}
            }

	var $westAccordion; // init global vars

	function resizeWidgets() {
		//myLayout.resizeAll();
//		$westAccordion.accordion("resize");
	};


		// ACCORDION - in the West pane
		$westAccordion = $("#accordion1").accordion({
			fillSpace:	true
		,	active:		0
		});
		
		setTimeout( resizeWidgets, 1000 );

	function aafw_fullscreen(rContainer) {
		var newWidth = $('#mainContent').width() - 20;
		var newHeight = $('#mainContent').height() - 20;
		rContainer.mb_setPosition(10,10);
		rContainer.mb_resizeTo(newHeight,newWidth);
	}

	function aafw_recalcCenterObjectDimension() {
		var containerCollection = $(".containerPlus").not("#modalContainer");
		for(var i = 0; i < containerCollection.length; i++) {
			if($(containerCollection[i]).attr("fullscreen").toString() == "true") {
				aafw_fullscreen($(containerCollection[i]));
			}else{
				var maxWidth = $('#mainContent').width() - 20;
				var maxHeight = $('#mainContent').height() - 20;
				if($(containerCollection[i]).outerWidth() > maxWidth || $(containerCollection[i]).outerHeight() > maxHeight) {
					var newWidth = $(containerCollection[i]).outerWidth() > maxWidth ? maxWidth : $(containerCollection[i]).outerWidth();
					var newHeight = $(containerCollection[i]).outerHeight() > maxHeight ? maxHeight : $(containerCollection[i]).outerHeight();
					$(containerCollection[i]).mb_resizeTo(newHeight, newWidth);
					$(containerCollection[i]).mb_setPosition(10,10);
				}
			}
			setTimeout('if($("#' + containerCollection[i].id + '").offset().left < 5 || $("#' + containerCollection[i].id + '").offset().top < 5) $("#' + containerCollection[i].id + '").mb_setPosition(20,20);', 1000 );
		}
	}

	function toggleTimeRestrictionFields() {
		var curControlState = !document.getElementById('timeRestriction').checked;
		document.getElementById('bMon').disabled = curControlState;
		document.getElementById('bTue').disabled = curControlState;
		document.getElementById('bWed').disabled = curControlState;
		document.getElementById('bThu').disabled = curControlState;
		document.getElementById('bFri').disabled = curControlState;
		document.getElementById('bSat').disabled = curControlState;
		document.getElementById('bSun').disabled = curControlState;
		document.getElementById('timeFrom').disabled = curControlState;
		document.getElementById('timeUntil').disabled = curControlState;
		document.getElementById('fieldsTimeRestriction').style.color = curControlState ? '#888' : '#000';
	}

	function toggleIpRestrictionFields() {
		var curControlState = !document.getElementById('ipRestriction').checked;
		document.getElementById('ipInclude1').disabled = curControlState;
		document.getElementById('subnetInclude1').disabled = curControlState;
		document.getElementById('ipInclude2').disabled = curControlState;
		document.getElementById('subnetInclude2').disabled = curControlState;
		document.getElementById('ipInclude3').disabled = curControlState;
		document.getElementById('subnetInclude3').disabled = curControlState;
		document.getElementById('ipInclude4').disabled = curControlState;
		document.getElementById('subnetInclude4').disabled = curControlState;
		document.getElementById('ipInclude5').disabled = curControlState;
		document.getElementById('subnetInclude5').disabled = curControlState;
		document.getElementById('fieldsIpRestriction').style.color = curControlState ? '#888' : '#000';
	}
/*
	var isCtrl = false;
	document.onkeyup=function(e){
		if(e.which == 17) isCtrl=false;
	}
	document.onkeydown=function(e){
		if(e.which == 17) isCtrl=true;
	}
*/
function tableGetSelectedRowDataCell(tblSelector,cellNumber) {
	var idCollector = '';
	oTable = $(tblSelector).dataTable();
	$(tblSelector + ' tr.row_selected').each(function (i) {
		var aData = oTable.fnGetData( this );
		idCollector += (idCollector=='' ? '' : ',') + aData[cellNumber];
	});
	return idCollector;
}

function tableDeleteSelectedRows(tblSelector) {
	oTable = $(tblSelector).dataTable();
	$(tblSelector + ' tr.row_selected').each(function (i) {
		oTable.fnDeleteRow(this);
	});
	return;
}

function mathRound(x, n) {
	if (n < 1 || n > 14) return false;
	var e = Math.pow(10, n);
	var k = (Math.round(x * e) / e).toString();
	if (k.indexOf('.') == -1) k += '.';
	k += e.toString().substring(1);
	return k.substring(0, k.indexOf('.') + n+1);
}

function currencyRound(x, n) {
	return Math.round(x / n) * n;
}

jQuery.fn.filterByText = function(textbox, selectSingleMatch) {
	return this.each(function() {
		var select = this;
		var options = [];
		$(select).find('option').each(function() {
			options.push({value: $(this).val(), text: $(this).text()});
		});
		$(select).data('options', options);
		$(textbox).data("lastval",$(textbox).val());
		$(textbox).bind('change keyup', function() {
			if($(this).data("suspendEvents")) return;
			var search = $.trim($(this).val());
			if(search==$(this).data("lastval")) return;
			$(this).data("suspendEvents",true);
			var selVal = $(select).val();
			var options = $(select).empty().scrollTop(0).data('options');
			var regex = new RegExp(search,'gi');

			$.each(options, function(i) {
				var option = options[i];
				if(option.text.match(regex) !== null) {
					$(select).append(
						$('<option>').text(option.text).val(option.value)
					);
				}
			});
			if(selectSingleMatch === true && $(select).children().length === 1) {
				$(select).children().get(0).selected = true;
			}else $(select).children().filter('[value='+selVal+']').attr("selected", true);
			$(this).data("lastval",search);
			$(this).data("suspendEvents",false);
		});
	});
};

