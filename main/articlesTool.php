<?php
session_start();
include "init.php";
?>
<div style="margin-top:90px"></div>
<?php
if (isset($_POST)) {
    if (!empty($_POST)) {
        // Make each line an element in an array for the description and paragraphs (as they are textareas)
        $_POST['description'] = explode("\r\n", trim($_POST['description']));
        $keys = array_keys($_POST);
        foreach ($keys as $key) {
            if (str_contains($key, 'paragraph')) {
                $_POST[$key] = explode("\r\n", trim($_POST[$key]));
            }
        }
        // Arrange and save elemetns of the article body
        $elementsArray = [];
        $imgNo = 0;
        $theKeys = array_keys($_POST);
        for ($i = 3; $i < count($_POST); $i++) {
            $neededKey = $theKeys[$i];
            if (!str_contains($neededKey, 'img')) {
                $elementsArray[$neededKey] = $_POST[$neededKey];
            } else {
                $ext = explode("/", $_FILES['image-' . $imgNo]['type']);
                $elementsArray[$neededKey] = $_POST[$neededKey] . '/' . $ext[1];
            }
        }
        // Creating the folders and the files
        // First check the latest article number on DB
        $stmt = $con->prepare("SELECT article_id FROM articles ORDER BY article_id DESC LIMIT 1");
        $stmt->execute();
        $articlesNo = $stmt->fetch();
        $articleNo = (!empty($articlesNo)) ? intval($articlesNo['article_id']) + 1 : '1';
        $targetedFile;
        if (!is_dir('articles/' . $articleNo)) {
            mkdir('articles/' . $articleNo, 0777, true);
            mkdir('articles/' . $articleNo . '/' . 'imgs/', 0777, true);
            if (!file_exists('articles/' . $articleNo . '/' . $articleNo . '.php')) {
                $targetedFile = fopen('articles/' . $articleNo . '/' . $articleNo . '.php', 'w');
            }
            // Renaming images and uploading them to imgs folder of the article
            $imgNo = 01;
            $imageKeys = array_keys($_FILES);
            foreach ($imageKeys as $imgKey) {
                if (str_contains($imgKey, 'image')) {
                    $imgExt = explode("/", $_FILES[$imgKey]['type']);
                    $_FILES[$imgKey]['name'] = $imgNo . '.' . $imgExt[1];
                    if (!file_exists('articles/' . $articleNo . '/' . 'imgs/' . $_FILES[$imgKey]['name'])) {
                        move_uploaded_file($_FILES[$imgKey]['tmp_name'], 'articles/' . $articleNo . '/' . 'imgs/' . $_FILES[$imgKey]['name']);
                    }
                    $imgNo++;
                }
            }
            // Submit info into function to write the file
            $fileStructure = createArticle($_POST["articleTitle"], $_SESSION["fullname"], date("j/m/Y"), $_POST["subject"], $elementsArray, $articleNo);
            fwrite($targetedFile, $fileStructure);
            fclose($targetedFile);
        } else {
            $articleNo = 0;
        }
        // Submit info into DB
        if ($articleNo != 0) {
            $thumbnailext = explode('/', $_FILES['image-0']['type'])[1];
            $stmt = $con->prepare("INSERT INTO articles(subject,date,rating,status,title,description,ext) VALUES(?,now(),?,?,?,?,?)");
            $stmt->execute(array($_POST['subject'], null, 0, $_POST['articleTitle'], json_encode($_POST['description']), $thumbnailext));
        }
    }
    // Reset the storages
    $_POST = "";
    $_FILES = "";
}

?>
<div class="container article">
    <form action="<?php echo $_SERVER['PHP_SELF'] ?>" class="articleForm" method="post" enctype='multipart/form-data'>
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-lg-5 col-12">
                        <h1 class="articleTitle">Article Title Here</h1>
                        <div class="input-group articleTitleGroup">
                            <textarea name="articleTitle" cols="30" rows="3" class="form-control" style="resize: none;" required></textarea>
                            <button type="button" class="btn btn-outline-secondary articleTitleDone">Done</button>
                        </div>
                    </div>
                    <div class="col-lg-4 offset-lg-3 col-12">
                        <h4 class="madeBy">Mabe by: <?php echo $_SESSION['fullname'] ?></h4>
                        <h4 class="rating">Rating: <i class="fa-regular fa-star"></i><i class="fa-regular fa-star"></i><i class="fa-regular fa-star"></i><i class="fa-regular fa-star"></i><i class="fa-regular fa-star"></i></h4>
                        <h4 class="publishDate">Date: <?php echo date('j/m/Y') ?></h4>
                        <div class="input-group">
                            <label for="subject" class="input-group-text">Subject</label>
                            <select name="subject" id="subject" class="form-select" required>
                                <option value="">Choose a subject</option>
                                <?php
foreach ($subjects as $subject) {?>
                                        <option value="<?php echo $subject ?>"><?php echo $subject ?></option>
                                    <?php }?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                    <div class="form-floating mb-3">
                        <!-- TODO: Add max number of characters to description -->
                        <textarea class="form-control" name="description" placeholder="Comment Here" id="commentArea" style="height: 150px; resize: none;" required></textarea>
                        <label for="commentArea">Description</label>
                    </div>
            <div class="articleTools d-flex flex-wrap justify-content-around mx-auto rounded-2 border py-3 position-sticky mb-3">
                <button type="button" class="btn btn-outline-secondary subtitleBtn">Add subtitle</button>
                <button type="button" class="btn btn-outline-secondary pBtn">Add paragraph</button>
                <button type="button" class="btn btn-outline-secondary imgBtn">Add image</button>
                <div class="vr"></div>
                <button type="submit" class="btn btn-outline-secondary">Submit Article</button>
            </div>
            <div class="mb-3">
                <h3 class="subtitle">Subtitle Here</h3>
                <div class="input-group subtitleChange">
                    <input type="text" name="subtitle-0" class="form-control" required>
                    <button type="button" class="btn btn-outline-secondary subtitleDone">Done</button>
                </div>
            </div>
            <div class="mb-3">
                <p class="paragraph" style="white-space:pre">Paragraph Here</p>
                <div class="input-group pChange">
                    <textarea name="paragraph-0" id="" cols="30" rows="10" class="form-control" style="resize: none;" required></textarea>
                    <button type="button" class="btn btn-outline-secondary pDone">Done</button>
                </div>
            </div>
            <div class="mb-3">
                <input type="hidden" name="img-0" value="1">
                <img src="https://placehold.co/500x350" alt="" class="mx-auto d-block img-thumbnail img-fluid mb-2 articleImg">
                    <div class="input-group justify-content-center">
                        <input type="file" name="image-0" class="btn btn-outline-secondary imgInput border-2" accept=".png, .jpg" value="Choose Image" required>
                    </div>
            </div>
            </div>
        </div>
    </form>
</div>
<?php
include "includes/templates/footer.php";
