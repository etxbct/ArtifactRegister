<?php
require '../shared/mysqlconfig.php';
require '../shared/inventconfig.php';

$empty = "-";
$whereFilter = " ";
$locations = array();

// Anslut till mySQL databasen
$conn = new mysqli(MYSQL_HOST, MYSQL_USR, MYSQL_PWD, MYSQL_DB);

// Kontrollera mySQL anslutningen
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

if (DEBUG) echo "In: ";
if (DEBUG) print_r($_REQUEST);

// Hämta alla byggnader till en matrix
// ID, name
$sql = "SELECT * FROM ".MYSQL_TABSTO." ORDER BY name";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $locations[$row["ID"]] = array("name"=>$row["name"]);
  }
}

if (isset($_REQUEST["do"])) {
//Do find
  if ($_REQUEST["do"] == DOFIND) {
    if (isset($_REQUEST["find"])) {
      if (strlen($_REQUEST["find"]) > 0) {
        $whereFilter = " WHERE t1.name LIKE '%".$_REQUEST["find"]."%' OR t1.origin LIKE '%".$_REQUEST["find"]."%' OR t1.inventoried_year LIKE '%".$_REQUEST["find"]."%'";
      }
    }

    unset($_REQUEST["do"]);
  }
// Do filter
  elseif ($_REQUEST["do"] == DOFILTER) {
    if (isset($_REQUEST["building"])) {
      if ($_REQUEST["building"] > 1) {
        $whereFilter = " WHERE t1.storage = ".$_REQUEST["building"];
      }
    }

    unset($_REQUEST["do"]);
  }

  if (DEBUG) echo "Efter Do: ";
  if (DEBUG) print_r($_REQUEST);
}

?>
<html>
<head>
<?php
if (preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"])) {
?>
<meta name="MobileOptimized" content="320" />
<?php
}
?>
<link rel="stylesheet" href="inventory.css">
</head>
<body>
<h1><?php echo LISTNAME;?></h1>
<?php
if (isset($_REQUEST["do"])) {
  if ($_REQUEST["do"] == DOVIEW) {
// Visa inventarie
    $sql = "SELECT t1.*, t2.name AS building FROM ".MYSQL_TABINV." t1 LEFT JOIN ".MYSQL_TABSTO." t2 ON t1.storage = t2.ID WHERE t1.ID = ".$_REQUEST["ID"];

    if (DEBUG) echo "SQL: ";
    if (DEBUG) print_r($sql);

    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
?>
<table>
<thead>
<tr><td colspan=3 class="center large" ><?php echo LINVENTORY;?></td></tr>
</thead>
<?php
      while ($row = $result->fetch_assoc()) {
?>
<tr class="form"><td class="top" colspan="2"><b><?php echo LNAME;?></b>
    <p><?php echo str_replace("%", '"', $row['name']);?></td>
    <td class="top"><b><?php echo LINVENTORYNR;?></b>
    <p><?php echo str_replace("%", '"', $row['inventory_nr']);?></td></tr>
<tr class="form"><td class="top"><b><?php echo LARRIVALDATE;?></b>
    <p><?php echo $row['arrival_date'] > 0 ? $row['arrival_date'] : $empty;?></td>
    <td class="top" colspan="2"><b><?php echo LORIGIN;?></b>
    <p><?php echo strlen($row['origin']) > 0 ? str_replace("%", '"', $row['origin']) : $empty;?></td></tr>
<tr class="form"><td class="top" colspan="3"><b><?php echo LSUBMITTEDBY;?></b>
    <p><?php echo strlen($row['submitted_by']) > 0 ? str_replace("\n", "<br>", $row['submitted_by']) : $empty;?></td></tr>
<tr class="form"><td class="top" colspan="2"><b><?php echo LMAKER;?></b>
    <p><?php echo strlen($row['maker']) > 0 ? str_replace("%", '"', $row['maker']) : $empty;?></td>
    <td class="top"><b><?php echo LMANUFACTUREYEAR;?></b>
    <p><?php echo strlen($row['manufacture_year']) > 0 ? str_replace("%", '"', $row['manufacture_year']) : $empty;?></td></tr>
<tr class="form"><td class="top" colspan="3"><b><?php echo LDESCRIPTION;?></b>
    <p><?php echo strlen($row['description']) > 0 ? str_replace("\n", "<br>", $row['description']) : $empty;?></td></tr>
<tr class="form"><td class="top" colspan="3"><b><?php echo LREMARK;?></b>
    <p><?php echo strlen($row['remark']) > 0 ? str_replace("\n", "<br>", $row['remark']) : $empty;?></td></tr>
<tr class="form"><td class="top" colspan="3"><b><?php echo LSTORAGEPLACE;?></b>
    <p><?php echo strlen($row['building']) > 1 ? str_replace("%", '"', $row['building']).' '.$row['storage_place'] : $empty;?></td></tr>
<?php
        if ($row['photo'] <> NULL) {
?>
<tr class="form"><td class="top"><b><?php echo LFOTO;?></b>
<!--    <p><?php //echo $row['photo'];?></td>-->
    <p><?php echo "bild";?></td>
<?php
        }

        else {
?>
<tr class="form"><td class="top"><b><?php echo LFOTONR;?></b>
    <p><?php echo $row['photo_nr'] == '0' ? $empty : $row['photo_nr'];?></td>
<?php
        }
?>
    <td class="top"><b><?php echo LDIVNOTE;?></b>
    <p><?php echo strlen($row['misc_notes']) > 0 ? str_replace("\n", "<br>", $row['misc_notes']) : $empty;?></td>
    <td class="top"><b><?php echo LINVENTORIEDYEAR;?></b>
    <p><?php echo $row['inventoried_year'] > 0 ? $row['inventoried_year'] : $empty;?></td></tr>
<?php
      }
    }
  }
}

else {
// Visa lista av inventarier
  $orderBy = " CAST(`inventory_nr` AS DECIMAL)";
  $arrow1 = 0;

  if (isset($_REQUEST["sort"])) {
    if ($_REQUEST["sort"] == "inventorynrdesc") {
      $orderBy = " CAST(`inventory_nr` AS DECIMAL) DESC";
      $arrow1 = 1;
    }

    elseif ($_REQUEST["sort"] == "name") {
      $orderBy = " name";
      $arrow2 = 0;
    }

    elseif ($_REQUEST["sort"] == "namedesc") {
      $orderBy = " name DESC";
      $arrow2 = 1;
    }

    elseif ($_REQUEST["sort"] == "storage") {
      $orderBy = " building";
      $arrow3 = 0;
    }

    elseif ($_REQUEST["sort"] == "storagedesc") {
      $orderBy = " building DESC";
      $arrow3 = 1;
    }

//    $sql = "SELECT t1.*, t2.name AS building FROM ".MYSQL_TABINV." t1 LEFT JOIN ".MYSQL_TABSTO." t2 ON t1.storage = t2.ID".$whereFilter." ORDER BY".$orderBy;
  }

//  else {
  $sql = "SELECT t1.*, t2.name AS building FROM ".MYSQL_TABINV." t1 LEFT JOIN ".MYSQL_TABSTO." t2 ON t1.storage = t2.ID".$whereFilter." ORDER BY".$orderBy;
//  }

  if (DEBUG) echo "SQL: ";
  if (DEBUG) print_r($sql);

  $result = $conn->query($sql);
?>
<form method="post" id="find" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>"></form>
<form method="post" id="filter" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>"></form>
  <?php echo LFIND." ";?>
  <input type="text" name="find" size="20" form="find" class="find"/>
  <button type="submit" name="do" value="<?php echo DOFIND;?>" form="find" class="button"><?php echo BLFIND;?></button>
  <input type="hidden" name="do" value="<?php echo DOFILTER;?>" form="filter" />
  <?php echo LFILTERSTORAGE." ";?>
    <select name="building" onchange="this.form.submit()" form="filter" class="filter">
<?php
  foreach($locations as $SID => $location) {
    if ($SID == $_REQUEST["building"]) {
?>
      <option value="<?php echo $SID;?>" selected><?php echo $location['name'];?></option>
<?php
    }

    else {
?>
      <option value="<?php echo $SID;?>"><?php echo $location['name'];?></option>
<?php
    }
  }
?>
    </select>
<p>
<table>
<thead>
<tr><td colspan=8 class="large" ><?php echo LINVENTORYLIST;?></td></tr>
<tr><td><?php echo LINVENTORYNR;?> <a href="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);echo $arrow1 == 0 ? '?sort=inventorynrdesc' : '?sort=inventorynr';?>"><?php echo $arrow1 == 0 ? DOWNARROW : UPARROW;?></a></td>
    <td><?php echo LNAME;?> <a href="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);echo $arrow2 == 0 ? '?sort=namedesc' : '?sort=name';?>"><?php echo $arrow2 == 0 ? DOWNARROW : UPARROW;?></a></td>
    <td><?php echo LORIGIN;?></td>
    <td><?php echo LARRIVALDATE;?></td>
    <td><?php echo LBUILDING;?> <a href="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);echo $arrow3 == 0 ? '?sort=storagedesc' : '?sort=storage';?>"><?php echo $arrow3 == 0 ? DOWNARROW : UPARROW;?></a></td>
    <td><?php echo LSTORAGEPLACE;?></td>
    <td><?php echo LINVENTORIEDYEAR;?></td>
	<td></td>
</tr>
</thead>
<tbody>
<?php
  if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
?>
<form method="post" id="<?php echo $row['ID'];?>" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>"></form>
    <input type="hidden" name="ID" value="<?php echo $row['ID'];?>" form="<?php echo $row['ID'];?>" />
<tr class="list">
    <td><?php echo str_replace("%", '"', $row['inventory_nr']);?></td>
    <td><?php echo str_replace("%", '"', $row['name']);?></td>
    <td><?php echo strlen($row['origin']) > 0 ? $row['origin'] : $empty;?></td>
    <td><?php echo $row['arrival_date'] > 0 ? $row['arrival_date'] : $empty;?></td>
    <td><?php echo str_replace("%", '"', $row['building']);?></td>
    <td><?php echo strlen($row['storage_place']) > 0 ? $row['storage_place'] : $empty;?></td>
    <td><?php echo $row['inventoried_year'] > 0 ? $row['inventoried_year'] : $empty;?></td>
    <td><button type="submit" name="do" value="<?php echo DOVIEW;?>" form="<?php echo $row['ID'];?>" class="button view"><?php echo BLVIEW;?></button></td>
</tr>
<?php
    }
?>
</tbody>
<?php
  }
}

// Stäng mySQL anslutningen
$conn->close();

?>
</table>
<?php
if (isset($_REQUEST["do"])) {
?>
<p><button type="submit" name="back" value="<?php echo DOBACK;?>" class="button" onClick="history.go(-1);"><?php echo BLBACK;?></button>
<?php
}
?>
</body>
</html>
