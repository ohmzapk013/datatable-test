<?php

  include "connection.php";

  session_start();

  $output = '';

  if(isset($_POST["action"])){

    // Fetch user
    if($_POST["action"] == "user_fetch"){

      // Read value
      $draw = $_POST['draw'];
      $row = $_POST['start'];
      $rowperpage = $_POST['length'];
      $columnIndex = $_POST['order'][0]['column'];
      $columnName = $_POST['columns'][$columnIndex]['data'];
      $columnSortOrder = $_POST['order'][0]['dir'];
      $searchValue = $_POST['search']['value'];

      // Search
      $searchQuery = " ";
      if($searchValue != ''){
        $searchQuery = " and (id LIKE '%".$searchValue."%' OR
              username LIKE '%".$searchValue."%' OR
              firstname LIKE '%".$searchValue."%' OR
              lastname LIKE '%".$searchValue."%' OR
              address LIKE '%".$searchValue."%' OR
              email LIKE '%".$searchValue."%' OR
              phone LIKE '%".$searchValue."%' ) ";
      }

      // Total number of records without filtering
      $sel = mysqli_query($conn,"SELECT count(*) AS allcount FROM tbl_user");
      $records = mysqli_fetch_assoc($sel);
      $totalRecords = $records['allcount'];

      // Total number of records with filtering
      $sel = mysqli_query($conn,"SELECT count(*) AS allcount FROM tbl_user WHERE 1 ".$searchQuery);
      $records = mysqli_fetch_assoc($sel);
      $totalRecordwithFilter = $records['allcount'];

      // Fetch records
      $empQuery = "SELECT * FROM tbl_user WHERE 1 ".$searchQuery." ORDER BY ".$columnName." ".$columnSortOrder." LIMIT ".$row.",".$rowperpage;
      $empRecords = mysqli_query($conn, $empQuery);
      $data = array();



      while ($row = mysqli_fetch_assoc($empRecords)) {

        $data[] = array(
          "id"              =>$row['id'],
          "username"     =>$row['username'],
          "password"     =>$row['password'],
          "firstname"     =>$row['firstname'],
          "lastname"          =>$row['lastname'],
          "address"            =>$row['address'],
          "email"            =>$row['email'],
          "phone"            =>$row['phone'],
          "action"       =>
          '<button type="button" class="btn btn-primary edit_user" data-toggle="modal" data-target="#editModal" id="'.$row['id'].'">Update</button>
          <button type="button" class="btn btn-danger delete_user" id="'.$row['id'].'">Delete</button>
          '
        );


      }

      $response = array(
        "draw" => intval($draw),
        "iTotalRecords" => $totalRecords,
        "iTotalDisplayRecords" => $totalRecordwithFilter,
        "aaData" => $data,
      );

      echo json_encode($response);

    }

    // Add user
    if($_POST["action"] == "Add"){

      $username = $_POST['username'];
      $password = sha1($_POST['password']);
      $firstname = $_POST['firstname'];
      $lastname = $_POST['lastname'];
      $address = $_POST['address'];
      $email = $_POST['email'];
      $phone = $_POST['phone'];

      $sql = "INSERT INTO tbl_user (username, password, firstname, lastname, address, email, phone) VALUES('$username', '$password',  '$firstname', '$lastname', '$address', '$email', '$phone')";

      if(mysqli_query($conn, $sql)){
        $output = array(
          'status'        => 'success',
          'alert'         => 'New user has been successfully added.'
        );
      }else{
        $output = array(
          'status'        => 'error'
        );
      }

      echo json_encode($output);

    }

    // Update user
    if($_POST["action"] == "Edit"){

      $user_id = $_POST['user_id'];
      $username = $_POST['username'];
      $password = sha1($_POST['password']);
      $firstname = $_POST['firstname'];
      $lastname = $_POST['lastname'];
      $address = $_POST['address'];
      $email = $_POST['email'];
      $phone = $_POST['phone'];

      $sql = "UPDATE tbl_user SET username = '$username',
                                  password = '$password',
                                  firstname = '$firstname',
                                  lastname = '$lastname',
                                  address = '$address',
                                  email = '$email',
                                  phone = '$phone'
                                  WHERE id = '$user_id'";

      $result = mysqli_query($conn, $sql);

      $output = array(
        'status'        => 'success',
        'alert'         => 'user has been successfully updated.'
      );

        echo json_encode($output);
    }

    // Single edit fetch
    if($_POST["action"] == "edit_fetch"){

      $user_id = $_POST['user_id'];

      $sql = "SELECT id, username, password, firstname, lastname, address , email, phone FROM tbl_user WHERE id = '$user_id'";

      $result = mysqli_query($conn, $sql);

      $row = mysqli_fetch_row($result);

      $output = array(
        "id"		        =>	$row[0],
        "username"		      =>	$row[1],
        "password"		      =>	$row[2],
        "firstname"		    => 	$row[3],
        "lastname"		      => 	$row[4],
        "address"		        => 	$row[5],
        "email"		          => 	$row[6],
        "phone"	            =>	$row[7],
      );

      echo json_encode($output);

    }

    // Delete user
    if($_POST["action"] == "delete"){

      $user_id = $_POST['user_id'];

      $sql = "DELETE FROM tbl_user WHERE id='$user_id'";

      $result = mysqli_query($conn, $sql);

      $output = array(
          'status'        => 'success'
      );

      echo json_encode($output);

    }

  }

?>