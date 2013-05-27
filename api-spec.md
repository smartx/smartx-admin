TDriver
============

### Update TDriver Location

`POST`http://localhost:8888/tdriver/update_location

``` 
id=33
&lat=-11.864414890431348
&lng=-77.0938904899619
```
---
```
{
  "data":[ // list of new rides found for this tdriver
    {
      "ride_id":"133",
      "lat":"-12.1231231",
      "lng":"-67.312312312"
    }
  ],
  "meta":{
  
  }
}
```


User 
====

### FakeLogin

`GET` http://localhost:8888/user/fakelogin/__username__
```
```
---
```js
{"user_id":"103"}
```

### Update User Location
`POST` http://smx.com/user/update_location

```
id=33
&lat=-11.864414890431348
&lng=-77.0938904899619
```
---
```
ignore it for now
```


### Start Ride
`POST` http://smx.com/user/start_ride

```
id=92
&lat=-12.0956402734
&lng=-77.0316267014
&a=Some Address
&r=Some reference
```
---

```
ignore it for now
```
