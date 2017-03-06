angular.module('testApp', [])
.controller('mainController', function($http) {
  var cc = this;

  //instantiate a list of objects with fields
  cc.list = [{a: "yo", b: "suh"},{ a: "yo buddai", b: "suhb"},{a: "heyah", b: "sup"}];


  $http({
      //the type of call to make - Get, post, etc.
      method: 'GET',
      //url to make the call to
      url: 'seeddms/restapi/index.php/folder/6/children',
      //JSON object you pass it
      //data: termData
      //
      //headers - just keep this
      //headers: {'Content-Type': 'application/x-www-form-urlencoded'}
      headers: {
        'Content-Type': 'application/json'
      }
  }).then(function(data) {
      //do all of your processing here
      //example: look through the JSON and fill an array will all the relevant information
     cc.list = data.data.data
     var newDataLength = cc.list.length;
	
	for(var i = 0; i< newDataLength; i++){
        cc.list[i].url = "seeddms/op/op.ViewOnline.php?documentid=" + cc.list[i].id + "&version=" + cc.list[i].version;
   	cc.list[i].date = getdhm(cc.list[i].date);
	 console.log(cc.list[i]);
		//cc.list[10] = "/seeddms/out/out.ViewOnline.php?documentid="

	}
  });
function getdhm(timestamp){
	var a = new Date(timestamp*1000);
	var months = [1,2,3,4,5,6,7,8,9,10,11,12];
	var month = months[a.getMonth()];
	var date = a.getDate();
	var year = a.getFullYear();
	var formattedTime = month + '/' + date + '/' +  year;
	return formattedTime;
}
});
