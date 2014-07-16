<?php
		$image 		 = $_FILES['imageField']['name'];
		$imagename 	 = explode(".",$image);
		$image2 	 = $imagename[0];
		$imageext    = $imagename[1];
		$filename = $image2.".".$imageext;
		if($image!='')
		{
			move_uploaded_file($_FILES['imageField']['tmp_name'],"../categoryimage/$filename");
		}
?>