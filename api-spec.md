TDriver
============

### Update TDriver Location
**request:**

`POST`http://localhost:8888/tdriver/update_location

``` 
id=33
&lat=-11.864414890431348
&lng=-77.0938904899619
```
**response:**
```
1
```


User 
====

### FakeLogin
**request:**

`GET` http://localhost:8888/user/fakelogin/__username__

**response:**
```js
{"user_id":"103"}
```

### Update User Location
#### request
> POST http://smx.com/user/update_location

```
id=33
&lat=-11.864414890431348
&lng=-77.0938904899619
```

**response:**

ignore it for now


### Start Ride

> POST` http://smx.com/user/start_ride

>DATA
```
id=92
&lat=-12.0956402734
&lng=-77.0316267014
&a=Some Address
&r=Some reference
```
---

>
~~~
ignore it for now
~~~
