	
	var admGPBCdata 				= [];
	var admPluginVersions 			= [];
	var admPluginGroups				= [];
	var admCustomersPerPlugin		= [];
	var admPluginGroupsCustomer		= [];
	var admVersionTables			= [];
	var admVersionTablesByGroup 	= [];
	var admPluginsNewCustomer 		= [];
	var admAllCustomers				= [];
	var admCustomerDetail			= [];
	var admPluginsByCustomer 		= [];
	var admPluginsForNewCustomer 	= [];
	var admSelectedPlugins			= [];
	var admCountries 				= [];
	var admPayrollYear				= [];
	var admPayrollLanguages			= [];
	var admExistingCustomers		= [];
	
	function showCustomers() {
		
		var objWnd = $( '#showCustomersWindow' );
		
		if ( objWnd.length > 0 ) {
			objWnd.mb_bringToFront();
			cb('admin.showCustomers',{"wndStatus":1});
		}else{
			cb('admin.showCustomers',{"wndStatus":0});
		}
		
	}
	
	function showCustomer( customerID , customerDB ) {
		
		var objWnd = $( '#showCustomerWindow' );
		
		if ( objWnd.length > 0 ) {
			objWnd.mb_bringToFront();
			cb('admin.showCustomer',{"customerID":customerID,"customerDB":dbcustomerDBName,"wndStatus":1});
		}else{
			cb('admin.showCustomer',{"customerID":customerID,"customerDB":customerDB,"wndStatus":0});
		}
		
	}
	
	function loadCustomer( customerID , customerDB ) {
		
		//alert( customerDetail[0] );
		// anzeige fuer adress-zeile 1 - 3
		$('#customerAddress1Label').css( 'display' , 'block' );
		$('#customerAddress2Label').css( 'display' , 'block' );
		$('#customerAddress3Label').css( 'display' , 'block' );
		
		$('#customerID').html( customerDetail[0] );
		$('#customerDB').html( customerDetail[1] );
		$('#customerName').html( customerDetail[2] );
		
		if (customerDetail[3] == '') {
			$('#customerAddress1Label').css( 'display' , 'none' );
		} else {
			$('#customerAddress1').html( customerDetail[3] );
		}
		if (customerDetail[4] == '') {
			$('#customerAddress2Label').css( 'display' , 'none' );
		} else {
			$('#customerAddress2').html( customerDetail[4] );
		}
		if (customerDetail[5] == '') {
			$('#customerAddress3Label').css( 'display' , 'none' );
		} else {
			$('#customerAddress3').html( customerDetail[5] );
		}
		
		$('#customerStreet').html( customerDetail[6] );
		$('#customerPlz').html( customerDetail[7] );
		$('#customerPlace').html( customerDetail[8] );
		$('#customerCountry').html( customerDetail[9] );
		$('#customerPhone').html( customerDetail[10] );
		$('#customerEmail').html( customerDetail[11] );
		
		var oPC = $('#customerPlugins');
		$.each( admPluginsByCustomer, function( key, val ) {			
			//o.find('option').remove();
			oPC.append( $('<label for="customerPlugins_'+val['pluginID']+'" style="padding-left:20px;"></label>').html( '<span id="customerPlugins_'+val['pluginID']+'" class="spanLeft">&bull;&nbsp;'+val['pluginName']+'</span><span class="spanLeft">'+val['version']+'</span><br />' ) );
			
		});
		
	}
	
	function loadPluginsForNewCustomer() {
		
		var textPlugin 	= $('#textPluginNewCustomer').val();
		$('#showNewCustomerFormText').html( textPlugin );
		var oCF 		= $('#showNewCustomerForm');
		
		$.each( admPluginsNewCustomer, function( key, val ) {			
			//o.find('option').remove();
			oCF.append( $('<div class="break"></div>').html( '<div class="nonbreak" style="width: 20px;"><input type="checkbox" id="pluginCustomerDBName_'+val['pluginID']+'" name="pluginCustomerDBName" value="'+val['pluginID']+'" onclick="javascript:showPluginDataByCustomer( '+val['pluginID']+' , \''+val['pluginName']+'\' , '+val['versionID']+' );" /></div><div class="nonbreak">'+val['pluginName']+'</div>' ) );
			
		});
		
	}
	
	function showPluginDataByCustomer( pluginID , pluginName , versionID ) {
		
		var tmpCustomer = $( '#tmpCustomer' ).val();
		var pluginVal = $( '#pluginCustomerDBName_'+pluginID );
		
		if ( pluginVal.prop( 'checked' ) == true ) {
			
			//var objWnd = $( '#showPluginDataByCustomersWindow' );
			
			/*if ( objWnd.length > 0 ) {
				
				objWnd.mb_bringToFront();*/
				cb('admin.showPluginDataByCustomers', {'wndStatus':1,'tmpCustomer':tmpCustomer,'pluginID':pluginID,'pluginName':pluginName,'versionID':versionID} );
				
			/*} else {
				
				cb('admin.showPluginDataByCustomers', {'wndStatus':0,'tmpCustomer':tmpCustomer,'pluginID':pluginID,'pluginName':pluginName,'versionID':versionID} );
				
			}*/
			
		} else {
			
			$( '#pluginCustomerDBName_'+pluginID ).removeAttr( 'checked' );
			// daten löschen muss noch eingebunden werden
			
		}
		
	}
	
	function admShowNewCustomer() {
		
		var objWnd = $( '#showNewCustomerWindow' );
		objWnd.fadeIn();
		//alert( customerID+' '+customerName );
		if ( objWnd.length > 0 ) {
			objWnd.mb_bringToFront();
			cb('admin.showNewCustomer',{"wndStatus":1});
		}else{
			cb('admin.showNewCustomer',{"wndStatus":0});
		}
		
	}
	
	function loadCustomersForNewCustomer() {
		
		var textCustomer 	= $('#textCustomerNewCustomer').val();
		$('#showNewCustomerFormText').html( textCustomer );
		var oCF 			= $('#showNewCustomerForm');
		//alert( 'hallo patrick' );
		$.each( admAllCustomers, function( key, val ) {			
			//alert( val+' '+key);
			oCF.append( $('<div class="break"></div>').html( '<div class="nonbreak" style="width: 20px;"><input type="checkbox" name="pluginCustomerDBName" value="'+val['customerID']+'" /></div><div class="nonbreak">'+val['customerID']+'</div>' ) );
			
		});
		
		
	}
	
	
	function adminCancelNewCustomer() {
		
		cb('admin.cancelNewCustomer');
	
	}
	
	function adminShowErrorMessage( typ ) {
		
		$( '#divErrorMessage' ).css( 'display' , 'block' );
		
		switch ( typ ) {
			
			case 'customerExists':
				var errorMessage = $( '#errorMessageCustomerExists' ).val();
				$( '#divErrorMessage' ).html( errorMessage );
				break;
			
			case 'emptyFields':
				var errorMessage = $( '#errorMessageEmptyFields' ).val();
				$( '#divErrorMessage' ).html( errorMessage );
				break;
			
			case 'passwordNotSame':
				var errorMessage = $( '#errorMessagePassword' ).val();
				$( '#divErrorMessage' ).html( errorMessage );
				break;
			
			case 'emptyPlugins':
				var errorMessage = $( '#errorMessagePlugin' ).val();
				$( '#divErrorMessage' ).html( errorMessage );
				break;
			
		}
		
	}
	
	function adminNewCustomerFirstStep( customerExists ) {
		
		//$( '#divErrorMessage' ).css( 'display' , 'none' );
		var newCustomer 		= $( '#newCustomer' ).val();
		$( '#newCustomer' ).css( 'backgroundColor' , '#ffffff' );
		var newUser				= $( '#newUserId' ).val();
		$( '#newUserId' ).css( 'backgroundColor' , '#ffffff' );
		var newUserPassword1	= $( '#newUserPassword' ).val();
		$( '#newUserPassword' ).css( 'backgroundColor' , '#ffffff' );
		var newUserPassword2	= $( '#newUserPasswordRepeat' ).val();
		$( '#newUserPasswordRepeat' ).css( 'backgroundColor' , '#ffffff' );
		var newUserName			= $( '#newUserName' ).val();
		$( '#newUserName' ).css( 'backgroundColor' , '#ffffff' );
		var newUserEMail		= $( '#newUserEmail' ).val();
		$( '#newUserEmail' ).css( 'backgroundColor' , '#ffffff' );
		
		var existingCustomerSel	= $( '#customersForExistingUsers' ).val();
		
		if ( customerExists == 1 ) {
			
			adminShowErrorMessage( 'customerExists' );
			$( '#newCustomer' ).css( 'backgroundColor' , '#ffacac' );
			
		} else if ( newUserPassword1 == '' || newCustomer == '' || newUser == '' || newUserName == '' || newUserEMail == '' ) {
			
			adminShowErrorMessage( 'emptyFields' );
			if ( newCustomer == '' ) {
				$( '#newCustomer' ).css( 'backgroundColor' , '#ffacac' );
			}
			if ( newUserPassword1 == '' ) {
				$( '#newUserPassword' ).css( 'backgroundColor' , '#ffacac' );
			}
			if ( newUserPassword2 == '' ) {
				$( '#newUserPasswordRepeat' ).css( 'backgroundColor' , '#ffacac' );
			}
			if ( newUser == '' ) {
				$( '#newUserId' ).css( 'backgroundColor' , '#ffacac' );
			}
			if ( newUserName == '' ) {
				$( '#newUserName' ).css( 'backgroundColor' , '#ffacac' );
			}
			if ( newUserEMail == '' ) {
				$( '#newUserEmail' ).css( 'backgroundColor' , '#ffacac' );
			}
			return false;
			
		} else if ( newUserPassword1 != newUserPassword2 ) {
			
			$( '#newUserPassword' ).css( 'backgroundColor' , '#ffacac' );
			$( '#newUserPasswordRepeat' ).css( 'backgroundColor' , '#ffacac' );
			adminShowErrorMessage( 'passwordNotSame' );
			return false;
		
		} else {
			
			cb('admin.newCustomerFirstStep', {'customer':newCustomer,'userID':newUser,'userPassword':newUserPassword1,'userName':newUserName,'userMail':newUserEMail,'existingCustomerSel':existingCustomerSel } );
			
		}
		
	}
	
	function adminLoadNewCustomerFirstStep() {
		
		$('#admCustomerBack').css( 'display' , 'block' );
		$('#admCustomerCancel').css( 'display' , 'block' );
		$('#admCustomerContinue').css( 'display' , 'block' );
		
		$( '#newCustomerFirstStep' ).css('display' , 'block' );
		$( '#newCustomerSecondStep' ).css('display' , 'none' );
		$( '#newCustomerThirdStep' ).css('display' , 'none' );
		cb('admin.loadNewCustomerFirstStep');
		
	}
	
	function adminShowNewCustomerFirstStep( newCustomer , customerDB , userID , userPassword , userName , userMail ) {
	//alert( newCustomer+' , '+customerDB+' , '+userID+' , '+userPassword+' , '+userName+' , '+userMail );
		$( '#divErrorMessage' ).css( 'display' , 'none' );
		$( '#newCustomer' ).val( newCustomer );
		$( '#newCustomer' ).focus();
		$( '#newUserId' ).val( userID );
		$( '#newUserPassword' ).val( userPassword );
		$( '#newUserPasswordRepeat' ).val( userPassword );
		$( '#newUserName' ).val( userName );
		$( '#newUserEmail' ).val( userMail );
		
		var oEC = $('#customersForExistingUsers');
		oEC.html( '' );
		//alert( admCountries );
		$.each( admExistingCustomers, function( key, val ) {
			//alert( val['selected']+' , '+val['countryID'] );
			if ( val['selected'] == 1 ) {
				
				oEC.append( $('<option></option>').val(val['customerDB']).text(val['customerName']).attr('selected',true) );
				
			} else {
				
				oEC.append( $('<option></option>').val(val['customerDB']).text(val['customerName']) );
				
			}
			
		});
		
		$( '#admCustomerBack' ).attr('disabled', true );
		$( '#admCustomerContinue' ).attr( 'onClick' , 'adminNewCustomerFirstStep( 0 )' );
		
	}
	
	function adminLoadNewCustomerSecondStep() {
		
		cb('admin.loadNewCustomerSecondStep');
		
	}
	
	function adminNewCustomerSecondStep() {
		
		var customerName 		= $( '#newCustomerName' ).val();
		$( '#newCustomerName' ).css( 'backgroundColor' , '#ffffff' );
		var customerAddress1	= $( '#newCustomerAddress1' ).val();
		var customerAddress2	= $( '#newCustomerAddress2' ).val();
		var customerAddress3	= $( '#newCustomerAddress3' ).val();
		var customerStreet		= $( '#newCustomerStreet' ).val();
		$( '#newCustomerStreet' ).css( 'backgroundColor' , '#ffffff' );
		var customerPLZ			= $( '#newCustomerPlz' ).val();
		$( '#newCustomerPlz' ).css( 'backgroundColor' , '#ffffff' );
		var customerPlace		= $( '#newCustomerPlace' ).val();
		$( '#newCustomerPlace' ).css( 'backgroundColor' , '#ffffff' );
		var customerCountry		= $( '#newCustomerCountry' ).val();
		var customerPhone		= $( '#newCustomerPhone' ).val();
		$( '#newCustomerPhone' ).css( 'backgroundColor' , '#ffffff' );
		var customerEMail		= $( '#newCustomerEmail' ).val();
		$( '#newCustomerEmail' ).css( 'backgroundColor' , '#ffffff' );
		
		if ( customerName == '' || customerStreet == '' || customerPLZ == '' || customerPlace == '' || customerPhone == '' || customerEMail == '' ) {
			
			if ( customerName == '' ) {
				$( '#newCustomerName' ).css( 'backgroundColor' , '#ffacac' );
			}
			if ( customerStreet == '' ) {
				$( '#newCustomerStreet' ).css( 'backgroundColor' , '#ffacac' );
			}
			if ( customerPLZ == '' ) {
				$( '#newCustomerPlz' ).css( 'backgroundColor' , '#ffacac' );
			}
			if ( customerPlace == '' ) {
				$( '#newCustomerPlace' ).css( 'backgroundColor' , '#ffacac' );
			}
			if ( customerPhone == '' ) {
				$( '#newCustomerPhone' ).css( 'backgroundColor' , '#ffacac' );
			}
			if ( customerEMail == '' ) {
				$( '#newCustomerEmail' ).css( 'backgroundColor' , '#ffacac' );
			}
			
			adminShowErrorMessage( 'emptyFields' );
			return false;
			
		} else {
		
			cb('admin.newCustomerSecondStep', {'customerName':customerName,'customerAddress1':customerAddress1,'customerAddress2':customerAddress2,'customerAddress3':customerAddress3,'customerStreet':customerStreet,'customerPLZ':customerPLZ,'customerPlace':customerPlace,'customerCountry':customerCountry,'customerPhone':customerPhone,'customerEMail':customerEMail} );
		
		}
		
	}
	
	function adminShowNewCustomerSecondStep( customerDB , customerName ,  customerAddress1 , customerAddress2 , customerAddress3 , customerStreet , customerPLZ , customerPlace , customerCountry , customerPhone , customerEMail ) {
		
		$( '#divErrorMessage' ).css( 'display' , 'none' );
		$( '#newCustomerFirstStep' ).css('display' , 'none' );
		$( '#newCustomerSecondStep' ).css('display' , 'block' );
		$( '#newCustomerThirdStep' ).css('display' , 'none' );
		
		$( '#newCustomerDB' ).val( customerDB );
		$( '#newCustomerName' ).val( customerName );
		$( '#newCustomerName' ).focus();
		$( '#newCustomerAddress1' ).val( customerAddress1 );
		$( '#newCustomerAddress2' ).val( customerAddress2 );
		$( '#newCustomerAddress3' ).val( customerAddress3 );
		$( '#newCustomerStreet' ).val( customerStreet );
		$( '#newCustomerPlz' ).val( customerPLZ );
		$( '#newCustomerPlace' ).val( customerPlace );
		$( '#newCustomerCountry' ).val( customerCountry );
		$( '#newCustomerPhone' ).val( customerPhone );
		$( '#newCustomerEmail' ).val( customerEMail );
		
		var oC 	= $('#newCustomerCountry');
		oC.html( '' );
		//alert( admCountries );
		$.each( admCountries, function( key, val ) {
			//alert( val['selected']+' , '+val['countryID'] );
			if ( val['selected'] == 1 ) {
				
				oC.append( $('<option></option>').val(val['countryID']).text(val['countryName']).attr('selected',true) );
				
			} else {
				
				oC.append( $('<option></option>').val(val['countryID']).text(val['countryName']) );
				
			}
			
		});
		
		$( '#admCustomerBack' ).removeAttr('disabled');
		
		$('#admCustomerBack').attr( 'onClick' , 'adminLoadNewCustomerFirstStep()' );
		$('#admCustomerContinue').attr( 'onClick' , 'adminNewCustomerSecondStep()' );
		
	}
	
	function adminLoadNewCustomerThirdStep() {
		
		cb('admin.loadNewCustomerThirdStep');
		
	}
	
	function adminNewCustomerThirdStep() {
		
		var customerPlugins = '';
		var i = 0;
		
		$.each( admPluginsForNewCustomer, function( key, val ) {
			
			if ( $( '#pluginNewCustomer_'+key ).is(':checked') ) {
				
				if ( i > 0 ) {
					customerPlugins += '@@';
				}
				
				customerPlugins += $( '#pluginNewCustomer_'+key ).val();
				i++;
				
			}
			
		});
		
		if ( customerPlugins == '' ) {
			
			adminShowErrorMessage( 'emptyPlugins' );
			return false;
			
		} else {
		//alert( 'JS customerPlugins: '+customerPlugins );
			cb('admin.newCustomerThirdStep', {'customerPlugins':customerPlugins} );
			
		}
		
	}
	
	function adminShowNewCustomerThirdStep() {
		
		$( '#divErrorMessage' ).css( 'display' , 'none' );
		var oP 	= $('#pluginsForNewCustomer');
		oP.html( '' );
		
		$.each( admPluginsForNewCustomer, function( key, val ) {
			
			if ( val['mandatory'] != 1 ) {
				
				if ( val['checked'] == 1 ) {
				
					checkbox = '<input type="checkbox" id="pluginNewCustomer_'+key+'" value="'+val['pluginName']+'##'+val['pluginID']+'" checked="checked" />';
					
				} else {
					
					checkbox = '<input type="checkbox" id="pluginNewCustomer_'+key+'" value="'+val['pluginName']+'##'+val['pluginID']+'" />';
					
				}
				
				oP.append( $('<div class="break"></div>').html( '<div class="nonbreak" style="width: 20px;">'+checkbox+'</div><div class="nonbreak">'+val['pluginName']+'</div>' ) );
				
			} /*else {
				
				checkbox = '<input type="checkbox" id="pluginNewCustomer_'+i+'" value="'+val['pluginName']+':'+val['pluginID']+'" checked="checked" disabled="disabled" />';
				
			}*/
			
		});
		

		$( '#newCustomerSecondStep' ).css('display' , 'none' );
		$( '#newCustomerThirdStep' ).css('display' , 'block' );
		$( '#newCustomerFourthStep' ).css('display' , 'none' );
		
		$('#admCustomerBack').attr( 'onClick' , 'adminLoadNewCustomerSecondStep()' );
		$('#admCustomerContinue').attr( 'onClick' , 'adminNewCustomerThirdStep()' );	
		
	}
	
	function adminLoadNewCustomerFourthStep() {
		
		cb('admin.loadNewCustomerFourthStep');
		
	}
	
	function adminNewCustomerFourthStep( number , pluginID , pluginName) {
		
		var pluginData 		= $('#customersPerPlugin').val();
		var pluginGroups	= '';
		
		if ( admPluginGroups.length > 0 ) {
			
			$.each( admPluginGroups, function( key, val ) {
		//alert( 'PluginDate: '+pluginData+' JS Key admPluginGroups: '+$( '#customersPerPluginGroup_'+val['groupID'] ).val() );
				if ( key > 0 ) {
					
					pluginGroups += '@@';
					
				}
				
				pluginGroups += val['groupID']+'##'+$( '#customersPerPluginGroup_'+val['groupID'] ).val();
				
			});
			
		}
		//alert( pluginName );
		switch ( pluginName ) {
			
			case 'payroll':
				
				var month 	= $('#payrollMonth').val();
				var year 	= $('#payrollYear').val();
				var values 	= month+'@@'+year;
				
				var l = 0;
				var payrollLanguageSelected = '';
				
				$.each( admPayrollLanguages, function( key, val ) {
					//alert( key+' '+val['wert'] );
					if ( $( '#admLanguage_'+key ).is(':checked') ) {
				
						if ( l > 0 ) {
							payrollLanguageSelected += '@@';
						}
						
						payrollLanguageSelected += $( '#admLanguage_'+key ).val();
						l++;
						
					}
					
				});
				
				var standardLanguage = $( '#standardLanguage' ).val();
				values += '##'+payrollLanguageSelected+'##'+standardLanguage; 
				break;
			
			default:
				var values = '';
			
		}
		
		cb('admin.newCustomerFourthStep', {'number':number,'pluginID':pluginID,'pluginName':pluginName,'pluginData':pluginData,'pluginGroups':pluginGroups,'values':values} );
		
	}
	
	function adminShowNewCustomerFourthStep( number , pluginID , pluginName , pluginVersion ) {
		//alert(number+' '+pluginName);
		$( '#divErrorMessage' ).css( 'display' , 'none' );
		$( '#newCustomerThirdStep' ).css('display' , 'none' );
		$( '#newCustomerFourthStep' ).css('display' , 'block' );
		$( '#newCustomerFifthStep' ).css('display' , 'none' );
		var buttonText = $( '#buttonTextContinue' ).val();
		$('#titleNewCustomerFourthStep').html( '"'+pluginName+'"' );
		
		var objSpecialFieldsOpen = $( '#pluginSpecialFieldsOpen' ).val();
		if ( objSpecialFieldsOpen != '' ) {
			$( '#specialFieldsPlugin_'+objSpecialFieldsOpen ).css( 'display' , 'none' );
		}
		
		var objSpecialFieldsPlugin = $( '#specialFieldsPlugin_'+pluginName );
		if ( objSpecialFieldsPlugin.length > 0 ) {
			$( '#specialFieldsPlugin_'+pluginName ).css( 'display' , 'block' );
			$( '#pluginSpecialFieldsOpen' ).val( pluginName );
		}
			
		switch ( pluginName ) {
			
			case 'payroll':
		
				var OPM = $( '#payrollMonth' );
				OPM.html('');
				
				var admMonths = [ $( '#january' ).val() , $( '#february' ).val() , $( '#march' ).val() , $( '#april' ).val() , $( '#may' ).val() , $( '#june' ).val() , $( '#july' ).val() , $( '#august' ).val() , $( '#september' ).val() , $( '#october' ).val() , $( '#november' ).val() , $( '#december' ).val()];
				var m = 1;
				
				$.each( admMonths, function( key, val ) {
					//alert( key+' '+val+' '+val['customerID'] );
					if ( admPayrollMonth == m ) {
						
						OPM.append( $('<option></option>').attr("value", (key+1)).text(val).attr('selected' , 'selected' ) );
						
					} else {
						
						OPM.append( $('<option></option>').attr("value", (key+1)).text(val) );
						
					}
					
					m++;
						
				});
				
				var oPY = $('#payrollYear');
				oPY.html('');
				$.each( admPayrollYear, function( key, val ) {
					//alert( key+' '+val+' '+val['customerID'] );
					if ( val['checked'] == 1 ) {
						
						oPY.append( $('<option></option>').attr("value", val['year']).text(val['year']).attr('selected' , 'selected' ) );
						
					} else {
						
						oPY.append( $('<option></option>').attr("value", val['year']).text(val['year']) );
						
					}
						
				});
				
				var oPL = $('#payrollLanguages');
				oPL.html('');
				
				$.each( admPayrollLanguages, function( key, val ) {
					//alert( key+' '+val['wert'] );
					if ( val['checked'] == 1 ) {
						
						checkbox = '<input type="checkbox" id="admLanguage_'+key+'" value="'+val['wert']+'" checked="checked" />';
						
					} else {
						
						checkbox = '<input type="checkbox" id="admLanguage_'+key+'" value="'+val['wert']+'" />';
						
					}
					
					if ( val['standard'] == 1 ) {
						
						radio = '<input type="radio" value="'+val['wert']+'" name="standardLanguage" id="standardLanguage" checked="checked" />';
						
					} else {
						
						radio = '<input type="radio" value="'+val['wert']+'" name="standardLanguage" id="standardLanguage" />';
						
					}
					
					oPL.append( $('<div class="break"></div>').html( '<div class="nonbreak" style="width:20px;">'+checkbox+'</div><div class="nonbreak" style="width:50px;">'+val['text']+'</div><div class="nonbreak" style="width:20px;">'+radio+'</div><div class="nonbreak">(standard)</div>' ) );
						
				});
				
				break;
			
		}
		
		var oCP 	= $('#customersPerPlugin');
		oCP.html( '' );
		var i 	= 1;
		
		$.each( admCustomersPerPlugin, function( key, val ) {
			
			if ( val['checked'] == 1 ) {
				
				oCP.append( $('<option></option>').val( val['customerDB']+'##'+val['version'] ).text( val['customerName'] ).attr('selected' , 'selected' ) );
				
			} else {
				
				oCP.append( $('<option></option>').val( val['customerDB']+'##'+val['version'] ).text( val['customerName'] ) );
				
			}
			
		});
		
		var oPG 	= $('#pluginGroups');
		oPG.html( '' );
		var i 	= 1;
		
		if ( admPluginGroups.length == 0 ) {
			
			var textNoGroups = $( '#textNoGroups' ).val();
			oPG.append( $('<div class="break"></div>').html( textNoGroups ) );
			
		} else {
		
			$.each( admPluginGroups, function( key, val ) {			
				
				oPG.append( $('<div class="break"></div>').html( '<div class="nonbreak" style="width: 250px;">'+val['groupName']+'</div><div class="nonbreak" style="width: 200px;"><select id="customersPerPluginGroup_'+val['groupID']+'"></select></div>' ) );
				var selectedDB 	= val['selectedDB'];
				var oCPS 		= $('#customersPerPluginGroup_'+val['groupID']);
				oCPS.html( '' );
				var i 	= 1;
				var textLikeUp = $( '#textLikeUp' ).val();
				// option werte fuer "wie oben" eintrag
				oCPS.append( $('<option></option>').val( 0 ).text( textLikeUp ).attr('selected' , 'selected' ) );
				
				$.each( admCustomersPerPlugin, function( key, val ) {
					
					if (  selectedDB == val['customerDB'] ) {
						
						oCPS.append( $('<option></option>').val( val['customerDB']+'##'+val['version'] ).text( val['customerName'] ).attr('selected' , 'selected' ) );
						
					} else {
						
						oCPS.append( $('<option></option>').val( val['customerDB']+'##'+val['version'] ).text( val['customerName'] ) );
						
					}
						
				});
				
			});
		
		}
		
		$('#admCustomerBack').attr( 'onClick' , 'adminLoadNewCustomerThirdStep()' );
		$('#admCustomerContinue').text( buttonText );
		$('#admCustomerContinue').attr( 'onClick' , 'adminNewCustomerFourthStep( '+number+' , '+pluginID+' , \''+pluginName+'\' )' );
		
	}
	
	function adminShowNewCustomerFifthStep() {
		
		$( '#newCustomerThirdStep' ).css('display' , 'none' );
		$( '#newCustomerFourthStep' ).css('display' , 'none' );
		$( '#newCustomerFifthStep' ).css('display' , 'block' );
		$( '#newCustomerAddress1Label' ).css( 'display' , 'block' );
		$( '#newCustomerAddress2Label' ).css( 'display' , 'block' );
		$( '#newCustomerAddress3Label' ).css( 'display' , 'block' );
		var buttonText = $( '#buttonTextCreate' ).val();
		
		$( '#newCustomerDisplay' ).html( $( '#newCustomer' ).val() );
		$( '#newCustomerDBDisplay' ).html( $( '#newCustomerDB' ).val() );
		$( '#newUserIdDisplay' ).html( $( '#newUserId' ).val() );
		var newPassword 		= $( '#newUserPassword' ).val();
		var newPasswordValue 	= '';
		for( p=0; p<newPassword.length; p++ ) {
			newPasswordValue += '*';
		}
		$( '#newUserPasswordDisplay' ).html( newPasswordValue );
		$( '#newUserNameDisplay' ).html( $( '#newUserName' ).val() );
		$( '#newUserEmailDisplay' ).html( $( '#newUserEmail' ).val() );
		
		$( '#newCustomerNameDisplay' ).html( $( '#newCustomerName' ).val() );
		if ( $( '#newCustomerAddress1' ).val() == '' ) {
			$( '#newCustomerAddress1Label' ).css( 'display' , 'none' );
		} else {
			$( '#newCustomerAddress1Display' ).html( $( '#newCustomerAddress1' ).val() );
		}
		if ( $( '#newCustomerAddress2' ).val() == '' ) {
			$( '#newCustomerAddress2Label' ).css( 'display' , 'none' );
		} else {
			$( '#newCustomerAddress2Display' ).html( $( '#newCustomerAddress2' ).val() );
		}
		if ( $( '#newCustomerAddress3' ).val() == '' ) {
			$( '#newCustomerAddress3Label' ).css( 'display' , 'none' );
		} else {
			$( '#newCustomerAddress3Display' ).html( $( '#newCustomerAddress3' ).val() );
		}
		$( '#newCustomerStreetDisplay' ).html( $( '#newCustomerStreet' ).val() );
		$( '#newCustomerPlzDisplay' ).html( $( '#newCustomerPlz' ).val() );
		$( '#newCustomerPlaceDisplay' ).html( $( '#newCustomerPlace' ).val() );
		$( '#newCustomerCountryDisplay' ).html( $( '#newCustomerCountry' ).val() );
		$( '#newCustomerPhoneDisplay' ).html( $( '#newCustomerPhone' ).val() );
		$( '#newCustomerEmailDisplay' ).html( $( '#newCustomerEmail' ).val() );
		
		var oPG 	= $('#selectedPlugins');
		oPG.html( '' );
		$.each( admSelectedPlugins, function( key, val ) {			
			
			if ( val['pluginImg'] == 'red' ) {
				
				var pluginImg = '<a href="javascript:showPluginForNewCustomerDetail( '+val['pluginID']+');"><img src="/plugins/admin_V00_00_01/code_ui/media/icons/red.png" stlye="border:0" /></a>';
				
			} else if ( val['pluginImg'] == 'yellow' ) {
				
				var pluginImg = '<a href="javascript:showPluginForNewCustomerDetail( '+val['pluginID']+');"><img src="/plugins/admin_V00_00_01/code_ui/media/icons/yellow.png" stlye="border:0" /></a>';
				
			} else {
				
				var pluginImg = '<img src="/plugins/admin_V00_00_01/code_ui/media/icons/green.png" stlye="border:0" />';
				
			}
			
			oPG.append( $('<div class="break"></div>').html( '<div class="nonbreak" style="width: 30px;">'+pluginImg+'</div><div class="nonbreak" style="width: 170px;">'+val['pluginName']+'</div><div id="nonbreak">'+val['pluginID']+'</div>' ) );
			
		});
		
		$('#admCustomerBack').attr( 'onClick' , 'adminLoadNewCustomerFourthStep()' );
		$('#admCustomerContinue').text( buttonText );
		$('#admCustomerContinue').attr( 'onClick' , 'adminNewCustomerCreate()' );
		
	}
	
	function showPluginForNewCustomerDetail( pluginID ) {
		
		alert( pluginID );
		
	}
	
	function adminNewCustomerCreate() {
		
		$( '#newCustomerFourthStep' ).css('display' , 'none' );
		$( '#newCustomerFifthStep' ).css('display' , 'none' );
		$( '#newCustomerSixthStep' ).css('display' , 'block' );
		
		$('#admCustomerBack').css( 'display' , 'none' );
		$('#admCustomerCancel').css( 'display' , 'none' );
		$('#admCustomerContinue').css( 'display' , 'none' );
		
		cb('admin.newCustomerCreate');
		
	}
	
	function showPlugins() {
		
		var objWnd = $( '#showPluginsWindow' );
		
		if ( objWnd.length > 0 ) {
			objWnd.mb_bringToFront();
			cb('admin.showPlugins',{"wndStatus":1});
		}else{
			cb('admin.showPlugins',{"wndStatus":0});
		}
		
	}
	
	function showPlugin( pluginID ) {
		
		var objWnd = $( '#showPluginWindow' );
		
		if ( objWnd.length > 0 ) {
			
			objWnd.mb_bringToFront();
			cb('admin.showPlugin',{'wndStatus':1,'pluginID':pluginID});
			
		}else{
			cb('admin.showPlugin',{'wndStatus':0,'pluginID':pluginID});
		}
		
	}
	
	function admShowNewPlugin() {
		
		var objWnd = $( '#showNewPluginWindow' );
		
		if ( objWnd.length > 0 ) {
			
			objWnd.mb_bringToFront();
			cb('admin.showNewPlugin',{'wndStatus':1});
			
		}else{
			cb('admin.showNewPlugin',{'wndStatus':0});
		}
		
	}
	
	function admSavePlugin() {
		
		var plugin 			= $( '#newPlugin' ).val();
		var version 		= $( '#newVersion' ).val();
		var tablePreface 	= $( '#newTablePreface' ).val();
		//alert( plugin+' '+version );
		cb('admin.savePlugin', {'plugin':plugin,'version':version,'tablePreface':tablePreface} );
		
	}
	
	function loadPlugin( pluginID , pluginName , tablePreface ) {
		
		$('#pluginName').val( pluginName );
		$('#tablePreface').val( tablePreface );
		
		$.each( admPluginVersions, function(key, val) {
		
			alert( key+' '+val);
			
		});
		
		
	}
	
	function loadPluginGroups( pluginID ) {
		//alert( pluginID+' , '+versionID );
		// objects
		var oPG 		= $('#pluginGroupsByPlugin');
		oPG.html( '' );
		$.each( admPluginGroups, function( key, val ) {			
			//o.find('option').remove();
			oPG.append( $('<div class="break"></div>').html( '<div class="nonbreak" style="width: 250px;">'+val['groupName']+'</div><div class="nonbreak" style="width: 20px;"><a href="javascript:showPluginGroup( '+pluginID+' , '+val['groupID']+' )">D</a></div><div class="nonbreak" style="width: 20px;"><a href="javascript:deletePluginGroup( '+pluginID+' , '+val['groupID']+' )" style="color:#ff0000;">X</a></div>' ) );
			
		});
		
	}
	
	function admShowPluginVersion( pluginID , versionID ) {
		
		var objWnd = $( '#showPluginVersionWindow' );
		
		if ( objWnd.length > 0 ) {
			
			objWnd.mb_bringToFront();
			cb('admin.showPluginVersion',{'wndStatus':1,'pluginID':pluginID,'versionID':versionID});
			
		} else {
			
			cb('admin.showPluginVersion',{'wndStatus':0,'pluginID':pluginID,'versionID':versionID});
			
		}
		
	}
	
	function loadPluginVersion( pluginID , versionID , versionName , status , current ) {
		//alert( pluginID+' , '+versionID );
		$('#versionName').val( versionName );
		$('#versionStatus').val( status );
		$('#versionCurrent').val( current );
		
		// get text values
		var noTables 	= $('#noTables').val();
		var txtAssign 	= $('#txtAssign').val();
		
		// objects
		var oPG 		= $('#pluginGroups');
		var oVT 		= $('#availableTables');
		
		$.each( admPluginGroups, function( key, val ) {			
			//o.find('option').remove();
			oPG.append( $('<div class="break"></div>').html( '<div class="nonbreak" style="width:270px;">'+val['groupName']+'</div><div class="nonbreak" id="groupForTables_'+val['groupID']+'"></div><div class="nonbreak"><a href="javascript:admShowAssignPluginTableToGroup( '+pluginID+' , '+versionID+' , '+val['groupID']+' );">Z</a></div>' ) );
			oPGID = $( '#group_'+val['groupID'] );
			
		});
		
		$.each( admVersionTables, function(key, val) {
			
			oVT.append( $('<li></li>').html( val['tableName'] ) );
			
		});
		
	}
	
	function loadPluginVersionTableByGroups( groupID ) {
		//alert( groupID );
		var oGT 		= $('#groupForTables_'+groupID );
		
		$.each( admVersionTablesByGroup, function(key, val) {
			//alert( key+' '+val['tableName']);
			oGT.append( $('<li></li>').html( val['tableName'] ) );
			
		});
		
		
	}
	
	function assignTablesToGroup( groupID ) {
		
		var pluginID 	= $( '#pluginID' ).val();
		var versionID 	= $( '#versionID' ).val();
		var objWnd 		= $( '#showTablesByGroupWindow' );
		//alert( pluginID+' '+versionID+' '+groupID );
		if ( objWnd.length > 0 ) {
			
			objWnd.mb_bringToFront();
			cb('admin.showTablesByGroup',{'wndStatus':1,'pluginID':pluginID,'versionID':versionID,'groupID':groupID});
			
		} else {
			
			cb('admin.showTablesByGroup',{'wndStatus':0,'pluginID':pluginID,'versionID':versionID,'groupID':groupID});
			
		}
		
	}
	
	function loadTablesByGroup( pluginID , versionID , groupID , groupName ) {
		
		$('#groupName').val( groupName );
		var oVT 	= $('#admVersionTablesByGroup');
		var i 		= 1;
		
		$.each( admVersionTables, function(key, val) {
			
			if ( val['checked'] == 1 ) {
				
				checkbox = '<input type="checkbox" id="tableByGroup_'+i+'" value="'+val['tableID']+'" checked="checked">';
				
			} else {
				
				checkbox = '<input type="checkbox" id="tableByGroup_'+i+'" value="'+val['tableID']+'">';
				
			}
			
			oVT.append( $('<li></li>').html( '<div class="nonbreak" style="width:30px;">'+checkbox+'</div><div id="nonbreak">'+val['tableName']+'</div><div class="break" style="height:3px;">&nbsp;</div>' ) );
			i++;
			
		});
		
		$('#tableNumbers').val( i );
		
	}
	
	function saveTablesByGroup( pluginID , versionID , groupID ) {
		
		var totalTables = $('#tableNumbers').val();
		var tableID;
		
		for ( i=1; i<totalTables; i++ ) {
			
			tableID = $( '#tableByGroup_'+i ).val();
			
			if ( $( '#tableByGroup_'+i ).is(':checked') ) {
				
				cb( 'admin.saveTableByGroup',{'groupID':groupID,'tableID':tableID} );
				
			} else {
				
				cb( 'admin.deleteTableByGroup',{'groupID':groupID,'tableID':tableID} );
				
			}
			
		}
		
		$( '#showTablesByGroupWindow' ).fadeOut();
		showPluginVersion( pluginID , versionID );
		
	}
	
	function savePluginVersion( pluginID ) {
		
		var version 			= $( '#newVersion' ).val();
		var newVersionStatus 	= $('#newVersionStatus').val();
		
		if ( $('#newVersionCurrent').is(':checked') ) {
			var isCurrent = 1;
		} else {
			var isCurrent = 0;
		}
		
		cb( 'admin.savePluginVersion',{"pluginID":pluginID,"version":version,"newVersionStatus":newVersionStatus,"isCurrent":isCurrent} );
		
	}
	
	function savePluginTable( pluginID ) {
		
		var tableName 		= $( '#newPluginTable' ).val();
		var tablePreface 	= $( '#tablePreface' ).val();
		cb( 'admin.savePluginTable',{'pluginID':pluginID,'tableName':tableName,'tablePreface':tablePreface} );
		
	}
	
	function getVersionTablesFromDB( pluginID , versionID , tablePreface ) {
		
		var customerDB = $( '#customerForGetTables' ).val();
		cb( 'admin.getVersionTablesFromDB',{'pluginID':pluginID,'versionID':versionID,'tablePreface':tablePreface,'customerDB':customerDB} );
		
	}
	
	function saveVersionTables( pluginID , versionID ) {
		
		var table;
		var withData;
		var groupID;
		
		$.each( admVersionTables, function( key , val ) {
			
			table 		= $( '#table_'+key ).val();
			if ( $( '#withData_'+key ).is(':checked') ) {
				withData 	= 1;
			} else {
				withData 	= 0;
			}
			groupID = $( '#groupID_'+key ).val();
			
			cb( 'admin.saveVersionTable',{'versionID':versionID,'table':table,'withData':withData,'groupID':groupID} );
			
		});
		
	}
	
	function admInitVersionTables( totalNumber ) {
	
		var oVT = $('#versionTables');
		
		$.each( admVersionTables, function( key , val ) {
			
			selectedGroupID = val['groupID'];
			//alert( val['groupID'] );
			if ( val['checked'] == 1 ) {
				
				checkbox = '<input type="checkbox" id="withData_'+key+'" value="1" checked="checked">';
				
			} else {
				
				checkbox = '<input type="checkbox" id="withData_'+key+'" value="1">';
				
			}
			
			oVT.append( $('<div class="break"></div>').html( '<div class="nonbreak" style="width:280px;"><input type="hidden" id="table_'+key+'" value="'+val['tableName']+'" />'+val['tableName']+'</div><div class="nonbreak" style="width:100px;">'+checkbox+'</div><div class="nonbreak"><select id="groupID_'+key+'"></select></div>' ) );
			var oVTG = $( '#groupID_'+key );
			
			$.each( admPluginGroups, function( key , val ) {
			
				if ( selectedGroupID == val['groupID'] ) {
					
					oVTG.append( $('<option></option>' ).val( val['groupID'] ).text( val['groupName'] ).attr( 'selected' , 'selected' ) );
					
				} else {
					
					oVTG.append( $('<option></option>' ).val( val['groupID'] ).text( val['groupName'] ) );
					
				}
				
			});
			
		});
		
	}
	
	
	function admShowAssignPluginTableToGroup( pluginID , versionID , groupID ) {
		
		var objWnd = $( '#showAssignPluginTableToGroupWindow' );
		
		if ( objWnd.length > 0 ) {
			
			objWnd.mb_bringToFront();
			cb( 'admin.showAssignPluginTablesToGroup',{'wndStatus':1,'pluginID':pluginID,'versionID':versionID,'groupID':groupID} );
			
		} else {
		//alert( pluginID+' , '+versionID+' , '+tableID );	
			cb( 'admin.showAssignPluginTablesToGroup',{'wndStatus':0,'pluginID':pluginID,'versionID':versionID,'groupID':groupID} );
			
		}
		
	}
	
	function loadTablesForGroup( pluginID , versionID , groupID , groupName ) {
	
		$( '#groupName' ).html( groupName );
		// objects
		var oAT = $('#availableTablesForGroup');
		var i 	= 1;
		
		$.each( admVersionTables, function(key, val) {
			//alert( key+' '+val['checked'] );
			if ( val['checked'] == 1 ) {
				
				checkbox = '<input type="checkbox" id="tableForGroup_'+i+'" value="'+val['tableID']+'" checked="checked">';
				
			} else {
				
				checkbox = '<input type="checkbox" id="tableForGroup_'+i+'" value="'+val['tableID']+'">';
				
			}
			oAT.append( $('<div class="break"></div>').html( '<div class="nonbreak">'+checkbox+'</div><div class="nonbreak">'+val['tableName']+'</div>' ) );
			i++;
			
		});
		
		$( '#totalTablesForGroup' ).val( i );
	
	}
	
	function admAssignPluginTablesToGroup( versionID , groupID ) {
		
		var totalTablesForGroup = $( '#totalTablesForGroup' ).val();
		var table;
		var structure;
		var data;
		var inputData;
		
		for ( i=1; i<totalTablesForGroup; i++ ) {
			
			tableID =  $( '#tableForGroup_'+i ).val();
			
			if ( $( '#tableForGroup_'+i ).is(':checked') ) {
				
				cb( 'admin.saveTableForGroup',{'groupID':groupID,'tableID':tableID} );
				
			} else {
				
				cb( 'admin.deleteTableForGroup',{'groupID':groupID,'tableID':tableID} );
				
			}
			
		}
		
	}
	
	function admSavePluginGroup( pluginID ) {
		
		var groupName = $( '#newPluginGroup' ).val();
		
		if ( groupName == '' ) {
			
			alert( 'group name is empty' );
			return false;
		
		} else {
		
			cb( 'admin.savePluginGroup',{"pluginID":pluginID,"groupName":groupName} );
			
		}
		
	}
	
	function showPluginGroup( pluginID , groupID ) {
		
		var objWnd = $( '#showPluginGroupWindow' );
		
		if ( objWnd.length > 0 ) {
			
			objWnd.mb_bringToFront();
			cb('admin.showPluginGroup',{'wndStatus':1,'pluginID':pluginID,'groupID':groupID});
			
		} else {
			
			cb('admin.showPluginGroup',{'wndStatus':0,'pluginID':pluginID,'groupID':groupID});
			
		}
		
	}
	
	function editPluginGroup( pluginID , groupID ) {
		
		var groupName = $( '#editPluginGroup' ).val();
		//alert( groupID+' '+groupName );
		cb( 'admin.editPluginGroup',{"pluginID":pluginID,'groupID':groupID,"groupName":groupName} );
		
	}
	
	function deletePluginGroup( pluginID , groupID ) {
		
		if ( confirm( 'delete group?') ) {
			
			cb( 'admin.deletePluginGroup',{"pluginID":pluginID,'groupID':groupID} );	
			
		} else {
			
			return false;
			
		}
		
		
	}
	
	
	function loadPluginData( pluginName ) {
		
		$( '#pluginName' ).html( pluginName );
		var oPDG 		= $('#pluginDataGroups');
		
		$.each( admPluginGroups, function( key, val ) {			
			
			oPDG.append( $('<div class="break"></div>').html( '<div class="nonbreak" style="width:200px;">'+val['groupName']+'</div><div class="nonbreak"><select id="groupSel_'+val['groupID']+'"></select></div>' ) );	
			oPDGC = $('#groupSel_'+val['groupID'] );
			
			$.each( admPluginGroupsCustomer , function( key1 , val1 ) {
			//alert( val1['customerID'] );
				oPDGC.append( $('<option></option>').attr( 'value' , val1['customerID'] ) );
				
			});
			
		});
	}
	
	function loadPluginGroupData( groupID ) {
		
		var oPGS 		= $('#groupSel_'+groupID );
		
		$.each( admPluginGroupsCustomer, function( key, val ) {			
			//alert( key+' '+val );
			oPGS.append( $('<option></option>').attr( 'value' , val['customerID'] ) );
			
		});
	}
	
	function setPluginByCustomers() {
		
		alert( admGPBCdata+' '+hallo );
		/*$.each(admGPBCdata, function(key, val) {
			alert( key+' , '+val );
		/*var o = $('#prlCalcFin_'+key);
		o.find('option').remove();
			$.each(val, function() {
				o.append( $('<option></option>').attr("value", this[0]).text(this[1]) );
			});
		});*/
		
	}
	
	//alert( dummyAdressArray.length );
	// slickgrid
	var dataView;
	var columnFilters = {};
	var grid;
	var data = [];

	var options = {
		showHeaderRow: true,
		enableCellNavigation: false,
		enableColumnReorder: false,
		explicitInitialization: true
	};


	function prlPsoFilter(item) {
		for(var columnId in columnFilters) {
			if(columnId !== undefined && columnFilters[columnId] !== "") {
				var c = grid.getColumns()[grid.getColumnIndex(columnId)];
				if(item[c.field].toString().toLowerCase().indexOf(columnFilters[columnId].toString().toLowerCase()) == -1) {
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
			for(var columnId in columnFilters) {
				settings['quickFilterValues'].push({'colID': columnId, 'filterValue': columnFilters[columnId]});
			}
		}
		settings['columnsWidth'] = [];
		var cols = grid.getColumns();
		for(var i=0;i<cols.length;i++) settings['columnsWidth'].push(cols[i].width);
		settings['sort'] = grid.getSortColumns();

		alert(settings.toSource()); //TODO: löschen, da nur zu Testzwecken...
		
	}

	function prlPsoSetSettings(param) {
		
		$('#prlPsoBtnQFilter').attr('checked', param.quickFilterEnabled);
		
		if(param.quickFilterEnabled) {
			for(var i=0;i<param.quickFilterValues.length;i++) {
				columnFilters[param.quickFilterValues[i].colID] = param.quickFilterValues[i].filterValue;
			}
		}
		
		var cols = grid.getColumns();
		
		for(var i=0;i<cols.length;i++) cols[i].width=param.columnsWidth[i];
		
		grid.setColumns(cols);

		if(param.sort.length>0) {
			grid.setSortColumn(param.sort[0].columnId, param.sort[0].sortAsc);
			prlPsoSortSingleColumn(param.sort[0].columnId, param.sort[0].sortAsc, false);
		}

		prlPsoToggleFilterRow();

		grid.setData(dataView);
		grid.updateRowCount();
		grid.render();
		
	}

	function prlPsoToggleFilterRow() {
		if($("#prlPsoBtnQFilter").is(':checked')) {
			$(grid.getHeaderRow()).show();
			grid.showHeaderRow(true);
		}else{
			$(grid.getHeaderRow()).hide();
			grid.showHeaderRow(false);
			$(grid.getHeaderRow()).find("input").val('');
			for(var columnId in columnFilters) columnFilters[columnId] = "";
			dataView.refresh();
		}
		grid.resizeCanvas();
	}

	function prlPsoSortSingleColumn(field, sortAsc, updateGrid) {
		dataView.sort(function(a, b){
			var result = a[field] > b[field] ? 1 : a[field] < b[field] ? -1 : 0; 
			return sortAsc ? result : -result;
		});
		if(updateGrid==null) {
			grid.setData(dataView);
			grid.updateRowCount();
			grid.render();
		}
	}

	function admGritInit( dataType ) {

		switch ( dataType ) {
			
			case 'customers':
				
				var titleCustomerID = $( '#admCustomer' ).val();
				var titleDatabase 	= $( '#admCustomerDB' ).val();
				var titleName 		= $( '#admCustomerName' ).val();
				var titleStreet		= $( '#admCustomerStreet' ).val();
				var titlePlace 		= $( '#admCustomerPlace' ).val();
				
				var columns = [
					{id: "customerID", name: titleCustomerID, field: "customerID", sortable: true, resizable: true, width: 150},
					{id: "customerDB", name: titleDatabase, field: "customerDB", sortable: true, resizable: true , width:150 } ,
					{id: "customerName", name: titleName, field: "customerName", sortable: true, resizable: true , width:150 } ,
					{id: "customerStreet", name: titleStreet, field: "customerStreet", sortable: true, resizable: true , width:150 } ,
					{id: "customerPlace", name: titlePlace, field: "customerPlace", sortable: true, resizable: true , width:150 }
				];
				
				var myGrid = 'customersGrid';
				break;
			
			case 'plugins':
				
				var pluginIDName 		= $( '#pluginIDName' ).val();
				var pluginNameName 		= $( '#pluginNameName' ).val();
				var tablePrefaceName 	= $( '#tablePrefaceName' ).val();
				
				var columns = [
					{id: "pluginID", name: pluginIDName, field: "pluginID", sortable: true, resizable: true, width: 100},
					{id: "pluginName", name: pluginNameName, field: "pluginName", sortable: true, resizable: true , width:200 },
					{id: "tablePreface", name: tablePrefaceName, field: "tablePreface", sortable: true, resizable: true , width:200 }
				];
				var myGrid = 'pluginsGrid';
				break;
			
			default:
				// do nothing

		}
		
		//alert( data );
		dataView = new Slick.Data.DataView();
		grid = new Slick.Grid("#"+myGrid, data , columns, options);
		grid.onSort.subscribe(function (e, args) {
			prlPsoSortSingleColumn(args.sortCol.field, args.sortAsc);
		});

		
		dataView.onRowCountChanged.subscribe(function (e, args) {
			grid.updateRowCount();
			grid.render();
		});

		dataView.onRowsChanged.subscribe(function (e, args) {
			grid.invalidateRows(args.rows);
			grid.render();
		});


		$(grid.getHeaderRow()).delegate(":input", "change keyup", function (e) {
			var columnId = $(this).data("columnId");
			if (columnId != null) {
				columnFilters[columnId] = $.trim($(this).val());
				dataView.refresh();
			}
		});

		grid.onHeaderRowCellRendered.subscribe(function(e, args) {
			$(args.node).empty();
			$("<input type='text'>")
			   .data("columnId", args.column.id)
			   .val(columnFilters[args.column.id])
			   .appendTo(args.node);
		});

		switch ( dataType ) {
			
			case 'customer':
				grid.onClick.subscribe(function(e, args) {
					var cell = grid.getCellFromEvent(e), row = cell.row;
					var item = dataView.getItem(row); //args.item;
					cb('admin.showCustomer', {'customerID':item.customerID,'customerDB':item.customerDB} );
					//showCustomer( item.customerID , item.customerDB );
				});
				
				//Button: MA hinzufügen
				$( "#newCustomer" ).button({
					text: false,
					icons: {
						primary: "p-icon-add"
					}
				})
				.click(function() {
					showNewCustomer();
				});
				
				break;
			
			case 'customers':
				grid.onClick.subscribe(function(e, args) {
					var cell = grid.getCellFromEvent(e), row = cell.row;
					var item = dataView.getItem(row); //args.item;
					showCustomer( item.customerID, item.customerDB );
				});
				//Button: MA hinzufügen
				$( "#prlPsoBtnNew" ).button({
					text: false,
					icons: {
						primary: "p-icon-add"
					}
				})
				.click(function() {
					admShowNewCustomer();
				});
				
				break;
			
			case 'plugins':
				grid.onClick.subscribe(function(e, args) {
					var cell = grid.getCellFromEvent(e), row = cell.row;
					var item = dataView.getItem(row); //args.item;
					showPlugin( item.pluginID );
				});
				//Button: Plugin hinzufügen
				$( "#prlPsoBtnNew" ).button({
					text: false,
					icons: {
						primary: "p-icon-add"
					}
				})
				.click(function() {
					admShowNewPlugin();
				});
				
				break;
			
			default:
				// do nothing

		}

		grid.init();
		admGritData( dataType );
		
		dataView.beginUpdate();
		dataView.setItems(data);
		dataView.setFilter(prlPsoFilter);
		dataView.endUpdate();

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
	}

/*
*******************************************************************************************
Der Code oberhalb muss z.B. in testPlugin.js gespeichert werden.
Der Code innerhalb der "$(function () {..." (s. unten) muss aus den plugin.php mittels ...::jsExecute aufgerufen werden.
Im Prinzip reichen also 3 Zeilen JS-Code, die von plugin.php an den Client übertragen werden um die Tabelle mit Daten zu füllen und zu generieren.
*/

	function admGritData( dataType ) {
		
		var dataTbl = [];
		var dummyAdressArray = [];
		var i=1;
		//alert( data );
		
		switch ( dataType ) {
			
			case 'customer':
				
				$.each(data, function() {
			
					dummyAdressArray = {
						id: i,
						customerID: this.customerID,
						customerDB: this.customerDB,
						active: this.active
					};
					
					dataTbl.push( dummyAdressArray );
					i++;
					
				});
				
				break;
				
			case 'customers':
				
				$.each(data, function() {
			//alert( this.customerID );
					dummyAdressArray = {
						id: i,
						customerID: this.customerID,
						customerDB: this.customerDB,
						customerName: this.customerName,
						customerStreet: this.customerStreet,
						customerPlace: this.customerPlace
					};
					
					dataTbl.push( dummyAdressArray );
					i++;
					
				});
				
				break;
			
			case 'plugins':
				
				$.each(data, function() {
			
					dummyAdressArray = {
						id: i,
						pluginID: this.pluginID,
						pluginName: this.pluginName,
						tablePreface: this.tablePreface
					};
					
					dataTbl.push( dummyAdressArray );
					i++;
					
				});
				
				break;
			
			default:
				// do nothing
			
		}
		
		dataView.beginUpdate();
		dataView.setItems( dataTbl );
		dataView.endUpdate();
		
		prlCalcDataGrid.setSortColumn("customerID",true);
		prlCalOvSortSingleColumn("customerID",true);
		
	}
	
  /*$(function () {
	//var dummyAdressArray = new Array();
	//dummyAdressArray = [{ id: 1, customerID: 'administratoren', customerDB: 'dev-admin', active: 1 }];
	//customerArray = [ { id: 1, customerID: 'dev-admin' , customerDB: 'admindev' , active: 1 } , { id: 2, customerID: 'dev-all' , customerDB: 'development' , active: 1 } , { id: 3, customerID: 'dev-lohn' , dbName: 'lohndev' , active: 1 }];
	
	 //hier wird ein vorbereiteter Array mit ca. 500 Beispieladressen zugewiesen. Normalerweise ist der Array in PHP zu erstellen: "data = [ { id: 1644, EmployeeNumber: 678466, Lastname: 'Müller', Firstname: 'Daniel', Street: 'Tiefenmattstrasse 2a', Zip: 4434, City: 'Hölstein', Sex: 'm' }, { id: 1644, EmployeeNumber: 678466, Lastname: 'Muster', Firstname: 'Hans', Street: 'Testgasse 23', Zip: 4000, City: 'Basel', Sex: 'm' } ];"
	prlPsoInit();
	//prlPsoSetSettings({'columnsWidth':[150, 150, 50], sort:[{'columnId':'customerID', 'sortAsc':true}]});
	
  });*/
	
	/*var grid = new Slick.Grid(container, rows, columns, options);
	
	var columns = [
	  {id: "title", name: "Title", field: "title"},
	  {id: "duration", name: "Duration", field: "duration"},
	  {id: "%", name: "% Complete", field: "percentComplete"},
	  {id: "start", name: "Start", field: "start"},
	  {id: "finish", name: "Finish", field: "finish"},
	  {id: "effort-driven", name: "Effort Driven", field: "effortDriven"}
	];
	
	var options = {
	  enableCellNavigation: true,
	  enableColumnReorder: false
	};
	
	$(function () {
	  var data = [];
	  for (var i = 0; i < 500; i++) {
		data[i] = {
		  title: "Task " + i,
		  duration: "5 days",
		  percentComplete: Math.round(Math.random() * 100),
		  start: "01/01/2009",
		  finish: "01/05/2009",
		  effortDriven: (i % 5 == 0)
		};
	  }
	  
	}*/
	