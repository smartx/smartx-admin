TDriver
============


**POST**
http://localhost:8888/users/start_ride
`u=92&la=-12.0956402734&ln=-77.0316267014&a=Some Address&r=Some reference`

## Update Driver location
request:
**POST**
:http://localhost:8888/drivers/update_location
`data=33:-11.864414890431348:-77.0938904899619`
response:
ignore it for now


User 
====

FakeLogin
request
**GET**
http://localhost:8888/users/fakelogin/**{username}**
response
{"user_id":"103"}

Update User location
request:
POST:http://localhost:8888/user/update_location
data=107:-11.864414890431348:-77.09389048996191
response:
ignore it for now
