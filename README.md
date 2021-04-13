<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

# laravel Developer Avinashi Chander

Admin Role:
Admin will login into the account using credentials. Here are the credentials. Username - admin, Password - secret
On login, admin will be redirected to invitation page where admin can send invitation to users using their emails. Unique link is send to user as invitation link from where user can register themselves. Link once used will not be used again.


User role:
User will get the invitation link from where user can register themselve. 
Fields for user to register themselve are username, password and password confirmation. In hidden, i am sending unique code as well from where i will take the email of user as i am not asking for email again.
A 6 digit pin is sent to email. User will enter the 6 digit pin and upon verification, the user is registed and login sucessfully. 
Now the user can access to the profile page where user can update the name and avatar.

### setup
1. composer install
2. npm install
3. copy .env.example file to .env
4. update database credentials and E-mail credentials and other required details in .env file 
5. php artisan key:generate
6. php artisan migrate
7. php artisan db:seed
8. php artisan passport:client --personal --no-interaction
9. npm run prod
