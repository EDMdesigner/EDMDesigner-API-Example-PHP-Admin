EDMDesigner-API-Example-PHP-Admin
=================================

Example project about integrating the the admin functionalities.

EDMdesigner-API
===============

[EDMdesigner](http://www.edmdesigner.com) is a drag and drop tool for creating responsive HTML e-mail templates very quickly and painlessly, radically increasing your click-through rate. This documentation is to give you a detailed description about the EDMdesigner-API with which you can integrate our editor into any web-based system (e.g. a CRM, CMS, WebShop or anything else you can imagine).

To start developing with our api, please request an API_KEY (and a corresponding magic word) at info@edmdesigner.com.

Basically, the only thing you have to do is to implement one side of the handshaking on your server. When the handshaking is done, you can use the sent object, in which you can find the functions that are communicating with our server.

The handshaking is built into the initialization process, so if you implemented the handshaking on your side, everything will work automatically.

We provide example implementations that include the handshaking as well. You can find the continously broading list at the end of this page at the Example implementations section.

Initializing
------------
	
	<script src="path_to_your_jquery.js"></script>
	<script src="http://api.edmdesigner.com/EDMdesignerAPI.js?route=##handshaking_route##"></script>
	<script>
		initEDMdesignerPlugin("##userId##", function(edmDesignerApi) {
			...
		});
	</script>
	
First of all, jQuery has to be loaded before you load our API. In the second line you can see a route parameter in the script's src. By that you can tell the script where to look for the handshaking implementation. For example, if you implemented it in your index.php, you have to replace ##handshaking_route## with index.php.
The first parameter is a user id from your system. It can be any string. If you don't want to handle separate user accounts, just pass there your API_KEY.

In the resulting object (edmDesignerApi) you will find some functions through which you can interact with our system.

### Handshaking
To implement the handshaking on your server, you need an API_KEY, a magic word (wich is delivered with your API_KEY), your user's IPv4 address and a timestamp. The logic that handles handshaking has to receive the userId too, because it has to be sent to our server as well. Our API implementation automatically sends the userId in a POST HTTP request. You can set this user id by the first parameter of the initEDMdesignerPlugin.

After concatenating the API_KEY, the IPv4 address of your user, the timestamp (as a string) and your magic word, you have to create an md5 hash of the resulting string.

	
	hash = md5(API_KEY + ipv4 + timestamp + magic)


After this, you have to send the API_KEY, the userId, the IPv4 address, the timestamp and the generated md5 hash to our server at api.edmdesigner.com/api/token through HTTP.

If everything goes well, you get back a token, with which your user can be identified.

# API functions
## User functions
### edmDesignerApi.listProjects(callback)
Lists the projects of the actual user.
#### Parameters:
  * callback

#### Example:
	
	<script>
		initEDMdesignerPlugin("TestUser", function(edmDesignerApi) {
			edmDesignerApi.listProjects(function(result) {
				console.log(result);
			});
		});
	</script>
	

### edmDesignerApi.createProject(data, callback)
Creates a new project (a new e-mail template).
#### Parameters:
  * data {Object}
    * data.title {String} The title of the new project.
    * data.description {String} The description of the new project.
    * data.document {Object} An object, which represents a template. By setting this param, you can create new projects based on your prepared custom templates.
  * callback(result) {Function}
    * the result param is an object in which you can find an _id property, which identifies the newly created project
    * result._id {String} The id of the new project. (This is a [MongoDB](http://www.mongodb.org/) id.)

#### Example:
	
	<script>
		initEDMdesignerPlugin("TestUser", function(edmDesignerApi) {
			edmDesignerApi.createProject(function({title: "test-title", description: "test-desc", document: {}}, result) {
				console.log(result._id);
			});
		});
	</script>
	
	
### edmDesignerApi.duplicateProject(projectId, callback)
Creates the exact copy of the project with the ID specified in projectId.
#### Parameters:
  * projectId {String} The id of the project. Note that it has to be a valid MongoDB _id. It's best if you use the values that you got when you listed the projects of the user with the edmDesignerAPI.listProjects function.
  * callback

#### Example:
	
	<script>
		initEDMdesignerPlugin("TestUser", function(edmDesignerApi) {
			edmDesignerApi.createProject({title: "test-title", description: "test-desc"}, function(result) {
				edmDesignerApi.duplicateProject(result._id, function(result) {
					//callback hell...
				});
			});
		});
	</script>
	

### edmDesignerApi.removeProject(projectId, callback)
Removes a project.
#### Parameters:
  * projectId {String} The id of the project. Note that it has to be a valid MongoDB _id. It's best if you use the values that you got when you listed the projects of the user with the edmDesignerAPI.listProjects function.
  * callback

#### Example:
	
	<script>
		initEDMdesignerPlugin("TestUser", function(edmDesignerApi) {
			edmDesignerApi.createProject({title: "test-title", description: "test-desc"}, function(result) {
				edmDesignerApi.removeProject(result._id, function(result) {
					//callback hell...
				});
			});
		});
	</script>
	

### edmDesignerApi.openProject(projectId, callback)
Opens a project.
#### Parameters:
  * projectId {String} The id of the project. Note that it has to be a valid MongoDB _id. It's best if you use the values that you got when you listed the projects of the user with the edmDesignerAPI.listProjects function.
  * callback

#### Example:
	
	<script>
		initEDMdesignerPlugin("TestUser", function(edmDesignerApi) {
			edmDesignerApi.createProject({title: "test-title", description: "test-desc"}, function(result) {
				edmDesignerApi.openProject(result._id, function(result) {
					$("body").append(result.iframe);
				});
			});
		});
	</script>

___	

### edmDesignerApi.generateProject(projectId, callback)
Generates the bulletproof responsive HTML e-mail based on the projectId.
#### Parameters:
  * projectId {String} The id of the project. Note that it has to be a valid MongoDB _id. It's best if you use the values that you got when you listed the projects of the user with the edmDesignerAPI.listProjects function.
  * callback

#### Example:
	
	<script>
		initEDMdesignerPlugin("TestUser", function(edmDesignerApi) {
			edmDesignerApi.createProject({title: "test-title", description: "test-desc"}, function(result) {
				edmDesignerApi.generateProject(result._id, function(result) {
					//the result is a robust responsive HTML e-mail code.
				});
			});
		});
	</script>

___	
	
### edmDesignerApi.getDefaultTemplates(callback)
You can get the default templates povided by EDMdesigner by calling this funciton.
#### Parameters:
  * callback
 
#### Example:
	
  <script>
	  initEDMdesignerPlugin("TestUser", function(edmDesignerApi) {
			edmDesignerApi.getDefaultTemplates(function(result) {
				//the result is an array, containing the default projects, provided by EDMdesigner
			});
		});
	</script>

___

##Admin functions
### edmDesignerApi.listGroups(callback)
Lists the groups you have
#### Parameters:
  * callback
 
#### Example:
	
	<script>
		initEDMdesignerPlugin("TestAdmin", function(edmDesignerApi) {
			edmDesignerApi.listGroups(function(result) {
				//the result is an array, containing your groups
			});
		});
	</script>

___

### edmDesignerApi.createGroup(data, callback)
Creates a new group
#### Parameters:
  * data {Object}
    * data.name {String} /REQUIRED/ The name you want to give to the new group
    * data.featureSwitch {Object} The features that available for users belong to this group. Please note that now it doesn't has any function, but later there will be a list of possible features which you can choose from. 
  * callback
 
#### Example:
	
	<script>
		initEDMdesignerPlugin("TestAdmin", function(edmDesignerApi) {
			edmDesignerApi.createGroup({name: "exampleGroup", featureSwitch: {exampleFeature1: true, exampleFeature3: true}}, function(result) {
				//the resultGroup is an object with
				//name {String} (the new group's name)
				//_id  {String} (the new group's id) and
				//featureSwitch {Object} (the group's features) properties
				console.log(result);
			});
		});
	</script>

___

### edmDesignerApi.getGroup(groupId, callback)
Gets a specified group
#### Parameters:
   * groupId {String} The id of the group. Note that it has to be a valid MongoDB _id. It's best if you use the values that you got when you listed your groups with the edmDesignerAPI.listGroups function.
   * callback
 
#### Example:
	
	<script>
		initEDMdesignerPlugin("TestAdmin", function(edmDesignerApi) {
			edmDesignerApi.createGroup({name: "exampleGroup", featureSwitch: {}}, function(result) {
			
				edmDesignerApi.getGroup(result._id, function(resultGroup) {	
					//the resultGroup is an object with
					//name {String} (the new group's name)
					//_id  {String} (the new group's id) and
					//featureSwitch {Object} (the group's features) properties
					console.log(resultGroup);
				});
			
			});
		});
	</script>

___

### edmDesignerApi.updateGroup(groupId, data, callback)
Updates a specified group's name or the features it provides or both of these two at the same time.
#### Parameters:
   * groupId {String} The id of the group. Note that it has to be a valid MongoDB _id. It's best if you use the values that you got when you listed your groups with the edmDesignerAPI.listGroups function.
   * data {Object}
     * data.name {String} The name you want to give to the group
     * data.featureSwitch {Object} The features that available for users belong to this group. Please note that now it doesn't has any function, but later there will be a list of possible features which you can choose from.
   * callback
 
#### Example:
	
	<script>
		initEDMdesignerPlugin("TestAdmin", function(edmDesignerApi) {
			edmDesignerApi.createGroup({name: "exampleGroup", featureSwitch: {feature1: true}}, function(result) {
			
				edmDesignerApi.updateGroup(result._id, {name: "newName"}, function(resultGroup) {	
					//the resultGroup is an object with
					//name {String} (the new group's name)
					//_id  {String} (the new group's id) and
					//featureSwitch {Object} (the group's features) properties
					console.log(resultGroup);
				});
			
			});
			
			edmDesignerApi.createGroup({name: "exampleGroup2", featureSwitch: {feature1: true}}, function(result) {
			
				edmDesignerApi.updateGroup(result._id, {featureSwitch: {feature1: true, newFeature: true}}, function(resultGroup) {	
					//the resultGroup is an object with
					//name {String} (the new group's name)
					//_id  {String} (the new group's id) and
					//featureSwitch {Object} (the group's features) properties
					console.log(resultGroup);
				});
				
			});
			
			edmDesignerApi.createGroup({name: "exampleGroup3", featureSwitch: {feature1: true}}, function(result) {
			
				edmDesignerApi.updateGroup(result._id, {name: "newExampleName", featureSwitch: {feature1: true, newFeature: true}}, function(resultGroup) {	
					//the resultGroup is an object with
					//name {String} (the new group's name)
					//_id  {String} (the new group's id) and
					//featureSwitch {Object} (the group's features) properties
					console.log(resultGroup);
				});
			
			});
		});
	</script>

___
	
### edmDesignerApi.listUsers(callback)
Lists the users you have
#### Parameters:
  * callback
 
#### Example:
	
	<script>
		initEDMdesignerPlugin("TestAdmin", function(edmDesignerApi) {
			edmDesignerApi.listUsers(function(result) {
				//the result is an array, containing your users
				//A user is an object with
				//id (the user's id)
				//group (The group which the user belongs) and
				//createTime (Time of creation) properties
				console.log(result);
			});
		});
	</script>

___

### edmDesignerApi.createUser(data, callback)
Creates a new user
#### Parameters:
  * data {Object}
    * data.id {String} /REQUIRED/ The id you want to use for this new user
    * data.group {String} The id of the group you want this user to belong. Note that it has to be a valid MongoDB _id. It's best if you use the values that you got when you listed your groups with the edmDesignerAPI.listGroups function.
  * callback
 
#### Example:
	
	<script>
		initEDMdesignerPlugin("TestAdmin", function(edmDesignerApi) {
			edmDesignerApi.createGroup({name: "exampleGroup", featureSwitch: {}}, function(result) {
			
				edmDesignerApi.createUser({id: "exampleuserId", group: result._id}, function(resultUser) {
					// the resultUser is an object with an id property
					console.log(resultUser);
				});
			
			});
		});
	</script>

___

### edmDesignerApi.createMultipleUser(data, callback)
Creates multiple user
#### Parameters:
  * data {Array} It contains user objects. User object should have the following properties:
    * id {String} /REQUIRED/ The id you want to use for this new user
    * group {String} The id of the group you want this user to belong. Note that it has to be a valid MongoDB _id. It's best if you use the values that you got when you listed your groups with the edmDesignerAPI.listGroups function.
  * callback
 
#### Example:
	
	<script>
		initEDMdesignerPlugin("TestAdmin", function(edmDesignerApi) {
			edmDesignerApi.createGroup({name: "exampleGroup", featureSwitch: {}}, function(result) {
			
				edmDesignerApi.createMultipleUser([{id: "exampleUserId", group: result._id}, {id: "exampleUser2Id}, {id: "exampleUser3"}], function(resultObj) {
					// the resultObj is an object with the following properties:
					// created {Array} It contains the users whose have been created
					// failed {Array} It contains the users whose creation has failed
					// alreadyHave {Array} It contains the users whose you already created
					console.log(resultObj);
				});
			
			});
		});
	</script>

___

### edmDesignerApi.getUser(userId, callback)
Gets a specified user
#### Parameters:
   * userId {String} The id of the user.
   * callback
 
#### Example:
	
	<script>
		initEDMdesignerPlugin("TestAdmin", function(edmDesignerApi) {
			edmDesignerApi.createUser({id: "exampleUserId"}, function(result) {
			
				edmDesignerApi.getUser(result.id, function(resultUser) {
					/**the resultUser is an object with
					id (the user's id)
					group (The group which the user belongs) and
					createTime (Time of creation) properties*/
					console.log(resultUser);
				});
			
			});
		});
	</script>

___

### edmDesignerApi.updateUser(userId, data, callback)
Updates a specified user. Only the group (which the user belongs) can be changed.
#### Parameters:
   * userId {String} The id of the user. 
   * data {Object}
     * data.group {String} The id of the group you want this user to belong. Note that it has to be a valid MongoDB _id. It's best if you use the values that you got when you listed your groups with the edmDesignerAPI.listGroups function.
   * callback
 
#### Example:
	
	<script>
		initEDMdesignerPlugin("TestAdmin", function(edmDesignerApi) {
			edmDesignerApi.createUser({id: "exampleUserId"}, function(result) {
				edmDesignerApi.createGroup({name: "exampleGroup", featureSwitch: {}}, function(resultGroup) {
				
					edmDesignerApi.updateUser(result.id, {group: resultGroup._id}, function(resultUser) {	
						//the resultUser is an object with an id property
						console.log(resultUser);
					});
				
				});
			});
		});
	</script>

___

### edmDesignerApi.deleteUser(userId, callback)
Deletes a specified user
#### Parameters:
   * userId {String} The id of the user.
   * callback
 
#### Example:
	
	<script>
		initEDMdesignerPlugin("TestAdmin", function(edmDesignerApi) {
			edmDesignerApi.createUser({id: "exampleUserId"}, function(result) {
			
				edmDesignerApi.deleteUser(result.id, function(resultUser) {
					//the resultUser is an object with an id propert
					console.log(resultUser);
				});
			
			});
		});
	</script>

___

# Server side routes
Almost every client side functions have a corresponding route on server side.

## Admin functions

## Group manipulating routes
### List groups
Lists the groups you have

#####Type
  + GET

#####Route
  + //api.edmdesigner.com/json/groups/list

___

### Create group
Creates a new group

#####Type
  + POST

#####Route 
  + //api.edmdesigner.com/json/groups/create

#### Parameters (you should post):
  * name {String} /REQUIRED/ The name you want to give to the new group
  * featureSwitch {Object} The features that available for users belong to this group. Please note that now it doesn't has any function, but later there will be a list of possible features which you can choose from. 

___

### Get one group
Gets a specified group

#####Type
  + GET

#####Route
  + //api.edmdesigner.com/json/groups/read/:id

#### Parameters (in the route):
   * id {String} The id of the group. Note that it has to be a valid MongoDB _id. It's best if you use the values that you got when you listed your groups with the /json/groups/list route.

___

### Update one group
Updates a specified group's name or the features it provides or both of these two at the same time.

#####Type
  + POST

#####Route
  + //api.edmdesigner.com/json/groups/update

#### Parameters (you should post):
   * _id {String} The id of the group. Note that it has to be a valid MongoDB _id. It's best if you use the values that you got when you listed your groups with the /json/groups/list route.
   * name {String} The name you want to give to the group
   * featureSwitch {Object} The features that available for users belong to this group. Please note that now it doesn't has any function, but later there will be a list of possible features which you can choose from.

___

## User manipulating routes


### Create handshaking
Create the handshaking between PHP and API needs to be done before any further call
		
		$url = "http://api.edmdesigner.com/api/token";
		$data = array(
			"id"	=> $publicId,
			"uid"	=> $user,
			"ip"	=> $ip,
			"ts"	=> $timestamp,
			"hash"	=> $hash
		);
		
		$options = array(
		    'http' => array(
		        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
		        'method'  => 'POST',
		        'content' => http_build_query($data),
		    )
		);

		$context  = stream_context_create($options);
		$result = file_get_contents($url, false, $context);
		
		$token = json_decode($result, TRUE);


### Add custom string to all users
Add customstrings to all of your users.
Note that every calls overwrites the previous, so if you want to remove all the custom strings, just do the call with 	$customStrings['items'] or dont send the items at all
	
		$url = "http://api.edmdesigner.com/json/user/addCustomStrings?token=".$token."&user=".$user;
		//the $token is the string received from token validation and the $user is an existing userId
		
		$customStrings = array();
		$customStrings['items'] = array();
		$customStrings['items'][] = array('label' => 'testLabel1', 'replacer' => 'testReplacer1');
		$customStrings['items'][] = array('label' => 'testLabel2', 'replacer' => 'testReplacer2');
		$customStrings['items'][] = array('label' => 'testLabel3', 'replacer' => 'testReplacer3');
		
		$options = array(
		    'http' => array(
		        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
		        'method'  => 'POST',
		        'content' => http_build_query($data),
		    )
		);

		$context  = stream_context_create($options);
		$result = file_get_contents($url, false, $context);
		$savedResult = json_decode($result, TRUE);

### Add custom string to specified user
Add customstrings to just some of your users. 
Note that every calls overwrites the previous, so if you want to remove all the custom strings, just do the call with 	$customStrings['items'] or dont send the items at all
	
		$url = "http://api.edmdesigner.com/json/user/addCustomStringsToUser?token=".$token."&user=".$user;
		//the $token is the string received from token validation and the $user is an existing userId
		
		$customStrings = array();
		$customStrings['userId'] = 'your user id'; // It must be one of your existing user's id, otherwise returns error
		$customStrings['items'] = array();
		$customStrings['items'][] = array('label' => 'testLabel1', 'replacer' => 'testReplacer1');
		$customStrings['items'][] = array('label' => 'testLabel2', 'replacer' => 'testReplacer2');
		$customStrings['items'][] = array('label' => 'testLabel3', 'replacer' => 'testReplacer3');
		
		$options = array(
		    'http' => array(
		        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
		        'method'  => 'POST',
		        'content' => http_build_query($data),
		    )
		);

		$context  = stream_context_create($options);
		$result = file_get_contents($url, false, $context);
		$savedResult = json_decode($result, TRUE);

Example implementations
-----------------------
  * [Node.js example](https://github.com/EDMdesigner/EDMdesigner-API-Example-Node.js)
  * [PHP example](https://github.com/EDMdesigner/EDMdesigner-API-Example-PHP)
  * [PHP admin example](https://github.com/EDMdesigner/EDMDesigner-API-Example-PHP-Admin)
  
Dependencies
------------
jQuery

