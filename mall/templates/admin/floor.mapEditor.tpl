<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "//www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>{$element->title}</title>

		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

		<script src="{$controller->baseURL}libs/ckeditor/ckeditor.js"></script>
		<script src="{$controller->baseURL}libs/ckfinder/ckfinder.js"></script>
		<script type="text/javascript" src="{$controller->baseURL}javascript/set:{$theme->getCode()}/file:{$JSFileName}.js"></script>
		<link rel="shortcut icon" href="{$theme->getImageUrl("icons/favicon.ico")}"/>
		<link rel="stylesheet" type="text/css" href="{$controller->baseURL}css/set:{$theme->getCode()}/file:{$CSSFileName}.css"/>
	</head>
	<body class="floor_mapeditor">
		{*<div class="">*}
			<script>

				window.ajaxURL = '{$controller->baseURL}adminAjax/';
				window.elementId = '{$element->id}';
				window.editorInfo = {$element->getEditorInfo()|json_encode};

			</script>
			<div class='floor_mapeditor_center_block'>
				<div class='floor_mapeditor_map'>
				</div>
			</div>

			<div class='floor_mapeditor_panel'>
				<div class="floor_mapeditor_panel_content">
					<div class="floor_mapeditor_panel_tabs">
						<div class="floor_mapeditor_panel_tabs_buttons tabs_list">
							<a class="floor_mapeditor_panel_tabs_button tabs_list_item" href="#">Areas</a>
							<a class="floor_mapeditor_panel_tabs_button tabs_list_item" href="#">Icons</a>
						</div>
						<div class="floor_mapeditor_panel_tabs_content tabs_content">
							<div class="tabs_content_item">
								<div class="form_fields">
									<div class="form_items">
										<span class="form_label">

										</span>
										<div class="form_field">
											<select class='floor_mapeditor_panel_precision_room_selector dropdown_placeholder' autocomplete='off'></select>
										</div>
									</div>
									<div class="form_items">
										<span class="form_label">
											Precision
										</span>
										<div class="form_field">
											<input class="floor_mapeditor_panel_precision input_component" value="0"/>
										</div>
									</div>
								</div>
							</div>
							<div class="tabs_content_item">
								<div class="form_fields">
									<div class="form_items">
										<span class="form_label">

										</span>
										<div class="form_field">
											<select class='icon_selector dropdown_placeholder' autocomplete='off'></select>
										</div>
									</div>
									<div class="form_items">
										<span class="form_label">
											Width
										</span>
										<div class="form_field">
											<input class="floor_mapeditor_panel_icon_width input_component" type="text">
										</div>
									</div>
									<div class="form_items">
										<span class="form_label">
											Height
										</span>
										<div class="form_field">
											<input class="floor_mapeditor_panel_icon_height input_component" type="text">
										</div>
									</div>
									<div class="form_items">
										<span class="form_label">
											Angle
										</span>
										<div class="form_field">
											<input class="floor_mapeditor_panel_icon_angle input_component" type="text">
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="floor_mapeditor_panel_editcontrols">

						<input class='floor_mapeditor_panel_button button edit_button' type='button' value='Create'
						size='7'
						disabled='disabled'/>

						<input class='floor_mapeditor_panel_button button cancel_button' type='button' value='Cancel'
						size='7'
						disabled='disabled'/>

						<input class='floor_mapeditor_panel_button button delete_button' type='button' value='Delete'
						size='7'
						disabled='disabled'/>
					</div>

					<div class='floor_mapeditor_panel_undocontrols'>

						<input class='floor_mapeditor_panel_button button undo_button' type='button' value='Undo'
						size='3'
						disabled='disabled'/>

						<input class='floor_mapeditor_panel_button button redo_button' type='button' value='Redo'
						size='3'
						disabled='disabled'/>
					</div>
				</div>
			</div>
	</body>
</html>