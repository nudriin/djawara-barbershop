<?php
namespace Nurdin\Djawara\App;

class ViewRender
{
    public static function render(string $view, $model) : void
    {
        require __DIR__ . "/../View/header.php";
        require __DIR__ . "/../View/" . $view . ".php";
        require __DIR__ . "/../View/footer.php";
    }
    
    public static function adminRender(string $view, $model) : void
    {
        require __DIR__ . "/../View/admin-header.php";
        require __DIR__ . "/../View/" . $view . ".php";
        require __DIR__ . "/../View/admin-footer.php";
    }

    public static function userRender(string $view, $model) : void
    {
        require __DIR__ . "/../View/user-header.php";
        require __DIR__ . "/../View/" . $view . ".php";
        require __DIR__ . "/../View/admin-footer.php";
    }

    public static function redirect(string $url)
    {
        header("Location: $url");
        exit();
    }
}

?>