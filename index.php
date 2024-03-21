<?php 
$Host="localhost";
$UserName="root";
$Password="";
$dbName="company";
$con= mysqli_connect($Host,$UserName,$Password,$dbName);
$mood="create";



//select all departments
$selectDepartment="SELECT * FROM `departments`";
$departments= mysqli_query( $con , $selectDepartment );

//select all supervisors
$selectSupervisor="SELECT * FROM `supervisors`";
$supervisors= mysqli_query( $con , $selectSupervisor );

// dark and light mood 
$selectMode= "SELECT * FROM theme WHERE id = 1";
$theme= mysqli_query( $con, $selectMode );
$mode= mysqli_fetch_assoc( $theme );

if (isset($_GET['color'])){
    $color= $_GET['color'];
    $updateThemeQuery= "UPDATE theme SET mode='$color' WHERE id=1 ";
    $updateTheme= mysqli_query( $con , $updateThemeQuery);
}

//create
if(isset($_POST["submit"])){
    $name=$_POST["name"];
    $department= $_POST["department"];
    $supervisor= $_POST["supervisor"];
    $gender=$_POST["gender"];

    //image code 
    $image_name= rand(0,255). rand(0,255). $_FILES['image']['name'];
    $image_tmp=$_FILES['image']['tmp_name'];
    $location="./uploads/". $image_name;
    move_uploaded_file($image_tmp,$location);

    $insert = "INSERT INTO `employees` VALUES (NULL,'$name','$gender',' $department',' $supervisor','$image_name')";
    $insertQuery= mysqli_query($con, $insert);

    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}
//empty variables
$userId=null;
$name="";
$department="";
$gender="";
$supervisor="";
$image=null;


//edit
if(isset($_GET['edit'])){
    $id=$_GET['edit'];
    $selectOne= "SELECT * FROM `employees` WHERE id = $id";
    $getOne=mysqli_query($con,$selectOne);
    $row= mysqli_fetch_assoc($getOne);
    $name=$row['name'];
    $department= $row["department_id"];
    $supervisor=$row['supervisor_id'];
    $image=$row['image'];
    $gender=$row["gender"];
    $mood="update";
    $userId=$id;

}

if (isset($_POST['update'])) {
     //image code 
     if($_FILES['image']['name']== null){
        $image_name=$image;
     }else{
     
     $image_name= rand(0,255). rand(0,255). $_FILES['image']['name'];
     $image_tmp=$_FILES['image']['tmp_name'];
     $location="./uploads/". $image_name;
     move_uploaded_file($image_tmp,$location);
     unlink("./uploads/$image");
     }
    $name=$_POST["name"];
    $department= $_POST["department"];
    $supervisor=$_POST["supervisor"];
    $gender=$_POST["gender"];
    $update= "UPDATE `employees` SET `name`='$name',`department_id`='$department',`supervisor_id`=$supervisor,`gender`='$gender' , `image`='$image_name' WHERE id ='$userId' ";
    $updateQuery= mysqli_query($con,$update);
    $mood="create";
    header('location:index.php');
}




//delete
if (isset($_GET['delete'])) {
 
$id=$_GET['delete'];
//old image
$selectOneDelete="SELECT * FROM employees where id = $id";
$selectOneDeleteQuery= mysqli_query($con,$selectOneDelete);
$rowDataDeleted=mysqli_fetch_assoc($selectOneDeleteQuery);
$oldImage=$rowDataDeleted['image'];
// delete old image
unlink("./uploads/$oldImage");
// delete row
    $delete = "DELETE FROM employees WHERE id = $id";
    $deleteQuery= mysqli_query($con, $delete);
}

//read
$select = "SELECT * FROM `employee_view`";
$selectQuery= mysqli_query($con, $select);



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <?php  if($mode['mode']=='dark'): ?>
    <link rel="stylesheet" href="main.css">
    <?php endif;?>
</head>
<body> 
    <?php  if($mode['mode']=='dark'): ?>
    <a href="?color=light" class="btn btn-light ">light mood</a>
    <?php else: ?>
    <a href="?color=dark" class="btn btn-dark ">dark mood</a>
    <?php endif;?>

    <div class="container col-6">
        <div class="row justify-content-center mt-5">
            <div class="col-12">
                <div class="card bg-dark text-light">
                    <div class="card-body">
                        <form action="" method="post" enctype="multipart/form-data">
                            <div class="form-group mb-3">
                                <label for="name" class="form-label">name</label>
                                <input type="text" value="<?= $name?>" class="form-control" name="name" id="name">
                            </div>
                            <div class="form-group mb-3">
                                <label for="department" class="form-label">department</label>
                                <select name="department" class="form-control">
                                <?php foreach($departments as $item): ?>    
                                <option value="<?= $item['id'] ?>"><?=  $item['name'] ?></option>
                                <?php  endforeach;?>
                                </select>
                                
                            </div>
                            <div class="form-group mb-3">
                                <label for="supervisor" class="form-label">supervisor</label>
                                <select name="supervisor" class="form-control">
                                <?php foreach($supervisors as $item): ?>    
                                <option value="<?= $item['id'] ?>"><?=  $item['name'] ?></option>
                                <?php  endforeach;?>
                                </select>
                                
                            </div>
                            <div class="form-group mb-3">
                                <label for="gender" class="form-label">gender</label>
                                <input type="text"  value="<?= $gender?>" class="form-control" name="gender" id="gender">
                            </div>
                            <div class="form-group mb-3">
                                <label for="gender" class="form-label">image : <img width="60" src="./uploads/<?= $image?>" alt=""></label>
                                <input type="file"  value="<?= $image?>" class="form-control" name="image" id="image">
                            </div>
                            <div class="form-group mb-3 text-center">
                                <?php if($mood == "create"): ?>
                                    <button class="btn btn-primary" name="submit" type="submit">Add Employee</button>
                                <?php else: ?>
                                    <button class="btn btn-warning" name="update" type="submit">Update Employee</button>
                                    <a href="index.php" class="btn btn-dark" name="cancel" type="submit"> cancel</a>
                                <?php endif ?>


                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 mt-3">
            <table class="table table-dark">
                <tr>
                    <th>id</th>
                    <th>name</th>
                    <th>department</th>
                    <th>supervisor</th>
                    <th>image</th>
                    <th>gender</th>
                    <th colspan="2">Action</th>
                </tr>
                <?php foreach ($selectQuery as $employee): ?>
                <tr>
                    <td><?= $employee['id'] ?></td>
                    <td><?= $employee['name'] ?></td>
                    <td><?= $employee['department_name'] ?></td>
                    <td><?= $employee['supervisor_name'] ?></td>
                    <td><img style="width: 70px;" src="./uploads/<?= $employee['image'] ?>" alt=""></td>
                    <td><?= $employee['gender'] ?></td>
                    <td><a href="?edit=<?= $employee['id']?>"  name="edit" class="btn btn-warning">Edit</a ></td>
                    <td><a  href="?delete=<?= $employee['id']?>" name="delete" class="btn btn-danger">Delete</a></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
</body>
</html>