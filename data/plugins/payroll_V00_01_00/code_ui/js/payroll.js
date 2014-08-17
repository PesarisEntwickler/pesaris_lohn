
function prlAuszahlenHistoryWindowInit() {
	$('#btnAuszahlenClose').bind('click', function() {
		cb('payroll.auszahlen.closewindow');
		$('#modalContainer').mb_close();
	});  
	$( "#ausz_PeriodenSelect" )
	  .click(function () {
	    var str = "";
	    $( "select option:selected" ).each(function() {
	      str += $( this ).text() + " ";
	    });
		cb('payroll.auszahlen.openHistoryWindow', str); 
	  });
}
function prlAuszahlenGenerateWindowInit() {
	$('#btnAuszahlenReset').bind('click', function() {
		cb('payroll.auszahlen.periodenReset');
		$('#modalContainer').mb_close();
		cb('payroll.prlCalcOvProcess',{'functionNumber':5});
	});  
	$('#btnAuszahlenGenerateWindowClose').bind('click', function() {
		$('#modalContainer').mb_close();
	});  
	$('#btnAuszahlenGenerateFiles').bind('click', function() {
	    var str = "";
	    var dat = document.getElementById('datepicker').value ;
	    if (document.getElementById('chk_payroll_auszahlen_allepersonen').checked) {
	    	str += "ALL_EMPLOYEES";
	    } else {
	    	str += "SELECTED_EMPL";
	    }
	    $( "select option:selected" ).each(function() {
	      str += "##" + $( this ).text();
	    });
		cb('payroll.auszahlen.GenerateFiles', str, dat );
	});
	$( "#sel_prl_auszahlen_zahlstellen" )
	  .click(function () {
		document.getElementById('btnAuszahlenGenerateFiles').disabled = false;  
	  });
	$( "#sel_prl_auszahlen_personalfilter" )
	  .click(function () {
		document.getElementById('chk_payroll_auszahlen_personengruppen').checked = true;
		document.getElementById('chk_payroll_auszahlen_allepersonen').checked = false;
	  });	
}


/*
*************************************
** Employee Overview
*************************************
*/
	var prlPsoDataView;
	var prlPsoColumnFilters = {};
	var prlPsoGrid;
	var prlPsoColumns = [];

	var prlPsoOptions = {
		showHeaderRow: true,
		enableCellNavigation: false,
		enableColumnReorder: false,
		explicitInitialization: true
	};

	var prlPsoData = [];

	function prlPsoFilter(item) {
		for(var columnId in prlPsoColumnFilters) {
			if(columnId !== undefined && prlPsoColumnFilters[columnId] !== "") {
				var c = prlPsoGrid.getColumns()[prlPsoGrid.getColumnIndex(columnId)];
				if(item[c.field].toString().toLowerCase().indexOf(prlPsoColumnFilters[columnId].toString().toLowerCase()) == -1) {
					return false;
				}
			}
		}
		return true;
	}

	function prlPsoSaveSettings() {
		var settings = {};
		settings['quickFilterEnabled'] = $("#prlPsoBtnQFilter").is(':checked');
		if(settings['quickFilterEnabled']) {
			settings['quickFilterValues'] = [];
			for(var columnId in prlPsoColumnFilters) {
				settings['quickFilterValues'].push({'colID': columnId, 'filterValue': prlPsoColumnFilters[columnId]});
			}
		}
		settings['columnsWidth'] = [];
		var cols = prlPsoGrid.getColumns();
		for(var i=0;i<cols.length;i++) settings['columnsWidth'].push(cols[i].width);
		settings['sort'] = prlPsoGrid.getSortColumns();
		cb('payroll.psoSaveSettings',settings);
	}

	function prlPsoSetSettings(param) {
		$('#prlPsoBtnQFilter').attr('checked', param.quickFilterEnabled);
		if(param.quickFilterEnabled) {
			for(var i=0;i<param.quickFilterValues.length;i++) {
				prlPsoColumnFilters[param.quickFilterValues[i].colID] = param.quickFilterValues[i].filterValue;
			}
		}
		var cols = prlPsoGrid.getColumns();
		for(var i=0;i<cols.length;i++) cols[i].width=param.columnsWidth[i];
		prlPsoGrid.setColumns(cols);

		if(param.sort.length>0) {
			prlPsoGrid.setSortColumn(param.sort[0].columnId, param.sort[0].sortAsc);
			prlPsoSortSingleColumn(param.sort[0].columnId, param.sort[0].sortAsc, false);
		}

		prlPsoToggleFilterRow();

		prlPsoGrid.setData(prlPsoDataView);
		prlPsoGrid.updateRowCount();
		prlPsoGrid.render();
	}

	function prlPsoToggleFilterRow() {
		if($("#prlPsoBtnQFilter").is(':checked')) {
			$(prlPsoGrid.getHeaderRow()).show();
			prlPsoGrid.showHeaderRow(true);
		}else{
			$(prlPsoGrid.getHeaderRow()).hide();
			prlPsoGrid.showHeaderRow(false);
			$(prlPsoGrid.getHeaderRow()).find("input").val('');
			for(var columnId in prlPsoColumnFilters) prlPsoColumnFilters[columnId] = "";
			prlPsoDataView.refresh();
		}
		prlPsoGrid.resizeCanvas();
	}

	function prlPsoSortSingleColumn(field, sortAsc, updateGrid) {
		prlPsoDataView.sort(function(a, b){
			var result = a[field] > b[field] ? 1 : a[field] < b[field] ? -1 : 0; 
			return sortAsc ? result : -result;
		});
		if(updateGrid==null) {
			prlPsoGrid.setData(prlPsoDataView);
			prlPsoGrid.updateRowCount();
			prlPsoGrid.render();
		}
	}

	function prlPsoInit() {
		prlPsoDataView = new Slick.Data.DataView();
		prlPsoGrid = new Slick.Grid("#gridEmplOv", prlPsoDataView, prlPsoColumns, prlPsoOptions);
		prlPsoGrid.onSort.subscribe(function (e, args) {
			prlPsoSortSingleColumn(args.sortCol.field, args.sortAsc);
		});

		prlPsoDataView.onRowCountChanged.subscribe(function (e, args) {
			prlPsoGrid.updateRowCount();
			prlPsoGrid.render();
		});

		prlPsoDataView.onRowsChanged.subscribe(function (e, args) {
			prlPsoGrid.invalidateRows(args.rows);
			prlPsoGrid.render();
		});


		$(prlPsoGrid.getHeaderRow()).delegate(":input", "change keyup", function (e) {
			var columnId = $(this).data("columnId");
			if (columnId != null) {
				prlPsoColumnFilters[columnId] = $.trim($(this).val());
				prlPsoDataView.refresh();
			}
		});

		prlPsoGrid.onHeaderRowCellRendered.subscribe(function(e, args) {
			$(args.node).empty();
			$("<input type='text'>")
			   .data("columnId", args.column.id)
			   .val(prlPsoColumnFilters[args.column.id])
			   .appendTo(args.node);
		});

		prlPsoGrid.onClick.subscribe(function(e, args) {
			var cell = prlPsoGrid.getCellFromEvent(e), row = cell.row;
			var item = prlPsoDataView.getItem(row);
			prlPsoOpenEmployeeForm(item.id);
		});

		prlPsoGrid.init();

		prlPsoDataView.beginUpdate();
		prlPsoDataView.setItems(prlPsoData);
		prlPsoDataView.setFilter(prlPsoFilter);
		prlPsoDataView.endUpdate();

		//Button: MA hinzufuegen
		$( "#prlPsoBtnNew" ).button({
			text: false,
			icons: {
				primary: "p-icon-adduser"
			}
		})
		.click(function() {
			prlPsoOpenEmployeeForm(0);
		});
// ----------------------------------------
		//Button: DB Filter
		$( "#prlPsoBtnDFilter" ).button({
			text: false,
			icons: {
				primary: "p-icon-db"
			}
		})
		.click(function() {
			cb('payroll.psoSetDBFilter');
		}).addClass('toolbar-space-left');

		//Button: DB Edit
		$( "#prlPsoBtnDEdit" ).button({
			text: false,
			icons: {
				primary: "p-icon-dbedit"
			}
		})
		.click(function() {
			cb('payroll.EmployeeFilter',{'action':'overview'});
		});
// ----------------------------------------
		//Button: Tabellenfilter
		$( "#prlPsoBtnQFilter" ).button({
			text: false,
			icons: {
				primary: "p-icon-tblfilter"
			}
		})
		.click(function() {
			prlPsoToggleFilterRow();
		});
		$('label[for=prlPsoBtnQFilter]').addClass('toolbar-space-left');

		//Button: Edit Table Columns
		$( "#prlPsoBtnEditCols" ).button({
			text: false,
			icons: {
				primary: "p-icon-editcols"
			}
		})
		.click(function() {
			alert('Tabellenspalten bearbeiten');
		});

		//Button: Einstellungen speichern
		$( "#prlPsoBtnSaveSettings" ).button({
			text: false,
			icons: {
				primary: "p-icon-savesettings"
			}
		})
		.click(function() {
			prlPsoSaveSettings();
		});
// ----------------------------------------
		//Button: Select Form
		$( "#prlPsoBtnForm" ).button({
			text: false,
			icons: {
				primary: "p-icon-window"
			}
		})
		.click(function() {
			cb('payroll.prlVlSelectForm',0);
		}).addClass('toolbar-space-left');

		//Button: Edit Form
		$( "#prlPsoBtnFormEdit" ).button({
			text: false,
			icons: {
				primary: "p-icon-windowedit"
			}
		})
		.click(function() {
			var objWnd = $('#employeeForm');
			if ( objWnd.length > 0 ) {
				if(objWnd.mb_getState('iconized')) objWnd.mb_iconize();
				else objWnd.mb_bringToFront();
				if(!prlVlDesignMode) cb('payroll.prlVlEditForm',{'f':'dmAlert'});
			}else{
				cb('payroll.prlVlEditForm',{'f':'layoutOv'});
			}
		});
// ----------------------------------------
	}

	function prlPsoOpenEmployeeOverview() {
		var objWnd = $('#employeeOverview');
		if ( objWnd.length > 0 ) {
			if(objWnd.mb_getState('iconized')) objWnd.mb_iconize();
			else objWnd.mb_bringToFront();
		}else{
			cb('payroll.prlPsoEmployeeOverview');
		}
	}
/*
*************************************
** Employee Edit Form
*************************************
*/
var prlVlMain = "#prlVlTabContainer";
var prlVlDirty = false;
var prlVlDesignMode = false;
var prlVlDesignParam = {};
var prlVlCount = 0;
var prlVlFieldDef = {};
var prlVlFldDefFP = "";
var prlVlRid = 0;

function prlVlSave() {
//alert("0"); //TODO: XXX
	if(prlVlDesignMode) {
		var retObj = [];
		//hier werden design-daten ausgelesen und in einen JSON-Array geschrieben, der anschliessend via ajax an den Server übermittelt wird
		$('#prlVlTabs > li > ul > li').not('[add=y]').each(function(index) {
			var tabID = $(this).attr('ptab-assignment');
			var tabRow = $(this).parent().parent().is(':last-child') ? 1 : 2;
			var elementsUsed = [];
			$(prlVlMain+' > div[id='+tabID+'] > ul > li[eid]').each(function(index) {
				elementsUsed.push($(this).attr('eid'));
			});
			retObj.push({'tabName':$(this).find('a').text(), 'tabID':tabID, 'tabRow':tabRow, 'elements':elementsUsed});
		});
//		alert(retObj.toSource()); //TODO: löschen, da nur zu Testzwecken...
		cb("payroll.prlVlSaveFormLayout",{"layoutID":prlVlDesignParam.lid,"layoutName":prlVlDesignParam.lname,"layoutTmp":prlVlDesignParam.tmp,"layoutGlob":prlVlDesignParam.glob,"layoutElements":retObj});
	}else{
		var jsonItem = {};
		//hier werden benutzereingaben ausgelesen und in einen JSON-Array geschrieben, der anschliessend via ajax an den Server übermittelt wird
		$('#prlVlTabs > li > ul > li').each(function(index) {
			var tabID = $(this).attr('ptab-assignment');
			$(prlVlMain+' > div[id='+tabID+'] > ul > li[eid]').each(function(index) {
				switch(prlVlFieldDef[$(this).attr('eid')].type) {
				case 100: //ZipCity
					jsonItem[$(this).find('input[st=zip]').attr('id')] = $(this).find('input[st=zip]').val();
					jsonItem[$(this).find('input[st=city]').attr('id')] = $(this).find('input[st=city]').val();
					break;
				case 110: //table with input fields
					var recs=[];
					var element = prlVlFieldDef[$(this).attr('eid')];
					$(prlVlMain+' > div > ul > li[eid='+$(this).attr('eid')+'] .prlVltblBody > tbody > tr').each(function(index) {
						var jsonRow = {};
						var fields = $(this).children('td');
						jsonRow['id'] = $(this).attr('rid');
						for(var i=0;i<element.tablecols.length;i++){
							jsonRow[element.tablecols[i].colID] = fields.eq(i).attr('realVal');
						}
						recs.push(jsonRow);
					});
					jsonItem[$(this).attr('eid')] = recs;
					break;
				case 2: //checkbox
					jsonItem[$(this).attr('eid')] = $(this).find('input[type=checkbox]').is(':checked') ? 1 : 0;
					break;
				default:
					jsonItem[$(this).attr('eid')] = $(this).find('input[type=text],select').val();
					break;
				}
			});
		});
		cb("payroll.prlVlSaveFormData",{"rid":prlVlRid,"data":jsonItem});
//		alert(jsonItem.toSource()); //TODO: löschen, da nur zu Testzwecken...
	}
}

function prlVlCreateForm(tabConfig) {
	for(var i=0;i<tabConfig.length;i++){
		prlVlInsertTab('prlVlTab'+tabConfig[i].tabRow, tabConfig[i].tabName, tabConfig[i].tabID);
		if(!prlVlDesignMode) $(prlVlMain+' > #'+tabConfig[i].tabID).hide();

		for(var k=0;k<tabConfig[i].elements.length;k++){
			prlVlInsertField(tabConfig[i].tabID, tabConfig[i].elements[k]);
		}
	}
	if(!prlVlDesignMode) {
		prlVlInitTabs();
		if($('#prlVlTab2 li').length < 1) $('#prlVlTab2').parent().remove();
	}

	$('#prlVlTab1 li:first-child a').click();
}

function prlVlValidation(elementId, value) {
	var element = prlVlFieldDef[elementId];
	if(element.validate) {
		var passed = true;
		switch(element.type) {
		case 1: //text
			if(element.rgx!='') {
				if(!element.rgx.test(value)) passed = false;
			}
			break;
		case 2: //checkbox
			break;
		case 3: //Decimalnumber
			if(!element.rgx.test(value)) passed = false;
			if(passed) {
				if(value < element.vMin || value > element.vMax) passed = false;
			}
			break;
		case 4: //select
			break;
		case 5: //date
			if(!element.rgx.test(value)) passed = false;
			break;
		}
		if(value=='' && !element.mandatory) passed = true;
		if(!passed) {
			$(prlVlMain+' #'+elementId).css('background-color','#f88');
		}else{
			$(prlVlMain+' #'+elementId).css('background-color','');
		}
	}
}

function prlVlRemoveField(elementId) {
	$(prlVlMain+' li[eid='+elementId+']').remove();
	var element = prlVlFieldDef[elementId];
	element.inUse = false;
}

function prlVlInsertTab(tabLineNumber, tabName, techID) {
	if(techID==null) {
		techID = "new1";
		var counter = 1;
		while($(prlVlMain+" #"+techID).length > 0){
			counter++;
			techID = "new"+counter;
		}
	}

	$('#'+tabLineNumber).append('<li ptab-assignment="'+techID+'" tabindex="0" class="ui-state-default ui-corner-top"><a tabindex="-1" class="ui-tabs-anchor" href="#'+techID+'" style="padding:3px;">'+tabName+'</a></li>');
	$(prlVlMain).append('<div id="'+techID+'"><ul style="list-style:none;list-style-position:inside; padding:0; margin-left:10px; margin-top:15px;"></ul></div>');

	if(prlVlDesignMode) {
		var addLI = $('#'+tabLineNumber).find('li[add=y]');
		if(!addLI.is(':last-child')) {
			addLI.appendTo(addLI.parent());
		}
		prlVlInitTabs('li[ptab-assignment='+techID+'] a');
		$('<li><p class="prlVlAdd">&#10010;</p></li>').appendTo(prlVlMain+' #'+techID+' > ul');
		prlVlBindAddEl();
		$(prlVlMain+' div ul').sortable();
		$(prlVlMain+' > #'+techID).hide();
	}
}

function prlVlRemoveTab(identifier) {
	$('li[ptab-assignment='+identifier+']').remove();
	$(prlVlMain+' #'+identifier+' li[eid]').each(function(index) {
		prlVlFieldDef[$(this).attr('eid')].inUse = false;
	});
	$(prlVlMain+' #'+identifier).remove();
}

function prlVlTblSave(elementId) {
	var element = prlVlFieldDef[elementId];
	var allPassed = true;

	$.each(element.fieldset, function(k, v) {
		var subelement = $(prlVlMain+' #'+elementId+'_'+k);
		var passed = true;
		var value = subelement.val();
		//Mandatory-Check
		if(v.mandatory && value=='') passed=false;
		//Validity-Check
		if(passed && v.validate) {
			switch(v.type) {
			case 1: //text
			case 5: //date
				if(v.rgx!='') {
					if(!v.rgx.test(value)) passed = false;
				}
				break;
			case 3: //Decimalnumber
				if(!v.rgx.test(value)) passed = false;
				if(passed) {
					if(value < v.vMin || value > v.vMax) passed = false;
				}
				break;
			}
			if(value=='' && !v.mandatory) passed = true;
		}
		if(!passed) {
			allPassed = false;
			subelement.css('background-color','#f88');
		}else{
			subelement.css('background-color','');
		}

	});
	if(allPassed) {
		if($(prlVlMain+' > div > ul > li[eid='+elementId+'] .prlVltblBody > tbody > tr').hasClass('prlVlTblEdit')) {
			var tblCols= $(prlVlMain+' > div > ul > li[eid='+elementId+'] .prlVltblBody > tbody > tr.prlVlTblEdit > td');
			for(var i=0;i<element.tablecols.length;i++){
				var subelement = $(prlVlMain+' #'+elementId+'_'+element.tablecols[i].colID);
				tblCols.eq(i).attr('realVal', subelement.val());
				tblCols.eq(i).text(subelement.is("select") ? subelement.find("option:selected").text() : subelement.val());
			}
			prlVlTblCancel(elementId);
		}else{
			prlVlCount++;
			var tblCols='<tr rid="new'+prlVlCount+'">';
			for(var i=0;i<element.tablecols.length;i++){
				var subelement = $(prlVlMain+' #'+elementId+'_'+element.tablecols[i].colID);
				tblCols += '<td realVal="'+subelement.val()+'" style="width:'+element.tablecols[i].width+'px;">'+(subelement.is("select") ? subelement.find("option:selected").text() : subelement.val())+'</td>';
				subelement.val('');
			}
			tblCols += '</tr>';
			$(prlVlMain+' > div > ul > li[eid='+elementId+'] .prlVltblBody > tbody:last').append(tblCols);
			var curTR = $(prlVlMain+' > div > ul > li[eid='+elementId+'] .prlVltblBody > tbody > tr');
			curTR.unbind();
			curTR.bind('click', function(e) {
				if($('.prlVltblBody tr').hasClass('prlVlTblEdit')) return;

				$('#prlVlTblMenu').css('top',e.pageY-15);
				$('#prlVlTblMenu').css('left',e.pageX-70);
				$('#prlVlTblMenu').attr('rid', $(this).attr('rid'));
				$('#prlVlTblMenu').attr('eid', $(this).parent().parent().parent().parent().parent().attr('eid'));
				$('#prlVlTblMenu').show();
			});
		}
	}
}

function prlVlTblCancel(elementId) {
	buttons = $(prlVlMain+' > div > ul > li[eid='+elementId+'] ul > li > button');
	buttons.eq(0).parent().parent().find('input[type=text]').each(function(index) {
		$(this).val('');
		$(this).css('background-color','');
	});

	buttons.eq(0).html('+');
	$(prlVlMain+' > div > ul > li[eid='+elementId+'] .prlVltblBody > tbody > tr').removeClass('prlVlTblEdit');
	buttons.eq(1).hide();
}

function prlVlInsertField(tab, elementId) {
	var insertionPoint = $('#'+tab+' ul');
	var element = prlVlFieldDef[elementId];

	if(element.inUse) return;
	var designModeControls = prlVlDesignMode ? '<p class="prlVlMv">&#8597;</p><p class="prlVlDl">&#10005;</p>' : '';
	var disabled = element.disabled ? ' disabled="disabled"' : '';
	var elementWidth = 'prlVlWidth'+element.guiWidth;
	switch(element.type) {
	case 1: //text
		insertionPoint.append('<li eid="'+elementId+'">'+designModeControls+'<label for="'+elementId+'">'+element.label+':</label><input type="text" class="'+elementWidth+'"'+(element.len>0 ? ' maxlength="'+element.len+'"' : '')+' id="'+elementId+'"'+disabled+'></li>');
		element.inUse = true;
		break;
	case 2: //checkbox
		insertionPoint.append('<li eid="'+elementId+'">'+designModeControls+'<label for="'+elementId+'">'+element.label+':</label><input type="checkbox" id="'+elementId+'"'+disabled+'></li>');
		element.inUse = true;
		break;
	case 3: //Decimalnumber
		insertionPoint.append('<li eid="'+elementId+'">'+designModeControls+'<label for="'+elementId+'">'+element.label+':</label><input type="text" class="'+elementWidth+'"'+(element.len>0 ? ' maxlength="'+element.len+'"' : '')+' id="'+elementId+'"'+disabled+'></li>');
		element.inUse = true;
		break;
	case 4: //select
		var optList="";
		for(var i=0;i<element.options.length;i++){
			optList += '<option value="'+element.options[i][0]+'">'+element.options[i][1]+'</option>';
		}
		insertionPoint.append('<li eid="'+elementId+'">'+designModeControls+'<label for="'+elementId+'">'+element.label+':</label><select id="'+elementId+'" class="'+elementWidth+'"'+disabled+'>'+optList+'</select></li>');
		element.inUse = true;
		break;
	case 5: //date
		insertionPoint.append('<li eid="'+elementId+'">'+designModeControls+'<label for="'+elementId+'">'+element.label+':</label><input type="text" class="'+elementWidth+'"'+(element.len>0 ? ' maxlength="'+element.len+'"' : '')+' id="'+elementId+'"'+disabled+'></li>');
		element.inUse = true;
		break;
	case 100: //zip/city
		insertionPoint.append('<li eid="'+elementId+'">'+designModeControls+'<label for="'+elementId+'">'+element.label+':</label><input type="text" st="zip"'+(element.fieldset.ZIP.len>0 ? ' maxlength="'+element.fieldset.ZIP.len+'"' : '')+' id="'+element.fieldset.ZIP.id+'"'+disabled+'><input type="text" st="city"'+(element.fieldset.City.len>0 ? ' maxlength="'+element.fieldset.City.len+'"' : '')+' id="'+element.fieldset.City.id+'"'+disabled+'></li>');
		element.inUse = true;
		break;
	case 110: //table with input fields
		var tblCols='';
		var fields='';
		for(var i=0;i<element.tablecols.length;i++){
			tblCols += '<li assignField="'+element.tablecols[i].colID+'" style="width:'+element.tablecols[i].width+'px;">'+element.tablecols[i].label+'</li>';
		}
		$.each(element.fieldset, function(k, v) {
			fields += '<li><label for="'+k+'">'+v.label+':</label><br/>';
			disabled = v.disabled ? ' disabled="disabled"' : '';
			switch(v.type) {
			case 1: //text
			case 3: //Decimalnumber
			case 5: //date
				fields += '<input type="text"'+(v.len>0 ? ' maxlength="'+v.len+'"' : '')+' id="'+elementId+'_'+k+'" style="width:'+v.width+'px;"'+disabled+'>';
				break;
			case 2: //checkbox
				fields += '<input type="checkbox" id="'+elementId+'_'+k+'"'+disabled+'>';
				break;
			case 4: //select
				var optList;
				for(var i=0;i<v.options.length;i++){
					optList += '<option value="'+v.options[i][0]+'">'+v.options[i][1]+'</option>';
				}
				fields += '<select id="'+elementId+'_'+k+'" style="width:'+v.width+'px;"'+disabled+'>'+optList+'</select>';
				break;
			}
			fields += '</li>';
		});
		if(fields!='') fields = '<ul class="prlVlFieldLine">'+fields+'<li><button act="1" onclick="prlVlTblSave(\''+elementId+'\');">+</button><button act="1" onclick="prlVlTblCancel(\''+elementId+'\');" style="display:none;">&#10007;</button></li></ul><br style="clear:left;" />';
		insertionPoint.append('<li eid="'+elementId+'">'+designModeControls+'<div class="prlVltblWrapper"><ul class="prlVltblHead">'+tblCols+'</ul><div class="prlVltblContainer"><table class="prlVltblBody"><tbody></tbody></table></div></div>'+fields+'</li>');
		element.inUse = true;
		break;
	}
	if((element.validate || element.callback) && element.type<100) {
		if(element.validate && element.callback)
			$('li[eid='+elementId+'] input').bind('change', function() {
				prlVlValidation(elementId, $(this).val());
				cb('payroll.prlVlCallback',{"fieldName":$(this).attr('id'),"value":$(this).val(),"rid":prlVlRid});
			});
		else if(element.validate)
			$('li[eid='+elementId+'] input').bind('change', function() {
				prlVlValidation(elementId, $(this).val());
			});
		else if(element.callback)
			$('li[eid='+elementId+'] input, li[eid='+elementId+'] select').bind('change', function() {
				cb('payroll.prlVlCallback',{"fieldName":$(this).attr('id'),"value":$(this).val(),"rid":prlVlRid});
			});
	}
	if(prlVlDesignMode) {
		$('.prlVlDl').unbind();
		$('.prlVlDl').bind('click', function() {
			prlVlRemoveField($(this).parent().attr('eid'));
		});

		var addLI = insertionPoint.find('.prlVlAdd').parent();
		if(!addLI.is(':last-child')) {
			addLI.appendTo(addLI.parent());
		}
	}
}

function prlVlFill(values) {
	var element;
	for(var i=0;i<values.length;i++){
		element = prlVlFieldDef[values[i][0]];
		if(element.inUse) {
			switch(element.type) {
			case 1: //text
			case 3: //Decimalnumber
			case 4: //select
			case 5: //date
				$(prlVlMain+' #'+values[i][0]).val(values[i][1]);
				break;
			case 2: //checkbox
				$(prlVlMain+' #'+values[i][0]).attr('checked', (values[i][1]==1 ? true : false));
				break;
			case 100: //ZipCity
				$(prlVlMain+' > div > ul > li[eid='+values[i][0]+'] > input[st=zip]').val(values[i][1].zip);
				$(prlVlMain+' > div > ul > li[eid='+values[i][0]+'] > input[st=city]').val(values[i][1].city);
				break;
			case 110: //table with input fields
				$(prlVlMain+' > div > ul > li[eid='+values[i][0]+'] .prlVltblBody > tbody > tr').remove();
				var tblCols='';
				for(var k=0;k<values[i][1].length;k++){
					tblCols += '<tr rid="'+values[i][1][k][0]+'">';
					for(var l=1;l<values[i][1][k].length;l++){
						if(element.fieldset[element.tablecols[l-1].colID].type==4) { //special treatment for values from SELECT controls
							var trnsval = values[i][1][k][l];
							for(var m=0;m<element.fieldset[element.tablecols[l-1].colID].options.length;m++){
								if(element.fieldset[element.tablecols[l-1].colID].options[m][0]==trnsval) {
									trnsval=element.fieldset[element.tablecols[l-1].colID].options[m][1];
									break;
								}
							}
							tblCols += '<td realVal="'+values[i][1][k][l]+'" style="width:'+element.tablecols[l-1].width+'px;">'+trnsval+'</td>';
						}else tblCols += '<td realVal="'+values[i][1][k][l]+'" style="width:'+element.tablecols[l-1].width+'px;">'+values[i][1][k][l]+'</td>';
					}
					tblCols += '</tr>';
				}
				$(prlVlMain+' > div > ul > li[eid='+values[i][0]+'] .prlVltblBody > tbody:last').append(tblCols);

				$(prlVlMain+' > div > ul > li[eid='+values[i][0]+'] .prlVltblBody > tbody > tr').unbind();
				$(prlVlMain+' > div > ul > li[eid='+values[i][0]+'] .prlVltblBody > tbody > tr').bind('click', function(e) {
					if($('.prlVltblBody tr').hasClass('prlVlTblEdit')) return;

					$('#prlVlTblMenu').css('top',e.pageY-15);
					$('#prlVlTblMenu').css('left',e.pageX-70);
					$('#prlVlTblMenu').attr('rid', $(this).attr('rid'));
					$('#prlVlTblMenu').attr('eid', $(this).parent().parent().parent().parent().parent().attr('eid'));
					$('#prlVlTblMenu').show();
				});
				break;
			}
		}
	}
	$(prlVlMain+' > div[id] > ul > li[eid] > input[type=text]').each(function(index) {
		$(this).css('background-color','');
	});
}

function prlVlBindAddEl() {
	$('.prlVlAdd').unbind();
	$('.prlVlAdd').bind('click', function() {
		var elSelect = $("#availableElements");
		elSelect.empty();
		for(var key in prlVlFieldDef) {
			if(prlVlFieldDef.hasOwnProperty(key) && !prlVlFieldDef[key].inUse) {
				elSelect.append($('<option></option>').attr("value", key).text(prlVlFieldDef[key].label));
			}
		}
		$('#esTarget').val($(this).parent().parent().parent().attr('id'));
		$('#prlVlAddElement').show();
	});
}

function prlVlInit() {
	if(!prlVlDesignMode) $.each(prlVlFieldDef, function(k, v) { prlVlFieldDef[k].inUse = false; });
	prlVlInitTabs();

	if(prlVlDesignMode) {
		$( "#prlVlTab1, #prlVlTab2" ).sortable({
		connectWith: ".ui-tabs-nav"
		}).disableSelection();

		$(prlVlMain+' div ul').sortable();
//		$('<li><p class="prlVlAdd">&#10010;</p></li>').appendTo(prlVlMain+' div ul');
		$('<li ptab-assignment="none" add="y" tabindex="-1" class="ui-state-default ui-corner-top"><a tabindex="-1" class="ui-tabs-anchor" href="#tabs-3" style="padding:3px; color:#080;">&#10010;</a></li>').appendTo(prlVlMain+' #prlVlTabs ul');

		$('li[add=y] a').bind('click', function() {
			$('#atTarget').val($(this).parent().parent().attr('id'));
			$('#prlVlAddTab').show();
			return false;
		});

		$('.prlVlDl').bind('click', function() {
			prlVlRemoveField($(this).parent().attr('eid'));
		});

		prlVlBindAddEl();
	}
	$('#prlVlAddElement').hide();
	$('#prlVlAddTab').hide();
	$('#prlVlEditTab').hide();

	$('#prlVlTblMenu').menu();
	$('#prlVlTblMenu').hide();
	$('#prlVlTblMenu').css('width','100');
	$('#prlVlTblMenu').css('position','fixed');
	$('#prlVlTblMenu').bind('mouseleave', function(e) { $(this).hide(); });
	$('#prlVlTblMenu li a').bind('click', function(e) {
		var curRID = $('#prlVlTblMenu').attr('rid'); //record nummer
		var curEID = $('#prlVlTblMenu').attr('eid'); //elementID, z.B. tbl_education
		var curTR = $(prlVlMain+' > div > ul > li[eid='+curEID+'] .prlVltblBody > tbody > tr[rid='+curRID+']');

		switch($(this).attr('act')) {
		case '1':
			$('.prlVltblBody tr').removeClass('prlVlTblEdit');
			curTR.addClass('prlVlTblEdit');
			var columns = prlVlFieldDef[curEID].tablecols;

			var tdCollection = curTR.children('td[realVal]');
			for(var i=0;i<columns.length;i++){
				$(prlVlMain+' #'+curEID+'_'+columns[i].colID).val(tdCollection.eq(i).attr('realVal'));
			}
			var btn = $(prlVlMain+' > div > ul > li[eid='+curEID+'] > ul > li > button');
			btn.eq(0).html('&#10003;'); //display OK sign
			btn.eq(1).show();
			break;
		case '2':
			if(curTR.attr('rid').indexOf("new") == -1) {
				curTR.attr('rid', 'remove_'+curTR.attr('rid'));
				curTR.hide();
			}else{
				curTR.remove();
			}
			break;
		}
		$('#prlVlTblMenu').hide();

		return false;
	});
}

function prlVlInitTabs(tabID) {
	var obj = tabID==null ? '.ui-tabs-nav li a' : tabID;
	$(obj).bind('click', function() {
		$('.ui-tabs-nav li').removeClass('ui-tabs-active');
		$('.ui-tabs-nav li').removeClass('ui-state-active');
		$(this).parent().addClass('ui-tabs-active');
		$(this).parent().addClass('ui-state-active');
		var grandparentLI = $(this).parent().parent().parent();
		if(grandparentLI.index() == 0) {
			var first = grandparentLI;
			var last = grandparentLI.next();
			first.appendTo(first.parent());
			last.prependTo(last.parent());
		}
		$(prlVlMain+' > div').hide();
		$(prlVlMain+' > div[id=' + $(this).parent().attr('ptab-assignment') + ']').show();
		return false;
	});
	if(prlVlDesignMode) {
		$(obj).bind('dblclick', function(){
			$('#etName').val($(this).text());
			$('#etTarget').val($(this).parent().attr('ptab-assignment'));
			$('#prlVlEditTab').show();
		});
	}
}

function prlPsoOpenEmployeeForm(empId) {
	var objWnd = $('#employeeForm');
	if ( objWnd.length > 0 ) {
		if(objWnd.mb_getState('iconized')) objWnd.mb_iconize();
		else objWnd.mb_bringToFront();
		cb('payroll.prlVlOpenForm',{"id":empId,"dirty":prlVlDirty?1:0,"mode":prlVlDesignMode?1:0,"wndStatus":1,"fldDefFP":prlVlFldDefFP});
	}else{
		cb('payroll.prlVlOpenForm',{"id":empId,"dirty":prlVlDirty?1:0,"mode":prlVlDesignMode?1:0,"wndStatus":0,"fldDefFP":prlVlFldDefFP});
	}
}

/*
*************************************
** Configuration Overview
*************************************
*/
function prlCfgOpenMainWindow() {
	var objWnd = $('#prlConfigMain');
	if ( objWnd.length > 0 ) {
		if(objWnd.mb_getState('iconized')) objWnd.mb_iconize();
		else objWnd.mb_bringToFront();
	}else{
		cb('payroll.OpenConfigMain');
	}
}

var prlCfg = {	'CfgCmpc':{ 'BtnNew':{'obj':'btn','available':true,'function':function() { cb('payroll.ConfigEditFormOpen',{'section':'CfgCmpc','id':'0'}); }}, 'BtnQFilter':{'obj':'btn','available':true,'function':function() { prlCfgToggleFilterRow(); }}, 'BtnSaveSettings':{'obj':'btn','available':true,'function':function() { prlCfgSaveSettings(); }}, 'BtnInsr':{'obj':'btn','available':false,'function':null}, 'BtnCode':{'obj':'btn','available':false,'function':null}, 'BtnFinMgmtAcc':{'obj':'btn','available':false,'function':0}, 'BtnFldMod':{'obj':'btn','available':false,'function':null}, 'BtnSettings':{'obj':'btn','available':false,'function':null}, 'MnuEdit':{'obj':'menuItem','available':true,'function':function(rid) { cb('payroll.ConfigEditFormOpen',{'section':'CfgCmpc','id':rid}); }}, 'MnuDelete':{'obj':'menuItem','available':true,'function':function(rid) { cb('payroll.ConfigEditFormDelete',{'section':'CfgCmpc','id':rid}); }}, 'grid':{'obj':'grid','available':true,'data':[], 'gridObj':null, 'dataView':null, 'columnFilters':{}, 'columns':[]} }, 
				'CfgLoac':{ 'BtnNew':{'obj':'btn','available':true,'function':function() { prlLoacOpenForm(0); }}, 'BtnQFilter':{'obj':'btn','available':true,'function':function() { prlCfgToggleFilterRow(); }}, 'BtnSaveSettings':{'obj':'btn','available':true,'function':function() { prlCfgSaveSettings(); }}, 'BtnInsr':{'obj':'btn','available':false,'function':null}, 'BtnCode':{'obj':'btn','available':false,'function':null}, 'BtnFinMgmtAcc':{'obj':'btn','available':true,'function':function() { cb('payroll.FinMgmtAccOpen'); }}, 'BtnFldMod':{'obj':'btn','available':true,'function':function() { cb('payroll.ConfigFldModOverview'); }}, 'BtnSettings':{'obj':'btn','available':false,'function':null}, 'MnuEdit':{'obj':'menuItem','available':true,'function':function(rid) { prlLoacOpenForm(rid); }}, 'MnuDelete':{'obj':'menuItem','available':true,'function':function(rid) { cb('payroll.prlLoacDelete',{"id":rid}); }}, 'grid':{'obj':'grid','available':true,'data':[], 'gridObj':null, 'dataView':null, 'columnFilters':{}, 'columns':[]} }, 
				'CfgInsc':{ 'BtnNew':{'obj':'btn','available':true,'function':function() { cb('payroll.ConfigEditFormOpen',{'section':'CfgInsc','id':'0','InsuranceType':$('#prlCfgInscTab select').val()}); }}, 'BtnQFilter':{'obj':'btn','available':true,'function':function() { prlCfgToggleFilterRow(); }}, 'BtnSaveSettings':{'obj':'btn','available':true,'function':function() { prlCfgSaveSettings(); }}, 'BtnInsr':{'obj':'btn','available':true,'function':function() { cb('payroll.ConfigEditFormOpen',{'section':'CfgInscInsr'}); }}, 'BtnCode':{'obj':'btn','available':true,'function':function() { cb('payroll.ConfigEditFormOpen',{'section':'CfgInscCode','InsuranceType':$('#prlCfgInscTab select').val()}); }}, 'BtnFinMgmtAcc':{'obj':'btn','available':false,'function':0}, 'BtnFldMod':{'obj':'btn','available':false,'function':null}, 'BtnSettings':{'obj':'btn','available':false,'function':null}, 'MnuEdit':{'obj':'menuItem','available':true,'function':function(rid) { cb('payroll.ConfigEditFormOpen',{'section':'CfgInsc','id':rid,'InsuranceType':$('#prlCfgInscTab select').val()}); }}, 'MnuDelete':{'obj':'menuItem','available':true,'function':function(rid) { cb('payroll.ConfigEditFormDelete',{'section':'CfgInsc','id':rid,'InsuranceType':$('#prlCfgInscTab select').val()}); }}, 'grid':{'obj':'grid','available':true,'data':[], 'gridObj':null, 'dataView':null, 'columnFilters':{}, 'columns':[]} }, 
				'CfgSyac':{ 'BtnNew':{'obj':'btn','available':false,'function':0}, 'BtnQFilter':{'obj':'btn','available':true,'function':function() { prlCfgToggleFilterRow(); }}, 'BtnSaveSettings':{'obj':'btn','available':true,'function':function() { prlCfgSaveSettings(); }}, 'BtnInsr':{'obj':'btn','available':false,'function':null}, 'BtnCode':{'obj':'btn','available':false,'function':null}, 'BtnFinMgmtAcc':{'obj':'btn','available':false,'function':0}, 'BtnFldMod':{'obj':'btn','available':false,'function':null}, 'BtnSettings':{'obj':'btn','available':false,'function':null}, 'MnuEdit':{'obj':'menuItem','available':true,'function':function(rid) { cb('payroll.ConfigEditFormOpen',{'section':'CfgSyac','id':rid}); }}, 'MnuDelete':{'obj':'menuItem','available':false,'function':null}, 'grid':{'obj':'grid','available':true,'data':[], 'gridObj':null, 'dataView':null, 'columnFilters':{}, 'columns':[]} }, 
				'CfgDasc':{ 'BtnNew':{'obj':'btn','available':true,'function':function() { cb('payroll.ConfigEditFormOpen',{'section':'CfgDasc','id':'0'}); }}, 'BtnQFilter':{'obj':'btn','available':true,'function':function() { prlCfgToggleFilterRow(); }}, 'BtnSaveSettings':{'obj':'btn','available':true,'function':function() { prlCfgSaveSettings(); }}, 'BtnInsr':{'obj':'btn','available':false,'function':null}, 'BtnCode':{'obj':'btn','available':false,'function':null}, 'BtnFinMgmtAcc':{'obj':'btn','available':false,'function':null}, 'BtnFldMod':{'obj':'btn','available':true,'function':function() { cb('payroll.ConfigImportDasRates'); }}, 'BtnSettings':{'obj':'btn','available':true,'function':function() { cb('payroll.ConfigEditFormOpen',{'section':'CfgDascGlob','id':'0'}); }}, 'MnuEdit':{'obj':'menuItem','available':true,'function':function(rid) { cb('payroll.ConfigEditFormOpen',{'section':'CfgDasc','id':rid}); }}, 'MnuDelete':{'obj':'menuItem','available':true,'function':function(rid) { cb('payroll.ConfigEditFormDelete',{'section':'CfgDasc','id':rid}); }}, 'grid':{'obj':'grid','available':true,'data':[], 'gridObj':null, 'dataView':null, 'columnFilters':{}, 'columns':[]} }
			};

var prlCfgOptions = {
	showHeaderRow: true,
	enableCellNavigation: false,
	enableColumnReorder: false,
	explicitInitialization: true
};

function prlCfgEditFormInit(section,data) {
	$.each(data, function(k, v) {
		var o = $('#prlFormCfg_'+k);
		if(o.length > 0) {
			if(o.is(":checkbox") || o.is(":radio")) o.prop("checked",(v==1?true:false));
			else o.val(v);
		}
	});
}
function prlCfgEditFormSave(curCfgSection) {
	var frmData = {};
	$('input[id^="prlFormCfg_"], select[id^="prlFormCfg_"]').each(function( index ) {
		if($(this).is(":checkbox") || $(this).is(":radio")) frmData[$(this).attr('id').substring(11)] = $(this).is(':checked') ? 1 : 0;
		else frmData[$(this).attr('id').substring(11)] = $(this).val();
	});
	cb('payroll.ConfigEditFormSave',{'section':curCfgSection,'data':frmData});
}

	function prlCfgFilter(item) {
		var curCfgSection = $('#prlCfgTabs > ul > li').eq($('#prlCfgTabs').tabs('option', 'active')).attr('sct');
		for(var columnId in prlCfg[curCfgSection].grid.columnFilters) {
			if(columnId !== undefined && prlCfg[curCfgSection].grid.columnFilters[columnId] !== "") {
				var c = prlCfg[curCfgSection].grid.gridObj.getColumns()[prlCfg[curCfgSection].grid.gridObj.getColumnIndex(columnId)];
				if(item[c.field].toString().toLowerCase().indexOf(prlCfg[curCfgSection].grid.columnFilters[columnId].toString().toLowerCase()) == -1) {
					return false;
				}
			}
		}
		return true;
	}

	function prlCfgSaveSettings() {
		var settings = {};
		var curCfgSection = $('#prlCfgTabs > ul > li').eq($('#prlCfgTabs').tabs('option', 'active')).attr('sct');
		settings['quickFilterEnabled'] = $("#prl"+curCfgSection+"BtnQFilter").is(':checked');
		if(settings['quickFilterEnabled']) {
			settings['quickFilterValues'] = [];
			for(var columnId in prlCfg[curCfgSection].grid.columnFilters) {
				settings['quickFilterValues'].push({'colID': columnId, 'filterValue': prlCfg[curCfgSection].grid.columnFilters[columnId]});
			}
		}
		settings['columnsWidth'] = [];
		var cols = prlCfg[curCfgSection].grid.gridObj.getColumns();
		for(var i=0;i<cols.length;i++) settings['columnsWidth'].push(cols[i].width);
		settings['sort'] = prlCfg[curCfgSection].grid.gridObj.getSortColumns();
		cb('payroll.prlCfgSaveSettings',{'section':curCfgSection,'settings':settings});
	}

	function prlCfgSetSettings(param,curCfgSection) {
		curCfgSection = curCfgSection==null ? $('#prlCfgTabs > ul > li').eq($('#prlCfgTabs').tabs('option', 'active')).attr('sct') : curCfgSection;
		$("#prl"+curCfgSection+"BtnQFilter").attr('checked', param.quickFilterEnabled);
		if(param.quickFilterEnabled) {
			for(var i=0;i<param.quickFilterValues.length;i++) {
				prlCfg[curCfgSection].grid.columnFilters[param.quickFilterValues[i].colID] = param.quickFilterValues[i].filterValue;
			}
		}
		var cols = prlCfg[curCfgSection].grid.gridObj.getColumns();
		for(var i=0;i<cols.length;i++) cols[i].width=param.columnsWidth[i];
		prlCfg[curCfgSection].grid.gridObj.setColumns(cols);

		if(param.sort.length>0) {
			prlCfg[curCfgSection].grid.gridObj.setSortColumn(param.sort[0].columnId, param.sort[0].sortAsc);
			prlCfgSortSingleColumn(param.sort[0].columnId, param.sort[0].sortAsc, false);
		}

		prlCfgToggleFilterRow(curCfgSection);

		prlCfg[curCfgSection].grid.gridObj.setData(prlCfg[curCfgSection].grid.dataView);
		prlCfg[curCfgSection].grid.gridObj.updateRowCount();
		prlCfg[curCfgSection].grid.gridObj.render();
	}

	function prlCfgToggleFilterRow(curCfgSection) {
		curCfgSection = curCfgSection==null ? $('#prlCfgTabs > ul > li').eq($('#prlCfgTabs').tabs('option', 'active')).attr('sct') : curCfgSection;
		if($("#prl"+curCfgSection+"BtnQFilter").is(':checked')) {
			$(prlCfg[curCfgSection].grid.gridObj.getHeaderRow()).show();
			prlCfg[curCfgSection].grid.gridObj.showHeaderRow(true);
		}else{
			$(prlCfg[curCfgSection].grid.gridObj.getHeaderRow()).hide();
			prlCfg[curCfgSection].grid.gridObj.showHeaderRow(false);
			$(prlCfg[curCfgSection].grid.gridObj.getHeaderRow()).find("input").val('');
			for(var columnId in prlCfg[curCfgSection].grid.columnFilters) prlCfg[curCfgSection].grid.columnFilters[columnId] = "";
			prlCfg[curCfgSection].grid.dataView.refresh();
		}
		prlCfg[curCfgSection].grid.gridObj.resizeCanvas();
	}

	function prlCfgSortSingleColumn(field, sortAsc, updateGrid) {
		var curCfgSection = $('#prlCfgTabs > ul > li').eq($('#prlCfgTabs').tabs('option', 'active')).attr('sct');
		prlCfg[curCfgSection].grid.dataView.sort(function(a, b){
			var result = a[field] > b[field] ? 1 : a[field] < b[field] ? -1 : 0; 
			return sortAsc ? result : -result;
		});
		if(updateGrid==null) {
			prlCfg[curCfgSection].grid.gridObj.setData(prlCfg[curCfgSection].grid.dataView);
			prlCfg[curCfgSection].grid.gridObj.updateRowCount();
			prlCfg[curCfgSection].grid.gridObj.render();
		}
	}

	function prlCfgInit() {
		$('#prlCfgTabs').tabs().bind('tabsactivate', function(event, ui) {
			var curCfgSection = $('#prlCfgTabs > ul > li').eq($('#prlCfgTabs').tabs('option', 'active')).attr('sct');
			if(curCfgSection=='CfgDiv') return;
			if(prlCfg[curCfgSection].grid.gridObj != null) prlCfg[curCfgSection].grid.gridObj.resizeCanvas();
			cb('payroll.ConfigUpdateTable',{'section':curCfgSection});
		});

		$('#prlCfgOvMenu').menu().hide().css('width','100').css('position','fixed').bind('mouseleave', function(e) { $(this).hide(); });
		$('#prlCfgOvMenu li a').bind('click', function(e) {
			var curCfgSection = $('#prlCfgTabs > ul > li').eq($('#prlCfgTabs').tabs('option', 'active')).attr('sct');

			var rid = $(this).parent().parent().attr('rid');
			switch($(this).attr('fnc')) {
			case '1': //edit
				prlCfg[curCfgSection].MnuEdit.function(rid);
				break;
			case '2': //delete
				prlCfg[curCfgSection].MnuDelete.function(rid);
				break;
			}
			$(this).parent().parent().hide();
			return false;
		});

		$('#prlCfgInscTab select').bind('change', function(e) {
			cb('payroll.ConfigUpdateTable',{'section':'CfgInsc','InsuranceType':$(this).val()});
		});

		var tabSections = ['CfgCmpc', 'CfgLoac', 'CfgInsc', 'CfgSyac', 'CfgDasc'];

		$.each(tabSections, function() {
			prlCfg[this].grid.dataView = new Slick.Data.DataView();
			prlCfg[this].grid.gridObj = new Slick.Grid("#grid"+this, prlCfg[this].grid.dataView, prlCfg[this].grid.columns, prlCfgOptions);
			prlCfg[this].grid.gridObj.onSort.subscribe(function (e, args) {
				prlCfgSortSingleColumn(args.sortCol.field, args.sortAsc);
			});

			prlCfg[this].grid.dataView.onRowCountChanged.subscribe(function (e, args) {
				var curCfgSection = $('#prlCfgTabs > ul > li').eq($('#prlCfgTabs').tabs('option', 'active')).attr('sct');
				prlCfg[curCfgSection].grid.gridObj.updateRowCount();
				prlCfg[curCfgSection].grid.gridObj.render();
			});

			prlCfg[this].grid.dataView.onRowsChanged.subscribe(function (e, args) {
				var curCfgSection = $('#prlCfgTabs > ul > li').eq($('#prlCfgTabs').tabs('option', 'active')).attr('sct');
				prlCfg[curCfgSection].grid.gridObj.invalidateRows(args.rows);
				prlCfg[curCfgSection].grid.gridObj.render();
			});


			$(prlCfg[this].grid.gridObj.getHeaderRow()).delegate(":input", "change keyup", function (e) {
				var curCfgSection = $('#prlCfgTabs > ul > li').eq($('#prlCfgTabs').tabs('option', 'active')).attr('sct');
				var columnId = $(this).data("columnId");
				if (columnId != null) {
					prlCfg[curCfgSection].grid.columnFilters[columnId] = $.trim($(this).val());
					prlCfg[curCfgSection].grid.dataView.refresh();
				}
			});

			prlCfg[this].grid.gridObj.onHeaderRowCellRendered.subscribe(function(e, args) {
				var curCfgSection = $('#prlCfgTabs > ul > li').eq($('#prlCfgTabs').tabs('option', 'active')).attr('sct');
				$(args.node).empty();
				$("<input type='text'>")
				   .data("columnId", args.column.id)
				   .val(prlCfg[curCfgSection].grid.columnFilters[args.column.id])
				   .appendTo(args.node);
			});

			prlCfg[this].grid.gridObj.onClick.subscribe(function(e, args) {
				var curCfgSection = $('#prlCfgTabs > ul > li').eq($('#prlCfgTabs').tabs('option', 'active')).attr('sct');
				var cell = prlCfg[curCfgSection].grid.gridObj.getCellFromEvent(e), row = cell.row;
				var item = prlCfg[curCfgSection].grid.dataView.getItem(row);

				if(prlCfg[curCfgSection].MnuEdit.available && prlCfg[curCfgSection].MnuDelete.available) $('#prlCfgOvMenu').css('top',e.pageY-15).css('left',e.pageX-70).css('zIndex', 9999).attr('rid', item.id).show();
				else prlCfg[curCfgSection].MnuEdit.function(item.id);
			});

			prlCfg[this].grid.gridObj.init();

			prlCfg[this].grid.dataView.beginUpdate();
			prlCfg[this].grid.dataView.setItems(prlCfg[this].grid.data);
			prlCfg[this].grid.dataView.setFilter(prlCfgFilter);
			prlCfg[this].grid.dataView.endUpdate();

			if(prlCfg[this].BtnNew.available) {
				//Button: neu/hinzufügen
				$( "#prl"+this+"BtnNew" ).button({
					text: false,
					icons: { primary: "p-icon-add" }
				})
				.click(function() {
					var curCfgSection = $('#prlCfgTabs > ul > li').eq($('#prlCfgTabs').tabs('option', 'active')).attr('sct');
					prlCfg[curCfgSection].BtnNew.function();
				});
			}
	// ----------------------------------------
			if(prlCfg[this].BtnQFilter.available) {
				//Button: Tabellenfilter
				$( "#prl"+this+"BtnQFilter" ).button({
					text: false,
					icons: { primary: "p-icon-tblfilter" }
				})
				.click(function() {
					var curCfgSection = $('#prlCfgTabs > ul > li').eq($('#prlCfgTabs').tabs('option', 'active')).attr('sct');
					prlCfg[curCfgSection].BtnQFilter.function();
				}).addClass('toolbar-space-left');
			}

			if(prlCfg[this].BtnSaveSettings.available) {
				//Button: Einstellungen speichern
				$( "#prl"+this+"BtnSaveSettings" ).button({
					text: false,
					icons: { primary: "p-icon-savesettings" }
				})
				.click(function() {
					var curCfgSection = $('#prlCfgTabs > ul > li').eq($('#prlCfgTabs').tabs('option', 'active')).attr('sct');
					prlCfg[curCfgSection].BtnSaveSettings.function();
				});
			}
	// ----------------------------------------
			if(prlCfg[this].BtnFinMgmtAcc.available) {
				//Button: FIBU-BEBU-Config
				$( "#prl"+this+"BtnFinMgmtAcc" ).button({
					text: false,
					icons: { primary: "p-icon-distribute" }
				})
				.click(function() {
					var curCfgSection = $('#prlCfgTabs > ul > li').eq($('#prlCfgTabs').tabs('option', 'active')).attr('sct');
					prlCfg[curCfgSection].BtnFinMgmtAcc.function();
				}).addClass('toolbar-space-left');
			}
	// ----------------------------------------
			if(prlCfg[this].BtnFldMod.available) {
				//Button: field modifier
				$( "#prl"+this+"BtnFldMod" ).button({
					text: false,
					icons: { primary: "p-icon-process" }
				})
				.click(function() {
					var curCfgSection = $('#prlCfgTabs > ul > li').eq($('#prlCfgTabs').tabs('option', 'active')).attr('sct');
					prlCfg[curCfgSection].BtnFldMod.function();
				}).addClass('toolbar-space-left');
			}
	// ----------------------------------------
			if(prlCfg[this].BtnInsr.available) {
				//Button: Versicherer verwalten
				$( "#prl"+this+"BtnInsr" ).button({
					text: false,
					icons: { primary: "p-icon-building" }
				})
				.click(function() {
					var curCfgSection = $('#prlCfgTabs > ul > li').eq($('#prlCfgTabs').tabs('option', 'active')).attr('sct');
					prlCfg[curCfgSection].BtnInsr.function();
				}).addClass('toolbar-space-left');
			}
			if(prlCfg[this].BtnCode.available) {
				//Button: Codes verwalten
				$( "#prl"+this+"BtnCode" ).button({
					text: false,
					icons: { primary: "p-icon-codes" }
				})
				.click(function() {
					var curCfgSection = $('#prlCfgTabs > ul > li').eq($('#prlCfgTabs').tabs('option', 'active')).attr('sct');
					prlCfg[curCfgSection].BtnCode.function();
				});
			}
	// ----------------------------------------
			if(prlCfg[this].BtnSettings.available) {
				//Button: field modifier
				$( "#prl"+this+"BtnSettings" ).button({
					text: false,
					icons: { primary: "p-icon-orgchart" }
				})
				.click(function() {
					var curCfgSection = $('#prlCfgTabs > ul > li').eq($('#prlCfgTabs').tabs('option', 'active')).attr('sct');
					prlCfg[curCfgSection].BtnSettings.function();
				}).addClass('toolbar-space-left');
			}
		});
	}

/*
*************************************
** Configuration: Payroll Accounts
*************************************
*/
var prlLoacWnd = "#payrollAccountForm";
var prlLoacDirty = false;
var prlLoacRID = "0";
var prlLoacLoaAll = {};
var prlLoacLabels = {};
var prlLoacLoaInExcl = []; //array mit IDs, deren LOA nicht zur Auswahl stehen
var prlLoacLoaOutExcl = []; //array mit IDs, deren LOA nicht zur Auswahl stehen
var prlLoacFieldDef = {};

function prlLoacFillLoaSelection(isInputSection) {
	var elSelect = isInputSection ? $('#loacInpSel') : $('#loacOutSel');
	elSelect.find('option').remove();
	$.each(prlLoacLoaAll, function(k, v) {
		elSelect.append(
			$('<option></option>').val(k).html(v.number+' - '+v.label)
		);
	});
	$("#loacInpLOA > option").each(function() {
		elSelect.find('option[value='+$(this).val()+']').remove();
	});
	$("#loacOutLOA > option").each(function() {
		elSelect.find('option[value='+$(this).val()+']').remove();
	});
	$.each(isInputSection?prlLoacLoaInExcl:prlLoacLoaOutExcl, function() {
		elSelect.find('option[value='+this+']').remove();
	});
}

function prlLoacFillAuxLoaSelection() {
	var elSelect = $('#loac_limits_aux_account_ID');
	elSelect.find('option').remove();
	elSelect.append($('<option></option>').val(0).html(''));
	$.each(prlLoacLoaAll, function(k, v) {
		elSelect.append(
			$('<option></option>').val(k).html(v.number+' - '+v.label)
		);
	});
}

function prlLoacOneItemLoaSelection(itemID,isInputSection) {
	var elSelect = isInputSection ? $('#loacInpSel') : $('#loacOutSel');
	elSelect.find('option').remove();
	var v = prlLoacLoaAll[itemID];
	elSelect.append(
		$('<option></option>').val(itemID).html(v.number+' - '+v.label)
	);
}

function prlLoacLoadData(data) {
	$('#loacInpLOA').find('option').remove();
	$('#loacOutLOA').find('option').remove();
	$.each(data, function(k, v) {
		switch(k) {
		case 'accIn':
			var elList = $('#loacInpLOA');
			$.each(v, function() {
				elList.append(
//					$('<option></option>').val(this.id).html(prlLoacLoaAll[this.id].number+' - '+prlLoacLoaAll[this.id].label+' ['+prlLoacLabels['fwdField'+this.fwdField]+(this.fwdNegVal?'':' | '+prlLoacLabels['fwdNegVal'])+(!this.fwdInvVal?'':' | '+prlLoacLabels['invertVal'])+(this.fwdTarget==0?'':' | '+prlLoacLabels['targetField'])+']').attr('fwdField',this.fwdField).attr('fwdNegVal',this.fwdNegVal?1:0).attr('targetfield',this.fwdTarget).attr('invertval',this.fwdInvVal?1:0)
					$('<option></option>').val(this.id).html(prlLoacLoaAll[this.id].number+' - '+prlLoacLoaAll[this.id].label+' ['+prlLoacLabels['fwdField'+this.fwdField]+' ⇨ '+prlLoacLabels['fwdField'+this.fwdTarget]+(this.fwdNegVal?'':' | '+prlLoacLabels['fwdNegVal'])+(!this.fwdInvVal?'':' | '+prlLoacLabels['invertVal'])+']').attr('fwdField',this.fwdField).attr('fwdNegVal',this.fwdNegVal?1:0).attr('targetfield',this.fwdTarget).attr('invertval',this.fwdInvVal?1:0)
				);
			});
			break;
		case 'accOut':
			var elList = $('#loacOutLOA');
			$.each(v, function() {
				elList.append(
//					$('<option></option>').val(this.id).html(prlLoacLoaAll[this.id].number+' - '+prlLoacLoaAll[this.id].label+' ['+prlLoacLabels['fwdField'+this.fwdField]+(this.fwdNegVal?'':' | '+prlLoacLabels['fwdNegVal'])+(!this.fwdInvVal?'':' | '+prlLoacLabels['invertVal'])+(this.fwdTarget==0?'':' | '+prlLoacLabels['targetField'])+']').attr('fwdField',this.fwdField).attr('fwdNegVal',this.fwdNegVal?1:0).attr('targetfield',this.fwdTarget).attr('invertval',this.fwdInvVal?1:0)
					$('<option></option>').val(this.id).html(prlLoacLoaAll[this.id].number+' - '+prlLoacLoaAll[this.id].label+' ['+prlLoacLabels['fwdField'+this.fwdField]+' ⇨ '+prlLoacLabels['fwdField'+this.fwdTarget]+(this.fwdNegVal?'':' | '+prlLoacLabels['fwdNegVal'])+(!this.fwdInvVal?'':' | '+prlLoacLabels['invertVal'])+']').attr('fwdField',this.fwdField).attr('fwdNegVal',this.fwdNegVal?1:0).attr('targetfield',this.fwdTarget).attr('invertval',this.fwdInvVal?1:0)
				);
			});
			break;
		case 'having_limits':
		case 'having_calculation':
		case 'having_rounding':
		case 'quantity_print':
		case 'rate_print':
		case 'amount_print':

			$('#loac_'+k).attr("checked",v);
			break;
		default:
			$('#loac_'+k).val(v);
			break;
		}
	});
	$("#loac_input_assignment").change();
	$("#loac_output_assignment").change();
	prlLoacToggleCrtls($("#loac_having_limits"));
	prlLoacToggleCrtls($("#loac_having_calculation"));
	prlLoacToggleCrtls($("#loac_having_rounding"));
}

function prlLoacToggleCrtls(obj) {
	var frmset = obj.parent().parent();
	if(obj.is(":checked")){
		frmset.find("input[type=text],select").removeAttr("disabled");
		frmset.find("label").css("color","");
	}else{
		frmset.find("input[type=text],select").attr("disabled", "disabled");
		frmset.find("label").css("color","#888");
	}

	if($('#loac_having_limits').is(":checked")) $('#loacFsAsgnIn').find("input[type=text],select").removeAttr("disabled").parent().find("label").css("color","");
	else{
		$('#loacFsAsgnIn').find("input[type=text],select").attr("disabled", "disabled").parent().find("label").css("color","#888");
		$('#loac_input_assignment').val('0').change();
	}

	if($('#loac_having_calculation').is(":checked")) $('#loacFsAsgnOut').find("input[type=text],select").removeAttr("disabled").parent().find("label").css("color","");
	else {
		$('#loac_having_rounding').attr('checked',false).parent().parent().find("input[type=text],select").attr("disabled", "disabled").parent().find("label").css("color","#888");
		$('#loacFsAsgnOut').find("input[type=text],select").attr("disabled", "disabled").parent().find("label").css("color","#888");
		$('#loac_output_assignment').val('0').change();
	}
}

function prlLoacInit() {
	$("#loacTabs").tabs();
	$("#loacInpAdd,#loacOutAdd").bind('click', function(e) {
		var section = $(this).parent().parent().attr("id")=="loacTab2" ? true : false;
		$("#loac"+(section?"Inp":"Out")+"Sel").removeAttr("disabled");
		$("#loac"+(section?"Inp":"Out")+"Neg").attr('checked',true); //checked by default
		$("#loac"+(section?"Inp":"Out")+"LgA").show();
		$("#loac"+(section?"Inp":"Out")+"LgE").hide();
		prlLoacFillLoaSelection(section);
		$("#loacTab"+(section?2:4)+" fieldset").eq(1).show();
		$("#loacTab"+(section?2:4)+" fieldset").eq(0).find("input,select,button").attr("disabled", "disabled");
	});
	$("#loacInpEdit,#loacInpDel,#loacInpGoto,#loacOutEdit,#loacOutDel,#loacOutGoto").attr("disabled", "disabled");
	$("#loacInpLOA,#loacOutLOA").bind('click', function(e) {
		var section = $(this).parent().parent().attr("id")=="loacTab2" ? true : false;
		$("#loac"+(section?"Inp":"Out")+"Edit,#loac"+(section?"Inp":"Out")+"Del,#loac"+(section?"Inp":"Out")+"Goto").removeAttr("disabled");
		prlLoacOneItemLoaSelection($(this).val(),section);
		$("#loac"+(section?"Inp":"Out")+"Fld").val($(this).find("option[value="+$(this).val()+"]").attr("fwdField"));
		$("#loac"+(section?"Inp":"Out")+"Neg").attr("checked",$(this).find("option[value="+$(this).val()+"]").attr("fwdNegVal")==1?true:false);
		$("#loac"+(section?"Inp":"Out")+"Target").val($(this).find("option[value="+$(this).val()+"]").attr("targetfield"));
		$("#loac"+(section?"Inp":"Out")+"Inv").attr("checked",$(this).find("option[value="+$(this).val()+"]").attr("invertval")==1?true:false);
	});
	$("#loacInpEdit,#loacOutEdit").bind('click', function(e) {
		var section = $(this).parent().parent().attr("id")=="loacTab2" ? true : false;
		$("#loac"+(section?"Inp":"Out")+"LgA").hide();
		$("#loac"+(section?"Inp":"Out")+"LgE").show();
		$("#loacTab"+(section?2:4)+" fieldset").eq(1).show();
		$("#loac"+(section?"Inp":"Out")+"Sel").attr("disabled", "disabled");
		$("#loacTab"+(section?2:4)+" fieldset").eq(0).find("input,select,button").attr("disabled", "disabled");
	});
	$("#loacInpDel,#loacOutDel").bind('click', function(e) {
		var section = $(this).parent().parent().attr("id")=="loacTab2" ? true : false;
		$("#loac"+(section?"Inp":"Out")+"LOA option[value="+$("#loac"+(section?"Inp":"Out")+"Sel").val()+"]").remove();
		$("#loac"+(section?"Inp":"Out")+"Edit,#loac"+(section?"Inp":"Out")+"Del,#loac"+(section?"Inp":"Out")+"Goto").attr("disabled", "disabled");
	});
	$("#loacInpGoto,#loacOutGoto").bind('click', function(e) {
		var section = $(this).parent().parent().attr("id")=="loacTab2" ? true : false;
		prlLoacOpenForm($("#loac"+(section?"Inp":"Out")+"Sel").val());
	});
	$("#loacInpOK,#loacOutOK").bind('click', function(e) {
		var section = $(this).parent().parent().attr("id")=="loacTab2" ? true : false;
		$("#loacTab"+(section?2:4)+" fieldset").eq(1).hide();
		$("#loacTab"+(section?2:4)+" fieldset").eq(0).find("input,select,button[id=loac"+(section?"Inp":"Out")+"Add]").removeAttr("disabled");
		var selectedItemID = $("#loac"+(section?"Inp":"Out")+"Sel").val();
		var lfwdNegVal = $("#loac"+(section?"Inp":"Out")+"Neg").is(":checked");
		var lfwdField = $("#loac"+(section?"Inp":"Out")+"Fld").val();
		var lfwdInvVal = $("#loac"+(section?"Inp":"Out")+"Inv").is(":checked");
		var lfwdTarget = $("#loac"+(section?"Inp":"Out")+"Target").val();
		if($("#loac"+(section?"Inp":"Out")+"Sel").is(":disabled")) {
			//edit item
			$('#loac'+(section?"Inp":"Out")+'LOA option[value='+selectedItemID+']').val(selectedItemID).html(prlLoacLoaAll[selectedItemID].number+' - '+prlLoacLoaAll[selectedItemID].label+' ['+prlLoacLabels['fwdField'+lfwdField]+' ⇨ '+prlLoacLabels['fwdField'+lfwdTarget]+(lfwdNegVal?'':' | '+prlLoacLabels['fwdNegVal'])+(!lfwdInvVal?'':' | '+prlLoacLabels['invertVal'])+']').attr('fwdField',lfwdField).attr('fwdNegVal',lfwdNegVal?1:0).attr('targetfield',lfwdTarget).attr('invertval',lfwdInvVal?1:0);
		}else{
			//add item
			$('#loac'+(section?"Inp":"Out")+'LOA').append(
				$('<option></option>').val(selectedItemID).html(prlLoacLoaAll[selectedItemID].number+' - '+prlLoacLoaAll[selectedItemID].label+' ['+prlLoacLabels['fwdField'+lfwdField]+' ⇨ '+prlLoacLabels['fwdField'+lfwdTarget]+(lfwdNegVal?'':' | '+prlLoacLabels['fwdNegVal'])+(!lfwdInvVal?'':' | '+prlLoacLabels['invertVal'])+']').attr('fwdField',lfwdField).attr('fwdNegVal',lfwdNegVal?1:0).attr('targetfield',lfwdTarget).attr('invertval',lfwdInvVal?1:0)
			);
		}
	});
	$("#loacInpCancel,#loacOutCancel").bind('click', function(e) {
		var section = $(this).parent().parent().attr("id")=="loacTab2" ? true : false;
		$("#loacTab"+(section?2:4)+" fieldset").eq(1).hide();
		$("#loacTab"+(section?2:4)+" fieldset").eq(0).find("input,select,button[id=loac"+(section?"Inp":"Out")+"Add]").removeAttr("disabled");
	});
	$("#loac_having_limits,#loac_having_calculation,#loac_having_rounding").bind('click', function(e) {
		prlLoacToggleCrtls($(this));
	});
	$("#loac_limits_calc_mode").bind('change keyup', function(e) {
		if($(this).val()=='2') $('#loac_limits_aux_account_ID').val('0');
	});
	$("#loac_input_assignment").bind('change keyup', function(e) {
		$('.loacFieldsRow li').css('background-color','');
		if($(this).val()=='0') return;
		$('.loacFieldsRow').eq(0).find('li').eq($(this).val()-1).css('background-color','#f60');
		$('.loacFieldsRow').eq(1).find('li').eq($(this).val()-1).css('background-color','#f60');
	});
	$("#loac_output_assignment").bind('change keyup', function(e) {
		var fieldN = ['','','','loac_quantity','loac_rate','loac_amount'];
		$.each(fieldN, function() { $('#'+this).css('background-color',''); });
		if($(this).val()=='0') return;
		$('#'+fieldN[$(this).val()]).css('background-color','#fe6');
	});
	$(".loacFormulaEditor").bind('click', function(e) {
		cb('payroll.FormulaEditor');
		return false;
	});

	$.each(prlLoacFieldDef, function(k, v) {
		$("#loac_"+k).bind('change blur', function(e) {
			var fldName = $(this).attr('id');
			fldName = fldName.substring(5);
			var err = false;
			if(prlLoacFieldDef[fldName].mandatory && $(this).val()=="") err = true;
			else if(prlLoacFieldDef[fldName].rgx!='' && !prlLoacFieldDef[fldName].rgx.test($(this).val())) err = true;
			if(err) $(this).css('background-color','#f88');
			else $(this).css('background-color','');
		});
		if(v.maxlength>0) $("#loac_"+k).attr('maxlength',v.maxlength);
	});

	$("#loac_input_assignment").change();
	$("#loac_output_assignment").change();

	$("#loacTab2 fieldset").eq(1).hide();
	$("#loacTab4 fieldset").eq(1).hide();
}

function prlLoacSave() {
	var jsonItem = {};
	var exclude = [];
	var ip = [];
	var op = [];
	$("#loacTab2 input,#loacTab2 select,#loacTab4 input,#loacTab4 select").each(function(index) { exclude.push($(this).attr('id')); });

	$(prlLoacWnd+' input,'+prlLoacWnd+' select').each(function(index) {
		var fldName = $(this).attr('id');
		if($.inArray(fldName, exclude)<0) {
			fldName = fldName.substring(5);
			if($(this).is(":checkbox")) jsonItem[fldName] = $(this).is(':checked') ? 1 : 0;
			else jsonItem[fldName] = $(this).val();
		}
	});

	$("#loacInpLOA option").each(function(index) { ip.push([$(this).attr('value'),$(this).attr('fwdfield'),$(this).attr('fwdnegval'),$(this).attr('invertval'),$(this).attr('targetfield')]); });
	jsonItem["inploa"] = ip;
	$("#loacOutLOA option").each(function(index) { op.push([$(this).attr('value'),$(this).attr('fwdfield'),$(this).attr('fwdnegval'),$(this).attr('invertval'),$(this).attr('targetfield')]); });
	jsonItem["outloa"] = op;

	cb("payroll.prlLoacSave",{"rid":prlLoacRID,"data":jsonItem});
}

function prlLoacOpenForm(accId) {
	var objWnd = $(prlLoacWnd);
	if ( objWnd.length > 0 ) {
		if(objWnd.mb_getState('iconized')) objWnd.mb_iconize();
		else objWnd.mb_bringToFront();
		cb('payroll.OpenPayrollAccountForm',{"id":accId,"dirty":prlLoacDirty?1:0,"wndStatus":1});
	}else{
		cb('payroll.OpenPayrollAccountForm',{"id":accId,"dirty":prlLoacDirty?1:0,"wndStatus":0});
	}
}
/*
*************************************
** Calculation Overview
*************************************
*/
	var prlCalcOvDataView;
	var prlCalcOvColumnFilters = {};
	var prlCalcOvGrid;
	var prlCalcOvColumns = [];
	var prlCalcOvCfg = {};

	var prlCalcOvOptions = {
		showHeaderRow: true,
		enableCellNavigation: false,
		enableColumnReorder: false,
		explicitInitialization: true
	};

	var prlCalcOvData = [];

	function prlCalcOvFilter(item) {
		for(var columnId in prlCalcOvColumnFilters) {
			if(columnId !== undefined && prlCalcOvColumnFilters[columnId] !== "") {
				var c = prlCalcOvGrid.getColumns()[prlCalcOvGrid.getColumnIndex(columnId)];
				if(item[c.field].toString().toLowerCase().indexOf(prlCalcOvColumnFilters[columnId].toString().toLowerCase()) == -1) {
					return false;
				}
			}
		}
		return true;
	}

	function prlCalcOvSaveSettings() {
		var settings = {};
		settings['quickFilterEnabled'] = $("#prlCalcOvBtnQFilter").is(':checked');
		if(settings['quickFilterEnabled']) {
			settings['quickFilterValues'] = [];
			for(var columnId in prlCalcOvColumnFilters) {
				settings['quickFilterValues'].push({'colID': columnId, 'filterValue': prlCalcOvColumnFilters[columnId]});
			}
		}
		settings['columnsWidth'] = [];
		var cols = prlCalcOvGrid.getColumns();
		for(var i=0;i<cols.length;i++) settings['columnsWidth'].push(cols[i].width);
		settings['sort'] = prlCalcOvGrid.getSortColumns();
		cb('payroll.calcOvSaveSettings',settings);
	}

	function prlCalcOvSetSettings(param) {
		$('#prlCalcOvBtnQFilter').attr('checked', param.quickFilterEnabled);
		if(param.quickFilterEnabled) {
			for(var i=0;i<param.quickFilterValues.length;i++) {
				prlCalcOvColumnFilters[param.quickFilterValues[i].colID] = param.quickFilterValues[i].filterValue;
			}
		}
		var cols = prlCalcOvGrid.getColumns();
		for(var i=0;i<cols.length;i++) cols[i].width=param.columnsWidth[i];
		prlCalcOvGrid.setColumns(cols);

		if(param.sort.length>0) {
			prlCalcOvGrid.setSortColumn(param.sort[0].columnId, param.sort[0].sortAsc);
			prlCalcOvSortSingleColumn(param.sort[0].columnId, param.sort[0].sortAsc, false);
		}

		prlCalcOvToggleFilterRow();

		prlCalcOvGrid.setData(prlCalcOvDataView);
		prlCalcOvGrid.updateRowCount();
		prlCalcOvGrid.render();
	}

	function prlCalcOvToggleFilterRow() {
		if($("#prlCalcOvBtnQFilter").is(':checked')) {
			$(prlCalcOvGrid.getHeaderRow()).show();
			prlCalcOvGrid.showHeaderRow(true);
		}else{
			$(prlCalcOvGrid.getHeaderRow()).hide();
			prlCalcOvGrid.showHeaderRow(false);
			$(prlCalcOvGrid.getHeaderRow()).find("input").val('');
			for(var columnId in prlCalcOvColumnFilters) prlCalcOvColumnFilters[columnId] = "";
			prlCalcOvDataView.refresh();
		}
		prlCalcOvGrid.resizeCanvas();
	}

	function prlCalcOvSortSingleColumn(field, sortAsc, updateGrid) {
		prlCalcOvDataView.sort(function(a, b){
			var result = a[field] > b[field] ? 1 : a[field] < b[field] ? -1 : 0; 
			return sortAsc ? result : -result;
		});
		if(updateGrid==null) {
			prlCalcOvGrid.setData(prlCalcOvDataView);
			prlCalcOvGrid.updateRowCount();
			prlCalcOvGrid.render();
		}
	}

	function prlCalcOvSetData(updateTbl) {
		var n = prlCalcOvData.length;
		for(var i=0;i<n;i++) {
			prlCalcOvData[i]['ProcStatus'] = prlCalcOvCfg.statusLabel[prlCalcOvData[i].processing]+(prlCalcOvData[i].fin_acc_status != prlCalcOvData[i].mgmt_acc_status?prlCalcOvData[i].fin_acc_status==1?prlCalcOvCfg.finAccLabel:prlCalcOvCfg.mgmtAccLabel:'');
		}
		if(updateTbl) {
			prlCalcOvDataView.beginUpdate();
			prlCalcOvDataView.setItems(prlCalcOvData);
			prlCalcOvDataView.endUpdate();
			prlCalcOvDataView.reSort();
			prlCalcOvGrid.invalidate();
		}
	}

	function prlCalcOvInit() {//Lohn bearbeiten
		prlCalcOvDataView = new Slick.Data.DataView();
		prlCalcOvGrid = new Slick.Grid("#gridCalcOv", prlCalcOvDataView, prlCalcOvColumns, prlCalcOvOptions);
		prlCalcOvGrid.onSort.subscribe(function (e, args) {
			prlCalcOvSortSingleColumn(args.sortCol.field, args.sortAsc);
		});

		prlCalcOvDataView.onRowCountChanged.subscribe(function (e, args) {
			prlCalcOvGrid.updateRowCount();
			prlCalcOvGrid.render();
		});

		prlCalcOvDataView.onRowsChanged.subscribe(function (e, args) {
			prlCalcOvGrid.invalidateRows(args.rows);
			prlCalcOvGrid.render();
		});


		$(prlCalcOvGrid.getHeaderRow()).delegate(":input", "change keyup", function (e) {
			var columnId = $(this).data("columnId");
			if (columnId != null) {
				prlCalcOvColumnFilters[columnId] = $.trim($(this).val());
				prlCalcOvDataView.refresh();
			}
		});

		prlCalcOvGrid.onHeaderRowCellRendered.subscribe(function(e, args) {
			$(args.node).empty();
			$("<input type='text'>")
			   .data("columnId", args.column.id)
			   .val(prlCalcOvColumnFilters[args.column.id])
			   .appendTo(args.node);
		});

		prlCalcOvGrid.onClick.subscribe(function(e, args) {
			var cell = prlCalcOvGrid.getCellFromEvent(e), row = cell.row;
			var item = prlCalcOvDataView.getItem(row);
//			alert(item.id);
			cb('payroll.paymentSplit', {'empId':item.id});
		});


		prlCalcOvGrid.init();

		prlCalcOvDataView.beginUpdate();
		prlCalcOvDataView.setItems(prlCalcOvData);
		prlCalcOvDataView.setFilter(prlCalcOvFilter);
		prlCalcOvDataView.endUpdate();

		//Button: MA hinzufügen
		$( "#prlCalcOvBtnNew" ).button({
			text: false,
			icons: {
				primary: "p-icon-add"
			}
		})
		.click(function() {
			alert("Zwischenzahltag hinzufuegen");
		});
// ----------------------------------------
		//Button: Perioden-Einstellungen bearbeiten
		$( "#prlCalcOvBtnSettings" ).button({
			text: false,
			icons: {
				primary: "p-icon-info"
			}
		})
		.click(function() {
			var pYear = $('#prlCalcOvYear').val();
			var pMajorPeriod = $('#prlCalcOvMajorPeriod').val();
			var pMinorPeriod = 0;
			var param = {'year': pYear, 'majorPeriod': pMajorPeriod, 'minorPeriod': pMinorPeriod};
			cb('payroll.prlCalcOvSettings', param);
		}).addClass('toolbar-space-left');
// ----------------------------------------
// ----------------------------------------
		$( "#prlCalcOvBtnEdit" ).button({
			text: false,
			icons: {
				primary: "p-icon-edit"
			}
		})
		.click(function() {
			prlCalcDataOpen();
		});
// ----------------------------------------
		//Button: Add employee
		$( "#prlCalcOvBtnAddEmpl" ).button({
			text: false,
			icons: {
				primary: "p-icon-adduser"
			}
		})
		.click(function() {
			cb('payroll.EmployeeSelectorOpen');
		}).addClass('toolbar-space-left');
// ----------------------------------------
		//Button: Tabellenfilter
		$( "#prlCalcOvBtnQFilter" ).button({
			text: false,
			icons: {
				primary: "p-icon-tblfilter"
			}
		})
		.click(function() {
			prlCalcOvToggleFilterRow();
		});
		$('label[for=prlCalcOvBtnQFilter]').addClass('toolbar-space-left');

		//Button: Edit Table Columns
		$( "#prlCalcOvBtnEditCols" ).button({
			text: false,
			icons: {
				primary: "p-icon-editcols"
			}
		})
		.click(function() {
			alert('Tabellenspalten bearbeiten');
		});

		//Button: Einstellungen speichern
		$( "#prlCalcOvBtnSaveSettings" ).button({
			text: false,
			icons: {
				primary: "p-icon-savesettings"
			}
		})
		.click(function() {
			prlCalcOvSaveSettings();
//			alert('Coming soon...');
		});
// ----------------------------------------
		//Button: Daten verarbeiten
		$( "#prlCalcOvBtnProcess" ).button({
			text: false,
			icons: {
				primary: "p-icon-process",
				secondary: "ui-icon-triangle-1-s"
			}
		})
		.click(function() {
			var prlCalcOvProcessMenu = $("#prlCalcOvProcessMenu");
			if(prlCalcOvProcessMenu.is(":visible")) {
				prlCalcOvProcessMenu.hide();
			}else{
				prlCalcOvProcessMenu.show().position({
					my: "left top",
					at: "left bottom",
					of: this
				});
				$(document).one("click", function() {
					prlCalcOvProcessMenu.hide();
				});
				$("#prlCalcOvOutputMenu").hide();
			}
			return false;
		}).addClass('toolbar-space-left');

// ----------------------------------------
		//Button: Output (PDF)
		$( "#prlCalcOvBtnOutput" ).button({
			text: false,
			icons: {
				primary: "p-icon-pdf",
				secondary: "ui-icon-triangle-1-s"
			}
		})
		.click(function() {
			var prlCalcOvOutputMenu = $("#prlCalcOvOutputMenu");
			if(prlCalcOvOutputMenu.is(":visible")) {
				prlCalcOvOutputMenu.hide();
			}else{
				prlCalcOvOutputMenu.show().position({
					my: "left top",
					at: "left bottom",
					of: this
				});
				$(document).one("click", function() {
					prlCalcOvOutputMenu.hide();
				});
				$("#prlCalcOvProcessMenu").hide();
			}
			return false;
		}).addClass('toolbar-space-left');
// ----------------------------------------
		$("#prlCalcOvProcessMenu").hide().menu().css('width','150').css('position','fixed').css('zIndex','9999');
		$("#prlCalcOvOutputMenu").hide().menu().css('width','150').css('position','fixed').css('zIndex','9999');

		$('#prlCalcOvProcessMenu a').bind('click', function(e) {
			var pFunctionNumber = $(this).attr('fnc');
			var pYear = $('#prlCalcOvYear').val();
			var pMajorPeriod = $('#prlCalcOvMajorPeriod').val();
			var pMinorPeriod = 0;
			var param = {'functionNumber': pFunctionNumber, 'year': pYear, 'majorPeriod': pMajorPeriod, 'minorPeriod': pMinorPeriod};
			cb('payroll.prlCalcOvProcess',param);
			$("#prlCalcOvProcessMenu").hide();
			return false;
		});

		$('#prlCalcOvOutputMenu a').bind('click', function(e) {
			var pFunctionNumber = $(this).attr('fnc');
			var pYear = $('#prlCalcOvYear').val();
			var pMajorPeriod = $('#prlCalcOvMajorPeriod').val();
			var pMinorPeriod = 0;
			var param = {'functionNumber': pFunctionNumber, 'year': pYear, 'majorPeriod': pMajorPeriod, 'minorPeriod': pMinorPeriod};
			cb('payroll.prlCalcOvOutput',param);
			$("#prlCalcOvOutputMenu").hide();
			return false;
		});

		$('#prlCalcOvYear').bind('change', function(e) {
			var param = {'functionNumber': 1, 'year': $(this).val() };
			cb('payroll.prlCalcOvFnc',param);
			return false;
		});

		$('#prlCalcOvMajorPeriod').bind('change', function(e) {
			var pYear = $('#prlCalcOvYear').val();
			var pMajorPeriod = $(this).val();
			var pMinorPeriod = 0;
			var param = {'functionNumber': 2, 'year': pYear, 'majorPeriod': pMajorPeriod, 'minorPeriod': pMinorPeriod};
			cb('payroll.prlCalcOvFnc',param);
			return false;
		});
	}

	function prlCalcOvOpen() {
		var objWnd = $('#prlCalcOverview');
		if ( objWnd.length > 0 ) {
			if(objWnd.mb_getState('iconized')) objWnd.mb_iconize();
			else objWnd.mb_bringToFront();
		}else{
			cb('payroll.prlCalcOverview');
		}
	}
/*
*************************************
** Calculation Data Editor
*************************************
*/
	var prlCalcDataFldDef = {};
	var prlCalcDataLOA = [];
	var prlCalcDataLoaAC = [];
	var prlCalcDataEmpl = [];
	var prlCalcDataStorage = {};
	var prlCalcDataCurEmpl = [];
	var prlCalcDataRidOffset = 2000000000;
	var prlCalcDataView;
	var prlCalcDataGrid;
	var prlCalcDataCols = [
		{id: "RecStatus", name: "", field: "RecStatus", sortable: false, resizable: false, width: 20, cssClass: "txtCenter", formatter: function (row, cell, value, columnDef, dataContext) { var ic,tt; switch(value){case 1: ic="db"; tt=prlCalcDataLbl.db; break; case 2: ic="star"; tt=prlCalcDataLbl.new; break; case 3: ic="redx"; tt=prlCalcDataLbl.del; break; case 4: ic="pencil"; tt=prlCalcDataLbl.edit; break;} return '<div title="'+tt+'" class="prlCalcDataIcon prlCalcData-'+ic+'"></div>'; }},
		{id: "PayrollDataType", name: "Gültigk.", field: "PayrollDataType", sortable: false, resizable: false, width: 60, cssClass: "txtCenter", formatter: function (row, cell, value, columnDef, dataContext) { var ic,tt; switch(value){case '1': ic="1x"; tt=prlCalcDataLbl.once; break; case '3': ic="eternal"; tt=prlCalcDataLbl.perm; break; case '4': ic="calendar"; tt=prlCalcDataLbl.limt; break;} return '<div title="'+tt+'" class="prlCalcDataIcon prlCalcData-'+ic+'"></div>'; } },
		{id: "payroll_account_ID", name: "LOA Nr.", field: "payroll_account_ID", sortable: true, resizable: false, width: 65},
		{id: "account_text", name: "LOA Text", field: "account_text", sortable: true, resizable: false, width: 275},
		{id: "quantity", name: "Menge", field: "quantity", sortable: true, resizable: false, width: 100, cssClass: "txtRight"},
		{id: "rate", name: "Ansatz", field: "rate", sortable: true, resizable: false, width: 100, cssClass: "txtRight"},
		{id: "amount", name: "Betrag", field: "amount", sortable: true, resizable: false, width: 100, cssClass: "txtRight"},
		{id: "CostCenter", name: "Kostenstelle", field: "CostCenter", sortable: true, resizable: false, width: 100}
	];
	var prlCalcDataTblData = [];
	var prlCalcDataLbl = {};

	function prlCalOvSortSingleColumn(field, sortAsc, updateGrid) {
		prlCalcDataView.sort(function(a, b){
			var result = a[field] > b[field] ? 1 : a[field] < b[field] ? -1 : 0; 
			return sortAsc ? result : -result;
		});
		if(updateGrid==null) {
			prlCalcDataGrid.setData(prlCalcDataView);
			prlCalcDataGrid.updateRowCount();
			prlCalcDataGrid.render();
		}
	}

	function prlCalcDataFldState(state) {
		var fldDateFrom,fldDateTo;
		switch(state) {
		case '1': //einmalig
			fldDateFrom = false;
			fldDateTo = false;
			break;
		case '3': //permanent
			fldDateFrom = true;
			fldDateTo = false;
			break;
		case '4': //befristet
			fldDateFrom = true;
			fldDateTo = true;
			break;
		}
		$('#prlCalcDataFrom').prop('disabled', !fldDateFrom);
		$('label[for=prlCalcDataFrom]').css('color', fldDateFrom?'':'#888');
		$('#prlCalcDataTo').prop('disabled', !fldDateTo);
		$('label[for=prlCalcDataTo]').css('color', fldDateTo?'':'#888');
	}

	function prlCalcDataLoadTbl() {
		prlCalcDataTblData = [];
		var srec,accountText,i,eid=0,storageItemsDone = [];
		$.each(prlCalcDataCurEmpl, function() {
			eid = this.emplId;
			if(this.accTxt=='') {
				for(i=0;i<prlCalcDataLOA.length;i++) if(prlCalcDataLOA[i][0]==this.accNo) {accountText = prlCalcDataLOA[i][1]; break;}
			}else{
				accountText = this.accTxt;
			}

			if((eid in prlCalcDataStorage) && (this.rid in prlCalcDataStorage[eid])) {
				switch(prlCalcDataStorage[eid][this.rid].action) {
				case 'delete':
					rStatus = 3;
					storageItemsDone.push(parseInt(this.rid));
					break;
				case 'add':
					rStatus = 2;
					break;
				case 'edit':
					rStatus = 4;
					storageItemsDone.push(parseInt(this.rid));
					break;
				default:
					rStatus = 1;
					break;
				}
			}else rStatus = 1;

			if(rStatus==4) {
				srec = prlCalcDataStorage[eid.toString()][this.rid.toString()];
				srec.RecStatus = 4;
			}else{
				srec = {
					id: this.rid,
					emplId: eid,
					RecStatus: rStatus,
					PayrollDataType: this.PayrollDataType,
					payroll_account_ID: this.accNo,
					account_text: accountText,
					quantity: this.quantity,
					rate: this.rate,
					amount: this.amount,
					dateFrom: this.dateFrom,
					dateTo: this.dateTo,
					CostCenter: this.cc
				};
			}
			prlCalcDataTblData.push(srec);
		});

		if(eid==0) eid = $('#prlCalcDataCmbEmpl').val();
		if((eid in prlCalcDataStorage)) {
			$.each(prlCalcDataStorage[eid], function(k,v) {
				if($.inArray(parseInt(k), storageItemsDone) == -1) prlCalcDataTblData.push(v);
			});
		}

		prlCalcDataView.beginUpdate();
		prlCalcDataView.setItems(prlCalcDataTblData);
		prlCalcDataView.endUpdate();

		prlCalcDataGrid.setSortColumn("payroll_account_ID",true);
		prlCalOvSortSingleColumn("payroll_account_ID",true);
	}

	function prlCalcDataAdd() {
		//Mandatory + validity checks

		// --> Gültige LOA-Nr?
		// --> weicht LOA-Text vom Standard ab?
		var validLoaNo = false;
		var defaultLoaTxt = false;
		var loaNo = $('#prlCalcDataLoaNo').val();
		var loaTxt = $('#prlCalcDataLoaTxt').val();
		for(var i=0;i<prlCalcDataLOA.length;i++) if(prlCalcDataLOA[i][0]==loaNo) { validLoaNo = true; if(prlCalcDataLOA[i][1]==loaTxt) defaultLoaTxt = true; break;}

		var testPassed = true;
		if(!validLoaNo) {
			testPassed = validLoaNo;
			$('#prlCalcDataLoaNo').css('background-color','#f88');
		}else $('#prlCalcDataLoaNo').css('background-color','');
		$.each(prlCalcDataFldDef, function(k,v) {
			if($('#'+k).is(':disabled') || (!v.mandatory && $('#'+k).val()=='') || v.rgx.test($('#'+k).val()) ) {
				$('#'+k).css('background-color','');
			}else{
				$('#'+k).css('background-color','#f88');
				testPassed = false;
			}
		});
		if(!testPassed) return;

		//Check if Redcord with corresponding ID already exists -> if YES, then update existing record, otherwise append a new record
		var editMode = $('#prlCalcDataBtnAdd[rid]').length>0 ? true : false;

		var rid = prlCalcDataRidOffset;
		if(editMode) rid = $('#prlCalcDataBtnAdd').attr('rid');
		else prlCalcDataRidOffset++;

		var employeeID = $('#prlCalcDataCmbEmpl').val();
		if(!(employeeID in prlCalcDataStorage)) prlCalcDataStorage[employeeID] = {};

		var isDefaultLoaTxt = true;
		for(i=0;i<prlCalcDataLOA.length;i++) if(prlCalcDataLOA[i][0]==$('#prlCalcDataLoaNo').val()) {accountText = prlCalcDataLOA[i][1]; break;}
		if($('#prlCalcDataLoaTxt').val()!=accountText) isDefaultLoaTxt = false;

		var actn = 'edit';
		if(!editMode || parseInt(rid)>=2000000000) actn = 'add';

		var srec = {
			id: rid,
			action: actn,
			emplId: $('#prlCalcDataCmbEmpl').val(),
			RecStatus: (actn=='edit'?4:2),
			PayrollDataType: $('#prlCalcDataCmbType').val(),
			payroll_account_ID: $('#prlCalcDataLoaNo').val(),
			account_text: $('#prlCalcDataLoaTxt').val(),
			defaultTxt: isDefaultLoaTxt,
			quantity: $("#prlCalcDataQuantity").is(':disabled')?'':$("#prlCalcDataQuantity").val(),
			rate: $("#prlCalcDataRate").is(':disabled')?'':$("#prlCalcDataRate").val(),
			amount: $("#prlCalcDataAmount").is(':disabled')?'':$("#prlCalcDataAmount").val(),
			dateFrom: $("#prlCalcDataFrom").is(':disabled')?'':$("#prlCalcDataFrom").val(),
			dateTo: $("#prlCalcDataTo").is(':disabled')?'':$("#prlCalcDataTo").val(),
			CostCenter: $('#prlCalcDataCC').val()
		};
		prlCalcDataStorage[employeeID.toString()][rid.toString()] = srec;
		if(editMode) {
			for(i=0;i<prlCalcDataTblData.length;i++) {
				if(prlCalcDataTblData[i].id==rid) prlCalcDataTblData[i] = srec;
			}
		}else prlCalcDataTblData.push(srec);

		prlCalcDataView.beginUpdate();
		prlCalcDataView.setItems(prlCalcDataTblData);
		prlCalcDataView.endUpdate();

		prlCalcDataGrid.setSortColumn("payroll_account_ID",true);
		prlCalOvSortSingleColumn("payroll_account_ID",true); //,false

		if(editMode) prlCalcDataEditMode(false);
		prlCalcDataClr();
	}

	function prlCalcDataClr(startFld) {
		if(startFld==null) startFld = $('input[name=prlCalcDataRad]:checked').val();
		else $('input[name=prlCalcDataRad][value='+startFld+']').attr('checked', true);
		var selVal = 0;
		var clrFlds = ['prlCalcDataTxtEmpl', 'prlCalcDataCmbEmpl', 'prlCalcDataCmbType', 'prlCalcDataLoaNo', 'prlCalcDataLoaTxt', 'prlCalcDataQuantity', 'prlCalcDataRate', 'prlCalcDataAmount', 'prlCalcDataFrom', 'prlCalcDataTo', 'prlCalcDataCC'];
		$('#'+clrFlds[0]).data("suspendEvents",true);
		$('#'+clrFlds[1]).data("suspendEvents",true);

		$.each(prlCalcDataFldDef, function(k,v) { $('#'+k).css('background-color',''); });
		$('#prlCalcDataLoaNo').css('background-color','');

		if(startFld==clrFlds[0]) selVal = $('#'+clrFlds[1]).val();
		for(var i=0;i<clrFlds.length;i++) if(clrFlds[i]==startFld) break;
		for(i--;i<clrFlds.length;i++) {
			if($('#'+clrFlds[i]).is('input')) $('#'+clrFlds[i]).val('');
		}

		prlCalcDataEditMode(false);
		if(selVal != 0) {
			$('#'+clrFlds[1]+' option[value='+selVal+']').attr("selected", true);
		}
		$('#'+clrFlds[0]).data("suspendEvents",false);
		$('#'+clrFlds[1]).data("suspendEvents",false);
		$('#'+startFld).focus();
	}

	function prlCalcDataEditMode(editMode,rid) {
		var labelAttr = editMode ? 'lblEdit' : 'lblAdd';
		$('#prlCalcDataTxtEmpl').prop('disabled', editMode);
		$('#prlCalcDataCmbEmpl').prop('disabled', editMode);
		if(editMode) {
			$('#prlCalcDataBtnAdd').text($('#prlCalcDataBtnAdd').attr(labelAttr)).attr('rid',rid);
			$('#prlCalcDataBtnClr').text($('#prlCalcDataBtnClr').attr(labelAttr)).attr('rid',rid);
		}else{
			$('#prlCalcDataBtnAdd').removeAttr('rid');
			$('#prlCalcDataBtnClr').removeAttr('rid');
		}
	}

	function prlCalcDataInit() {
		var options = {
			showHeaderRow: false,
			enableCellNavigation: false,
			enableColumnReorder: false,
			explicitInitialization: true
		};

		prlCalcDataView = new Slick.Data.DataView();
		prlCalcDataGrid = new Slick.Grid("#prlCalcDataGrid", prlCalcDataView, prlCalcDataCols, options);
		prlCalcDataGrid.onSort.subscribe(function (e, args) {
			prlCalOvSortSingleColumn(args.sortCol.field, args.sortAsc);
		});

		prlCalcDataView.onRowCountChanged.subscribe(function (e, args) {
			prlCalcDataGrid.updateRowCount();
			prlCalcDataGrid.render();
		});

		prlCalcDataView.onRowsChanged.subscribe(function (e, args) {
			prlCalcDataGrid.invalidateRows(args.rows);
			prlCalcDataGrid.render();
		});

		prlCalcDataGrid.onClick.subscribe(function(e, args) {
			var cell = prlCalcDataGrid.getCellFromEvent(e), row = cell.row;
			var item = prlCalcDataView.getItem(row); //args.item;
			if($('#prlCalcDataBtnAdd[rid]').length>0) return; //do not show menu in edit mode!

			var mnu = $('#prlCalcDataTblMenu');
			mnu.css('top',e.pageY-15);
			mnu.css('left',e.pageX-70);
			mnu.css('zIndex', 9999);
			mnu.attr('rid', item.id);
			mnu.show();
		});


		prlCalcDataGrid.init();

		prlCalcDataView.beginUpdate();
		prlCalcDataView.setItems(prlCalcDataTblData);
		prlCalcDataView.endUpdate();

		prlCalcDataLoaAC = [];
		$.each( prlCalcDataLOA, function() {
			prlCalcDataLoaAC.push({ 
				"label" : this[0] + ' - ' + this[1],
				"value" : this[0]
			});
		});

		var empOpt = "";
		$.each( prlCalcDataEmpl, function() {
			empOpt += "<option value=\"";
			empOpt += this[0] + "\">";
			empOpt += this[1] + " - " + this[2] + ", " + this[3] + "</option>";
		});
		$('#prlCalcDataCmbEmpl').empty().append(empOpt);

		$('#prlCalcDataCmbEmpl').filterByText($('#prlCalcDataTxtEmpl'), true);
		$('input[type=text]').keypress(function (e) {
			if (e.which == 13) {
				prlCalcDataAdd();
			}
		});

		$('#prlCalcDataCmbEmpl').bind('change', function() {
			if($(this).data("suspendEvents")) return;
			cb('payroll.prlCalcDataGetEmplRecs',$(this).val());
		});
		$('#prlCalcDataCmbType').bind('change', function() {
			prlCalcDataFldState($(this).val());
		});
		$('#prlCalcDataLoaNo').bind('change', function() {
			var vf = ['prlCalcDataQuantity', 'prlCalcDataRate', 'prlCalcDataAmount'];
			var loaTxt = "";
			var varFields = 0;
			for(var i = 0; i < prlCalcDataLOA.length; i++) {
				if(prlCalcDataLOA[i][0]==$(this).val()) { loaTxt = prlCalcDataLOA[i][1]; varFields = prlCalcDataLOA[i][2]; break; }
			}
			for(i=0;i<3;i++) {
				var a = (varFields & Math.pow(2,i)) != 0 ? true : false;
				$('#'+vf[i]).prop('disabled', !a);
				$('label[for='+vf[i]+']').css('color', a?'':'#888');
			}
			$('#prlCalcDataLoaTxt').val(loaTxt);
		});

		$('#prlCalcDataTxtEmpl').bind('focus', function() {
			$(this).data("lastEmplID", $('#prlCalcDataCmbEmpl'));
		});
		$('#prlCalcDataTxtEmpl').bind('blur', function() {
			if($('#prlCalcDataCmbEmpl') != $(this).data("lastEmplID")) $('#prlCalcDataCmbEmpl').change();
		});

		prlCalcDataFldState(1);
		$('#prlCalcDataTxtEmpl').focus();
		$('#prlCalcDataCmbEmpl').change(); //hiermit laden wir die Daten des ersten selektierten MA

		$( "#prlCalcDataLoaNo" ).autocomplete({
			delay: 1000,
			source: prlCalcDataLoaAC,
			close: function() { $('#prlCalcDataLoaNo').change(); }
		});

		$('#prlCalcDataBtnAdd').bind('click', function() {
			prlCalcDataAdd();
		});
		$('#prlCalcDataBtnClr').bind('click', function() {
			prlCalcDataClr('prlCalcDataTxtEmpl');
		});
		$('#prlCalcDataBtnCancel').bind('click', function() {
			$('#prlCalcDataCmbEmpl').data('suspendEvents',true);
			$('#prlCalcDataEditor').mb_close();
		});
		$('#prlCalcDataBtnSave').bind('click', function() {
			$.each(prlCalcDataStorage, function(empl,recs) {
				$.each(recs, function(recid,recdat) {
					if(recdat.action!='delete') {
						if(recdat.defaultTxt) delete recdat.account_text;
						delete recdat.defaultTxt;
						if(recdat.quantity=='') delete recdat.quantity;
						if(recdat.rate=='') delete recdat.rate;
						if(recdat.amount=='') delete recdat.amount;
						if(recdat.dateFrom=='') delete recdat.dateFrom;
						if(recdat.dateTo=='') delete recdat.dateTo;
						if(recdat.CostCenter=='') delete recdat.CostCenter;
					}
				});
			});
			cb('payroll.prlCalcDataSave',prlCalcDataStorage);
		});


		var mnu = $('#prlCalcDataTblMenu');
		mnu.menu();
		mnu.hide();
		mnu.css('width','100');
		mnu.css('position','fixed');
		mnu.bind('mouseleave', function(e) { $(this).hide(); });
		$('#prlCalcDataTblMenu li a').bind('click', function(e) {
			var rid = $(this).parent().parent().attr('rid');
			switch($(this).attr('act')) {
			case '1':
				//Daten in Felder laden
				var srec = {};
				for(i=0;i<prlCalcDataTblData.length;i++) if(prlCalcDataTblData[i].id==rid) { srec = prlCalcDataTblData[i]; break; }
				$('#prlCalcDataCmbType').val(srec.PayrollDataType);
				$('#prlCalcDataLoaNo').val(srec.payroll_account_ID);
				$('#prlCalcDataQuantity').val(srec.quantity);
				$('#prlCalcDataRate').val(srec.rate);
				$('#prlCalcDataAmount').val(srec.amount);
				$('#prlCalcDataFrom').val(srec.dateFrom);
				$('#prlCalcDataTo').val(srec.dateTo);
				$('#prlCalcDataCC').val(srec.CostCenter);

				//Felder MENGE,ANSATZ,BETRAG gem. LOA-Einstellungen ein-/ausblenden
				$('#prlCalcDataLoaNo').change();
				$('#prlCalcDataLoaTxt').val(srec.account_text);
				//Felder DATUM von/bis gem. LOA-Einstellungen ein-/ausblenden
				prlCalcDataFldState(srec.PayrollDataType);

				//Controlls, die etwas mit dem Ändern des MA zu tun haben, sperren
				//Labels der Buttons ändern
				prlCalcDataEditMode(true,rid);
				//Entsprechende Tabellenzeile einfärben (#7d7)
				break;
			case '2':
				var employeeID = 0;
				for(var i=0;i<prlCalcDataTblData.length;i++) {
					if(prlCalcDataTblData[i].id==rid) {
						employeeID = prlCalcDataTblData[i].emplId;
						if(rid>=2000000000) prlCalcDataTblData.splice(i, 1);
						else prlCalcDataTblData[i].RecStatus=3; //rec auf 'gelöscht' setzen
						break;
					}
				}

				if(!(employeeID in prlCalcDataStorage)) prlCalcDataStorage[employeeID.toString()] = {};
				if(rid>=2000000000) delete prlCalcDataStorage[employeeID.toString()][rid.toString()];
				else prlCalcDataStorage[employeeID.toString()][rid.toString()] = {'action':'delete'};

				prlCalcDataView.beginUpdate();
				prlCalcDataView.setItems(prlCalcDataTblData);
				prlCalcDataView.endUpdate();
				prlCalcDataGrid.setSortColumn("payroll_account_ID",true);
				prlCalOvSortSingleColumn("payroll_account_ID",true);
				break;
			}
			$(this).parent().parent().hide();
			return false;
		});
	}

	function prlCalcDataOpen() {
		var objWnd = $('#prlCalcDataEditor');
		if ( objWnd.length > 0 ) {
			if(objWnd.mb_getState('iconized')) objWnd.mb_iconize();
			else objWnd.mb_bringToFront();
		}else{
			cb('payroll.prlCalcDataEditor');
		}
	}
/*
*************************************
** Calculation: period closing/carry forward form
*************************************
*/
var prlCalcFinDATA = {};
var prlCalcFinCFG = {};

function prlCalcFinSave() {
	var ret = {};
	$('.prlCalcFinNPrd select, #prlCalcFinTabs input').each(function( index ) {
		var n = $(this).attr('id');
		n = n.replace("prlCalcFin_","");
		if($(this).get(0).tagName=='INPUT' && $(this).attr('type')=='checkbox') ret[n] = $(this).is(':checked') ? 1 : 0;
		else ret[n] = $(this).val();
	});
	cb('payroll.prlCalcOvProcess',{'functionNumber':3, 'commit':1, 'formData': ret});
}

function prlCalcFinUpdatePrd(n) {
	var o = $('#prlCalcFin_major_period');
	o.find('option').remove();
	$.each(n, function() {
		o.append( $('<option></option>').attr("value", this[0]).text(this[1]) );
	});
}

function prlCalcFinToggleEqualDates() {
	if($('#prlCalcFin_equalDates').is(':checked')) {
		var r = '.prlCalcFinDate > li';
		$(r+':first-child + li > ul > li > input').not(':first').attr('disabled',true);
		$(r+':last-child > ul > li > input').not(':first').attr('disabled',true);

		$(r+':first-child + li > ul > li > input:first, '+r+':last-child > ul > li > input:first').bind('keyup', function(e) {
			$(this).parent().parent().find('input').not(':first').val( $(this).val() );
		});
		$('.prlCalcFinDate input').keyup();
	}else{
		$('.prlCalcFinDate input').removeAttr('disabled');
		$('.prlCalcFinDate input').unbind('keyup');
	}
}

function prlCalcFinInit() {
	$('#prlCalcFinTabs').tabs();
	$.each(prlCalcFinDATA, function(key, val) {
		var o = $('#prlCalcFin_'+key);
		if(o.get(0).tagName=='SELECT') {
			o.find('option').remove();
			$.each(val, function() {
				o.append( $('<option></option>').attr("value", this[0]).text(this[1]) );
			});
		}else{
			switch(o.attr('type')) {
			case 'checkbox':
				if(val==1) o.attr('checked', true);
				else o.removeAttr('checked');
				break;
			default:
				o.val(val);
				break;
			}
		}
	});
	$('.prlCalcFinDate input[type=text]').bind('change', function(e) {
		if(!prlCalcFinCFG.chkDate.test($(this).val())) $(this).css('background-color','#f88');
		else $(this).css('background-color','');
	});
	$('#prlCalcFinSave').bind('click', function(e) {
		prlCalcFinSave();
	});
	$('#prlCalcFinCancel').bind('click', function(e) {
		$('#modalContainer').mb_close();
	});
	$('#prlCalcFin_equalDates').bind('click', function(e) {
		prlCalcFinToggleEqualDates();
	});
	$('#prlCalcFin_payroll_year_ID').bind('change', function(e) {
		cb('payroll.prlCalcOvProcess',{'functionNumber':1003, 'year':$(this).val()});
	});
	prlCalcFinToggleEqualDates();
}
/*
********************************************
** Utility functions: Employee selector form
********************************************
*/
var prlPsSelALL = []; //alle MA im aktuellen Filter (id, persNr, Vorname, Nachname)
var prlPsSelEXCL = []; //bietet die Möglichkeit, MA zu exkludieren
var prlPsSelCFG = {};

function prlPsSelUpdate() {
	var elSelect = $('#prlPsSelLst');
	elSelect.find('option').remove();
	$.each(prlPsSelALL, function() {
		if(jQuery.inArray(this[0], prlPsSelEXCL)==-1)
			elSelect.append( $('<option></option>').val(this[0]).html(this[1]+' - '+this[3]+', '+this[2]) );
	});
	$('#prlPsSelLst').filterByText($('#prlPsSelQFilter'), true);
}

function prlPsSelFnc(fnc) {
	switch(fnc) {
	case 'sa':
		$("#prlPsSelLst option").attr("selected",true);
		break;
	case 'da':
		$("#prlPsSelLst option:selected").removeAttr("selected");
		break;
	case 'iv':
		$("#prlPsSelLst > option").each(function() {
			if($(this).is(':selected')) $(this).removeAttr("selected");
			else $(this).attr("selected",true);
		});
		break;
	}
	$("#prlPsSelLst").focus();
}

function prlPsSelSave() {
	var x = $("#prlPsSelLst");
	if(!$.isArray(x.val())) $('#prlPsSelErr').text(prlPsSelCFG.errNoSelection);
	else if(typeof(prlPsSelCFG.saveCB)=="string") cb(prlPsSelCFG.saveCB,x.val());
	else prlPsSelCFG.saveCB();
}

function prlPsSelCancel() {
	if(prlPsSelCFG.cancelCB=='') $('#modalContainer').mb_close();
	else cb(prlPsSelCFG.cancelCB);
}

function prlPsSelInit() {
	var o = $('.prlPsSelRight button');
	o.eq(0).bind('click', function() { prlPsSelFnc('sa'); });
	o.eq(1).bind('click', function() { prlPsSelFnc('da'); });
	o.eq(2).bind('click', function() { prlPsSelFnc('iv'); });
	$('#prlPsSelSave').bind('click', function() { prlPsSelSave(); });
	$('#prlPsSelCancel').bind('click', function() { prlPsSelCancel(); });
	$('#prlPsSelLst').bind('click', function() { $('#prlPsSelErr').text(''); });
	$('#prlPsSelDBFilter').bind('change', function() { cb('payroll.EmployeeSelectorFilter',$(this).val()); });
}
/*
********************************************
** FIBU / BEBU Report Selector
********************************************
*/
var prlRepSelCFG = {};

function prlRepSelOnReportTypeChange(elem) {

    if (elem.value >= 1 && elem.value <= 5) {
        document.getElementById('hiddenDiv_Company').style.display = "block";
    }
    else {
        document.getElementById('hiddenDiv_Company').style.display = "none";
    }
    if (elem.value == 2 || elem.value == 4 || elem.value == 7 || elem.value == 9) {
        document.getElementById('hiddenDiv_CostCenter').style.display = "block";
    }
    else {
        document.getElementById('hiddenDiv_CostCenter').style.display = "none";
    }
}

function prlRepSelSave() {
    var company = $("#prlRepSelFilterFirma").val();
    var kst = $("#prlRepSelFilterKst").val();

    $('#prlRepSelErr').text("");
    
    var emptyRgx = /^\s?$/;
    var companyRgx = /^[0-9]{1,11}$/;
    var kstRgx = /^[A-Za-z0-9]{0,15}$/;
    var errors = "";

    if (document.getElementById('hiddenDiv_Company').style.display != "none" && !emptyRgx.test(company) && !companyRgx.test(company)) {
        errors += prlRepSelCFG.errWrongCompanyFormat;
    }
    if (document.getElementById('hiddenDiv_CostCenter').style.display != "none" && !emptyRgx.test(kst) != null && !kstRgx.test(kst)) {
        errors += prlRepSelCFG.errWrongKstFormat;
    }

    if (!emptyRgx.test(errors)) {
        $('#prlRepSelErr').text(errors);
        return;
    }

    //if (typeof (prlPsSelCFG.saveCB) == "string")
    //    cb(prlRepSelCFG.saveCB, x.val());
    //else
    prlRepSelCFG.saveCB();
}

function prlRepSelCancel() {
    if (prlRepSelCFG.cancelCB == '') $('#modalContainer').mb_close();
    else cb(prlRepSelCFG.cancelCB);
}

function prlRepSelInit() {
    $('#prlRepSelSave').bind('click', function () { prlRepSelSave(); });
    $('#prlRepSelCancel').bind('click', function () { prlRepSelCancel(); });
    //$('#prlPsSelLst').bind('click', function () { $('#prlPsSelErr').text(''); });
    $('#prlRepSelReportType').bind('change', function () { prlRepSelOnReportTypeChange(this) });
}

/*
********************************************
** Configuration: Financial and management accounting
********************************************
*/
var prlAccAsgData = [ [],[],[],[] ];
var prlAccAsgParam = {};
var prlAccAsgGrd = []; //Grid objects 1,2,3
var prlAccAsgDV = []; //Dataviews 1,2,3
var prlAccAsgColFltr = [[],[],[],[]];
var prlAccAsgLoaAC = [];

function prlAccAsgFilter(item) {
	var curTab = $('#prlAccAsgTabs').tabs('option', 'active') + 1;
	for(var columnId in prlAccAsgColFltr[curTab]) {
		if(columnId !== undefined && prlAccAsgColFltr[curTab][columnId] !== "") {
			var c = prlAccAsgGrd[curTab].getColumns()[prlAccAsgGrd[curTab].getColumnIndex(columnId)];
			if(item[c.field].toString().toLowerCase().indexOf(prlAccAsgColFltr[curTab][columnId].toString().toLowerCase()) == -1) {
				return false;
			}
		}
	}
	return true;
}

function prlAccAsgToggleFilterRow() {
	for(var i=1;i<4;i++) {
		if($("#prlAccAsgBtnQFilter").is(':checked')) {
			$(prlAccAsgGrd[i].getHeaderRow()).show();
			prlAccAsgGrd[i].showHeaderRow(true);
		}else{
			$(prlAccAsgGrd[i].getHeaderRow()).hide();
			prlAccAsgGrd[i].showHeaderRow(false);
			$(prlAccAsgGrd[i].getHeaderRow()).find("input").val('');
			for(var columnId in prlAccAsgColFltr[i]) prlAccAsgColFltr[i][columnId] = "";
			prlAccAsgDV[i].refresh();
		}
		prlAccAsgGrd[i].resizeCanvas();
	}
}

function prlAccAsgSortSingleColumn(field, sortAsc, updateGrid) {
	var curTab = $('#prlAccAsgTabs').tabs('option', 'active') + 1;
	prlAccAsgDV[curTab].sort(function(a, b){
		var result = a[field] > b[field] ? 1 : a[field] < b[field] ? -1 : 0; 
		return sortAsc ? result : -result;
	});
	if(updateGrid==null) {
		prlAccAsgGrd[curTab].setData(prlAccAsgDV[curTab]);
		prlAccAsgGrd[curTab].updateRowCount();
		prlAccAsgGrd[curTab].render();
	}
}

function prlAccAsgToggleBtns() {
	var curTab = $('#prlAccAsgTabs').tabs('option', 'active') + 1;
	var mndFlds = [ [],['aid','acc','et'],['cc','aid','amt'],['aid','acc','et'] ];
	var x = false;
	$.each(mndFlds[curTab], function() {
		if($.trim($('#prlAccAsg_'+this+curTab).val())=='') x=true;
	});
	$('#prlAccAsgBtnSave, #prlAccAsgBtnCancel').prop('disabled', x);
}

function prlAccAsgSave() {
	if( $('#prlAccAsgBtnSave').is(':disabled') ) return;
	var curTab = $('#prlAccAsgTabs').tabs('option', 'active') + 1;
	var testPassed = true;
	//validity checks
	$.each(prlAccAsgParam.validity[curTab], function() {
		var curField = $('#prlAccAsg_'+this[0]);
		if( this[1].test(curField.val()) ) {
			curField.css('background-color','');
			$('#prlAccAsgErr'+curTab).text('');
		}else{
			curField.css('background-color','#f88');
			$('#prlAccAsgErr'+curTab).text( prlAccAsgParam.validity[0][0]+$('label[for=prlAccAsg_'+this[0]+']').text()+prlAccAsgParam.validity[0][1] );
			testPassed = false;
		}
	});
	var curField = $('#prlAccAsg_aid'+curTab);
	if(curField.val().toString() in prlAccAsgParam.loa) {
	}else{
		curField.css('background-color','#f88');
		$('#prlAccAsgErr'+curTab).text( prlAccAsgParam.validity[0][0]+$('label[for=prlAccAsg_aid'+curTab+']').text()+prlAccAsgParam.validity[0][1] );
		testPassed = false;
	}
	if(!testPassed) return;

	if(prlAccAsgParam.savemode==1) {
		//direkt in db speichern
		var submitObj = {};
		$('input[id^="prlAccAsg_"], select[id^="prlAccAsg_"]').each(function( index ) {
			var sectionMapper = [ '','fin_acc_assign','mgmt_acc_split','mgmt_acc_assign' ];
			var fieldMapper = {'cid':'payroll_company_ID', 'eid':'payroll_employee_ID', 'cc':'cost_center', 'aid':'payroll_account_ID', 'acc':'account_no', 'cacc':'counter_account_no', 'dc':'debitcredit', 'inv':'invert_value', 'et':'entry_text', 'amt':'amount', 'rem':'remainder'};

			submitObj['section'] = sectionMapper[curTab];
			if($('#prlAccAsgBtnSave[edt]').length>0) {
				submitObj['mode'] = 'edit'; //'add','edit','delete'
				submitObj['id'] = $('#prlAccAsgBtnSave').attr('edt');
			}else{
				submitObj['mode'] = 'add';
			}
			if($(this).attr('id').slice(-1)==curTab) {
				var fldName = $(this).attr('id').substring(10);
				fldName = fldName.substring(0, fldName.length - 1);
				submitObj[fieldMapper[fldName]] = $(this).val();
			}
		});
		cb('payroll.saveAccountAssingment', submitObj);
	}else{
		alert('speichern in variables layout ist noch nicht aktiv');
	}
}

function prlAccAsgIndexByID(section,id) {
	for(var i=0;i<prlAccAsgData[section].length;i++) {
		if(prlAccAsgData[section][i].id==id) return i;
	}
	return -1;
}

function prlAccAsgRec(section, mode, retObj) {
	var sectionMapper = {'fin_acc_assign':1,'mgmt_acc_split':2,'mgmt_acc_assign':3};
	var curTab = sectionMapper[section];
	switch(mode) {
	case 'add':
		//Firmen- und MA-Codes im Data array in leserlichen Text umwandeln
		retObj['cidt'] = prlAccAsgParam.companies[retObj.cid];
		retObj['eidt'] = prlAccAsgParam.employees[retObj.eid];
		//neuer Rec dem Array hinzufügen
		prlAccAsgData[curTab].push(retObj);
		//felder löschen
		$('#prlAccAsgTb'+curTab+' input[id^=prlAccAsg_]').val('');
		$('#prlAccAsgTb'+curTab+' select[id^=prlAccAsg_]').val(0);
		//fokus auf erstes eingabefeld setzen
		$('#prlAccAsg_cid'+curTab).focus();
		break;
	case 'edit':
		var editID = prlAccAsgIndexByID(curTab,retObj.id);
		//Firmen- und MA-Codes im Data array in leserlichen Text umwandeln
		retObj['cidt'] = prlAccAsgParam.companies[retObj.cid];
		retObj['eidt'] = prlAccAsgParam.employees[retObj.eid];
		//bestehender Rec ersetzen
		prlAccAsgData[curTab][editID] = retObj;
		//felder löschen
		$('#prlAccAsgTb'+curTab+' input[id^=prlAccAsg_]').val('');
		$('#prlAccAsgTb'+curTab+' select[id^=prlAccAsg_]').val(0);
		//fokus auf erstes eingabefeld setzen
		$('#prlAccAsg_cid'+curTab).focus();
		break;
	case 'delete':
		var delID = prlAccAsgIndexByID(curTab,retObj.id);
		prlAccAsgData[curTab].splice(delID, 1);
		break;
	}
	//tabelle aktualisieren
	prlAccAsgDV[curTab].beginUpdate();
	prlAccAsgDV[curTab].setItems(prlAccAsgData[curTab]);
	prlAccAsgDV[curTab].setFilter(prlAccAsgFilter);
	prlAccAsgDV[curTab].endUpdate();
	prlAccAsgGrd[curTab].setData(prlAccAsgDV[curTab]);
	prlAccAsgGrd[curTab].updateRowCount();
	prlAccAsgGrd[curTab].render();

	$('#prlAccAsgBtnSave').removeAttr('edt');
	$("#prlAccAsgTabs").tabs("option", "disabled", false);

	prlAccAsgToggleBtns();
}

function prlAccAsgInit() {
	$('#prlAccAsgTabs').tabs().bind('tabsactivate', function(event, ui) {
		prlAccAsgToggleBtns();
	});

	var columns = [ [],
			[
				{id: "cidt", name: "Firma", field: "cidt", sortable: true, resizable: true, width: 100},
				{id: "eidt", name: "Mitarbeiter", field: "eidt", sortable: true, resizable: true, width: 160},
				{id: "cc", name: "KST", field: "cc", sortable: true, resizable: true, width: 60},
				{id: "aid", name: "LOA", field: "aid", sortable: true, resizable: true, width: 50},
				{id: "acc", name: "Konto", field: "acc", sortable: true, resizable: true, width: 60},
				{id: "cacc", name: "G-Konto", field: "cacc", sortable: true, resizable: true, width: 60},
				{id: "dc", name: "S/H", field: "dc", sortable: true, width: 35, cssClass: "txtCenter", formatter: function (row, cell, value, columnDef, dataContext) { return value==1 ? prlAccAsgParam.credit : prlAccAsgParam.debit; } },
				{id: "inv", name: "Vz", field: "inv", sortable: true, width: 40, cssClass: "txtCenter", formatter: function (row, cell, value, columnDef, dataContext) { return value==1 ? prlAccAsgParam.yes : prlAccAsgParam.no; } },
				{id: "et", name: "Text", field: "et", sortable: true, width: 160}
			],
			[
				{id: "cidt", name: "Firma", field: "cidt", sortable: true, resizable: true, width: 155},
				{id: "eidt", name: "Mitarbeiter", field: "eidt", sortable: true, resizable: true, width: 200},
				{id: "cc", name: "KST", field: "cc", sortable: true, resizable: true, width: 110},
				{id: "aid", name: "LOA", field: "aid", sortable: true, resizable: true, width: 110},
				{id: "amt", name: "Proz.", field: "amt", sortable: true, width: 65},
				{id: "inv", name: "Vz", field: "inv", sortable: true, width: 45, csClass: "txtCenter", formatter: function (row, cell, value, columnDef, dataContext) { return value==1 ? prlAccAsgParam.yes : prlAccAsgParam.no; } },
				{id: "rem", name: "Rest", field: "rem", sortable: true, width: 40, csClass: "txtCenter"}
			]
	];
	columns[3] = columns[1];

	//SELECT mit OPTIONS füllen
	$.each(prlAccAsgParam.companies, function(k,v) {
		for(var i=1;i<4;i++) $('#prlAccAsg_cid'+i).append( $('<option>').text(v).val(k) );
	});
	$.each(prlAccAsgParam.employees, function(k,v) {
		for(var i=1;i<4;i++) $('#prlAccAsg_eid'+i).append( $('<option>').text(v).val(k) );
	});
	//Firmen- und MA-Codes im Data array in leserlichen Text umwandeln
	for(var i=1;i<4;i++) {
		$.each(prlAccAsgData[i], function() {
			this['cidt'] = prlAccAsgParam.companies[this.cid];
			this['eidt'] = prlAccAsgParam.employees[this.eid];
		});
	}

	prlAccAsgLoaAC = [];
	$.each(prlAccAsgParam.loa, function(k,v) {
		prlAccAsgLoaAC.push({ 
			"label" : k + ' - ' + v,
			"value" : k
		});
	});

	$('#prlAccAsg_aid1, #prlAccAsg_aid2, #prlAccAsg_aid3').autocomplete({
		delay: 1000,
		source: prlAccAsgLoaAC,
		close: function() { /*$('#prlCalcDataLoaNo').change();*/ }
	});

	for(var i=1;i<4;i++) {
		prlAccAsgDV[i] = new Slick.Data.DataView();
		prlAccAsgGrd[i] = new Slick.Grid("#prlAccAsgGrd"+i, prlAccAsgDV[i], columns[i], { showHeaderRow: true, enableCellNavigation: false, enableColumnReorder: false, explicitInitialization: true });
		prlAccAsgGrd[i].onSort.subscribe(function (e, args) {
			prlAccAsgSortSingleColumn(args.sortCol.field, args.sortAsc);
		});

		switch(i) {
		case 1:
			prlAccAsgDV[1].onRowCountChanged.subscribe(function (e, args) { prlAccAsgGrd[1].updateRowCount(); prlAccAsgGrd[1].render(); });
			prlAccAsgDV[1].onRowsChanged.subscribe(function (e, args) { prlAccAsgGrd[1].invalidateRows(args.rows); prlAccAsgGrd[1].render(); });
			break;
		case 2:
			prlAccAsgDV[2].onRowCountChanged.subscribe(function (e, args) { prlAccAsgGrd[2].updateRowCount(); prlAccAsgGrd[2].render(); });
			prlAccAsgDV[2].onRowsChanged.subscribe(function (e, args) { prlAccAsgGrd[2].invalidateRows(args.rows); prlAccAsgGrd[2].render(); });
			break;
		case 3:
			prlAccAsgDV[3].onRowCountChanged.subscribe(function (e, args) { prlAccAsgGrd[3].updateRowCount(); prlAccAsgGrd[3].render(); });
			prlAccAsgDV[3].onRowsChanged.subscribe(function (e, args) { prlAccAsgGrd[3].invalidateRows(args.rows); prlAccAsgGrd[3].render(); });
			break;
		}

		$(prlAccAsgGrd[i].getHeaderRow()).delegate(":input", "change keyup", function (e) {
			var columnId = $(this).data("columnId");
			var curTab = $('#prlAccAsgTabs').tabs('option', 'active') + 1;
			if (columnId != null) {
				prlAccAsgColFltr[curTab][columnId] = $.trim($(this).val());
				if(columnId=='dc') if($.trim($(this).val()).toLowerCase()==prlAccAsgParam.debit.toLowerCase()) prlAccAsgColFltr[curTab][columnId] = 0; else if($.trim($(this).val()).toLowerCase()==prlAccAsgParam.credit.toLowerCase()) prlAccAsgColFltr[curTab][columnId] = 1;
				if(columnId=='inv') if($.trim($(this).val()).toLowerCase()==prlAccAsgParam.no.toLowerCase()) prlAccAsgColFltr[curTab][columnId] = 0; else if($.trim($(this).val()).toLowerCase()==prlAccAsgParam.yes.toLowerCase()) prlAccAsgColFltr[curTab][columnId] = 1;
				prlAccAsgDV[curTab].refresh();
			}
		});

		prlAccAsgGrd[i].onHeaderRowCellRendered.subscribe(function(e, args) {
			var curTab = $('#prlAccAsgTabs').tabs('option', 'active') + 1;
			$(args.node).empty();
			$("<input type='text' style='width:100%;'>")
			   .data("columnId", args.column.id)
			   .val(prlAccAsgColFltr[curTab][args.column.id])
			   .appendTo(args.node);
		});

		prlAccAsgGrd[i].onClick.subscribe(function(e, args) {
			if($('#prlAccAsgBtnSave[edt]').length>0) return;
			var curTab = $('#prlAccAsgTabs').tabs('option', 'active') + 1;
			var cell = prlAccAsgGrd[curTab].getCellFromEvent(e), row = cell.row;
			var item = prlAccAsgDV[curTab].getItem(row); //args.item;
//			if($('#prlCalcDataBtnAdd[rid]').length>0) return; //do not show menu in edit mode!

			var mnu = $('#prlAccAsgTblMenu');
			mnu.css('top',e.pageY-15);
			mnu.css('left',e.pageX-70);
			mnu.css('zIndex', 9999);
			mnu.attr('rid', item.id);
			mnu.attr('tid', curTab);
			mnu.show();
		});

		prlAccAsgGrd[i].init();

		prlAccAsgDV[i].beginUpdate();
		prlAccAsgDV[i].setItems(prlAccAsgData[i]);
		prlAccAsgDV[i].setFilter(prlAccAsgFilter);
		prlAccAsgDV[i].endUpdate();
	}

	$('#prlAccAsgDel').hide();

	var mnu = $('#prlAccAsgTblMenu');
	mnu.menu();
	mnu.hide();
	mnu.css('width','100');
	mnu.css('position','fixed');
	mnu.bind('mouseleave', function(e) { $(this).hide(); });
	$('#prlAccAsgTblMenu li a').bind('click', function(e) {
		var rid = $(this).parent().parent().attr('rid');
		var tid = $(this).parent().parent().attr('tid');
		switch($(this).attr('act')) {
		case '1': //edit
			var editID = prlAccAsgIndexByID(tid,rid);
			var targetObj = prlAccAsgData[tid][editID];
			//id in "edt" attribut laden .. "edt" attribut nach speichern oder abbrechen wieder entfernen... denn "edt" attribut soll auch als indikator für den edit modus dienen!
			$('#prlAccAsgBtnSave').attr('edt',targetObj.id);
			$.each(targetObj, function(k,v) {
				$('#prlAccAsg_'+k+tid).val(v);
			});
			prlAccAsgToggleBtns();
			$("#prlAccAsgTabs").tabs("option", "disabled", true);
			break;
		case '2': //delete
			var sectionMapper = [ '','fin_acc_assign','mgmt_acc_split','mgmt_acc_assign' ];
//			var submitObj = { 'section':sectionMapper[tid], 'mode':'delete', 'id':rid };
			prlAccAsgParam.deleteObj = { 'section':sectionMapper[tid], 'mode':'delete', 'id':rid };
//			cb('payroll.saveAccountAssingment', submitObj);
			$('#prlAccAsgDel').css('top',e.pageY-15).css('left',e.pageX-70).css('zIndex', 9999).show();
			break;
		}
		$(this).parent().parent().hide();
		return false;
	});

	$('#prlAccAsgDel button').eq(0).bind('click', function(e) {
		$('#prlAccAsgDel').hide();
		cb('payroll.saveAccountAssingment', prlAccAsgParam.deleteObj);
	});
	$('#prlAccAsgDel button').eq(1).bind('click', function(e) {
		$('#prlAccAsgDel').hide();
	});

	$('#prlAccAsgBtnSave, #prlAccAsgBtnCancel').prop('disabled', true);
	$('input[type=text][id^="prlAccAsg_"]').bind('change keyup', function() {
		prlAccAsgToggleBtns();
	});
	$('input[id^="prlAccAsg_"], select[id^="prlAccAsg_"]').keypress(function (e) {
		if (e.which == 13) {
			prlAccAsgSave();
		}
	});
	$('#prlAccAsgBtnSave').bind('click', function() {
		prlAccAsgSave();
	});
	$('#prlAccAsgBtnCancel').bind('click', function() {
		var curTab = $('#prlAccAsgTabs').tabs('option', 'active') + 1;
		//felder löschen
		$('#prlAccAsgTb'+curTab+' input[id^=prlAccAsg_]').val('');
		$('#prlAccAsgTb'+curTab+' select[id^=prlAccAsg_]').val(0);
		//edit-status entfernen und tabs wieder aktivieren
		$('#prlAccAsgBtnSave').removeAttr('edt');
		$("#prlAccAsgTabs").tabs("option", "disabled", false);
		prlAccAsgToggleBtns();
	});
	$('#prlAccAsgBtnClose').bind('click', function() {
		$('#modalContainer').mb_close();
	});

	//Button: Tabellenfilter
	$( "#prlAccAsgBtnQFilter" ).button({
		text: false,
		icons: {
			primary: "p-icon-tblfilter"
		}
	})
	.click(function() {
		prlAccAsgToggleFilterRow();
	});

	prlAccAsgToggleFilterRow();
}

/*
*** Calculation: Payout / process payment
*/
var prlPayoutData = {};

function prlPayoutInit() {
	$('#prlPayout_payment_date, #prlPayout_interest_date').datepicker({
		showOn: 'button',
		buttonImage: 'web/css/calendar.png',
		buttonImageOnly: true
	});
	$('#prlPayout_filter_mode').bind('change keyup', function() {
		var o = $('#prlPayout_payroll_company_ID, label[for=prlPayout_payroll_company_ID]');
		if($(this).val()==1) o.show();
		else o.hide();
	});
	$('#prlPayout_payroll_company_ID, label[for=prlPayout_payroll_company_ID]').hide();
	$('#prlPayoutBtnSave').bind('click', function() {
		$(this).prop('disabled', true);
		prlPayoutData['subfnc1'] = {};
		prlPayoutData['subfnc1']['payment_date'] = $('#prlPayout_payment_date').val();
		prlPayoutData['subfnc1']['interest_date'] = $('#prlPayout_interest_date').val();
		prlPayoutData['subfnc1']['filter_mode'] = $('#prlPayout_filter_mode').val();
		prlPayoutData['subfnc1']['payroll_company_ID'] = $('#prlPayout_payroll_company_ID').val();
		cb('payroll.prlCalcOvProcess',{'functionNumber':'4','subFunction':'1','data':prlPayoutData.subfnc1});
	});
	$('#prlPayoutBtnCancel').bind('click', function() {
		prlPayoutData = {};
		$('#modalContainer').mb_close();
	});
}

/*
*** Calculation: accounting entry
*/
var prlAccEtrData = {};

function prlAccEtrToggleFieldStatus() {
	var count = 0;
	$.each([$('#prlAccEtr_fin_acc_process'),$('#prlAccEtr_mgmt_acc_process')], function() {
		var chk = !this.is(':checked');
		var prnt = this.parent().parent();
		prnt.find('input').not('[xx=1]').prop('disabled', chk);
		prnt.find('label').css('color', chk?'#888':'');
		prnt.find('.hasDatepicker').datepicker(chk?'disable':'enable');
		if(chk) count++;
	});
	$('#prlAccEtrBtnSave').prop('disabled', count==2?true:false);
}

function prlAccEtrInit() {
	$('#prlAccEtr_fin_acc_date, #prlAccEtr_mgmt_acc_date').datepicker({
		showOn: 'button',
		buttonImage: 'web/css/calendar.png',
		buttonImageOnly: true
	});
	$('#prlAccEtr_filter_mode').bind('change keyup', function() {
		var o = $('#prlAccEtr_payroll_company_ID, label[for=prlAccEtr_payroll_company_ID]');
		if($(this).val()==1) o.show();
		else o.hide();
	});
	$('#prlAccEtr_payroll_company_ID, label[for=prlAccEtr_payroll_company_ID]').hide();
	$('#prlAccEtrBtnSave').bind('click', function() {
		prlAccEtrData['subfnc1'] = {};
		$("input[id^='prlAccEtr_'],select[id^='prlAccEtr_']").each(function() {
			prlAccEtrData['subfnc1'][$(this).attr('id').substring(10, 100)] = $(this).is(":checkbox") ? ($(this).is(':checked')?1:0) : $(this).val();
		});
		cb('payroll.prlCalcOvProcess',{'functionNumber':'2','subFunction':'1','data':prlAccEtrData.subfnc1});
	});
	$('#prlAccEtrBtnCancel').bind('click', function() {
		prlAccEtrData = {};
		$('#modalContainer').mb_close();
	});
	$('#prlAccEtr_fin_acc_process, #prlAccEtr_mgmt_acc_process').bind('click', function() {
		prlAccEtrToggleFieldStatus();
	});
}
/*
*** Utilities: Employee filter
*/
var prlUtlEfc = {};

function prlUtlEfcToggleEdtFields() {
	var f = {'FieldName':[true,false,false,false], 'FieldModifier':[false,false,false,false], 'Conjunction':[false,true,false,false], 'Comparison':[true,false,false,false], 'ComparativeValues':[true,false,false,false], 'Checkbox':[false,false,false,false], 'Select':[false,false,false,false], 'DateFrom':[false,false,false,false], 'DateTo':[false,false,false,false]};
	var t = $('#prlUtlEfcEdtCriteriaType').val() - 1;
	var sf = $('#prlUtlEfcEdtFieldName').val();
	var cm = [];
	var fieldDefObj = sf.substring(0,3)=='ex_' ? prlUtlEfc.auxFields : prlVlFieldDef;
	switch(fieldDefObj[sf].type) {
	case 1: //text
		cm = [true,false,false,true,false,false,false]; //bestimmt, welche der Comparison OPTIONs angezeigt werden
		break;
	case 2: //checkbox
		cm = [true,false,false,true,false,false,false];
		f.ComparativeValues = false;
		f.Checkbox[0] = true;
		$('#prlUtlEfcEdtCheckbox').removeAttr('disabled');
		break;
	case 3: //nummer
		cm = [true,true,true,true,true,true,false];
		break;
	case 4: //liste
		cm = [true,false,false,true,false,false,false];
		//liste leeren und neu füllen
		f.ComparativeValues = false;
		f.Select[0] = true;
		var se = $('#prlUtlEfcEdtSelect');
		se.find('option').remove();
		$.each(fieldDefObj[sf].options, function() {
			se.append( $('<option></option>').attr("value", this[0]).text(this[1]) );
		});
		se.removeAttr('disabled');
		break;
	case 5: //datum
		cm = [true,true,true,true,true,true,false];
		f.FieldModifier[0] = true;
		break;
	case 91: //datum ohne field modifier
		cm = [true,false,false,true,false,false,false];
		break;
	case 92: //von-bis datum
		cm = [true,false,false,true,false,false,false];
		f.ComparativeValues = false;
		f.DateFrom[0] = true;
		f.DateTo[0] = true;
		$('#prlUtlEfcEdtDateFrom').removeAttr('disabled');
		$('#prlUtlEfcEdtDateTo').removeAttr('disabled');
		break;
	}
	$('#prlUtlEfcEdtComparison option').each(function( index ) {
		if(cm[$(this).index()]) $(this).show();
		else $(this).hide();
	});
	$.each(f, function(fld, sw) {
		if(sw[t]) $('#prlUtlEfcEdt'+fld+',label[for=prlUtlEfcEdt'+fld+']').show().next("br").show();
		else $('#prlUtlEfcEdt'+fld+',label[for=prlUtlEfcEdt'+fld+']').hide().next("br").hide();
	});
}

function prlUtlEfcSaveItem() {
	//manuell erfasste daten überprüfen
	if($('#prlUtlEfcEdtComparativeValues').is(":visible") || $('#prlUtlEfcEdtDateFrom').is(":visible")) {
		var tObj = $('#prlUtlEfcEdtComparativeValues');
		var testValue = $('#prlUtlEfcEdtComparativeValues').val();
		var fldName = $('#prlUtlEfcEdtFieldName').val();
		var fldMod = $('#prlUtlEfcEdtFieldModifier');
		var fieldDefObj = fldName.substring(0,3)=='ex_' ? prlUtlEfc.auxFields : prlVlFieldDef;
		if(fieldDefObj[fldName].rgx != '') {
			if( fldName=='ex_activeempl_daterange' ) {
				//exception: bei Datumsbereich
				var pfx = '#prlUtlEfcEdtDate';
				var bcl = 'background-color';
				if(!fieldDefObj[fldName].rgx.test($(pfx+'From').val())) {
					$(pfx+'From').css(bcl,'#f88'); return;
				}else $(pfx+'From').css(bcl,'');
				if(!fieldDefObj[fldName].rgx.test($(pfx+'To').val())) {
					$(pfx+'To').css(bcl,'#f88'); return;
				}else $(pfx+'To').css(bcl,'');
			}else if( fldMod.is(':visible') && fldMod.val()>0 ) {
				//exception: wenn FieldModifier visible und val>0, dann muss der Regex-Check für den Datumswert durch einen Integer-Check übersteuert werden!
				var rgxpat = fldMod.val()==3 ? /^[0-9]{4,4}$/ : /^[0-9]{1,2}$/;
				if(!rgxpat.test(testValue)) {
					tObj.css('background-color','#f88');
					return;
				}else tObj.css('background-color','');
			}else{
				if(!fieldDefObj[fldName].rgx.test(testValue)) {
					tObj.css('background-color','#f88');
					return;
				}else tObj.css('background-color','');
			}
		}
		if(fieldDefObj[fldName].type == 1 && fieldDefObj[fldName].len > 0) {
			if(testValue.length > fieldDefObj[fldName].len) {
				tObj.css('background-color','#f88');
				return;
			}else tObj.css('background-color','');
		}
		if(fieldDefObj[fldName].type == 3 && (fieldDefObj[fldName].vMin + fieldDefObj[fldName].vMax) != 0.0) {
			if(testValue<fieldDefObj[fldName].vMin || testValue>fieldDefObj[fldName].vMax) {
				tObj.css('background-color','#f88');
				return;
			}else tObj.css('background-color','');
		}
		if(testValue=='') {
			tObj.css('background-color','#f88');
			return;
		}else tObj.css('background-color','');
	}
	//daten aus eingabefelder in übersicht laden
	prlUtlEfcLoadData();
	//eingabefelder deaktivieren
	prlUtlEfcEditCancel();
	$('#prlUtlEfcEdtComparativeValues').val('');
}

function prlUtlEfcEditData(liObj) {
	$.each(['CriteriaType','FieldName','FieldModifier','Conjunction','Comparison','ComparativeValues'], function() {
		$('#prlUtlEfcEdt'+this).removeAttr('disabled');
		$('#prlUtlEfcEdt'+this).val( liObj.attr(this.toString()) );
	});
	$.each(['BtnSet', 'BtnCancel', 'Checkbox', 'Select'], function() { $('#prlUtlEfcEdt'+this).removeAttr('disabled'); });
	prlUtlEfcToggleEdtFields();
	var cv = $('#prlUtlEfcEdtComparativeValues').val();
	//falls es sich um ein checkbox- oder select-feld handelt, muss der wert aus ComparativeValues für das entsprechende element übernommen werden
	if($('#prlUtlEfcEdtSelect').is(":visible")) {
		$('#prlUtlEfcEdtSelect').val( cv );
	}
	if($('#prlUtlEfcEdtCheckbox').is(":visible")) {
		if(cv==1) $('#prlUtlEfcEdtCheckbox').attr('checked', true);
		else $('#prlUtlEfcEdtCheckbox').removeAttr('checked');
	}
	if($('#prlUtlEfcEdtDateFrom').is(":visible")) {
		var n=cv.split("-");
		if(n.length==2) {
			$('#prlUtlEfcEdtDateFrom').val( n[0] );
			$('#prlUtlEfcEdtDateTo').val( n[1] );
		}
	}

	$.each(['prlUtlEfcSave','prlUtlEfcCancel','prlUtlEfcNew'], function() { $('#'+this).attr('disabled',true); });

	$('#prlUtlEfcEdtBtnSet').attr('mode','edit').attr('lindex',liObj.index());
	$('.prlUtlEfcLstD ul').sortable('disable');
}

function prlUtlEfcEditCancel() {
	$.each(['prlUtlEfcSave','prlUtlEfcCancel','prlUtlEfcNew'], function() { $('#'+this).removeAttr('disabled'); });
	$('#prlUtlEfcEdtBtnSet').removeAttr('mode').removeAttr('lindex');
	$('.prlUtlEfcLstD ul').sortable('enable');
	$.each(['CriteriaType','FieldName','FieldModifier','Conjunction','Comparison','ComparativeValues', 'BtnSet', 'BtnCancel', 'Checkbox', 'Select','DateFrom','DateTo'], function() { $('#prlUtlEfcEdt'+this).attr('disabled',true); }); //.val('')
	$('#prlUtlEfcEdtComparativeValues').css('background-color','');
}

function prlUtlEfcNew() {
	$('#prlUtlEfcEdtBtnSet').attr('mode','add');
	$.each(['prlUtlEfcSave','prlUtlEfcCancel','prlUtlEfcNew'], function() { $('#'+this).attr('disabled',true); });
	$('.prlUtlEfcLstD ul').sortable('disable');
	$.each(['CriteriaType','FieldName','FieldModifier','Conjunction','Comparison','ComparativeValues', 'BtnSet', 'BtnCancel', 'Checkbox', 'Select','DateFrom','DateTo'], function() { $('#prlUtlEfcEdt'+this).val('').removeAttr('disabled'); });
	prlUtlEfcToggleEdtFields();
}

function prlUtlEfcLoadData() {
	var modeAdd = true;
	var dataArr = [];
	var initialLoading = !!$('#prlUtlEfcEdtBtnSet').attr('mode') ? false : true;
	if(!initialLoading && $('#prlUtlEfcEdtBtnSet').attr('mode')=='edit') modeAdd = false;

	if(initialLoading) {
		$(".prlUtlEfcLstD ul").empty();
		dataArr = prlUtlEfc.data;
		$.each(['FilterName','ValidForEmplOverview','ValidForCalculation','GlobalFilter','TemporaryFilter'], function() { var o = $('#prlUtlEfc_'+this); if(o.is(':checkbox')) o.attr('checked',(prlUtlEfc[this.toString()]==1?true:false)); else o.val( prlUtlEfc[this.toString()] ); });
	}else{
		switch($('#prlUtlEfcEdtCriteriaType').val()) {
		case '1':
			dataArr = [{'CriteriaType':'1', 'FieldName':$('#prlUtlEfcEdtFieldName').val(), 'FieldModifier':($('#prlUtlEfcEdtFieldModifier').is(":visible") ? $('#prlUtlEfcEdtFieldModifier').val() : 0), 'Conjunction':'0', 'Comparison':$('#prlUtlEfcEdtComparison').val(), 'ComparativeValues':$('#prlUtlEfcEdtComparativeValues').val()}];
			break;
		case '2':
			dataArr = [{'CriteriaType':'2', 'FieldName':'', 'FieldModifier':'0', 'Conjunction':$('#prlUtlEfcEdtConjunction').val(), 'Comparison':'0', 'ComparativeValues':''}];
			break;
		case '3':
		case '4':
			dataArr = [{'CriteriaType':$('#prlUtlEfcEdtCriteriaType').val(), 'FieldName':'', 'FieldModifier':'0', 'Conjunction':'0', 'Comparison':'0', 'ComparativeValues':''}];
			break;
		}
		if(modeAdd) dataArr[0].id = 0;
	}
	$.each(dataArr, function() {
		var txt = '';
		var comparativeVal = '';
		var fieldDefObj = this.FieldName.substring(0,3)=='ex_' ? prlUtlEfc.auxFields : prlVlFieldDef;
		switch(this.CriteriaType) {
		case '1':
			var l = fieldDefObj[this.FieldName].label;
			if(this.FieldModifier>0) txt = prlUtlEfc.labels.FieldModifier[this.FieldModifier]+'('+l+')';
			else txt = l;
			txt += ' '+prlUtlEfc.labels.Comparison[this.Comparison]+' ';

			switch(fieldDefObj[this.FieldName].type) {
			case 2: //checkbox
				comparativeVal = this.ComparativeValues==1 ? prlUtlEfc.labels.Checkbox[0] : prlUtlEfc.labels.Checkbox[1];
				break;
			case 4: //list
				comparativeVal = this.ComparativeValues;
				$.each(fieldDefObj[this.FieldName].options, function() {
					if(this[0]==comparativeVal) {
						comparativeVal = this[1];
						return false;
					}
				});
				break;
			default:
				comparativeVal = this.ComparativeValues;
				break;
			}

			if(this.Comparison<7) txt += comparativeVal;
			else txt += '('+comparativeVal+')';
			break;
		case '2':
			txt = '<b>'+prlUtlEfc.labels.Conjunction[this.Conjunction]+'</b>';
			break;
		case '3':
			txt = '<b>(</b>';
			break;
		case '4':
			txt = '<b>)</b>';
			break;
		}
		if(modeAdd) {
			$('.prlUtlEfcLstD ul').append('<li></li>')
			var o = $('.prlUtlEfcLstD ul li:last-child');
		}else{
			var liIndex = $('#prlUtlEfcEdtBtnSet').attr('lindex'); //liIndex bestimmt, in welches LI die Daten zurückgeschrieben werden
			var o = $('.prlUtlEfcLstD ul li').eq(liIndex);
		}
		o.html('<div>'+txt+'</div><div></div><div></div>')
		o.find('div').eq(1).bind('click', function() { if(!!$('#prlUtlEfcEdtBtnSet').attr('mode')) return; $(this).parent().remove(); });
		o.find('div').eq(2).bind('click', function() { if(!!$('#prlUtlEfcEdtBtnSet').attr('mode')) return; prlUtlEfcEditData($(this).parent()); });
		$.each(this, function(fldN, fldV) { o.attr(fldN, fldV); });
	});
	if(modeAdd) $('.prlUtlEfcLstD').animate({ scrollTop: $('.prlUtlEfcLstD')[0].scrollHeight}, 1000);
}

function prlUtlEfcSave() {
	var objFilterName = $('#prlUtlEfc_FilterName');
	var objValidEmplOv = $('#prlUtlEfc_ValidForEmplOverview');
	var objValidCalc = $('#prlUtlEfc_ValidForCalculation');
	var objGlobal = $('#prlUtlEfc_GlobalFilter');
	var objTemp = $('#prlUtlEfc_TemporaryFilter');
	if($.trim(objFilterName.val())=='') {
		objFilterName.css('background-color','#f88');
		return;
	}else objFilterName.css('background-color','');
	if(!objValidEmplOv.is(':checked') && !objValidCalc.is(':checked')) {
		$('.prlUtlEfcMain fieldset').eq(0).css('background-color','#f88');
		return;
	}else $('.prlUtlEfcMain fieldset').eq(0).css('background-color','');

	var scanAttr = ['id', 'CriteriaType', 'FieldName', 'FieldModifier', 'Conjunction', 'Comparison', 'ComparativeValues'];
	var retObj = {'FilterName':$('#prlUtlEfc_FilterName').val(), 'ValidForEmplOverview':(objValidEmplOv.is(':checked')?1:0), 'ValidForCalculation':(objValidCalc.is(':checked')?1:0), 'GlobalFilter':(objGlobal.is(':checked')?1:0), 'TemporaryFilter':(objTemp.is(':checked')?1:0), 'payroll_empl_filter_ID':prlUtlEfc.payroll_empl_filter_ID, 'data':[]}
	$('.prlUtlEfcLstD ul li').each(function() {
		var liObj = $(this);
		var rec = {};
		$.each(scanAttr, function() { rec[this.toString()] = liObj.attr(this.toString()); });
		rec.SortOrder = $(this).index();
		retObj.data.push(rec);
	});
	cb('payroll.EmployeeFilter',{'action':'save', 'data':retObj});
}

function prlUtlEfcInit() {
	$('#prlUtlEfcEdtCriteriaType, #prlUtlEfcEdtFieldName').bind('change keyup', function() {
		prlUtlEfcToggleEdtFields();
		$("#prlUtlEfcEdtComparison").val($("#prlUtlEfcEdtComparison option:first").val());
		var o = $('#prlUtlEfcEdtSelect');
		var p = $('#prlUtlEfcEdtComparativeValues');
		if(o.is(":visible")) p.val(o.find("option:first").val());
		else p.val('');
	});
	$('.prlUtlEfcLstD ul').sortable();
	$('.prlUtlEfcLstD ul').disableSelection();
	$('#prlUtlEfcEdtBtnSet').bind('click', function() { prlUtlEfcSaveItem(); });
	$('#prlUtlEfcSave').bind('click', function() { prlUtlEfcSave(); });
	$('#prlUtlEfcEdtBtnCancel').bind('click', function() { 
		prlUtlEfcEditCancel(); 
		$('#prlUtlEfcEdtComparativeValues').val('');
	});
	$('#prlUtlEfcCancel').bind('click', function() { $('#modalContainer').mb_close(); });
	$('#prlUtlEfcNew').bind('click', function() {
		prlUtlEfcNew();
		var o = $('#prlUtlEfcEdtSelect');
		if(o.is(":visible")) $('#prlUtlEfcEdtComparativeValues').val(o.find("option:first").val());
	});

	var o = $('#prlUtlEfcEdtFieldName');
	$.each(prlUtlEfc.auxFields, function(fldName,fldParam) { o.append( $('<option></option>').attr("value", fldName).text(fldParam.label) ); });
	$.each(prlVlFieldDef, function(fldName,fldParam) { if(fldParam.type<6) o.append( $('<option></option>').attr("value", fldName).text(fldParam.label) ); });

	$('#prlUtlEfcEdtSelect').bind('change keyup', function() { $('#prlUtlEfcEdtComparativeValues').val($(this).val()); });
	$('#prlUtlEfcEdtCheckbox').bind('click', function() { $('#prlUtlEfcEdtComparativeValues').val($(this).is(':checked')?1:0); });
	$('#prlUtlEfcEdtDateFrom,#prlUtlEfcEdtDateTo').bind('change keyup', function() { $('#prlUtlEfcEdtComparativeValues').val($('#prlUtlEfcEdtDateFrom').val()+'-'+$('#prlUtlEfcEdtDateTo').val()); });

	prlUtlEfcLoadData();
	prlUtlEfcToggleEdtFields();
	prlUtlEfcEditCancel();
}

/*
*** Configuration: field modifiers (overview)
*/
	var prlFldModDataView;
	var prlFldModColumnFilters = {};
	var prlFldModGrid;
	var prlFldModColumns = [];

	var prlFldModOptions = {
		showHeaderRow: true,
		enableCellNavigation: false,
		enableColumnReorder: false,
		explicitInitialization: true
	};

	var prlFldModData = [];

	function prlFldModFilter(item) {
		for(var columnId in prlFldModColumnFilters) {
			if(columnId !== undefined && prlFldModColumnFilters[columnId] !== "") {
				var c = prlFldModGrid.getColumns()[prlFldModGrid.getColumnIndex(columnId)];
				if(item[c.field].toString().toLowerCase().indexOf(prlFldModColumnFilters[columnId].toString().toLowerCase()) == -1) {
					return false;
				}
			}
		}
		return true;
	}

	function prlFldModSaveSettings() {
		var settings = {};
		settings['quickFilterEnabled'] = $("#prlFldModBtnQFilter").is(':checked');
		if(settings['quickFilterEnabled']) {
			settings['quickFilterValues'] = [];
			for(var columnId in prlFldModColumnFilters) {
				settings['quickFilterValues'].push({'colID': columnId, 'filterValue': prlFldModColumnFilters[columnId]});
			}
		}
		settings['columnsWidth'] = [];
		var cols = prlFldModGrid.getColumns();
		for(var i=0;i<cols.length;i++) settings['columnsWidth'].push(cols[i].width);
		settings['sort'] = prlFldModGrid.getSortColumns();
		cb('payroll.psoSaveSettings',settings);
	}

	function prlFldModSetSettings(param) {
		$('#prlFldModBtnQFilter').attr('checked', param.quickFilterEnabled);
		if(param.quickFilterEnabled) {
			for(var i=0;i<param.quickFilterValues.length;i++) {
				prlFldModColumnFilters[param.quickFilterValues[i].colID] = param.quickFilterValues[i].filterValue;
			}
		}
		var cols = prlFldModGrid.getColumns();
		for(var i=0;i<cols.length;i++) cols[i].width=param.columnsWidth[i];
		prlFldModGrid.setColumns(cols);

		if(param.sort.length>0) {
			prlFldModGrid.setSortColumn(param.sort[0].columnId, param.sort[0].sortAsc);
			prlFldModSortSingleColumn(param.sort[0].columnId, param.sort[0].sortAsc, false);
		}

		prlFldModToggleFilterRow();

		prlFldModGrid.setData(prlFldModDataView);
		prlFldModGrid.updateRowCount();
		prlFldModGrid.render();
	}

	function prlFldModToggleFilterRow() {
		if($("#prlFldModBtnQFilter").is(':checked')) {
			$(prlFldModGrid.getHeaderRow()).show();
			prlFldModGrid.showHeaderRow(true);
		}else{
			$(prlFldModGrid.getHeaderRow()).hide();
			prlFldModGrid.showHeaderRow(false);
			$(prlFldModGrid.getHeaderRow()).find("input").val('');
			for(var columnId in prlFldModColumnFilters) prlFldModColumnFilters[columnId] = "";
			prlFldModDataView.refresh();
		}
		prlFldModGrid.resizeCanvas();
	}

	function prlFldModSortSingleColumn(field, sortAsc, updateGrid) {
		prlFldModDataView.sort(function(a, b){
			var result = a[field] > b[field] ? 1 : a[field] < b[field] ? -1 : 0; 
			return sortAsc ? result : -result;
		});
		if(updateGrid==null) {
			prlFldModGrid.setData(prlFldModDataView);
			prlFldModGrid.updateRowCount();
			prlFldModGrid.render();
		}
	}

	function prlFldModInit() {
		prlFldModDataView = new Slick.Data.DataView();
		prlFldModGrid = new Slick.Grid("#prlFldModGrd", prlFldModDataView, prlFldModColumns, prlFldModOptions);
		prlFldModGrid.onSort.subscribe(function (e, args) {
			prlFldModSortSingleColumn(args.sortCol.field, args.sortAsc);
		});

		prlFldModDataView.onRowCountChanged.subscribe(function (e, args) {
			prlFldModGrid.updateRowCount();
			prlFldModGrid.render();
		});

		prlFldModDataView.onRowsChanged.subscribe(function (e, args) {
			prlFldModGrid.invalidateRows(args.rows);
			prlFldModGrid.render();
		});


		$(prlFldModGrid.getHeaderRow()).delegate(":input", "change keyup", function (e) {
			var columnId = $(this).data("columnId");
			if (columnId != null) {
				prlFldModColumnFilters[columnId] = $.trim($(this).val());
				prlFldModDataView.refresh();
			}
		});

		prlFldModGrid.onHeaderRowCellRendered.subscribe(function(e, args) {
			$(args.node).empty();
			$("<input type='text'>")
			   .data("columnId", args.column.id)
			   .val(prlFldModColumnFilters[args.column.id])
			   .appendTo(args.node);
		});

		prlFldModGrid.onClick.subscribe(function(e, args) {
			var cell = prlFldModGrid.getCellFromEvent(e), row = cell.row;
			var item = prlFldModDataView.getItem(row);
			$('#prlFldModGrdTblMenu').css('top',e.pageY-15).css('left',e.pageX-70).css('zIndex',9999).attr('rid', item.id).show();
		});

		prlFldModGrid.init();

		prlFldModDataView.beginUpdate();
		prlFldModDataView.setItems(prlFldModData);
		prlFldModDataView.setFilter(prlFldModFilter);
		prlFldModDataView.endUpdate();

		$('#prlFldModGrdTblMenu').menu().hide().css('width','100').css('position','fixed').bind('mouseleave', function(e) { $(this).hide(); });
		$('#prlFldModGrdTblMenu li a').bind('click', function(e) {
			var rid = $(this).parent().parent().attr('rid');
			switch($(this).attr('act')) {
			case '1':
				cb('payroll.ConfigEditFormOpen',{'section':'CfgFldMod','id':rid});
				break;
			case '2':
				cb('payroll.ConfigEditFormDelete',{'section':'CfgFldMod','id':rid});
				break;
			}
			$(this).parent().parent().hide();
			return false;
		});

		$( "#prlFldModBtnClose" ).click(function() { $('#modalContainer').mb_close(); });

		//Button: Modifikator hinzufügen
		$( "#prlFldModBtnNew" ).button({
			text: false,
			icons: {
				primary: "p-icon-add"
			}
		})
		.click(function() {
			cb('payroll.ConfigEditFormOpen',{'section':'CfgFldMod','id':'0'});
		});
// ----------------------------------------
		//Button: Tabellenfilter
		$( "#prlFldModBtnQFilter" ).button({
			text: false,
			icons: {
				primary: "p-icon-tblfilter"
			}
		})
		.click(function() {
			prlFldModToggleFilterRow();
		});
		$('label[for=prlFldModBtnQFilter]').addClass('toolbar-space-left');

		prlFldModToggleFilterRow();
	}

/*
*** Config: Field modifier
*/

function prlCfgFldModToggle() {
	var showValueInp = $('#prlFormCfg_TargetType0').is(':checked');
	var oTV = $('#prlFormCfg_TargetValue');
	var oTF = $('#prlFormCfg_FieldName');
	if(showValueInp) { oTV.show(); oTF.hide(); }else{ oTV.hide(); oTF.show(); }

	var showFieldInp = $('#prlFormCfg_FldModType').val()==9 ? false : true;
	var oFSF = $('.CfgFldMod fieldset').eq(0);
	var oFSL = $('.CfgFldMod fieldset').eq(1);
	if(showFieldInp) { oFSF.show(); oFSL.hide(); }else{ oFSF.hide(); oFSL.show(); }
}

function prlCfgFldModInit(param) {
	$('#prlFormCfg_FldModType').bind('change keyup', function() { prlCfgFldModToggle(); });
	$('#prlFormCfg_TargetType0,#prlFormCfg_TargetType1').bind('click', function() { prlCfgFldModToggle(); });

	var editMode = $('#prlFormCfg_id').val()==0 ? false : true;
	if(!editMode) {
		$('#prlFormCfg_FldModType').val(4);
		$.each(['TargetType0','major_period','major_period_bonus','minor_period'], function() { $('#prlFormCfg_' + this).prop('checked',true); });
	}
	//SELECT mit PS-Feldern füllen
	var se = $('#prlFormCfg_FieldName');
	se.find('option').remove();
	$.each(prlVlFieldDef, function(fldName,fldOptions) {
		switch(fldOptions.type) {
		case 3: //nummer
			se.append( $('<option></option>').attr("value", fldName).text(fldOptions.label) );
			break;
		case 5: //datum
			$.each(param.labels.dateFnc, function() {
				se.append( $('<option></option>').attr("value", fldName+this[0]).text(fldOptions.label+this[1]) );
			});
			break;
		}
	});
	//LOA-Autocomplete
	prlCalcDataLoaAC = [];
	$.each( prlCalcDataLOA, function() {
		prlCalcDataLoaAC.push({ 
			"label" : this[0] + ' - ' + this[1],
			"value" : this[0]
		});
	});
	$( "#prlFormCfg_payroll_account_ID" ).autocomplete({
		delay: 1000,
		source: prlCalcDataLoaAC
	});
//,
//		close: function() { $('#prlCalcDataLoaNo').change(); }

	prlCfgFldModToggle();
}
/*
*** Formula editor
*/

var prlLoaFe = {};

function prlLoaFeSave() {
	var testsPassed = true;
	var oBrkC = 0;
	var cBrkC = 0;
	var retObj = {'id':prlLoaFe.id, 'data':[]}
	$('.prlLoaFeLstD ul li').each(function() {
		var liObj = $(this);
		var v = liObj.attr('elmt');
		if(v=='(') oBrkC++;
		if(v==')') cBrkC++;
		if(v=='const') {
			v = $(this).find('input').val();
			if(!prlLoaFe.valuePattern.test(v)) {
				$('#prlLoaFeErr').text(prlLoaFe.labels.errValuePattern);
				testsPassed = false;
			}
		}
		retObj.data.push(v);
	});
	if(oBrkC != cBrkC) {
		$('#prlLoaFeErr').text(prlLoaFe.labels.errBracket);
		testsPassed = false;
	}
	if(testsPassed) {
		cb('payroll.FormulaEditor',{'action':'save', 'data':retObj});
		$('#prlLoaFeErr').text('');
	}
}

function prlLoaFeLoadData(dataArr) {
	if(dataArr==null) {
		$(".prlLoaFeLstD ul").empty();
		dataArr = prlLoaFe.data;
	}

	$.each(dataArr, function() {
		var elmt = this.toString();
		var txt = '';
		if(elmt=='const') elmt='0.00';

		if(prlLoaFe.valuePattern.test(elmt)) {
			txt = '<input value="'+elmt+'" />';
			elmt = 'const';
		}else{
			txt = prlLoaFe.labels[elmt];
		}

		$('.prlLoaFeLstD ul').append('<li></li>')
		var o = $('.prlLoaFeLstD ul li:last-child');
		o.html('<div>'+txt+'</div><div></div>')
		o.find('div').eq(1).bind('click', function() { $(this).parent().remove(); });
		o.attr('elmt',elmt);
	});
	$('.prlLoaFeLstD').animate({ scrollTop: $('.prlLoaFeLstD')[0].scrollHeight}, 1000);
}

function prlLoaFeRefreshSelect(dataArr) {
	var o = $('#loac_payroll_formula_ID');
	var curVal = o.val();
	o.find('option').remove();
	$.each(dataArr, function() {
		o.append( $('<option></option>').attr("value", this[0]).html(this[1]) );
	});
	o.val(curVal);
}

function prlLoaFeInit() {
	$('.prlLoaFeLstD ul').sortable();
	$('.prlLoaFeEdt button').bind('click', function() { prlLoaFeLoadData([$(this).attr('elmt')]); });
	$('#prlLoaFeSave').bind('click', function() { prlLoaFeSave(); });
	$('#prlLoaFeCancel').bind('click', function() { $('#modalContainer').mb_close(); });

	prlLoaFeLoadData();
}

/*
*** Payslip configuration
*/
var prlPslpCfg = {};

function prlPslpCfgSave() {
	var tArr = {};
	$("#modalContainer [id*='prlPslpCfg_']").each(function( index ) {
		var fldName = $(this).attr('id').substring(11);
		if($(this).is(":checkbox")) tArr[fldName] = $(this).is(':checked') ? 1 : 0;
		else tArr[fldName] = $(this).val();
	});
	tArr['InfoFields'] = [];
	$('.prlPslpCfgLstI li').each(function( index ) {
		var fld = $(this).find('select').eq(0);
		var ft = fld.find('option:selected').attr('field_type');
		var lb = $(this).find('input').val();
		var la = $(this).find('select').eq(1).val();
		tArr['InfoFields'].push( {'label':lb,'language':la,'field_type':ft,'field_name':(ft==0 ? fld.val() : '')} );
	});
	cb('payroll.payslipConfig',{'f':'takeAction', 'w':'save', 'data':tArr});
}

function prlPslpCfgAdd(p,d) {
	var objList = $('.prlPslpCfgLstI ul');
	if(d) objList.find('li').remove();

	$.each(p, function() {
		objList.append('<li></li>');
		var objLI = $('.prlPslpCfgLstI ul li:last-child');
		objLI.html('<div></div><input type="text"/> <select name="fld"></select> <select name="lng"></select>');

		objLI.find('input').val(this.label);

		var el = objLI.find('select').eq(0);
		$.each(prlPslpCfg.SystemInfoFields, function() {
			el.append( $('<option></option>').attr('value', this.val).attr('field_type', this.val).text(this.lbl).css('background-color','#ddf') );
		});
		$.each(prlVlFieldDef, function(k,v) {
			el.append( $('<option></option>').attr('value', k).attr('field_type', '0').text(v.label) );
		});
		if(this.field_type==0) el.val(this.field_name);
		else el.val(this.field_type);

		el = objLI.find('select').eq(1);
		$.each(prlPslpCfg.Languages, function() {
			el.append( $('<option></option>').attr("value", this).text(this) );
		});
		el.val(this.language);

		objLI.find('div').eq(0).bind('click', function() { $(this).parent().remove(); });
	});
}

function prlPslpCfgInit() {
	$("#prlPslpCfgSave").bind('click', function() { prlPslpCfgSave(); });
	$("#prlPslpCfgCancel").bind('click', function() { $('#modalContainer').mb_close(); });
	$("#prlPslpCfgAdd").bind('click', function() { prlPslpCfgAdd([{'label':'','language':'','field_type':1,'field_name':''}],false); });
	$('.prlPslpCfgLstI ul').sortable();
	$.each(prlPslpCfg.data, function(k,v) {
		var obj = $('#prlPslpCfg_'+k);
		if(obj.length != 0) {
			if(obj.is(":checkbox")) obj.attr('checked', (v==1 ? true : false));
			else obj.val(v);
		}
	});
	prlPslpCfgAdd(prlPslpCfg.data.InfoFields,true);
}

/*
*** Calculation: Period settings
*/


var prlPrdSet = {};

function prlPrdSetSave() {
	prlPrdSetMsgChange();
	var tArr = {};
	tArr['payroll_period_ID'] = prlPrdSet.payroll_period_ID;
	tArr['action'] = 'save';
	$("#prlPrdSetTbSettings [id*='prlPrdSet_']").each(function( index ) {
		var fldName = $(this).attr('id').substring(10);
		if($(this).is(":checkbox")) tArr[fldName] = $(this).is(':checked') ? 1 : 0;
		else tArr[fldName] = $(this).val();
	});
	tArr['messages'] = prlPrdSet.messages;
	cb('payroll.prlCalcOvSettings',tArr);
}

function prlPrdSetMsgChange() {
	var cl = prlPrdSet.lastCompanyID;
	var cn = $("#prlPrdSet_msg_comp").val();
	var ll = prlPrdSet.lastLanguage;
	var ln = $("#prlPrdSet_msg_language").val();
	//bei einem Wechsel, lesen wir zuerst die Werte in den Feldern aus und speichern sie im JSON-Obj
	var idx = 0;
	var touched = false;
	$.each(prlPrdSet.messages, function() {
		if(this.payroll_company_ID == cl && this.language == ll) { touched = true; return false; }
		idx++;
	});
	if(!touched) {
		//es gibt noch keinen Eintrag -> neuen Eintrag anlegen
		prlPrdSet.messages.push( {'payroll_company_ID':cl,'language':ll,'notification':$("#prlPrdSet_msg_txt").val()} );
	}else{
		//bestehenden Eintrag aktualisieren
		prlPrdSet.messages[idx].payroll_company_ID = cl;
		prlPrdSet.messages[idx].language = ll;
		prlPrdSet.messages[idx].notification = $("#prlPrdSet_msg_txt").val();
	}

	//...falls vorhanden, laden wir bestehende Daten in die Felder
	touched = false;
	$.each(prlPrdSet.messages, function() {
		if(this.payroll_company_ID == cn && this.language == ln) {
			$("#prlPrdSet_msg_comp").val(this.payroll_company_ID);
			$("#prlPrdSet_msg_language").val(this.language);
			$("#prlPrdSet_msg_txt").val(this.notification);
			touched = true;
		}
	});
	prlPrdSet.lastCompanyID = cn;
	prlPrdSet.lastLanguage = ln;
	if(!touched) $("#prlPrdSet_msg_txt").val('');
}

function prlPrdSetInit() {
	$('#prlPrdSetTabs').tabs();
	$("#prlPrdSetSave").bind('click', function() { prlPrdSetSave(); });
	$("#prlPrdSetCancel").bind('click', function() { $('#modalContainer').mb_close(); });
	$("#prlPrdSet_msg_comp, #prlPrdSet_msg_language").bind('change keyup', function() { prlPrdSetMsgChange(); });

	if(prlPrdSet.messages.length>0) {
		$("#prlPrdSet_msg_comp").val(prlPrdSet.messages[0].payroll_company_ID);
		$("#prlPrdSet_msg_language").val(prlPrdSet.messages[0].language);
		$("#prlPrdSet_msg_txt").val(prlPrdSet.messages[0].notification);
		prlPrdSet.lastCompanyID = prlPrdSet.messages[0].payroll_company_ID;
		prlPrdSet.lastLanguage = prlPrdSet.messages[0].language;
	}else{
		prlPrdSet.lastCompanyID = $("#prlPrdSet_msg_comp").val();
		prlPrdSet.lastLanguage = $("#prlPrdSet_msg_language").val();
	}
}

/*
*** Configuration: employee fields and lists
*/

var prlVlFldCfg = {};

function prlVlFldCfgDataFromJSON() {
	var n = $('.prlVlFldCfg fieldset').eq(2).find('input');
	if(prlVlFldCfg.enableLables) n.removeAttr('disabled'); else n.attr('disabled','disabled');
	var m = $('#prlVlFldCfg_active');
	if(prlVlFldCfg.enableActiveFlag) m.removeAttr('disabled'); else m.attr('disabled','disabled');
	$.each(prlVlFldCfg.toggleSettings, function(k,v) {
		var o = $('#prlVlFldCfg_'+k);
		var l = $('label[for=prlVlFldCfg_'+k+']');
		if(o.length!=0) if(v) o.show().next().filter('br').show();
		else o.hide().next().filter('br').hide();
		if(l.length!=0) if(v) l.show(); else l.hide();
	});

	if(prlVlFldCfg.toggleSettings.listID) {
		var o = $('#prlVlFldCfg_listID');
		o.find('option').not('[value=""]').remove();
		$.each(prlVlFldCfg.data.listItems, function(k,v) {
			o.append( $('<option></option>').attr("value", k).attr("token", v.ListItemToken).text( (prlVlFldCfg.data.fields.displayCode==1 ? v.ListItemToken+' - ' : '')+v.labels[prlVlFldCfg.userLanguage]) );
		});
		o.html($("option", o).sort(function(a, b) { 
			return a.text == b.text ? 0 : a.text < b.text ? -1 : 1 
		}));
	}

	$.each(prlVlFldCfg.data.fields, function(k,v) {
		var o = $('#prlVlFldCfg_'+k);
		if(o.length!=0) {
			if(o.is(':checkbox')) o.attr('checked',v==1?true:false);
			else o.val(v);
		}
	});
	$('#prlVlFldCfg_fieldName').text(prlVlFldCfg.fieldName);
	$('#prlVlFldCfg_fieldType').text(prlVlFldCfg.fieldType);
}

function prlVlFldCfgLoadItem(itemID) {
	if(itemID=='') return;
	$('#prlVlFldCfg_ListItemOrder').val(prlVlFldCfg.data.listItems[itemID]['ListItemOrder']);
	$('#prlVlFldCfg_ListItemToken').val(prlVlFldCfg.data.listItems[itemID]['ListItemToken']);
	$('input[id*=prlVlFldCfg_label_]').each(function(index) {
		var lngID = $(this).attr('id').substr(18);
		$(this).val(prlVlFldCfg.data.listItems[itemID]['labels'][lngID]);
	});
}

function prlVlFldCfgChangeItem(itemID) {
	var editMode = itemID=='' ? false : true;

	$('input[id*=prlVlFldCfg_]').each(function(index) { $(this).css('background-color',''); });
	var valid = true;
	var rgxpat = /^[_a-zA-Z0-9]{1,45}$/;
	if(!rgxpat.test($('#prlVlFldCfg_ListItemToken').val())) { $('#prlVlFldCfg_ListItemToken').css('background-color','#f88'); valid = false; }
	var rgxpat = /^1?[0-9]{1,2}$/;
	if(!rgxpat.test($('#prlVlFldCfg_ListItemOrder').val())) { $('#prlVlFldCfg_ListItemOrder').css('background-color','#f88'); valid = false; }
	$('input[id*=prlVlFldCfg_label_]').each(function(index) {
		if($.trim($(this).val())=='') {
			$(this).css('background-color','#f88');
			valid = false;
		}
	});
	if(!valid) return false;

	if(!editMode) {
		itemID = "new1";
		var counter = 1;
		while(prlVlFldCfg.data.listItems[itemID] !== undefined){
			counter++;
			itemID = "new"+counter;
		}
		prlVlFldCfg.data.listItems[itemID] = {'ListItemOrder':'', 'ListItemOrder':'', 'labels':{}};
	}
	prlVlFldCfg.data.listItems[itemID]['ListItemOrder'] = $('#prlVlFldCfg_ListItemOrder').val();
	prlVlFldCfg.data.listItems[itemID]['ListItemToken'] = $('#prlVlFldCfg_ListItemToken').val();

	$('input[id*=prlVlFldCfg_label_]').each(function(index) {
		var lngID = $(this).attr('id').substr(18);
		prlVlFldCfg.data.listItems[itemID]['labels'][lngID] = $(this).val();
	});

	cb('payroll.prlVlFieldCfg',{'action':'edit','loadData':'false'});
}

function prlVlFldCfgDataToJSON() {
	$.each(prlVlFldCfg.data.fields, function(k,v) {
		var o = $('#prlVlFldCfg_'+k);
		if(o.length!=0) {
			if(o.is(':checkbox')) prlVlFldCfg.data.fields[k] = o.is(':checked') ? 1 : 0;
			else prlVlFldCfg.data.fields[k] = o.val();
		}
	});
}

function prlVlFldCfgInit() {
	$("#prlVlFldCfgSave").bind('click', function() { prlVlFldCfgDataToJSON(); cb('payroll.prlVlFieldCfg', {'action':'save', 'data':prlVlFldCfg}); });
	$("#prlVlFldCfgCancel").bind('click', function() { $('#modalContainer').mb_close(); });
	$("#prlVlFldCfg_displayCode").bind('click', function() {
		prlVlFldCfgDataToJSON();
		prlVlFldCfgDataFromJSON();
	});
	$("#prlVlFldCfg_BtnAdd, #prlVlFldCfg_BtnEdit, #prlVlFldCfg_BtnDel").bind('click', function() {
		prlVlFldCfgDataToJSON();
		var curMode = $(this).attr('id').substr(15);
		var vl = $('#prlVlFldCfg_listID').val();
		if((curMode=='Edit' || curMode=='Del') && vl=='') return false;
		cb('payroll.prlVlFieldCfg', {'action':'ListItem'+curMode, 'ItemID':vl, 'ItemText':$('#prlVlFldCfg_listID option:selected').text()} );
	});
	prlVlFldCfgDataFromJSON();
}

/*
*** Payment Split
*/

var prlPmtSplt = {};

function prlPmtSpltMainInit() {
	$('#prlPmtSpltClose').bind('click', function(e) { $('#modalContainer').mb_close(); });
	//$('#prlPmtSpltNew').bind('click', function(e) { cb('payroll.paymentSplit',{'action':'paymentSplitAction_BankverbindungBearbeiten', 'empId':prlPmtSplt.empId, 'bankID':0}); });//'action':'paymentSplitAction_editSplit'
	$('#prlPmtSpltNew').bind('click', function(e) { cb('payroll.paymentSplit',{'action':'paymentSplitAction_initZahlungssplitt', 'empId':prlPmtSplt.empId, 'zahlstelle':0, 'bankID':0});   $('#modalContainer').mb_close();  });//'action':'paymentSplitAction_editSplit'
	$('#prlPmtSpltSaveOrder').bind('click', function(e) {
		var r = [];
		var c = 0;
		$('.prlPmtSpltScroll ul li').each(function(index) {
			r.push([$(this).attr('rid'), c++]);
		});
		cb('payroll.paymentSplit',{'action':'paymentSplitAction_saveSplitOrder', 'empId':prlPmtSplt.empId, 'data':r});
	});
	$('.prlPmtSpltScroll li div span').bind('click', function(e) {
		var a = $(this).attr('class')=='d' ? 'paymentSplitAction_deleteSplit' : 'paymentSplitAction_BankverbindungBearbeiten'; //gibt e oder d  //'paymentSplitAction_editSplit'
		//alert("payroll.js    rid:"+$(this).parent().parent().attr('rid'));
		cb('payroll.paymentSplit',{'action':a, 'empId':prlPmtSplt.empId, 'bankID':0, 'splitID':$(this).parent().parent().attr('rid')});
	});
	$('.prlPmtSpltScroll ul').sortable();
}

function fieldsetZahlungssplittEditJSON2Form() {
	$.each(prlPmtSplt.editSplt, function(k, v) {
		var o = $('#prlPmtSplt_'+k);
		if(o.length > 0) {
			if(o.is(":checkbox")) o.prop("checked",(v==1?true:false));
			else o.val(v);
		}
	});
	$('#prlPmtSplt_split_mode').change();
}

function fieldsetZahlungssplittEditInit() {
	$('#prlPmtSplt_split_mode').bind('change keyup', function(e) {
		var f = ['#prlPmtSplt_amount','#prlPmtSplt_payroll_account_ID'];
		var v = $(this).val();
		if(v==1) {
			$(f[1]).show().prev().show().next().next().show();
			$(f[0]).hide().prev().hide().next().next().hide();
		}else{
			$(f[1]).hide().prev().hide().next().next().hide();
			$(f[0]).show().prev().show().next().next().show();
			$(f[0]).prev().text($(f[0]).prev().attr('smd'+v));
		}
	});
}

function prlBankDestSave() {
	var r = {};
	$('input[id^="prlPmtSplt_"], select[id^="prlPmtSplt_"]').each(function( index ) {
		var n = $(this).attr('id').substring(11);
		if($(this).is(":checkbox")) r[n] = $(this).is(':checked') ? 1 : 0;
		else r[n] = $(this).val();
	});
	cb('payroll.paymentSplit', {'action':'paymentSplitAction_saveBankDestination', 'empId':prlPmtSplt.empId, 'data':r});
}


function jsSaveBankDestinationUndSplit(empId) {
	var r = {};
	$('input[id^="prlPmtSplt_"], select[id^="prlPmtSplt_"]').each(function( index ) {
		var n = $(this).attr('id').substring(11);
		if($(this).is(":checkbox")) r[n] = $(this).is(':checked') ? 1 : 0;
		else r[n] = $(this).val();
	});
	cb('payroll.paymentSplit', {'action':'paymentSplitAction_saveBankDestinationUndSplit', 'empId':empId, 'data':r});
}

function prlPmtSpltEditInit() {
	$('#prlPmtSplt_split_mode').bind('change keyup', function(e) {
		var f = ['#prlPmtSplt_amount','#prlPmtSplt_payroll_account_ID'];
		var v = $(this).val();
		if(v==1) {
			$(f[1]).show().prev().show().next().next().show();
			$(f[0]).hide().prev().hide().next().next().hide();
		}else{
			$(f[1]).hide().prev().hide().next().next().hide();
			$(f[0]).show().prev().show().next().next().show();
			$(f[0]).prev().text($(f[0]).prev().attr('smd'+v));
		}
	});
	$('#prlPmtSplt_having_rounding').bind('click', function(e) {
		var o = $('#prlPmtSplt_round_param');
		if($(this).is(':checked')) o.removeAttr('disabled');
		else {
			o.attr('disabled', 'disabled');
			o.val('0.01');
		}
	});
//	$('#prlPmtSpltCancel').bind('click', function(e) {
//		cb('payroll.paymentSplit', {'empId':prlPmtSplt.empId});
//	});
//	$('#prlPmtSplt_BankSource_Cancel').bind('click', function(e) {
//		cb('payroll.paymentSplit', {'empId':prlPmtSplt.empId});
//	});
//	$('#prlPmtSplt_BankSourceEdit_btnCancel').bind('click', function(e) {
//		cb('payroll.paymentSplit', {'empId':prlPmtSplt.empId});
//	});
//	$('#prlPmtSplt_BtnBSEdit').bind('click', function(e) {
//		prlPmtSpltEditForm2JSON();
//		cb('payroll.paymentSplit', {'action':'GUI_bank_source_Overview', 'empId':prlPmtSplt.empId});
//	});
//	$('#prlPmtSplt_BtnBDEdit').bind('click', function(e) {
//		prlPmtSpltEditForm2JSON();
//		cb('payroll.paymentSplit', {'action':'paymentSplitAction_BankverbindungAuswaehlen', 'empId':prlPmtSplt.empId});
//	});
}

function prlPmtSpltEditJSON2Form() {
	$.each(prlPmtSplt.editSplt, function(k, v) {
		var o = $('#prlPmtSplt_'+k);
		if(o.length > 0) {
			if(o.is(":checkbox")) o.prop("checked",(v==1?true:false));
			else o.val(v);
		}
	});
	$('#prlPmtSplt_split_mode').change();
	var o = $('#prlPmtSplt_round_param');
	if($('#prlPmtSplt_having_rounding').is(':checked')) o.removeAttr('disabled');
	else {
		o.attr('disabled', 'disabled');
		o.val('0.01');
	}
}

function prlPmtSpltEditForm2JSON() {
	$('input[id^="prlPmtSplt_"], select[id^="prlPmtSplt_"]').each(function( index ) {
		if($(this).is(":checkbox")) prlPmtSplt.editSplt[$(this).attr('id').substring(11)] = $(this).is(':checked') ? 1 : 0;
		else prlPmtSplt.editSplt[$(this).attr('id').substring(11)] = $(this).val();
	});
}

function prl_BankSourceEdit_btnSave() {
	var r = {};
	$('input[id^="prlPmtSplt_"], select[id^="prlPmtSplt_"]').each(function( index ) {
		var n = $(this).attr('id').substring(11);
		if($(this).is(":checkbox")) r[n] = $(this).is(':checked') ? 1 : 0;
		else r[n] = $(this).val();
	});
	cb('payroll.paymentSplit', {'action':'GUI_bank_source_save', 'data':r});
}


