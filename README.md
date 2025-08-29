1. Authentication System
The platform includes a complete authentication system with the following features:
Signup (User Registration):
 New users can register with their email, password, and profile information.


Login:
 Users can log in securely using email and password.


Forgot Password:
 Users can request a password reset link through email.


Reset Password:
 Users can securely reset their password using the email verification link.


Logout:
 Logged-in users can log out anytime, which clears their active session.



2. User Management
Admin Features:


Admin can view a complete list of all users.


Admin can add new users.


Normal User Features:


Users can only see their own listing (their data only).


Users can update their own profile information in the Settings tab.



3. Chat System
The application provides a real-time chat system using Pusher.
a) Personal Chat
Authenticated users can send and receive messages in real-time.


Messages include:


Seen / Unseen indicators


Typing indicator in real-time


b) Group Chat
Group Creation:
 Any authenticated user can create a group.


Group Admin:
 The group creator becomes the admin.


Admin Permissions:


Add  members


View group members


Control group type (private or public)


Group Types:


Public Group:
 Any group member can send and receive messages in real-time.


Private Group:
 Only the group admin can send messages. Other members can only view.


Real-time Features in Groups:


Typing indicator


Real-time send and receive messages


Seen / Unseen indicators



4. Pusher Integration
The system uses Pusher for real-time communication.
Why Pusher?


Simple and reliable real-time messaging


Provides typing, seen/unseen, and message delivery events


How It Works:


When a user sends a message, an event is triggered in Laravel.


The event is published to Pusher channels.


All connected clients subscribed to that channel instantly receive the new message.


Typing indicators and seen/unseen status are also updated instantly.



5. Access Control & Security
Only authenticated users can access the chat system.


Role-based access control ensures


Pusher channels are configured with authentication to prevent unauthorized access.



6. Key Features Summary
Secure login, signup, and password reset


Role-based user management (Admin vs User)


Real-time personal chat with seen/unseen and typing indicators


Real-time group chat (public and private)


Group admin management (add members, control group type)


Pusher-powered reliable real-time communication


Authenticated access only



7. Technology Stack
Backend: Laravel (PHP Framework)


Frontend: Blade Templates


Database: MySQL


Real-time Messaging: Pusher
8. Default Admin & User Passwords (Seeder)
To make the system easy to set up and test, an AdminUserSeeder has been created.
When you run the seeder, it automatically creates a default Admin account:


Email: admin@gmail.com


Password: 12345678


Whenever an Admin creates a new normal user through the platform, that user’s default password will also be set to:


Password: 12345678
