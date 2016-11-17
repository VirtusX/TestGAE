<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Test project</title>
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/main.css">
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery-3.1.1.min.js"></script>
    <script src="js/main.js"></script>
</head>
<body>
<div class="container-fluid">
    <div class="page-header">
        <h1>Manage the organizational structure</h1>
    </div>
    <div>
        <div class="col-md-2 panel panel-default col-md-offset-2">
            <div class="control-label panel-title modal-header" id="select_head">Choose company</div>
            <div class="panel-body" style="display: none;" id="select">
                <select class="form-control" id="select" title="Select company">
                    <option value='0'>Choose company:</option>
                    <?php
                    require_once "db.php";
                    $link = dataBase::getLink();
                    $query = "SELECT * FROM company_list";
                    $result = mysqli_query($link, $query) or die(mysqli_error($link));
                    if ($result) {
                        foreach ($result as $item) {
                            $id = $item["company_id"];
                            echo "<option value='$id'>" . $item["company_name"] . "</option>";
                        }
                    }
                    mysqli_close($link);
                    ?>
                </select>
            </div>
        </div>
        <div id="add" class="col-md-2 col-md-offset-1  panel panel-default">
            <div class="modal-header panel-title" id="form_open">Add new company</div>
            <div class="panel-body" style="display: none;" id="form">
                <form class="form-inline" method="POST" id="new_company">
                    <div class="form-group">
                        <input class="form-control" required id="name" name="name" type="text"
                               placeholder="Company name">
                    </div>
                    <div class="input-group">
                        <input class="form-control" required id="earning" name="earning" type="number"
                               placeholder="Estimated earning">
                        <span class='input-group-addon'>$</span>
                    </div>
                    <div class="form-group">
                        <select class="form-control" name="m_comp" id="m_id" title="Select main company">
                            <option value=''>Select main company:</option>
                            <?php
                            $link = dataBase::getLink();
                            $query = "SELECT * FROM company_list";
                            $result = mysqli_query($link, $query) or die(mysqli_error($link));
                            if ($result) {
                                foreach ($result as $item) {
                                    $id = $item["company_id"];
                                    echo "<option value='$id'>" . $item["company_name"] . "</option>";
                                }
                            }
                            mysqli_close($link);
                            ?>
                        </select>
                    </div>
                    <button class="btn btn-primary"><span class="glyphicon glyphicon-ok"></span></button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-offset-1 col-md-9" id="company">
    </div>
</div>
<?php
if (isset($_POST['name']) && isset($_POST['earning'])) {
    Company::add($_POST['name'], $_POST['earning'], $_POST['m_comp']);
    header("Location: index.php");
}
if (isset($_POST['id']) && isset($_POST['c_name']) && isset($_POST['c_earn'])) {
    Company::update($_POST['id'], $_POST['c_name'], $_POST['c_earn']);
}
if (isset($_GET['delete'])) {
    Company::delete($_GET['delete']);
    header("Location: index.php");
}
?>
</body>
</html>