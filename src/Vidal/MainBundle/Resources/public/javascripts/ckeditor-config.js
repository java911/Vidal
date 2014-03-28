$(document).ready(function() {

	$('textarea.ckeditorfull').ckeditor(function() {}, {
			filebrowserBrowseUrl:      '/bundles/vidalmain/kcfinder/browse.php?type=files',
			filebrowserImageBrowseUrl: '/bundles/vidalmain/kcfinder/browse.php?type=files',
			filebrowserFlashBrowseUrl: '/bundles/vidalmain/kcfinder/browse.php?type=flash',
			filebrowserUploadUrl:      '/bundles/vidalmain/kcfinder/upload.php?type=files',
			filebrowserImageUploadUrl: '/bundles/vidalmain/kcfinder/upload.php?type=images',
			filebrowserFlashUploadUrl: '/bundles/vidalmain/kcfinder/upload.php?type=flash'
		}
	);

});