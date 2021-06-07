<?php
$link = new mysqli('localhost', 'root', '', 'recipe');
if (mysqli_connect_error()) {
 die('Ошибка подключения (' . mysqli_connect_errno() . ') '
  . mysqli_connect_error());
} 
mysqli_set_charset($link, "UTF-8");
$type = isset($_GET['type']) ? $_GET['type'] : null;
$type = strip_tags($type);
$type = $link->real_escape_string($type);
$type = trim($type);
$sorting = isset($_GET['sort']) ? $_GET['sort'] : null;
switch($sorting){
    case 'news';
        $sorting = 'datatime DESC';
        $sort_name = 'по дате создания(вначале новые)';
        break;
    case 'popular';
        $sorting = 'count DESC';
        $sort_name = 'по просмотрам';
        break;
    case 'rating';
        $sorting = 'votes DESC';
        $sort_name = 'по рейтингу';
        break;
    case 'comment';
        $sorting = 'vote DESC';
        $sort_name = 'по числу комментариев';
        break;
    default:
        $sorting = 'recipe_id DESC';
        $sort_name = 'нет сортировки';
        break;
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="content-type" content="text/"; charset="utf-8">
        <link rel="stylesheet" href="css/stylez.css">
        <link rel="stylesheet" href="css/reset.css">
        <script type="text/javascript" src="jquery-1.8.2.min.js"></script>
        <script type="text/javascript">
            $(document).ready(function(){
                $("#style-grid").click(function(){
                    $("#block-recipe-list").hide();
                    $("#block-recipe-grid").show();      
                    $.cookie('select_style','grid');
                });
                    $("#style-list").click(function(){
                    $("#block-recipe-grid").hide();
                    $("#block-recipe-list").show();
                    $.cookie('select_style','list');
                });
                $("#select-sort").click(function(){
                    $("#sorting-list").slideToggle(200);
                });
                });
        </script>
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
                    if(!empty($type)){
                        $querytype = "type_recipes = '$type'";
                    }else{
                        $querytype = "";
                    }
                    $result = mysqli_query($link, "SELECT * FROM `table_recipe` WHERE $querytype ORDER BY $sorting");
                    if(mysqli_num_rows($result) > 0)/*если товаров больше нуля*/
                        {
                        $row = mysqli_fetch_array($result);
                        echo'
                        <div id="block-sorting">
                            <p id="nav-breadcrumbs"><a href="index.php">Главная страница</a> \ <span>'.$row["type_recipes"].'</span></p></p>
                            <ul id="options-list">
                                <li>Вид: </li>
                                <li><img id="style-grid" src="images/icon-grid-active.png"/></li>
                                <li><img id="style-list" src="images/icon-list-active.png"/></li>
                                <li>Сортировать:</li>
                                <li><div class="select-sort">'.$sort_name.'
                                <div class="select-sort-run">
                                    <ul id="sorting-list">
                                        <li><a href="view_type.php?type='.$type.'&sort=news">по дате создания(вначале новые)</a></li>
                                        <li><a href="view_type.php?type='.$type.'&sort=popular">по просмотрам</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                        <ul id="block-recipe-grid">';
                        do {
                            if ($row["image"]!="" && file_exists("./images/".$row["image"]))
                            {
                                $img_path='./images/'.$row["image"];/*путь к картинке*/
                                $max_width = 200;
                                $max_height = 200;
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
                            echo'
                            <li>
                                <div class="block-images-grid">
                                    <img src="'.$img_path.'" width="'.$width.'" height="'.$height.'" />
                                </div>
                                <p class="style-title-grid"><a href="view_content.php?id='.$row['recipe_id'].'">'.$row["title"].'</a></p>
                                <p class="style-mini-description">'.$row["mini_description"].'</p>
                                <ul class="reviews-and-counts-grid">
                                    <li><img src="images/eye-icon.png" /><p>'.$row["count"].'</p></li>
                                </ul>
                            </li>
                            ';
                        }
                        while ($row = mysqli_fetch_array($result));
                        echo'</ul>';
                ?>
                <ul id="block-recipe-list">
                <?php
                    $result = mysqli_query($link, "SELECT * FROM `table_recipe` WHERE $querytype ORDER BY $sorting ");
                    if(mysqli_num_rows($result) > 0)/*если товаров больше нуля*/
                    {
                        $row = mysqli_fetch_array($result);
                        do
                        {
                            if ($row["image"]!="" && file_exists("./images/".$row["image"]))
                            {
                                $img_path='./images/'.$row["image"];
                                $max_width = 150;
                                $max_height = 150;
                                list($width, $height) = getimagesize($img_path);
                                $ratioh = $max_height/$height;
                                $ratiow = $max_width/$width;
                                $ratio = min($ratioh, $ratiow);
                                $width = intval($ratio*$width);
                                $height = intval($ratio*$height);
                            }
                            else{
                                $img_path = "images/noimages80x70.png";
                                $width = 80;
                                $height = 70;
                            }
                            echo'
                            <li>
                                <div class="block-images-list">
                                    <img src="'.$img_path.'" width="'.$width.'" height="'.$height.'" />
                                </div>
                                <ul class="reviews-and-counts-list">
                                    <li><img src="images/eye-icon.png" /><p>'.$row["count"].'</p></li>
                                </ul>
                               <p class="style-title-grid"><a href="view_content.php?id='.$row['recipe_id'].'">'.$row["title"].'</a></p>
                                <p class="style-mini-description-list">'.$row["mini_description"].'</p>
                            </li>
                            ';
                        }
                        while ($row = mysqli_fetch_array($result));
                    }}?>
                    </ul>
            </div>
            <div id="block-footer">
            </div>
        </div>
    </body>
</html>