<?php

include_once 'db_functions.php';
include('session.php');

$db = new DB_Functions();

$close = 0;
$open = 0;
$count = 0;
$var = array();

$currentUser = $db->getSingleUserByEmail($login_session);
$currentUserRow = mysql_fetch_array($currentUser);
if("admin" === strtolower($login_name) || "1" === $currentUserRow["Admin"] || "1" === $currentUserRow["Auditor"]){
	$query1 = $db->openCloseChart();
}
else{
	$query1 = $db->openCloseChartUser($login_name);
}

if($query1)
{
	while ($obj = mysql_fetch_object($query1))
	{
		$var[$count]= $obj;

		if("admin" === strtolower($login_name) || "1" === $currentUserRow["Admin"] || "1" === $currentUserRow["Auditor"]){
			$q2 = "select Status from report where ReportName = '".$var[$count]->ReportName."'";
		}
		else{
			$q2 = "select Status from report where ReportName = '".$var[$count]->ReportName."' AND (ResponsiblePerson = '".$login_name."' OR ResponsiblePerson2 = '".$login_name."' OR ResponsiblePerson3 = '".$login_name."' OR ResponsiblePerson4 = '".$login_name."' OR ResponsiblePerson5 = '".$login_name."') ";
		}

		if ( $query2 = $db->openCloseChartq2($q2))
		{
			while($obj1 = mysql_fetch_object($query2))
			{
				if ($obj1->Status == 'Close' )
				{
					$close++;
				}

				if ($obj1->Status == 'Open' || $obj1->Status == 'open')
				{
					$open++;
				}


			}
		}
		$count++;
	}

	$total = $close+$open;
	$data[0] = array('title' => 'open', 'total' => $open);
	$data[1] = array('title' => 'closed', 'total' => $close);
	echo json_encode($data);
}

?>