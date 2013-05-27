TDriver
============

### Update TDriver Location

`POST`http://smx.com/tdriver/update_location

``` 
id=33
&lat=-11.864414890431348
&lng=-77.0938904899619
```
---
```js
{
  "data":[ // list of new rides found for this tdriver
    {
      "ride_id":"133",
      "lat":"-12.1231231",
      "lng":"-67.312312312",
      "user_first_name":"Patricio",
      "phone":"993923913"
    }
  ],
  "meta":{
    code:"200"
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

```js
{
  "data":{
    "ride_id":"123",
    "ex_time":2, // expected time to get a tdriver (in minutes)
    "n_tdrivers":3 // number of tdrivers that received the request in the first shot
  }
}
```

### Fetch User Riding Status
`GET` http://smx.com/user/fetch_status


```
id=92
```
---

```js
{
  "data":{
    "ride_id":"123",
    "status":1 // ride status (1=requested, 2=tdriver_coming, 3=riding)
    "tdriver_id":"30", // present only if status is 2
    "tdriver_name":"Taxista Ra Ra",
    "tdriver_pic":"http://pic.smx.com/123.png",
    "tdriver_phone":"994939443",
    "ex_time":2, // expected time to wait for a tdriver to pick (in minutes)
    "ex_ar_time":3 // expected time for the tdriver to arrive (in minutes). Present only if status is 2
    "ex_land_time":12 // expected time to get to end location (in mins). Present only if status is 3
  }
}
```
