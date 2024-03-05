<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email</title>
</head>
<body style="background-color: #312244; color: white; font-family: Arial, sans-serif; text-align: center; padding: 20px;">
<div style="max-width: 600px; margin: auto; padding: 20px;">
    <h1 style="font-size: 48px; color: #ffcc00; margin-bottom: 50px;">Welcome {{ $user->name }}</h1>
    <p style="font-size: 22px; color: #00ddff; margin-top: 5px; text-align: left">Thank you for registering to Project Mayhem!</p>
    <p style="font-size: 18px; color: #ff66ff; margin-bottom: 30px; text-align: left">Please take a moment to verify your email address to continue.</p>
    <a href="{{ $signedUrl }}" target="_blank" style="background-color: #00ddff; color: #312244; padding: 15px 25px; text-decoration: none; font-size: 20px; border-radius: 5px; display: inline-block;">Verify your email</a>
    <p style="font-size: 18px; color: #ff66ff; margin-bottom: 30px; text-align: left">Project Mayhem team.</p>
</div>
</body>
</html>
