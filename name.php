<form action="name.php" method="post" enctype="multipart/form-data">
    <label>Select Image File:</label>
    <input type="file" name="image[]" multiple>
    <input type="text" name="dir" placeholder="direction ...">
    <input type="submit" name="submit" value="Upload">
</form>

<?php 
 
/* 
 * Custom function to compress image size and 
 * upload to the server using PHP 
 */ 

function compressImage($source, $destination, $quality) { 
    // Get image info 
    $imgInfo = getimagesize($source); 
    $mime = $imgInfo['mime']; 
     
    // Create a new image from file 
    switch($mime){ 
        case 'image/jpeg': 
            $image = imagecreatefromjpeg($source); 
            break; 
        case 'image/png': 
            $image = imagecreatefrompng($source); 
            break; 
        case 'image/gif': 
            $image = imagecreatefromgif($source); 
            break; 
        default: 
            $image = imagecreatefromjpeg($source); 
    } 
     
    // Save image 
    imagejpeg($image, $destination, $quality); 
     
    // Return compressed image 
    return $destination; 
} 
 
 
 
// If file upload form is submitted 
$status = $statusMsg = ''; 
$i=0;$count = count($_FILES['image']['name']);
if(isset($_POST["submit"])){ 
    // File upload path 
    $uploadPath = "uploads/".$_POST['dir'].'/'; 

    // die();
    $status = 'error'; 
    for($i ; $i < count($_FILES['image']['name']) ; $i++){
        
        if(!empty($_FILES["image"]["name"][$i])) { 
            // File info 
            $fileName = basename($_FILES["image"]["name"][$i]); 
            $imageUploadPath = $uploadPath . $fileName; 
            $fileType = pathinfo($imageUploadPath, PATHINFO_EXTENSION); 
            
            // Allow certain file formats 
            $allowTypes = array('jpg','png','jpeg','gif'); 
            if(in_array($fileType, $allowTypes)){ 
                // Image temp source 
                $imageTemp = $_FILES["image"]["tmp_name"][$i]; 
                
                // Compress size and upload image 
                $compressedImage = compressImage($imageTemp, $imageUploadPath, 4); 
                
                if($compressedImage){ 
                    $status = 'success'; 
                    $statusMsg = "Image compressed successfully."; 
                }else{ 
                    $statusMsg = "Image compress failed!"; 
                } 
            }else{ 
                $statusMsg = 'Sorry, only JPG, JPEG, PNG, & GIF files are allowed to upload.'; 
            } 
        }else{ 
            $statusMsg = 'Please select an image file to upload.'; 
        } 
    }
} 
 
// Display status message 
echo $statusMsg; 
 

// $directory = '';
// if(!empty($directory)){
//     $files = scandir($directory);
//     mkdir($directory."_2");
//     foreach($files as $img){
//         copy($directory.'/'.$img, $directory.'_2/ls_'.rand(11111,99999).'.jpg');
//     }
//     echo "<pre>";
//     print_r($files);
//     echo "</pre>";
// }else{
//     echo 'null directory';
// }

// $zip = new ZipArchive;
// $res = $zip->open("file.zip");
// if ($res === TRUE) {
//     $zip->extractTo("/myFolder/");
//     $zip->close();
//     echo "success !";
// } else {
//     echo "fail !";
// }