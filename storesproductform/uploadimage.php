<?php

if (isset($_FILES['file']['name'])) {
    if (0 < $_FILES['file']['error']) {
        echo 'ERROR during file upload' . $_FILES['file']['error'];
    } else {
        if (file_exists('uploads/' . $_FILES['file']['name'])) {
            echo 'Image <a target="_blank" href="http://www.webprojects55.co.uk/storesproductform/uploads/' . $_FILES['file']['name'] . '">already added</a>';
        } else {
            move_uploaded_file($_FILES['file']['tmp_name'], 'uploads/' . $_FILES['file']['name']);
			$newfilename = $_FILES['file']['name'];
			echo '<img width=60" height="60" src="http://www.webprojects55.co.uk/storesproductform/uploads/' . $newfilename . '">';
			}
    }
} else {
    echo 'ERROR: Please choose a file';
}
    
/* 
 * End of script
 */