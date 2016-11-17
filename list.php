<?php
require_once "db.php";
$this_id = intval($_GET['c']);
$link = dataBase::getLink();
$query = "SELECT * FROM company_list WHERE company_id = $this_id";
$result = mysqli_query($link, $query);
$query = "SELECT company_name FROM company_list WHERE company_id =(SELECT main_id FROM organization_tree WHERE sub_id = $this_id)";
$m_id_raw = mysqli_query($link, $query) or die(mysqli_error($link));
$m = mysqli_fetch_array($m_id_raw)['company_name'];
$main = $m == "" ? "No main company" : $m;
$query = "SELECT * FROM company_list WHERE company_id IN (SELECT sub_id FROM organization_tree WHERE main_id = $this_id)";
$s_id = mysqli_query($link, $query) or die(mysqli_error($link));
$query = "SELECT * FROM company_list";
$org = mysqli_query($link, $query);
if ($result) {
    while ($row = mysqli_fetch_array($result)) {
        $id = $row['company_id'];
        $name = $row['company_name'];
        $earning = $row['company_earnings'];
        echo "
<form class='form-horizontal' method='POST'>
    <div class='form-group'>
        <label for='c_name' class='control-label col-sm-3'>Company name</label>
        <div class='col-sm-9 input-group'>
            <input type='text' id='c_name' required class='form-control com_prop' name='c_name' value='$name' readonly>
            <div class='input-group-btn' role='group'>
                <button class='btn btn-warning c_edit' id='edit'><span class='glyphicon glyphicon-pencil' aria-hidden='true'></span></button>
                <button class='btn btn-danger c_edit' id='delete'><span class='glyphicon glyphicon-trash' aria-hidden='true'></span> </button>
            </div>
        </div>
    </div>
<input id='id' hidden name='id' value='$id'>
    <div class='form-group'>
        <label for='c_earn' class='control-label col-sm-3'>Company estimated earning</label>
        <div class='col-sm-9 input-group'>
            <input type='number' id='c_earn' required class='form-control com_prop' value='$earning' name='c_earn' readonly >
            <span class='input-group-addon'>$</span>
        </div>
    </div>
    <div class='form-group'>
         <label for='main' class='control-label col-sm-3'>Main company</label>
         <div class=' col-sm-9 input-group'>";
        /*
         * Uncomment to editing main company
          <select disabled class='form-control check' id='main'>
           <option value=''>No main company</option>";
           foreach ($org as $row) {
               $id = $row['company_id'];
               $name = $row['company_name'];
               if($id == $m_id)
                   echo "<option value='$id' selected>$name</option>";
               elseif($id != $this_id)
                   echo "<option value='$id'>$name</option>";
           }
        echo "</select>
        */
        echo "<input type='text' id='main' readonly class='form-control' value='$main'>
        </div>
    </div>";
        /*
         * Uncomment to editing sub-companies
         echo "<div class='form-control check'>";
    $id_not_in = Array();
    $id_in = Array();
    foreach ($org as $row) {
        foreach ($s_id as $id_row) {
            $id = $row['company_id'];
            $name = $row['company_name'];
            if ($id == $id_row['sub_id'] &&!in_array($id_row['sub_id'],$id_in)) {
                array_push($id_in,$id);
                echo "<input value='$id' type='checkbox' class='check' name='sub' hidden checked><span class='check'>$name; </span>";
            }
            elseif ($id != $this_id&&!in_array($id_row['sub_id'],$id_not_in)&&!in_array($id_row['sub_id'],$id_in)) {
                array_push($id_not_in,$id_row['sub_id']);
                echo "<input value='$id' type='checkbox' class='check' name='sub' hidden><span hidden class='check'>$name; </span>";
            }
        }
    }
     echo "</div>";*/
        echo "<div class='form-group'> <label class='control-label col-sm-5 col-sm-offset-3'>Subsidiary companies</label></div>";
        if ($s_id->num_rows === 0)
            echo "<div class='form-group'>
                        <div class='col-sm-9 col-sm-offset-3 hierarchy-1 input-group'>
                            <input type='text' required class='form-control' value='No subsidiary companies' readonly>
                        </div>
                      </div>";
        foreach ($s_id as $id) {
            $this_earn = $id['company_earnings'];
            $earning += $this_earn;
            $n = $id['company_name'];
            $total = total($id['company_id'], $this_earn);
            echo "<div class='form-group'>
                        <div class='col-sm-9 col-sm-offset-3 hierarchy-1 input-group'>
                            <input type='text' required class='form-control' value='$n - $this_earn$, total - $total$' readonly>
                        </div>
                      </div>";
            $earning += subComp($id['company_id'], 0, 2);
        }
        echo "<div class='form-group'>
<label for='sub' class='control-label col-sm-3'>Total estimated earning</label>
         <div class=' col-sm-9 input-group'>";
        echo "<input type='number' id='c_earn' class='form-control' value='$earning' readonly >
            <span class='input-group-addon'>$</span>
         </div>
    </div>
</form>";
    }
    mysqli_close($link);
}
function subComp($id, $earn, $hier)
{
    $link = dataBase::getLink();
    $query = "SELECT * FROM company_list WHERE company_id IN (SELECT sub_id FROM organization_tree WHERE main_id = $id)";
    $result = mysqli_query($link, $query) or die(mysqli_error($link));
    if ($result) {
        foreach ($result as $s_id) {
            $this_earn = $s_id['company_earnings'];
            $earn += $this_earn;
            $n = $s_id['company_name'];
            $total = total($s_id['company_id'], $this_earn);
            echo "<div class='form-group'>
                        <div class='col-sm-9 col-sm-offset-3 hierarchy-$hier input-group'>
                            <input type='text' required class='form-control' value='$n - $this_earn$, total - $total$' readonly>
                        </div>
                      </div>";
            subComp($s_id['company_id'], $earn, $hier + 1);
        }
    }
    mysqli_close($link);
    return $earn;
}

function total($id, $earn)
{
    $link = dataBase::getLink();
    $query = "SELECT * FROM company_list WHERE company_id IN (SELECT sub_id FROM organization_tree WHERE main_id = $id)";
    $result = mysqli_query($link, $query) or die(mysqli_error($link));
    if ($result) {
        foreach ($result as $s_id) {
            $earn += $s_id['company_earnings'];
            total($s_id['company_id'], $earn);
        }
    }
    mysqli_close($link);
    return $earn;
}