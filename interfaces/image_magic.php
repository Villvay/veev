<?php

	function thumbnail($image, $size){
		if (is_numeric($size))
			$size = array("height" => $size, "width" => $size);
		else
			$size = array("height" => 64, "width" => 64);
		$original_width = imageSX($image);
		$original_height = imageSY($image);
		if (($original_width / $original_height) > ($size["width"] / $size["height"])){	// crop from either sides
			$rect_width = $original_height * ($size["width"] / $size["height"]);
			return crop($image, array("left" => ($original_width - $rect_width) / 2, "top" => 0, "width" => $rect_width, "height" => $original_height), array("width" => $size["width"], "height" => $size["height"]));
		}
		else{													// crop from bottom
			$rect_height = $original_width * ($size["height"] / $size["width"]);
			return crop($image, array("left" => 0, "top" => 0, "width" => $original_width, "height" => $rect_height), array("width" => $size["width"], "height" => $size["height"]));
		}
	}

	function resize($image, $width = false, $height = false){
		$original_width = imageSX($image);
		$original_height = imageSY($image);
		// ---------------------------------------------------------------------
		if ($height != false && is_numeric($height))
			$size["height"] = $height;
		else
			$size["height"] = $width * $original_height / $original_width;
		// ---------------------------------------------------------------------
		if ($width != false && is_numeric($width))
			$size["width"] = $width;
		else
			$size["width"] = $height * $original_width / $original_height;
		// ---------------------------------------------------------------------
		if (($original_width / $original_height) > ($size["width"] / $size["height"])){	// crop from either sides
			$rect_width = $original_height * ($size["width"] / $size["height"]);
			return crop($image, array("left" => ($original_width - $rect_width) / 2, "top" => 0, "width" => $rect_width, "height" => $original_height), array("width" => $size["width"], "height" => $size["height"]));
		}
		else{													// crop from bottom
			$rect_height = $original_width * ($size["height"] / $size["width"]);
			return crop($image, array("left" => 0, "top" => 0, "width" => $original_width, "height" => $rect_height), array("width" => $size["width"], "height" => $size["height"]));
		}
	}

	function crop($image, $rect, $new_size = false){
		if (!$new_size)
			$new_size = array("width" => $rect["width"], "height" => $rect["height"]);
		$dst_img = imagecreatetruecolor($new_size["width"], $new_size["height"]);
		imagecopyresampled($dst_img, $image, 0, 0, $rect["left"], $rect["top"], $new_size["width"], $new_size["height"], $rect["width"], $rect["height"]);
		return $dst_img;
	}

	function load_image($file){
		$ext = substr($file, strrpos($file, ".") + 1);
		$ext = strtolower($ext);
		$src_img = false;
		if ($ext == "jpg" || $ext == "jpeg")
			$src_img = imagecreatefromjpeg($file);
		if ($ext == "png")
			$src_img = imagecreatefrompng($file);
		if ($ext == "gif")
			$src_img = imagecreatefromgif($file);
		if ($ext == "bmp")
			$src_img = imagecreatefrombmp($file);
		return $src_img;
	}

	function save_image($image, $file, $ext = false){
		if ($ext == false){
			$ext = substr($file, strrpos($file, ".") + 1);
			$ext = strtolower($ext);
		}
		imageinterlace($image, true);
		if ($ext == "jpg" || $ext == "jpeg")
			imagejpeg($image, $file);
		if ($ext == "png")
			imagepng($image, $file);
		if ($ext == "bmp")
			imagebmp($image, $file);
		if ($ext == "gif")
			imagegif($image, $file);
		return true;
	}

?>