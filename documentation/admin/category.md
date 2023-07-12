
# Category API Reference

## Base Url
```http
  https://panensia.id/api
```

### Auth 

#### Get All Category 
```http
  GET ${baseUrl}/admin/category
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
    "message": "Category list",
    "data": [
        {
            "id": 1,
            "name": "Herbal",
            "slug": "herbal",
            "image": "http://panensia.id/storage/images/categories/j0AihD2gN3Edvc4t39h69OW45GpJ036Rg9eUqzfx.jpg",
            "created_at": "2023-07-12T10:13:27.000000Z",
            "updated_at": "2023-07-12T10:58:34.000000Z"
        }
    ]
}
```
Error Response
```http
{
    "success": false,
    "code": 403,
    "message": "Unauthorized",
    "errors": [
        "You are not authorized to access this resource"
    ]
}
```

#### Create Category
```http
  POST ${baseUrl}/admin/category
```

Request Body
```http
{
    "name": "olahan",
}
````
Success Response
```http
{
    "success": true,
    "code": 201,
    "message": "Category created successfully",
    "data": {
        "name": "olahan",
        "slug": "olahan",
        "updated_at": "2023-07-12T11:49:06.000000Z",
        "created_at": "2023-07-12T11:49:06.000000Z",
        "id": 3
    }
}
````

Error Response
```http
{
    "success": false,
    "code": 403,
    "message": "Unauthorized",
    "errors": [
        "You are not authorized to access this resource"
    ]
}
````
#### Edit Category
```http
  PUT ${baseUrl}/admin/category/${categoryId}
```

Authentication
```http
Barer ${Access Token}
````

Request Body
```http
{
    "name": "olahan",
}
```

Success Response
```http
{
    "success": true,
    "code": 200,
    "message": "Category updated successfully",
    "data": {
        "id": 3,
        "name": "olahan",
        "slug": "olahan",
        "image": null,
        "created_at": "2023-07-12T11:49:06.000000Z",
        "updated_at": "2023-07-12T11:49:06.000000Z"
    }
}
````

Error Response
```http
{
    "success": false,
    "code": 403,
    "message": "Unauthorized",
    "errors": [
        "You are not authorized to access this resource"
    ]
}
````

#### Delete Category
```http
  DELETE ${baseUrl}/admin/category/${categoryId}
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
    "message": "Category deleted successfully"
}
````

Error Response
```http
{
    "success": false,
    "code": 404,
    "message": "Not Found",
    "errors": [
        "Model Not Found"
    ]
}
````

#### Add Image Category
```http
  POST ${baseUrl}/admin/category/${categoryId}/image
```

Authentication
```http
Barer ${Access Token}
````
Request Body (Form Data)

| key | value |
| :---: | :---: | 
| image | image file (jpeg,png,jpg,gif,svg) max 2mb |

Success Response
```http
{
    "success": true,
    "code": 201,
    "message": "Category image uploaded successfully",
    "data": {
        "id": 1,
        "name": "Herbal",
        "slug": "herbal",
        "image": "http://panensia.id/storage/images/categories/ASm5MJGx7AofBzen38qvmri1PWYA0Q2VQYqyBy3Q.jpg",
        "created_at": "2023-07-12T10:13:27.000000Z",
        "updated_at": "2023-07-12T10:49:50.000000Z"
    }
    
}
````

Error Response
```http
{
    "success": false,
    "code": 422,
    "message": "Unprocessable Entity",
    "errors": [
        "The image field is required."
    ]
}
````

#### Edit Image Category
```http
  POST ${baseUrl}/admin/category/${categoryId}/image/edit
```

Authentication
```http
Barer ${Access Token}
````

Request Body (Form Data)

| key | value |
| :---: | :---: | 
| image | image file (jpeg,png,jpg,gif,svg) max 2mb |

Success Response
```http
{
    "success": true,
    "code": 200,
    "message": "Category image updated successfully",
    "data": {
        "id": 1,
        "name": "Herbal",
        "slug": "herbal",
        "image": "http://panensia.id/storage/images/categories/j0AihD2gN3Edvc4t39h69OW45GpJ036Rg9eUqzfx.jpg",
        "created_at": "2023-07-12T10:13:27.000000Z",
        "updated_at": "2023-07-12T10:58:34.000000Z"
    }
    
}
````

Error Response
```http
{
    "success": false,
    "code": 422,
    "message": "Unprocessable Entity",
    "errors": [
        "The image field is required."
    ]
}
````