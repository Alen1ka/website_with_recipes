<?php
$link = new mysqli('localhost', 'root', '', 'recipe');
if (mysqli_connect_error()) {
 die('Ошибка подключения (' . mysqli_connect_errno() . ') '
  . mysqli_connect_error());
} 
mysqli_set_charset($link, "UTF-8");
function clear_string ($cl_str){
    $mysqldb = new mysqli('localhost', 'root', '', 'recipe');
    $cl_str = strip_tags ($cl_str);
    $cl_str = mysqli_real_escape_string ($mysqldb,$cl_str);
    $cl_str = trim ($cl_str);
    return $cl_str;
}
$id = clear_string($_GET["id"]);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="content-type" content="text/"; charset="utf-8">
        <link rel="stylesheet" href="css/stylez.css">
        <link rel="stylesheet" href="css/reset.css">
        <script type="text/javascript" src="jquery-1.8.2.min.js"></script>
        <script type="text/javascript">
        </script>
        <title>Кулинарные рецепты</title>
    </head>
    <body>
        <div id="block-body">
            <div id="block-header">
                <div id="img-logo">
                    <a href="index.php"><img src="images/logo.jpg" width="72" height="99" alt="logo"/></a>
                </div>
                <div id="category">
                    <div class="block-category">
                        <p class="category-title">Категории рецептов</p>
                        <div class="block-category-run">
                        <ul class="category-section">
                        <li><a href="index.php"><strong>Все рецепты</strong></a></li>
                        <?php
                            $result = mysqli_query($link, "SELECT * FROM `category`");
                            if(mysqli_num_rows($result) > 0)/*если товаров больше нуля*/
                            {
                                $row = mysqli_fetch_array($result);
                                do {
                                        echo '
                                        <li><a href="view_type.php?type='.$row["type"].'">'.$row["type"].'</a></li>';
                                    }
                                while ($row = mysqli_fetch_array($result));
                            }
                            ?>
                        </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div id="block-content">
                <?php
                    $result1 = mysqli_query($link, "SELECT * FROM `table_recipe` WHERE recipe_id='$id'");
                    if(mysqli_num_rows($result1) > 0)/*если товаров больше нуля*/
                    {
                        $row1 = mysqli_fetch_array($result1);
                        do {
                           if(strlen($row1["image"]) > 0 && file_exists("./images/".$row1["image"])) {
                            $img_path='./images/'.$row1["image"];/*путь к картинке*/
                            $max_width = 300;
                            $max_height = 300;
                            list($width, $height) = getimagesize($img_path);
                            $ratioh = $max_height/$height;
                            $ratiow = $max_width/$width;
                            $ratio = min($ratioh, $ratiow);
                            $width = intval($ratio*$width);
                            $height = intval($ratio*$height);
                            }
                            else{
                                $img_path = "images/no-image.png";
                                $width = 110;
                                $height = 200;
                            }
                            $query_reviews = mysqli_query($link, "SELECT * FROM `table_reviews` WHERE products_id='$id' ORDER BY reviews_id DESC");
                            $count_reviews = mysqli_num_rows($query_reviews);
                            echo'
                            <div id="block-breadcrumbs-and-rating"><p id="nav-breadcrumbs"><a href="index.php">Все рецепты</a> \ <span>'.$row1["type_recipes"].'</span></p>
                            </div>
                            <div id="block-content-info">
                            <img src="'.$img_path.'" width="'.$width.'" height="'.$height.'"/>
                            <div id="block-mini-description">
                            <p id="content-title">'.$row1["title"].'</p>
                            <ul class="reviews-and-counts">
                            <li><img src="images/eye-icon.png" /><p>'.$row1["count"].'</p></li>
                            <li><img src="images/comment-icon.png" /><p>'.$count_reviews.'</p></li>
                            <li><p id="data">'.$row1["datatime"].'</p></li>
                            </ul>
                            <p id="content-text">'.$row1["mini_description"].'</p>
                            </div>
                            </div>
                            ';
                            echo '
                            <ul class="tabs">
                                <li><div id=title1>Описание</div><p>Ингредиенты:</p><p>'.$row1["ingredients"].'</p><p>Время приготовления: '.$row1["time"].'</p><p>Количество порций: '.$row1["number"].'</p></li>
                                <li><div id=title1>Приготовление</div><p>'.$row1["description"].'</p></li>
                                <li><div id=title1>Отзывы</div>
                                ';
                                    $query_reviews = mysqli_query($link, "SELECT * FROM `table_reviews` WHERE products_id='$id' ORDER BY reviews_id DESC");
                                    if(mysqli_num_rows($query_reviews) > 0)/*если товаров больше нуля*/
                                    {
                                        $row_reviews = mysqli_fetch_array($query_reviews);
                                        do {
                                            echo'
                                            <div class="block-reviews">
                                            <p class="author-date"><strong>'.$row_reviews["name"].'</strong>, '.$row_reviews["date"].'</p>
                                            <p class="text-comment">'.$row_reviews["comment"].'</p>
                                            </div>
                                            ';
                                        }
                                        while ($row_reviews = mysqli_fetch_array($query_reviews));
                                    }
                                        else
                                        {
                                            echo '<p class="title-no-info>Отзывов нет</p>';
                                        }
                                '
                                </li>
                            </ul>
                            ';
                        }
                        while ($row1 = mysqli_fetch_array($result1));
                    }
                ?>
            </div>
            <div id="block-footer">
            </div>
        </div>
    </body>
</html>