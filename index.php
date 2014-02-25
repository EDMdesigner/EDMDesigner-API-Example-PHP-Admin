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

				//GROUPS
				var groupList = [];

				function updateGroupList() {
					$("#NewGroup").hide();
					$("#UpdateGroup").hide();
					$("#UserStuff").show();
					$("#GroupList").show();

					var groupListContainer = $("#GroupListContent").empty();

					edmPlugin.listGroups(function(result) {
						groupList = result;
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
									var fs = result.featureSwitch || "{}";
									$("<p />").html("id: " + result._id + "<br> features: " + fs).appendTo(name);
									button.text("Show less");
								});
							} else {
								var p = name.find("p");
								p.remove();
								button.text("Show more");
							}
						})
						.appendTo(buttons);

					var EditGroupInfoButton = $("<button>")
						.text("Update group")
						.click(function() {
							$("#UserStuff").hide();
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


				//USERS
				var multipleCreateList = [];

				function updateUserList() {
					$("#NewUser").hide();
					$("#MultipleNewUser").hide();
					$("#UpdateUser").hide();
					$("#GroupStuff").show();
					$("#UserList").show();

					var userListContainer = $("#UserListContent").empty();

					edmPlugin.listUsers(function(result) {
						for(var idx = 0; idx < result.length; idx += 1) {
							userListContainer.append(createUserListElem(result[idx]));
						}
					});
				}

				function createOptions(select, groupId) {
					var length = groupList.length;
					for(var i = 0; i < length; i+= 1) {
						var option = $("<option />");
						option.attr("value", groupList[i]._id);
						option.text(groupList[i].name);
						if(!groupId) {
							if(i === 0) {
								option.attr("selected", true);
							}
						} else {
							if(groupList[i]._id === groupId) {
								option.attr("selected", true);
							}
						}
						select.append(option);
					}
				}

				function createUserListElem(data) {
					var elem = $("<div class='user-list-elem'/>");
					var name = $("<div class='info'/>").appendTo(elem);
					var buttons = $("<div class='buttons'/>").appendTo(elem);

					name.append($("<h3/>").text(data.id));

					var openButton = $("<button/>")
						.text("Show more")
						.click(function(event) {
							var button = $(event.delegateTarget);
							var text  = button.text();
							if(text === "Show more") {
								edmPlugin.readUser(data.id, function (result) {
									$("<p />").html("createTime: " + result.createTime + "<br> group id: " + result.group).appendTo(name);
									button.text("Show less");
								});
							} else {
								var p = name.find("p");
								p.remove();
								button.text("Show more");
							}
						})
						.appendTo(buttons);

					var EditUserButton = $("<button>")
						.text("Update user")
						.click(function() {
							$("#GroupStuff").hide();
							$("#UserList").hide();
							$("#UpdateUser").show();

							var groupInput = $("#UserGroupInput");
							groupInput.empty();
							createOptions(groupInput, data.group);

							$("#UserUpdateOk").off("click");
							$("#UserUpdateCancel").off("click");

							$("#UserUpdateOk").click(function() {
								var group = groupInput.val();

								groupInput.val("");

								edmPlugin.updateUser(data.id, {group: group}, function(result) {
									updateUserList();
								});
							});

							$("#UserUpdateCancel").click(function() {
								updateUserList();
							});
						})
						.appendTo(buttons);

					var deleteUserButton = $("<button>")
					.text("Delete")
					.click(function() {
						edmPlugin.deleteUser(data.id, function(result) {
							updateUserList();
						});
					})
					.appendTo(buttons);

					return elem;
				}

				function pushToCreateList(element) {
					var span = $("#MultipleUsersList");
					var first = false;
					if(span.text() === "") {
						first = true;
					}
					var text = "";
					if(!first) {
						text += ",<br>";
					}
					text += "{id: " + element.id;
					if(element.email) {
						text += ", email: " + element.email;
					}
					if(element.normalName) {
						text += ", normalName: " + element.normalName;
					}
					text += ", group: " + element.group + "}";

					span.append(text);
					multipleCreateList.push(element);
				}

				$("#NewUserButton").click(function() {
					$("#UserList").hide();
					$("#NewUser").show();

					var groupSelect = $("#NewUserGroup");

					groupSelect.empty();

					createOptions(groupSelect);
				});

				$("#NewUserAddButton").click(function() {
					var idInput = $("#NewUserId"),
						emailInput = $("#NewUserEmail"),
						nameInput = $("#NewUserName"),
						groupInput = $("#NewUserGroup");


					if(idInput.val() !== "") {
						var data = {
							id: idInput.val(),
							group: groupInput.val()
						};

						if(emailInput.val() !== "") {
							data.email = emailInput.val();
							emailInput.val("");
						}
						if(nameInput.val() !== "") {
							data.normalName = nameInput.val();
							nameInput.val("");
						}

						idInput.val("");
						edmPlugin.createUser(data, updateUserList);
					}
				});

				$("#MultipleNewUserButton").click(function() {
					$("#UserList").hide();
					$("#MultipleNewUser").show();

					var groupSelect = $("#MultipleNewUserGroup");

					groupSelect.empty();
					multipleCreateList = [];
					$("#MultipleUsersList").empty();

					createOptions(groupSelect);
				});

				$("#MultipleNewUserAddButton").click(function() {
					var idInput = $("#MultipleNewUserId"),
						emailInput = $("#MultipleNewUserEmail"),
						nameInput = $("#MultipleNewUserName"),
						groupInput = $("#MultipleNewUserGroup");


					if(idInput.val() !== "") {
						var data = {
							id: idInput.val(),
							group: groupInput.val()
						};

						if(emailInput.val() !== "") {
							data.email = emailInput.val();
							emailInput.val("");
						}
						if(nameInput.val() !== "") {
							data.normalName = nameInput.val();
							nameInput.val("");
						}

						idInput.val("");
						pushToCreateList(data);
					}
				});

				$("#MultipleCreateUserButton").click(function() {
					if(multipleCreateList.length > 0) {
						edmPlugin.createMultipleUser({users: multipleCreateList}, function(err) {
							updateUserList();
						})
					}
				});

				function readyHandler() {
					updateGroupList();
					updateUserList();
				}

				$(document).ready(readyHandler);
			}, function(error) {
				alert(error);
			});
		</script>
	</head>
	<body>
		<div>
			<h1>EDMdesigner-API-Example-PHP-Admin</h1>
		</div>

		<div id="GroupStuff">
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
		</div>

		<br>
		<p>---------------------------------------------------------------------------</p>
		<br>

		<div id="UserStuff">
			<div id="UserList">

				<button id="NewUserButton">New user</button>
				<button id="MultipleNewUserButton">Create multiple users</button>
				
				<h2>Users</h2>

				<div id="UserListContent">
				</div>
				
			</div>

			<div id="NewUser">
				<h2>New User</h2>
				<h3>UserId*</h3>
				<input id="NewUserId"/>
				<h3>Email</h3>
				<input id="NewUserEmail" />
				<h3>Normal Name</h3>
				<input id="NewUserName" />
				<h3>Group</h3>
				<select id="NewUserGroup" >
				</select>
				<p>*required</p>
				<div>
					<button id="NewUserAddButton">Add</button>
				</div>
			</div>

			<div id="MultipleNewUser">
				<h2>New User</h2>
				<h3>UserId*</h3>
				<input id="MultipleNewUserId"/>
				<h3>Email</h3>
				<input id="MultipleNewUserEmail" />
				<h3>Normal Name</h3>
				<input id="MultipleNewUserName" />
				<h3>Group</h3>
				<select id="MultipleNewUserGroup" >
				</select>
				<p>*required</p>
				<div>
					<button id="MultipleNewUserAddButton">Add</button>
					<button id="MultipleCreateUserButton">Create</button>
				</div>
				<p>Array to send: [<br><span id="MultipleUsersList"></span><br>]</p>
			</div>

			<div id="UpdateUser">
				<h2>Update User's group</h2>
				<div>
					<select id="UserGroupInput" >
					</select>
				</div>
				<div>
					<button id="UserUpdateOk">Ok</button>
					<button id="UserUpdateCancel">Cancel</button>
				</div>
			</div>
		</div>

	</body>
</html>

<?php
}
?>