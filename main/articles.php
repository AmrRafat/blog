<?php
session_start();
include "init.php";
?>
<div style="margin-top:100px"></div>
<div class="container mainArticle">
    <!-- Main articles page -->
    <?php if (empty($_GET)) {?>
        <?php
$stmt = $con->prepare("SELECT rate FROM comments");
    $stmt->execute();
    $check = $stmt->rowCount();
    if ($check > 0) {
        ?>
        <h2 class="mb-3">Hot Articles</h2> <!-- NOTE: Hot Articles -->
        <div id="hotArticles" class="carousel slide mx-auto overflow-hidden rounded-4" style="height:450px;">
            <div class="carousel-indicators">
                <?php
$stmt = $con->prepare("SELECT articles.*, AVG(comments.rate) as AvgRate FROM articles INNER JOIN comments ON articles.article_id = comments.article_id GROUP BY articles.article_id ORDER BY AvgRate DESC LIMIT 5");
        $stmt->execute();
        $check = $stmt->rowCount();
        for ($i = 0; $i < $check; $i++) {
            if ($i == 0) {
                echo '<button type="button" data-bs-target="#hotArticles" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>';
            } else {
                echo '<button type="button" data-bs-target="#hotArticles" data-bs-slide-to="' . $i . '" aria-label="Slide ' . $i + 1 . '"></button>';
            }
        }
        ?>
            </div>
            <div class="carousel-inner h-100">
                <?php
$stmt = $con->prepare("SELECT articles.*, AVG(comments.rate) as AvgRate FROM articles INNER JOIN comments ON articles.article_id = comments.article_id GROUP BY articles.article_id ORDER BY AvgRate DESC LIMIT 5");
        $stmt->execute();
        $articles = $stmt->fetchAll();
        $first = 1;
        foreach ($articles as $article) {
            if ($first == 1) {
                $first++;
                ?>
        <div class="carousel-item h-100 active">
            <?php
} else {
                ?>
        <div class="carousel-item h-100">
            <?php
}
            ?>
<a href="<?php echo $_SERVER['PHP_SELF'] . '?' . $article['article_id'] ?>">
    <img src="<?php echo 'layout/imgs/articles/' . $article['article_id'] . '/1.' . $article['ext'] ?>" class="d-block position-absolute mx-auto" style="height: 110%; width: 110%; object-fit: cover; right: 50%; top: 50%; transform: translate(50%, -50%);filter: brightness(75%);">
    <div class="carousel-caption d-block bg-black bg-opacity-50">
        <h5><?php echo $article['title'] ?></h5>
        <?php
$desc = "";
            $DbDesc = json_decode($article['description']);
            foreach ($DbDesc as $line) {
                $desc = $desc . '<p class="mb-0">' . $line . '</p>';
            }
            echo $desc;
            ?>
            </div>
        </a>
    </div>
    <?php
}
        ?>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#hotArticles" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#hotArticles" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
        <?php }?>
        <h2 class="mt-3 mb-3">Newest Articles</h2> <!-- NOTE: Newest Articles -->
        <div class="card p-3 mb-3">
            <div class="row">
                <?php
$stmt = $con->prepare("SELECT * FROM articles ORDER BY date DESC LIMIT 6");
    $stmt->execute();
    $rows = $stmt->fetchAll();
    foreach ($rows as $row) {
        $urlImg = 'layout/imgs/articles/' . $row['article_id'] . '/1.' . $row['ext'];
        ?>
                    <div class="col-12 col-md-6">
                        <a href="?<?php echo $row['article_id'] ?>" class="mb-3 d-block position-relative">
                            <div class="card" style="overflow: hidden; height:150px">
                                <img src="<?php echo $urlImg ?>" class="d-block mx-auto position-absolute" alt="" style="top:50%; left:50%; transform:translate(-50%,-50%); filter:brightness(80%); object-fit:cover; width:110%; height:110%;">
                                <div class="info position-absolute ps-2 py-2 w-100 bg-white bg-opacity-75" style="bottom: 0; left:0;">
                                    <h5 class="titleInMain mb-0"><?php echo $row['title'] ?></h5>
                                </div>
                            </div>
                        </a>
                    </div>
                    <?php }
    ?>
            </div>
        </div>
        <h2 class="mt-3 mb-3">Subjects</h2> <!-- NOTE: Showing Subjects -->
        <div class="card mb-4 px-2 pb-2 g-2 row justify-content-center flex-row">
            <?php
foreach ($subjects as $subject) {?>
                <div class="col-lg-2 col-md-3 col-sm-4 col-6 p-1"><a href="?<?php echo $subject ?>" class="text-decoration-none text-reset btn btn-outline-secondary text-center w-100"><?php echo $subject ?></a></div>
                <?php }?>
            </div>
            <?php } else {
    $subject = array_keys($_GET)[0];
    $exp = "/\d/";
    if ($subject == 'my_articles') {?>
        <h1 class="text-center my-4">My articles</h1>
        <?php
$stmt = $con->prepare("SELECT * FROM articles WHERE user_id = ?");
        $stmt->execute(array($_SESSION['id']));
        $check = $stmt->rowCount();
        if ($check > 0) {
            $articles = $stmt->fetchAll();
            foreach ($articles as $article) {
                ?>
        <a href="?<?php echo $article['article_id'] ?>" class="text-decoration-none text-reset mb-3 d-block articleCard">
            <div class="card p-3" style="max-height: 200px; overflow:hidden">
                <div class="row" style="height: 160px;">
                    <div class="col-8">
                        <h3><?php echo $article['title'] ?></h3>
                        <?php
$descLines = json_decode($article['description']);
                foreach ($descLines as $line) {?>
        <p style="max-height: 125px; overflow:hidden; font-size: 18px; margin-bottom:0;"><?php echo $line ?></p>
        <?php
}
                ?>
                </div>
                <div class="col-4 position-relative">
                    <img src="<?php echo 'layout/imgs//articles/' . $article['article_id'] . '/1.' . $article['ext'] ?>" alt="" class="img-fluid img-thumbnail d-block mx-auto" style="max-height: 180px;position: absolute;top: 50%;right:0;transform: translateY(-50%)">
                </div>
            </div>
        </div>
    </a>
    <?php }
        } else {?>
                <div class="alert text-center">No articles yet</div>
                <?php }
        ?>
    <?php } elseif ($subject == "my_fav") {?>
        <h1 class="text-center my-4">My favourite articles</h1>
        <?php
$stmt = $con->prepare("SELECT fav_articles FROM users WHERE userid = ?");
        $stmt->execute(array($_SESSION['id']));
        $info = $stmt->fetch();
        $allFav = json_decode($info['fav_articles']);
        if ($allFav != []) {
            foreach ($allFav as $oneFav) {
                $stmt1 = $con->prepare("SELECT * FROM articles WHERE article_id = ?");
                $stmt1->execute(array($oneFav));
                $article = $stmt1->fetch();
                ?>
            <a href="?<?php echo $article['article_id'] ?>" class="text-decoration-none text-reset mb-3 d-block articleCard">
                <div class="card p-3" style="max-height: 200px; overflow:hidden">
                    <div class="row" style="height: 160px;">
                        <div class="col-8">
                            <h3><?php echo $article['title'] ?></h3>
                            <?php
$descLines = json_decode($article['description']);
                foreach ($descLines as $line) {?>
            <p style="max-height: 125px; overflow:hidden; font-size: 18px; margin-bottom:0;"><?php echo $line ?></p>
            <?php
}
                ?>
                    </div>
                    <div class="col-4 position-relative">
                        <img src="<?php echo 'layout/imgs//articles/' . $article['article_id'] . '/1.' . $article['ext'] ?>" alt="" class="img-fluid img-thumbnail d-block mx-auto" style="max-height: 180px;position: absolute;top: 50%;right:0;transform: translateY(-50%)">
                    </div>
                </div>
            </div>
        </a>
        <?php
}
        } else {?>
                <div class="alert text-center noFavYet mx-auto mt-5">No favourite articles yet</div>
                <?php }

    } elseif (preg_match($exp, $subject)) {
        // Show each article
        // Get main variables
        $stmt = $con->prepare("SELECT articles.*, users.fullname as fullname FROM articles INNER JOIN users ON users.userid = articles.user_id WHERE article_id = ?");
        $stmt->execute(array($subject));
        $data = $stmt->fetch();
        $elementsArray = json_decode($data['elementsArray']);
        $articleTitle = $data['title'];
        $name = $data['fullname'];
        $date = $data['date'];
        $subjectName = $data['subject'];
        // Set up article body
        $imgsCount = 1;
        $elements = "";
        $elementsArray = json_decode(json_encode($elementsArray), true);
        $elementsKeys = array_keys($elementsArray);
        foreach ($elementsKeys as $key) {
            if (str_contains($key, 'subtitle')) {
                $elements = $elements . '<h3 class="subtitle">' . $elementsArray[$key] . '</h3>';
            } elseif (str_contains($key, 'paragraph')) {
                foreach ($elementsArray[$key] as $line) {
                    $elements = $elements . '<p class="paragraph mb-0">' . $line . '</p>';
                }
            } elseif (str_contains($key, 'img')) {
                $imgExt = explode('/', $elementsArray[$key]);
                $elements = $elements . '<img src="layout/imgs/articles/' . $subject . '/' . $imgsCount . '.' . $imgExt[1] . '" alt="" class="mx-auto d-block img-thumbnail img-fluid my-3 articleImg border-2">';
                $imgsCount++;
            }
        }
        // Article Structure
        // Check for comments
        if (isset($_POST["comment"])) {
            // Get variables
            $userID = $_SESSION["id"];
            $commentInfo = explode("\r\n", trim(filter_var($_POST["comment"], FILTER_SANITIZE_STRING)));
            $commentInfo = json_encode($commentInfo);
            $dateNeeded = date("Y-m-j H-i-s");
            $urlInfo = explode("/", $_SERVER["REQUEST_URI"]);
            $lastOne = $urlInfo[count($urlInfo) - 1];
            $rate = $_POST["rate"];
            $needed_article_id = $subject;
            // Check for the user comment in the article (to prevent multiple comments)
            $stmt = $con->prepare("SELECT user_id FROM comments WHERE article_id = ?");
            $stmt->execute(array($needed_article_id));
            $commentsUsers = $stmt->fetchAll();
            $found = 0;
            foreach ($commentsUsers as $commentsUser) {
                if ($commentsUser["user_id"] == $userID) {
                    $found = 1;
                }
            }
            if ($found == 0) {
                $stmt1 = $con->prepare("INSERT INTO comments(user_id,article_id,comment,date,rate) VALUES(?,?,?,?,?)");
                $stmt1->execute(array($userID, $needed_article_id, $commentInfo, $dateNeeded, $rate));
            } else {
                header("location: #comment");
            }
        } elseif (isset($_POST["editComment"])) {
            $commentID = $_POST["comment_id"];
            $stmt = $con->prepare("SELECT * FROM comments WHERE comment_id = ?");
            $stmt->execute(array($commentID));
            $info = $stmt->fetch();
            $oldComment = $info["comment"];
            $oldRate = $info["rate"];
            // Check if edited comment is empty
            if (filter_var($_POST["editComment"], FILTER_SANITIZE_STRING) != "") {
                // Check if comment needs to be deleted
                if (filter_var($_POST["editComment"], FILTER_SANITIZE_STRING) != "DEL_COMMENT") {
                    $commentInfo = explode("\r\n", trim(filter_var($_POST["editComment"], FILTER_SANITIZE_STRING)));
                    $commentInfo = json_encode($commentInfo);
                    $rate = $_POST["rate"];
                    if ($oldComment != $commentInfo || $oldRate != $rate) { // Check if the comment is different or rating is different
                        $userID = $_SESSION["id"];
                        $dateNeeded = date("Y-m-j H-i-s");
                        $urlInfo = explode("/", $_SERVER["REQUEST_URI"]);
                        $lastOne = $urlInfo[count($urlInfo) - 1];
                        $needed_article_id = explode(".", $lastOne)[0];
                        $stmt1 = $con->prepare("UPDATE comments SET comment = ? , date = ?, rate = ? WHERE comment_id = ?");
                        $stmt1->execute(array($commentInfo, $dateNeeded, $rate, $commentID));
                    }
                }
            }
        } elseif (!isset($_POST["comment"]) && !isset($_POST["editComment"]) && isset($_POST["comment_id"])) {
            $commentID = $_POST["comment_id"];
            $stmt = $con->prepare("DELETE FROM comments WHERE comment_id = ?");
            $stmt->execute(array($commentID));
        }
        unset($_POST);
        ?>
        <div class="article">
            <div class="card mb-3">
                <div class="card-header">
                    <?php if (isset($_SESSION['fullname'])) {
            // Check if article is in favourites
            $stmt4 = $con->prepare("SELECT fav_articles FROM users WHERE userid = ?");
            $stmt4->execute(array($_SESSION['id']));
            $check = $stmt4->rowCount();
            $notFound = 0;
            if ($check > 0) {
                $info = $stmt4->fetch();
                $favArticles = json_decode($info['fav_articles']);
                if ($favArticles == []) {
                    $notFound = 1;
                } else {
                    foreach ($favArticles as $fav) {
                        if ($fav == $subject) {
                            $notFound = 0;
                            ?>
                            <i class="fa-solid fa-sun favIcon" data-article="<?php echo $subject ?>" data-user="<?php echo $_SESSION['id'] ?>"></i>
                            <?php } else {
                            $notFound = 1;
                        }
                    }
                }
                if ($notFound == 1) {?>
                    <i class="fa-regular fa-sun favIcon" data-article="<?php echo $subject ?>" data-user="<?php echo $_SESSION['id'] ?>"></i>
                    <?php }
            } else {?>
                <i class="fa-regular fa-sun favIcon" data-article="<?php echo $subject ?>" data-user="<?php echo $_SESSION['id'] ?>"></i>
                <?php }}?>
                <div class="row align-items-center">
                    <div class="col-lg-5 col-12">
                        <h1 class="article-title"><?php echo $articleTitle ?></h1>
                    </div>
                    <div class="col-lg-4 offset-lg-3 col-12">
                        <h4 class="madeBy mt-2">Mabe by: <?php echo $name ?></h4>
                        <h4 class="rating">Rating: <?php rating($subject)?></h4>
                        <h4 class="publishDate">Date: <?php echo date('j/m/Y', strtotime($date)) ?></h4>
                        <h4 class="subject">Subject: <?php echo $subjectName ?></h4>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <?php echo $elements ?>
            </div>
            <div class="card-footer d-flex justify-content-end flex-wrap">
                <span class="me-2">Made by: <?php echo $name ?></span>
                <div class="vr me-2"></div>
                <span class="me-2">Rating: <?php rating($subject)?></span>
                <div class="vr me-2"></div>
                <span>Article No: <?php echo $subject ?></span>
            </div>
        </div>
        <?php
if (isset($_SESSION['id'])) {
            // Show own comment
            ?>
                <form action="<?php echo $_SERVER['PHP_SELF'] . '?' . $subject ?>" class="mb-3" method="post">
                    <div class="card">
                        <div class="card-body">
                            <div class="form-floating mb-2">
                                <!-- TODO: Add max number of characters to description -->
                                <textarea class="form-control" name="comment" placeholder="Comment Here" id="commentArea" style="height: 100px; resize: none;" required></textarea>
                                <label for="commentArea">Comment</label>
                            </div>
                        </div>
                        <div class="card-footer d-flex justify-content-between align-items-center px-3">
                            <div class="rateField">
                                <span>Leave a rate: </span>
                                <i class="fa-regular fa-star rateChoice one"></i>
                                <i class="fa-regular fa-star rateChoice two"></i>
                                <i class="fa-regular fa-star rateChoice three"></i>
                                <i class="fa-regular fa-star rateChoice four"></i>
                                <i class="fa-regular fa-star rateChoice five"></i>
                                <input type="hidden" name="rate" class="rate" value="5">
                            </div>
                            <div>
                                <button type="reset" class="btn btn-outline-secondary me-2">Cancel</button>
                                <button type="submit" class="btn btn-outline-secondary">Submit</button>
                            </div>
                        </div>
                    </div>
                </form>
                <?php
} else {?>
            <div class="card mb-2">
                <a href="index.php" class="btn btn-outline-secondary">Login/Register</a>
            </div>
            <?php
}
        ?>
            <?php $id = (isset($_SESSION["id"])) ? $_SESSION["id"] : 0;
        commentsSection($subject, $id)?>
        </div>
        <?php } else {
        // Subject Part
        ?>
    <h1 class="text-center my-4"><?php echo $subject ?></h1>
    <?php
$stmt = $con->prepare("SELECT * FROM articles WHERE subject = ?");
        $stmt->execute(array($subject));
        $check = $stmt->rowCount();
        if ($check == 0) {?>
        <div class="alert noArticles text-center">There are no articles yet.</div>
        <?php } else {
            $articles = $stmt->fetchAll();
            foreach ($articles as $article) {
                ?>
<a href="?<?php echo $article['article_id'] ?>" class="text-decoration-none text-reset mb-3 d-block articleCard">
    <div class="card p-3" style="max-height: 200px; overflow:hidden">
        <div class="row" style="height: 160px;">
            <div class="col-8">
                <h3><?php echo $article['title'] ?></h3>
                <?php
$descLines = json_decode($article['description']);
                foreach ($descLines as $line) {?>
<p style="max-height: 125px; overflow:hidden; font-size: 18px; margin-bottom:0;"><?php echo $line ?></p>
<?php
}
                ?>
        </div>
        <div class="col-4 position-relative">
            <img src="layout/imgs/articles/<?php echo $article['article_id'] . '/1.' . $article['ext'] ?>" alt="" class="img-fluid img-thumbnail d-block mx-auto" style="max-height: 180px;position: absolute;top: 50%;right:0;transform: translateY(-50%)">
        </div>
    </div>
</div>
</a>
<?php }}
    }
}?>
</div>
<?php
include "includes/templates/footer.php";
