
<?php
passthru('sudo /sbin/shutdown -h');
?>
<h1>System will go down in 1 minute.</h1>
<script>
   setTimeout(function(){
        window.location.href = "index.php";
    }, 1000);
</script>
