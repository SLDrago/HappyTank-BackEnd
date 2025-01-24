# HappyTank Backend

Welcome to the HappyTank Backend repository! This project is part of the HappyTank platform, providing robust backend services to support the features of the frontend application. The backend ensures seamless data management, API handling, and integration with external services.

## Features

- **User Authentication and Authorization**: Secure login and role-based access control.
- **Ornamental Fish Database Management**: APIs to handle fish data CRUD operations.
- **Chatbot Integration**: Backend support for AI-powered chatbot queries.
- **Image Processing**: Services for fish identification via image uploads.
- **Advertisement Management**: APIs for posting, editing, and retrieving advertisements.
- **Fish Compatibility Logic**: Backend logic for compatibility checks.
- **Community Forum APIs**: Support for forum posts, comments, and user interactions.
- **Tank Design Recommendations**: Algorithms for generating personalized tank designs.
- **Admin Panel APIs**: Management endpoints for admin functionalities.

## Tech Stack

- **Framework**: Laravel
- **Programming Language**: PHP
- **Database**: MySQL
- AI Features: Open AI API

## Prerequisites

- PHP (v8.0 or higher)
- Composer
- MySQL (v8.0 or higher)

## Getting Started

### Installation

1. Clone the repository:

   ```bash
   git clone https://github.com/your-username/happytank-backend.git
   ```

2. Navigate to the project directory:

   ```bash
   cd happytank-backend
   ```

3. Install dependencies:

   ```bash
   composer install
   ```

### Configuration

1. Create a `.env` file using `.env.example` in the root directory and add the missing variables to setup mail and DB.

2. Generate the application key:

   ```bash
   php artisan key:generate
   ```
###Database Setup

1. Run the migrations to set up the database schema:
    ```bash
    php artisan migrate
    ```
2. Seed the database with initial data:
   ```bash
   php artisan db:seed
   ```

### Running the Development Server

1. Start the server:

   ```bash
   php artisan serve
   ```
   
## Contributing

We welcome contributions to improve HappyTank!
