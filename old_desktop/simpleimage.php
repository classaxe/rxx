<?php 
Header("Content-type: image/gif"); 
//create a new image 
$image = ImageCreate(200,200); 
//create white (for background ) 
$white = ImageColorAllocate($image ,255,255,255); 
//create blue for text 
$blue = ImageColorAllocate($image , 0,0,255); 
//OK lets create our white background 
ImageFilledRectangle($image ,0,0,200,200,$white); 
//display some text 
ImageString($image,10,15,10,'iain@iain.com',$blue); 
//output image to browser as a JPEG 
ImageGIF($image); 
//clean up by destroying the image 
ImageDestroy($image); 
?>
