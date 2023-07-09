
# Auth API Reference

## Base Url
```http
  localhost:8000/api
```

### Auth 

#### Register 
```http
  POST /register
```

Request Body
```http
{
    "email": "andikaaaaa@gmail.com",
    "name": "andika",
    "password": "123123123",
    "phone": "098787895664"
}
```
Success Response
```http
{
    "success": true,
    "code": 200,
    "data": {
        "user": {
            "email": "andikaaaaaa@gmail.com",
            "name": "andika",
            "phone": "098787895664",
            "updated_at": "2023-07-09T13:54:39.000000Z",
            "created_at": "2023-07-09T13:54:39.000000Z",
            "id": 4
        },
        "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiOGJkOTQxMjg4MmRmMWI0ZDBkM2FkNzczZmZkMGJlOTMyOTU1MzBhNTI5ODk3MmY1YzFkZmQyZDA1MDhjM2VmOGVkZmU5MDRhNjY3OTc5ZTciLCJpYXQiOjE2ODg5MTA4NzkuODA4MDgxLCJuYmYiOjE2ODg5MTA4NzkuODA4MDg2LCJleHAiOjE3MDQ4MDg0NzkuNzY3NDM0LCJzdWIiOiI0Iiwic2NvcGVzIjpbXX0.VdxwwDWnMTilD_w_tVpPoGEugJ-EbqILbIfY9g2xBB1F1rerV_gNlwlqJ5_Ld7qiVLwCe_RfhVOzuL5akmT2onwPzl4c1x69BwB6CDszt-YMRhodknU6D_GWCVcDZCI08Q7YjkHLwnN2SVDT_RaBX5HsTA1pwAzeLtrJ6XQOZYirr8lMAUJRhzwnk5bw-UAmjlkhENyAUhHWX6xPmuDAkMo_ix21vyvgw4xEYY1RHopqG-sGjSG5BjUcV6j9G_KnanI9E3SWIvfZR__XRlTdfxGtH0FzrEOIdp63D40I8kLazFOsX9BjILKpY_zYEn7_YccyjfpmgsUt6BsbEKiy1bcamqsOZH2NoWzWkZgsbIfiIus0mRqMDCZd4RO5eWiC5z-du5uxAynhMctYE1LwZg33SpaSoWJmSxR1ScFgc0yCH23E9VUJHAETsh9o7SYSwUn4m5fCrxHe-5Vjja6VsnE3ZiJy0B1mJZuff5B4WAnrmdL7IX8f7jVRAfqQODj1KQFd6LNmGjy621Z-z_EMdiDOmrhCvkTqkbNvZGiuCkWEYq13BVEEtPr7bRhYxm1Rxz7UwecUldkUr1iAcUZNyw6HW5k1nhoDN9gyTQOSZIF-u8Kzh191AqI6lgAovuSQ358j921oMDaxNZhAAVmgTTH0ormk04t_cbPgKuxYGRQ"
    }
}
```
Error Response
```http
{
    "success": false,
    "code": 422,
    "errors": [
        "The email has already been taken."
    ]
}
```
#### Login
```http
  POST /login
```

Request Body
```http
{
    "email": "andikaaaaa@gmail.com",
    "password": "123123123",
}
````
Success Response
```http
{
    "success": true,
    "code": 200,
    "data": {
        "user": {
            "id": 4,
            "name": "andika",
            "email": "andikaaaaaa@gmail.com",
            "email_verified_at": null,
            "phone": "098787895664",
            "role": "user",
            "last_login": "2023-07-09 12:35:15",
            "created_at": "2023-07-09T13:54:39.000000Z",
            "updated_at": "2023-07-09T13:54:39.000000Z"
        },
        "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiNDQzY2IzMzEzNWQ4MTQ5Zjk1ZWYxZGJmZmViNzM1ZTI5ZGM5ZTE2NTYzNzBkZjAyYmM4MmMzMTFmYmExNmRlZDIyYmYxZWNlYjg1YjNiYzQiLCJpYXQiOjE2ODg5MTEwNTIuODU0ODgyLCJuYmYiOjE2ODg5MTEwNTIuODU0ODg4LCJleHAiOjE3MDQ4MDg2NTIuNzgwOTIyLCJzdWIiOiI0Iiwic2NvcGVzIjpbXX0.I1T4_qAGVkw0sDq6aplys6YgRctEgBpWDOVWxuB_bZapc_gIR8y1849AocKKsqNfI7sKazsenI1gyIZHmplZiPeHtP51vaf-98SjyfMVfIh65TnCQQYSB3tTjV5HLrm_BILuBrx0JelWmdZn3ENnmBHDb4fy3tecyzGL65n9LvTQUTvr9p3KUnpWE0mv3MUL9y92O4RY-NbHSYNiblQ_rwPju8qiKCEFmMhMNZkP8AtLYqLYlFc15vFkS69L0GJd_gd7BkxiP-D0TtDGS9UwGUMTwW8NgpxllxwzOEQVSvUfzlDkVvABihIzvTEk3uHtEPhwqSvPtYY3HtUzLqaNwrcb3QZQ0FQz6tQcH3uhhLt4dCA-iFdlZOpddK_knZmEBeNZ8MwS6Swj8MtcbUN1qkJvUgyN5_lxhzJzDQn5zKJ2Mb9z6I8kR5a_XTojylQQilLispwvYQkrBgm3V5RniRM1HBwr3rlNUhPkN8aQ9yfa7CMQQJe6KXuwQYYDdgxDzQPMV-ntHMidQIBX3grlYr606cfTVZ3yhuKeeXSE4tzCD3Ur-VJo7kM6AwpKKlvXaSCwebVE5dTfcjfg-PewJsuYhtopHAXEUNVUVjhNAcHdkYQSRln2vQ6m6Zmdg0A9KOC9qj8RViyrYp7aS_SgN8N7IrrVeyOGMdicY_qrOKs"
    }
}
````

Error Response
```http
{
    "success": false,
    "code": 422,
    "errors": [
        "The email field is required.",
        "The password field is required."
    ]
}
````
#### See Profile
```http
  GET /profile
```

Authentication
```http
Barer ${Access Token}
````


Success Response
```http
{
    "success": true,
    "code": 200,
    "data": {
        "id": 3,
        "name": "andika",
        "email": "andikaaaaa@gmail.com",
        "email_verified_at": null,
        "phone": "098787895664",
        "role": "user",
        "last_login": "2023-07-09 12:35:15",
        "created_at": "2023-07-09T12:43:18.000000Z",
        "updated_at": "2023-07-09T12:43:18.000000Z"
    }
}
````

Error Response
```http
{
    "success": false,
    "code": 403,
    "message": [
        "You are not allowed to access this route"
    ]
}
````