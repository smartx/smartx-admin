TDriver
============



### Start Ride
`POST` http://localhost:8888/user/start_ride

>DATA
```
id=92
&lat=-12.0956402734
&lng=-77.0316267014
&a=Some Address
&r=Some reference
```

### Update Driver Location
`POST`http://localhost:8888/tdriver/update_location


```
id=33
&lat=-11.864414890431348
&lng=-77.0938904899619
```
response:
ignore it for now


User 
====

### FakeLogin
`GET` http://localhost:8888/user/fakelogin/__username__

response
```js
{"user_id":"103"}
```

### Update User Location
`POST`http://localhost:8888/user/update_location

```
id=33
&lat=-11.864414890431348
&lng=-77.0938904899619
```

response:
ignore it for now
