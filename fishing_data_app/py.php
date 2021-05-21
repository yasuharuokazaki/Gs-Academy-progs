<?php
$path=mb_substr(__FILE__,0,-6);
exec("python test.py",$output,$state);

print("test");
print($output);

?>