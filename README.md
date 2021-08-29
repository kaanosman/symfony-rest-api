# Symfony Rest Api

## Installation

- git clone git@github.com:kaanosman/symfony-rest-api.git
- cd symfony-rest-api  
- composer install
- symfony server:start

## Info

- login(get token) : (post)https://127.0.0.1:8000/api/login
- register : (post)https://127.0.0.1:8000/register
- order list : (get)https://127.0.0.1:8000/api/orders
- order show : (get)https://127.0.0.1:8000/api/orders/{order_id}
- order create : (post)https://127.0.0.1:8000/api/orders/
- order update : (patch)https://127.0.0.1:8000/api/orders/{order_id}
- order delete : (delete)https://127.0.0.1:8000/api/orders/{order_id}

## Swagger
- https://127.0.0.1:8000/document