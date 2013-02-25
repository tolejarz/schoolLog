<?php
class FileManipulation {
	function delete($filename) {
		if (!empty($filename)) {
			if (is_file($filename)) {
				unlink($filename);
			}
		}
	}
	
	function send($fileTitle, $fileVar, $directory) {
		$filename = $this->format($fileTitle, $_FILES[$fileVar]['name']);
		move_uploaded_file($_FILES[$fileVar]['tmp_name'], $directory . $filename);
		return $filename;
	}
	
	function sanitize($s) {
		$r = strip_tags($s);
		$r = strtolower(stripslashes($r));
		$r = eregi_replace("[אבגדהוְֱֲֳִֵ]", "a", $r);
		$r = eregi_replace("[()\"°]", "", $r);
		$r = eregi_replace("[ָֹÊֻטיךכ]", "e", $r);
		$r = eregi_replace("[ַח]", "c", $r);
		$r = eregi_replace("[ּֽ־ֿלםמן]", "i", $r);
		$r = eregi_replace("[ׂ׃װױײעףפץצ]", "o", $r);
		$r = eregi_replace("[ÙÚÛÜשתûü]", "u", $r);
		$r = eregi_replace("[ÿ]", "y", $r);
		$r = eregi_replace("[^a-zA-Z0-9. ]", "", $r);
		$r = trim($r);
		$r = eregi_replace(" ", "_", $r);
		$r = eregi_replace("[_]{2,}", "_", $r);
		return $r;
	}
	
	function format($fileTitle, $fileOrigin) {
		$ext = explode('.', $fileOrigin);
		$date = getdate();
		$filename =  $date['year'] . str_pad($date["mon"], 2, '0', STR_PAD_LEFT) . str_pad($date['mday'], 2, '0', STR_PAD_LEFT) . ' ' . str_pad($date['hours'], 2, '0', STR_PAD_LEFT) . str_pad($date['minutes'], 2, '0', STR_PAD_LEFT) . str_pad($date['seconds'], 2, '0', STR_PAD_LEFT) . ' ' . $fileTitle . '.' . $ext[count($ext) - 1];
		$filename = $this->sanitize($filename);
		return $filename;
	}
}
?>