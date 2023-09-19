<nav class="navbar fixed-top navbar-expand-lg bg-body-tertiary py-3">
    <div class="container">
        <a class="navbar-brand" href="index.php"><i class="fa-brands fa-pied-piper fa-xl text-light"></i></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="border-light"><i class="fa-solid fa-bars fa-lg text-light"></i></span>
        </button>
    <div class="collapse navbar-collapse justify-content-end" id="navbarSupportedContent">
        <form class="d-flex mx-auto" role="search">
            <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
            <button class="btn btn-outline-light" type="submit">Search</button>
        </form>
        <ul class="navbar-nav mb-2 mb-lg-0">
            <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="articles.php">Articles</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Questions</a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <?php
if (isset($_SESSION['username'])) {
    echo $_SESSION['username'];
    echo '</a>';
    echo '<ul class="dropdown-menu">';
    echo '<li><a class="dropdown-item" href="#">Profile</a></li>';
    echo '<li><a class="dropdown-item" href="#">Settings</a></li>';
    echo '<li><a class="dropdown-item" href="#">Help & Support</a></li>';
    echo '<li><hr class="dropdown-divider"></li>';
    echo '<li><a class="dropdown-item" href="includes/functions/logout.php">Log out</a></li>';
    echo '</ul>';
} else {
    echo 'Settings';
    echo '</a>';
    echo '<ul class="dropdown-menu">';
    echo '<li><a class="dropdown-item" href="#">Settings</a></li>';
    echo '<li><a class="dropdown-item" href="#">Help & Support</a></li>';
    echo '<li><hr class="dropdown-divider"></li>';
    echo '<li><a class="dropdown-item" href="index.php">Login/Signup</a></li>';
    echo '</ul>';
}
?>
            </li>
        </ul>
    </div>
</div>
</nav>
