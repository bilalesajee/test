<?php
		echo $image 		 = $_FILES['image']['name'];
		$imagename 	 = explode(".",$image);
		$image2 	 = $imagename[0];
		$imageext    = $imagename[1];
		$filename = $image2.".".$imageext;
		if($image!='')
		{
			if(move_uploaded_file($_FILES['image']['tmp_name'],"../productimage/$filename"))
			{
				print"$image";	
			}
			if($oldimage!='')
			{
				@unlink('../productimage/'.$oldimage);
			}
		}
?>