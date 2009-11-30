<!--
This file contains the navigational menu entries for actions,
like creating assets and services, adding goods to the inventory, etc.

It is included from the main skeleton in each file. 
-->
			<h3><?php echo translate("uws:actions") ?></h3>
			<div class="content">
				<ul class="linklist">
					<li class="first"><a href="uploadStory.php"><?php echo translate("uws:chat_upload") ?></a></li>
					<!-- admin users should be able to manage users here? -->
					<li><a href="createEntry.php"><?php echo translate("uws:create_entry") ?></a></li>
					<li><a href="createService.php"><?php echo translate("uws:create_service") ?></a></li>
					<li><a href="createAsset.php"><?php echo translate("uws:create_asset") ?></a></li>
					<li><a href="addToInventory.php"><?php echo translate("uws:add_inventory") ?></a></li>
				</ul>
			</div>
