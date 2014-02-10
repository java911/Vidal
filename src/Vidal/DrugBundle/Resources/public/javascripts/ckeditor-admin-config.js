$(document).ready(function() {

	CKEDITOR.config.resize_dir = 'vertical';

	CKEDITOR.config.toolbar_Ver =
		[
			{ name: 'clipboard', items: [ 'Undo', 'Redo' ] },
			{ name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat', '-', "JustifyCenter", "JustifyRight", "JustifyBlock" ] },
			{ name: 'paragraph', items: [ "NumberedList", "BulletedList"]},

			//{ name: 'paragraph', "groups": [ "list", "indent", "blocks", "align" ], items : [ 'NumberedList','BulletedList','align' ] },
			{ name: 'links', items: [ 'Link', 'Unlink' ] },
			{ name: 'insert', items: [ 'Image', 'HorizontalRule', 'Table' ] },
			//{ name: 'colors', items : [ 'TextColor' ] },
			{ name: 'document', items: [ 'Source' ] }
		];
	CKEDITOR.config.toolbar_Trial =
		[
			{ name: 'clipboard', items: [ 'Undo', 'Redo' ] },
			{ name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat', '-', "JustifyCenter", "JustifyRight", "JustifyBlock" ] },
			{ name: 'paragraph', items: [ "NumberedList", "BulletedList"]},

			//{ name: 'paragraph', "groups": [ "list", "indent", "blocks", "align" ], items : [ 'NumberedList','BulletedList','align' ] },
			{ name: 'links', items: [ 'Link', 'Unlink' ] },
			{ name: 'insert', items: [ 'HorizontalRule', 'Table' ] },
			//{ name: 'colors', items : [ 'TextColor' ] },
			{ name: 'document', items: [ 'Source' ] }
		];

	var a = CKEDITOR.config.toolbar = 'Ver';
	$('textarea').ckeditor(function() {}, {
			extraPlugins:              'justify',
			customConfig:              '',
			toolBar:                   '',
            width:                      800,
			filebrowserBrowseUrl:      '/bundles/vidalmain/kcfinder/browse.php?type=files',
			filebrowserImageBrowseUrl: '/bundles/vidalmain/kcfinder/browse.php?type=files',
			filebrowserFlashBrowseUrl: '/bundles/vidalmain/kcfinder/browse.php?type=flash',
			filebrowserUploadUrl:      '/bundles/vidalmain/kcfinder/upload.php?type=files',
			filebrowserImageUploadUrl: '/bundles/vidalmain/kcfinder/upload.php?type=images',
			filebrowserFlashUploadUrl: '/bundles/vidalmain/kcfinder/upload.php?type=flash'
		}
	);

	//var b = CKEDITOR.config.toolbar = 'Trial';
	$('.ckeditortrial').ckeditor(function() {
			$('.cke_button__image').eq(0).remove(); //It's very bad
		}, {
			extraPlugins: 'justify',
			customConfig: '',
			toolBar:      'trial'
		}
	);

});