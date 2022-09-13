# aspire-mini
# This is practical work assigned

# Steps to execute this practical
1. git clone https://github.com/rushabhmadhu18121987/aspire-mini.git
2. cd aspire-min
3. cp .env_practical .env
4. composer install
5. npm install && npm run dev
6. Update database credentials into .env file
7. Execute migration by $ php artisan migrate
8. Exeucte seeder by $ php artisan db:seed
9. run project using $ php artisan serve

# note Rate of intreset is specified in .env file

# below is the API Postman Collection
https://www.getpostman.com/collections/3b16b80be2d51bc1fbaa

Points are Task coverrred in the practical
1. Created/ Modififed users table and added user_type for separating User and Admin using migration
2. Created Loan and LoanEmi Table using migrations
3. Created Seeder for single Admin User which can be found in seeder file easily 
    (email : 'admin@practicalexam.com', 'password' => bcrypt('Practical@Test#2022')
4. For Loan Apply, User needs to register using /api/auth/register API
5. After register User can login using api/auth/login API + Admin can also login to List All Loan + approve any loan
6. After login user can apply for loan using api/apply-loan API with required bearer header params + body params
7. Once Loan places, Admin can view all the loans in the system before approval, by api/loans api
8. Admin can approve any loan by api/approve-loan/{id} API
9. User can see loan EMis by api/loan-emis by passing required header and loan_id as a formdata body params
10. User can Pay loan EMI by using api/pay-emi by passing required header and loan_id + amount as a formdata body params

# Laravel features used:
1. For Admin user created new middleware is_admin so admin api can not be accessed by anyone (Security purpose)
2. Creatd ModelTraits for code re-usability of common feature of Model.
3. Used Laravel seeder feature
4. Used Laravel sanctum for API (REST API)
5. Used One to one, one to Many and belongs relations ship in Model

# Regarding Test cases, separate document is created
