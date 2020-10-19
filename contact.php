<?php require_once("init.php"); ?>
<!DOCTYPE html>
<html lang="<?php echo $lang ?>">
    <head>
        <?php 
            $headTitle = ${'contact_' . $lang};
            require_once("head.php"); 
        ?>
    </head>
    <body>
        <div class="container mx-auto">
        
            <?php require_once("banner.php"); ?>
            
            <div class="row">
            </div>
    
            <?php require_once("footer.php"); ?>
            
        </div>
    
        <?php require_once("finish.php"); ?>
        
    </body>
</html>