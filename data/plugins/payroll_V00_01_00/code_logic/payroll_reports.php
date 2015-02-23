<?php
class payroll_BL_reports {
	
	public function CalculationJournal($param) {
        require_once(getcwd()."/kernel/common-functions/configuration.php");
        require_once('payroll_reporting_functions.php');
        $payroll_reporting_functions = new payroll_reporting_functions();
        
        global $aafwConfig;
		ini_set('memory_limit', '512M');
//$param = array("year"=>$functionParameters[0]["year"],"majorPeriod"=>$functionParameters[0]["majorPeriod"],"minorPeriod"=>$functionParameters[0]["minorPeriod"])
		$periodLabels["de"] = $payroll_reporting_functions->getPeriodLabels("de");

//communication_interface::alert("CalculationJournal param=".print_r($param,true));
		
		$fm = new file_manager();
		$newTmpDirName = $fm->createTmpDir();
		$newTmpPath = $fm->getFullPath();
		$fm->setFile("metadata.dat")->putContents( serialize(array("fileFormat"=>"pdf","realFileName"=>"compileme.pdf","transmissionFileName"=>"CalculationJournal.pdf")) );
		
		if($param["majorPeriod"]<13 || $param["majorPeriod"]>14) $periodTitle = $param["majorPeriod"]." (".$periodLabels[session_control::getSessionInfo("language")][$param["majorPeriod"]].")";
		else $periodTitle = $param["majorPeriod"];

		$system_database_manager = system_database_manager::getInstance();
		$fp = $fm->setFile("data.xml")->fopen("w");

		$ReportName = "CalculationJournal";
        fwrite($fp, 
               "<Report name=\"".$ReportName."\" lang=\"de\">\n\t<Header>".
                $payroll_reporting_functions->getReportingCompany("Company", 0).
                $payroll_reporting_functions->getPrintDateTime()."
				<Year>".$param["year"]."</Year>
				<Period>".$periodTitle."</Period>
                </Header>
                <Employees>\n");

		//TODO: $param Werte gegen intrusion sichern !!!!!
		$result = $system_database_manager->executeQuery("SELECT id FROM payroll_period WHERE payroll_year_ID=".$param["year"]." AND major_period=".$param["majorPeriod"]." AND minor_period=".$param["minorPeriod"], "payroll_report_CalculationJournal");
		if(count($result)>0) $payrollPeriodID = $result[0]["id"];
		else return;

		$isCurrentPeriod = false;
		$sql = "SELECT payroll_period_ID FROM payroll_calculation_current LIMIT 1";
		$result = $system_database_manager->executeQuery($sql, "payroll_report_CalculationJournal");
		if(count($result)>0) $isCurrentPeriod = $result[0]["payroll_period_ID"]==$payrollPeriodID ? true : false;

		$sql = "SELECT 
		  empl.id as EmployeeID
		, empl.EmployeeNumber
		, empl.Firstname
		, empl.Lastname
		, empl.payroll_company_ID
		, empl.CodeAHV
		, empl.CodeALV
		, empl.CodeUVG
		, empl.CodeUVGZ1
		, empl.CodeUVGZ2
		, empl.CodeBVG
		, empl.CodeKTG
		, empl.EmploymentStatus
		, acc.id as AccountNumber
		, acclbl.label
		, calc.quantity
		, calc.rate
		, calc.amount
		, calc.code 
		FROM ".($isCurrentPeriod ? "payroll_calculation_current" : "payroll_calculation_entry")." calc 
		INNER JOIN payroll_employee empl ON empl.id=calc.payroll_employee_ID 
		INNER JOIN payroll_account acc ON acc.id=calc.payroll_account_ID 
		AND acc.payroll_year_ID=calc.payroll_year_ID 
		INNER JOIN payroll_account_label acclbl ON acclbl.payroll_account_ID=acc.id 
		AND acclbl.payroll_year_ID=acc.payroll_year_ID 
		AND acclbl.language='".session_control::getSessionInfo("language")."' 
		WHERE calc.payroll_period_ID=".$payrollPeriodID." 
		ORDER BY empl.Lastname, empl.Firstname, calc.payroll_employee_ID, acc.id"; 
		$result = $system_database_manager->executeQuery($sql, "payroll_report_CalculationJournal");
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
				$singleEmployeeData = 
				"\t\t<Employee>
				<EmployeeNumber>".$row["EmployeeNumber"]."</EmployeeNumber>
				<CompanyID>".$row["payroll_company_ID"]."</CompanyID>
				<Firstname>".$row["Firstname"]."</Firstname>
				<Lastname>".$row["Lastname"]."</Lastname>
				<CodeAHV>".$row["CodeAHV"]."</CodeAHV>
				<CodeALV>".$row["CodeALV"]."</CodeALV>
				<CodeKTG>".$row["CodeKTG"]."</CodeKTG>
				<CodeUVG>".$row["CodeUVG"]."</CodeUVG>
				<CodeBVG>".$row["CodeBVG"]."</CodeBVG>
				<Status>".$row["EmploymentStatus"]."</Status>
				<Entries>\n";
			}
			$entryCollector[] = "
				<Entry>
					<AccountNumber>".$row["AccountNumber"]."</AccountNumber>
					<AccountName>".$row["label"]."</AccountName>
					<quantity>".$row["quantity"]."</quantity>
					<rate>".$row["rate"]."</rate>
					<amount>".$row["amount"]."</amount>".
					($row["code"]=="" ? "" : "
					<code>".$row["code"]."</code>")."
				</Entry>\n";
		}
		if($singleEmployeeData != "") {
			//there are still a few more data for writing to the XML file
			fwrite($fp, $singleEmployeeData.str_replace(array("&","_","%","#"), array("\\&","\\_","\\%","\\#"), implode("",$entryCollector))."\t\t\t</Entries>\n\t\t</Employee>\n");
		}
		fwrite($fp, "\t</Employees>\n</Report>\n");
		$fm->fclose();

		chdir($newTmpPath);

        system($aafwConfig["paths"]["utilities"]["xsltproc"]." ".$aafwConfig["paths"]["reports"]["templates"]."CalculationJournal.xslt ./data.xml > ./compileme.tex");
        
        system($aafwConfig["paths"]["utilities"]["pdflatex"]." -interaction=batchmode compileme.tex > ".$aafwConfig["paths"]["utilities"]["stdout"]);
		system($aafwConfig["paths"]["utilities"]["pdflatex"]." -interaction=batchmode compileme.tex > ".$aafwConfig["paths"]["utilities"]["stdout"]);
		system("chmod 666 *");

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
        require_once('payroll_reporting_functions.php');
        $payroll_reporting_functions = new payroll_reporting_functions();
        global $aafwConfig;
		ini_set('memory_limit', '512M');
		$periodLabels["de"] = $payroll_reporting_functions->getPeriodLabels("de");

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
        
//communication_interface::alert("AccountingJournal case ".$param["selectedReportType"]);
		
        //Query bereitstellen und Reportvorlage definieren
        $reportTemplate = "";
        switch ($param["selectedReportType"])
        {
            case 0:
                // Auswertung nach Mitarbeiter (von Daniel Müller)
                $reportTemplate = $ReportName;
                
                fwrite($fp, 
               "<Report name=\"".$ReportName."\" lang=\"de\">\n\t<Header>".
                $payroll_reporting_functions->getReportingCompany("Company", 0).
                $payroll_reporting_functions->getPrintDateTime()."
				<Year>".$param["year"]."</Year>
				<Period>".$periodTitle."</Period>".
                "\n\t</Header>
				<Employees>\n");
                
                $query = "SELECT 
                                emp.`id` as EmployeeID, 
                                emp.`EmployeeNumber`, 
                                IF(accetry.payroll_company_ID = 0, emp.payroll_company_ID, accetry.payroll_company_ID) AS payroll_company_ID, 
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

                fwrite($fp,
                "<Report name=\"".$ReportName."\" lang=\"de\">\n\t<Header>".
                $payroll_reporting_functions->getReportingCompany("MainCompany", 0).
                $payroll_reporting_functions->getPrintDateTime()."
				<Year>".$param["year"]."</Year>
				<Period>".$periodTitle."</Period>
				<AccountType>".$entryTable."</AccountType>".
                "\n\t</Header>
                <Corporation>
                	<Companies>\n");
                
                $query = "  SELECT 
                                IF(accetry.payroll_company_ID = 0, emp.payroll_company_ID, accetry.payroll_company_ID) AS payroll_company_ID,
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
                                payroll_company comp ON comp.id = IF(accetry.payroll_company_ID = 0, emp.payroll_company_ID, accetry.payroll_company_ID)
									INNER JOIN
                                payroll_account_label acclbl ON acclbl.payroll_account_ID = accetry.payroll_account_ID
                                    AND acclbl.payroll_year_ID = ".$param["year"]."
                                    AND acclbl.language = '".session_control::getSessionInfo("language")."'
                            WHERE accetry.payroll_period_ID = ".$payrollPeriodID." AND `amount_quantity` = 0 AND `account_no` <> ''".
                            ($param["company"] == false ? "":" AND IF(accetry.payroll_company_ID = 0, emp.payroll_company_ID, accetry.payroll_company_ID) = ".$param["company"])."
                            GROUP BY    IF(accetry.payroll_company_ID = 0, emp.payroll_company_ID, accetry.payroll_company_ID), 
                                        accetry.account_no, 
                                        accetry.counter_account_no, 
                                        accetry.cost_center, 
                                        accetry.entry_text
                            ORDER BY    IF(accetry.payroll_company_ID = 0, emp.payroll_company_ID, accetry.payroll_company_ID), 
                                        accetry.account_no , 
                                        accetry.counter_account_no , 
                                        accetry.cost_center, 
                                        accetry.entry_text";
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
                        $singleEmployeeData = "
						<Company>
							<CompanyID>".$row["payroll_company_ID"]."</CompanyID>
							<CompanyName>".$row["company_shortname"]."</CompanyName>
							<Entries>\n";
                    }
                    $runningSumDebitAmount += $row["debit_amount"];
                    $runningSumCreditAmount += $row["credit_amount"];
                    $corporationDebitAmount += $row["debit_amount"];
                    $corporationCreditAmount += $row["credit_amount"];
                    	
                    $entryCollector[] = "
					<Entry ".	($entryCounter % 30 == 0?	"doPageBreak=\"true\""	:	"doPageBreak=\"false\"").">
						<CompanyID>".$row["payroll_company_ID"]."</CompanyID>
						<Account>".$row["account_no"]."</Account>
						<CounterAccount>".$row["counter_account_no"]."</CounterAccount>
						<CostCenter>".$row["cost_center"]."</CostCenter>
						<DebitAmount>".$row["debit_amount"]."</DebitAmount>
						<CreditAmount>".$row["credit_amount"]."</CreditAmount>
						<EntryText>".$row["entry_text"]."</EntryText>
						<RunningSumDebitAmount>".$runningSumDebitAmount."</RunningSumDebitAmount>
						<RunningSumCreditAmount>".$runningSumCreditAmount."</RunningSumCreditAmount>
					</Entry>\n";
                    
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

                 fwrite($fp,
                 "<Report name=\"".$ReportName."\" lang=\"de\">\n\t<Header>".
                 $payroll_reporting_functions->getReportingCompany("MainCompany", 0).
                 $payroll_reporting_functions->getPrintDateTime()."
				<Year>".$param["year"]."</Year>
				<Period>".$periodTitle."</Period>
				<AccountType>".$entryTable."</AccountType>".
                "\n\t</Header>
				<Corporation>
                	<Companies>\n");
                 
                 $query = "SELECT 
                                IF(accetry.payroll_company_ID = 0, emp.payroll_company_ID, accetry.payroll_company_ID) AS payroll_company_ID,
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
                                payroll_company comp ON comp.id = IF(accetry.payroll_company_ID = 0, emp.payroll_company_ID, accetry.payroll_company_ID)
									INNER JOIN
                                payroll_account_label acclbl ON acclbl.payroll_account_ID = accetry.payroll_account_ID
                                    AND acclbl.payroll_year_ID = ".$param["year"]."
                                    AND acclbl.language = '".session_control::getSessionInfo("language")."'
                            WHERE accetry.payroll_period_ID = ".$payrollPeriodID." AND `amount_quantity` = 0 AND `account_no` <> ''".
                            ($param["company"] == false ? "":" AND IF(accetry.payroll_company_ID = 0, emp.payroll_company_ID, accetry.payroll_company_ID) = ".$param["company"]).
                            ($param["cost_center"] == false ? "": " AND accetry.cost_center = ".$param["cost_center"])."
                            GROUP BY    IF(accetry.payroll_company_ID = 0, emp.payroll_company_ID, accetry.payroll_company_ID), 
                                        accetry.cost_center, 
                                        accetry.account_no, 
                                        accetry.counter_account_no, 
                                        accetry.entry_text
                            ORDER BY    IF(accetry.payroll_company_ID = 0, emp.payroll_company_ID, accetry.payroll_company_ID) , 
                                        accetry.cost_center,
                                        accetry.account_no , 
                                        accetry.counter_account_no, 
                                        accetry.entry_text";
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
                             fwrite($fp, $singleEmployeeData.str_replace(array("&","_","%","#"), array("\\&","\\_","\\%","\\#"), implode("",$entryCollector))."\t\t\t\t</Entries>\n\t\t\t<CompanyDebitAmount>".$runningSumDebitAmount."</CompanyDebitAmount>\n\t\t\t<CompanyCreditAmount>".$runningSumCreditAmount."</CompanyCreditAmount>\n\t\t</Company>\n");
                         }
                         $runningSumDebitAmount = 0;
                         $runningSumCreditAmount = 0;
                         $entryCounter = 1;
                         $lastPayrollCompanyID = $row["payroll_company_ID"];
                         $entryCollector = array();
                         $singleEmployeeData = "
                         	<Company>
                         		<CompanyID>".$row["payroll_company_ID"]."</CompanyID>
                         		<CompanyName>".$row["company_shortname"]."</CompanyName>
                         		<Entries>\n";
                     }
                     $runningSumDebitAmount += $row["debit_amount"];
                     $runningSumCreditAmount += $row["credit_amount"];
                     $corporationDebitAmount += $row["debit_amount"];
                     $corporationCreditAmount += $row["credit_amount"];
                     $entryCollector[] = "
					<Entry ".	($entryCounter % 30 == 0?	"doPageBreak=\"true\""	:	"doPageBreak=\"false\"").">
						<CompanyID>".$row["payroll_company_ID"]."</CompanyID>
						<Account>".$row["account_no"]."</Account>
						<CounterAccount>".$row["counter_account_no"]."</CounterAccount>
						<CostCenter>".$row["cost_center"]."</CostCenter>
						<DebitAmount>".$row["debit_amount"]."</DebitAmount>
						<CreditAmount>".$row["credit_amount"]."</CreditAmount>
						<EntryText>".$row["entry_text"]."</EntryText>
						<RunningSumDebitAmount>".$runningSumDebitAmount."</RunningSumDebitAmount>
						<RunningSumCreditAmount>".$runningSumCreditAmount."</RunningSumCreditAmount>
					</Entry>\n";
                     $entryCounter += 1;
                 }
                 if($singleEmployeeData != "") {
                     fwrite($fp, $singleEmployeeData.str_replace(array("&","_","%","#"), array("\\&","\\_","\\%","\\#"), implode("",$entryCollector))."\t\t\t\t</Entries>\n\t\t\t<CompanyDebitAmount>".$runningSumDebitAmount."</CompanyDebitAmount>\n\t\t\t<CompanyCreditAmount>".$runningSumCreditAmount."</CompanyCreditAmount>\n\t\t</Company>\n");
                 }
                 fwrite($fp, "\t\t</Companies>
                 			  \t\t<CorporationDebitAmount>".$corporationDebitAmount."</CorporationDebitAmount>
                 			  \t\t<CorporationCreditAmount>".$corporationCreditAmount."</CorporationCreditAmount>
                 			  \t</Corporation>
                 			  \n</Report>\n");
                 $fm->fclose();
                 break;
             case 3:
                 // Auswertung nach Firma / Konto / Gegenkonto
                 $reportTemplate = "AccountingJournal[Company][Account][Counter_account]"; 

                 fwrite($fp,
                 "<Report name=\"".$ReportName."\" lang=\"de\">\n\t<Header>".
                 $payroll_reporting_functions->getReportingCompany("MainCompany", 0).
                 $payroll_reporting_functions->getPrintDateTime()."
				<Year>".$param["year"]."</Year>
				<Period>".$periodTitle."</Period>
				<AccountType>".$entryTable."</AccountType>".
                "\n\t</Header>
				<Corporation>
					<Companies>\n");
                 
                 $query = "  SELECT 
                                IF(accetry.payroll_company_ID = 0, emp.payroll_company_ID, accetry.payroll_company_ID) AS payroll_company_ID,
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
                                payroll_company comp ON comp.id = IF(accetry.payroll_company_ID = 0, emp.payroll_company_ID, accetry.payroll_company_ID)
									INNER JOIN
                                payroll_account_label acclbl ON acclbl.payroll_account_ID = accetry.payroll_account_ID
                                    AND acclbl.payroll_year_ID = ".$param["year"]."
                                    AND acclbl.language = '".session_control::getSessionInfo("language")."'
                            WHERE accetry.payroll_period_ID = ".$payrollPeriodID." AND `amount_quantity` = 0 AND `account_no` <> ''".
                            ($param["company"] == false ? "":" AND IF(accetry.payroll_company_ID = 0, emp.payroll_company_ID, accetry.payroll_company_ID) = ".$param["company"])."
                            GROUP BY    IF(accetry.payroll_company_ID = 0, emp.payroll_company_ID, accetry.payroll_company_ID), 
                                        accetry.account_no, 
                                        accetry.counter_account_no, 
                                        accetry.entry_text
                            ORDER BY    IF(accetry.payroll_company_ID = 0, emp.payroll_company_ID, accetry.payroll_company_ID), 
                                        accetry.account_no , 
                                        accetry.counter_account_no, 
                                        accetry.entry_text";
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
                    	     fwrite($fp, $singleEmployeeData.str_replace(array("&","_","%","#"), array("\\&","\\_","\\%","\\#"), implode("",$entryCollector))."\t\t\t\t</Entries>\n\t\t\t<CompanyDebitAmount>".$runningSumDebitAmount."</CompanyDebitAmount>\n\t\t\t<CompanyCreditAmount>".$runningSumCreditAmount."</CompanyCreditAmount>\n\t\t</Company>\n");
                    	}
						$runningSumDebitAmount = 0;
						$runningSumCreditAmount = 0;
						$entryCounter = 1;
						$lastPayrollCompanyID = $row["payroll_company_ID"];
						$entryCollector = array();
						$singleEmployeeData = "
						<Company>
						<CompanyID>".$row["payroll_company_ID"]."</CompanyID>
							<CompanyName>".$row["company_shortname"]."</CompanyName>
							<Entries>\n";
                    }
                    $runningSumDebitAmount += $row["debit_amount"];
                    $runningSumCreditAmount += $row["credit_amount"];
                    $corporationDebitAmount += $row["debit_amount"];
                    $corporationCreditAmount += $row["credit_amount"];
                    $entryCollector[] = "
					<Entry ".	($entryCounter % 30 == 0?	"doPageBreak=\"true\""	:	"doPageBreak=\"false\"").">
						<CompanyID>".$row["payroll_company_ID"]."</CompanyID>
						<Account>".$row["account_no"]."</Account>
						<CounterAccount>".$row["counter_account_no"]."</CounterAccount>
						<DebitAmount>".$row["debit_amount"]."</DebitAmount>
						<CreditAmount>".$row["credit_amount"]."</CreditAmount>
						<EntryText>".$row["entry_text"]."</EntryText>
						<RunningSumDebitAmount>".$runningSumDebitAmount."</RunningSumDebitAmount>
						<RunningSumCreditAmount>".$runningSumCreditAmount."</RunningSumCreditAmount>
					</Entry>\n";
                    $entryCounter += 1;
                 }
                 if($singleEmployeeData != "") {
                     fwrite($fp, $singleEmployeeData.str_replace(array("&","_","%","#"), array("\\&","\\_","\\%","\\#"), implode("",$entryCollector))."\t\t\t\t</Entries>\n\t\t\t<CompanyDebitAmount>".$runningSumDebitAmount."</CompanyDebitAmount>\n\t\t\t<CompanyCreditAmount>".$runningSumCreditAmount."</CompanyCreditAmount>\n\t\t</Company>\n");
                 }
                 fwrite($fp, "
                 	</Companies>
                 		<CorporationDebitAmount>".$corporationDebitAmount."</CorporationDebitAmount>
                 		<CorporationCreditAmount>".$corporationCreditAmount."</CorporationCreditAmount>
                 		</Corporation>\n</Report>\n");
                 $fm->fclose();
                 break;
             case 4:
                 // Auswertung nach Firma / Kst
                 $reportTemplate = "AccountingJournal[Company][Cost_center]"; 

                 fwrite($fp,
                 "<Report name=\"".$ReportName."\" lang=\"de\">\n\t<Header>".
                 $payroll_reporting_functions->getReportingCompany("MainCompany", 0).
                 $payroll_reporting_functions->getPrintDateTime()."
				<Year>".$param["year"]."</Year>
				<Period>".$periodTitle."</Period>
				<AccountType>".$entryTable."</AccountType>".
                "\n\t</Header>
                <Corporation>
                	<Companies>\n");
                 
                 $query = "  SELECT 
                                IF(accetry.payroll_company_ID = 0, emp.payroll_company_ID, accetry.payroll_company_ID) AS payroll_company_ID,
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
                                payroll_company comp ON comp.id = IF(accetry.payroll_company_ID = 0, emp.payroll_company_ID, accetry.payroll_company_ID)
									INNER JOIN
                                payroll_account_label acclbl ON acclbl.payroll_account_ID = accetry.payroll_account_ID
                                    AND acclbl.payroll_year_ID = ".$param["year"]."
                                    AND acclbl.language = '".session_control::getSessionInfo("language")."'
                            WHERE accetry.payroll_period_ID = ".$payrollPeriodID." AND `amount_quantity` = 0 AND `account_no` <> ''".
                            ($param["company"] == false ? "":" AND IF(accetry.payroll_company_ID = 0, emp.payroll_company_ID, accetry.payroll_company_ID) = ".$param["company"]).
                            ($param["cost_center"] == false ? "": " AND accetry.cost_center = ".$param["cost_center"])."
                            GROUP BY    IF(accetry.payroll_company_ID = 0, emp.payroll_company_ID, accetry.payroll_company_ID), 
                                        accetry.cost_center, 
                                        accetry.entry_text
                            ORDER BY    IF(accetry.payroll_company_ID = 0, emp.payroll_company_ID, accetry.payroll_company_ID), 
                                        accetry.cost_center, 
                                        accetry.entry_text";
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
                             fwrite($fp, $singleEmployeeData.str_replace(array("&","_","%","#"), array("\\&","\\_","\\%","\\#"), implode("",$entryCollector))."\t\t\t\t</Entries>\n\t\t\t<CompanyDebitAmount>".$runningSumDebitAmount."</CompanyDebitAmount>\n\t\t\t<CompanyCreditAmount>".$runningSumCreditAmount."</CompanyCreditAmount>\n\t\t</Company>\n");
                         }
                         $runningSumDebitAmount = 0;
                         $runningSumCreditAmount = 0;
                         $entryCounter = 1;
                         $lastPayrollCompanyID = $row["payroll_company_ID"];
                         $entryCollector = array();
                         $singleEmployeeData = "
                         	<Company>
                         		<CompanyID>".$row["payroll_company_ID"]."</CompanyID>
                         		<CompanyName>".$row["company_shortname"]."</CompanyName>
                         		<Entries>\n";
                    }
                    $runningSumDebitAmount += $row["debit_amount"];
                    $runningSumCreditAmount += $row["credit_amount"];
                    $corporationDebitAmount += $row["debit_amount"];
                    $corporationCreditAmount += $row["credit_amount"];
                    $entryCollector[] = "
					<Entry ".	($entryCounter % 30 == 0?	"doPageBreak=\"true\""	:	"doPageBreak=\"false\"").">
						<CompanyID>".$row["payroll_company_ID"]."</CompanyID>
						<CostCenter>".$row["cost_center"]."</CostCenter>
						<DebitAmount>".$row["debit_amount"]."</DebitAmount>
						<CreditAmount>".$row["credit_amount"]."</CreditAmount>
						<EntryText>".$row["entry_text"]."</EntryText>
						<RunningSumDebitAmount>".$runningSumDebitAmount."</RunningSumDebitAmount>
						<RunningSumCreditAmount>".$runningSumCreditAmount."</RunningSumCreditAmount>
					</Entry>\n";
                    $entryCounter += 1;
                 }
                 if($singleEmployeeData != "") {
                     fwrite($fp, $singleEmployeeData.str_replace(array("&","_","%","#"), array("\\&","\\_","\\%","\\#"), implode("",$entryCollector))."\t\t\t\t</Entries>\n\t\t\t<CompanyDebitAmount>".$runningSumDebitAmount."</CompanyDebitAmount>\n\t\t\t<CompanyCreditAmount>".$runningSumCreditAmount."</CompanyCreditAmount>\n\t\t</Company>\n");
                 }
                 fwrite($fp, "\t\t</Companies>\n\t\t<CorporationDebitAmount>".$corporationDebitAmount."</CorporationDebitAmount>\n\t\t<CorporationCreditAmount>".$corporationCreditAmount."</CorporationCreditAmount>\n\t</Corporation>\n</Report>\n");
                 $fm->fclose();
                 break;
             case 5:
                 // Auswertung nach Firma / Konto
                 $reportTemplate = "AccountingJournal[Company][Account]"; 

                 fwrite($fp,
                 "<Report name=\"".$ReportName."\" lang=\"de\">\n\t<Header>".
                 $payroll_reporting_functions->getReportingCompany("MainCompany", 0).
                 $payroll_reporting_functions->getPrintDateTime()."
				<Year>".$param["year"]."</Year>
				<Period>".$periodTitle."</Period>
				<AccountType>".$entryTable."</AccountType>".
                "\n\t</Header>
                <Corporation>
                	<Companies>\n");
                 
                 $query = "  SELECT 
                                IF(accetry.payroll_company_ID = 0, emp.payroll_company_ID, accetry.payroll_company_ID) AS payroll_company_ID,
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
                                payroll_company comp ON comp.id = IF(accetry.payroll_company_ID = 0, emp.payroll_company_ID, accetry.payroll_company_ID)
									INNER JOIN
                                payroll_account_label acclbl ON acclbl.payroll_account_ID = accetry.payroll_account_ID
                                    AND acclbl.payroll_year_ID = ".$param["year"]."
                                    AND acclbl.language = '".session_control::getSessionInfo("language")."'
                            WHERE accetry.payroll_period_ID = ".$payrollPeriodID." AND `amount_quantity` = 0 AND `account_no` <> ''".
                            ($param["company"] == false ? "":" AND IF(accetry.payroll_company_ID = 0, emp.payroll_company_ID, accetry.payroll_company_ID) = ".$param["company"])."
                            GROUP BY    IF(accetry.payroll_company_ID = 0, emp.payroll_company_ID, accetry.payroll_company_ID), 
                                        accetry.account_no, 
                                        accetry.entry_text
                            ORDER BY    IF(accetry.payroll_company_ID = 0, emp.payroll_company_ID, accetry.payroll_company_ID), 
                                        accetry.account_no, 
                                        accetry.entry_text";
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
                             fwrite($fp, $singleEmployeeData.str_replace(array("&","_","%","#"), array("\\&","\\_","\\%","\\#"), implode("",$entryCollector))."\t\t\t\t</Entries>\n\t\t\t<CompanyDebitAmount>".$runningSumDebitAmount."</CompanyDebitAmount>\n\t\t\t<CompanyCreditAmount>".$runningSumCreditAmount."</CompanyCreditAmount>\n\t\t</Company>\n");
                         }
                         $runningSumDebitAmount = 0;
                         $runningSumCreditAmount = 0;
                         $entryCounter = 1;
                         $lastPayrollCompanyID = $row["payroll_company_ID"];
                         $entryCollector = array();
                         $singleEmployeeData = "
                         	<Company>
                         		<CompanyID>".$row["payroll_company_ID"]."</CompanyID>
                         		<CompanyName>".$row["company_shortname"]."</CompanyName>
                         		<Entries>\n";
                     }
                     $runningSumDebitAmount += $row["debit_amount"];
                     $runningSumCreditAmount += $row["credit_amount"];
                     $corporationDebitAmount += $row["debit_amount"];
                     $corporationCreditAmount += $row["credit_amount"];
                    $entryCollector[] = "
					<Entry ".	($entryCounter % 30 == 0?	"doPageBreak=\"true\""	:	"doPageBreak=\"false\"").">
						<CompanyID>".$row["payroll_company_ID"]."</CompanyID>
						<Account>".$row["account_no"]."</Account>
						<DebitAmount>".$row["debit_amount"]."</DebitAmount>
						<CreditAmount>".$row["credit_amount"]."</CreditAmount>
						<EntryText>".$row["entry_text"]."</EntryText>
						<RunningSumDebitAmount>".$runningSumDebitAmount."</RunningSumDebitAmount>
						<RunningSumCreditAmount>".$runningSumCreditAmount."</RunningSumCreditAmount>
					</Entry>\n";
                     $entryCounter += 1;
                 }
                 if($singleEmployeeData != "") {
                     fwrite($fp, $singleEmployeeData.str_replace(array("&","_","%","#"), array("\\&","\\_","\\%","\\#"), implode("",$entryCollector))."\t\t\t\t</Entries>\n\t\t\t<CompanyDebitAmount>".$runningSumDebitAmount."</CompanyDebitAmount>\n\t\t\t<CompanyCreditAmount>".$runningSumCreditAmount."</CompanyCreditAmount>\n\t\t</Company>\n");
                 }
                 fwrite($fp, "
                 	</Companies>
                 		<CorporationDebitAmount>".$corporationDebitAmount."</CorporationDebitAmount>
                 		<CorporationCreditAmount>".$corporationCreditAmount."</CorporationCreditAmount>
                 		</Corporation>\n</Report>\n");
                 $fm->fclose();
                 break;
             case 6:
                 // Auswertung nach Konto / Gegenkonto / Kst
                 $reportTemplate = "AccountingJournal[Account][Counter_account][Cost_center]"; 

                 fwrite($fp,
                 "<Report name=\"".$ReportName."\" lang=\"de\">\n\t<Header>".
                 $payroll_reporting_functions->getReportingCompany("MainCompany", 0).
                 $payroll_reporting_functions->getPrintDateTime()."
				<Year>".$param["year"]."</Year>
				<Period>".$periodTitle."</Period>
				<AccountType>".$entryTable."</AccountType>".
                "\n\t</Header>				
                <Corporation>
              		<Entries>\n");
                  
                 
                 $query = "SELECT 
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
                            WHERE accetry.payroll_period_ID = ".$payrollPeriodID." AND `amount_quantity` = 0 AND `account_no` <> ''
                            GROUP BY    accetry.account_no, 
                                        accetry.counter_account_no, 
                                        accetry.cost_center, 
                                        accetry.entry_text
                            ORDER BY    accetry.account_no , 
                                        accetry.counter_account_no , 
                                        accetry.cost_center, 
                                        accetry.entry_text";
                 $result = $system_database_manager->executeQuery($query, "payroll_report_".$ReportName);
                 $corporationDebitAmount = 0;
                 $corporationCreditAmount = 0;
                 $entryCounter = 1;
                 $singleEmployeeData = "";
                 foreach($result as $row) {
                     $corporationDebitAmount += $row["debit_amount"];
                     $corporationCreditAmount += $row["credit_amount"];

                     fwrite($fp, 
                     "<Entry ".	($entryCounter % 30 == 0?	"doPageBreak=\"true\""	:	"doPageBreak=\"false\"").">
						<Account>".$row["account_no"]."</Account>
						<CounterAccount>".$row["counter_account_no"]."</CounterAccount>
						<CostCenter>".$row["cost_center"]."</CostCenter>
						<DebitAmount>".$row["debit_amount"]."</DebitAmount>
						<CreditAmount>".$row["credit_amount"]."</CreditAmount>
						<EntryText>".$row["entry_text"]."</EntryText>
						<RunningSumDebitAmount>".$corporationDebitAmount."</RunningSumDebitAmount>
						<RunningSumCreditAmount>".$corporationCreditAmount."</RunningSumCreditAmount>
					</Entry>\n");
                     $entryCounter += 1;
                 }
                 fwrite($fp, "\t\t</Entries>\n\t\t<CorporationDebitAmount>".$corporationDebitAmount."</CorporationDebitAmount>\n\t\t<CorporationCreditAmount>".$corporationCreditAmount."</CorporationCreditAmount>\n\t</Corporation>\n</Report>\n");
                 $fm->fclose();
                 break;
             case 7:
                 // Auswertung nach Kst / Konto / Gegenkonto
                 $reportTemplate = "AccountingJournal[Cost_center][Account][Counter_account]"; 

                 fwrite($fp,
                 "<Report name=\"".$ReportName."\" lang=\"de\">\n\t<Header>".
                 $payroll_reporting_functions->getReportingCompany("MainCompany", 0).
                 $payroll_reporting_functions->getPrintDateTime()."
				<Year>".$param["year"]."</Year>
				<Period>".$periodTitle."</Period>
				<AccountType>".$entryTable."</AccountType>".
                "\n\t</Header>
                <Corporation>
              		<Entries>\n");
                 
                 $query = "SELECT 
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
                            WHERE accetry.payroll_period_ID = ".$payrollPeriodID." AND `amount_quantity` = 0 AND `account_no` <> ''".
                            ($param["cost_center"] == false ? "": " AND accetry.cost_center = ".$param["cost_center"])."
                            GROUP BY    accetry.cost_center, 
                                        accetry.account_no, 
                                        accetry.counter_account_no, 
                                        accetry.entry_text
                            ORDER BY    accetry.cost_center , 
                                        accetry.account_no , 
                                        accetry.counter_account_no, 
                                        accetry.entry_text";
                 $result = $system_database_manager->executeQuery($query, "payroll_report_".$ReportName);
                 $corporationDebitAmount = 0;
                 $corporationCreditAmount = 0;
                 $entryCounter = 1;
                 $singleEmployeeData = "";
                 foreach($result as $row) {
                     $corporationDebitAmount += $row["debit_amount"];
                     $corporationCreditAmount += $row["credit_amount"];
                     fwrite($fp, 
                     "<Entry ".	($entryCounter % 30 == 0?	"doPageBreak=\"true\""	:	"doPageBreak=\"false\"").">
						<Account>".$row["account_no"]."</Account>
						<CounterAccount>".$row["counter_account_no"]."</CounterAccount>
						<CostCenter>".$row["cost_center"]."</CostCenter>
						<DebitAmount>".$row["debit_amount"]."</DebitAmount>
						<CreditAmount>".$row["credit_amount"]."</CreditAmount>
						<EntryText>".$row["entry_text"]."</EntryText>
						<RunningSumDebitAmount>".$corporationDebitAmount."</RunningSumDebitAmount>
						<RunningSumCreditAmount>".$corporationCreditAmount."</RunningSumCreditAmount>
					</Entry>\n");
                     $entryCounter += 1;
                 }
                 fwrite($fp, "\t\t</Entries>\n\t\t<CorporationDebitAmount>".$corporationDebitAmount."</CorporationDebitAmount>\n\t\t<CorporationCreditAmount>".$corporationCreditAmount."</CorporationCreditAmount>\n\t</Corporation>\n</Report>\n");
                 $fm->fclose();
                 break;
             case 8:
                 // Auswertung nach Konto / Gegenkonto
                 $reportTemplate = "AccountingJournal[Account][Counter_account]"; 

                 fwrite($fp,
                 "<Report name=\"".$ReportName."\" lang=\"de\">\n\t<Header>".
                 $payroll_reporting_functions->getReportingCompany("MainCompany", 0).
                 $payroll_reporting_functions->getPrintDateTime()."
				<Year>".$param["year"]."</Year>
				<Period>".$periodTitle."</Period>
				<AccountType>".$entryTable."</AccountType>".
                "\n\t</Header>				
                <Corporation>
              		<Entries>\n");
                 
                 $query = 
"SELECT 
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
WHERE accetry.payroll_period_ID = ".$payrollPeriodID." AND `amount_quantity` = 0 AND `account_no` <> ''
GROUP BY    accetry.account_no, 
			accetry.counter_account_no, 
			accetry.entry_text
ORDER BY    accetry.account_no , 
			accetry.counter_account_no, 
			accetry.entry_text";
                 $result = $system_database_manager->executeQuery($query, "payroll_report_".$ReportName);
                 $corporationDebitAmount = 0;
                 $corporationCreditAmount = 0;
                 $entryCounter = 1;
                 $singleEmployeeData = "";
                 foreach($result as $row) {
                     $corporationDebitAmount += $row["debit_amount"];
                     $corporationCreditAmount += $row["credit_amount"];
                     fwrite($fp, 
                     "<Entry ".	($entryCounter % 30 == 0?	"doPageBreak=\"true\""	:	"doPageBreak=\"false\"").">
						<Account>".$row["account_no"]."</Account>\
						<CounterAccount>".$row["counter_account_no"]."</CounterAccount>
						<DebitAmount>".$row["debit_amount"]."</DebitAmount>
						<CreditAmount>".$row["credit_amount"]."</CreditAmount>
						<EntryText>".$row["entry_text"]."</EntryText>
						<RunningSumDebitAmount>".$corporationDebitAmount."</RunningSumDebitAmount>
						<RunningSumCreditAmount>".$corporationCreditAmount."</RunningSumCreditAmount>
					</Entry>\n");
                    $entryCounter += 1;
                 }
                 fwrite($fp, "\t\t</Entries>\n\t\t<CorporationDebitAmount>".$corporationDebitAmount."</CorporationDebitAmount>\n\t\t<CorporationCreditAmount>".$corporationCreditAmount."</CorporationCreditAmount>\n\t</Corporation>\n</Report>\n");
                 $fm->fclose();
                 break;
             case 9:
                 // Auswertung nach Kst
                 $reportTemplate = "AccountingJournal[Cost_center]"; 

                 fwrite($fp,
                 "<Report name=\"".$ReportName."\" lang=\"de\">\n\t<Header>".
                 $payroll_reporting_functions->getReportingCompany("MainCompany", 0).
                 $payroll_reporting_functions->getPrintDateTime()."
				<Year>".$param["year"]."</Year>
				<Period>".$periodTitle."</Period>
				<AccountType>".$entryTable."</AccountType>".
                "\n\t</Header>				
                <Corporation>
              		<Entries>\n");
                 
                 $query = "SELECT 
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
                            WHERE accetry.payroll_period_ID = ".$payrollPeriodID." AND `amount_quantity` = 0 AND `account_no` <> ''".
                            ($param["cost_center"] == false ? "": " AND accetry.cost_center = ".$param["cost_center"])."
                            GROUP BY    accetry.cost_center, 
                                        accetry.entry_text
                            ORDER BY    accetry.cost_center, 
                                        accetry.entry_text";
                 $result = $system_database_manager->executeQuery($query, "payroll_report_".$ReportName);
                 $corporationDebitAmount = 0;
                 $corporationCreditAmount = 0;
                 $entryCounter = 1;
                 $singleEmployeeData = "";
                 foreach($result as $row) {
                     $corporationDebitAmount += $row["debit_amount"];
                     $corporationCreditAmount += $row["credit_amount"];
                     fwrite($fp, 
                     "<Entry ".	($entryCounter % 30 == 0?	"doPageBreak=\"true\""	:	"doPageBreak=\"false\"").">
						<CostCenter>".$row["cost_center"]."</CostCenter>
						<DebitAmount>".$row["debit_amount"]."</DebitAmount>
						<CreditAmount>".$row["credit_amount"]."</CreditAmount>
						<EntryText>".$row["entry_text"]."</EntryText>
						<RunningSumDebitAmount>".$corporationDebitAmount."</RunningSumDebitAmount>
						<RunningSumCreditAmount>".$corporationCreditAmount."</RunningSumCreditAmount>
					</Entry>\n");
                    $entryCounter += 1;
                 }
                 fwrite($fp, "\t\t</Entries>\n\t\t<CorporationDebitAmount>".$corporationDebitAmount."</CorporationDebitAmount>\n\t\t<CorporationCreditAmount>".$corporationCreditAmount."</CorporationCreditAmount>\n\t</Corporation>\n</Report>\n");
                 $fm->fclose();
                 break;
             case 10:
                 // Auswertung nach Konto
                 $reportTemplate = "AccountingJournal[Account]"; 

                 fwrite($fp,
                 "<Report name=\"".$ReportName."\" lang=\"de\">\n\t<Header>".
                 $payroll_reporting_functions->getReportingCompany("MainCompany", 0).
                 $payroll_reporting_functions->getPrintDateTime()."
				<Year>".$param["year"]."</Year>
				<Period>".$periodTitle."</Period>
				<AccountType>".$entryTable."</AccountType>".
                "\n\t</Header>				
                 <Corporation>
                 <Entries>\n");
                 
                 $query = "SELECT 
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
                            WHERE accetry.payroll_period_ID = ".$payrollPeriodID." AND `amount_quantity` = 0 AND `account_no` <> ''
                            GROUP BY    accetry.account_no, 
                                        accetry.entry_text
                            ORDER BY    accetry.account_no, 
                                        accetry.entry_text";
                 $result = $system_database_manager->executeQuery($query, "payroll_report_".$ReportName);
                 $corporationDebitAmount = 0;
                 $corporationCreditAmount = 0;
                 $entryCounter = 1;
                 $singleEmployeeData = "";
                 foreach($result as $row) {
					$corporationDebitAmount += $row["debit_amount"];
					$corporationCreditAmount += $row["credit_amount"];
					fwrite($fp, 
					"<Entry ".	($entryCounter % 30 == 0?	"doPageBreak=\"true\""	:	"doPageBreak=\"false\"").">
						<Account>".$row["account_no"]."</Account>
						<DebitAmount>".$row["debit_amount"]."</DebitAmount>
						<CreditAmount>".$row["credit_amount"]."</CreditAmount>
						<EntryText>".$row["entry_text"]."</EntryText>
						<RunningSumDebitAmount>".$corporationDebitAmount."</RunningSumDebitAmount>
						<RunningSumCreditAmount>".$corporationCreditAmount."</RunningSumCreditAmount>
					</Entry>\n");
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
        require_once('payroll_auszahlen.php');
        $auszahlen = new auszahlen();
        require_once('payroll_reporting_functions.php');
        $payroll_reporting_functions = new payroll_reporting_functions();
        
        $statistikArr = array();
        
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
		$sql = "SELECT 
		emp.`id`
		, emp.`EmployeeNumber`
		, emp.`Firstname`
		, emp.`Lastname`
		, emp.`payroll_company_ID`
		, emp.`CodeAHV`
		, emp.`CodeALV`
		, emp.`CodeUVG`
		, emp.`CodeUVGZ1`
		, emp.`CodeUVGZ2`
		, emp.`CodeBVG`
		, emp.`CodeKTG`
		, emp.`DateOfBirth`
		, IF(`SV-AS-Number`!=''
		,`SV-AS-Number`,`AHV-AVS-Number`) as `SV-AS-Number` 
		FROM `payroll_employee` emp 
		INNER JOIN `payroll_period_employee` ppe ON ppe.payroll_employee_ID=emp.id 
		INNER JOIN `payroll_period` prd ON prd.`id`=ppe.`payroll_period_ID` 
		AND prd.`payroll_year_ID`=".$param["year"]." 
		INNER JOIN payroll_tmp_change_mng emplst ON emplst.core_user_id=".session_control::getSessionInfo("id")." 
		AND emplst.numID=ppe.payroll_employee_ID 
		WHERE ppe.`processing`!=0 
		GROUP BY emp.`id`";
		$result = $system_database_manager->executeQuery($sql, "payroll_report_CalculationJournal");
		foreach($result as $row) $arrEmployees["id".$row["id"]] = $row;

		//Beschaeftigungsdaten (Ein-/Austritte) pro Mitarbeiter in einen assoziativen Array einlesen
		$arrEmployeePeriods = array();
		$sql = "SELECT 
		pep.`payroll_employee_ID`
		, pep.`DateFrom`
		, IF(pep.`DateTo`!='0000-00-00',pep.`DateTo`,'') as DateTo 
		FROM `payroll_employment_period` pep 
		INNER JOIN `payroll_tmp_change_mng` emplst ON pep.`payroll_employee_ID`=emplst.`numID` 
		AND emplst.`core_user_id`=".session_control::getSessionInfo("id")." 
		WHERE pep.`DateFrom`<='".$param["year"]."-12-31' 
		AND (pep.`DateTo`>='".$param["year"]."-01-01' OR pep.`DateTo`='0000-00-00') 
		ORDER BY pep.`payroll_employee_ID`, pep.`DateFrom`";
		$result = $system_database_manager->executeQuery($sql, "payroll_report_CalculationJournal");
		foreach($result as $row) {
			if(isset($arrEmployeePeriods["id".$row["payroll_employee_ID"]])) {
				$arrEmployeePeriods["id".$row["payroll_employee_ID"]] = "\t\t\t\t<Period><From>".$row["DateFrom"]."</From><Until>".$row["DateTo"]."</Until></Period>\n";
			} else {
				$arrEmployeePeriods["id".$row["payroll_employee_ID"]] = "\t\t\t\t<Period><From>".$row["DateFrom"]."</From><Until>".$row["DateTo"]."</Until></Period>\n";
			}
		}
		$fp = $fm->setFile("data.xml")->fopen("w");
		fwrite($fp, "<Report name=\"PayrollAccountJournal\" lang=\"de\">
		<Header>".$payroll_reporting_functions->getReportingCompany("Company", 0)
                 .$payroll_reporting_functions->getPrintDateTime()."
			<Year>".$param["year"]."</Year>
			<ExtendedMonthView>".$extendedMonthView."</ExtendedMonthView>
		</Header>
		<Employees>\n");
		//TODO: Moegliche, noch zu loesende Probleme: 
		//im aktuellen Jahr, ist die aktuelle Periode nicht (oder nur partiell) in "payroll_calculation_entry" gespeichert. 
		//Stattdessen sind die Werte aus "payroll_calculation_entry" zu beziehen; 
		//zugleich muessen die teilweise vorhandenen Daten in "payroll_calculation_entry" herausgefiltert werden.
		//TODO: Einzelne MA selektieren (ev. "payroll_tmp_change_mng" verwenden)
		if($isCurrentYear) {
			$sql = "SELECT 
			empl.id as EmployeeID
			, acc.id as AccountNumber
			, acc.print_account
			, acclbl.label
			, SUM(IF(prd.major_period=1,calc.quantity,0)) as prd1q
			, SUM(IF(prd.major_period=1,calc.amount,0)) as prd1a
			, SUM(IF(prd.major_period=2,calc.quantity,0)) as prd2q
			, SUM(IF(prd.major_period=2,calc.amount,0)) as prd2a
			, SUM(IF(prd.major_period=3,calc.quantity,0)) as prd3q
			, SUM(IF(prd.major_period=3,calc.amount,0)) as prd3a
			, SUM(IF(prd.major_period=4,calc.quantity,0)) as prd4q
			, SUM(IF(prd.major_period=4,calc.amount,0)) as prd4a
			, SUM(IF(prd.major_period=5,calc.quantity,0)) as prd5q
			, SUM(IF(prd.major_period=5,calc.amount,0)) as prd5a
			, SUM(IF(prd.major_period=6,calc.quantity,0)) as prd6q
			, SUM(IF(prd.major_period=6,calc.amount,0)) as prd6a
			, SUM(IF(prd.major_period=7,calc.quantity,0)) as prd7q
			, SUM(IF(prd.major_period=7,calc.amount,0)) as prd7a
			, SUM(IF(prd.major_period=8,calc.quantity,0)) as prd8q
			, SUM(IF(prd.major_period=8,calc.amount,0)) as prd8a
			, SUM(IF(prd.major_period=9,calc.quantity,0)) as prd9q
			, SUM(IF(prd.major_period=9,calc.amount,0)) as prd9a
			, SUM(IF(prd.major_period=10,calc.quantity,0)) as prd10q
			, SUM(IF(prd.major_period=10,calc.amount,0)) as prd10a
			, SUM(IF(prd.major_period=11,calc.quantity,0)) as prd11q
			, SUM(IF(prd.major_period=11,calc.amount,0)) as prd11a
			, SUM(IF(prd.major_period=12,calc.quantity,0)) as prd12q
			, SUM(IF(prd.major_period=12,calc.amount,0)) as prd12a
			, SUM(IF(prd.major_period=13,calc.quantity,0)) as prd13q
			, SUM(IF(prd.major_period=13,calc.amount,0)) as prd13a
			, SUM(IF(prd.major_period=14,calc.quantity,0)) as prd14q
			, SUM(IF(prd.major_period=14,calc.amount,0)) as prd14a
			, SUM(IF(prd.major_period=15,calc.quantity,0)) as prd15q
			, SUM(IF(prd.major_period=15,calc.amount,0)) as prd15a
			, SUM(IF(prd.major_period=16,calc.quantity,0)) as prd16q
			, SUM(IF(prd.major_period=16,calc.amount,0)) as prd16a 
			FROM (SELECT * FROM payroll_calculation_entry 
			INNER JOIN payroll_tmp_change_mng emplst ON emplst.core_user_id=".session_control::getSessionInfo("id")." 
					AND emplst.numID=payroll_calculation_entry.payroll_employee_ID 
			WHERE payroll_year_ID=".$param["year"]." 
			AND payroll_period_ID!=".$payrollPeriodID." 
			UNION SELECT * FROM payroll_calculation_current 
			INNER JOIN payroll_tmp_change_mng emplst ON emplst.core_user_id=".session_control::getSessionInfo("id")." 
			AND emplst.numID=payroll_calculation_current.payroll_employee_ID) calc 
			INNER JOIN payroll_employee empl ON empl.id=calc.payroll_employee_ID 
			INNER JOIN payroll_account acc ON acc.id=calc.payroll_account_ID 
			AND acc.payroll_year_ID=calc.payroll_year_ID 
			AND acc.print_account!=0 
			INNER JOIN payroll_account_label acclbl ON acclbl.payroll_account_ID=acc.id 
			AND acclbl.payroll_year_ID=acc.payroll_year_ID 
			AND acclbl.language='".session_control::getSessionInfo("language")."' 
			INNER JOIN payroll_period prd ON prd.id=calc.payroll_period_ID 
			GROUP BY calc.payroll_employee_ID, acc.id 
			ORDER BY empl.Lastname, empl.Firstname, calc.payroll_employee_ID, acc.id";
		}
		else {
			$sql = "SELECT
			  empl.id as EmployeeID
			, acc.id as AccountNumber
			, acc.print_account
			, acclbl.label
			, SUM(IF(prd.major_period=1,calc.quantity,0)) as prd1q
			, SUM(IF(prd.major_period=1,calc.amount,0)) as prd1a
			, SUM(IF(prd.major_period=2,calc.quantity,0)) as prd2q
			, SUM(IF(prd.major_period=2,calc.amount,0)) as prd2a
			, SUM(IF(prd.major_period=3,calc.quantity,0)) as prd3q
			, SUM(IF(prd.major_period=3,calc.amount,0)) as prd3a
			, SUM(IF(prd.major_period=4,calc.quantity,0)) as prd4q
			, SUM(IF(prd.major_period=4,calc.amount,0)) as prd4a
			, SUM(IF(prd.major_period=5,calc.quantity,0)) as prd5q
			, SUM(IF(prd.major_period=5,calc.amount,0)) as prd5a
			, SUM(IF(prd.major_period=6,calc.quantity,0)) as prd6q
			, SUM(IF(prd.major_period=6,calc.amount,0)) as prd6a
			, SUM(IF(prd.major_period=7,calc.quantity,0)) as prd7q
			, SUM(IF(prd.major_period=7,calc.amount,0)) as prd7a
			, SUM(IF(prd.major_period=8,calc.quantity,0)) as prd8q
			, SUM(IF(prd.major_period=8,calc.amount,0)) as prd8a
			, SUM(IF(prd.major_period=9,calc.quantity,0)) as prd9q
			, SUM(IF(prd.major_period=9,calc.amount,0)) as prd9a
			, SUM(IF(prd.major_period=10,calc.quantity,0)) as prd10q
			, SUM(IF(prd.major_period=10,calc.amount,0)) as prd10a
			, SUM(IF(prd.major_period=11,calc.quantity,0)) as prd11q
			, SUM(IF(prd.major_period=11,calc.amount,0)) as prd11a
			, SUM(IF(prd.major_period=12,calc.quantity,0)) as prd12q
			, SUM(IF(prd.major_period=12,calc.amount,0)) as prd12a
			, SUM(IF(prd.major_period=13,calc.quantity,0)) as prd13q
			, SUM(IF(prd.major_period=13,calc.amount,0)) as prd13a
			, SUM(IF(prd.major_period=14,calc.quantity,0)) as prd14q
			, SUM(IF(prd.major_period=14,calc.amount,0)) as prd14a
			, SUM(IF(prd.major_period=15,calc.quantity,0)) as prd15q
			, SUM(IF(prd.major_period=15,calc.amount,0)) as prd15a
			, SUM(IF(prd.major_period=16,calc.quantity,0)) as prd16q
			, SUM(IF(prd.major_period=16,calc.amount,0)) as prd16a 
			FROM payroll_calculation_entry calc 
			INNER JOIN payroll_employee empl ON empl.id=calc.payroll_employee_ID 
			INNER JOIN payroll_account acc ON acc.id=calc.payroll_account_ID 
			AND acc.payroll_year_ID=calc.payroll_year_ID 
			AND acc.print_account!=0 
			INNER JOIN payroll_account_label acclbl ON acclbl.payroll_account_ID=acc.id 
			AND acclbl.payroll_year_ID=acc.payroll_year_ID 
			AND acclbl.language='".session_control::getSessionInfo("language")."' 
			INNER JOIN payroll_period prd ON prd.id=calc.payroll_period_ID 
			INNER JOIN payroll_tmp_change_mng emplst ON emplst.core_user_id=".session_control::getSessionInfo("id")." 
			AND emplst.numID=calc.payroll_employee_ID 
			WHERE calc.payroll_year_ID=".$param["year"]." 
			GROUP BY calc.payroll_employee_ID, acc.id 
			ORDER BY empl.Lastname, empl.Firstname, calc.payroll_employee_ID, acc.id";
		}
		$result = $system_database_manager->executeQuery($sql, "payroll_report_CalculationJournal");
		$lastEmployeeID = 0;
		$entryCollector = array();
		$entryCollectorRekap = array();
		$rekap = array();
		$singleEmployeeData = "";
		$maxLohn = 0.0;
		$maxMonatsLohn = 0.0;
		$rowTotal = 0.0;
		//communication_interface::alert(print_r($result, true));
		
		//Max Lohn-Bestimmung für das Auswählen des XSLT-Templates
		$mitarb = "";
		foreach($result as $row) {
			if ($row["AccountNumber"] == "5000") {
				$mitarb = $mitarb .",". $row["EmployeeID"];
				//communication_interface::alert(print_r($row, true));
				for($i=1;$i<17;$i++) {
					$rowTotal = $row["prd".$i."a"];
					if ($maxLohn < $rowTotal) $maxLohn = $rowTotal;
				}
			}
			if ($maxMonatsLohn < $maxLohn) $maxMonatsLohn = $maxLohn;
		}
		//communication_interface::alert($mitarb."\nmaxMonatsLohn:".$maxMonatsLohn."\n   maxLohn:".$maxLohn);

		
		
		$rowTotal = 0.0;
		foreach($result as $row) {
			$hatQ = "N";
			$hatA = "N";
			$rowTotal = 0.0;
			if($row["EmployeeID"] != $lastEmployeeID) {
				//the employee changed!
				if($singleEmployeeData != "") {
					//there are data for writing to the XML file
					//fwrite($fp, $singleEmployeeData.str_replace(array("&","_","%","#"), array("\\&","\\_","\\%","\\#"), implode("",$entryCollector))."</Entries>\n\t\t\t</Employee>\n");
					fwrite($fp, $singleEmployeeData.str_replace(array("&","%","#"), array("\\&","\\%","\\#"), implode("",$entryCollector))."</Entries>\n\t\t\t</Employee>\n");
				}
				$curEmplData = $arrEmployees["id".$row["EmployeeID"]];
				$lastEmployeeID = $row["EmployeeID"];
				$entryCollector = array();
				$entryCollectorRekap = array();
				$eid = "id".$lastEmployeeID;
				$singleEmployeeData = "\t\t\t<Employee>
				<EmployeeNumber>".$curEmplData["EmployeeNumber"]."</EmployeeNumber>
				<CompanyID>".$curEmplData["payroll_company_ID"]."</CompanyID>
				<Firstname>".$curEmplData["Firstname"]."</Firstname>
				<Lastname>".$curEmplData["Lastname"]."</Lastname>
				<DateOfBirth>".$curEmplData["DateOfBirth"]."</DateOfBirth>
				<SV-AS-Number>".$curEmplData["SV-AS-Number"]."</SV-AS-Number>
				<CodeAHV>".$curEmplData["CodeAHV"]."</CodeAHV>
				<CodeALV>".$curEmplData["CodeALV"]."</CodeALV>
				<CodeKTG>".$curEmplData["CodeKTG"]."</CodeKTG>
				<CodeUVG>".$curEmplData["CodeUVG"]."</CodeUVG>
				<CodeBVG>".$curEmplData["CodeBVG"]."</CodeBVG>
				<EmploymentPeriods>"."</EmploymentPeriods>
				<Entries>
					";
				//"<EmploymentPeriods>".implode("",$arrEmployeePeriods["id".$row["EmployeeID"]])."</EmploymentPeriods>
			}
			$quantityAbsWerte = 0.0;
			if($row["print_account"]!=1) {
				$rowTotal = 0.0;
				for($i=1;$i<17;$i++) {
					$rowTotal += $row["prd".$i."q"];
					$quantityAbsWerte += abs( $row["prd".$i."q"] );
					if (!isset($rekap[$row["AccountNumber"]]["Perioden"]["prd".$i."q"])) {
						$rekap[$row["AccountNumber"]]["Perioden"]["prd".$i."q"] = 0;
					}
					$rekap[$row["AccountNumber"]]["Perioden"]["prd".$i."q"] += $row["prd".$i."q"];
				}
				$tagQuantity = "<quantity>
						<Jan>".	 $row["prd1q"]."</Jan>
						<Feb>".	 $row["prd2q"]."</Feb>
						<Mar>".	 $row["prd3q"]."</Mar>
						<Apr>".	 $row["prd4q"]."</Apr>
						<May>".	 $row["prd5q"]."</May>
						<June>". $row["prd6q"]."</June>
						<July>". $row["prd7q"]."</July>
						<Aug>".	 $row["prd8q"]."</Aug>
						<Sept>". $row["prd9q"]."</Sept>
						<Oct>".	 $row["prd10q"]."</Oct>
						<Nov>".	 $row["prd11q"]."</Nov>
						<Dec>".	 $row["prd12q"]."</Dec>
						<Prd13>".$row["prd13q"]."</Prd13>
						<Prd14>".$row["prd14q"]."</Prd14>
						<Prd15>".$row["prd15q"]."</Prd15>
						<Prd16>".$row["prd16q"]."</Prd16>
						<Total>".$rowTotal	."</Total>
					</quantity>";
				$hatQ ="Y";
			} else {
				$tagQuantity = "";
				$hatQ = "N";
			}
			$amountAbsWerte = 0.0;
			if($row["print_account"]!=2) {
				$rowTotal = 0.0;
				for($i=1;$i<17;$i++) {
					$rowTotal += $row["prd".$i."a"];
					$amountAbsWerte += abs( $row["prd".$i."a"] );
					if (!isset($rekap[$row["AccountNumber"]]["Perioden"]["prd".$i."a"])) {
						$rekap[$row["AccountNumber"]]["Perioden"]["prd".$i."a"] = 0;
					}
					$rekap[$row["AccountNumber"]]["Perioden"]["prd".$i."a"] += $row["prd".$i."a"];
				}
				$tagAmount = "<amount>
						<Jan>".  $row["prd1a"]."</Jan>
						<Feb>".  $row["prd2a"]."</Feb>
						<Mar>".  $row["prd3a"]."</Mar>
						<Apr>".  $row["prd4a"]."</Apr>
						<May>".  $row["prd5a"]."</May>
						<June>". $row["prd6a"]."</June>
						<July>". $row["prd7a"]."</July>
						<Aug>".  $row["prd8a"]."</Aug>
						<Sept>". $row["prd9a"]."</Sept>
						<Oct>".  $row["prd10a"]."</Oct>
						<Nov>".  $row["prd11a"]."</Nov>
						<Dec>".  $row["prd12a"]."</Dec>
						<Prd13>".$row["prd13a"]."</Prd13>
						<Prd14>".$row["prd14a"]."</Prd14>
						<Prd15>".$row["prd15a"]."</Prd15>
						<Prd16>".$row["prd16a"]."</Prd16>
						<Total>".$rowTotal."</Total>
					</amount>";
				//$statistikArr[]=$tagAmount;
				$hatA = "Y";				
			} else {
				$tagAmount = "";
				$hatA = "N";				
			}
			$lbl = $row["label"];
			if ($maxMonatsLohn > 100000.00) {
				$lbl = $auszahlen->replaceUmlaute(utf8_decode($lbl));
				$lbl = trim(substr($lbl."               ",0,13));
			}
			$hatWerte = $quantityAbsWerte + $amountAbsWerte;
			if ($hatWerte > 0.0 ) {
				$entryCollector[] = "
				<Entry>
					<AccountNumber>".$row["AccountNumber"]."</AccountNumber>
					<AccountName>".$lbl."</AccountName>
					".$tagQuantity.$tagAmount."
				</Entry>
				";
			}
			$rowTotal = 0;
			
			if (!isset($rekap[$row["AccountNumber"]]["betroffeneMitarbeiterId"])) {	$rekap[$row["AccountNumber"]]["betroffeneMitarbeiterId"] = "";	} 	
			$rekap[$row["AccountNumber"]]["betroffeneMitarbeiterId"] .= ",".$row["EmployeeID"]; 	
			$rekap[$row["AccountNumber"]]["AccountNumber"] = $row["AccountNumber"]; 
			$rekap[$row["AccountNumber"]]["AccountName"] = $lbl; 	
			$rekap[$row["AccountNumber"]]["hatQ"] = $hatQ; 	
			$rekap[$row["AccountNumber"]]["hatA"] = $hatA;
		}
		if($singleEmployeeData != "") {
			//there are still a few more data for writing to the XML file
			//fwrite($fp, $singleEmployeeData.str_replace(array("&","_","%","#"), array("\\&","\\_","\\%","\\#"), implode("",$entryCollector))."</Entries>\n\t\t</Employee>\n");
			fwrite($fp, $singleEmployeeData.str_replace(array("&","%","#"), array("\\&","\\%","\\#"), implode("",$entryCollector))."</Entries>\n\t\t</Employee>\n");
		}
		
		//REKAP
		$rekapEmployeeData = 
		"
		<Employee>
			<EmployeeNumber>0</EmployeeNumber>
			<CompanyID>0</CompanyID>
			<Firstname>REKAP</Firstname>
			<Lastname>ZUSAMMENSTELLUNG</Lastname>
			<DateOfBirth></DateOfBirth>
			<SV-AS-Number></SV-AS-Number>
			<CodeAHV></CodeAHV>
			<CodeALV></CodeALV>
			<CodeKTG></CodeKTG>
			<CodeUVG></CodeUVG>
			<CodeBVG></CodeBVG>
			<EmploymentPeriods></EmploymentPeriods>
			<Entries>
			";
		$rekapEntries = "";
		$rekapMetaArray = array_keys($rekap);
		asort($rekapMetaArray);
		$statistikArr["P"."0"] = 0;
		foreach ($rekapMetaArray as $lohnart) {
			//communication_interface::alert("wert: ".$value."\n".print_r($rekap[$value],true));
			$P = array_keys($rekap[$lohnart]["Perioden"]);
			$rowTotal = 0;
			for ($i = 0; $i < 16; $i++) {
				$rowTotal += $rekap[$lohnart]["Perioden"][$P[$i]];
				$statistikArr["P".$i] += $rekap[$lohnart]["Perioden"][$P[$i]];
			}
			$periodenXML = "<Jan>".  $rekap[$lohnart]["Perioden"][$P[0]]."</Jan>
						<Feb>".  $rekap[$lohnart]["Perioden"][$P[1]]."</Feb>
						<Mar>".  $rekap[$lohnart]["Perioden"][$P[2]]."</Mar>
						<Apr>".  $rekap[$lohnart]["Perioden"][$P[3]]."</Apr>
						<May>".  $rekap[$lohnart]["Perioden"][$P[4]]."</May>
						<June>". $rekap[$lohnart]["Perioden"][$P[5]]."</June>
						<July>". $rekap[$lohnart]["Perioden"][$P[6]]."</July>
						<Aug>".  $rekap[$lohnart]["Perioden"][$P[7]]."</Aug>
						<Sept>". $rekap[$lohnart]["Perioden"][$P[8]]."</Sept>
						<Oct>".  $rekap[$lohnart]["Perioden"][$P[9]]."</Oct>
						<Nov>".  $rekap[$lohnart]["Perioden"][$P[10]]."</Nov>
						<Dec>".  $rekap[$lohnart]["Perioden"][$P[11]]."</Dec>
						<Prd13>".$rekap[$lohnart]["Perioden"][$P[12]]."</Prd13>
						<Prd14>".$rekap[$lohnart]["Perioden"][$P[13]]."</Prd14>
						<Prd15>".$rekap[$lohnart]["Perioden"][$P[14]]."</Prd15>
						<Prd16>".$rekap[$lohnart]["Perioden"][$P[15]]."</Prd16>
						<Total>$rowTotal</Total>";
			$hatWerte = abs( $rekap[$lohnart]["Perioden"][$P[0]] );
			$hatWerte += abs( $rekap[$lohnart]["Perioden"][$P[1]] );
			$hatWerte += abs( $rekap[$lohnart]["Perioden"][$P[2]] );
			$hatWerte += abs( $rekap[$lohnart]["Perioden"][$P[3]] );
			$hatWerte += abs( $rekap[$lohnart]["Perioden"][$P[4]] );
			$hatWerte += abs( $rekap[$lohnart]["Perioden"][$P[5]] );
			$hatWerte += abs( $rekap[$lohnart]["Perioden"][$P[6]] );
			$hatWerte += abs( $rekap[$lohnart]["Perioden"][$P[7]] );
			$hatWerte += abs( $rekap[$lohnart]["Perioden"][$P[8]] );
			$hatWerte += abs( $rekap[$lohnart]["Perioden"][$P[9]] );
			$hatWerte += abs( $rekap[$lohnart]["Perioden"][$P[10]] );
			$hatWerte += abs( $rekap[$lohnart]["Perioden"][$P[11]] );
			$hatWerte += abs( $rekap[$lohnart]["Perioden"][$P[12]] );
			$hatWerte += abs( $rekap[$lohnart]["Perioden"][$P[13]] );
			$hatWerte += abs( $rekap[$lohnart]["Perioden"][$P[14]] );
			$hatWerte += abs( $rekap[$lohnart]["Perioden"][$P[15]] );
			if ($hatWerte > 0.0) {
				$rekapEntries .= "
				<Entry>
					<AccountNumber>".$rekap[$lohnart]["AccountNumber"]."</AccountNumber>
					<AccountName>".$rekap[$lohnart]["AccountName"]."</AccountName>
					";						
				
			  if ($rekap[$lohnart]["hatA"] == "Y") {
				$rekapEntries 
				.= "<amount>
						".$periodenXML."
					</amount>";
			  } else {
				$rekapEntries 
				.= "<quantity>
						".$periodenXML."
					</quantity>";
			  }
				$rekapEntries .= "
				</Entry>";
			}
					
			//communication_interface::alert("wert: ".$lohnart."\nperiodenXML=".$periodenXML."\nP: ".print_r($P,true)."\n".print_r($rekap[$value]["Perioden"],true));
			//communication_interface::alert("wert: ".$lohnart."\nperiodenXML=".$periodenXML."\n: ".print_r($rekap,true));
		}										

		if($rekapEmployeeData != "") {
			//there are still a few more data for writing to the XML file
			fwrite($fp, $rekapEmployeeData 
			//.str_replace(array("&","_","%","#"), array("\\&","\\_","\\%","\\#"), $rekapEntries)
			.str_replace(array("&","%","#"), array("\\&","\\%","\\#"), $rekapEntries)
			."\n\t\t\t</Entries>\n\t\t</Employee>\n");
		}
		fwrite($fp, "\t</Employees>\n</Report>\n");
		$fm->fclose();

		chdir($newTmpPath);
		$y = 0;
		$cnt = count($statistikArr);
		for ($i = 0; $i < $cnt; $i++) {
			if ( floatval ($statistikArr["P".$i]) > 0 ) {
				$y++;
			}
		}
//communication_interface::alert("y:".$y."\nPayrollAccountJournal:".print_r($statistikArr, true)."\narray_count_values:".print_r(array_values($statistikArr),true));

		if ($y > 10) {
			system($aafwConfig["paths"]["utilities"]["xsltproc"]." ".$aafwConfig["paths"]["reports"]["templates"]."PayrollAccountJournal_ForLongData.xslt ./data.xml > ./compileme.tex");
		} else {
 			system($aafwConfig["paths"]["utilities"]["xsltproc"]." ".$aafwConfig["paths"]["reports"]["templates"]."PayrollAccountJournal.xslt ./data.xml > ./compileme.tex");
		}

        system($aafwConfig["paths"]["utilities"]["pdflatex"]." -interaction=batchmode compileme.tex > ".$aafwConfig["paths"]["utilities"]["stdout"]);
		system($aafwConfig["paths"]["utilities"]["pdflatex"]." -interaction=batchmode compileme.tex > ".$aafwConfig["paths"]["utilities"]["stdout"]);
		system("chmod 666 *");

		return $newTmpDirName;
	}

	
	
	
	
	
	public function Payslip($param) {//Lohnkonto
//communication_interface::alert("reports.php 1 - Payslip() param:".print_r($param,true));
        require_once(getcwd()."/kernel/common-functions/configuration.php");
        global $aafwConfig;
		$payrollPeriodID = $param["payroll_period_ID"];
		$uid = session_control::getSessionInfo("id");
		$language = session_control::getSessionInfo("language");
		$cashPayment = array("de" => "Barauszahlung", "en" => "Cash payment", "fr" => "Paiement en espaces", "it" => "Pagamento in contanti");

		$system_database_manager = system_database_manager::getInstance();

		$countryNames = array();
		$result = $system_database_manager->executeQuery(
				"SELECT `core_intl_country_ID`,`country_name` 
				FROM `core_intl_country_names` 
				WHERE (`country_name_language`='en' 
				AND `core_intl_country_ID` NOT IN ('AT','CH','DE','FR','IT','LI')) 
				OR (`country_name_language`='".$language."' 
				AND `core_intl_country_ID` IN ('AT','CH','DE','FR','IT','LI'))", "payroll_report_Payslip");
		foreach($result as $row) {
			$countryNames[$row["core_intl_country_ID"]] = $row["country_name"];
		}
		unset($result);

		$notifications = array();
		$result = $system_database_manager->executeQuery(
				"SELECT `payroll_company_ID`,`employee_notification`,`language` 
				FROM `payroll_payslip_notice` 
				WHERE `payroll_period_ID`=".$payrollPeriodID, "payroll_report_Payslip");
		foreach($result as $row) {
			$notifications[(string)$row["payroll_company_ID"]][$row["language"]] = str_replace("\n", "<br/>", $row["employee_notification"]);
		}
		unset($result);

		$periodInfo = array();
		$result = $system_database_manager->executeQuery(
			"SELECT `payroll_period`.*
			, LAST_DAY(CONCAT(`payroll_year_ID`, '-', `major_period_associated`, '-01')) as `lastDayOfPeriod` 
			FROM `payroll_period` WHERE `id`=".$payrollPeriodID, "payroll_report_Payslip");
		foreach($result[0] as $fldName=>$fldValue) $periodInfo[$fldName] = $fldValue;
		unset($result);
		if($periodInfo["locked"]==0 && $periodInfo["finalized"]==0) $tableNameSuffix = "current";
		else $tableNameSuffix = "entry";

		
		$payments = array();
		$lohnabrechnnug = array();$i=0;
		
		$sql = "SELECT * FROM payroll_payment_current_effectifpayout ";		
		$result = $system_database_manager->executeQuery( $sql, "payroll_report_Payslip");
		
		$resultParam = $result[0]["payroll_period_ID"];
//communication_interface::alert("payrollPeriodID:".$payrollPeriodID."\n"."resultParam:".$resultParam."\n".$sql."\n".print_r($result, true));
		if ($resultParam == $payrollPeriodID) {
			foreach($result as $row) {
				if(!isset($payments[(string)$row["payroll_employee_ID"]])) {
					$payments[(string)$row["payroll_employee_ID"]] = array();
				}
				$payout = $row["amount_payout"];
				$payout = number_format($row["amount_payout"], 2, '.', "'");
				$payoutCHF = "";
				if ( $row["currency_ID"] != "CHF" ) {
					$payoutCHF = "(CHF ".number_format($row["amount_CHF"], 2, '.', "'").")";
				}
				$payments[(string)$row["payroll_employee_ID"]][] =  "
				<Payout>
					<BankAddrLine1>".$row["beneBank1"]."</BankAddrLine1>
					<BankAddrLine2>".$row["beneBank2"]."</BankAddrLine2>
					<BankAddrLine3>".$row["beneBank3"]."</BankAddrLine3>
					<BankAddrLine4> </BankAddrLine4>
					<BankAccountNo>".$row["benIBAN"]."</BankAccountNo>
					<PayoutCurrency>".$row["currency_ID"]."</PayoutCurrency>
					<PayoutAmount>".$payout."</PayoutAmount>
					<PayoutAmountCHF>".$payoutCHF."</PayoutAmountCHF>
				</Payout>\n";
			}
		} else {
			$sql = "SELECT * FROM payroll_bank_destination ";
			$result = $system_database_manager->executeQuery( $sql, "payroll_report_Payslip");
					foreach($result as $row) {
				if(!isset($payments[(string)$row["payroll_employee_ID"]])) {
					$payments[(string)$row["payroll_employee_ID"]] = array();
				}
				$payments[(string)$row["payroll_employee_ID"]][] = "
				<Payout>
					<BankAddrLine1>".$row["beneficiary_bank_line1"]."</BankAddrLine1>
					<BankAddrLine2>".$row["beneficiary_bank_line2"]."</BankAddrLine2>
					<BankAddrLine3>".$row["beneficiary_bank_line3"]."</BankAddrLine3>
					<BankAddrLine4>".$row["beneficiary_bank_line4"]."</BankAddrLine4>
					<BankAccountNo>".$row["bank_account"]."</BankAccountNo>
					<PayoutCurrency> </PayoutCurrency>
					<PayoutAmount> </PayoutAmount>
					<PayoutAmountCHF> </PayoutAmountCHF>
				</Payout>\n";
			}
		}
//communication_interface::alert("abr  i:".$i."\ncount:".count($lohnabrechnnug)."\n".print_r($lohnabrechnnug, true));
		unset($result);

		$payslipInfo = array();
		$result = $system_database_manager->executeQuery(
				"SELECT `payroll_payslip_cfg_ID`,`label`,`language`,`field_type`,`field_name` 
				 FROM `payroll_payslip_cfg_info` 
				 ORDER BY `position`", "payroll_report_Payslip");
		foreach($result as $row) {
			if(!isset($payslipInfo[(string)$row["payroll_payslip_cfg_ID"]][$row["language"]])) {
				$payslipInfo[(string)$row["payroll_payslip_cfg_ID"]][$row["language"]] = array();
			}
			$payslipInfo[(string)$row["payroll_payslip_cfg_ID"]][$row["language"]][] = array("field_type"=>$row["field_type"], "field_name"=>$row["field_name"], "label"=>$row["label"]);
		}
		unset($result);

		$payslipCfg = array();
		$result = $system_database_manager->executeQuery("SELECT * FROM `payroll_payslip_cfg`", "payroll_report_Payslip");
//communication_interface::alert($sql."\n".print_r($result, true));
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
		$result = $system_database_manager->executeQuery(
				"SELECT flddef.`fieldName`
				,IF(flddef.`dataSourceToken`=1,lstgrp.`ListItemToken`,lstgrp.`id`) as ListItemID
				,lstlbl.`language`, lstlbl.`label` 
				FROM `payroll_employee_field_def` flddef 
				INNER JOIN `payroll_empl_list` lstgrp 
					ON lstgrp.`ListGroup`=flddef.`dataSourceGroup` 
				INNER JOIN `payroll_empl_list_label` lstlbl 
					ON lstlbl.`payroll_empl_list_ID`=lstgrp.`id` 
				WHERE flddef.`fieldType`=4 
				AND flddef.`dataSource`='payroll_empl_list' 
				AND flddef.`fieldName` IN (".implode(",",$arrFld).")"
				, "payroll_report_Payslip");
		foreach($result as $row) {
			$listValues[$row["fieldName"]][$row["ListItemID"]][$row["language"]] = $row["label"];
		}
		unset($result);
		unset($arrFld);

		$arrFld = array();
		$employeeDetail = array();
		$mitarbeiterListe = array();
		foreach($employeeFieldsOfInterest as $fldName) $arrFld[] = "emp.`".$fldName."`";
		$result = $system_database_manager->executeQuery(
				"SELECT ".implode(",", $arrFld)." 
				FROM `payroll_employee` emp 
				INNER JOIN `payroll_tmp_change_mng` emplist ON emplist.`numID`=emp.`id` 
				AND emplist.`core_user_id`=".$uid, "payroll_report_Payslip");
		foreach($result as $row) {
			$employeeDetail[(string)$row["id"]] = $row;
		}
		
		//communication_interface::alert("employees:".print_r($result,true));
		//communication_interface::alert("employeeDetail:".print_r($employeeDetail,true));
		
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
			$sql =
			"SELECT " .
			"calc.`payroll_employee_ID`" .
			",calc.`payroll_account_ID`" .
			",IF(calc.`label`!=''" .
			",calc.`label`" .
			",IF(calc.`code`!='',CONCAT(acclbl.`label`,' ',calc.`code`),acclbl.`label`)) as `label`" .
			",IF(acc.`quantity_print`=1,FORMAT(calc.`quantity`*acc.`quantity_conversion`" .
			",IF(acc.`quantity_decimal`=10,2,acc.`quantity_decimal`)),NULL) as `quantity`" .
			",IF(acc.`quantity_print`=1 AND acclbl.`quantity_unit`!='',acclbl.`quantity_unit`,NULL) as `quantity_unit`" .
			",IF(acc.`rate_print`=1,FORMAT(calc.`rate`*acc.`rate_conversion`" .
			",IF(acc.`rate_decimal`=10,2,acc.`rate_decimal`)),NULL) as `rate`" .
			",IF(acc.`rate_print`=1 AND acclbl.`rate_unit`!='',acclbl.`rate_unit`,NULL) as `rate_unit`" .
			",IF(acc.`amount_print`=1,FORMAT(calc.`amount`*acc.`amount_conversion`" .
			",IF(acc.`amount_decimal`=10,2,acc.`amount_decimal`)),NULL) as `amount`" .
			",acc.`bold`, acc.`space_before`, acc.`space_after`, prdemp.`payment_date`, prdemp.`interest_date` " .
			"FROM `payroll_period_employee` prdemp " .
			"INNER JOIN `payroll_tmp_change_mng` emplist " .
				"ON emplist.`numID`=prdemp.`payroll_employee_ID` " .
				"AND emplist.`core_user_id`=".$uid." " .
			"INNER JOIN `payroll_employee` emp ON emp.`id`=prdemp.`payroll_employee_ID` " .
			"INNER JOIN `payroll_calculation_".$tableNameSuffix."` calc " .
				"ON calc.`payroll_period_ID`=prdemp.`payroll_period_ID` " .
				"AND calc.`payroll_employee_ID`=prdemp.`payroll_employee_ID` " .
			"INNER JOIN `payroll_account` acc ON calc.`payroll_account_ID`=acc.`id` " .
				"AND calc.`payroll_year_ID`=acc.`payroll_year_ID` " .
				"AND (acc.`quantity_print`=1 " .
				"OR acc.`rate_print`=1 " .
				"OR acc.`amount_print`=1) " .
			"LEFT JOIN `payroll_account_label` acclbl " .
				"ON calc.`payroll_account_ID`=acclbl.`payroll_account_ID` " .
				"AND calc.`payroll_year_ID`=acclbl.`payroll_year_ID` " .
				"AND acclbl.`language`=emp.`Language` " .
			"WHERE prdemp.`payroll_period_ID`=".$payrollPeriodID." " .
				"AND prdemp.`processing`!=0 " .
			"ORDER BY calc.`payroll_employee_ID`,calc.`position`,calc.`payroll_account_ID`";
		$result = $system_database_manager->executeQuery($sql, "payroll_report_Payslip");
		
//communication_interface::alert("sql".$sql."\result:".print_r($result,true));
		
		foreach($result as $row) {
// communication_interface::alert("lastEmpId".$lastEmpId."\ncount:".count($entries)."\tentries: ".print_r($entries,true));
			if($row["payroll_employee_ID"] != $lastEmpId) {
				if(count($entries)!=0) {
					$emplData = $employeeDetail[(string)$lastEmpId];
					//kommt aus Tabelle "payroll_payslip_cfg"
					//hier ist auch das Underlay, das template (.pdf) hinterlegt
					//physisch liegt das Template auf data-hidden/CUSTOMER/[DB des Mandanten]/TEMPLATE
					$templCfg = $payslipCfg[(string)$emplData["payroll_payslip_cfg_ID"]];
// if ($emplData["id"] >= 13 && $emplData["id"] <= 16) {   
// 	communication_interface::alert($emplData["id"]." emplData: ".print_r($emplData,true)."\n\ntemplCfg: [InfoFields]\n".print_r($templCfg["InfoFields"][$emplData["Language"]],true));
// }					
					$infoFields = "";
					foreach($templCfg["InfoFields"][$emplData["Language"]] as $infoRow) {
						switch($infoRow["field_type"]) {
						case 0:
// $listValues[$row["fieldName"]][$row["ListItemID"]][$row["language"]] = $row["label"];
							if(isset($listValues[$infoRow["field_name"]])) {
								$curValue = $listValues[$infoRow["field_name"]][$emplData[$infoRow["field_name"]]][$emplData["Language"]];
							}else{
								$curValue = $emplData[$infoRow["field_name"]];
							}
							$infoFields .= "\t\t\t\t<Field>
								<Name>".$infoRow["field_name"]."</Name>
								<Label>".$infoRow["label"]."</Label>
								<Value>".$curValue."</Value>
								</Field>\n";
							break;
						default:
							$infoFields .= "\t\t\t\t<Field>
								<Name>IFLD</Name>
								<Label>".$infoRow["label"]."</Label>
								<Value>".$infoRow["field_type"]."</Value>
								</Field>\n";
							break;
						}
					}
					$tmpNote = array();
					if(isset($notifications[(string)$emplData["payroll_company_ID"]][$emplData["Language"]])) {
						$tmpNote[] = $notifications[(string)$emplData["payroll_company_ID"]][$emplData["Language"]];
					}
					if(isset($notifications[(string)"0"][$emplData["Language"]])) {
						$tmpNote[] = $notifications[(string)"0"][$emplData["Language"]];
					}
					$curEmpl =  "\t\t<Employee>\n\t\t\t<EmployeeNumber>".$emplData["EmployeeNumber"]."</EmployeeNumber>" .
								"\n\t\t\t<CompanyID>".$emplData["payroll_company_ID"]."</CompanyID>" .
								"\n\t\t\t<Firstname>".$emplData["Firstname"]."</Firstname>" .
								"\n\t\t\t<Lastname>".$emplData["Lastname"]."</Lastname>" .
								"\n".
								($emplData["AdditionalAddrLine1"]!="" ? "\t\t\t<AdditionalAddrLine1>".$emplData["AdditionalAddrLine1"]."</AdditionalAddrLine1>\n" : "").
								($emplData["AdditionalAddrLine2"]!="" ? "\t\t\t<AdditionalAddrLine2>".$emplData["AdditionalAddrLine2"]."</AdditionalAddrLine2>\n" : "").
								($emplData["AdditionalAddrLine3"]!="" ? "\t\t\t<AdditionalAddrLine3>".$emplData["AdditionalAddrLine3"]."</AdditionalAddrLine3>\n" : "").
								($emplData["AdditionalAddrLine4"]!="" ? "\t\t\t<AdditionalAddrLine4>".$emplData["AdditionalAddrLine4"]."</AdditionalAddrLine4>\n" : "").
								"\t\t\t<Street>".$emplData["Street"]."</Street>" .
								"\n\t\t\t<ZIP-Code>".$emplData["ZIP-Code"]."</ZIP-Code>" .
								"\n\t\t\t<City>".$emplData["City"]."</City>" .
								"\n\t\t\t<Country>".$emplData["Country"]."</Country>" .
								"\n\t\t\t<CountryName>".$countryNames[$emplData["Country"]]."</CountryName>" .
								"\n".$infoByWageCode[$emplData["WageCode"]]."\t\t\t<PaymentDate>".$paymentDate."</PaymentDate>" .
								"\n\t\t\t<InterestDate>".$interestDate."</InterestDate>" .
								"\n".(count($tmpNote)!=0 ? "\t\t\t<Notification>".implode("<br/>",$tmpNote)."</Notification>" .
								"\n" : "").
								"\t\t\t<DocumentSettings>" .
								"\n\t\t\t\t<Language>".$emplData["Language"]."</Language>" .
								"\n\t\t\t\t<DecimalPoint>.</DecimalPoint>" .
								"\n\t\t\t\t<ThousandsSeparator>'</ThousandsSeparator>" .
								"\n\t\t\t\t<PdfTemplate>".($templCfg["pdf_template"]!="" ? $pdfTemplateDir.$templCfg["pdf_template"] : "")."</PdfTemplate>" .
								"\n\t\t\t\t<AddrOffsetLeft>".$templCfg["addr_offset_left"]."</AddrOffsetLeft>" .
								"\n\t\t\t\t<AddrOffsetTop>".$templCfg["addr_offset_top"]."</AddrOffsetTop>" .
								"\n\t\t\t\t<InfoOffsetLeft>".$templCfg["info_offset_left"]."</InfoOffsetLeft>" .
								"\n\t\t\t\t<InfoOffsetTop>".$templCfg["info_offset_top"]."</InfoOffsetTop>" .
								"\n\t\t\t\t<ContentOffsetLeft>".$templCfg["content_offset_left"]."</ContentOffsetLeft>" .
								"\n\t\t\t\t<ContentOffsetTop>".$templCfg["content_offset_top"]."</ContentOffsetTop>" .
								"\n\t\t\t\t<ContentWidth>".$templCfg["content_width"]."</ContentWidth>" .
								"\n\t\t\t\t<AddrFontName>".$templCfg["addr_font_name"]."</AddrFontName>" .
								"\n\t\t\t\t<AddrFontSize>".$templCfg["addr_font_size"]."</AddrFontSize>" .
								"\n\t\t\t\t<InfoFontName>".$templCfg["info_font_name"]."</InfoFontName>" .
								"\n\t\t\t\t<InfoFontSize>".$templCfg["info_font_size"]."</InfoFontSize>" .
								"\n\t\t\t\t<ContentFontName>".$templCfg["content_font_name"]."</ContentFontName>" .
								"\n\t\t\t\t<ContentFontSize>".$templCfg["content_font_size"]."</ContentFontSize>" .
								"\n\t\t\t\t<ProcessingSuffix>".$templCfg["processing_suffix"]."</ProcessingSuffix>" .
								"\n\t\t\t</DocumentSettings>" .
								"\n\t\t\t<InfoFields>\n".$infoFields."\t\t\t</InfoFields>" .
								"\n\t\t\t<Entries>" .
								"\n".implode("",$entries)."\t\t\t</Entries>" .
								"\n\t\t\t<Payouts>\n".(isset($payments[(string)$lastEmpId]) ? implode("", $payments[(string)$lastEmpId]) : "")."\t\t\t</Payouts>" .
								"\n\t\t</Employee>\n";
					
					//fwrite($fp, str_replace(array("&","_","%","#"), array("\\&","\\_","\\%","\\#"), $curEmpl) );
					fwrite($fp, str_replace(array("&","%","#"), array("\\&","\\%","\\#"), $curEmpl) );
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
			
			if (($row["amount"]===NULL || $row["amount"]==0) && ($row["quantity"]===NULL || $row["quantity"]==0)) {
				$entries[] = "";
			} else {
				$entries[] = "\t\t\t\t<Entry".$designAttributes.">
					<AccountNumber>".$row["payroll_account_ID"]."</AccountNumber>
					<AccountName>".$row["label"]."</AccountName>
					".($row["quantity"]===NULL ? "" : "<quantity>".$row["quantity"]."</quantity>
					").($row["quantity_unit"]===NULL ? "" : "<quantityUnit>".$row["quantity_unit"]."</quantityUnit>
					").($row["rate"]===NULL ? "" : "<rate>".$row["rate"]."</rate>
					").($row["rate_unit"]===NULL ? "" : "<rateUnit>".$row["rate_unit"]."</rateUnit>
					").($row["amount"]===NULL ? "" : "<amount>".$row["amount"]."</amount>
				")."</Entry>\n";
			}
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
					break;
				default:
					$infoFields .= "\t\t\t\t<Field>\n\t\t\t\t\t<Name>IFLD</Name>\n\t\t\t\t\t<Label>".$infoRow["label"]."</Label>\n\t\t\t\t\t<Value>".$infoRow["field_type"]."</Value>\n\t\t\t\t</Field>\n";
					break;
				}
			}
			$tmpNote = array();
			if(isset($notifications[(string)$emplData["payroll_company_ID"]][$emplData["Language"]])) {
				$tmpNote[] = $notifications[(string)$emplData["payroll_company_ID"]][$emplData["Language"]];
			}
			if(isset($notifications[(string)"0"][$emplData["Language"]])) {
				$tmpNote[] = $notifications[(string)"0"][$emplData["Language"]];
			}
			$curEmpl = "
				<Employee>
					<EmployeeNumber>".$emplData["EmployeeNumber"]."</EmployeeNumber>
					<CompanyID>".$emplData["payroll_company_ID"]."</CompanyID>
					<Firstname>".$emplData["Firstname"]."</Firstname>
					<Lastname>".$emplData["Lastname"]."</Lastname>".
					($emplData["AdditionalAddrLine1"]!="" ? "<AdditionalAddrLine1>".$emplData["AdditionalAddrLine1"]."</AdditionalAddrLine1>\n" : "").
					($emplData["AdditionalAddrLine2"]!="" ? "<AdditionalAddrLine2>".$emplData["AdditionalAddrLine2"]."</AdditionalAddrLine2>\n" : "").
					($emplData["AdditionalAddrLine3"]!="" ? "<AdditionalAddrLine3>".$emplData["AdditionalAddrLine3"]."</AdditionalAddrLine3>\n" : "").
					($emplData["AdditionalAddrLine4"]!="" ? "<AdditionalAddrLine4>".$emplData["AdditionalAddrLine4"]."</AdditionalAddrLine4>\n" : "")."
					<Street>".$emplData["Street"]."</Street>
					<ZIP-Code>".$emplData["ZIP-Code"]."</ZIP-Code>
					<City>".$emplData["City"]."</City>
					<Country>".$emplData["Country"]."</Country>
					<CountryName>".$countryNames[$emplData["Country"]]."</CountryName>"
					.$infoByWageCode[$emplData["WageCode"]]."
					<PaymentDate>".$paymentDate."</PaymentDate>
					<InterestDate>".$interestDate."</InterestDate>".
					(count($tmpNote)!=0 ? "\t\t\t<Notification>".implode("<br/>",$tmpNote)."</Notification>\n" : "")."
					<DocumentSettings>
					<Language>".$emplData["Language"]."</Language>
					<DecimalPoint>.</DecimalPoint>
					<ThousandsSeparator>'</ThousandsSeparator>
					<PdfTemplate>".($templCfg["pdf_template"]!="" ? $pdfTemplateDir.$templCfg["pdf_template"] : "")."</PdfTemplate>
					<AddrOffsetLeft>".$templCfg["addr_offset_left"]."</AddrOffsetLeft>
					<AddrOffsetTop>".$templCfg["addr_offset_top"]."</AddrOffsetTop>
					<InfoOffsetLeft>".$templCfg["info_offset_left"]."</InfoOffsetLeft>
					<InfoOffsetTop>".$templCfg["info_offset_top"]."</InfoOffsetTop>
					<ContentOffsetLeft>".$templCfg["content_offset_left"]."</ContentOffsetLeft>
					<ContentOffsetTop>".$templCfg["content_offset_top"]."</ContentOffsetTop>
					<ContentWidth>".$templCfg["content_width"]."</ContentWidth>
					<AddrFontName>".$templCfg["addr_font_name"]."</AddrFontName>
					<AddrFontSize>".$templCfg["addr_font_size"]."</AddrFontSize>
					<InfoFontName>".$templCfg["info_font_name"]."</InfoFontName>
					<InfoFontSize>".$templCfg["info_font_size"]."</InfoFontSize>
					<ContentFontName>".$templCfg["content_font_name"]."</ContentFontName>
					<ContentFontSize>".$templCfg["content_font_size"]."</ContentFontSize>
					<ProcessingSuffix>".$templCfg["processing_suffix"]."</ProcessingSuffix>
					</DocumentSettings>\n\t\t\t<InfoFields>
					".$infoFields."\t\t\t</InfoFields>\n\t\t\t<Entries>
					".implode("",$entries)."\t\t\t</Entries>
					<Payouts>
					".(isset($payments[(string)$row["payroll_employee_ID"]]) ? implode("", $payments[(string)$row["payroll_employee_ID"]]) : "")."\t\t\t</Payouts>
				</Employee>\n";
			//fwrite($fp, str_replace(array("&","_","%","#"), array("\\&","\\_","\\%","\\#"), $curEmpl) );
			fwrite($fp, str_replace(array("&","%","#"), array("\\&","\\%","\\#"), $curEmpl) );
		}
		unset($payments);
		fwrite($fp, "\t</Employees>\n</Report>");
		$fm->fclose();

		chdir($newTmpPath);

        system($aafwConfig["paths"]["utilities"]["xsltproc"]." ".$aafwConfig["paths"]["reports"]["templates"]."Payslip.xslt ./data.xml > ./compileme.tex");        
		system($aafwConfig["paths"]["utilities"]["pdflatex"]." -interaction=batchmode compileme.tex > ".$aafwConfig["paths"]["utilities"]["stdout"]);
        
		system("chmod 666 *");

		return $newTmpDirName;
	}
	
	public function generateAuszahlDataReports($ZahlstellenID, $Personenkreis, $dueDateGUI) {
		require_once(getcwd()."/web/fpdf17/fpdf.php");
        require_once(getcwd()."/kernel/common-functions/configuration.php");
		require_once('payroll_auszahlen.php');
		$auszahlen = new auszahlen();
		require_once('payroll_auszahlfiles.php');
		$auszahlfiles = new auszahlfiles();
        global $aafwConfig;
		ini_set('memory_limit', '700M');
		
		if (strlen($dueDateGUI) < 10) {
			communication_interface::alert("Err 21: Valuta Datum ungueltig! Format:[TT.MM.JJJJ]");
			return false;//Abbruch ganze Aktion (Keine Files erzeugen)
		}
		$arrDueDate = explode(".", $dueDateGUI);
		if (count($arrDueDate) < 2){
			communication_interface::alert("Err 22: Valuta Datum ungueltig! Format:[TT.MM.JJJJ]");
			return false;//Abbruch ganze Aktion (Keine Files erzeugen)
		}
		if (strlen($arrDueDate[2]) < 4){
			communication_interface::alert("Err 23: Valuta Datum ungueltig! Format:[TT.MM.JJJJ]");
			return false;//Abbruch ganze Aktion (Keine Files erzeugen)
		}

		$noBankAccount = $auszahlen->getEployeesWithoutIBAN();
		if (strlen($noBankAccount) > 1) {
			$s=	"Folgende Personen haben noch kein" .CRLF.
				"Bankkonto registriert (IBAN fehlt)".CRLF;
			communication_interface::alert($s.$noBankAccount);
			return false;//Abbruch ganze Aktion (Keine Files erzeugen)
		}
		
		$periodeID = blFunctionCall('payroll.auszahlen.getActualPeriodID');		
				
		$nurZahlstelleBeruecksichtigen = false;
		$zahlstellenListe = array($ZahlstellenID);
		if ($ZahlstellenID <= 0) {
			//Zahlstelle verwenden, die den Employees hinterlegt sind
			/* Mitarbeiter (dieser Periode) ohne hinterlegte Zahlstelle (source bank) 
			 * werden für diesen Lauf ausgelassen. 
			 * 1.)	In dieser Periode betroffene Mitarbeiter finden
			 * 2.)	Von diesen Mitarbeitern schauen, wer Split hat
			 * 3.)	Diese Splitts nach Zahlstelle ordnen
			 * 3.a.)	für jede Zahlstelle (ein File erstellen) die Zahlungen abarbeiten
			 * 4.)	Abhaken aller Mitarbeiter, deren offenen Rest-Lohn jetzt 0 ist (isFullPayed="Y"), 
			 * 		die anderen bekommen isFullPayed="T" (Teil) 
			 */
			$zahlstellenListe = $auszahlen->getPeriodZahlstellenListe($periodeID);
			$nurZahlstelleBeruecksichtigen = true;
			//communication_interface::alert("zahlstellenListe:".print_r($zahlstellenListe, true)."\ncount".count($zahlstellenListe));
		}

		//Einschraenkung mit dem Personenfilter
		$emplFilter = "";
		if (substr($Personenkreis."xx",0,1)!="*") {			
			$emplFilter = $auszahlen->getEmployeeFilters($Personenkreis);
		}

		if($nurZahlstelleBeruecksichtigen && strlen($emplFilter) > 1) {
			communication_interface::alert("Doppelbedingung nicht zugelassen!\n\nMitarbeiter-Zahlstelle UND Personenfilter");
			return 0;
		}
		
		//Test, ob Mitarbeiter zur Auszahlung gelangen
		$allEmplWithPayment = $auszahlen->getMitarbeiterZurAuszahlung("8000", "amount > 0.001", "isFullPayed <> 'Y' ",$emplFilter);
		if ($allEmplWithPayment['count'] < 1) {
			communication_interface::alert("Keine Daten zur Auszahlung");
			return 0;
		}
		
		$retMessage = "";
		$anzFiles = 0;
		for ( $index = 0, $max_count = sizeof( $zahlstellenListe ); $index < $max_count; $index++ ) {
   			//communication_interface::alert("ZS:".$zahlstellenListe[$index]);	
			$ZahlstellenID = $zahlstellenListe[$index];					
			$ret = $auszahlfiles->setAuszahlFiles($ZahlstellenID, $Personenkreis, $nurZahlstelleBeruecksichtigen, $arrDueDate, $periodeID);
			$anzFiles += $ret["anzFiles"];
			$retMessage .= $ret["retMessage"];
		}
		if ($anzFiles > 3) {
			communication_interface::alert($retMessage."= = = = = = = = = = = = = = =\n ".$anzFiles." Dateien generiert");						
		} else {
			communication_interface::alert($retMessage."= = = = = = = = = = = = = = =\n");			
		}	
		return $anzFiles;
	}// end function generateAuszahlDataReports($ZahlstellenID, $Personenkreis, $dueDateGUI)
	
		
}
?>
