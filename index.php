<?php
if ($_POST["userId"]) {
	$publicId = "TESTAPIKEY";
	$magic = "XSDE422RSDJQDJW8QADM31SMA";

	$ip = $_SERVER["REMOTE_ADDR"];
	$timestamp = time();

	$hash = md5($publicId . $ip . $timestamp . $magic);

	$url = "http://127.0.0.1:3001/api/token";

	$data = array(
				"id"	=> $publicId,
				"uid"	=> $_POST["userId"],
				"ip"	=> $ip,
				"ts"	=> $timestamp,
				"hash"	=> $hash
	);

	// use key 'http' even if you send the request to https://...
	$options = array(
	    'http' => array(
	        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
	        'method'  => 'POST',
	        'content' => http_build_query($data),
	    ),
	);

	$context  = stream_context_create($options);
	$result = file_get_contents($url, false, $context);

	print($result);
} else {
?>
<html>
	<head>
		<title>EDMdesigner-API-Example-PHP-Admin</title>
		<script src="http://code.jquery.com/jquery-2.0.3.min.js"></script>
		<script src="//127.0.0.1:3001/EDMdesignerAPI.js?route=index.php"></script>
		<script>
			initEDMdesignerPlugin("admin", function(edmPlugin) {

				var counter = 0;
				function updateGroupList() {
					$("#NewGroup").hide();
					$("#UpdateGroup").hide();
					$("#GroupList").show();

					var groupListContainer = $("#GroupListContent").empty();

					edmPlugin.listGroups(function(result) {
						for(var idx = 0; idx < result.length; idx += 1) {
							groupListContainer.append(createGroupListElem(result[idx]));
						}
					});
				}

				function createGroupListElem(data) {
					var elem = $("<div class='group-list-elem'/>");
					var name = $("<div class='info'/>").appendTo(elem);
					var buttons = $("<div class='buttons'/>").appendTo(elem);

					name.append($("<h3/>").text(data.name));

					var openButton = $("<button/>")
						.text("Show more")
						.click(function(event) {
							var button = $(event.delegateTarget);
							var text  = button.text();
							if(text === "Show more") {
								edmPlugin.readGroup(data._id, function (result) {
									var fs = result.featureSwitch || {};
									$("<p />").text(fs).appendTo(name);
									button.text("Show less");
								});
							} else {
								var p = name.find("p");
								p.remove();
								button.text("Show more");
							}
						})
						.appendTo(buttons);

					var EditProjectInfoButton = $("<button>")
						.text("Update group")
						.click(function() {
							$("#ProjectStuff").hide();
							$("#GroupList").hide();
							var groupUpdate = $("#UpdateGroup");
							groupUpdate.show();

							var nameInput = $("#GroupNameInput");
							var featureInput = $("#GroupFeatureTextarea");
							nameInput.val(data.name);
							featureInput.val(data.featureSwitch);

							$("#GroupUpdateOk").off("click");
							$("#GroupUpdateCancel").off("click");

							$("#GroupUpdateOk").click(function() {
								console.log(counter);
								counter++;
								var name = nameInput.val();
								var feature = featureInput.val();

								nameInput.val("");
								featureInput.val("");

								groupUpdate.hide();
								edmPlugin.updateGroup(data._id, {name: name, featureSwitch: feature}, function(result) {
									updateGroupList();
								});
							});

							$("#GroupUpdateCancel").click(function() {
								updateGroupList();
							});
						})
						.appendTo(buttons);

					return elem;
				}

				$("#NewGroupButton").click(function() {
					$("#GroupList").hide();
					$("#NewGroup").show();
				});

				$("#NewGroupAddButton").click(function() {
					var nameInput = $("#NewGroupName"),
						featureInput = $("#NewGroupFeatures");

					var data = {
						name: nameInput.val(),
						featureSwitch: featureInput.val()
					};

					if(data.title !== "") {
						if(data.featureSwitch === "") {
							data.featureSwitch = {};
						}
						nameInput.val("");
						featureInput.val("{}");

						edmPlugin.createGroup(data, updateGroupList);
					}
				});


				$(document).ready(updateGroupList);
			}, function(error) {
				alert(error);
			});
		</script>
	</head>
	<body>
		<div>
			<h1>EDMdesigner-API-Example-PHP</h1>
		</div>

		<div id="GroupList">

			<button id="NewGroupButton">New group</button>
			
			<h2>Groups</h2>

			<div id="GroupListContent">
			</div>
			
		</div>

		<div id="NewGroup">
			<h2>New Group</h2>
			<h3>name</h3>
			<input id="NewGroupName"/>
			<h3>Feature list</h3>
			<textarea id="NewGroupFeatures" placeholder="{}"></textarea>
			<div>
				<button id="NewGroupAddButton">Add</button>
			</div>
		</div>

		<div id="UpdateGroup">
			<h2>Update Group</h2>
			<div>
				<input id="GroupNameInput" />
			</div>
			<textarea id="GroupFeatureTextarea" placeholder="{}"></textarea>
			<div>
				<button id="GroupUpdateOk">Ok</button>
				<button id="GroupUpdateCancel">Cancel</button>
			</div>
		</div>

	</body>
</html>

<?php
}
?>