<?php
session_start();

if ($_POST) {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    if ($user === "Brain Samaj" && $pass === "S@ngoyom1") {
        $_SESSION['admin'] = true;
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Invalid login";
    }
}
?>

<h2>Admin Login</h2>

<?php if (isset($error)) echo $error; ?>

<form method="POST">
    <input name="username" placeholder="Username"><br><br>
    <input name="password" type="password" placeholder="Password"><br><br>
    <button type="submit">Login</button>
</form>
