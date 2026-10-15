<?php
require '../../shared/mysqlconfig.php';
require '../../shared/adminconfig.php';

$empty = "-";
$whereFilter = " ";
$status = "";
$error = "";
$locations = array();
$specialChar = array("'", '"');

// Anslut till mySQL databasen
$conn = new mysqli(MYSQL_HOST, MYSQL_USR, MYSQL_PWD, MYSQL_DB);

// Kontrollera mySQL anslutningen
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
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
<link rel="stylesheet" href="admin.css">
</head>
<body>
<h1><?php echo LISTNAME;?></h1>
<?php
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
// Do new
  if ($_REQUEST["do"] == DONEW) {
// Inventarie
    $inventNo = "n".rand(10,100);
    $sql = "INSERT INTO ".MYSQL_TABINV." (inventory_nr) VALUES ('".$inventNo."')";

    if (isset($sql)) {
      if ($conn->query($sql) === TRUE) {
        if (DEBUG) echo "Efter Insert new inventory: ";
        if (DEBUG) echo $sql;
      }
    }

    $newsql = "SELECT ID FROM ".MYSQL_TABINV." WHERE inventory_nr = '".$inventNo."'";
    $result = $conn->query($newsql);
    if ($result->num_rows > 0) {
      $row = $result->fetch_assoc();
      $_REQUEST["ID"] = $row["ID"];
      $_REQUEST["do"] = DOEDIT;
      $_REQUEST["table"] = MYSQL_TABINV;
    }
  }

// Do delete
  elseif ($_REQUEST["do"] == DODELETE) {
// Inventarie
    if ($_REQUEST["table"] == MYSQL_TABINV) {
      $sql = "DELETE FROM ".$_REQUEST["table"]." WHERE ID = ".$_REQUEST["ID"];
    }

    if (isset($sql)) {
      if ($conn->query($sql) === TRUE) {
        if (DEBUG) echo "Efter Delete inventory: ";
        if (DEBUG) echo $sql;
        $status = "Inventarie nr ".$_REQUEST["inventory_nr"]." borttagen";
      }

      else {
        $error = "Inventarie nr ".$_REQUEST["inventory_nr"]." kunde inte tas bort";
      }
    }

    unset($_REQUEST["do"]);
  }
// Do save
  elseif ($_REQUEST["do"] == DOSAVE) {
// Byggnad
    if ($_REQUEST["table"] == MYSQL_TABSTO) {
      $sql = "UPDATE ".$_REQUEST["table"].
        " SET name = '".str_replace($specialChar, "%", $_REQUEST["name"])."' WHERE ID = ".$_REQUEST["ID"];
 
      if (isset($sql)) {
        if ($conn->query($sql) === TRUE) {
          if (DEBUG) echo "Efter Update storage: ";
          if (DEBUG) echo $sql;
          $status = "Lagringsplats ".$_REQUEST["name"]." sparad";
        }

        else {
          $error = "Lagringsplats ".$_REQUEST["name"]." kunde inte sparas";
        }
      }

      unset($_REQUEST["do"]);
    }
// Inventarie
    elseif ($_REQUEST["table"] == MYSQL_TABINV) {
      $_REQUEST["inventory_nr"] = trim($_REQUEST["inventory_nr"]);
      $_REQUEST["photo_nr"] = $_REQUEST["photo_nr"] == '' ? '0' : $_REQUEST["photo_nr"];
      $_REQUEST["inventoried_year"] = $_REQUEST["inventoried_year"] == '' ? '0000' : $_REQUEST["inventoried_year"];

      $sql = "UPDATE ".$_REQUEST["table"].
        " SET inventory_nr = '".str_replace($specialChar, "%", $_REQUEST["inventory_nr"])."'".
        ", name = '".str_replace($specialChar, "%", $_REQUEST["name"])."'".
        ", arrival_date = '".$_REQUEST["arrival_date"]."'".
        ", origin = '".str_replace($specialChar, "%", $_REQUEST["origin"])."'".
        ", submitted_by = '".$_REQUEST["submitted_by"]."'".
        ", maker = '".str_replace($specialChar, "%", $_REQUEST["maker"])."'".
        ", manufacture_year = '".str_replace($specialChar, "%", $_REQUEST["manufacture_year"])."'".
        ", description = '".$_REQUEST["description"]."'".
        ", remark = '".$_REQUEST["remark"]."'".
        ", storage = '".$_REQUEST["storage"]."'".
        ", storage_place = '".$_REQUEST["storage_place"]."'".
        ", photo_nr = '".$_REQUEST["photo_nr"]."'".
        ", misc_notes = '".$_REQUEST["misc_notes"]."'".
        ", inventoried_year = '".$_REQUEST["inventoried_year"]."' WHERE ID = ".$_REQUEST["ID"];

      if (isset($sql)) {
        if ($conn->query($sql) === TRUE) {
          if (DEBUG) echo "Efter Update inventory: ";
          if (DEBUG) echo $sql;
          $status = "Inventarie nr ".$_REQUEST["inventory_nr"]." sparad";
        }

        else {
          if (DEBUG) echo "Error Update inventory: ";
          if (DEBUG) echo $sql;
          $error = "Inventarie nr ".$_REQUEST["inventory_nr"]." kunde inte sparas";
        }
      }

      unset($_REQUEST["table"]);
    }
  }
//Do find
  elseif ($_REQUEST["do"] == DOFIND) {
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

    unset($_REQUEST["table"]);
    unset($_REQUEST["do"]);
  }

  if (DEBUG) echo "Efter Do: ";
  if (DEBUG) print_r($_REQUEST);
}

// Skapa lista
?>
<form method="post" id="buttons" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>"></form>
<form method="post" id="find" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>"></form>
<form method="post" id="filter" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>"></form>
<?php
if (isset($_REQUEST["table"])) {
?>
        <input type="hidden" name="table" value="<?php echo $_REQUEST["table"];?>" form="buttons" />
<?php
}

else {
?>
  <button type="submit" name="do" value="<?php echo DONEW;?>" form="buttons" class="button"><?php echo BLNEWINVENTORY;?></button>
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
<?php
}
?>
<p>
<table>
<thead>
<?php
// Byggnader
if (isset($_REQUEST["table"])) {
  if ($_REQUEST["table"] == MYSQL_TABSTO) {
    $sql = "SELECT * FROM ".MYSQL_TABSTO." ORDER BY name";
  }

// Inventarier
  else {
    if (isset($_REQUEST["do"])) {
      if ($_REQUEST["do"] == DOEDIT) {
        $sql = "SELECT * FROM ".MYSQL_TABINV." WHERE ID = '".$_REQUEST["ID"]."'";
      }
    }
  }
}

else {
  $sql = "SELECT t1.*, t2.name AS building FROM ".MYSQL_TABINV." t1 LEFT JOIN ".MYSQL_TABSTO." t2 ON t1.storage = t2.ID".$whereFilter." ORDER BY CAST(`inventory_nr` AS DECIMAL)";
  $sqltot = "SELECT COUNT(ID) AS totinventory FROM ".MYSQL_TABINV;
  if (DEBUG) echo "Skapa lista: ";
  if (DEBUG) print_r($sql);
}

// Sortering
if (isset($_REQUEST["sort"])) {
  $orderBy = " CAST(`inventory_nr` AS DECIMAL)";
  $arrow1 = 0;

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

  $sql = "SELECT t1.*, t2.name AS building FROM ".MYSQL_TABINV." t1 LEFT JOIN ".MYSQL_TABSTO." t2 ON t1.storage = t2.ID".$whereFilter." ORDER BY".$orderBy;
  if (DEBUG) echo "Skapa lista: ";
  if (DEBUG) print_r($sql);
}

if (DEBUG) echo "SQL: ";
if (DEBUG) print_r($sql);

$result = $conn->query($sql);
// Byggnader
if (isset($_REQUEST["table"])) {
  if ($_REQUEST["table"] == MYSQL_TABSTO) {
?>
<tr><td><?php echo LABELSIGTYPE;?></td>
    <td></td></tr>
</thead>
<tbody>
<?php
    if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
?>
<form method="post" id="<?php echo $row['ID'];?>" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>"></form>
        <input type="hidden" name="table" value="<?php echo $_REQUEST['table'];?>" form="<?php echo $row['ID'];?>" />
        <input type="hidden" name="ID" value="<?php echo $row['ID'];?>" form="<?php echo $row['ID'];?>" />
<tr><td><input type="text" name="name" value="<?php echo $row['name'];?>" size="20" form="<?php echo $row['ID'];?>" />
    <td><button type="submit" name="do" value="<?php echo DOSAVE;?>" form="<?php echo $row['ID'];?>" class="button update"><?php echo BLSAVE;?></button>
    </td>
</tr>
<?php
      }
    }
  }

// Inventarier
  else {
    if (isset($_REQUEST["do"])) {
      if ($_REQUEST["do"] = DOEDIT) {
        if ($result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
            $rowid = $row['ID'];
            if (DEBUG) echo "Edit: ";
            if (DEBUG) print_r($row);
?>
<tr><td colspan="3" class="center large" ><?php echo LINVENTORY;?></td></tr>
</thead>
<tbody>
<form method="post" id="<?php echo $rowid;?>" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>"></form>
        <input type="hidden" name="table" value="<?php echo $_REQUEST['table'];?>" form="<?php echo $rowid;?>" />
        <input type="hidden" name="ID" value="<?php echo $rowid;?>" form="<?php echo $rowid;?>" />
<tr class="form"><td class="top" colspan="2"><b><?php echo LNAME;?></b>
    <p><input type="text" name="name" value="<?php echo str_replace("%", "'", $row["name"]);?>" size="60" maxlength="100" form="<?php echo $rowid;?>" autofocus /></td>
    <td class="top"><b><?php echo LINVENTORYNR;?></b>
    <p><input type="text" name="inventory_nr" value="<?php echo str_replace("%", "'", $row['inventory_nr']);?>" size="8" form="<?php echo $rowid;?>" /></td></tr>
<tr class="form"><td class="top"><b><?php echo LARRIVALDATE;?></b>
    <p><input type="date" name="arrival_date" value="<?php echo $row['arrival_date'];?>" size="5" form="<?php echo $rowid;?>" /></td>
    <td class="top" colspan="2"><b><?php echo LORIGIN;?></b>
    <p><input type="text" name="origin" value="<?php echo str_replace("%", "'", $row['origin']);?>" size="71" maxlength="100" form="<?php echo $rowid;?>" /></td></tr>
<tr class="form"><td class="top" colspan="3"><b><?php echo LSUBMITTEDBY;?></b>
    <p><textarea name="submitted_by" rows="3" cols="95" form="<?php echo $rowid;?>"><?php echo $row['submitted_by'];?></textarea></td></tr>
<tr class="form"><td class="top" colspan="2"><b><?php echo LMAKER;?></b>
    <p><input type="text" name="maker" value="<?php echo str_replace("%", "'", $row['maker']);?>" size="60" maxlength="100" form="<?php echo $rowid;?>" /></td>
    <td class="top"><b><?php echo LMANUFACTUREYEAR;?></b>
    <p><input type="text" name="manufacture_year" value="<?php echo str_replace("%", "'", $row['manufacture_year']);?>" size="25" form="<?php echo $rowid;?>" /></td></tr>
<tr class="form"><td class="top" colspan="3"><b><?php echo LDESCRIPTION;?></b>
    <p><textarea name="description" rows="8" cols="95" form="<?php echo $rowid;?>"><?php echo $row['description'];?></textarea></td></tr>
<tr class="form"><td class="top" colspan="3"><b><?php echo LREMARK;?></b>
    <p><textarea name="remark" rows="5" cols="95" form="<?php echo $rowid;?>"><?php echo $row['remark'];?></textarea></td></tr>
<tr class="form"><td class="top" colspan="3"><b><?php echo LSTORAGEPLACE;?></b>
    <p><select id="STO" name="storage" form="<?php echo $rowid;?>">
<?php
            foreach($locations as $SID => $location) {
              if ($SID == $row["storage"]) {
?>
          <option value="<?php echo $SID;?>" selected><?php echo str_replace("%", "'", $locations[$SID]['name']);?></option>
<?php
              }

              else {
?>
          <option value="<?php echo $SID;?>"><?php echo str_replace("%", "'", $locations[$SID]['name']);?></option>
<?php
              }
            }
?>
        </select>
        <input type="text" name="storage_place" value="<?php echo $row['storage_place'];?>" size="50" maxlength="100" form="<?php echo $rowid;?>" /></td></tr>
<tr class="form"><td class="top"><b><?php echo LFOTONR;?></b>
    <p><input type="text" name="photo_nr" value="<?php echo $row['photo_nr'] > 0 ? $row['photo_nr'] : '';?>" size="15" maxlength="10" form="<?php echo $rowid;?>" /></td>
    <td class="top"><b><?php echo LDIVNOTE;?></b>
    <p><textarea name="misc_notes" rows="4" cols="40" form="<?php echo $rowid;?>"><?php echo $row['misc_notes'];?></textarea></td>
    <td class="top"><b><?php echo LINVENTORIEDYEAR;?></b>
    <p><input type="year" name="inventoried_year" value="<?php echo $row['inventoried_year'] > 0 ? $row['inventoried_year'] : '';?>" size="4" form="<?php echo $rowid;?>" /></td></tr>


<?php
          }
        }
      }
    }
  }
}
// Lista Inventarierna
else {
  $sqltot = "SELECT COUNT(ID) AS totinventory FROM ".MYSQL_TABINV;
  $totresult = $conn->query($sqltot);
  if ($totresult->num_rows > 0) {
    $count = $totresult->fetch_assoc();
    $totinventory = LTOTAL." ".$count['totinventory']." ".LPCS." ".LITEMS;
  }

  else {
    $totinventory = "";
  }
?>
<tr><td colspan="3" class="large" ><?php echo LINVENTORYLIST;?></td>
    <td colspan="3"><?php echo $totinventory;?></td>
	<td colspan="2">
<?php
  if (strlen($status) > 0) {
?>
    <div class="status"><?php echo $status;?></div>
<?php
  }

  elseif (strlen($error) > 0) {
?>
    <div class="error"><?php echo $error;?></div>
<?php
  }
?>
      </td></tr>
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
    <input type="hidden" name="table" value="<?php echo MYSQL_TABINV;?>" form="<?php echo $row['ID'];?>" />
    <input type="hidden" name="ID" value="<?php echo $row['ID'];?>" form="<?php echo $row['ID'];?>" />
<tr class="list">
    <td><?php echo str_replace("%", '"', $row['inventory_nr']);?></td>
    <td><?php echo str_replace("%", '"', $row["name"]);?></td>
    <td><?php echo strlen($row['origin']) > 0 ? $row['origin'] : $empty;?></td>
    <td><?php echo $row['arrival_date'] > 0 ? $row['arrival_date'] : $empty;?></td>
    <td><?php echo str_replace("%", '"', $row['building']);?></td>
    <td><?php echo strlen($row['storage_place']) > 0 ? $row['storage_place'] : $empty;?></td>
    <td><?php echo $row['inventoried_year'] > 0 ? $row['inventoried_year'] : $empty;?></td>
    <td><button type="submit" name="do" value="<?php echo DOEDIT;?>" form="<?php echo $row['ID'];?>" class="button update"><?php echo BLCHANGE;?></button>
        <button type="submit" name="do" value="<?php echo DODELETE;?>" form="<?php echo $row['ID'];?>" class="button delete"><?php echo BLDELETE;?></button></td>
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
if (isset($_REQUEST["table"])) {
  if ($_REQUEST["table"] == MYSQL_TABINV) {
?>
<p>
   <button type="submit" name="do" value="<?php echo DOSAVE;?>" form="<?php echo $rowid;?>" class="button"><?php echo BLSAVE;?></button>
   <button type="submit" name="back" value="<?php echo DOBACK;?>" class="button" onClick="history.go(-1);"><?php echo BLBACK;?></button>
<?php
  }
}
?>
</body>
</html>
