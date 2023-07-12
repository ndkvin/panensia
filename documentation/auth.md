
# Auth API Reference

## Base Url
```http
  https://panensia.id/api
```

### Auth 

#### Register 
```http
  POST ${baseUrl}/register
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
    "message": "User created successfully",
    "data": {
        "user": {
            "email": "andika@gmail.com",
            "phone": "084789891832",
            "name": "Andika",
            "updated_at": "2023-07-12T10:22:48.000000Z",
            "created_at": "2023-07-12T10:22:48.000000Z",
            "id": 2
        },
        "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiNzA5ZWQ0ZDkxNDhmYTRlZmJiNTAyZjRjYjFlNTkyZjVjM2U0YmFmYzVlZWFiMTlmNmM0ZDg3MWRhZWZmNjI2YThiNjI4MDBhNGFmMDk0MDMiLCJpYXQiOjE2ODkxNTczNjguNDA2MTUyLCJuYmYiOjE2ODkxNTczNjguNDA2MTU3LCJleHAiOjE2ODkyNDM3NjguMjA2NzgsInN1YiI6IjIiLCJzY29wZXMiOltdfQ.TMuDavdUxxLdcOo18cOZ9yMjg5l4HJOuSRDV49IyLLDtTrHGIzrK2KRVGipuLpZAW_jrwoE8_AgGDO3zthISSiN2E1tkGn2idGeaT1YDwB4Njuf3502dQ7a-IQowhGGRBC_YR8nmWgp-9oYbvPt2N9AtwP-n0uJFiQLnwx9rDGInjnH7oiz3qgzn4GIeM4qqkQZ1eaoCEVQ-t6GGwXzGOXjKCNM-5LRr69SJXRC_qRAT0jRJekL_YEsGlLsLYgdUaigwJxK8NgPZV-r-tKxnFWToAxEFk6iaErT1sz-U5aFvGcMDME1k7lHH7Zcve9yWvwzhLjhzg9EaCVj0rNrcSyVS1juDClZ5Dre7mukWABPpgkr8WfNpAJfNRSUrVDcBtnbrVGnWlEyePnJX5_kalXM3GnLV-tQAbdht2cb0e56HT7eNjR7VXQdsf9rK-gNCv5k6VZ_zC-x42gF0qWDkeRsRpdyzse4H4W2T3qFtEmpxZGAWhFMmF5V6cGiFCzDb3NMqpA--N_De_6aK_AY3eKy8ygatTasqSa2Gmfr2sPjzQqh-sshvoUpDLFPCUK33j4HYhDtcnWH3PONLFCMdi7_uyU3z2MjzyBePhvKHaKmrx8GjJba3LaSzGZyllzegL8Epgjm969r1Xjroq8uDOYVQyeInxRndWNjkoWQTRwI"
    }
}
```
Error Response
```http
{
    "success": false,
    "code": 422,
    "message": "Unprocessable Entity",
    "errors": [
        "The name field is required.",
        "The email has already been taken.",
        "The phone field is required."
    ]
}
```
#### Login
```http
  POST ${baseUrl}/login
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
    "message": "User login successfully",
    "data": {
        "user": {
            "id": 1,
            "name": "Admin",
            "email": "admin@gmail.com",
            "email_verified_at": null,
            "phone": "081234567890",
            "role": "admin",
            "last_login": "2023-07-11 10:54:05",
            "created_at": "2023-07-12T09:14:44.000000Z",
            "updated_at": "2023-07-12T09:14:44.000000Z"
        },
        "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiOTVkMDI3ZjhhYmM3OGQ3OGRlZjFhMzNlZjQyM2I1Y2RkMGMwMzViNWJjYjFmM2QwNGE0ZGViZGZmMTM4YjBkZGM1YzEzYjk5ZmJiZGRhZGMiLCJpYXQiOjE2ODkxNTY5NjYuMDQxMTk4LCJuYmYiOjE2ODkxNTY5NjYuMDQxMjAxLCJleHAiOjE2ODkyNDMzNjUuODI0NDgsInN1YiI6IjEiLCJzY29wZXMiOltdfQ.AoQJ_k4Ofm7lQ0-_XDGiwHk_RgZYWrU5KPm28TK4P-EumeHdgZOQCXbJ1DFtQhTuZRRP-PxhviOJOpLoy8sABxtR_zenVEevMYv3IcBLQewBoxfE6JnRA1nkZB-mIQcpF0WxPadm8V-qCUBbtUJ1Vy9p_OyPl7dr0x0OlGXFg2YaAi9BuiY4GEmLpoaXcbZ-iXms4qUtRJxmSW-aQQgGEr01sIRzqnqKqoHJZoVeYLdJ0hNQLjAjPSQKRJCeRYNXMrXMkgODl2NL_E2Sje8RJxJQ3f7Eeu61GeCajSXAt2ivP7uEnIGEnKx0ug6wNw8pXmZzb5F3v7rcoBLTEAUAYzEUIQ-5x4C-l9HWgOx2faUqdoYxLfsySiFGafg7fVN-bHcqDBcCOrHiVh1b_chgpbmuApBRB4LhZcOIjGkCAVFxIW4pyEbSWxbzH6b9F15g8eGXkO1s2JA47fJaK94lo5FccCcNUhoQA0C2wxtTBWDcbUqpqvBPwpa11faFufs7QN1_04_4c6-wgEq7Gl0P5AXG-ptVDrmB7Vlrpu9y81dVkKozVEvEUQVzI-V99eG7FM-jtaxjDh_H-2lPPKGZPii6jJLXCTT0k5SJC3uuv1y_2y6CNvzjV0ZbFZJxtrPIABZnMmJ-VouxHdxuhveQSEPZyH3MYRHakKEDzEdovSE"
    }
}
````

Error Response
```http
{
    "success": false,
    "code": 400,
    "message": "Bad Request",
    "errors": [
        "credential not match to our record"
    ]
}
````
#### See Profile
```http
  GET ${baseUrl}/profile
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
    "message": "User profile successfully",
    "data": {
        "id": 2,
        "name": "Andika",
        "email": "andika@gmail.com",
        "email_verified_at": null,
        "phone": "084789891832",
        "role": "user",
        "last_login": "2023-07-11 10:54:05",
        "created_at": "2023-07-12T10:22:48.000000Z",
        "updated_at": "2023-07-12T10:22:48.000000Z"
    }
}
````

Error Response
```http
{
    "success": false,
    "code": 500,
    "message": "Server Error",
    "errors": [
        "Unauthenticated."
    ]
}
````