## **Drug Search and Tracker**

Develop a Laravel-based API service for drug information search and user-specific medication tracking. The service will integrate with the National Library of Medicine's RxNorm APIs for drug data.

## **Installation**

Follow these steps to install the project:

### **Prerequisites**

*   PHP (version 8.0 or higher)
*   Composer
*   A web server like Apache or Nginx
*   MySQL or another database system
*   Laravel (version X.X)

### **Steps**

**Clone the Repository**

```plaintext
git clone https://your-repository-url.git
cd your-repository-directory
```

**Install Dependencies**

```plaintext
composer install
```

**Environment Setup**

Copy the **.env.example** file to a new **.env** file and configure your environment settings, including database, mail, and other services.

```plaintext
cp .env.example .env
```

**Generate Application Key**

```plaintext
php artisan key:generate
```

**Run Migrations**

```plaintext
php artisan migrate
```

## **Running the Project**

To run the project locally:

```plaintext
php artisan serve
```

This will start the Laravel development server. You can access the application via **http://localhost:8000** in your browser.

## **API Documentation**

Detailed API documentation is available [here](https://documenter.getpostman.com/view/21443911/2s9Ykhfidz).

## **API Collection**

Access the Postman API collection [here](https://api.postman.com/collections/21443911-12fd38e9-7348-458c-8adb-aac2a02a002f?access_key=PMAT-01HH9K539HSXC0XQT9762BNFC5).
