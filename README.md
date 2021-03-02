# Demo
1. link: http://165.232.164.104
2. testing credit card for Stripe payment:
   - 4242 4242 4242 4242 // good, expire date should newer than today, CSV and Zip code are ramdom
# About PP Mall

PP Mall is a e-commerce web app.  It consists of users module, products module, cart & orders module, payment module, and coupons module.  

Technologies involved:

 - PHP 7
 - Laravel 7
 - Vue 2.x
 - JavaScript 
 - HTML/CSS
 - MySQL
 - Redis

## Roles

 - Guests -- not logged users
 - Users -- logged in users, who can do shopping
 - Operators -- users who can add/edit products and proccess orders
 - Admins -- users who can manage everything 

 ## Models
 
 - User
 - UserAddress
 - Product
 - ProductSKU
 - Order
 - OrderItem
 - CouponCode
 - Operator

## Actions
- Delete
- Add
- Edit
- Read


# User cases
## 1. Guests
  - can view products list
  - can view a product detail
  
## 2. Users
  - can view own shipping addresses
  - can add new shipping addresses
  - can edit shipping addresses
  - can delete shipping addresses
  - can favorate a product
  - can add products to the cart
  - can order from the cart
  - can use coupons
  - can pay by credit card
  - can view own orders info
  - can apply for refund from paid order
  - can confirm orders received
  - can write reviews to ordered products

## 3. Operators
- can view users
- can publish products
- can edit products
- can unpublish proudcts
- can edit orders
- can add, edit, and delete coupons

## 4. Admins
- can view operators
- can add, delete, edit and view operators
