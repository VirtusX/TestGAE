<?php

class dataBase
{
    private static $host = '104.199.49.189';
    private static $database = 'test_db';
    private static $user = 'test';
    private static $pass = 'test';
    private static $port = '3306';

    public static function getLink()
    {
        $link = mysqli_connect(dataBase::$host, dataBase::$user, dataBase::$pass, dataBase::$database, dataBase::$port) or die("Error while connecting to database " . mysqli_error($link));
        return $link;
    }
}

class Company
{
    static function add($name, $earning, $m_id)
    {
        $link = dataBase::getLink();
        $name = htmlentities(mysqli_real_escape_string($link, $name));
        $earning = htmlentities(mysqli_real_escape_string($link, $earning));
        $query = "INSERT INTO company_list VALUES(NULL,'$name','$earning')";
        $result = mysqli_query($link, $query) or die("Problem with adding");
        if (isset($m_id)) {
            $query = "SELECT company_id FROM company_list WHERE company_name ='$name'";
            $result = mysqli_query($link, $query);
            $id = mysqli_fetch_array($result)['company_id'];
            $query = "INSERT INTO organization_tree VALUES(NULL,$m_id,$id)";
            $result = mysqli_query($link, $query) or die("Some problem with org tree " . mysqli_error($link));
        }
        mysqli_close($link);
    }

    static function update($id, $name, $earning)
    {
        $link = dataBase::getLink();
        $name = htmlentities(mysqli_real_escape_string($link, $name));
        $earning = htmlentities(mysqli_real_escape_string($link, $earning));
        $query = "UPDATE company_list SET company_name ='$name', company_earnings ='$earning' WHERE company_id = $id";
        $result = mysqli_query($link, $query) or die("Problem with update" . mysqli_error($link));
        mysqli_close($link);
    }

    static function delete($id)
    {
        $link = dataBase::getLink();
        $id = htmlentities(mysqli_real_escape_string($link, $id));
        $query = "DELETE FROM company_list WHERE company_id =$id";
        $org_query = "DELETE FROM organization_tree WHERE main_id = $id OR sub_id = $id";
        $result = mysqli_query($link, $query) or die("Problem with update" . mysqli_error($link));
        $result = mysqli_query($link, $org_query) or die("Problem with deleting dependencies " . mysqli_error($link));
        mysqli_close($link);
    }
}


