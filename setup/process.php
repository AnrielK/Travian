<?php
require_once __DIR__ . "/guard.php"; // security: installer is locked after installation

require_once __DIR__ . "/../application/php8_compat.php"; // PHP 8 compatibility helpers
require_once __DIR__ . "/lib.php";

// PHP 8.1+: mysqli throws exceptions by default; keep the legacy return-false behaviour.
mysqli_report(MYSQLI_REPORT_OFF);




error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED); // PHP 8: hide deprecations like PHP 7 never emitted



if(file_exists("../application/config.php") && !isset($_POST['subconst'])) {

    include_once("../application/config.php");

    include_once("../application/Database/db_MYSQL.php");

}

class Process {



	function __construct() {

		if(isset($_POST['subconst'])) {

			$this->constForm();

            //header("Location: index.php?s=2");

		} else

			if(isset($_POST['substruc'])) {

				$this->createStruc();

			} else

				if(isset($_POST['subwdata'])) {

					$this->createWdata();

				}  else {

							header("Location: index.php");

						}

	}



	function constForm() {
		// credentials -> application/db_local.php (config.php stays as it is, see setup/lib.php)
		$sserver = trim((string)$_POST['sserver']);
		$mysqli = @new mysqli($sserver, $_POST['suser'], $_POST['spass'], $_POST['sdb']);
		if ($mysqli->connect_errno) {
			setup_fail('Cannot connect to the database "' . $_POST['sdb'] . '" on ' . $sserver . ' as ' . $_POST['suser'] . ': ' . $mysqli->connect_error
				. ' – create the database first (e.g. in phpMyAdmin) and check the user and password.');
		}
		$mysqli->set_charset("utf8");
		if (!setup_write_db_local($sserver, $_POST['suser'], $_POST['spass'], $_POST['sdb'])) {
			setup_fail("Cannot write application/db_local.php – allow PHP to write into the folder application/.");
		}

		$p_query = setup_sql_text();
		setup_drop_tables($mysqli, $p_query);
		$error = setup_run_sql($mysqli, $p_query);
		if ($error !== '') {
			setup_fail('Creating the tables failed. ' . $error);
		}
		$mysqli->query("SET SESSION sql_mode = ''");

		$sql = "INSERT INTO config VALUES(NULL,".time().",".time().",0,1,'','".sql_esc($_POST['servername'])."',".sql_num($_POST['defgold']).",".sql_num($_POST['auctime']).",'gpack/img_rtl/',".sql_num($_POST['opening']).",".sql_num($_POST['oasisx']).",".sql_num($_POST['speed']).",".sql_num($_POST['MOMENT_TRAIN']).",".sql_num($_POST['ARTEFACTS']).",".sql_num($_POST['WW_PLAN']).",".sql_num($_POST['cranny']).",".sql_num(max(safe_num($_POST['adv']),1)).",".round(safe_num($_POST['speed'])/80).",".sql_num($_POST['storage_multiplier']).",".sql_num($_POST['incspeed']).",".sql_num($_POST['beginner']).",".sql_num($_POST['tradercap']).",".sql_num($_POST['plus_time']).",".sql_num($_POST['plus_production']).",'".sql_esc($_POST['homepage'])."','admin@test.com',10,100,5,1,25,400,80,50,1000,2500,4900,75,150,250,180000000,560000000,1800000000,'en',0,0,0)";
		if (!$mysqli->query($sql)) {
			setup_fail('Saving the server settings failed: ' . $mysqli->error);
		}

		header("Location: index.php?s=3");
	}

	function createStruc() {
		$mysqli = @new mysqli(SQL_SERVER, SQL_USER, SQL_PASS, SQL_DB, (int)SQL_PORT);
		if ($mysqli->connect_errno) {
			setup_fail('Cannot connect to the database: ' . $mysqli->connect_error);
		}
		$mysqli->set_charset("utf8");
		$p_query = setup_sql_text();
		setup_drop_tables($mysqli, $p_query);
		$error = setup_run_sql($mysqli, $p_query);
		if ($error === '') {
			$mysqli->query("SET SESSION sql_mode = ''");
			$mysqli->query("INSERT INTO config VALUES(".time().",".time().",0,1,'')");
			header("Location: index.php?s=3");
		} else {
			header("Location: index.php?s=2&c=1");
		}
	}

	function createWdata() {

		header("Location: include/wdata.php");

	}



}

;



$process = new Process;





