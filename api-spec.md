TDriver
============



### Start Ride
`POST`http://localhost:8888/users/start_ride

`u=92&la=-12.0956402734&ln=-77.0316267014&a=Some Address&r=Some reference`

### Update Driver Location
`POST`http://localhost:8888/drivers/update_location

`user_id=33
  &lat=-11.864414890431348
  &lng=-77.0938904899619`

response:
ignore it for now


User 
====

### FakeLogin
`GET`http://localhost:8888/users/fakelogin/**username**

response
{"user_id":"103"}

### Update User Location
`POST`http://localhost:8888/user/update_location

`data=107:-11.864414890431348:-77.09389048996191`

response:
ignore it for now
