php artisan storage:link

ab ka password: password

#	Name	Email	Phone	Status	City
25	Ahmed Khan	ahmed.khan@example.com	+92-300-1111111	✅ enabled	Islamabad, PK
26	Sara Ali	sara.ali@example.com	+92-321-2222222	✅ enabled	Karachi, PK
27	James Wilson	james.wilson@example.com	+1-555-333-4444	✅ enabled	Springfield, US
28	Fatima Malik	fatima.malik@example.com	+92-333-5555555	✅ enabled	Lahore, PK
29	Omar Farooq	omar.farooq@example.com	+971-50-6666666	❌ disabled	Dubai, AE
Swagger UI mein test karne ka tarika:

POST /api/v1/auth/login call karo — email + password dalo
Response mein token milega
Swagger ke Authorize button (🔒) pe click karo
Token paste karo — phir sab protected endpoints khul jayenge
Note: Omar Farooq ka account disabled hai — usse login karne pe 403 Account is disabled error ayega. Negative case test karne ke liye useful hai.



migrate:fresh — puri DB drop karke scratch se migrations run karta hai
--seed — sab seeders automatically run ho jaate hain (admin, products, customers, orders, sab)
Baad mein credentials same rahenge:

Email	Password
Admin	admin@store.com	password
Customer	ahmed.khan@example.com	password

backend-dev-vantier-chronosouq.com
manage-backend.thevantier.com

vantierapi.chronosouq.com

ventier-dev-api
ventier-dev-api
ventier-dev-api

-------------
backend.thevantier.com
thevantier-backend
GmZ8flav0K9Pt8Hi5KKP


thevantier-dev
thevantier-dev
thevantier-dev123