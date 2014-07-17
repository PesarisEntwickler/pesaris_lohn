<?php

class payroll_BL_reports {
	
	public function CalculationJournal($param) {
        require_once(getcwd()."/kernel/common-functions/configuration.php");
        global $aafwConfig;
		ini_set('memory_limit', '512M');
//$now = microtime(true); //TODO: PROFILING START
//$param = array("year"=>$functionParameters[0]["year"],"majorPeriod"=>$functionParameters[0]["majorPeriod"],"minorPeriod"=>$functionParameters[0]["minorPeriod"])
		$periodLabels["de"] = array("", "Januar", "Februar", "Maerz", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember", "", "", "Gratifikation", "Gratifikation");

		$fm = new file_manager();
		$newTmpDirName = $fm->createTmpDir();
		$newTmpPath = $fm->getFullPath();
		$fm->setFile("metadata.dat")->putContents( serialize(array("fileFormat"=>"pdf","realFileName"=>"compileme.pdf","transmissionFileName"=>"CalculationJournal.pdf")) );
		
		if($param["majorPeriod"]<13 || $param["majorPeriod"]>14) $periodTitle = $param["majorPeriod"]." (".$periodLabels[session_control::getSessionInfo("language")][$param["majorPeriod"]].")";
		else $periodTitle = $param["majorPeriod"];

		$system_database_manager = system_database_manager::getInstance();
		$fp = $fm->setFile("data.xml")->fopen("w");

		fwrite($fp, "<Report name=\"CalculationJournal\" lang=\"de\">\n\t<Header>\n\t\t<Company>\n\t\t\t<Name>Testfirma AG</Name>\n\t\t\t<Street>Hauptstrasse 56</Street>\n\t\t\t<ZipCity>1234 Entenhausen</ZipCity>\n\t\t</Company>\n\t\t<PrintDate>".date("d.m.Y")."</PrintDate>\n\t\t<PrintTime>".date("H:i:s")."</PrintTime>\n\t\t<Year>".$param["year"]."</Year>\n\t\t<Period>".$periodTitle."</Period>\n\t</Header>\n\t<Employees>\n");

		//TODO: $param Werte gegen intrusion sichern !!!!!
		$result = $system_database_manager->executeQuery("SELECT id FROM payroll_period WHERE payroll_year_ID=".$param["year"]." AND major_period=".$param["majorPeriod"]." AND minor_period=".$param["minorPeriod"], "payroll_report_CalculationJournal");
		if(count($result)>0) $payrollPeriodID = $result[0]["id"];
		else return;

		$isCurrentPeriod = false;
		$result = $system_database_manager->executeQuery("SELECT payroll_period_ID FROM payroll_calculation_current LIMIT 1", "payroll_report_CalculationJournal");
		if(count($result)>0) $isCurrentPeriod = $result[0]["payroll_period_ID"]==$payrollPeriodID ? true : false;

//$now = microtime(true); //TODO: PROFILING START
		$result = $system_database_manager->executeQuery("SELECT empl.id as EmployeeID, empl.EmployeeNumber, empl.Firstname, empl.Lastname, empl.payroll_company_ID, empl.CodeAHV, empl.CodeALV, empl.CodeUVG, empl.CodeUVGZ1, empl.CodeUVGZ2, empl.CodeBVG, empl.CodeKTG, empl.EmploymentStatus, acc.id as AccountNumber, acclbl.label, calc.quantity, calc.rate, calc.amount, calc.code FROM ".($isCurrentPeriod ? "payroll_calculation_current" : "payroll_calculation_entry")." calc INNER JOIN payroll_employee empl ON empl.id=calc.payroll_employee_ID INNER JOIN payroll_account acc ON acc.id=calc.payroll_account_ID AND acc.payroll_year_ID=calc.payroll_year_ID INNER JOIN payroll_account_label acclbl ON acclbl.payroll_account_ID=acc.id AND acclbl.payroll_year_ID=acc.payroll_year_ID AND acclbl.language='".session_control::getSessionInfo("language")."' WHERE calc.payroll_period_ID=".$payrollPeriodID." ORDER BY empl.Lastname, empl.Firstname, calc.payroll_employee_ID, acc.id", "payroll_report_CalculationJournal");
		$lastEmployeeID = 0;
		$entryCollector = array();
		$singleEmployeeData = "";
		foreach($result as $row) {
			if($row["EmployeeID"] != $lastEmployeeID) {
				//the employee changed!
				if($singleEmployeeData != "") {
					//there are data for writing to the XML file
					fwrite($fp, $singleEmployeeData.str_replace(array("&","_","%","#"), array("\\&","\\_","\\%","\\#"), implode("",$entryCollector))."\t\t\t</Entries>\n\t\t</Employee>\n");
				}
				$lastEmployeeID = $row["EmployeeID"];
				$entryCollector = array();
				$singleEmployeeData = "\t\t<Employee>\n\t\t\t<EmployeeNumber>".$row["EmployeeNumber"]."</EmployeeNumber>\n\t\t\t<CompanyID>".$row["payroll_company_ID"]."</CompanyID>\n\t\t\t<Firstname>".$row["Firstname"]."</Firstname>\n\t\t\t<Lastname>".$row["Lastname"]."</Lastname>\n\t\t\t<CodeAHV>".$row["CodeAHV"]."</CodeAHV>\n\t\t\t<CodeALV>".$row["CodeALV"]."</CodeALV>\n\t\t\t<CodeKTG>".$row["CodeKTG"]."</CodeKTG>\n\t\t\t<CodeUVG>".$row["CodeUVG"]."</CodeUVG>\n\t\t\t<CodeBVG>".$row["CodeBVG"]."</CodeBVG>\n\t\t\t<Status>".$row["EmploymentStatus"]."</Status>\n\t\t\t<Entries>\n";
			}
			$entryCollector[] = "\t\t\t\t<Entry>\n\t\t\t\t\t<AccountNumber>".$row["AccountNumber"]."</AccountNumber>\n\t\t\t\t\t<AccountName>".$row["label"]."</AccountName>\n\t\t\t\t\t<quantity>".$row["quantity"]."</quantity>\n\t\t\t\t\t<rate>".$row["rate"]."</rate>\n\t\t\t\t\t<amount>".$row["amount"]."</amount>".($row["code"]=="" ? "" : "\n\t\t\t\t\t<code>".$row["code"]."</code>")."\n\t\t\t\t</Entry>\n";
		}
		if($singleEmployeeData != "") {
			//there are still a few more data for writing to the XML file
//			fwrite($fp, $singleEmployeeData.implode("",$entryCollector)."\t\t\t</Entries>\n\t\t</Employee>\n");
			fwrite($fp, $singleEmployeeData.str_replace(array("&","_","%","#"), array("\\&","\\_","\\%","\\#"), implode("",$entryCollector))."\t\t\t</Entries>\n\t\t</Employee>\n");
		}
		fwrite($fp, "\t</Employees>\n</Report>\n");
		$fm->fclose();

//communication_interface::alert("db+xml: ".(microtime(true) - $now)); //TODO: PROFILING STOP
//error_log("\ndb+xml: ".(microtime(true) - $now)."\n", 3, "/var/log/copronet-application.log");

		chdir($newTmpPath);
//$now = microtime(true); //TODO: PROFILING START

//		system("cp ./data.xml /usr/local/www/apache22/data/plugins/payroll_V00_01_00/code_logic/compileme.tex");
        system($aafwConfig["paths"]["utilities"]["xsltproc"]." ".$aafwConfig["paths"]["reports"]["templates"]."CalculationJournal.xslt ./data.xml > ./compileme.tex");
        
//communication_interface::alert("xslt: ".(microtime(true) - $now)); //TODO: PROFILING STOP
//error_log("\nxslt: ".(microtime(true) - $now)."\n", 3, "/var/log/copronet-application.log");

//$now = microtime(true); //TODO: PROFILING START
        system($aafwConfig["paths"]["utilities"]["pdflatex"]." -interaction=batchmode compileme.tex > ".$aafwConfig["paths"]["utilities"]["stdout"]);
		system($aafwConfig["paths"]["utilities"]["pdflatex"]." -interaction=batchmode compileme.tex > ".$aafwConfig["paths"]["utilities"]["stdout"]);
		system("chmod 666 *");
//file_put_contents($newTmpPath."/debug.txt", (microtime(true) - $now) ); //TODO: PROFILING STOP
//communication_interface::alert("pdflatex: ".(microtime(true) - $now)); //TODO: PROFILING STOP
//error_log("\npdflatex: ".(microtime(true) - $now)."\n", 3, "/var/log/copronet-application.log");

/*
$now = microtime(true); //TODO: PROFILING START
		system("latex compileme.tex > /dev/null");
		system("latex compileme.tex > /dev/null");
communication_interface::alert("LaTeX: ".(microtime(true) - $now)); //TODO: PROFILING STOP
$now = microtime(true); //TODO: PROFILING START
		system("dvips -t landscape compileme.dvi > /dev/null");
		system("ps2pdf compileme.ps > /dev/null");
communication_interface::alert("divps+ps2pdf: ".(microtime(true) - $now)); //TODO: PROFILING STOP
*/
//$now = microtime(true); //TODO: PROFILING START
//		system("rm compileme.aux");
//		system("rm compileme.dvi");
//		system("rm compileme.log");
//		system("rm compileme.ps");
//communication_interface::alert("rm: ".(microtime(true) - $now)); //TODO: PROFILING STOP

		return $newTmpDirName;
	}

	public function FinAccountingJournal($param) {
		return $this->AccountingJournal($param,"FinancialAccountingJournal","payroll_fin_acc_entry");
	}

	public function MgmtAccountingJournal($param) {
		return $this->AccountingJournal($param,"ManagementAccountingJournal","payroll_mgmt_acc_entry");
	}

	private function AccountingJournal($param,$ReportName,$entryTable) {
        require_once(getcwd()."/kernel/common-functions/configuration.php");
        global $aafwConfig;
		ini_set('memory_limit', '512M');
		$periodLabels["de"] = array("", "Januar", "Februar", "Maerz", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember", "", "", "Gratifikation", "Gratifikation");

		$fm = new file_manager();
		$newTmpDirName = $fm->createTmpDir();
		$newTmpPath = $fm->getFullPath();
		$fm->setFile("metadata.dat")->putContents( serialize(array("fileFormat"=>"pdf","realFileName"=>"compileme.pdf","transmissionFileName"=>$ReportName.".pdf")) );

		if($param["majorPeriod"]<13 || $param["majorPeriod"]>14) $periodTitle = $param["majorPeriod"]." (".$periodLabels[session_control::getSessionInfo("language")][$param["majorPeriod"]].")";
		else $periodTitle = $param["majorPeriod"];

		$system_database_manager = system_database_manager::getInstance();
		$fp = $fm->setFile("data.xml")->fopen("w");

		//TODO: $param Werte gegen intrusion sichern !!!!!
		$result = $system_database_manager->executeQuery("SELECT id FROM payroll_period WHERE payroll_year_ID=".$param["year"]." AND major_period=".$param["majorPeriod"]." AND minor_period=".$param["minorPeriod"], "payroll_report_".$ReportName);
		if(count($result)>0) $payrollPeriodID = $result[0]["id"];
		else return;

		$isCurrentPeriod = false;
		$result = $system_database_manager->executeQuery("SELECT payroll_period_ID FROM payroll_calculation_current LIMIT 1", "payroll_report_".$ReportName);
		if(count($result)>0) $isCurrentPeriod = $result[0]["payroll_period_ID"]==$payrollPeriodID ? true : false;
        
        //Query bereitstellen und Reportvorlage definieren
        $reportTemplate = "";
        switch ($param["selectedReportType"])
        {
            case 0:
                // Auswertung nach Mitarbeiter
                $reportTemplate = $ReportName;
                
                fwrite($fp, "<Report name=\"".$ReportName."\" lang=\"de\">\n\t<Header>\n\t\t<Company>\n\t\t\t<Name>Testfirma AG</Name>\n\t\t\t<Street>Hauptstrasse 56</Street>\n\t\t\t<ZipCity>1234 Entenhausen</ZipCity>\n\t\t</Company>\n\t\t<PrintDate>".date("d.m.Y")."</PrintDate>\n\t\t<PrintTime>".date("H:i:s")."</PrintTime>\n\t\t<Year>".$param["year"]."</Year>\n\t\t<Period>".$periodTitle."</Period>\n\t</Header>\n\t<Employees>\n");
                
                $query = "SELECT 
                                emp.`id` as EmployeeID, 
                                emp.`EmployeeNumber`, 
                                emp.`payroll_company_ID`, 
                                emp.`Lastname`, 
                                emp.`Firstname`, 
                                accetry.`payroll_account_ID`, 
                                acclbl.`label`, 
                                accetry.`account_no`, 
                                accetry.`counter_account_no`, 
                                accetry.`cost_center`, 
                                accetry.`amount_local`, 
                                accetry.`debitcredit`, 
                                accetry.`entry_text` 
                            FROM 
                                `".$entryTable."` accetry 
                                    INNER JOIN 
                                `payroll_employee` emp ON emp.`id`=accetry.`payroll_employee_ID` 
                                    INNER JOIN 
                                `payroll_account_label` acclbl ON acclbl.`payroll_account_ID`=accetry.`payroll_account_ID` 
                                AND acclbl.`payroll_year_ID`=".$param["year"]." 
                                AND acclbl.`language`='".session_control::getSessionInfo("language")."' 
                            WHERE accetry.`payroll_period_ID`=".$payrollPeriodID." 
                            ORDER BY 
                                emp.`Lastname`, 
                                emp.`Firstname`, 
                                emp.`id`, 
                                accetry.`payroll_account_ID`";
                $result = $system_database_manager->executeQuery($query, "payroll_report_".$ReportName);
                $lastEmployeeID = 0;
                $entryCollector = array();
                $singleEmployeeData = "";
                foreach($result as $row) {
                    if($row["EmployeeID"] != $lastEmployeeID) {
                        //the employee changed!
                        if($singleEmployeeData != "") {
                            //there are data for writing to the XML file
                            fwrite($fp, $singleEmployeeData.str_replace(array("&","_","%","#"), array("\\&","\\_","\\%","\\#"), implode("",$entryCollector))."\t\t\t</Entries>\n\t\t</Employee>\n");
                        }
                        $lastEmployeeID = $row["EmployeeID"];
                        $entryCollector = array();
                        $singleEmployeeData = "\t\t<Employee>\n\t\t\t<EmployeeNumber>".$row["EmployeeNumber"]."</EmployeeNumber>\n\t\t\t<CompanyID>".$row["payroll_company_ID"]."</CompanyID>\n\t\t\t<Firstname>".$row["Firstname"]."</Firstname>\n\t\t\t<Lastname>".$row["Lastname"]."</Lastname>\n\t\t\t<Entries>\n";
                    }
                    $entryCollector[] = "\t\t\t\t<Entry>\n\t\t\t\t\t<AccountNumber>".$row["payroll_account_ID"]."</AccountNumber>\n\t\t\t\t\t<AccountName>".$row["label"]."</AccountName>\n\t\t\t\t\t<MainAccountNumber>".$row["account_no"]."</MainAccountNumber>\n\t\t\t\t\t<CounterAccountNumber>".$row["counter_account_no"]."</CounterAccountNumber>\n\t\t\t\t\t<CostCenter>".$row["cost_center"]."</CostCenter>\n\t\t\t\t\t<amount>".$row["amount_local"]."</amount>\n\t\t\t\t\t<debitcredit>".$row["debitcredit"]."</debitcredit>\n\t\t\t\t\t<EntryText>".$row["entry_text"]."</EntryText>\n\t\t\t\t</Entry>\n";
                }
                if($singleEmployeeData != "") {
                    fwrite($fp, $singleEmployeeData.str_replace(array("&","_","%","#"), array("\\&","\\_","\\%","\\#"), implode("",$entryCollector))."\t\t\t</Entries>\n\t\t</Employee>\n");
                }
                fwrite($fp, "\t</Employees>\n</Report>\n");
                $fm->fclose();
                break;
        	case 1:
                // Auswertung nach Firma / Konto / Gegenkonto / Kst
                $reportTemplate = "AccountingJournal[Company][Account][Counter_account][Cost_center]"; 
                
                fwrite($fp, "<Report name=\"".$ReportName."\" lang=\"de\">\n\t<Header>\n\t\t<MainCompany>\n\t\t\t<Name>Testfirma AG</Name>\n\t\t\t<Street>Hauptstrasse 56</Street>\n\t\t\t<ZipCity>1234 Entenhausen</ZipCity>\n\t\t</MainCompany>\n\t\t<PrintDate>".date("d.m.Y")."</PrintDate>\n\t\t<PrintTime>".date("H:i:s")."</PrintTime>\n\t\t<Year>".$param["year"]."</Year>\n\t\t<Period>".$periodTitle."</Period>\n\t\t<AccountType>".$entryTable."</AccountType>\n\t</Header>\n\t<Corporation>\n\t\t<Companies>\n");
                
                $query = "  SELECT 
                                emp.payroll_company_ID,
                                comp.company_shortname,
                                accetry.account_no,
                                accetry.counter_account_no,
                                accetry.cost_center,
                                SUM(IF(accetry.debitcredit = 0, accetry.amount_local,0)) AS debit_amount,
	                            SUM(IF(accetry.debitcredit = 1, accetry.amount_local,0)) AS credit_amount,
                                accetry.entry_text
                            FROM
                                ".$entryTable." accetry
                                    INNER JOIN
                                payroll_employee emp ON emp.id = accetry.payroll_employee_ID
                                    INNER JOIN
                                payroll_company comp ON comp.id = emp.payroll_company_ID
									INNER JOIN
                                payroll_account_label acclbl ON acclbl.payroll_account_ID = accetry.payroll_account_ID
                                    AND acclbl.payroll_year_ID = ".$param["year"]."
                                    AND acclbl.language = '".session_control::getSessionInfo("language")."'
                            WHERE accetry.payroll_period_ID = ".$payrollPeriodID.
                            ($param["company"] == false ? "":" AND emp.payroll_company_ID = ".$param["company"])."
                            GROUP BY    emp.payroll_company_ID, 
                                        accetry.account_no, 
                                        accetry.counter_account_no, 
                                        accetry.cost_center, 
                                        accetry.entry_text
                            ORDER BY    emp.payroll_company_ID , 
                                        accetry.account_no , 
                                        accetry.counter_account_no , 
                                        accetry.cost_center";
                $result = $system_database_manager->executeQuery($query, "payroll_report_".$ReportName);
                $lastPayrollCompanyID = 0;
                $runningSumDebitAmount = 0;
                $runningSumCreditAmount = 0;
                $corporationDebitAmount = 0;
                $corporationCreditAmount = 0;
                $entryCounter = 1;
                $entryCollector = array();
                $singleEmployeeData = "";
                foreach($result as $row) {
                    if($row["payroll_company_ID"] != $lastPayrollCompanyID) {
                        //the payrollId changed!
                        if($singleEmployeeData != "") {
                            //there are data for writing to the XML file
                            fwrite($fp, $singleEmployeeData.str_replace(array("&","_","%","#"), array("\\&","\\_","\\%","\\#"), implode("",$entryCollector))."\t\t\t\t</Entries>\n\t\t\t\t<CompanyDebitAmount>".$runningSumDebitAmount."</CompanyDebitAmount>\n\t\t\t\t<CompanyCreditAmount>".$runningSumCreditAmount."</CompanyCreditAmount>\n\t\t\t</Company>\n");
                        }
                        $runningSumDebitAmount = 0;
                        $runningSumCreditAmount = 0;
                        $entryCounter = 1;
                        $lastPayrollCompanyID = $row["payroll_company_ID"];
                        $entryCollector = array();
                        $singleEmployeeData = "\t\t\t<Company>\n\t\t\t\t<CompanyID>".$row["payroll_company_ID"]."</CompanyID>\n\t\t\t\t<CompanyName>".$row["company_shortname"]."</CompanyName>\n\t\t\t\t<Entries>\n";
                    }
                    $runningSumDebitAmount += $row["debit_amount"];
                    $runningSumCreditAmount += $row["credit_amount"];
                    $corporationDebitAmount += $row["debit_amount"];
                    $corporationCreditAmount += $row["credit_amount"];
                    	
                    $entryCollector[] = "\t\t\t\t\t<Entry ".
                                             ($entryCounter % 30 == 0?
                                                 "doPageBreak=\"true\""
                                                 :
                                                 "doPageBreak=\"false\"").
                                        ">\n\t\t\t\t\t\t<CompanyID>".$row["payroll_company_ID"]."</CompanyID>\n\t\t\t\t\t\t<Account>".$row["account_no"]."</Account>\n\t\t\t\t\t\t<CounterAccount>".$row["counter_account_no"]."</CounterAccount>\n\t\t\t\t\t\t<CostCenter>".$row["cost_center"]."</CostCenter>\n\t\t\t\t\t\t<DebitAmount>".$row["debit_amount"]."</DebitAmount>\n\t\t\t\t\t\t<CreditAmount>".$row["credit_amount"]."</CreditAmount>\n\t\t\t\t\t\t<EntryText>".$row["entry_text"]."</EntryText>\n\t\t\t\t\t\t<RunningSumDebitAmount>".$runningSumDebitAmount."</RunningSumDebitAmount>\n\t\t\t\t\t\t<RunningSumCreditAmount>".$runningSumCreditAmount."</RunningSumCreditAmount>\n\t\t\t\t\t</Entry>\n";
                    
                    $entryCounter += 1;
                }
                if($singleEmployeeData != "") {
                    fwrite($fp, $singleEmployeeData.str_replace(array("&","_","%","#"), array("\\&","\\_","\\%","\\#"), implode("",$entryCollector))."\t\t\t\t</Entries>\n\t\t\t\t<CompanyDebitAmount>".$runningSumDebitAmount."</CompanyDebitAmount>\n\t\t\t\t<CompanyCreditAmount>".$runningSumCreditAmount."</CompanyCreditAmount>\n\t\t\t</Company>\n");
                }
                fwrite($fp, "\t\t</Companies>\n\t\t<CorporationDebitAmount>".$corporationDebitAmount."</CorporationDebitAmount>\n\t\t<CorporationCreditAmount>".$corporationCreditAmount."</CorporationCreditAmount>\n\t</Corporation>\n</Report>\n");
                $fm->fclose();
                break;
             case 2:
                 // Auswertung nach Firma / Kst / Konto / Gegenkonto
                 $reportTemplate = "AccountingJournal[Company][Cost_center][Account][Counter_account]"; 
                 
                 fwrite($fp, "<Report name=\"".$ReportName."\" lang=\"de\">\n\t<Header>\n\t\t<MainCompany>\n\t\t\t<Name>Testfirma AG</Name>\n\t\t\t<Street>Hauptstrasse 56</Street>\n\t\t\t<ZipCity>1234 Entenhausen</ZipCity>\n\t\t</MainCompany>\n\t\t<PrintDate>".date("d.m.Y")."</PrintDate>\n\t\t<PrintTime>".date("H:i:s")."</PrintTime>\n\t\t<Year>".$param["year"]."</Year>\n\t\t<Period>".$periodTitle."</Period>\n\t\t<AccountType>".$entryTable."</AccountType>\n\t</Header>\n\t<Corporation>\n\t\t<Companies>\n");
                 
                 $query = "  SELECT 
                                emp.payroll_company_ID,
                                comp.company_shortname,
                                accetry.account_no,
                                accetry.counter_account_no,
                                accetry.cost_center,
                                SUM(IF(accetry.debitcredit = 0, accetry.amount_local,0)) AS debit_amount,
	                            SUM(IF(accetry.debitcredit = 1, accetry.amount_local,0)) AS credit_amount,
                                accetry.entry_text
                            FROM
                                ".$entryTable." accetry
                                    INNER JOIN
                                payroll_employee emp ON emp.id = accetry.payroll_employee_ID
                                    INNER JOIN
                                payroll_company comp ON comp.id = emp.payroll_company_ID
									INNER JOIN
                                payroll_account_label acclbl ON acclbl.payroll_account_ID = accetry.payroll_account_ID
                                    AND acclbl.payroll_year_ID = ".$param["year"]."
                                    AND acclbl.language = '".session_control::getSessionInfo("language")."'
                            WHERE accetry.payroll_period_ID = ".$payrollPeriodID.
                            ($param["company"] == false ? "":" AND emp.payroll_company_ID = ".$param["company"]).
                            ($param["cost_center"] == false ? "": " AND accetry.cost_center = ".$param["cost_center"])."
                            GROUP BY    emp.payroll_company_ID, 
                                        accetry.cost_center, 
                                        accetry.account_no, 
                                        accetry.counter_account_no, 
                                        accetry.entry_text
                            ORDER BY    emp.payroll_company_ID , 
                                        accetry.cost_center,
                                        accetry.account_no , 
                                        accetry.counter_account_no";
                 $result = $system_database_manager->executeQuery($query, "payroll_report_".$ReportName);
                 $lastPayrollCompanyID = 0;
                 $runningSumDebitAmount = 0;
                 $runningSumCreditAmount = 0;
                 $corporationDebitAmount = 0;
                 $corporationCreditAmount = 0;
                 $entryCounter = 1;
                 $entryCollector = array();
                 $singleEmployeeData = "";
                 foreach($result as $row) {
                     if($row["payroll_company_ID"] != $lastPayrollCompanyID) {
                         //the employee changed!
                         if($singleEmployeeData != "") {
                             //there are data for writing to the XML file
                             fwrite($fp, $singleEmployeeData.str_replace(array("&","_","%","#"), array("\\&","\\_","\\%","\\#"), implode("",$entryCollector))."\t\t\t</Entries>\n\t\t\t<CompanyDebitAmount>".$runningSumDebitAmount."</CompanyDebitAmount>\n\t\t\t<CompanyCreditAmount>".$runningSumCreditAmount."</CompanyCreditAmount>\n\t\t</Company>\n");
                         }
                         $runningSumDebitAmount = 0;
                         $runningSumCreditAmount = 0;
                         $entryCounter = 1;
                         $lastPayrollCompanyID = $row["payroll_company_ID"];
                         $entryCollector = array();
                         $singleEmployeeData = "\t\t\t<Company>\n\t\t\t\t<CompanyID>".$row["payroll_company_ID"]."</CompanyID>\n\t\t\t\t<CompanyName>".$row["company_shortname"]."</CompanyName>\n\t\t\t\t<Entries>\n";
                     }
                     $runningSumDebitAmount += $row["debit_amount"];
                     $runningSumCreditAmount += $row["credit_amount"];
                     $corporationDebitAmount += $row["debit_amount"];
                     $corporationCreditAmount += $row["credit_amount"];
                     $entryCollector[] = "\t\t\t\t\t<Entry ".
                                             ($entryCounter % 30 == 0?
                                                 "doPageBreak=\"true\""
                                                 :
                                                 "doPageBreak=\"false\"").
                                        ">\n\t\t\t\t\t\t<CompanyID>".$row["payroll_company_ID"]."</CompanyID>\n\t\t\t\t\t\t<Account>".$row["account_no"]."</Account>\n\t\t\t\t\t\t<CounterAccount>".$row["counter_account_no"]."</CounterAccount>\n\t\t\t\t\t\t<CostCenter>".$row["cost_center"]."</CostCenter>\n\t\t\t\t\t\t<DebitAmount>".$row["debit_amount"]."</DebitAmount>\n\t\t\t\t\t\t<CreditAmount>".$row["credit_amount"]."</CreditAmount>\n\t\t\t\t\t\t<EntryText>".$row["entry_text"]."</EntryText>\n\t\t\t\t\t\t<RunningSumDebitAmount>".$runningSumDebitAmount."</RunningSumDebitAmount>\n\t\t\t\t\t\t<RunningSumCreditAmount>".$runningSumCreditAmount."</RunningSumCreditAmount>\n\t\t\t\t\t</Entry>\n";
                     $entryCounter += 1;
                 }
                 if($singleEmployeeData != "") {
                     fwrite($fp, $singleEmployeeData.str_replace(array("&","_","%","#"), array("\\&","\\_","\\%","\\#"), implode("",$entryCollector))."\t\t\t</Entries>\n\t\t\t<CompanyDebitAmount>".$runningSumDebitAmount."</CompanyDebitAmount>\n\t\t\t<CompanyCreditAmount>".$runningSumCreditAmount."</CompanyCreditAmount>\n\t\t</Company>\n");
                 }
                 fwrite($fp, "\n\t\t</Companies>\n\t\t<CorporationDebitAmount>".$corporationDebitAmount."</CorporationDebitAmount>\n\t\t<CorporationCreditAmount>".$corporationCreditAmount."</CorporationCreditAmount>\n\t</Corporation>\n</Report>\n");
                 $fm->fclose();
                 break;
             case 3:
                 // Auswertung nach Firma / Konto / Gegenkonto
                 $reportTemplate = "AccountingJournal[Company][Account][Counter_account]"; 
                 
                 fwrite($fp, "<Report name=\"".$ReportName."\" lang=\"de\">\n\t<Header>\n\t\t<MainCompany>\n\t\t\t<Name>Testfirma AG</Name>\n\t\t\t<Street>Hauptstrasse 56</Street>\n\t\t\t<ZipCity>1234 Entenhausen</ZipCity>\n\t\t</MainCompany>\n\t\t<PrintDate>".date("d.m.Y")."</PrintDate>\n\t\t<PrintTime>".date("H:i:s")."</PrintTime>\n\t\t<Year>".$param["year"]."</Year>\n\t\t<Period>".$periodTitle."</Period>\n\t\t<AccountType>".$entryTable."</AccountType>\n\t</Header>\n\t<Corporation>\n\t\t<Companies>\n");
                 
                 $query = "  SELECT 
                                emp.payroll_company_ID,
                                comp.company_shortname,
                                accetry.account_no,
                                accetry.counter_account_no,
                                SUM(IF(accetry.debitcredit = 0, accetry.amount_local,0)) AS debit_amount,
	                            SUM(IF(accetry.debitcredit = 1, accetry.amount_local,0)) AS credit_amount,
                                accetry.entry_text
                            FROM
                                ".$entryTable." accetry
                                    INNER JOIN
                                payroll_employee emp ON emp.id = accetry.payroll_employee_ID
                                    INNER JOIN
                                payroll_company comp ON comp.id = emp.payroll_company_ID
									INNER JOIN
                                payroll_account_label acclbl ON acclbl.payroll_account_ID = accetry.payroll_account_ID
                                    AND acclbl.payroll_year_ID = ".$param["year"]."
                                    AND acclbl.language = '".session_control::getSessionInfo("language")."'
                            WHERE accetry.payroll_period_ID = ".$payrollPeriodID.
                            ($param["company"] == false ? "":" AND emp.payroll_company_ID = ".$param["company"])."
                            GROUP BY    emp.payroll_company_ID, 
                                        accetry.account_no, 
                                        accetry.counter_account_no, 
                                        accetry.entry_text
                            ORDER BY    emp.payroll_company_ID , 
                                        accetry.account_no , 
                                        accetry.counter_account_no";
                 $result = $system_database_manager->executeQuery($query, "payroll_report_".$ReportName);
                 $lastPayrollCompanyID = 0;
                 $runningSumDebitAmount = 0;
                 $runningSumCreditAmount = 0;
                 $corporationDebitAmount = 0;
                 $corporationCreditAmount = 0;
                 $entryCounter = 1;
                 $entryCollector = array();
                 $singleEmployeeData = "";
                 foreach($result as $row) {
                     if($row["payroll_company_ID"] != $lastPayrollCompanyID) {
                         //the employee changed!
                         if($singleEmployeeData != "") {
                             //there are data for writing to the XML file
                             fwrite($fp, $singleEmployeeData.str_replace(array("&","_","%","#"), array("\\&","\\_","\\%","\\#"), implode("",$entryCollector))."\t\t\t</Entries>\n\t\t\t<CompanyDebitAmount>".$runningSumDebitAmount."</CompanyDebitAmount>\n\t\t\t<CompanyCreditAmount>".$runningSumCreditAmount."</CompanyCreditAmount>\n\t\t</Company>\n");
                         }
                         $runningSumDebitAmount = 0;
                         $runningSumCreditAmount = 0;
                         $entryCounter = 1;
                         $lastPayrollCompanyID = $row["payroll_company_ID"];
                         $entryCollector = array();
                         $singleEmployeeData = "\t\t\t<Company>\n\t\t\t\t<CompanyID>".$row["payroll_company_ID"]."</CompanyID>\n\t\t\t\t<CompanyName>".$row["company_shortname"]."</CompanyName>\n\t\t\t\t<Entries>\n";
                     }
                     $runningSumDebitAmount += $row["debit_amount"];
                     $runningSumCreditAmount += $row["credit_amount"];
                     $corporationDebitAmount += $row["debit_amount"];
                     $corporationCreditAmount += $row["credit_amount"];
                     $entryCollector[] = "\t\t\t\t\t<Entry ".
                                             ($entryCounter % 30 == 0?
                                                 "doPageBreak=\"true\""
                                                 :
                                                 "doPageBreak=\"false\"").
                                        ">\n\t\t\t\t\t\t<CompanyID>".$row["payroll_company_ID"]."</CompanyID>\n\t\t\t\t\t\t<Account>".$row["account_no"]."</Account>\n\t\t\t\t\t\t<CounterAccount>".$row["counter_account_no"]."</CounterAccount>\n\t\t\t\t\t\t<DebitAmount>".$row["debit_amount"]."</DebitAmount>\n\t\t\t\t\t\t<CreditAmount>".$row["credit_amount"]."</CreditAmount>\n\t\t\t\t\t\t<EntryText>".$row["entry_text"]."</EntryText>\n\t\t\t\t\t\t<RunningSumDebitAmount>".$runningSumDebitAmount."</RunningSumDebitAmount>\n\t\t\t\t\t\t<RunningSumCreditAmount>".$runningSumCreditAmount."</RunningSumCreditAmount>\n\t\t\t\t\t</Entry>\n";
                     $entryCounter += 1;
                 }
                 if($singleEmployeeData != "") {
                     fwrite($fp, $singleEmployeeData.str_replace(array("&","_","%","#"), array("\\&","\\_","\\%","\\#"), implode("",$entryCollector))."\t\t\t</Entries>\n\t\t\t<CompanyDebitAmount>".$runningSumDebitAmount."</CompanyDebitAmount>\n\t\t\t<CompanyCreditAmount>".$runningSumCreditAmount."</CompanyCreditAmount>\n\t\t</Company>\n");
                 }
                 fwrite($fp, "\n\t\t</Companies>\n\t\t<CorporationDebitAmount>".$corporationDebitAmount."</CorporationDebitAmount>\n\t\t<CorporationCreditAmount>".$corporationCreditAmount."</CorporationCreditAmount>\n\t</Corporation>\n</Report>\n");
                 $fm->fclose();
                 break;
             case 4:
                 // Auswertung nach Firma / Kst
                 $reportTemplate = "AccountingJournal[Company][Cost_center]"; 
                 
                 fwrite($fp, "<Report name=\"".$ReportName."\" lang=\"de\">\n\t<Header>\n\t\t<MainCompany>\n\t\t\t<Name>Testfirma AG</Name>\n\t\t\t<Street>Hauptstrasse 56</Street>\n\t\t\t<ZipCity>1234 Entenhausen</ZipCity>\n\t\t</MainCompany>\n\t\t<PrintDate>".date("d.m.Y")."</PrintDate>\n\t\t<PrintTime>".date("H:i:s")."</PrintTime>\n\t\t<Year>".$param["year"]."</Year>\n\t\t<Period>".$periodTitle."</Period>\n\t\t<AccountType>".$entryTable."</AccountType>\n\t</Header>\n\t<Corporation>\n\t\t<Companies>\n");
                 
                 $query = "  SELECT 
                                emp.payroll_company_ID,
                                comp.company_shortname,
                                accetry.cost_center,
                                SUM(IF(accetry.debitcredit = 0, accetry.amount_local,0)) AS debit_amount,
	                            SUM(IF(accetry.debitcredit = 1, accetry.amount_local,0)) AS credit_amount,
                                accetry.entry_text
                            FROM
                                ".$entryTable." accetry
                                    INNER JOIN
                                payroll_employee emp ON emp.id = accetry.payroll_employee_ID
                                    INNER JOIN
                                payroll_company comp ON comp.id = emp.payroll_company_ID
									INNER JOIN
                                payroll_account_label acclbl ON acclbl.payroll_account_ID = accetry.payroll_account_ID
                                    AND acclbl.payroll_year_ID = ".$param["year"]."
                                    AND acclbl.language = '".session_control::getSessionInfo("language")."'
                            WHERE accetry.payroll_period_ID = ".$payrollPeriodID.
                            ($param["company"] == false ? "":" AND emp.payroll_company_ID = ".$param["company"]).
                            ($param["cost_center"] == false ? "": " AND accetry.cost_center = ".$param["cost_center"])."
                            GROUP BY    emp.payroll_company_ID, 
                                        accetry.cost_center, 
                                        accetry.entry_text
                            ORDER BY    emp.payroll_company_ID , 
                                        accetry.cost_center";
                 $result = $system_database_manager->executeQuery($query, "payroll_report_".$ReportName);
                 $lastPayrollCompanyID = 0;
                 $runningSumDebitAmount = 0;
                 $runningSumCreditAmount = 0;
                 $corporationDebitAmount = 0;
                 $corporationCreditAmount = 0;
                 $entryCounter = 1;
                 $entryCollector = array();
                 $singleEmployeeData = "";
                 foreach($result as $row) {
                     if($row["payroll_company_ID"] != $lastPayrollCompanyID) {
                         //the employee changed!
                         if($singleEmployeeData != "") {
                             //there are data for writing to the XML file
                             fwrite($fp, $singleEmployeeData.str_replace(array("&","_","%","#"), array("\\&","\\_","\\%","\\#"), implode("",$entryCollector))."\t\t\t</Entries>\n\t\t\t<CompanyDebitAmount>".$runningSumDebitAmount."</CompanyDebitAmount>\n\t\t\t<CompanyCreditAmount>".$runningSumCreditAmount."</CompanyCreditAmount>\n\t\t</Company>\n");
                         }
                         $runningSumDebitAmount = 0;
                         $runningSumCreditAmount = 0;
                         $entryCounter = 1;
                         $lastPayrollCompanyID = $row["payroll_company_ID"];
                         $entryCollector = array();
                         $singleEmployeeData = "\t\t\t<Company>\n\t\t\t\t<CompanyID>".$row["payroll_company_ID"]."</CompanyID>\n\t\t\t\t<CompanyName>".$row["company_shortname"]."</CompanyName>\n\t\t\t\t<Entries>\n";
                     }
                     $runningSumDebitAmount += $row["debit_amount"];
                     $runningSumCreditAmount += $row["credit_amount"];
                     $corporationDebitAmount += $row["debit_amount"];
                     $corporationCreditAmount += $row["credit_amount"];
                     $entryCollector[] = "\t\t\t\t\t<Entry ".
                                             ($entryCounter % 30 == 0?
                                                 "doPageBreak=\"true\""
                                                 :
                                                 "doPageBreak=\"false\"").
                                        ">\n\t\t\t\t\t\t<CompanyID>".$row["payroll_company_ID"]."</CompanyID>\n\t\t\t\t\t\t<CostCenter>".$row["cost_center"]."</CostCenter>\n\t\t\t\t\t\t<DebitAmount>".$row["debit_amount"]."</DebitAmount>\n\t\t\t\t\t\t<CreditAmount>".$row["credit_amount"]."</CreditAmount>\n\t\t\t\t\t\t<EntryText>".$row["entry_text"]."</EntryText>\n\t\t\t\t\t\t<RunningSumDebitAmount>".$runningSumDebitAmount."</RunningSumDebitAmount>\n\t\t\t\t\t\t<RunningSumCreditAmount>".$runningSumCreditAmount."</RunningSumCreditAmount>\n\t\t\t\t\t</Entry>\n";
                     $entryCounter += 1;
                 }
                 if($singleEmployeeData != "") {
                     fwrite($fp, $singleEmployeeData.str_replace(array("&","_","%","#"), array("\\&","\\_","\\%","\\#"), implode("",$entryCollector))."\t\t\t</Entries>\n\t\t\t<CompanyDebitAmount>".$runningSumDebitAmount."</CompanyDebitAmount>\n\t\t\t<CompanyCreditAmount>".$runningSumCreditAmount."</CompanyCreditAmount>\n\t\t</Company>\n");
                 }
                 fwrite($fp, "\t\t</Companies>\n\t\t<CorporationDebitAmount>".$corporationDebitAmount."</CorporationDebitAmount>\n\t\t<CorporationCreditAmount>".$corporationCreditAmount."</CorporationCreditAmount>\n\t</Corporation>\n</Report>\n");
                 $fm->fclose();
                 break;
             case 5:
                 // Auswertung nach Firma / Konto
                 $reportTemplate = "AccountingJournal[Company][Account]"; 
                 
                 fwrite($fp, "<Report name=\"".$ReportName."\" lang=\"de\">\n\t<Header>\n\t\t<MainCompany>\n\t\t\t<Name>Testfirma AG</Name>\n\t\t\t<Street>Hauptstrasse 56</Street>\n\t\t\t<ZipCity>1234 Entenhausen</ZipCity>\n\t\t</MainCompany>\n\t\t<PrintDate>".date("d.m.Y")."</PrintDate>\n\t\t<PrintTime>".date("H:i:s")."</PrintTime>\n\t\t<Year>".$param["year"]."</Year>\n\t\t<Period>".$periodTitle."</Period>\n\t\t<AccountType>".$entryTable."</AccountType>\n\t</Header>\n\t<Corporation>\n\t\t<Companies>\n");
                 
                 $query = "  SELECT 
                                emp.payroll_company_ID,
                                comp.company_shortname,
                                accetry.account_no,
                                SUM(IF(accetry.debitcredit = 0, accetry.amount_local,0)) AS debit_amount,
	                            SUM(IF(accetry.debitcredit = 1, accetry.amount_local,0)) AS credit_amount,
                                accetry.entry_text
                            FROM
                                ".$entryTable." accetry
                                    INNER JOIN
                                payroll_employee emp ON emp.id = accetry.payroll_employee_ID
                                    INNER JOIN
                                payroll_company comp ON comp.id = emp.payroll_company_ID
									INNER JOIN
                                payroll_account_label acclbl ON acclbl.payroll_account_ID = accetry.payroll_account_ID
                                    AND acclbl.payroll_year_ID = ".$param["year"]."
                                    AND acclbl.language = '".session_control::getSessionInfo("language")."'
                            WHERE accetry.payroll_period_ID = ".$payrollPeriodID.
                            ($param["company"] == false ? "":" AND emp.payroll_company_ID = ".$param["company"])."
                            GROUP BY    emp.payroll_company_ID, 
                                        accetry.account_no, 
                                        accetry.entry_text
                            ORDER BY    emp.payroll_company_ID , 
                                        accetry.account_no";
                 $result = $system_database_manager->executeQuery($query, "payroll_report_".$ReportName);
                 $lastPayrollCompanyID = 0;
                 $runningSumDebitAmount = 0;
                 $runningSumCreditAmount = 0;
                 $corporationDebitAmount = 0;
                 $corporationCreditAmount = 0;
                 $entryCounter = 1;
                 $entryCollector = array();
                 $singleEmployeeData = "";
                 foreach($result as $row) {
                     if($row["payroll_company_ID"] != $lastPayrollCompanyID) {
                         //the employee changed!
                         if($singleEmployeeData != "") {
                             //there are data for writing to the XML file
                             fwrite($fp, $singleEmployeeData.str_replace(array("&","_","%","#"), array("\\&","\\_","\\%","\\#"), implode("",$entryCollector))."\t\t\t</Entries>\n\t\t\t<CompanyDebitAmount>".$runningSumDebitAmount."</CompanyDebitAmount>\n\t\t\t<CompanyCreditAmount>".$runningSumCreditAmount."</CompanyCreditAmount>\n\t\t</Company>\n");
                         }
                         $runningSumDebitAmount = 0;
                         $runningSumCreditAmount = 0;
                         $entryCounter = 1;
                         $lastPayrollCompanyID = $row["payroll_company_ID"];
                         $entryCollector = array();
                         $singleEmployeeData = "\t\t\t<Company>\n\t\t\t\t<CompanyID>".$row["payroll_company_ID"]."</CompanyID>\n\t\t\t\t<CompanyName>".$row["company_shortname"]."</CompanyName>\n\t\t\t\t<Entries>\n";
                     }
                     $runningSumDebitAmount += $row["debit_amount"];
                     $runningSumCreditAmount += $row["credit_amount"];
                     $corporationDebitAmount += $row["debit_amount"];
                     $corporationCreditAmount += $row["credit_amount"];
                     $entryCollector[] = "\t\t\t\t\t<Entry ".
                                             ($entryCounter % 30 == 0?
                                                 "doPageBreak=\"true\""
                                                 :
                                                 "doPageBreak=\"false\"").
                                        ">\n\t\t\t\t\t\t<CompanyID>".$row["payroll_company_ID"]."</CompanyID>\n\t\t\t\t\t\t<Account>".$row["account_no"]."</Account>\n\t\t\t\t\t\t<DebitAmount>".$row["debit_amount"]."</DebitAmount>\n\t\t\t\t\t\t<CreditAmount>".$row["credit_amount"]."</CreditAmount>\n\t\t\t\t\t\t<EntryText>".$row["entry_text"]."</EntryText>\n\t\t\t\t\t\t<RunningSumDebitAmount>".$runningSumDebitAmount."</RunningSumDebitAmount>\n\t\t\t\t\t\t<RunningSumCreditAmount>".$runningSumCreditAmount."</RunningSumCreditAmount>\n\t\t\t\t\t</Entry>\n";
                     $entryCounter += 1;
                 }
                 if($singleEmployeeData != "") {
                     fwrite($fp, $singleEmployeeData.str_replace(array("&","_","%","#"), array("\\&","\\_","\\%","\\#"), implode("",$entryCollector))."\t\t\t</Entries>\n\t\t\t<CompanyDebitAmount>".$runningSumDebitAmount."</CompanyDebitAmount>\n\t\t\t<CompanyCreditAmount>".$runningSumCreditAmount."</CompanyCreditAmount>\n\t\t</Company>\n");
                 }
                 fwrite($fp, "\n\t\t</Companies>\n\t\t<CorporationDebitAmount>".$corporationDebitAmount."</CorporationDebitAmount>\n\t\t<CorporationCreditAmount>".$corporationCreditAmount."</CorporationCreditAmount>\n\t</Corporation>\n</Report>\n");
                 $fm->fclose();
                 break;
             case 6:
                 // Auswertung nach Konto / Gegenkonto / Kst
                 $reportTemplate = "AccountingJournal[Account][Counter_account][Cost_center]"; 
                 
                 fwrite($fp, "<Report name=\"".$ReportName."\" lang=\"de\">\n\t<Header>\n\t\t<MainCompany>\n\t\t\t<Name>Testfirma AG</Name>\n\t\t\t<Street>Hauptstrasse 56</Street>\n\t\t\t<ZipCity>1234 Entenhausen</ZipCity>\n\t\t</MainCompany>\n\t\t<PrintDate>".date("d.m.Y")."</PrintDate>\n\t\t<PrintTime>".date("H:i:s")."</PrintTime>\n\t\t<Year>".$param["year"]."</Year>\n\t\t<Period>".$periodTitle."</Period>\n\t\t<AccountType>".$entryTable."</AccountType>\n\t</Header>\n\t<Corporation>\n\t\t<Entries>\n");
                 
                 $query = "  SELECT 
                                accetry.account_no,
                                accetry.counter_account_no,
                                accetry.cost_center,
                                SUM(IF(accetry.debitcredit = 0, accetry.amount_local,0)) AS debit_amount,
	                            SUM(IF(accetry.debitcredit = 1, accetry.amount_local,0)) AS credit_amount,
                                accetry.entry_text
                            FROM
                                ".$entryTable." accetry
                                    INNER JOIN
                                payroll_employee emp ON emp.id = accetry.payroll_employee_ID
                                    INNER JOIN
                                payroll_account_label acclbl ON acclbl.payroll_account_ID = accetry.payroll_account_ID
                                    AND acclbl.payroll_year_ID = ".$param["year"]."
                                    AND acclbl.language = '".session_control::getSessionInfo("language")."'
                            WHERE accetry.payroll_period_ID = ".$payrollPeriodID."
                            GROUP BY    accetry.account_no, 
                                        accetry.counter_account_no, 
                                        accetry.cost_center, 
                                        accetry.entry_text
                            ORDER BY    accetry.account_no , 
                                        accetry.counter_account_no , 
                                        accetry.cost_center";
                 $result = $system_database_manager->executeQuery($query, "payroll_report_".$ReportName);
                 $corporationDebitAmount = 0;
                 $corporationCreditAmount = 0;
                 $entryCounter = 1;
                 $singleEmployeeData = "";
                 foreach($result as $row) {
                     $corporationDebitAmount += $row["debit_amount"];
                     $corporationCreditAmount += $row["credit_amount"];
                     fwrite($fp, "\t\t\t<Entry ".
                                             ($entryCounter % 30 == 0?
                                                 "doPageBreak=\"true\""
                                                 :
                                                 "doPageBreak=\"false\"").
                                  ">\n\t\t\t\t<Account>".$row["account_no"]."</Account>\n\t\t\t\t<CounterAccount>".$row["counter_account_no"]."</CounterAccount>\n\t\t\t\t<CostCenter>".$row["cost_center"]."</CostCenter>\n\t\t\t\t<DebitAmount>".$row["debit_amount"]."</DebitAmount>\n\t\t\t\t<CreditAmount>".$row["credit_amount"]."</CreditAmount>\n\t\t\t\t<EntryText>".$row["entry_text"]."</EntryText>\n\t\t\t\t<RunningSumDebitAmount>".$corporationDebitAmount."</RunningSumDebitAmount>\n\t\t\t\t<RunningSumCreditAmount>".$corporationCreditAmount."</RunningSumCreditAmount>\n\t\t\t</Entry>\n");
                     $entryCounter += 1;
                 }
                 fwrite($fp, "\t\t</Entries>\n\t\t<CorporationDebitAmount>".$corporationDebitAmount."</CorporationDebitAmount>\n\t\t<CorporationCreditAmount>".$corporationCreditAmount."</CorporationCreditAmount>\n\t</Corporation>\n</Report>\n");
                 $fm->fclose();
                 break;
             case 7:
                 // Auswertung nach Kst / Konto / Gegenkonto
                 $reportTemplate = "AccountingJournal[Cost_center][Account][Counter_account]"; 
                 
                 fwrite($fp, "<Report name=\"".$ReportName."\" lang=\"de\">\n\t<Header>\n\t\t<MainCompany>\n\t\t\t<Name>Testfirma AG</Name>\n\t\t\t<Street>Hauptstrasse 56</Street>\n\t\t\t<ZipCity>1234 Entenhausen</ZipCity>\n\t\t</MainCompany>\n\t\t<PrintDate>".date("d.m.Y")."</PrintDate>\n\t\t<PrintTime>".date("H:i:s")."</PrintTime>\n\t\t<Year>".$param["year"]."</Year>\n\t\t<Period>".$periodTitle."</Period>\n\t\t<AccountType>".$entryTable."</AccountType>\n\t</Header>\n\t<Corporation>\n\t\t<Entries>\n");
                 
                 $query = "  SELECT 
                                accetry.account_no,
                                accetry.counter_account_no,
                                accetry.cost_center,
                                SUM(IF(accetry.debitcredit = 0, accetry.amount_local,0)) AS debit_amount,
	                            SUM(IF(accetry.debitcredit = 1, accetry.amount_local,0)) AS credit_amount,
                                accetry.entry_text
                            FROM
                                ".$entryTable." accetry
                                    INNER JOIN
                                payroll_employee emp ON emp.id = accetry.payroll_employee_ID
                                    INNER JOIN
                                payroll_account_label acclbl ON acclbl.payroll_account_ID = accetry.payroll_account_ID
                                    AND acclbl.payroll_year_ID = ".$param["year"]."
                                    AND acclbl.language = '".session_control::getSessionInfo("language")."'
                            WHERE accetry.payroll_period_ID = ".$payrollPeriodID.
                            ($param["cost_center"] == false ? "": " AND accetry.cost_center = ".$param["cost_center"])."
                            GROUP BY    accetry.cost_center, 
                                        accetry.account_no, 
                                        accetry.counter_account_no, 
                                        accetry.entry_text
                            ORDER BY    accetry.cost_center , 
                                        accetry.account_no , 
                                        accetry.counter_account_no";
                 $result = $system_database_manager->executeQuery($query, "payroll_report_".$ReportName);
                 $corporationDebitAmount = 0;
                 $corporationCreditAmount = 0;
                 $entryCounter = 1;
                 $singleEmployeeData = "";
                 foreach($result as $row) {
                     $corporationDebitAmount += $row["debit_amount"];
                     $corporationCreditAmount += $row["credit_amount"];
                     fwrite($fp, "\t\t\t<Entry ".
                                             ($entryCounter % 30 == 0?
                                                 "doPageBreak=\"true\""
                                                 :
                                                 "doPageBreak=\"false\"").
                                  ">\n\t\t\t\t<Account>".$row["account_no"]."</Account>\n\t\t\t\t<CounterAccount>".$row["counter_account_no"]."</CounterAccount>\n\t\t\t\t<CostCenter>".$row["cost_center"]."</CostCenter>\n\t\t\t\t<DebitAmount>".$row["debit_amount"]."</DebitAmount>\n\t\t\t\t<CreditAmount>".$row["credit_amount"]."</CreditAmount>\n\t\t\t\t<EntryText>".$row["entry_text"]."</EntryText>\n\t\t\t\t<RunningSumDebitAmount>".$corporationDebitAmount."</RunningSumDebitAmount>\n\t\t\t\t<RunningSumCreditAmount>".$corporationCreditAmount."</RunningSumCreditAmount>\n\t\t\t</Entry>\n");
                     $entryCounter += 1;
                 }
                 fwrite($fp, "\t\t</Entries>\n\t\t<CorporationDebitAmount>".$corporationDebitAmount."</CorporationDebitAmount>\n\t\t<CorporationCreditAmount>".$corporationCreditAmount."</CorporationCreditAmount>\n\t</Corporation>\n</Report>\n");
                 $fm->fclose();
                 break;
             case 8:
                 // Auswertung nach Konto / Gegenkonto
                 $reportTemplate = "AccountingJournal[Account][Counter_account]"; 
                 
                 fwrite($fp, "<Report name=\"".$ReportName."\" lang=\"de\">\n\t<Header>\n\t\t<MainCompany>\n\t\t\t<Name>Testfirma AG</Name>\n\t\t\t<Street>Hauptstrasse 56</Street>\n\t\t\t<ZipCity>1234 Entenhausen</ZipCity>\n\t\t</MainCompany>\n\t\t<PrintDate>".date("d.m.Y")."</PrintDate>\n\t\t<PrintTime>".date("H:i:s")."</PrintTime>\n\t\t<Year>".$param["year"]."</Year>\n\t\t<Period>".$periodTitle."</Period>\n\t\t<AccountType>".$entryTable."</AccountType>\n\t</Header>\n\t<Corporation>\n\t\t<Entries>\n");
                 
                 $query = "  SELECT 
                                accetry.account_no,
                                accetry.counter_account_no,
                                SUM(IF(accetry.debitcredit = 0, accetry.amount_local,0)) AS debit_amount,
	                            SUM(IF(accetry.debitcredit = 1, accetry.amount_local,0)) AS credit_amount,
                                accetry.entry_text
                            FROM
                                ".$entryTable." accetry
                                    INNER JOIN
                                payroll_employee emp ON emp.id = accetry.payroll_employee_ID
                                    INNER JOIN
                                payroll_account_label acclbl ON acclbl.payroll_account_ID = accetry.payroll_account_ID
                                    AND acclbl.payroll_year_ID = ".$param["year"]."
                                    AND acclbl.language = '".session_control::getSessionInfo("language")."'
                            WHERE accetry.payroll_period_ID = ".$payrollPeriodID."
                            GROUP BY    accetry.account_no, 
                                        accetry.counter_account_no, 
                                        accetry.entry_text
                            ORDER BY    accetry.account_no , 
                                        accetry.counter_account_no";
                 $result = $system_database_manager->executeQuery($query, "payroll_report_".$ReportName);
                 $corporationDebitAmount = 0;
                 $corporationCreditAmount = 0;
                 $entryCounter = 1;
                 $singleEmployeeData = "";
                 foreach($result as $row) {
                     $corporationDebitAmount += $row["debit_amount"];
                     $corporationCreditAmount += $row["credit_amount"];
                     fwrite($fp, "\t\t\t<Entry ".
                                             ($entryCounter % 30 == 0?
                                                 "doPageBreak=\"true\""
                                                 :
                                                 "doPageBreak=\"false\"").
                                  ">\n\t\t\t\t<Account>".$row["account_no"]."</Account>\n\t\t\t\t<CounterAccount>".$row["counter_account_no"]."</CounterAccount>\n\t\t\t\t<DebitAmount>".$row["debit_amount"]."</DebitAmount>\n\t\t\t\t<CreditAmount>".$row["credit_amount"]."</CreditAmount>\n\t\t\t\t<EntryText>".$row["entry_text"]."</EntryText>\n\t\t\t\t<RunningSumDebitAmount>".$corporationDebitAmount."</RunningSumDebitAmount>\n\t\t\t\t<RunningSumCreditAmount>".$corporationCreditAmount."</RunningSumCreditAmount>\n\t\t\t</Entry>\n");
                     $entryCounter += 1;
                 }
                 fwrite($fp, "\t\t</Entries>\n\t\t<CorporationDebitAmount>".$corporationDebitAmount."</CorporationDebitAmount>\n\t\t<CorporationCreditAmount>".$corporationCreditAmount."</CorporationCreditAmount>\n\t</Corporation>\n</Report>\n");
                 $fm->fclose();
                 break;
             case 9:
                 // Auswertung nach Kst
                 $reportTemplate = "AccountingJournal[Cost_center]"; 
                 
                 fwrite($fp, "<Report name=\"".$ReportName."\" lang=\"de\">\n\t<Header>\n\t\t<MainCompany>\n\t\t\t<Name>Testfirma AG</Name>\n\t\t\t<Street>Hauptstrasse 56</Street>\n\t\t\t<ZipCity>1234 Entenhausen</ZipCity>\n\t\t</MainCompany>\n\t\t<PrintDate>".date("d.m.Y")."</PrintDate>\n\t\t<PrintTime>".date("H:i:s")."</PrintTime>\n\t\t<Year>".$param["year"]."</Year>\n\t\t<Period>".$periodTitle."</Period>\n\t\t<AccountType>".$entryTable."</AccountType>\n\t</Header>\n\t<Corporation>\n\t\t<Entries>\n");
                 
                 $query = "  SELECT 
                                accetry.cost_center,
                                SUM(IF(accetry.debitcredit = 0, accetry.amount_local,0)) AS debit_amount,
	                            SUM(IF(accetry.debitcredit = 1, accetry.amount_local,0)) AS credit_amount,
                                accetry.debitcredit,
                                accetry.entry_text
                            FROM
                                ".$entryTable." accetry
                                    INNER JOIN
                                payroll_employee emp ON emp.id = accetry.payroll_employee_ID
                                    INNER JOIN
                                payroll_account_label acclbl ON acclbl.payroll_account_ID = accetry.payroll_account_ID
                                    AND acclbl.payroll_year_ID = ".$param["year"]."
                                    AND acclbl.language = '".session_control::getSessionInfo("language")."'
                            WHERE accetry.payroll_period_ID = ".$payrollPeriodID.
                            ($param["cost_center"] == false ? "": " AND accetry.cost_center = ".$param["cost_center"])."
                            GROUP BY    accetry.cost_center, 
                                        accetry.entry_text
                            ORDER BY    accetry.cost_center";
                 $result = $system_database_manager->executeQuery($query, "payroll_report_".$ReportName);
                 $corporationDebitAmount = 0;
                 $corporationCreditAmount = 0;
                 $entryCounter = 1;
                 $singleEmployeeData = "";
                 foreach($result as $row) {
                     $corporationDebitAmount += $row["debit_amount"];
                     $corporationCreditAmount += $row["credit_amount"];
                     fwrite($fp, "\t\t\t<Entry ".
                                             ($entryCounter % 30 == 0?
                                                 "doPageBreak=\"true\""
                                                 :
                                                 "doPageBreak=\"false\"").
                                  ">\n\t\t\t\t<CostCenter>".$row["cost_center"]."</CostCenter>\n\t\t\t\t<DebitAmount>".$row["debit_amount"]."</DebitAmount>\n\t\t\t\t<CreditAmount>".$row["credit_amount"]."</CreditAmount>\n\t\t\t\t<EntryText>".$row["entry_text"]."</EntryText>\n\t\t\t\t<RunningSumDebitAmount>".$corporationDebitAmount."</RunningSumDebitAmount>\n\t\t\t\t<RunningSumCreditAmount>".$corporationCreditAmount."</RunningSumCreditAmount>\n\t\t\t</Entry>\n");
                     $entryCounter += 1;
                 }
                 fwrite($fp, "\t\t</Entries>\n\t\t<CorporationDebitAmount>".$corporationDebitAmount."</CorporationDebitAmount>\n\t\t<CorporationCreditAmount>".$corporationCreditAmount."</CorporationCreditAmount>\n\t</Corporation>\n</Report>\n");
                 $fm->fclose();
                 break;
             case 10:
                 // Auswertung nach Konto
                 $reportTemplate = "AccountingJournal[Account]"; 
                 
                 fwrite($fp, "<Report name=\"".$ReportName."\" lang=\"de\">\n\t<Header>\n\t\t<MainCompany>\n\t\t\t<Name>Testfirma AG</Name>\n\t\t\t<Street>Hauptstrasse 56</Street>\n\t\t\t<ZipCity>1234 Entenhausen</ZipCity>\n\t\t</MainCompany>\n\t\t<PrintDate>".date("d.m.Y")."</PrintDate>\n\t\t<PrintTime>".date("H:i:s")."</PrintTime>\n\t\t<Year>".$param["year"]."</Year>\n\t\t<Period>".$periodTitle."</Period>\n\t\t<AccountType>".$entryTable."</AccountType>\n\t</Header>\n\t<Corporation>\n\t\t<Entries>\n");
                 
                 $query = "  SELECT 
                                accetry.account_no,
                                SUM(IF(accetry.debitcredit = 0, accetry.amount_local,0)) AS debit_amount,
	                            SUM(IF(accetry.debitcredit = 1, accetry.amount_local,0)) AS credit_amount,
                                accetry.entry_text
                            FROM
                                ".$entryTable." accetry
                                    INNER JOIN
                                payroll_employee emp ON emp.id = accetry.payroll_employee_ID
                                    INNER JOIN
                                payroll_account_label acclbl ON acclbl.payroll_account_ID = accetry.payroll_account_ID
                                    AND acclbl.payroll_year_ID = ".$param["year"]."
                                    AND acclbl.language = '".session_control::getSessionInfo("language")."'
                            WHERE accetry.payroll_period_ID = ".$payrollPeriodID."
                            GROUP BY    accetry.account_no, 
                                        accetry.entry_text
                            ORDER BY    accetry.account_no";
                 $result = $system_database_manager->executeQuery($query, "payroll_report_".$ReportName);
                 $corporationDebitAmount = 0;
                 $corporationCreditAmount = 0;
                 $entryCounter = 1;
                 $singleEmployeeData = "";
                 foreach($result as $row) {
                     $corporationDebitAmount += $row["debit_amount"];
                     $corporationCreditAmount += $row["credit_amount"];
                     fwrite($fp, "\t\t\t<Entry ".
                                             ($entryCounter % 30 == 0?
                                                 "doPageBreak=\"true\""
                                                 :
                                                 "doPageBreak=\"false\"").
                                  ">\n\t\t\t\t<Account>".$row["account_no"]."</Account>\n\t\t\t\t<DebitAmount>".$row["debit_amount"]."</DebitAmount>\n\t\t\t\t<CreditAmount>".$row["credit_amount"]."</CreditAmount>\n\t\t\t\t<EntryText>".$row["entry_text"]."</EntryText>\n\t\t\t\t<RunningSumDebitAmount>".$corporationDebitAmount."</RunningSumDebitAmount>\n\t\t\t\t<RunningSumCreditAmount>".$corporationCreditAmount."</RunningSumCreditAmount>\n\t\t\t</Entry>\n");
                     $entryCounter += 1;
                 }
                 fwrite($fp, "\t\t</Entries>\n\t\t<CorporationDebitAmount>".$corporationDebitAmount."</CorporationDebitAmount>\n\t\t<CorporationCreditAmount>".$corporationCreditAmount."</CorporationCreditAmount>\n\t</Corporation>\n</Report>\n");
                 $fm->fclose();
                 break;
        }
        
		chdir($newTmpPath);
        
        system($aafwConfig["paths"]["utilities"]["xsltproc"]." ".$aafwConfig["paths"]["reports"]["templates"].$reportTemplate.".xslt ./data.xml > ./compileme.tex");
        
		system($aafwConfig["paths"]["utilities"]["pdflatex"]." -interaction=batchmode compileme.tex > ".$aafwConfig["paths"]["utilities"]["stdout"]);
		system($aafwConfig["paths"]["utilities"]["pdflatex"]." -interaction=batchmode compileme.tex > ".$aafwConfig["paths"]["utilities"]["stdout"]);
        
		system("chmod 666 *");

//		system("rm compileme.aux");
//		system("rm compileme.log");

		return $newTmpDirName;
	}
    
	public function PayrollAccountJournal($param) {
        require_once(getcwd()."/kernel/common-functions/configuration.php");
        global $aafwConfig;
		ini_set('memory_limit', '512M');

		//mandatory und validity checks...
		if(!preg_match( '/^[0-9]{1,9}$/', $param["year"])) {
			$response["success"] = false;
			$response["errCode"] = 10;
			$response["errText"] = "invalid year";
			return $response;
		}
		if(!is_array($param["employees"]) || count($param["employees"])<1) {
			$response["success"] = false;
			$response["errCode"] = 20;
			$response["errText"] = "no employee id array";
			return $response;
		}
		$param["employees"] = array_unique($param["employees"]);
		foreach($param["employees"] as $emplID) {
			if(!preg_match( '/^[0-9]{1,9}$/', $emplID)) {
				$response["success"] = false;
				$response["errCode"] = 30;
				$response["errText"] = "invalid employee id";
				return $response;
			}
		}

		$fm = new file_manager();
		$newTmpDirName = $fm->createTmpDir();
		$newTmpPath = $fm->getFullPath();
		$fm->setFile("metadata.dat")->putContents( serialize(array("fileFormat"=>"pdf","realFileName"=>"compileme.pdf","transmissionFileName"=>"PayrollAccountJournal".$param["year"].".pdf")) );
		
//		if($param["majorPeriod"]<13 || $param["majorPeriod"]>14) $periodTitle = $param["majorPeriod"]." (".$periodLabels[session_control::getSessionInfo("language")][$param["majorPeriod"]].")";
//		else $periodTitle = $param["majorPeriod"];

		$system_database_manager = system_database_manager::getInstance();

		$system_database_manager->executeUpdate("DELETE FROM `payroll_tmp_change_mng` WHERE `core_user_ID`=".session_control::getSessionInfo("id"), "payroll_XXX");
		$system_database_manager->executeUpdate("INSERT INTO `payroll_tmp_change_mng`(`core_user_ID`,`numID`,`alphID`) SELECT ".session_control::getSessionInfo("id").",`id`,'' FROM `payroll_employee` WHERE `id` IN (".implode(",", $param["employees"]).")", "payroll_XXX");

		//Pruefen, ob es Hauptzahltagsperioden>12 gibt, denn fuer diese muessen wir zusaetzliche Spalten in der Auswertung darstellen
		$extendedMonthView = "";
		$result = $system_database_manager->executeQuery("SELECT `major_period` FROM `payroll_period` WHERE `payroll_year_ID`=".$param["year"]." AND `major_period`>12 GROUP BY `major_period`", "payroll_report_CalculationJournal");
		foreach($result as $row) $extendedMonthView .= "<Prd".$row["major_period"]."/>";

		//Ermitteln, ob das angeforderte Jahr dem aktuellen Abrechnungsjahr entspricht oder nicht (hat Einfluss auf die Wahl des Query)
		$result = $system_database_manager->executeQuery("SELECT MAX(`id`) as currentYear FROM `payroll_year`", "payroll_report_CalculationJournal");
		$isCurrentYear = $param["year"]==$result[0]["currentYear"] ? true : false;

		if($isCurrentYear) {
			//aktuelle PeriodenID holen (wird fuer Query benoetigt)
			$result = $system_database_manager->executeQuery("SELECT `payroll_period_ID` FROM `payroll_calculation_current` LIMIT 1", "payroll_report_CalculationJournal");
			$payrollPeriodID = $result[0]["payroll_period_ID"];
		}

		//Mitarbeiterdaten in einen assoziativen Array einlesen
		$arrEmployees = array();
		$result = $system_database_manager->executeQuery("SELECT emp.`id`, emp.`EmployeeNumber`, emp.`Firstname`, emp.`Lastname`, emp.`payroll_company_ID`, emp.`CodeAHV`, emp.`CodeALV`, emp.`CodeUVG`, emp.`CodeUVGZ1`, emp.`CodeUVGZ2`, emp.`CodeBVG`, emp.`CodeKTG`, emp.`DateOfBirth`, IF(`SV-AS-Number`!='',`SV-AS-Number`,`AHV-AVS-Number`) as `SV-AS-Number` FROM `payroll_employee` emp INNER JOIN `payroll_period_employee` ppe ON ppe.payroll_employee_ID=emp.id INNER JOIN `payroll_period` prd ON prd.`id`=ppe.`payroll_period_ID` AND prd.`payroll_year_ID`=".$param["year"]." INNER JOIN payroll_tmp_change_mng emplst ON emplst.core_user_id=".session_control::getSessionInfo("id")." AND emplst.numID=ppe.payroll_employee_ID WHERE ppe.`processing`!=0 GROUP BY emp.`id`", "payroll_report_CalculationJournal");
		foreach($result as $row) $arrEmployees["id".$row["id"]] = $row;

		//Beschaeftigungsdaten (Ein-/Austritte) pro Mitarbeiter in einen assoziativen Array einlesen
		$arrEmployeePeriods = array();
		$result = $system_database_manager->executeQuery("SELECT pep.`payroll_employee_ID`, pep.`DateFrom`, IF(pep.`DateTo`!='0000-00-00',pep.`DateTo`,'') as DateTo FROM `payroll_employment_period` pep INNER JOIN `payroll_tmp_change_mng` emplst ON pep.`payroll_employee_ID`=emplst.`numID` AND emplst.`core_user_id`=".session_control::getSessionInfo("id")." WHERE pep.`DateFrom`<='".$param["year"]."-12-31' AND (pep.`DateTo`>='".$param["year"]."-01-01' OR pep.`DateTo`='0000-00-00') ORDER BY pep.`payroll_employee_ID`, pep.`DateFrom`", "payroll_report_CalculationJournal");
		foreach($result as $row) if(isset($arrEmployeePeriods["id".$row["payroll_employee_ID"]])) $arrEmployeePeriods["id".$row["payroll_employee_ID"]][] = "\t\t\t\t<Period><From>".$row["DateFrom"]."</From><Until>".$row["DateTo"]."</Until></Period>\n"; else $arrEmployeePeriods["id".$row["payroll_employee_ID"]] = array("\t\t\t\t<Period><From>".$row["DateFrom"]."</From><Until>".$row["DateTo"]."</Until></Period>\n");

		$fp = $fm->setFile("data.xml")->fopen("w");

		fwrite($fp, "<Report name=\"PayrollAccountJournal\" lang=\"de\">\n\t<Header>\n\t\t<Company>\n\t\t\t<Name>Testfirma AG</Name>\n\t\t\t<Street>Hauptstrasse 56</Street>\n\t\t\t<ZipCity>1234 Entenhausen</ZipCity>\n\t\t</Company>\n\t\t<PrintDate>".date("d.m.Y")."</PrintDate>\n\t\t<PrintTime>".date("H:i:s")."</PrintTime>\n\t\t<Year>".$param["year"]."</Year>\n\t\t<ExtendedMonthView>".$extendedMonthView."</ExtendedMonthView>\n\t</Header>\n\t<Employees>\n");

		//TODO: $param Werte gegen intrusion sichern !!!!!

		//TODO: Moegliche, noch zu loesende Probleme: im aktuellen Jahr, ist die aktuelle Periode nicht (oder nur partiell) in "payroll_calculation_entry" gespeichert. Stattdessen sind die Werte aus "payroll_calculation_entry" zu beziehen; zugleich muessen die teilweise vorhandenen Daten in "payroll_calculation_entry" herausgefiltert werden.
		//TODO: Einzelne MA selektieren (ev. "payroll_tmp_change_mng" verwenden)
		if($isCurrentYear) $result = $system_database_manager->executeQuery("SELECT empl.id as EmployeeID, acc.id as AccountNumber, acc.print_account, acclbl.label, SUM(IF(prd.major_period=1,calc.quantity,0)) as prd1q, SUM(IF(prd.major_period=1,calc.amount,0)) as prd1a, SUM(IF(prd.major_period=2,calc.quantity,0)) as prd2q, SUM(IF(prd.major_period=2,calc.amount,0)) as prd2a, SUM(IF(prd.major_period=3,calc.quantity,0)) as prd3q, SUM(IF(prd.major_period=3,calc.amount,0)) as prd3a, SUM(IF(prd.major_period=4,calc.quantity,0)) as prd4q, SUM(IF(prd.major_period=4,calc.amount,0)) as prd4a, SUM(IF(prd.major_period=5,calc.quantity,0)) as prd5q, SUM(IF(prd.major_period=5,calc.amount,0)) as prd5a, SUM(IF(prd.major_period=6,calc.quantity,0)) as prd6q, SUM(IF(prd.major_period=6,calc.amount,0)) as prd6a, SUM(IF(prd.major_period=7,calc.quantity,0)) as prd7q, SUM(IF(prd.major_period=7,calc.amount,0)) as prd7a, SUM(IF(prd.major_period=8,calc.quantity,0)) as prd8q, SUM(IF(prd.major_period=8,calc.amount,0)) as prd8a, SUM(IF(prd.major_period=9,calc.quantity,0)) as prd9q, SUM(IF(prd.major_period=9,calc.amount,0)) as prd9a, SUM(IF(prd.major_period=10,calc.quantity,0)) as prd10q, SUM(IF(prd.major_period=10,calc.amount,0)) as prd10a, SUM(IF(prd.major_period=11,calc.quantity,0)) as prd11q, SUM(IF(prd.major_period=11,calc.amount,0)) as prd11a, SUM(IF(prd.major_period=12,calc.quantity,0)) as prd12q, SUM(IF(prd.major_period=12,calc.amount,0)) as prd12a, SUM(IF(prd.major_period=13,calc.quantity,0)) as prd13q, SUM(IF(prd.major_period=13,calc.amount,0)) as prd13a, SUM(IF(prd.major_period=14,calc.quantity,0)) as prd14q, SUM(IF(prd.major_period=14,calc.amount,0)) as prd14a, SUM(IF(prd.major_period=15,calc.quantity,0)) as prd15q, SUM(IF(prd.major_period=15,calc.amount,0)) as prd15a, SUM(IF(prd.major_period=16,calc.quantity,0)) as prd16q, SUM(IF(prd.major_period=16,calc.amount,0)) as prd16a FROM (SELECT * FROM payroll_calculation_entry INNER JOIN payroll_tmp_change_mng emplst ON emplst.core_user_id=".session_control::getSessionInfo("id")." AND emplst.numID=payroll_calculation_entry.payroll_employee_ID WHERE payroll_year_ID=".$param["year"]." AND payroll_period_ID!=".$payrollPeriodID." UNION SELECT * FROM payroll_calculation_current INNER JOIN payroll_tmp_change_mng emplst ON emplst.core_user_id=".session_control::getSessionInfo("id")." AND emplst.numID=payroll_calculation_current.payroll_employee_ID) calc INNER JOIN payroll_employee empl ON empl.id=calc.payroll_employee_ID INNER JOIN payroll_account acc ON acc.id=calc.payroll_account_ID AND acc.payroll_year_ID=calc.payroll_year_ID AND acc.print_account!=0 INNER JOIN payroll_account_label acclbl ON acclbl.payroll_account_ID=acc.id AND acclbl.payroll_year_ID=acc.payroll_year_ID AND acclbl.language='".session_control::getSessionInfo("language")."' INNER JOIN payroll_period prd ON prd.id=calc.payroll_period_ID GROUP BY calc.payroll_employee_ID, acc.id ORDER BY empl.Lastname, empl.Firstname, calc.payroll_employee_ID, acc.id", "payroll_report_CalculationJournal");
		else $result = $system_database_manager->executeQuery("SELECT empl.id as EmployeeID, acc.id as AccountNumber, acc.print_account, acclbl.label, SUM(IF(prd.major_period=1,calc.quantity,0)) as prd1q, SUM(IF(prd.major_period=1,calc.amount,0)) as prd1a, SUM(IF(prd.major_period=2,calc.quantity,0)) as prd2q, SUM(IF(prd.major_period=2,calc.amount,0)) as prd2a, SUM(IF(prd.major_period=3,calc.quantity,0)) as prd3q, SUM(IF(prd.major_period=3,calc.amount,0)) as prd3a, SUM(IF(prd.major_period=4,calc.quantity,0)) as prd4q, SUM(IF(prd.major_period=4,calc.amount,0)) as prd4a, SUM(IF(prd.major_period=5,calc.quantity,0)) as prd5q, SUM(IF(prd.major_period=5,calc.amount,0)) as prd5a, SUM(IF(prd.major_period=6,calc.quantity,0)) as prd6q, SUM(IF(prd.major_period=6,calc.amount,0)) as prd6a, SUM(IF(prd.major_period=7,calc.quantity,0)) as prd7q, SUM(IF(prd.major_period=7,calc.amount,0)) as prd7a, SUM(IF(prd.major_period=8,calc.quantity,0)) as prd8q, SUM(IF(prd.major_period=8,calc.amount,0)) as prd8a, SUM(IF(prd.major_period=9,calc.quantity,0)) as prd9q, SUM(IF(prd.major_period=9,calc.amount,0)) as prd9a, SUM(IF(prd.major_period=10,calc.quantity,0)) as prd10q, SUM(IF(prd.major_period=10,calc.amount,0)) as prd10a, SUM(IF(prd.major_period=11,calc.quantity,0)) as prd11q, SUM(IF(prd.major_period=11,calc.amount,0)) as prd11a, SUM(IF(prd.major_period=12,calc.quantity,0)) as prd12q, SUM(IF(prd.major_period=12,calc.amount,0)) as prd12a, SUM(IF(prd.major_period=13,calc.quantity,0)) as prd13q, SUM(IF(prd.major_period=13,calc.amount,0)) as prd13a, SUM(IF(prd.major_period=14,calc.quantity,0)) as prd14q, SUM(IF(prd.major_period=14,calc.amount,0)) as prd14a, SUM(IF(prd.major_period=15,calc.quantity,0)) as prd15q, SUM(IF(prd.major_period=15,calc.amount,0)) as prd15a, SUM(IF(prd.major_period=16,calc.quantity,0)) as prd16q, SUM(IF(prd.major_period=16,calc.amount,0)) as prd16a FROM payroll_calculation_entry calc INNER JOIN payroll_employee empl ON empl.id=calc.payroll_employee_ID INNER JOIN payroll_account acc ON acc.id=calc.payroll_account_ID AND acc.payroll_year_ID=calc.payroll_year_ID AND acc.print_account!=0 INNER JOIN payroll_account_label acclbl ON acclbl.payroll_account_ID=acc.id AND acclbl.payroll_year_ID=acc.payroll_year_ID AND acclbl.language='".session_control::getSessionInfo("language")."' INNER JOIN payroll_period prd ON prd.id=calc.payroll_period_ID INNER JOIN payroll_tmp_change_mng emplst ON emplst.core_user_id=".session_control::getSessionInfo("id")." AND emplst.numID=calc.payroll_employee_ID WHERE calc.payroll_year_ID=".$param["year"]." GROUP BY calc.payroll_employee_ID, acc.id ORDER BY empl.Lastname, empl.Firstname, calc.payroll_employee_ID, acc.id", "payroll_report_CalculationJournal");
		$lastEmployeeID = 0;
		$entryCollector = array();
		$singleEmployeeData = "";
		foreach($result as $row) {
			if($row["EmployeeID"] != $lastEmployeeID) {
				//the employee changed!
				if($singleEmployeeData != "") {
					//there are data for writing to the XML file
					fwrite($fp, $singleEmployeeData.str_replace(array("&","_","%","#"), array("\\&","\\_","\\%","\\#"), implode("",$entryCollector))."\t\t\t</Entries>\n\t\t</Employee>\n");
				}
				$curEmplData = $arrEmployees["id".$row["EmployeeID"]];
				$lastEmployeeID = $row["EmployeeID"];
				$entryCollector = array();
				$singleEmployeeData = "\t\t<Employee>\n\t\t\t<EmployeeNumber>".$curEmplData["EmployeeNumber"]."</EmployeeNumber>\n\t\t\t<CompanyID>".$curEmplData["payroll_company_ID"]."</CompanyID>\n\t\t\t<Firstname>".$curEmplData["Firstname"]."</Firstname>\n\t\t\t<Lastname>".$curEmplData["Lastname"]."</Lastname>\n\t\t\t<DateOfBirth>".$curEmplData["DateOfBirth"]."</DateOfBirth>\n\t\t\t<SV-AS-Number>".$curEmplData["SV-AS-Number"]."</SV-AS-Number>\n\t\t\t<CodeAHV>".$curEmplData["CodeAHV"]."</CodeAHV>\n\t\t\t<CodeALV>".$curEmplData["CodeALV"]."</CodeALV>\n\t\t\t<CodeKTG>".$curEmplData["CodeKTG"]."</CodeKTG>\n\t\t\t<CodeUVG>".$curEmplData["CodeUVG"]."</CodeUVG>\n\t\t\t<CodeBVG>".$curEmplData["CodeBVG"]."</CodeBVG>\n\t\t\t<EmploymentPeriods>".implode("",$arrEmployeePeriods["id".$row["EmployeeID"]])."</EmploymentPeriods>\n\t\t\t<Entries>\n";
			}
			if($row["print_account"]!=1) {
				$rowTotal = 0.0;
				for($i=1;$i<17;$i++) $rowTotal += $row["prd".$i."q"];
				$tagQuantity = "\t\t\t\t\t<quantity><Jan>".$row["prd1q"]."</Jan><Feb>".$row["prd2q"]."</Feb><Mar>".$row["prd3q"]."</Mar><Apr>".$row["prd4q"]."</Apr><May>".$row["prd5q"]."</May><June>".$row["prd6q"]."</June><July>".$row["prd7q"]."</July><Aug>".$row["prd8q"]."</Aug><Sept>".$row["prd9q"]."</Sept><Oct>".$row["prd10q"]."</Oct><Nov>".$row["prd11q"]."</Nov><Dec>".$row["prd12q"]."</Dec><Prd13>".$row["prd13q"]."</Prd13><Prd14>".$row["prd14q"]."</Prd14><Prd15>".$row["prd15q"]."</Prd15><Prd16>".$row["prd16q"]."</Prd16><Total>".$rowTotal."</Total></quantity>\n";
//error_log($tagQuantity, 3, "/var/log/daniel.log");
			}else $tagQuantity = "";
			if($row["print_account"]!=2) {
				$rowTotal = 0.0;
				for($i=1;$i<17;$i++) $rowTotal += $row["prd".$i."a"];
				$tagAmount = "\t\t\t\t\t<amount><Jan>".$row["prd1a"]."</Jan><Feb>".$row["prd2a"]."</Feb><Mar>".$row["prd3a"]."</Mar><Apr>".$row["prd4a"]."</Apr><May>".$row["prd5a"]."</May><June>".$row["prd6a"]."</June><July>".$row["prd7a"]."</July><Aug>".$row["prd8a"]."</Aug><Sept>".$row["prd9a"]."</Sept><Oct>".$row["prd10a"]."</Oct><Nov>".$row["prd11a"]."</Nov><Dec>".$row["prd12a"]."</Dec><Prd13>".$row["prd13a"]."</Prd13><Prd14>".$row["prd14a"]."</Prd14><Prd15>".$row["prd15a"]."</Prd15><Prd16>".$row["prd16a"]."</Prd16><Total>".$rowTotal."</Total></amount>\n";
			}else $tagAmount = "";
			$entryCollector[] = "\t\t\t\t<Entry>\n\t\t\t\t\t<AccountNumber>".$row["AccountNumber"]."</AccountNumber>\n\t\t\t\t\t<AccountName>".$row["label"]."</AccountName>\n".$tagQuantity.$tagAmount."\t\t\t\t</Entry>\n";
		}
		if($singleEmployeeData != "") {
			//there are still a few more data for writing to the XML file
			fwrite($fp, $singleEmployeeData.str_replace(array("&","_","%","#"), array("\\&","\\_","\\%","\\#"), implode("",$entryCollector))."\t\t\t</Entries>\n\t\t</Employee>\n");
		}
		fwrite($fp, "\t</Employees>\n</Report>\n");
		$fm->fclose();
//		fclose($fp);

		chdir($newTmpPath);

//		system("cp ./data.xml /usr/local/www/apache22/data-hidden/CUSTOMER/development/tmp/1/test/"); //TODO: Diese Zeile wieder loeschen...

        system($aafwConfig["paths"]["utilities"]["xsltproc"]." ".$aafwConfig["paths"]["reports"]["templates"]."PayrollAccountJournal.xslt ./data.xml > ./compileme.tex");

//		system("cp ./compileme.tex /usr/local/www/apache22/data-hidden/CUSTOMER/development/tmp/1/test/"); //TODO: Diese Zeile wieder loeschen...

        system($aafwConfig["paths"]["utilities"]["pdflatex"]." -interaction=batchmode compileme.tex > ".$aafwConfig["paths"]["utilities"]["stdout"]);
		system($aafwConfig["paths"]["utilities"]["pdflatex"]." -interaction=batchmode compileme.tex > ".$aafwConfig["paths"]["utilities"]["stdout"]);
		system("chmod 666 *");

//		system("rm compileme.aux");
//		system("rm compileme.log");

		return $newTmpDirName;
	}

	public function Payslip($param) {
        require_once(getcwd()."/kernel/common-functions/configuration.php");
        global $aafwConfig;
		$payrollPeriodID = $param["payroll_period_ID"];
		$uid = session_control::getSessionInfo("id");
		$language = session_control::getSessionInfo("language");
		$cashPayment = array("de" => "Barauszahlung", "en" => "Cash payment", "fr" => "Paiement en espÃ¨ces", "it" => "Pagamento in contanti");

		$system_database_manager = system_database_manager::getInstance();

		$countryNames = array();
		$result = $system_database_manager->executeQuery("SELECT `core_intl_country_ID`,`country_name` FROM `core_intl_country_names` WHERE (`country_name_language`='en' AND `core_intl_country_ID` NOT IN ('AT','CH','DE','FR','IT','LI')) OR (`country_name_language`='".$language."' AND `core_intl_country_ID` IN ('AT','CH','DE','FR','IT','LI'))", "payroll_report_Payslip");
		foreach($result as $row) $countryNames[$row["core_intl_country_ID"]] = $row["country_name"];
		unset($result);

		$notifications = array();
		$result = $system_database_manager->executeQuery("SELECT `payroll_company_ID`,`employee_notification`,`language` FROM `payroll_payslip_notice` WHERE `payroll_period_ID`=".$payrollPeriodID, "payroll_report_Payslip");
		foreach($result as $row) $notifications[(string)$row["payroll_company_ID"]][$row["language"]] = str_replace("\n", "<br/>", $row["employee_notification"]);
		unset($result);

		$periodInfo = array();
		$result = $system_database_manager->executeQuery("SELECT `payroll_period`.*, LAST_DAY(CONCAT(`payroll_year_ID`, '-', `major_period_associated`, '-01')) as `lastDayOfPeriod` FROM `payroll_period` WHERE `id`=".$payrollPeriodID, "payroll_report_Payslip");
		foreach($result[0] as $fldName=>$fldValue) $periodInfo[$fldName] = $fldValue;
		unset($result);
		if($periodInfo["locked"]==0 && $periodInfo["finalized"]==0) $tableNameSuffix = "current";
		else $tableNameSuffix = "entry";

		$payments = array();
		$result = $system_database_manager->executeQuery("SELECT paym.`payroll_employee_ID`, IF(empl.`Language`='','de',empl.`Language`) as `Language`, bankdst.`destination_type`, bankdst.`beneficiary_bank_line1`, bankdst.`beneficiary_bank_line2`, bankdst.`beneficiary_bank_line3`, bankdst.`beneficiary_bank_line4`, bankdst.`bank_account`, bankdst.`postfinance_account`, paym.`payroll_currency_ID` as `currency`, FORMAT(paym.`amount_payout`,2) as `amount` FROM `payroll_payment_current` paym INNER JOIN `payroll_tmp_change_mng` emplist ON emplist.`numID`=paym.`payroll_employee_ID` AND emplist.`core_user_id`=".$uid." INNER JOIN `payroll_payment_split` spltcfg ON spltcfg.`id`=paym.`payroll_payment_split_ID` INNER JOIN `payroll_bank_destination` bankdst ON bankdst.`id`=spltcfg.`payroll_bank_destination_ID` INNER JOIN `payroll_employee` empl ON empl.`id`=paym.`payroll_employee_ID` WHERE paym.`payroll_period_ID`=".$payrollPeriodID, "payroll_report_Payslip");
		foreach($result as $row) {
			if(!isset($payments[(string)$row["payroll_employee_ID"]])) $payments[(string)$row["payroll_employee_ID"]] = array();
			switch($row["destination_type"]) {
			case 1: $bankName = $row["beneficiary_bank_line1"]; $bankAccount = $row["bank_account"]; break; //Bank
			case 2: $bankName = "Postfinance"; $bankAccount = $row["postfinance_account"]; break; //Postfinance
			case 3: $bankName = $cashPayment[$row["Language"]]; $bankAccount = ""; break; //Barauszahlung
			}
			$payments[(string)$row["payroll_employee_ID"]][] = "\t\t\t\t<Payout>\n\t\t\t\t\t<BankAddrLine1>".$bankName."</BankAddrLine1>\n\t\t\t\t\t<BankAddrLine2>".$row["beneficiary_bank_line2"]."</BankAddrLine2>\n\t\t\t\t\t<BankAddrLine3>".$row["beneficiary_bank_line3"]."</BankAddrLine3>\n\t\t\t\t\t<BankAddrLine4>".$row["beneficiary_bank_line4"]."</BankAddrLine4>\n\t\t\t\t\t<BankAccountNo>".$bankAccount."</BankAccountNo>\n\t\t\t\t\t<PayoutCurrency>".$row["currency"]."</PayoutCurrency>\n\t\t\t\t\t<PayoutAmount>".$row["amount"]."</PayoutAmount>\n\t\t\t\t</Payout>\n";
		}
		unset($result);

		$payslipInfo = array();
		$result = $system_database_manager->executeQuery("SELECT `payroll_payslip_cfg_ID`,`label`,`language`,`field_type`,`field_name` FROM `payroll_payslip_cfg_info` ORDER BY `position`", "payroll_report_Payslip");
		foreach($result as $row) {
			if(!isset($payslipInfo[(string)$row["payroll_payslip_cfg_ID"]][$row["language"]])) $payslipInfo[(string)$row["payroll_payslip_cfg_ID"]][$row["language"]] = array();
			$payslipInfo[(string)$row["payroll_payslip_cfg_ID"]][$row["language"]][] = array("field_type"=>$row["field_type"], "field_name"=>$row["field_name"], "label"=>$row["label"]);
		}
		unset($result);

		$payslipCfg = array();
		$result = $system_database_manager->executeQuery("SELECT * FROM `payroll_payslip_cfg`", "payroll_report_Payslip");
		foreach($result as $row) {
			$payslipCfg[(string)$row["id"]] = $row;
			$payslipCfg[(string)$row["id"]]["InfoFields"] = $payslipInfo[(string)$row["id"]];
			if($row["default_payslip"]==1) {
				$payslipCfg[(string)"0"] = $row;
				$payslipCfg[(string)"0"]["InfoFields"] = $payslipInfo[(string)$row["id"]];
			}
		}
		unset($result);
		unset($payslipInfo);
//error_log(print_r($payslipCfg,true), 3, "/var/log/daniel.log");

		$employeeFieldsOfInterest = array("id", "EmployeeNumber", "Lastname", "Firstname", "AdditionalAddrLine1", "AdditionalAddrLine2", "AdditionalAddrLine3", "AdditionalAddrLine4", "Street", "ZIP-Code", "City", "Country", "payroll_company_ID", "Language", "payroll_payslip_cfg_ID", "WageCode");
		$result = $system_database_manager->executeQuery("SELECT DISTINCT `field_name` FROM `payroll_payslip_cfg_info` WHERE `field_type`=0", "payroll_report_Payslip");
		foreach($result as $row) $employeeFieldsOfInterest[] = $row["field_name"];
		unset($result);
		$employeeFieldsOfInterest = array_unique($employeeFieldsOfInterest);

		$infoByWageCode = array();
		$infoByWageCode["0"] = "\t\t\t<Year>".$periodInfo["payroll_year_ID"]."</Year>\n\t\t\t<PeriodNumber>".$periodInfo["major_period"]."</PeriodNumber>\n\t\t\t<PeriodNumberAssoc>".$periodInfo["major_period_associated"]."</PeriodNumberAssoc>\n\t\t\t<PeriodStartDate>".$periodInfo["Wage_DateFrom"]."</PeriodStartDate>\n\t\t\t<PeriodEndDate>".$periodInfo["Wage_DateTo"]."</PeriodEndDate>\n";	//keine Zuweisung -> Daten von Monatslohn
		$infoByWageCode["1"] = "\t\t\t<Year>".$periodInfo["payroll_year_ID"]."</Year>\n\t\t\t<PeriodNumber>".$periodInfo["major_period"]."</PeriodNumber>\n\t\t\t<PeriodNumberAssoc>".$periodInfo["major_period_associated"]."</PeriodNumberAssoc>\n\t\t\t<PeriodStartDate>".$periodInfo["HourlyWage_DateFrom"]."</PeriodStartDate>\n\t\t\t<PeriodEndDate>".$periodInfo["HourlyWage_DateTo"]."</PeriodEndDate>\n"; //Stundenlohn
		$infoByWageCode["2"] = "\t\t\t<Year>".$periodInfo["payroll_year_ID"]."</Year>\n\t\t\t<PeriodNumber>".$periodInfo["major_period"]."</PeriodNumber>\n\t\t\t<PeriodNumberAssoc>".$periodInfo["major_period_associated"]."</PeriodNumberAssoc>\n\t\t\t<PeriodStartDate>".$periodInfo["Wage_DateFrom"]."</PeriodStartDate>\n\t\t\t<PeriodEndDate>".$periodInfo["Wage_DateTo"]."</PeriodEndDate>\n";	//Monatslohn
		$infoByWageCode["3"] = "\t\t\t<Year>".$periodInfo["payroll_year_ID"]."</Year>\n\t\t\t<PeriodNumber>".$periodInfo["major_period"]."</PeriodNumber>\n\t\t\t<PeriodNumberAssoc>".$periodInfo["major_period_associated"]."</PeriodNumberAssoc>\n\t\t\t<PeriodStartDate>".$periodInfo["Salary_DateFrom"]."</PeriodStartDate>\n\t\t\t<PeriodEndDate>".$periodInfo["Salary_DateTo"]."</PeriodEndDate>\n"; //Gehalt
		$infoByWageCode["4"] = "\t\t\t<Year>".$periodInfo["payroll_year_ID"]."</Year>\n\t\t\t<PeriodNumber>".$periodInfo["major_period"]."</PeriodNumber>\n\t\t\t<PeriodNumberAssoc>".$periodInfo["major_period_associated"]."</PeriodNumberAssoc>\n\t\t\t<PeriodStartDate>".$periodInfo["payroll_year_ID"]."-".substr("0".$periodInfo["major_period_associated"],-2)."-01"."</PeriodStartDate>\n\t\t\t<PeriodEndDate>".$periodInfo["lastDayOfPeriod"]."</PeriodEndDate>\n";

		$arrFld = array();
		$listValues = array();
		foreach($employeeFieldsOfInterest as $fldName) $arrFld[] = "'".$fldName."'";
		$result = $system_database_manager->executeQuery("SELECT flddef.`fieldName`,IF(flddef.`dataSourceToken`=1,lstgrp.`ListItemToken`,lstgrp.`id`) as ListItemID, lstlbl.`language`, lstlbl.`label` FROM `payroll_employee_field_def` flddef INNER JOIN `payroll_empl_list` lstgrp ON lstgrp.`ListGroup`=flddef.`dataSourceGroup` INNER JOIN `payroll_empl_list_label` lstlbl ON lstlbl.`payroll_empl_list_ID`=lstgrp.`id` WHERE flddef.`fieldType`=4 AND flddef.`dataSource`='payroll_empl_list' AND flddef.`fieldName` IN (".implode(",",$arrFld).")", "payroll_report_Payslip");
		foreach($result as $row) $listValues[$row["fieldName"]][$row["ListItemID"]][$row["language"]] = $row["label"];
		unset($result);
		unset($arrFld);

		$arrFld = array();
		$employeeDetail = array();
		foreach($employeeFieldsOfInterest as $fldName) $arrFld[] = "emp.`".$fldName."`";
		$result = $system_database_manager->executeQuery("SELECT ".implode(",", $arrFld)." FROM `payroll_employee` emp INNER JOIN `payroll_tmp_change_mng` emplist ON emplist.`numID`=emp.`id` AND emplist.`core_user_id`=".$uid, "payroll_report_Payslip");
		foreach($result as $row) $employeeDetail[(string)$row["id"]] = $row;
		unset($arrFld);
		unset($result);

		$fm = new file_manager();
		$pdfTemplateDir = $fm->customerSpace()->setPath("TEMPLATE")->getFullPath();
		$newTmpDirName = $fm->createTmpDir();
		$newTmpPath = $fm->getFullPath();
		$fm->setFile("metadata.dat")->putContents( serialize(array("fileFormat"=>"pdf","realFileName"=>"compileme.pdf","transmissionFileName"=>"Payslip.pdf")) );

		$fp = $fm->setFile("data.xml")->fopen("w");
		fwrite($fp, "<Report name=\"Payslip\">\n\t<Employees>\n");

		//Bewegungsdaten auslesen
		$lastEmpId = 0;
		$entries = array();
		$result = $system_database_manager->executeQuery("SELECT calc.`payroll_employee_ID`, calc.`payroll_account_ID`, IF(calc.`label`!='',calc.`label`,IF(calc.`code`!='',CONCAT(acclbl.`label`,' ',calc.`code`),acclbl.`label`)) as `label`, IF(acc.`quantity_print`=1,FORMAT(calc.`quantity`*acc.`quantity_conversion`,IF(acc.`quantity_decimal`=10,2,acc.`quantity_decimal`)),NULL) as `quantity`, IF(acc.`quantity_print`=1 AND acclbl.`quantity_unit`!='',acclbl.`quantity_unit`,NULL) as `quantity_unit`, IF(acc.`rate_print`=1,FORMAT(calc.`rate`*acc.`rate_conversion`,IF(acc.`rate_decimal`=10,2,acc.`rate_decimal`)),NULL) as `rate`, IF(acc.`rate_print`=1 AND acclbl.`rate_unit`!='',acclbl.`rate_unit`,NULL) as `rate_unit`, IF(acc.`amount_print`=1,FORMAT(calc.`amount`*acc.`amount_conversion`,IF(acc.`amount_decimal`=10,2,acc.`amount_decimal`)),NULL) as `amount`, acc.`bold`, acc.`space_before`, acc.`space_after`, prdemp.`payment_date`, prdemp.`interest_date` FROM `payroll_period_employee` prdemp INNER JOIN `payroll_tmp_change_mng` emplist ON emplist.`numID`=prdemp.`payroll_employee_ID` AND emplist.`core_user_id`=".$uid." INNER JOIN `payroll_employee` emp ON emp.`id`=prdemp.`payroll_employee_ID` INNER JOIN `payroll_calculation_".$tableNameSuffix."` calc ON calc.`payroll_period_ID`=prdemp.`payroll_period_ID` AND calc.`payroll_employee_ID`=prdemp.`payroll_employee_ID` INNER JOIN `payroll_account` acc ON calc.`payroll_account_ID`=acc.`id` AND calc.`payroll_year_ID`=acc.`payroll_year_ID` AND (acc.`quantity_print`=1 OR acc.`rate_print`=1 OR acc.`amount_print`=1) LEFT JOIN `payroll_account_label` acclbl ON calc.`payroll_account_ID`=acclbl.`payroll_account_ID` AND calc.`payroll_year_ID`=acclbl.`payroll_year_ID` AND acclbl.`language`=emp.`Language` WHERE prdemp.`payroll_period_ID`=".$payrollPeriodID." AND prdemp.`processing`!=0 ORDER BY calc.`payroll_employee_ID`,calc.`position`,calc.`payroll_account_ID`", "payroll_report_Payslip");
		foreach($result as $row) {
			if($row["payroll_employee_ID"] != $lastEmpId) {
				if(count($entries)!=0) {
					$emplData = $employeeDetail[(string)$lastEmpId];
					$templCfg = $payslipCfg[(string)$emplData["payroll_payslip_cfg_ID"]];

					$infoFields = "";
					foreach($templCfg["InfoFields"][$emplData["Language"]] as $infoRow) {
						switch($infoRow["field_type"]) {
						case 0:
//$listValues[$row["fieldName"]][$row["ListItemID"]][$row["language"]] = $row["label"];
							if(isset($listValues[$infoRow["field_name"]])) {
								$curValue = $listValues[$infoRow["field_name"]][$emplData[$infoRow["field_name"]]][$emplData["Language"]];
							}else{
								$curValue = $emplData[$infoRow["field_name"]];
							}
							$infoFields .= "\t\t\t\t<Field>\n\t\t\t\t\t<Name>".$infoRow["field_name"]."</Name>\n\t\t\t\t\t<Label>".$infoRow["label"]."</Label>\n\t\t\t\t\t<Value>".$curValue."</Value>\n\t\t\t\t</Field>\n";
//							$infoFields .= "\t\t\t\t<Field>\n\t\t\t\t\t<Name>".$infoRow["field_name"]."</Name>\n\t\t\t\t\t<Label>".$infoRow["label"]."</Label>\n\t\t\t\t\t<Value>".$emplData[$infoRow["field_name"]]."</Value>\n\t\t\t\t</Field>\n";
							break;
						default:
							$infoFields .= "\t\t\t\t<Field>\n\t\t\t\t\t<Name>IFLD</Name>\n\t\t\t\t\t<Label>".$infoRow["label"]."</Label>\n\t\t\t\t\t<Value>".$infoRow["field_type"]."</Value>\n\t\t\t\t</Field>\n";
							break;
						}
					}
					$tmpNote = array();
					if(isset($notifications[(string)$emplData["payroll_company_ID"]][$emplData["Language"]])) $tmpNote[] = $notifications[(string)$emplData["payroll_company_ID"]][$emplData["Language"]];
					if(isset($notifications[(string)"0"][$emplData["Language"]])) $tmpNote[] = $notifications[(string)"0"][$emplData["Language"]];
					$curEmpl = "\t\t<Employee>\n\t\t\t<EmployeeNumber>".$emplData["EmployeeNumber"]."</EmployeeNumber>\n\t\t\t<CompanyID>".$emplData["payroll_company_ID"]."</CompanyID>\n\t\t\t<Firstname>".$emplData["Firstname"]."</Firstname>\n\t\t\t<Lastname>".$emplData["Lastname"]."</Lastname>\n".($emplData["AdditionalAddrLine1"]!="" ? "\t\t\t<AdditionalAddrLine1>".$emplData["AdditionalAddrLine1"]."</AdditionalAddrLine1>\n" : "").($emplData["AdditionalAddrLine2"]!="" ? "\t\t\t<AdditionalAddrLine2>".$emplData["AdditionalAddrLine2"]."</AdditionalAddrLine2>\n" : "").($emplData["AdditionalAddrLine3"]!="" ? "\t\t\t<AdditionalAddrLine3>".$emplData["AdditionalAddrLine3"]."</AdditionalAddrLine3>\n" : "").($emplData["AdditionalAddrLine4"]!="" ? "\t\t\t<AdditionalAddrLine4>".$emplData["AdditionalAddrLine4"]."</AdditionalAddrLine4>\n" : "")."\t\t\t<Street>".$emplData["Street"]."</Street>\n\t\t\t<ZIP-Code>".$emplData["ZIP-Code"]."</ZIP-Code>\n\t\t\t<City>".$emplData["City"]."</City>\n\t\t\t<Country>".$emplData["Country"]."</Country>\n\t\t\t<CountryName>".$countryNames[$emplData["Country"]]."</CountryName>\n".$infoByWageCode[$emplData["WageCode"]]."\t\t\t<PaymentDate>".$paymentDate."</PaymentDate>\n\t\t\t<InterestDate>".$interestDate."</InterestDate>\n".(count($tmpNote)!=0 ? "\t\t\t<Notification>".implode("<br/>",$tmpNote)."</Notification>\n" : "")."\t\t\t<DocumentSettings>\n\t\t\t\t<Language>".$emplData["Language"]."</Language>\n\t\t\t\t<DecimalPoint>.</DecimalPoint>\n\t\t\t\t<ThousandsSeparator>'</ThousandsSeparator>\n\t\t\t\t<PdfTemplate>".($templCfg["pdf_template"]!="" ? $pdfTemplateDir.$templCfg["pdf_template"] : "")."</PdfTemplate>\n\t\t\t\t<AddrOffsetLeft>".$templCfg["addr_offset_left"]."</AddrOffsetLeft>\n\t\t\t\t<AddrOffsetTop>".$templCfg["addr_offset_top"]."</AddrOffsetTop>\n\t\t\t\t<InfoOffsetLeft>".$templCfg["info_offset_left"]."</InfoOffsetLeft>\n\t\t\t\t<InfoOffsetTop>".$templCfg["info_offset_top"]."</InfoOffsetTop>\n\t\t\t\t<ContentOffsetLeft>".$templCfg["content_offset_left"]."</ContentOffsetLeft>\n\t\t\t\t<ContentOffsetTop>".$templCfg["content_offset_top"]."</ContentOffsetTop>\n\t\t\t\t<ContentWidth>".$templCfg["content_width"]."</ContentWidth>\n\t\t\t\t<AddrFontName>".$templCfg["addr_font_name"]."</AddrFontName>\n\t\t\t\t<AddrFontSize>".$templCfg["addr_font_size"]."</AddrFontSize>\n\t\t\t\t<InfoFontName>".$templCfg["info_font_name"]."</InfoFontName>\n\t\t\t\t<InfoFontSize>".$templCfg["info_font_size"]."</InfoFontSize>\n\t\t\t\t<ContentFontName>".$templCfg["content_font_name"]."</ContentFontName>\n\t\t\t\t<ContentFontSize>".$templCfg["content_font_size"]."</ContentFontSize>\n\t\t\t\t<ProcessingSuffix>".$templCfg["processing_suffix"]."</ProcessingSuffix>\n\t\t\t</DocumentSettings>\n\t\t\t<InfoFields>\n".$infoFields."\t\t\t</InfoFields>\n\t\t\t<Entries>\n".implode("",$entries)."\t\t\t</Entries>\n\t\t\t<Payouts>\n".(isset($payments[(string)$lastEmpId]) ? implode("", $payments[(string)$lastEmpId]) : "")."\t\t\t</Payouts>\n\t\t</Employee>\n";
					fwrite($fp, str_replace(array("&","_","%","#"), array("\\&","\\_","\\%","\\#"), $curEmpl) );
				}

				$lastEmpId = $row["payroll_employee_ID"];
				$entries = array();
			}
			$designAttributes = "";
			switch($row["space_before"]) {
			case 0:
				break;
			case 20:
				$designAttributes .= ' spaceBefore="small"'; break;
			case 40:
				$designAttributes .= ' spaceBefore="medium"'; break;
			case 60:
				$designAttributes .= ' spaceBefore="large"'; break;
			}
			switch($row["space_after"]) {
			case 0:
				break;
			case 20:
				$designAttributes .= ' spaceAfter="small"'; break;
			case 40:
				$designAttributes .= ' spaceAfter="medium"'; break;
			case 60:
				$designAttributes .= ' spaceAfter="large"'; break;
			}
			if($row["bold"]==1) $designAttributes .= ' bold="true"';
			$entries[] = "\t\t\t\t<Entry".$designAttributes.">\n\t\t\t\t\t<AccountNumber>".$row["payroll_account_ID"]."</AccountNumber>\n\t\t\t\t\t<AccountName>".$row["label"]."</AccountName>\n".($row["quantity"]===NULL ? "" : "\t\t\t\t\t<quantity>".$row["quantity"]."</quantity>\n").($row["quantity_unit"]===NULL ? "" : "\t\t\t\t\t<quantityUnit>".$row["quantity_unit"]."</quantityUnit>\n").($row["rate"]===NULL ? "" : "\t\t\t\t\t<rate>".$row["rate"]."</rate>\n").($row["rate_unit"]===NULL ? "" : "\t\t\t\t\t<rateUnit>".$row["rate_unit"]."</rateUnit>\n").($row["amount"]===NULL ? "" : "\t\t\t\t\t<amount>".$row["amount"]."</amount>\n")."\t\t\t\t</Entry>\n";
			$paymentDate = $row["payment_date"];
			$interestDate = $row["interest_date"];
		}
		if(count($entries)!=0) {
			$emplData = $employeeDetail[(string)$row["payroll_employee_ID"]];
			$templCfg = $payslipCfg[(string)$emplData["payroll_payslip_cfg_ID"]];

			$infoFields = "";
			foreach($templCfg["InfoFields"][$emplData["Language"]] as $infoRow) {
				switch($infoRow["field_type"]) {
				case 0:
					if(isset($listValues[$infoRow["field_name"]])) {
						$curValue = $listValues[$infoRow["field_name"]][$emplData[$infoRow["field_name"]]][$emplData["Language"]];
					}else{
						$curValue = $emplData[$infoRow["field_name"]];
					}
					$infoFields .= "\t\t\t\t<Field>\n\t\t\t\t\t<Name>".$infoRow["field_name"]."</Name>\n\t\t\t\t\t<Label>".$infoRow["label"]."</Label>\n\t\t\t\t\t<Value>".$curValue."</Value>\n\t\t\t\t</Field>\n";
//					$infoFields .= "\t\t\t\t<Field>\n\t\t\t\t\t<Name>".$infoRow["field_name"]."</Name>\n\t\t\t\t\t<Label>".$infoRow["label"]."</Label>\n\t\t\t\t\t<Value>".$emplData[$infoRow["field_name"]]."</Value>\n\t\t\t\t</Field>\n";
					break;
				default:
					$infoFields .= "\t\t\t\t<Field>\n\t\t\t\t\t<Name>IFLD</Name>\n\t\t\t\t\t<Label>".$infoRow["label"]."</Label>\n\t\t\t\t\t<Value>".$infoRow["field_type"]."</Value>\n\t\t\t\t</Field>\n";
					break;
				}
			}
			$tmpNote = array();
			if(isset($notifications[(string)$emplData["payroll_company_ID"]][$emplData["Language"]])) $tmpNote[] = $notifications[(string)$emplData["payroll_company_ID"]][$emplData["Language"]];
			if(isset($notifications[(string)"0"][$emplData["Language"]])) $tmpNote[] = $notifications[(string)"0"][$emplData["Language"]];
			$curEmpl = "\t\t<Employee>\n\t\t\t<EmployeeNumber>".$emplData["EmployeeNumber"]."</EmployeeNumber>\n\t\t\t<CompanyID>".$emplData["payroll_company_ID"]."</CompanyID>\n\t\t\t<Firstname>".$emplData["Firstname"]."</Firstname>\n\t\t\t<Lastname>".$emplData["Lastname"]."</Lastname>\n".($emplData["AdditionalAddrLine1"]!="" ? "\t\t\t<AdditionalAddrLine1>".$emplData["AdditionalAddrLine1"]."</AdditionalAddrLine1>\n" : "").($emplData["AdditionalAddrLine2"]!="" ? "\t\t\t<AdditionalAddrLine2>".$emplData["AdditionalAddrLine2"]."</AdditionalAddrLine2>\n" : "").($emplData["AdditionalAddrLine3"]!="" ? "\t\t\t<AdditionalAddrLine3>".$emplData["AdditionalAddrLine3"]."</AdditionalAddrLine3>\n" : "").($emplData["AdditionalAddrLine4"]!="" ? "\t\t\t<AdditionalAddrLine4>".$emplData["AdditionalAddrLine4"]."</AdditionalAddrLine4>\n" : "")."\t\t\t<Street>".$emplData["Street"]."</Street>\n\t\t\t<ZIP-Code>".$emplData["ZIP-Code"]."</ZIP-Code>\n\t\t\t<City>".$emplData["City"]."</City>\n\t\t\t<Country>".$emplData["Country"]."</Country>\n\t\t\t<CountryName>".$countryNames[$emplData["Country"]]."</CountryName>\n".$infoByWageCode[$emplData["WageCode"]]."\t\t\t<PaymentDate>".$paymentDate."</PaymentDate>\n\t\t\t<InterestDate>".$interestDate."</InterestDate>\n".(count($tmpNote)!=0 ? "\t\t\t<Notification>".implode("<br/>",$tmpNote)."</Notification>\n" : "")."\t\t\t<DocumentSettings>\n\t\t\t\t<Language>".$emplData["Language"]."</Language>\n\t\t\t\t<DecimalPoint>.</DecimalPoint>\n\t\t\t\t<ThousandsSeparator>'</ThousandsSeparator>\n\t\t\t\t<PdfTemplate>".($templCfg["pdf_template"]!="" ? $pdfTemplateDir.$templCfg["pdf_template"] : "")."</PdfTemplate>\n\t\t\t\t<AddrOffsetLeft>".$templCfg["addr_offset_left"]."</AddrOffsetLeft>\n\t\t\t\t<AddrOffsetTop>".$templCfg["addr_offset_top"]."</AddrOffsetTop>\n\t\t\t\t<InfoOffsetLeft>".$templCfg["info_offset_left"]."</InfoOffsetLeft>\n\t\t\t\t<InfoOffsetTop>".$templCfg["info_offset_top"]."</InfoOffsetTop>\n\t\t\t\t<ContentOffsetLeft>".$templCfg["content_offset_left"]."</ContentOffsetLeft>\n\t\t\t\t<ContentOffsetTop>".$templCfg["content_offset_top"]."</ContentOffsetTop>\n\t\t\t\t<ContentWidth>".$templCfg["content_width"]."</ContentWidth>\n\t\t\t\t<AddrFontName>".$templCfg["addr_font_name"]."</AddrFontName>\n\t\t\t\t<AddrFontSize>".$templCfg["addr_font_size"]."</AddrFontSize>\n\t\t\t\t<InfoFontName>".$templCfg["info_font_name"]."</InfoFontName>\n\t\t\t\t<InfoFontSize>".$templCfg["info_font_size"]."</InfoFontSize>\n\t\t\t\t<ContentFontName>".$templCfg["content_font_name"]."</ContentFontName>\n\t\t\t\t<ContentFontSize>".$templCfg["content_font_size"]."</ContentFontSize>\n\t\t\t\t<ProcessingSuffix>".$templCfg["processing_suffix"]."</ProcessingSuffix>\n\t\t\t</DocumentSettings>\n\t\t\t<InfoFields>\n".$infoFields."\t\t\t</InfoFields>\n\t\t\t<Entries>\n".implode("",$entries)."\t\t\t</Entries>\n\t\t\t<Payouts>\n".(isset($payments[(string)$row["payroll_employee_ID"]]) ? implode("", $payments[(string)$row["payroll_employee_ID"]]) : "")."\t\t\t</Payouts>\n\t\t</Employee>\n";
			fwrite($fp, str_replace(array("&","_","%","#"), array("\\&","\\_","\\%","\\#"), $curEmpl) );
		}
		unset($payments);
		fwrite($fp, "\t</Employees>\n</Report>");
		$fm->fclose();

		chdir($newTmpPath);

        system($aafwConfig["paths"]["utilities"]["xsltproc"]." ".$aafwConfig["paths"]["reports"]["templates"]."Payslip.xslt ./data.xml > ./compileme.tex");
        
		system($aafwConfig["paths"]["utilities"]["pdflatex"]." -interaction=batchmode compileme.tex > ".$aafwConfig["paths"]["utilities"]["stdout"]);
		//system($aafwConfig["paths"]["utilities"]["pdflatex"]." -interaction=batchmode compileme.tex > ".$aafwConfig["paths"]["utilities"]["stdout"]);
        
		system("chmod 666 *");

		return $newTmpDirName;
	}
	
	public function generateAuszahlDataReports($ZahlstellenID, $Personenkreis) {
		$anzFiles = 0;
        require_once(getcwd()."/kernel/common-functions/configuration.php");
		require_once('payroll_auszahlen.php');
		$auszahlen = new auszahlen();
        global $aafwConfig;
		ini_set('memory_limit', '512M');
		//communication_interface::alert("Zahlstelle:".$ZahlstellenID." Personenkreis:".$Personenkreis);


		$noBankAccount = $this->alleMitarbeiterHabenEineIBAN();
		if (strlen($noBankAccount) > 1) {
			$s=	"Verarbeitung wird abgebrochen!" .CRLF.CRLF.
				"Folgende Personen haben kein" .CRLF.
				"Bankkonto registriert (IBAN fehlt)".CRLF;
			communication_interface::alert($s.$noBankAccount);
			return false;//Abbruch ganze Aktion (Keine Files erzeugen)
		}
		
		if ($ZahlstellenID < 1) {
			//Zahlstelle verwenden, die den Employees hinterlegt sind
			communication_interface::alert("comming soon");
			return false;//Abbruch 
		}

		$dtaPersKreisFileName = "";
		//Einschaenkung mit dem Personenfilter
		$emplFilter = "";
		if (substr($Personenkreis."xx",0,1)!="*") {			
			$dtaPersKreisFileName = "_p".str_replace(",", "p", $Personenkreis);
			$emplFilter = $auszahlen->getEmployeeFilters($Personenkreis);
		}

		//Die jetztige Periode ist
		$periodeID = blFunctionCall('payroll.auszahlen.getActualPeriodID');
		$PeriodeDieserMonat = blFunctionCall('payroll.auszahlen.getActualPeriodName');
		
		
		$bankSource = $auszahlen->getZahlstelle($ZahlstellenID);
		$iban = str_replace(" ", "", $bankSource["IBAN"]);		
		$ZahlstelleL1 =  strtoupper($auszahlen->replaceUmlaute( $bankSource["line1"] ));
		$ZahlstelleL2 =  strtoupper($auszahlen->replaceUmlaute( $bankSource["line2"] ));
		$ZahlstelleL3 =  strtoupper($auszahlen->replaceUmlaute( $bankSource["line3"] ));
		$ZahlstelleL4 =  strtoupper($auszahlen->replaceUmlaute( $bankSource["line4"] ));
		$companyID = $bankSource["company"];
		$ZahlstelleKonto = $iban;
		
		$company = $auszahlen->getCompany($companyID);
		$AuftraggeberFirmaL1 = $company["name"];//"Presida Testunternehmung";
		$AuftraggeberFirmaL2 = $company["str"];//"Mitteldorfstrasse 37";
		$AuftraggeberFirmaL3 = $company["zip"]." ".$company["city"];//"5033 Buchs";
	
		$dtaFileName = "";
		$dtaJournalFileName = "";
		$dtaRekapFileName = "";
		//communication_interface::alert("Zahlstelle:".$ZahlstellenID." \nDTAFileName:".$dtaFileName."\nperiodeDieserMonat:".$PeriodeDieserMonat."\nPersonenkreis:".$Personenkreis);
		if (strlen($iban) < 3) {
			$dtaFileName = date("Y-m-d").$dtaPersKreisFileName.".dta";
			$dtaJournalFileName = date("Y-m-d").$dtaPersKreisFileName."_dtaJournal";
			$dtaRekapFileName = date("Y-m-d").$dtaPersKreisFileName.".rekap";;
		} else {
			$dtaFileName = $iban.$dtaPersKreisFileName.".dta";
			$dtaJournalFileName = $iban.$dtaPersKreisFileName."_dtaJournal";
			$dtaRekapFileName = $iban.$dtaPersKreisFileName.".rekap";;
		}

		//DTA File schreiben
		$emplwithPayment = $auszahlen->getMitarbeiterZurAuszahlung("8000", "amount > 0.001", "isFullPayed <> 'Y'",$emplFilter);
		if ($emplwithPayment['count'] < 1) {
			communication_interface::alert("Keine Daten zur Auszahlung");
			return 0;
		}
		
		$effectedEmployees = "";	
		foreach ( $emplwithPayment['data'] as $row ){
			$effectedEmployees .= ",".$row['payroll_employee_ID'];
		}
		$effectedEmployees = substr($effectedEmployees, 1);//erstes "," wegmachen
		//$employeeList = $auszahlen->getMitarbeiterAddress($effectedEmployees);
		$employeeList = $auszahlen->getEmployeesCurrentPeriod("8000", $effectedEmployees);

//communication_interface::alert("effectedEmployeeList:\n".$effectedEmployees);
//communication_interface::alert("employeeList:".count($employeeList["data"]));

		$dtaContent="";
		$dtaJournal="";
		$trxnr=0;
		$SeitenNr = 1;
		$SeitenTotal = 0;
		$GesamtTotal = 0;
  		$RealEffectedEmployees = "";
		foreach ( $employeeList as $row ) {     
			$employee_ID = $row['id'];
			$RealEffectedEmployees .= ",".$employee_ID;
			$employeeNumber = $row['EmployeeNumber'];
			$amountDTA = number_format($row["amount"], 2, ',', "");
			$amountDTAJournal = $row["amount"];//number_format($row["amount"], 2, '.', "'");
			//communication_interface::alert("amountDTA:".$amountDTA."\namountDTAJournal:".$amountDTAJournal);
			//$amountDTAJournal = str_replace(",", ".", $amountDTA);
			$bene = $auszahlen->getBeneficiaryAddress($employee_ID);
			$bn1 = trim($bene['beneAddress1']);
			$bn2 = trim($bene['beneAddress2']);
			$bn3 = trim($bene['beneAddress3']);
			$bn4 = trim($bene['beneAddress4']);
			if (strlen($bn1) < 3) {
				$bn1 = trim($row['Firstname']). " " . trim($row['Lastname']);
				$bn2 = trim($row['Street']);
				$bn3 = trim($row['AdditionalAddrLine1']). " " . trim($row['AdditionalAddrLine2']);
				$bn4 = trim($row['ZIP-Code']). " " . trim($row['City']);
			}
			$bn1 = $auszahlen->replaceUmlaute( $bn1 );
			$bn2 = $auszahlen->replaceUmlaute( $bn2 );
			$bn3 = $auszahlen->replaceUmlaute( $bn3 );
			$bn4 = $auszahlen->replaceUmlaute( $bn4 );
			$benIBAN = str_replace(" ", "", $bene['bank_account']);
			$beneBank1 = $auszahlen->replaceUmlaute( $bene['beneBank1'] );
			$beneBank2 = $auszahlen->replaceUmlaute( trim($bene['beneBank2'])." ".trim($bene['beneBank3']) );
			$beneBank3 = $auszahlen->replaceUmlaute( $bene['beneBank4'] );
			if (strlen($beneBank1) < 3) {
				$beneBank1 = trim($auszahlen->replaceUmlaute($bene['beneBankDescription']));
			}
			$trxnr++;
			$dtaHeader51stellen = "000000".str_pad(" ", 12)
				."00000"
				.date("ymd")
				.str_pad(" ", 7)
				."COPRO"
				.str_pad($trxnr,5,"0", STR_PAD_LEFT)
				."827"."1"."0";
			$dtaContent.= CRLF.substr("01"
					.$dtaHeader51stellen
					.str_pad(date("ymd"),6)
					.str_pad("DTAID",5)
					.str_pad("TRXNR".str_pad($trxnr,6,"0", STR_PAD_LEFT),11)
					.str_pad($iban, 24) 
					.str_pad("CHF", 6, " ", STR_PAD_LEFT)
					.str_pad($amountDTA,12)
					.str_pad(" ",14)
				, 0, 128);						
			$dtaContent.= CRLF.substr("02"
					.str_pad($ZahlstelleL1, 20)
					.str_pad($ZahlstelleL2, 20)
					.str_pad($ZahlstelleL3, 20)
					.str_pad($ZahlstelleL4, 20)
					.str_pad(" ",46)
				, 0, 128);						
			$dtaContent.= CRLF.substr("03"
					."/C/".str_pad($benIBAN,30)
					.str_pad($bn1, 24)
					.str_pad($bn2, 24)
					.str_pad($bn3, 24)
					.str_pad($bn4, 24)
				, 0, 128);						
			$dtaContent.= CRLF.substr("04"
					.str_pad(strtoupper( "Salaerzahlung" ), 28)
					.str_pad(strtoupper( $PeriodeDieserMonat ), 28)
					.str_pad(strtoupper( "" ), 28)
					.str_pad(strtoupper( "" ), 28)
					.str_pad(strtoupper( "" ), 14)
				, 0, 128);						
			$dtaContent.="";
			//DTA Journal
			$SeitenTotal += $amountDTAJournal;
			$GesamtTotal += $amountDTAJournal;
			if ($trxnr == 1) {
				$dtaJournal .= $this->setDTAJournalHeader($PeriodeDieserMonat, $AuftraggeberFirmaL1, $AuftraggeberFirmaL2, $AuftraggeberFirmaL3, $ZahlstelleL1, $ZahlstelleL2, $ZahlstelleL3, $ZahlstelleL4, $ZahlstelleKonto);
			}	
			$anzZeilenSeite01 = 24; $anzZeilenSeiteFF = 27;			
			if ($trxnr == $anzZeilenSeite01 || ($trxnr-$anzZeilenSeite01) % $anzZeilenSeiteFF == 0) {//zuerst nach X, dann alle Y
				$SeitenNr++;
				$dtaJournal .= $this->setDTAJournalSeitenTotal($SeitenTotal);
				$dtaJournal .= $this->setDTAJournalFollowHeader($SeitenNr, $AuftraggeberFirmaL1, $AuftraggeberFirmaL3, $ZahlstelleL1, $ZahlstelleL3, $ZahlstelleKonto);
				$SeitenTotal = 0;
			}
			$dtaJournal .= $this->setDTAJournalZeile($trxnr, $employeeNumber, $bn1, $bn2." ".$bn3, $bn4, $benIBAN, $beneBank1, $beneBank2, $beneBank3, $amountDTAJournal);
		}//end foreach

//communication_interface::alert("RealEffectedEmployees IDs:\n".$RealEffectedEmployees."\n");
		
		//Abhaken Mitarbeiter mit Lohnbezug
		$emplwithPayment = $trxnr;
		if ($emplwithPayment > 0) {
			$auszahlen->updatePeriodenAuszahlFlag($periodeID, substr($effectedEmployees, 1), "Y");//Liste ohne erstes Komma
		} 

		//Abhaken Mitarbeiter mit 0 Lohn
		$emplwithoutPaymentList = $auszahlen->getMitarbeiterZurAuszahlung("8000", "amount < 0.001", "isFullPayed <> 'Y'",$emplFilter);
		$emplwithoutPayment = $emplwithoutPaymentList["count"];
		$effectedNoPaymentEmployeeList = "";
		if ($emplwithoutPayment > 0) {
			foreach ( $emplwithoutPaymentList['data'] as $row ) {
				$effectedNoPaymentEmployeeList .= ",".$row['payroll_employee_ID'];
			}
			$effectedNoPaymentEmployeeList = substr($effectedNoPaymentEmployeeList, 1);
			$auszahlen->updatePeriodenAuszahlFlag($periodeID, $effectedNoPaymentEmployeeList, "0");//Liste ohne erstes Komma
		}		
		$dtaJournal .= $this->setDTAJournalSeitenTotal($SeitenTotal);
		$dtaRekap    = $this->setDTAJournalRekap($SeitenNr, $GesamtTotal, strtoupper($PeriodeDieserMonat), $trxnr, $Personenkreis, $dtaFileName, $emplFilter, $emplwithPayment, $effectedNoPaymentEmployeeList, $ZahlstelleL1, $ZahlstelleL2, $ZahlstelleL3, $ZahlstelleL4, $ZahlstelleKonto);
		$dtaJournal .= $dtaRekap;

		
		//Files speichern
		$fm = new file_manager();
		//$fullPath0 =  $fm->getFullPath();
		$fm->customerSpace()->setPath("/".AUSZAHLDIR)->makeDir();   
		$fm->customerSpace()->setPath("/".AUSZAHLDIR."/".$PeriodeDieserMonat);  
		$fm->customerSpace()->setPath("/".AUSZAHLDIR."/".$PeriodeDieserMonat)->makeDir(); 
		$fullPath =  $fm->getFullPath();
		$fm->setFile($dtaFileName); 
		$fm->putContents($dtaContent); 
		$anzFiles++;
//		$fm->setFile($dtaJournalFileName.".txt"); 
//		$fm->putContents($dtaJournal); 
//		$anzFiles++;
		$fm->setFile($dtaRekapFileName); 
		$fm->putContents($dtaRekap); 
		
		$c = "";
		$rekap = "";
		$filelist = $fm->listDir(0);  
		foreach($filelist as $row) {
			if(strtolower(substr($row,-6))==".rekap") {
				//communication_interface::alert("REKAP:\n".$row);
				$c = $fm->getContents($row); 
				$rekap = $rekap .CRLF. $c;
				$c = "";
				$row = "";
			} 
		}
		//communication_interface::alert("REKAP:\n".p);
//		$fm->setFile("REKAP.txt"); 
//		$fm->putContents($rekap);//füllen 
		$fm->fclose(); 
		
		require_once('/web/fpdf17/fpdf.php');
		$pdf = new FPDF('P','mm','A4');
		$pdf->AddPage();
		$pdf->SetFont('Courier','',9);
		$pdf->MultiCell( 188, 3, $dtaJournal , 0, 'L', 0); 
		$pdf->Output($fullPath.$dtaJournalFileName.'.pdf', 'F');
		$anzFiles++;

		$pdf = new FPDF('P','mm','A4');
		$pdf->AddPage();
		$pdf->SetFont('Courier','',9);
		$pdf->MultiCell( 188, 3, $rekap , 0, 'L', 0); 
		$pdf->Output($fullPath.'REKAP.pdf', 'F');
		$anzFiles++;
		
		system("chmod 666 *");
		

		communication_interface::alert(
			$emplwithPayment." Zahlungsauftraege\n" .
			$emplwithoutPayment." ohne Auszahlung\n".
			$anzFiles." Dateien erzeugt\n\n".
			"Zahlstelle [z".$ZahlstellenID."]\n".
			"Personenkreis [p".$Personenkreis."]"
			);
		return $anzFiles;
	}
	
	public function generateDTAFiles($param) {
		communication_interface::alert("Zahlstelle:".$param["ZahlstelleID"].", Personenkreis:".$param["Personenkreis"]);
		return null;
	}
	
	function alleMitarbeiterHabenEineIBAN(){
		$system_database_manager = system_database_manager::getInstance();
		$result_dest_emp = $system_database_manager->executeQuery("				
					SELECT * FROM
					  payroll_bank_destination
					, payroll_employee
					WHERE	payroll_bank_destination.payroll_employee_ID = payroll_employee.id
					AND		payroll_bank_destination.destination_type <> 3	
					AND		payroll_bank_destination.bank_account = '' 
					;");
		$c = "";	 				
		foreach ( $result_dest_emp as $row ) {
			$c .= $row['EmployeeNumber'].", ";
			$c .= $row['Firstname']." ";
			$c .= $row['Lastname'].", ";
			$c .= $row['City'].CRLF;
		}
		return $c;
	}
	
	function setDTAJournalHeader($PeriodeDieserMonat, $AuftraggeberFirmaL1, $AuftraggeberFirmaL2, $AuftraggeberFirmaL3, $ZahlstelleL1, $ZahlstelleL2, $ZahlstelleL3, $ZahlstelleL4, $ZahlstelleKonto) {
		$tab1_top= 35;$tab2_top= 30;$tab3_top= 12;$end_top = 18;
									$tab4_top2=14;$end_top2 = 4;
		$tab1_h1 = 35;$tab2_h1 = 20;$tab3_h1 = 25;$end_h1 = 35;

		$tab1_h3 =  7;$tab2_h3 = 53;$tab3_h3 = 20;$end_h3 = 15;
		$h  = "";
		$h .= CRLF.str_pad(substr("PESARIS/COPRONET", 0, $tab1_top-1), $tab1_top)
				  .str_pad(substr("LISTE ZUM DTA-AUFTRAG VOM:", 0, $tab2_top-1), $tab2_top)
				  .str_pad(date("d.m.Y"), $tab3_top, " ", STR_PAD_LEFT)
				  .str_pad(date("d.m.Y/H:i"), $end_top, " ", STR_PAD_LEFT);
		$h .= CRLF.str_pad(substr($PeriodeDieserMonat, 0, $tab1_top-1), $tab1_top)
				  .str_pad(substr("Valuta Datum:", 0, $tab2_top-1), $tab2_top)
				  .str_pad(date("d.m.Y"), $tab3_top, " ", STR_PAD_LEFT)
				  .str_pad("Seite", $tab4_top2, " ", STR_PAD_LEFT)
				  .str_pad("1", $end_top2, " ", STR_PAD_LEFT);
		$h .= CRLF.str_pad("", $tab1_h3+$tab2_h3+$tab3_h3+$end_h3, "-");
		$h .= CRLF;
		$h .= CRLF.str_pad(substr($AuftraggeberFirmaL1, 0, $tab1_h1-1), $tab1_h1);
		$h .= CRLF.str_pad(substr($AuftraggeberFirmaL2, 0, $tab1_h1-1), $tab1_h1);
		$h .= CRLF.str_pad(substr($AuftraggeberFirmaL3, 0, $tab1_h1-1), $tab1_h1);
		$h .= CRLF;
		$h .= CRLF.str_pad("Beauftragte Bank:",$tab1_h1).str_pad(substr($ZahlstelleL1, 0, $tab3_h1-1), $tab3_h1);
		$h .= CRLF.str_pad("",$tab1_h1).str_pad(substr($ZahlstelleL2, 0, $tab3_h1-1), $tab3_h1);
		$h .= CRLF.str_pad("",$tab1_h1).str_pad(substr($ZahlstelleL3, 0, $tab3_h1-1), $tab3_h1)
				  .str_pad(substr("Ab Konto:", 0, $end_h3-1), $end_h3).trim($ZahlstelleKonto);
		$h .= CRLF.str_pad("",$tab1_h1).str_pad(substr($ZahlstelleL4, 0, $tab3_h1-1), $tab3_h1);
		$h .= CRLF.str_pad("F-NR.", $tab1_h3)
				  .str_pad("P-NR.  EMPFAENGER/BEGUENSTIGTER", $tab2_h3)
				  .str_pad("IBAN NUMMER", $tab3_h3)
				  .str_pad("BETRAG", $end_h3, " ", STR_PAD_LEFT);
		$h .= CRLF.str_pad("", $tab1_h3+$tab2_h3+$tab3_h3+$end_h3, "-");		
		return $h;
	}
	
	function setDTAJournalFollowHeader($SeitenNr, $AuftraggeberFirmaL1, $AuftraggeberFirmaL2, $ZahlstelleL1, $ZahlstelleL2, $ZahlstelleKonto) {
		$tab1_h1 = 35;$tab2_h1 = 20;$end_h1 = 40;
		$tab1_h2 = 35;$tab2_h2 = 20;$tab3_h2 = 31;$tab4_h2 = 6;$end_h2 = 3;
		$tab1_h3 =  7;$tab2_h3 = 53;$tab3_h3 = 20;$end_h3 = 15;
		$h = "";
		$h .= CRLF.str_pad(substr($AuftraggeberFirmaL1, 0, $tab1_h1-1), $tab1_h1)
				  .str_pad(substr($ZahlstelleL1, 0, $tab2_h1-1), $tab2_h1)
				  .str_pad(date("d.m.Y/H:i"), $end_h1, " ", STR_PAD_LEFT);
		$h .= CRLF.str_pad(substr($AuftraggeberFirmaL2, 0, $tab1_h2-1), $tab1_h2)
				  .str_pad(substr($ZahlstelleL2, 0, $tab2_h2-1), $tab2_h2)
				  .str_pad($ZahlstelleKonto, $tab3_h2)
				  .str_pad("Seite", $tab4_h2)
				  .str_pad($SeitenNr, $end_h2, " ", STR_PAD_LEFT);
		$h .= CRLF;
		$h .= CRLF.str_pad("F-NR.", $tab1_h3)
				  .str_pad("P-NR.  EMPFAENGER/BEGUENSTIGTER", $tab2_h3)
				  .str_pad("IBAN NUMMER", $tab3_h3)
				  .str_pad("BETRAG", $end_h3, " ", STR_PAD_LEFT);
		$h .= CRLF.str_pad("", $tab1_h3+$tab2_h3+$tab3_h3+$end_h3, "-");
		
		return $h;
	}
	function setDTAJournalSeitenTotal($SeitenTotal) {
		$tab1_h3 =  7;$tab2_h3 = 53;$tab3_h3 = 20;$end_h3 = 15;
		$h  = CRLF.str_pad(" ", $tab1_h3)
				  .str_pad(" ", $tab2_h3)
				  .str_pad("SEITENTOTAL", $tab3_h3)
				  .str_pad(number_format($SeitenTotal, 2, '.', "'"), $end_h3, " ", STR_PAD_LEFT);
		$h .= CRLF;
		$h .= CRLF."\f".CRLF;
		$h .= CRLF;
		return $h;
	}
	function setDTAJournalRekap($SeitenNr, $GesamtTotal, $PeriodeDieserMonat, $trxnr, $Personenkreis, $dtaFileName, $emplFilter, $emplwithPayment, $effectedNoPaymentEmployeeList, $ZahlstelleL1, $ZahlstelleL2, $ZahlstelleL3, $ZahlstelleL4, $ZahlstelleKonto) {
		$tab1_h1 = 35;$tab2_h1 = 20;$end_h1 = 40;
		$tab1_h2 = 35;$tab2_h2 = 20;$tab3_h2 = 31;$tab4_h2 = 6;$end_h2 = 3;
		$tab1_h3 =  7;$tab2_h3 = 53;$tab3_h3 = 20;$end_h3 = 15;
		$h  = "";
		$h .= CRLF."REKAP DTA-JOURNAL ".strtoupper($PeriodeDieserMonat)." ".$dtaFileName;
		$h .= CRLF.str_pad("", $tab1_h3+$tab2_h3+$tab3_h3+$end_h3, "-");
		$h .= CRLF;
		$h .= CRLF.str_pad(" ", $tab1_h3)
				  .str_pad("Zahlstelle",$tab2_h3).$ZahlstelleKonto;
		$h .= CRLF;
		$h .= CRLF.str_pad("", $tab1_h3+$tab2_h3).$ZahlstelleL1;
		$h .= CRLF.str_pad("", $tab1_h3+$tab2_h3).$ZahlstelleL2;
		$h .= CRLF.str_pad("", $tab1_h3+$tab2_h3).$ZahlstelleL3;
		$h .= CRLF.str_pad("", $tab1_h3+$tab2_h3).$ZahlstelleL4;
		$h .= CRLF.str_pad("", $tab1_h3+$tab2_h3)
				  .str_pad("GESAMTTOTAL", $tab3_h3)
				  .str_pad(number_format($GesamtTotal, 2, '.', "'") , $end_h3, " ", STR_PAD_LEFT);
		$h .= CRLF;
		$h .= CRLF.str_pad(" ", $tab1_h3)
				  .str_pad("DTA-Datei", $tab2_h3).$dtaFileName;
		$h .= CRLF.str_pad(" ", $tab1_h3)
				  .str_pad("Auszahlperiode", $tab2_h3).$PeriodeDieserMonat;
		$h .= CRLF.str_pad(" ", $tab1_h3)
				  .str_pad("Transaktionen", $tab2_h3).$trxnr;
		if ($Personenkreis != "*"){
			$h .= CRLF;
			$h .= CRLF.str_pad(" ", $tab1_h3)
					  .str_pad("Personenkreis", $tab2_h3)."[p".$Personenkreis."]";
			if (strlen($emplFilter) > $tab2_h3+$tab3_h3+$end_h3-4){
				$h .= CRLF.str_pad("", $tab1_h3)."(".trim(substr($emplFilter, 4, $tab2_h3+$tab3_h3+$end_h3-4));//ohne Anfangs-"AND"				
				$h .= CRLF.str_pad("", $tab1_h3)."(".trim(substr($emplFilter, $tab2_h3+$tab3_h3+$end_h3-4)).")";//ohne Anfangs-"AND"								
			} else {
				$h .= CRLF.str_pad("", $tab1_h3)."(".trim(substr($emplFilter, 4)).")";//ohne Anfangs-"AND"				
			}		
		}
		if (strlen($effectedNoPaymentEmployeeList)>0){
			require_once('payroll_auszahlen.php');
			$auszahlen = new auszahlen();
			$h .= CRLF;
			$system_database_manager = system_database_manager::getInstance();
			$result_employeesNoPayment = $system_database_manager->executeQuery("
				SELECT * FROM 
				payroll_employee 
				WHERE id IN (".$effectedNoPaymentEmployeeList.");");
				foreach ( $result_employeesNoPayment as $row ){
					 $h .= CRLF
					    .str_pad("", $tab1_h3)
					    ."keine Zahlung:  "
						.str_pad($row['EmployeeNumber'], 6,"0", STR_PAD_LEFT)
						." ".$auszahlen->replaceUmlaute($row['Firstname']." ".$row['Lastname'].", ".$row['City']);
				}
		}
		$h .= CRLF.str_pad("", $tab1_h3+$tab2_h3+$tab3_h3+$end_h3, "-");
		$h .= CRLF;
		
		return $h;
	}	
	function setDTAJournalZeile($trxNr, $employeeNumber, $ben1, $ben2, $ben3, $benIBAN, $benBank1, $benBank2, $benBank3, $betrag) {
		$tab1_z1 =  7;$tab2_z1 = 33;$tab3_z1 = 20;
		$tab1_z2 = 20;$tab2_z2 = 24;$tab3_z2 = 16;$tab4_z2 = 20;$end_z2 =  15;
		$l  = "";
		$l .= CRLF.str_pad(str_pad($trxNr,$tab1_z1-2,"0", STR_PAD_LEFT),$tab1_z1)
				  .str_pad(substr( str_pad($employeeNumber,6,"0",STR_PAD_LEFT) ." ".$ben1, 0, $tab2_z1-1), $tab2_z1)  
				  .str_pad(substr($ben2, 0, $tab3_z1-1), $tab3_z1)
				  .$ben3;
		$l .= CRLF.substr(
				   str_pad(substr(trim($benBank1), 0, $tab1_z2-1), $tab1_z2)
				  .str_pad(substr(trim($benBank2), 0, $tab2_z2-1), $tab2_z2)
				  .str_pad(substr(trim($benBank3), 0, $tab3_z2-1), $tab3_z2)
				  .str_pad($benIBAN, $tab4_z2), 0, $tab1_z2+$tab2_z2+$tab3_z2+$tab4_z2);
		$l .=      str_pad(number_format($betrag, 2, '.', "'"), $end_z2, " ", STR_PAD_LEFT);
		$l .= CRLF.str_pad("----", $tab1_z2+$tab2_z2+$tab3_z2+$tab4_z2+$end_z2, "-");
		return $l;
	}
}
?>
