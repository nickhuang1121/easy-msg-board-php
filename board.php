<?php
    $db_host = "localhost";
    $db_username = "root";
    $db_password = "1234";
    $db_name = "phpboard";
    $db_link = new mySqli($db_host,$db_username,$db_password,$db_name);

    if($db_link->connect_error == ""){
        echo "LINK OK";
        $db_link->query("SET NAME 'utf8'");
    };

    if(isset($_POST["action"]) && ($_POST["action"] == "add") ){
        echo "有POST";
        $sendSQL = "INSERT INTO board (boardname ,boardsex ,boardsubject ,boardtime ,boardmail ,boardweb ,boardcontent) VALUES (?, ?, ?, NOW(), ?, ?, ?)";
        $stmt = $db_link->prepare($sendSQL);
        $stmt->bind_param("ssssss",
        $_POST["boardname"],
        $_POST["boardsex"],
        $_POST["boardsubject"],
        $_POST["boardmail"],
        $_POST["boardweb"],
        $_POST["boardcontent"]);

        $stmt->execute();
        $stmt->close();
        header("Location: board.php?page=1");

    }else{
        echo "無POST";
    };

    

    $sendSQL_getAllData = "SELECT * FROM `board` ORDER BY `boardtime` DESC";
    $allData = $db_link->query($sendSQL_getAllData);
    $allData_length = $allData->num_rows;

    $viewRowsNum = 4;
    $page = 1;
    if(isset($_GET["page"])){
        $page =$_GET["page"];
    };

    $getStartRows = ($page - 1)* $viewRowsNum;
    $sendSQL_getLimitData = $sendSQL_getAllData." LIMIT {$getStartRows},{$viewRowsNum}";
    $limitData = $db_link->query($sendSQL_getLimitData);

    $totalPage = ceil($allData_length / $viewRowsNum);
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <style>
        *{ box-sizing:border-box; }
        main { margin : 0 auto; width:500px}
        .msg { margin-bottom:20px; border:1px solid #ddd}
        .msg p { font:15px/1.4 "微軟正黑體";
        color:black}

        .pageBtn {display:flex;}
        .pageBtn > div { display:inline-block; width:50%}
    </style>
</head>
<body>
   <main>   
    <?php while($item = $limitData->fetch_assoc()){ ?>
        <div class="msg">
           <p>姓名： <?php echo $item['boardname']; ?></p>
           <p>標題： <?php echo $item['boardsubject']; ?></p>
           <p>內容： <?php echo $item['boardcontent']; ?></p>
        </div>        
    <?php } ?>
    <div class="pageBtn">
        <div>
            <?php if($page > 1) {?>
                <a href="<?php echo "?page=".($page-1); ?>">上一頁</a>
            <?php } ?>
        </div>
        <div>
        <?php if($page < $totalPage) {?>
                <a href="<?php echo "?page=".($page+1); ?>">下一頁</a>
            <?php } ?>
        </div>
    </div>

    <div>
        <form action="" method="post" name="formPost">
        <input type="text" name="boardname">
        <input type="text" name="boardsubject">
        <input type="text" name="boardmail">
        <input type="text" name="boardweb">
        <p>性別
            <input name="boardsex" type="radio" id="radio" value="男" checked>男
            <input name="boardsex" type="radio" id="radio2" value="女">女
        </p>
        <textarea name="boardcontent" id="" cols="30" rows="10"></textarea>
            <input type="hidden" name="action" value="add">
            <input type="submit" value="送出">
        </form>
    </div>

    </main>   
</body>
</html>