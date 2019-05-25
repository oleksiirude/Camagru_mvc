<div class="wrapper">
	<div class="menu">
		<span class="title">change avatar</span><br>
		<b>png or jpg image up to 5mb size</b>
		<form id="form" class="upload_avatar" enctype="multipart/form-data" onsubmit="ajax_img(event, this, 'user/change/avatar/set')">
			<label for="change_avatar">choose pic</label>
			<input class="input_zone" type="file" accept=".png, .jpg, .jpeg" name="avatar" id="change_avatar" onchange="getAvatarPreview()"><br>
			<div id="avatar_preview" class="avatar_preview"></div>
			<button id="change_avatar_button" type="submit" class="submit_button" style="display: none">change</button>
		</form>
		<?php
			if ($_SESSION['avatar'])
				echo "<a href='user/change/avatar/delete'>delete avatar</a><br>";
		?>
		<a href="user/settings">back</a>
	</div>
</div>