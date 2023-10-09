<?php
session_start();
include "init.php";

?>
<div style="margin-top:100px"></div>
<div class="container mainArticle">
    <!-- Main articles page -->
    <?php if (empty($_GET)) {?>
        <h2 class="mb-3">Hot Articles</h2>
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
<a href="<?php echo 'articles/' . $article['article_id'] . '/' . $article['article_id'] . '.php' ?>">
<img src="articles/<?php echo $article['article_id'] . '/imgs' . '/1.' . $article['ext']; ?>" class="d-block position-absolute mx-auto" style="height: 110%; width: 110%; object-fit: cover; right: 50%; top: 50%; transform: translate(50%, -50%);filter: brightness(75%);">
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
        <h2 class="mt-3 mb-3">Newest Articles</h2>
        <div class="card p-3 mb-3">
            <div class="row">
                <?php
$stmt = $con->prepare("SELECT * FROM articles ORDER BY date DESC LIMIT 6");
    $stmt->execute();
    $rows = $stmt->fetchAll();
    foreach ($rows as $row) {
        $urlArticle = 'articles/' . $row['article_id'] . '/' . $row['article_id'] . '.php';
        $urlImg = 'articles/' . $row['article_id'] . '/imgs' . '/1.' . $row['ext'];
        ?>
                    <div class="col-12 col-md-6">
                        <a href="<?php echo $urlArticle ?>" class="mb-3 d-block position-relative">
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
        <h2 class="mt-3 mb-3">Subjects</h2>
        <div class="card mb-4 px-2 pb-2 g-2 row justify-content-center flex-row">
            <?php
foreach ($subjects as $subject) {?>
                <div class="col-lg-2 col-md-3 col-sm-4 col-6 p-1"><a href="?<?php echo $subject ?>" class="text-decoration-none text-reset btn btn-outline-secondary text-center w-100"><?php echo $subject ?></a></div>
                <?php }?>
            </div>
<?php } else {
    $subject = array_keys($_GET)[0];
    ?>
    <!-- Subject Part -->
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
<a href="articles/<?php echo $article['article_id'] . '/' . $article['article_id'] . '.php' ?>" class="text-decoration-none text-reset mb-3 d-block articleCard">
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
            <img src="articles/<?php echo $article['article_id'] . '/imgs' . '/1.' . $article['ext'] ?>" alt="" class="img-fluid img-thumbnail d-block mx-auto" style="max-height: 180px;position: absolute;top: 50%;right:0;transform: translateY(-50%)">
        </div>
    </div>
</div>
</a>
    <?php }}}?>
</div>
<?php
include "includes/templates/footer.php";
